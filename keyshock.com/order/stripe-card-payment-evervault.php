<?php
// payment.php - Main PHP file that handles both form display and payment processing
include 'db.php';

// Configuration
$urlKey = str_replace('app_', '', $ev_appid);
$evervaultApiKey = $ev_key;
$evervaultAppId = $ev_appid;
$stripeSecretKey = $ev_stripe_skey;
$stripePublishableKey = $ev_stripe_pkey; // Make sure this is in your db.php

// Get POST data
$input = json_decode(file_get_contents("php://input"), true);

// Set content type for API responses
// header('Content-Type: application/json');

// Process payment
try {
    // Extract payment details
    $amount = isset($input["amount"]) ? (int) $input["amount"] : 2500;
    $currency = strtolower($input["currency"] ?? "usd");
    $description = $input["description"] ?? "Product Purchase";
    $customerEmail = $input["email"] ?? "";
    $customerName = $input["name"] ?? "";

    // Evervault-encrypted card data
    $cardNumber = $input["card_number"];
    $expMonth = $input["exp_month"];
    $expYear = $input["exp_year"];
    $cvc = $input["cvc"];

    // Create Payment Method
    $paymentMethodUrl = "https://api-stripe-com-app-$urlKey.relay.evervault.app/v1/payment_methods";

    $paymentMethodData = [
        "type" => "card",
        "card[number]" => $cardNumber,
        "card[exp_month]" => $expMonth,
        "card[exp_year]" => $expYear,
        "card[cvc]" => $cvc,
    ];

    if (!empty($customerName)) {
        $paymentMethodData["billing_details[name]"] = $customerName;
    }
    if (!empty($customerEmail)) {
        $paymentMethodData["billing_details[email]"] = $customerEmail;
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $paymentMethodUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($paymentMethodData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/x-www-form-urlencoded",
        "x-evervault-api-key: $evervaultApiKey",
        "x-evervault-app-id: $evervaultAppId",
        "Authorization: Bearer $stripeSecretKey"
    ]);

    $paymentMethodResponse = curl_exec($ch);
    
    if (curl_errno($ch)) {
        throw new Exception("Payment method creation failed: " . curl_error($ch));
    }
    curl_close($ch);

    $paymentMethodResult = json_decode($paymentMethodResponse, true);

    if (isset($paymentMethodResult["error"])) {
        throw new Exception($paymentMethodResult["error"]["message"] ?? "Failed to create payment method");
    }

    $paymentMethodId = $paymentMethodResult["id"] ?? null;
    if (!$paymentMethodId) {
        throw new Exception("Payment method ID not received");
    }

    // Create Payment Intent with 3D Secure support
    $paymentIntentUrl = "https://api-stripe-com-app-$urlKey.relay.evervault.app/v1/payment_intents";

    $paymentIntentData = [
        "amount" => $amount,
        "currency" => $currency,
        "payment_method" => $paymentMethodId,
        "confirm" => "true",
        "description" => $description,
        "capture_method" => "automatic",
        // Enable 3D Secure when required
        "payment_method_options[card][request_three_d_secure]" => "any",
        "automatic_payment_methods[enabled]" => "false",
        "payment_method_types[]" => "card"
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $paymentIntentUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($paymentIntentData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/x-www-form-urlencoded",
        "x-evervault-api-key: $evervaultApiKey",
        "x-evervault-app-id: $evervaultAppId",
        "Authorization: Bearer $stripeSecretKey"
    ]);

    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        throw new Exception("Payment processing failed: " . curl_error($ch));
    }
    curl_close($ch);

    $result = json_decode($response, true);

    if (isset($result["error"])) {
        throw new Exception($result["error"]["message"] ?? "Payment failed");
    }

    $paymentStatus = $result["status"] ?? "unknown";
    $paymentIntentId = $result["id"] ?? null;

    // Handle different payment statuses
    switch ($paymentStatus) {
        case "succeeded":
            echo json_encode([
                "success" => true,
                "message" => "Payment successful",
                "payment_intent_id" => $paymentIntentId,
                "amount" => $amount,
                "currency" => $currency,
                "status" => $paymentStatus
            ]);
            break;

        case "requires_action":
        case "requires_source_action":
            // 3D Secure required - return client secret for frontend handling
            echo json_encode([
                "success" => false,
                "requires_action" => true,
                "payment_intent_id" => $paymentIntentId,
                "client_secret" => $result["client_secret"],
                "message" => "3D Secure authentication required"
            ]);
            break;

        case "requires_payment_method":
            throw new Exception("Payment failed. Please check your card details and try again.");

        case "processing":
            echo json_encode([
                "success" => true,
                "message" => "Payment is processing",
                "payment_intent_id" => $paymentIntentId,
                "status" => $paymentStatus
            ]);
            break;

        case "canceled":
            throw new Exception("Payment was canceled");

        default:
            throw new Exception("Unexpected payment status: " . $paymentStatus);
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>