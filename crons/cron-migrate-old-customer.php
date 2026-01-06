<?php


// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
echo '<pre>';
$host = $_SERVER['HTTP_HOST']; // Get the current host (e.g., anuj.etra.group)
$subdomain = explode('.', $host)[0]; // Get the first part of the domain
$initial = $subdomain . '.';
$subdomain = '/' . $subdomain . '/etra.group';
if (!empty($initial) && $initial != "etra.") $_SERVER['DOCUMENT_ROOT'] = $_SERVER['DOCUMENT_ROOT'] . $subdomain;

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . '/etra.group/lambda/core-queue.php';

use Aws\CloudWatch\CloudWatchClient;

// Function to get Acquired API v2 authentication token
function getAcquiredToken($base_url, $app_id, $app_key) {

    $token_data = json_encode([
        'app_id' => $app_id,
        'app_key' => $app_key
    ]);

    $curl = curl_init($base_url . 'login');
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $token_data);

    $response = curl_exec($curl);
    curl_close($curl);

    $response_data = json_decode($response, true);
    return isset($response_data['access_token']) ? $response_data['access_token'] : null;
}


// Get authentication token for Acquired API v2
$token = getAcquiredToken($acquired_base_url, $acquired_app_id, $acquired_app_key);

if (empty($token)) {
    writeCloudWatchLog('cron-migrate-old-customer', 'Failed to get Acquired API token');
    die('Failed to authenticate with Acquired API');
}

$query = "SELECT *
          FROM automatic_likes WHERE
          recurring = '1' AND
          cancelbilling != '3' AND
          payment_id != '' AND
          payment_id = SUBSTRING_INDEX(payment_id, '-', 1)
          ORDER BY id DESC
          LIMIT 50";

$result = mysql_query($query);
$total_count = mysql_num_rows($result);

echo "Found {$total_count} subscriptions to migrate<br><br>";

if ($total_count == 0) {
    die("No subscriptions need migration.<br>");
}

$migrated = 0;
$failed = 0;
$skipped = 0;



while ($al_info = mysql_fetch_array($result)) {

    echo "\nProcessing AL #{$al_info['id']} - {$al_info['igusername']} ({$al_info['emailaddress']})<br>";
    echo "  Original Transaction ID: {$al_info['payment_id']}<br>";

    // Check if already has acquired_card_id
    if (!empty($al_info['acquired_card_id'])) {
        echo "  SKIPPED: Already has acquired_card_id: {$al_info['acquired_card_id']}<br>";
        $skipped++;
        continue;
    }

    // Prepare migration request data
    $migration_data = [
        'original_transaction_id' => $al_info['payment_id']
    ];

    $migration_json = json_encode($migration_data);

    // Make API call to migrate token using Acquired's migration endpoint
    $curl = curl_init($acquired_base_url . 'tools/migrate-token');
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        "Content-type: application/json",
        "Authorization: Bearer $token"
    ));
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $migration_json);

    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    $response_data = json_decode($response, true);
    // print_r($response_data);die;
    echo "  API Response Code: {$http_code}<br>";

    // Check if successful and card_id returned
    if (isset($response_data['status']) && $response_data['status'] == 'success'
        && isset($response_data['card_id']) && !empty($response_data['card_id'])) {

        $acquired_card_id = $response_data['card_id'];
        $acquired_customer_id = isset($response_data['customer_id']) ? $response_data['customer_id'] : '';

        $update_query = "UPDATE automatic_likes
                        SET acquired_card_id = '" . addslashes($acquired_card_id) . "',
                            acquired_customer_id = '" . addslashes($acquired_customer_id) . "'
                        WHERE id = '{$al_info['id']}'
                        LIMIT 1";

        if (mysql_query($update_query)) {
            echo " SUCCESS: Card migrated!<br>";
            $migrated++;
        } else {
            echo " FAILED: Could not update database<br>";
            $failed++;
        }
    } else {
        $error_msg = isset($response_data['message']) ? $response_data['message'] : 'Unknown error';
        echo " FAILED: {$error_msg}<br>";
        if (isset($response_data['errors'])) {
            echo "  Errors: " . json_encode($response_data['errors']) . "<br>";
        }
        $failed++;
    }

    usleep(100000); // 0.1 seconds
}
