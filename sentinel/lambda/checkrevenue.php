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

function money_convert($from, $amount, $the_key) {
    $url = "https://v6.exchangerate-api.com/v6/" . $the_key . "/latest/$from";
    $request = curl_init();
    curl_setopt($request, CURLOPT_URL, $url);
    curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($request);
    curl_close($request);
    $response = json_decode($response);

    if($from == 'USD')
        $converted_amount = round(($amount * $response->conversion_rates->GBP), 2);
    elseif ($from == 'GBP')
        $converted_amount = round(($amount * $response->conversion_rates->USD), 2);
    $formatted_amount = number_format($converted_amount, 2, '.', '');
    return $formatted_amount;
}


// Get authentication token for Acquired API v2
$token = getAcquiredToken($acquired_base_url, $acquired_app_id, $acquired_app_key);

if (empty($token)) {
    writeCloudWatchLog('sentinel-check-revenue', 'Failed to get Acquired API token');
    die('Failed to authenticate with Acquired API');
}

// orders
$query = "SELECT id, payment_id
                                FROM orders
                                WHERE added >= UNIX_TIMESTAMP(DATE_FORMAT(NOW() - INTERVAL 1 HOUR, '%Y-%m-%d %H:00:00'))
                                  AND added < UNIX_TIMESTAMP(DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00'))
                                ORDER BY added DESC;";
$query_run = mysql_query($query);

$sumTransaction = 0;
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

    writeCloudWatchLog('sentinel-check-revenue', ' curl response: '. $response_data);
    $response_data = json_decode($response_data, true);

    // Check if transaction was successful (status: 'success' or 'approved')
    if (isset($response_data['status']) && in_array($response_data['status'], ['success', 'approved'])) {

        // if GBP transaction then convert to USD
        if (isset($response_data['transaction']['currency']) && strtoupper($response_data['transaction']['currency']) == 'GBP') {
            $amount = money_convert('GBP', $response_data['transaction']['amount'], $exchangerate_api_key);
        } else {
            $amount = $response_data['transaction']['amount'];
        }

        $sumTransaction += $amount;
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
                    ORDER BY added DESC";
$query_run = mysql_query($query);

$alSumTransaction = 0;
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

        if (isset($response_data['transaction']['currency']) && strtoupper($response_data['transaction']['currency']) == 'GBP') {
            $amount1 = money_convert('GBP', $response_data['transaction']['amount'], $exchangerate_api_key);
        } else {
            $amount1 = isset($response_data['transaction']['amount']) ? $response_data['transaction']['amount'] : 0;
        }
        $alSumTransaction += $amount1;
    }

    curl_multi_remove_handle($mh, $ch);
    curl_close($ch);
}

curl_multi_close($mh);


$sumTransaction = $sumTransaction + $alSumTransaction;
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
    // Send custom revenue metric data to CloudWatch USD
    $cloudWatchClient->putMetricData([
        'Namespace' => 'MyApp/HourlyRevenueMetrics',
        'MetricData' => [
            [
                'MetricName' => 'HourlyRevenue',
                'Dimensions' => [
                    [
                        'Name' => 'FunctionName',
                        'Value' => 'hourly-revenue-function'
                    ],
                ],
                'Unit' => 'None', // Or 'Currency' if appropriate
                'Value' => $sumTransaction, // Use the calculated revenue value
            ],
        ],
    ]);

    error_log('Revenue metric data sent successfully');
} catch (Exception $e) {
    error_log('Error sending revenue metric data: ' . $e->getMessage());
}
$GBPAmount = money_convert('USD', $sumTransaction, $exchangerate_api_key);
// echo $sumTransaction .' ' . $GBPAmount;

try {
    // Send custom revenue metric data to CloudWatch GBP
    $cloudWatchClient->putMetricData([
        'Namespace' => 'MyApp/HourlyGBPRevenueMetrics',
        'MetricData' => [
            [
                'MetricName' => 'HourlyGBPRevenue',
                'Dimensions' => [
                    [
                        'Name' => 'FunctionName',
                        'Value' => 'hourly-gbp-revenue-function'
                    ],
                ],
                'Unit' => 'None', // Or 'Currency' if appropriate
                'Value' => $GBPAmount, // Use the calculated revenue value
            ],
        ],
    ]);

    error_log('Revenue metric data sent successfully');
} catch (Exception $e) {
    error_log('Error sending revenue metric data: ' . $e->getMessage());
}

echo 'Revenue data submitted to CloudWatch';


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
