<?php

$host = $_SERVER['HTTP_HOST']; // Get the current host (e.g., anuj.etra.group)
$subdomain = explode('.', $host)[0]; // Get the first part of the domain
$initial = $subdomain . '.';
$subdomain = '/'. $subdomain . '/keyshock';
if(!empty($initial) && $initial != "keyshock.") $_SERVER['DOCUMENT_ROOT'] = $_SERVER['DOCUMENT_ROOT'] . $subdomain;

require dirname($_SERVER["DOCUMENT_ROOT"]) .'/etra.group/sm-db.php';

header('Content-Type: application/json');
$now = time();
// Get the raw POST dataOS
$input = file_get_contents('php://input');
$event = json_decode($input, true);

writeCloudWatchLog('stripe-webhook', 'Response - ' . json_encode($event));

// Webhook secret for signature verification
$webhookSecret = $ev_stripe_webhook_secret; // Add this to your db.php or config

// Validate payload
if (empty($event)) {
    echo json_encode(['error' => 'Invalid payload']);
    writeCloudWatchLog('stripe-webhook', 'Invalid payload - no event type');
    exit;
}

// Log the webhook event
$eventType = $event['type'];
writeCloudWatchLog('stripe-webhook', 'Event received: ' . $eventType);

// Handle fraud-related events
if (strpos($eventType, 'fraud') !== false
    || strpos($eventType, 'blocked') !== false
    // || strpos($eventType, 'charge.dispute') !== false
    || strpos($eventType, 'radar.early_fraud_warning') !== false
    ) {

    writeCloudWatchLog('stripe-webhook', 'Fraud event detected: ' . $eventType);

    // Extract payment intent, charge ID, and order_session from event
    $paymentIntentId = null;
    $chargeId = null;
    $orderSession = null;

    // Get the main object from the event
    $eventObject = $event['data']['object'] ?? [];

    // Extract payment intent ID
    if (isset($eventObject['id']) && strpos($eventObject['id'], 'pi_') === 0) {
        $paymentIntentId = $eventObject['id'];
    } elseif (isset($eventObject['payment_intent'])) {
        $paymentIntentId = $eventObject['payment_intent'];
    }

    // Extract charge ID
    if (isset($eventObject['id']) && strpos($eventObject['id'], 'ch_') === 0) {
        $chargeId = $eventObject['id'];
    } elseif (isset($eventObject['latest_charge'])) {
        $chargeId = $eventObject['latest_charge'];
    }

    // Extract order_session from metadata
    if (isset($eventObject['metadata']['order_session'])) {
        $orderSession = $eventObject['metadata']['order_session'];
    }

    writeCloudWatchLog('stripe-webhook', 'Payment Intent: ' . $paymentIntentId . ' | Charge: ' . $chargeId . ' | Order Session: ' . $orderSession);

    // Find the order - try multiple methods
    $dataOS = null;

    // Method 1: Try order_session first (most reliable)
    if (!empty($orderSession)) {
        $getRecords = mysql_query("SELECT * FROM `orders` WHERE `order_session` = '{$orderSession}' ORDER BY id DESC LIMIT 1");
        if (mysql_num_rows($getRecords) > 0) {
            $dataOS = mysql_fetch_array($getRecords);
            writeCloudWatchLog('stripe-webhook', 'Found order by order_session: ' . $orderSession);
        }
    }

    // Method 2: Try payment_intent ID
    if (empty($dataOS) && !empty($paymentIntentId)) {
        $getRecords = mysql_query("SELECT * FROM `orders` WHERE `payment_id` = '{$paymentIntentId}' ORDER BY id DESC LIMIT 1");
        if (mysql_num_rows($getRecords) > 0) {
            $dataOS = mysql_fetch_array($getRecords);
            writeCloudWatchLog('stripe-webhook', 'Found order by payment_id: ' . $paymentIntentId);
        }
    }

    // Method 3: Try charge ID
    if (empty($dataOS) && !empty($chargeId)) {
        $getRecords = mysql_query("SELECT * FROM `orders` WHERE `payment_id` = '{$chargeId}' ORDER BY id DESC LIMIT 1");
        if (mysql_num_rows($getRecords) > 0) {
            $dataOS = mysql_fetch_array($getRecords);
            writeCloudWatchLog('stripe-webhook', 'Found order by charge_id: ' . $chargeId);
        }
    }

    if (!empty($dataOS)) {
        writeCloudWatchLog('stripe-webhook', 'Found order ID: ' . $dataOS['id'] . ' for email: ' . $dataOS['emailaddress']);

        // Mark order as disputed/refunded
        mysql_query("UPDATE `orders` SET `refund` = '1', `disputed` = '1' WHERE `id` = '{$dataOS['id']}' LIMIT 1");
        writeCloudWatchLog('stripe-webhook', 'Updated order ' . $dataOS['id'] . ' as disputed/refunded');

        // Prepare blacklist data (data already sanitized from database)
        $emailaddress = $dataOS['emailaddress'];
        $igusername = $dataOS['igusername'];
        $ipaddress = $dataOS['ipaddress'];

        // Check if already blacklisted
        $checkExist = mysql_query("SELECT * FROM `blacklist` WHERE `emailaddress` = '{$emailaddress}' OR `igusername` = '{$igusername}' OR `ipaddress` = '{$ipaddress}'");

        if (mysql_num_rows($checkExist) == 0) {
            // Insert into blacklist
            mysql_query("INSERT INTO `blacklist` SET
                `emailaddress` = '{$emailaddress}',
                `igusername` = '{$igusername}',
                `ipaddress` = '{$ipaddress}',
                `added` = '$now',
                `brand` = 'sv'
            ");

            writeCloudWatchLog('stripe-webhook', "Inserted blacklist - Email: {$emailaddress}, IG: {$igusername}, IP: {$ipaddress}");
            sendCloudwatchData('Superviral', 'stripe-fraud-blacklist', 'Webhook', 'stripe-fraud-blacklist-function', 1);

            echo json_encode(['status' => 'blacklisted', 'order_id' => $dataOS['id'], 'order_session' => $orderSession]);
        }
    } else {
        writeCloudWatchLog('stripe-webhook', 'No order found - Payment Intent: ' . $paymentIntentId . ' | Charge: ' . $chargeId . ' | Order Session: ' . $orderSession);
        echo json_encode(['status' => 'order_not_found', 'payment_intent' => $paymentIntentId, 'charge' => $chargeId, 'order_session' => $orderSession]);
    }

    exit;
}



?>
