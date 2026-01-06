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

$urlKey = str_replace('app_', '', $ev_appid);
$evervaultApiKey = $ev_key;
$evervaultAppId = $ev_appid;
$stripeSecretKey = $ev_stripe_skey;


$stripeApiBaseUrl = "https://api-stripe-com-app-{$urlKey}.relay.evervault.app/v1/";

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


function getStripePaymentIntent($paymentIntentId, $stripeApiBaseUrl, $evervaultApiKey, $evervaultAppId, $stripeSecretKey) {
    $url = $stripeApiBaseUrl . 'payment_intents/' . $paymentIntentId;
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/x-www-form-urlencoded",
        "x-evervault-api-key: $evervaultApiKey",
        "x-evervault-app-id: $evervaultAppId",
        "Authorization: Bearer $stripeSecretKey"
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200) {
        return json_decode($response, true);
    }
    
    return null;
}


function getStripeCharge($chargeId, $stripeApiBaseUrl, $evervaultApiKey, $evervaultAppId, $stripeSecretKey) {
    $url = $stripeApiBaseUrl . 'charges/' . $chargeId;
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/x-www-form-urlencoded",
        "x-evervault-api-key: $evervaultApiKey",
        "x-evervault-app-id: $evervaultAppId",
        "Authorization: Bearer $stripeSecretKey"
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200) {
        return json_decode($response, true);
    }
    
    return null;
}

writeCloudWatchLog('sentinel-check-stripe-revenue', 'Starting Stripe revenue check for last hour');

