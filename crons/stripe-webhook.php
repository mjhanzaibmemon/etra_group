<?php

$host = $_SERVER['HTTP_HOST']; // Get the current host (e.g., anuj.etra.group)
$subdomain = explode('.', $host)[0]; // Get the first part of the domain
$initial = $subdomain . '.';
$subdomain = '/'. $subdomain . '/etra.group';
if(!empty($initial) && $initial != "etra.") $_SERVER['DOCUMENT_ROOT'] = $_SERVER['DOCUMENT_ROOT'] . $subdomain;

require_once '../sm-db.php';

header('Content-Type: application/json');

// Get the raw POST data
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



?>

