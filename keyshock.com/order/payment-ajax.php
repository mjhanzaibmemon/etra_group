<?php
include '../db.php';

$urlKey = str_replace('app_','',$ev_appid);
// Evervault Stripe Relay URL
$stripeRelayUrl = "https://api-stripe-com-app-$urlKey.relay.evervault.app/v1/payment_intents";


// Get POST data
$input = json_decode(file_get_contents("php://input"), true);


$packagetitle = 'KeyShock Standard Package';
$finalprice = $input["amount"] ?? 0;

$priceamount = (int) (floatval($finalprice));

// Evervault API Key
$evervaultApiKey = $ev_key;
$evervaultAppId = $ev_appid;
$stripeSecretKey = $ev_stripe_skey; // <-- Replace with your Stripe secret key



// Validate request
if (!$input || empty($input["paymentMethod"]) || empty($input["amount"])) {
    echo json_encode(["success" => false, "error" => "Invalid request"]);
    exit;
}

$currency = strtolower($input["currency"] ?? "usd");
$data = [
    "amount" => $priceamount,
    "currency" => $currency,
    'confirm' => 'true',
    'capture_method' => 'automatic',
    'payment_method_types' => ['card'],
    "payment_method_data[type]" => "card",
    "payment_method_data[card][exp_month]" => $input['paymentMethod']["networkToken"]["expiry"]["month"],
    "payment_method_data[card][exp_year]" => $input['paymentMethod']["networkToken"]["expiry"]["year"],
    "payment_method_data[card][last4]" => $input["paymentMethod"]["card"]["lastFour"],
    "payment_method_data[card][network_token][exp_month]" => $input["paymentMethod"]["networkToken"]["expiry"]["month"],
    "payment_method_data[card][network_token][exp_year]" => $input["paymentMethod"]["networkToken"]["expiry"]["year"],
    "payment_method_data[card][network_token][number]" => $input["paymentMethod"]["networkToken"]["number"],
    "payment_method_data[card][network_token][tokenization_method]" => 'apple_pay',
    "payment_method_data[billing_details][email]" => $info['emailaddress'],
    "payment_method_options[card][network_token][cryptogram]" => $input["paymentMethod"]["cryptogram"],
    "metadata[payment_source]" => 'Apple Pay',
    "metadata[card_brand]" => $input["paymentMethod"]["card"]["brand"],
    "metadata[card_funding]" => $input["paymentMethod"]["card"]["funding"],
    "metadata[card_segment]" => $input["paymentMethod"]["card"]["segment"],
    "metadata[card_country]" => $input["paymentMethod"]["card"]["country"],
    "metadata[card_issuer]" => $input["paymentMethod"]["card"]["issuer"],

];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $stripeRelayUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/x-www-form-urlencoded",
    "x-evervault-api-key: $evervaultApiKey",
    "x-evervault-app-id: $evervaultAppId",
    "Authorization: Bearer $stripeSecretKey"
]);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode(["success" => false, "error" => curl_error($ch)]);
    exit;
}

curl_close($ch);

$result = json_decode($response, true);

// Handle 3DS
if ($result["status"] === "requires_action" || $result["status"] === "requires_source_action") {
    echo json_encode([
        "success" => false,
        "requires_action" => true,
        "payment_intent_client_secret" => $result["client_secret"]
    ]);
    exit;
}


// Check for Stripe errors
if (isset($result["error"])) {
    $liveresponse = array("success" => false, "error" => $result["error"]["message"] ?? "Payment failed");
    
    echo json_encode($liveresponse);
    exit;
}

// If we reach here, payment succeeded
echo json_encode([
    "success" => true,
    "payment" => $result
]);


