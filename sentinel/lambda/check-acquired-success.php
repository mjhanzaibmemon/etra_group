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
    writeCloudWatchLog('sentinel-check-success-rate-acquired', 'Failed to get Acquired API token');
    die('Failed to authenticate with Acquired API');
}

// orders
$query = "SELECT id, payment_id
                                FROM orders
                               WHERE added >= UNIX_TIMESTAMP(DATE_FORMAT(NOW() - INTERVAL 1 HOUR, '%Y-%m-%d %H:00:00'))
                                 AND added < UNIX_TIMESTAMP(DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00')) AND country 
                                ORDER BY added DESC limit 50;";
$query_run = mysql_query($query);

$successCount = 0;
$failedCount = 0;
$multiCurl = [];
$results = [];
$mh = curl_multi_init();

while ($data = mysql_fetch_array($query_run)) {

    // Use new Acquired API v2 endpoint
    $transaction_url = $acquired_base_url . 'transactions/' . $data['payment_id'];

    $ch = curl_init($transaction_url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-type: application/json",
        "Authorization: Bearer $token"
    ));

    curl_multi_add_handle($mh, $ch);
    $multiCurl[] = $ch;
}

$running = null;
do {
    curl_multi_exec($mh, $running);
} while ($running);
foreach ($multiCurl as $ch) {
    $response_data = curl_multi_getcontent($ch);
    writeCloudWatchLog('sentinel-check-success-rate-acquired', ' curl response: '. $response_data);
    $response_data = json_decode($response_data, true);

    // Check if transaction was successful (status: 'success' or 'approved')
    if (isset($response_data['status']) && in_array($response_data['status'], ['success', 'approved'])) {

        $successCount++;

    }
    // check if failed anyhow (but ignore "Unable to locate this transaction" errors)
   
    if (isset($response_data['status']) && in_array($response_data['status'], ['failed', 'error'])
        && (isset($response_data['invalid_parameters'][0]['reason']) 
        && strpos($response_data['invalid_parameters'][0]['reason'], 'Unable to locate this transaction') === false) 
        && (isset($response_data['invalid_parameters'][0]['parameter']) 
        && $response_data['invalid_parameters'][0]['parameter'] !== 'transaction_id')) {

        $failedCount++;
    }

    curl_multi_remove_handle($mh, $ch);
    curl_close($ch);
}

curl_multi_close($mh);


// AL
$query = "SELECT id, payment_id
                    FROM automatic_likes_billing
                   WHERE added >= UNIX_TIMESTAMP(DATE_FORMAT(NOW() - INTERVAL 1 HOUR, '%Y-%m-%d %H:00:00'))
                                 AND added < UNIX_TIMESTAMP(DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00'))
                    ORDER BY added DESC limit 50";
$query_run = mysql_query($query);

$multiCurl = [];
$results = [];
$mh = curl_multi_init();

while ($data = mysql_fetch_array($query_run)) {

    // Use new Acquired API v2 endpoint
    $transaction_url = $acquired_base_url . 'transactions/' . $data['payment_id'];

    $ch = curl_init($transaction_url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-type: application/json",
        "Authorization: Bearer $token"
    ));

    curl_multi_add_handle($mh, $ch);
    $multiCurl[] = $ch;
}

$running = null;
do {
    curl_multi_exec($mh, $running);
} while ($running);

foreach ($multiCurl as $ch) {
    $response_data = curl_multi_getcontent($ch);
    $response_data = json_decode($response_data, true);

    // Check if transaction was successful (status: 'success' or 'approved')
    if (isset($response_data['status']) && in_array($response_data['status'], ['success', 'approved'])) {

        $successCount++;
    }

    // check if failed anyhow (but ignore "Unable to locate this transaction" errors)
   if (isset($response_data['status']) && in_array($response_data['status'], ['failed', 'error'])
        && (isset($response_data['invalid_parameters'][0]['reason']) 
        && strpos($response_data['invalid_parameters'][0]['reason'], 'Unable to locate this transaction') === false) 
        && (isset($response_data['invalid_parameters'][0]['parameter']) 
        && $response_data['invalid_parameters'][0]['parameter'] !== 'transaction_id')) {

        $failedCount++;
    }

    curl_multi_remove_handle($mh, $ch);
    curl_close($ch);
}

curl_multi_close($mh);

// echo 'orders Success: ' . $successCount . ' failed: ' . $failedCount . '<br>';
// echo 'AL Success: ' . $alSuccessCount . ' failed: ' . $alFailedCount . '<br>';


// Initialize CloudWatch client
$cloudWatchClient = new CloudWatchClient([
    'region' => 'us-east-2',
    'version' => 'latest',
    'credentials' => [
        'key'    => $cloudwatchkey,
        'secret' => $cloudwatchpassword,
    ],
]);
try {
    $cloudWatchClient->putMetricData([
        'Namespace' => 'HourlyAcquiredSuccessMetrics',
        'MetricData' => [
            [
                'MetricName' => 'HourlySuccessRate',
                'Dimensions' => [
                    [
                        'Name' => 'SuccessRate',
                        'Value' => 'hourly-success-function'
                    ],
                ],
                'Unit' => 'None', // Or 'Currency' if appropriate
                'Value' => $successCount, // Use the calculated revenue value
            ],
        ],
    ]);

    error_log('Success acquired metric data sent successfully');
} catch (Exception $e) {
    error_log('Error sending success acquired metric data: ' . $e->getMessage());
}

try {
    $cloudWatchClient->putMetricData([
        'Namespace' => 'HourlyAcquiredFailedMetrics',
        'MetricData' => [
            [
                'MetricName' => 'HourlyFailedRate',
                'Dimensions' => [
                    [
                        'Name' => 'FailedRate',
                        'Value' => 'hourly-failed-function'
                    ],
                ],
                'Unit' => 'None', // Or 'Currency' if appropriate
                'Value' => $failedCount, // Use the calculated revenue value
            ],
        ],
    ]);

    error_log('Failed acquired metric data sent successfully');
} catch (Exception $e) {
    error_log('Error sending failed acquired metric data: ' . $e->getMessage());
}

echo 'Acquired Success/Failed data submitted to CloudWatch';


    // if ($sumTransaction < $objectiveAmountRev) {

    //     // echo '<br>sent';die;
    //     $MessageBird = new \MessageBird\Client($messagebirdclient);
    //     $Message = new \MessageBird\Objects\Message();
    //     $Message->originator = +447451272012;
    //     $Message->recipients = array($rfcontactnumber);

    //     $Message->body = 'Etra Group Alert: Daily revenue is not achieved £' . round($sumTransaction);

    //     $MessageBird->messages->create($Message);

    //     if ($MessageBird) {
    //         if ($showoutput == 1) echo 'Text Message Sent to Rabban !<br>';
    //     }
    // }