// ============================================
// REGULAR ORDERS - Stripe Payments
// ============================================
$query = "SELECT id, payment_id, price
                FROM orders
                WHERE added >= UNIX_TIMESTAMP(DATE_FORMAT(NOW() - INTERVAL 1 HOUR, '%Y-%m-%d %H:00:00'))
                  AND added < UNIX_TIMESTAMP(DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00'))
                  AND (payment_id LIKE 'pi_%' OR payment_id LIKE 'ch_%')
                  AND refund = '0'
                ORDER BY added DESC;";
$query_run = mysql_query($query);

$sumTransaction = 0;
$orderCount = 0;

writeCloudWatchLog('sentinel-check-stripe-revenue', 'Found ' . mysql_num_rows($query_run) . ' Stripe orders in last hour');

while ($data = mysql_fetch_array($query_run)) {
    $paymentId = $data['payment_id'];
    $orderId = $data['id'];
    $orderPrice = floatval($data['price']);
    
    // Determine if it's a payment intent or charge
    $isPaymentIntent = (stripos($paymentId, 'pi_') === 0);
    $isCharge = (stripos($paymentId, 'ch_') === 0);
    
    $paymentData = null;
    
    if ($isPaymentIntent) {
        $paymentData = getStripePaymentIntent($paymentId, $stripeApiBaseUrl, $evervaultApiKey, $evervaultAppId, $stripeSecretKey);
    } elseif ($isCharge) {
        $paymentData = getStripeCharge($paymentId, $stripeApiBaseUrl, $evervaultApiKey, $evervaultAppId, $stripeSecretKey);
    }
    
    if ($paymentData && isset($paymentData['status'])) {
        // Check if payment was successful
        if ($paymentData['status'] === 'succeeded' || $paymentData['status'] === 'paid') {
            $amount = isset($paymentData['amount']) ? ($paymentData['amount'] / 100) : $orderPrice; // Stripe amounts are in cents
            $currency = isset($paymentData['currency']) ? strtoupper($paymentData['currency']) : 'USD';
            
            // Convert to USD if needed
            if ($currency == 'GBP') {
                $amount = money_convert('GBP', $amount, $exchangerate_api_key);
            }
            
            $sumTransaction += $amount;
            $orderCount++;
            
            writeCloudWatchLog('sentinel-check-stripe-revenue', "Order {$orderId}: Payment {$paymentId} - {$currency} {$amount} (converted to USD)");
        } else {
            writeCloudWatchLog('sentinel-check-stripe-revenue', "Order {$orderId}: Payment {$paymentId} - Status: {$paymentData['status']} (not counted)");
        }
    } else {
        writeCloudWatchLog('sentinel-check-stripe-revenue', "Order {$orderId}: Failed to retrieve payment data for {$paymentId}");
    }
}

writeCloudWatchLog('sentinel-check-stripe-revenue', "Regular orders total: USD {$sumTransaction} from {$orderCount} orders");

// ============================================
// AUTOMATIC LIKES BILLING - Stripe Payments
// ============================================
$query = "SELECT id, payment_id, amount
                FROM automatic_likes_billing
                WHERE added >= UNIX_TIMESTAMP(DATE_FORMAT(NOW() - INTERVAL 1 HOUR, '%Y-%m-%d %H:00:00'))
                  AND added < UNIX_TIMESTAMP(DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00'))
                  AND (payment_id LIKE 'pi_%' OR payment_id LIKE 'ch_%')
                ORDER BY added DESC";
$query_run = mysql_query($query);

$alSumTransaction = 0;
$alOrderCount = 0;

writeCloudWatchLog('sentinel-check-stripe-revenue', 'Found ' . mysql_num_rows($query_run) . ' Stripe AL billing records in last hour');

while ($data = mysql_fetch_array($query_run)) {
    $paymentId = $data['payment_id'];
    $billingId = $data['id'];
    $billingAmount = floatval($data['amount']);
    
    $isPaymentIntent = (stripos($paymentId, 'pi_') === 0);
    $isCharge = (stripos($paymentId, 'ch_') === 0);
    
    $paymentData = null;
    
    if ($isPaymentIntent) {
        $paymentData = getStripePaymentIntent($paymentId, $stripeApiBaseUrl, $evervaultApiKey, $evervaultAppId, $stripeSecretKey);
    } elseif ($isCharge) {
        $paymentData = getStripeCharge($paymentId, $stripeApiBaseUrl, $evervaultApiKey, $evervaultAppId, $stripeSecretKey);
    }
    
    if ($paymentData && isset($paymentData['status'])) {
        // Check if payment was successful
        if ($paymentData['status'] === 'succeeded' || $paymentData['status'] === 'paid') {
            $amount = isset($paymentData['amount']) ? ($paymentData['amount'] / 100) : $billingAmount; // Stripe amounts are in cents
            $currency = isset($paymentData['currency']) ? strtoupper($paymentData['currency']) : 'USD';
            
            // Convert to USD if needed
            if ($currency == 'GBP') {
                $amount = money_convert('GBP', $amount, $exchangerate_api_key);
            }
            
            $alSumTransaction += $amount;
            $alOrderCount++;
            
            writeCloudWatchLog('sentinel-check-stripe-revenue', "AL Billing {$billingId}: Payment {$paymentId} - {$currency} {$amount} (converted to USD)");
        } else {
            writeCloudWatchLog('sentinel-check-stripe-revenue', "AL Billing {$billingId}: Payment {$paymentId} - Status: {$paymentData['status']} (not counted)");
        }
    } else {
        writeCloudWatchLog('sentinel-check-stripe-revenue', "AL Billing {$billingId}: Failed to retrieve payment data for {$paymentId}");
    }
}

writeCloudWatchLog('sentinel-check-stripe-revenue', "AL billing total: USD {$alSumTransaction} from {$alOrderCount} billing records");


$totalRevenue = $sumTransaction + $alSumTransaction;
$totalTransactions = $orderCount + $alOrderCount;

writeCloudWatchLog('sentinel-check-stripe-revenue', "TOTAL STRIPE REVENUE: USD {$totalRevenue} from {$totalTransactions} transactions");

$cloudWatchClient = new CloudWatchClient([
    'region' => 'us-east-2',
    'version' => 'latest',
    'credentials' => [
        'key'    => $cloudwatchkey,
        'secret' => $cloudwatchpassword,
    ],
]);

try {
    // Send Stripe hourly revenue metric (USD)
    $cloudWatchClient->putMetricData([
        'Namespace' => 'StripeHourlyRevenueMetrics',
        'MetricData' => [
            [
                'MetricName' => 'StripeHourlyRevenue',
                'Dimensions' => [
                    [
                        'Name' => 'FunctionName',
                        'Value' => 'stripe-hourly-revenue-function'
                    ],
                ],
                'Unit' => 'None',
                'Value' => $totalRevenue,
            ],
        ],
    ]);

    writeCloudWatchLog('sentinel-check-stripe-revenue', 'Stripe USD revenue metric sent successfully');
} catch (Exception $e) {
    writeCloudWatchLog('sentinel-check-stripe-revenue', 'Error sending Stripe USD revenue metric: ' . $e->getMessage());
}

// Convert to GBP and send
$GBPAmount = money_convert('USD', $totalRevenue, $exchangerate_api_key);

try {
    // Send Stripe hourly revenue metric (GBP)
    $cloudWatchClient->putMetricData([
        'Namespace' => 'StripeHourlyGBPRevenueMetrics',
        'MetricData' => [
            [
                'MetricName' => 'StripeHourlyGBPRevenue',
                'Dimensions' => [
                    [
                        'Name' => 'FunctionName',
                        'Value' => 'stripe-hourly-gbp-revenue-function'
                    ],
                ],
                'Unit' => 'None',
                'Value' => $GBPAmount,
            ],
        ],
    ]);

    writeCloudWatchLog('sentinel-check-stripe-revenue', 'Stripe GBP revenue metric sent successfully');
} catch (Exception $e) {
    writeCloudWatchLog('sentinel-check-stripe-revenue', 'Error sending Stripe GBP revenue metric: ' . $e->getMessage());
}

echo "Stripe revenue data submitted to CloudWatch\n";

