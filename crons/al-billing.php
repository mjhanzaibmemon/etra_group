<?php

//THIS PAGE IS FOR DAILY RESTARTING THE AUTO LIKES and only occurs once per day
$host = $_SERVER['HTTP_HOST']; // Get the current host (e.g., anuj.etra.group)
$subdomain = explode('.', $host)[0]; // Get the first part of the domain
$initial = $subdomain . '.';
$subdomain = '/' . $subdomain . '/etra.group';
if (!empty($initial) && $initial != "etra.") $_SERVER['DOCUMENT_ROOT'] = $_SERVER['DOCUMENT_ROOT'] . $subdomain;

include('../sm-db.php');
include 'emailer.php'; //TO EMAIL ONCE ORDER IS COMPLETE

use Google\Cloud\Translate\V2\TranslateClient;

require dirname($_SERVER["DOCUMENT_ROOT"]) . '/etra.group/common/gtranslate/index.php';

$translate = new TranslateClient(['key' => $googletranslatekey]);

// require_once dirname($_SERVER["DOCUMENT_ROOT"]) . '/etra.group/common/cardinity-php-master/vendor/autoload.php';

// use Cardinity\Client;
// use Cardinity\Method\Payment;
// use Cardinity\Exception;
// use Cardinity\Method\ResultObject;

// $client = Client::create([
//   'consumerKey' => $cardinitykey,
//   'consumerSecret' => $cardinitysecret,
// ]);

// acquired function


function request_hash($param, $company_hashcode)
{

  if (in_array($param['transaction']['transaction_type'], array('AUTH_ONLY', 'AUTH_CAPTURE', 'CREDIT', 'BENEFICIARY_NEW'))) {

    $str = $param['timestamp'] . $param['transaction']['transaction_type'] . $param['company_id'] . $param['transaction']['merchant_order_id'];
  } elseif (
    in_array($param['transaction_type'], array(
      'CAPTURE', 'VOID',
      'REFUND', 'SUBSCRIPTION_MANAGE', 'ACCOUNT_UPDATER', 'PAY_OUT'
    ))
  ) {
    $str = $param['timestamp'] . $param['transaction_type'] . $param['company_id'] .
      $param['original_transaction_id'];
  }

  return hash('sha256', $str . $company_hashcode);
}

function curl_request($URL, $Data)
{
  $curl = curl_init($URL);
  curl_setopt($curl, CURLOPT_HEADER, false);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt(
    $curl,
    CURLOPT_HTTPHEADER,
    array("Content-type: application/json")
  );
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $Data);

  $json_response = curl_exec($curl);

  $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);


  curl_close($curl);

  $response = json_decode($json_response, true);
  return $response;
}

// 


$now = time();
$threehoursago  = $now - 82000;

// superviral code
$q = mysql_query("SELECT * FROM `automatic_likes` WHERE 
      `cancelbilling` != '3' AND 
      `nextbilled` < $now AND 
      `nextbilled` != '0' AND
      `recurring` = '1' AND
      `billingfailure` = '' AND brand = 'sv' ORDER BY nextbilled DESC");


while ($info = mysql_fetch_array($q)) {


  if (($info['country'] !== 'ww') && ($info['country'] !== 'us') && ($info['country'] !== 'uk')) $notenglish2 = true;

  // SETTING country TO 'us'
  if($info['country'] == 'us' && $info['billing_country'] == 'GB'){$info['billing_country'] = 'US';}
  if($info['country'] == 'ww'){$info['country'] = 'us';$info['billing_country'] = 'US';}



  echo $info['id'] . ' - ' . date('jS F Y', $info['nextbilled']) . '<hr>';


  $locredirect = $info['country'] . '/';
  if ($locredirect == 'ww.') $locredirect = '';

  $loc333 = $info['country'];

  if (($info['cardexpiringtime'] !== '0') && (time() > $info['cardexpiringtime'])) {

    $billingfailure = 'Your card has expired<br>';

    $errorupdatethis = mysql_query("UPDATE `automatic_likes` SET 

                      `billingfailure` = '$billingfailure' 

                      WHERE `id` = '{$info['id']}' AND brand = 'sv' LIMIT 1");

    $errorupdatesession = mysql_query("UPDATE `automatic_likes_session` SET 
      `billingfailure` = '$billingfailure' 

      WHERE `order_session` = '{$info['autolikes_session']}' AND brand = 'sv' ORDER BY `id` DESC LIMIT 1");


    //EMAIL CUSTOMER HERE WITH ERROR ~~~
    $subject = 'Card Expired for your ' . $info['likes_per_post'] . ' Automatic Likes';

    $emailbody = '
      <p>Hi there,</p>
      <br>
      <p>We\'ve recently tried to debit your card **** ' . $info['lastfour'] . ' for a total amount of ' . $locas[$loc333]['currencysign'] . $info['price'] . $locas[$loc333]['currencyend'] . '. Unfortunately, your card has expired. To continue your Automatic Likes, you\'ll need to select a new payment method.
      </p>
       <br>
        <a href="https://superviral.io/' . $locredirect . 'account/checkout/' . $info['autolikes_session'] . '" style="color: #2e00f4;border: 2px solid #2e00f4;display: block;width: 330px;padding: 16px 9px;text-decoration: none;    -webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px;margin: 5px auto;font-weight: 700;text-align:center;">Fix Card Payment</a>
      <br>                   
      <br>
      <p>
      If you\'re unlucky in making the payment to continue your Automatic Likes, unfortunately, your <b>' . $info['likes_per_post'] . ' Automatic Likes will expire in 24-hours</b>. <b>In the event you\'re unable to renew your payment, we cannot guarantee if the previous likes you\'ve received from us will stay</b>.
      </p>
      <br>
      <p>Here is the package that\'s currently expiring:</p>
      <br>

      <table class="ordertbl">
        <tr><td>IG Username</td><td>Service</td><td>Payment Failure</td><td>Status:</td></tr>
        <tr><td>' . $info['igusername'] . '</td><td>' . $info['likes_per_post'] . ' Automatic Likes</td><td>' . $locas[$loc333]['currencysign'] . $info['price'] . $locas[$loc333]['currencyend'] . '</td><td><font color="red">Expiring Today</font></td></tr>
      </table>

      <br>
      <p>You\'ll stop receiving the following benefits if you\'re unable to change your payment method:</p>
      <br><p>
      - You can potentially lose the likes you\'ve previously gained<br>
      - Real likes from real users<br>
      - Free views on all videos<br>
      - Safe & Secure since 2012<br>
      - 24/7 customer support<br>
      - Cancel anytime you like<br></p>

      <br>
      <p>To make payment and continue your Automatic Likes along and keep its benefits, please click on the following link:</p>
      <br>

      <br>
        <a href="https://superviral.io/' . $locredirect . 'account/checkout/' . $info['autolikes_session'] . '" style="color: #2e00f4;border: 2px solid #2e00f4;display: block;width: 330px;padding: 16px 9px;text-decoration: none;    -webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px;margin: 5px auto;font-weight: 700;text-align:center;">Fix Card Payment</a>
      <br>
      <p>We\'ll do the rest of the hardwork for you!</p>
      <br>
      <p>Kind regards,</p>
      <br>
      <p>Superviral Team</p>
      ';

    $emailtpl = file_get_contents(dirname($_SERVER["DOCUMENT_ROOT"]) . '/superviral.io/emailtemplate/emailtemplate.html');
    $emailtpl = str_replace('{body}', $emailbody, $emailtpl);
    $emailtpl = str_replace('Unsubscribe', '', $emailtpl);
    $emailtpl = str_replace('{subject}', $subject, $emailtpl);

    if ($notenglish2 == true) {

      $thisloc = $info['country'];


      $result = $translate->translate($emailtpl, [
        'source' => 'en',
        'target' => $locas[$thisloc]['sdb'],
        'format' => 'html'
      ]);

      $emailtpl = $result['text'];

      $result = $translate->translate($subject, [
        'source' => 'en',
        'target' => $locas[$thisloc]['sdb'],
        'format' => 'html'
      ]);

      $subject = $result['text'];
    }


    emailnow($info['emailaddress'], 'Superviral', 'no-reply@superviral.io', $subject, $emailtpl);


    unset($billingfailure);
    unset($errorupdatethis);
  }



  if (($info['cardexpiringtime'] !== '0') && (time() > $info['cardexpiringtime'])) continue; //go to next loop; 





  /////////////////////////////////##################################################

  $thiscurrency = $info['country'];
  $price = floatval($info['price']);
  $currency = $locas[$thiscurrency]['currencypp'];

  $thisloc = $info['country'];

  if (empty($info['billing_country'])) continue;
  if($thisloc == 'us' && $info['billing_country'] !== 'GB')continue;
  if($info['billing_country'] == 'US' || $info['billing_country'] == 'CA')continue;
  
  writeCloudWatchLog('crons-al-billing',  $info['emailaddress'] .'- Payment Id:' . $info['payment_id']);

  if (!empty($info['acquired_card_id'])) {

    // acquired - Using NEW API V2 with token authentication

    try {

      // Step 1: Get authentication token
      $token_data = [
        'app_id' => $acquired_app_id,
        'app_key' => $acquired_app_key
      ];

      $token_response = curl_request($acquired_base_url . 'login', json_encode($token_data));

      if(empty($token_response['access_token'])) {
        writeCloudWatchLog('crons-al-billing',  $info['emailaddress'] .'- acquired token error: No access token received');
        $errors[] = 'Authentication failed with payment gateway';
        continue;
      }

      $access_token = $token_response['access_token'];
      // echo "Access Token: " . $access_token . "<br>";die;

      // Step 2: Process recurring payment using original transaction_id
      // This uses the subscription/recurring payment feature
      $payment_data = [
        "transaction" => [
          "order_id" => $info['id'] . '-' . time(),
          "amount" => $price,
          "currency" => strtolower($currency),
          "capture" => true,
        ],
        "payment" => [
          "subscription_reason" => "recurring",
          "reference" => "Recurring billing for AL #" . $info['id'],
          "card_id" => $info['acquired_card_id']
        ]
      ];

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $acquired_base_url . 'payments/recurring');
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $access_token,
        'Content-Type: application/json'
      ]);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payment_data));

      $json_response = curl_exec($ch);

      if(curl_errno($ch)) {
        $curl_error = curl_error($ch);
        curl_close($ch);
        writeCloudWatchLog('crons-al-billing',  $info['emailaddress'] .'- acquired curl error: ' . $curl_error);
        $errors[] = 'Connection error with payment gateway';
        continue;
      }

      curl_close($ch);

      $response = json_decode($json_response, true);

      // print_r($response);die;

      writeCloudWatchLog('crons-al-billing',  $info['emailaddress'] .'- acquired new API response: ' . json_encode($response));

      if(empty($response['transaction_id'])) {
        writeCloudWatchLog('crons-al-billing',  $info['emailaddress'] .'- acquired error: No transaction_id in response');

        // Handle error response
        if(!empty($response['errors'])) {
          foreach($response['errors'] as $error) {
            $errors[] = $error['message'] ?? 'Payment failed';
          }
        } elseif(!empty($response['title'])) {
          $errors[] = $response['title'];
        } else {
          $errors[] = 'Payment processing failed';
        }

        continue;
      }

      $paymentId = $response['transaction_id'];
      $payment_status = $response['status'] ?? '';

      // Check if payment was successful
      if($payment_status !== 'success') {
        writeCloudWatchLog('crons-al-billing',  $info['emailaddress'] .'- acquired payment status: ' . $payment_status);

        if(!empty($response['issuer_response_code'])) {
          $errors[] = 'Payment declined. Code: ' . $response['issuer_response_code'];
        } else {
          $errors[] = 'Payment was not successful. Status: ' . $payment_status;
        }

        continue;
      }

    } catch (\Exception $exception) {
      writeCloudWatchLog('crons-al-billing',  $info['emailaddress'] .'- acquired exception: ' . $exception->getMessage());
      $errors[] = 'Payment processing error: ' . $exception->getMessage();
      continue;
    }
    // end acquired

  }


  
  try {

    // if (substr_count ($info['payment_id'], '-') > 0) {
    //   // cardinity
    //   $method = new Payment\Create([
    //     'amount' => $price,
    //     'currency' => $currency,
    //     'settle' => true,
    //     'description' => 'Automatic Likes Recurring',
    //     'order_id' => $info['id'],
    //     'country' => $info['billing_country'],
    //     'payment_method' => Payment\Create::RECURRING,
    //     'payment_instrument' => [
    //       'payment_id' => $info['payment_id']
    //     ],
    //   ]);

    //   $payment = $client->call($method);
    //   writeCloudWatchLog('crons-al-billing',  $info['emailaddress'] .'- cardinity response:' .json_encode($payment));
    //   $paymentId = $payment->getId();

    //   // end cardinity
    // }

    // stripe
    if (!empty($info['stripe_customer_id'])) {

      $urlKey = str_replace('app_', '', $ev_appid);
      $evervaultApiKey = $ev_key;
      $evervaultAppId = $ev_appid;
      $stripeSecretKey = $ev_stripe_skey;

      $stripeCustomerId = $info['stripe_customer_id'];
      $stripePaymentMethodId = $info['stripe_payment_method_id'];
      $igusername = $info['igusername'];
      $emailaddress = $info['emailaddress'];
      // Create Payment Intent for recurring charge
      $paymentIntentUrl = "https://api-stripe-com-app-$urlKey.relay.evervault.app/v1/payment_intents";

      $paymentIntentData = [
        "amount" => $price,
        "currency" => "usd",
        "customer" => $stripeCustomerId,
        "payment_method" => $stripePaymentMethodId,
        "confirm" => "true",
        "description" => "Subscription",
        "capture_method" => "automatic",
        "off_session" => "true",
        "automatic_payment_methods[enabled]" => "false",
        "payment_method_types[]" => "card",
        "metadata[al_id]" => $info['id'],
        "metadata[ig_username]" => $igusername,
        "metadata[billing_type]" => "recurring",
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
        echo "Payment processing failed: " . curl_error($ch) . '<br>';
        continue;
      }
      curl_close($ch);

      $result = json_decode($response, true);

      if (isset($result["error"])) {
        echo $result["error"]["message"] ?? "Payment failed<br>" ;
        continue;
      }

      $paymentStatus = $result["status"] ?? "unknown";
      $paymentId = $result["id"] ?? null;
    }

    // check if not payment made
    if (empty($paymentId)) {
      writeCloudWatchLog('crons-al-billing',  $info['emailaddress'] .'- no payment made');
      continue;
    }

    $lastbilled = time();
    $nextbilled = time() + (86400 * 29);
    $expiry = $nextbilled + 86400;


    mysql_query("UPDATE `automatic_likes`

                          SET 
                          `lastbilled` = '$lastbilled', 
                          `nextbilled` = '$nextbilled', 
                          `expires` = '$expiry', 
                          `changenotallowed` = '0',
                          `expiredemail` = '0'

                          WHERE `id` = '{$info['id']}' AND brand = 'sv' LIMIT 1

                          ");


    $now = time();

    mysql_query("INSERT INTO `automatic_likes_billing`

                          SET 
                          `account_id` = '{$info['account_id']}',
                          `igusername` = '{$info['igusername']}',
                          `auto_likes_id` = '{$info['id']}',
                          `likesperpost` = '{$info['likes_per_post']}',
                          `currency` = '$currency',
                          `amount` = '{$info['price']}',
                          `added` = '$now',
                          `main_payment_id` = '{$info['payment_id']}',
                          `payment_id` = '$paymentId',
                          `lastfour` = '{$info['lastfour']}',
                          `billingname` = '{$info['payment_billingname_crdi']}',
                           brand = 'sv'

                          ");


    //SEND OUT BILLING SUCCESS EMAIL ~~~
    $subject = 'Automatic Likes #' . $info['id'] . ': Payment Successful';

    $emailbody = '
                      <p>Hi there,</p>
                      <br>
                      <p>We can confirm that we\'ve debit your card a total of ' . $locas[$loc333]['currencysign'] . $info['price'] . $locas[$loc333]['currencyend'] . ' on the card ending with **** ' . $info['lastfour'] . '. Billing name: ' . $info['payment_billingname_crdi'] . '</p>
                      <br>
                      <p>

                      </p>
                      <br>
                      <p>For the following service:</p>
                      <br>

                      <table class="ordertbl">
                        <tr><td>IG Username</td><td>Service</td><td>Payment</td><td>Next Billed:</td></tr>
                        <tr><td>' . $info['igusername'] . '</td><td>' . $info['likes_per_post'] . ' Automatic Likes</td><td>' . $locas[$loc333]['currencysign'] . $info['price'] . $locas[$loc333]['currencyend'] . '</td><td>' . date('jS F Y', time() + (86400 * 30)) . '</td></tr>
                      </table>

                      <br>
                      <p>You\'ll next be billed on ' . date('jS F Y', time() + (86400 * 30)) . '.</p>
                      <br>

                      <br>
                      <p>You\'ll continue to receive the following benefits with your package:</p>
                      <br><p>
                      - Up to ' . $info['max_post_per_day'] . '-posts per day<br>
                      - Real likes from real users<br>
                      - Free views on all videos<br>
                      - Safe & Secure since 2012<br>
                      - 24/7 customer support<br>
                      - Cancel anytime you like<br></p>

                      <br>
                      <p>You can manage and make changes to your auto likes here:</p>
                      <br>

                      <br>
                        <a href="https://superviral.io/' . $locredirect . 'account/edit/' . $info['md5'] . '" style="color: #2e00f4;border: 2px solid #2e00f4;display: block;width: 330px;padding: 16px 9px;text-decoration: none;    -webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px;margin: 5px auto;font-weight: 700;text-align:center;">Manage My Auto Likes</a>
                      <br>';

    $emailtpl = file_get_contents(dirname($_SERVER["DOCUMENT_ROOT"]) . '/superviral.io/emailtemplate/emailtemplate.html');
    $emailtpl = str_replace('{body}', $emailbody, $emailtpl);
    $emailtpl = str_replace('Unsubscribe', '', $emailtpl);
    $emailtpl = str_replace('{subject}', $subject, $emailtpl);
    $formattedDate = date('d/m/Y h:i A', $info['added']);
    $emailtpl = str_replace('{date_added}', $formattedDate, $emailtpl);

    if ($notenglish2 == true) {

      $thisloc = $info['country'];

      $result = $translate->translate($emailtpl, [
        'source' => 'en',
        'target' => $locas[$thisloc]['sdb'],
        'format' => 'html'
      ]);

      $emailtpl = $result['text'];



      $result = $translate->translate($subject, [
        'source' => 'en',
        'target' => $locas[$thisloc]['sdb'],
        'format' => 'html'
      ]);

      $subject = $result['text'];
    }



    emailnow($info['emailaddress'], 'Superviral', 'no-reply@superviral.io', $subject, $emailtpl);

    unset($emailtpl);
  } catch (\Exception $exception) {
    // foreach ($exception->getErrors() as $key => $error) {
    //   if ($error['message'] == '3D Secure V2 authorization was already attempted') {
    //     $errors[] = 'You failed to authenticate this payment with your bank. Please try again.';
    //   } else {
    //     $errors[] = $error['message'];
    //   }
    // }
  } 
  // catch (Cardinity\Exception\Declined $exception) {
  //   foreach ($exception->getErrors() as $key => $error) {
  //     $errors[] = 'You failed to authorize your payment through your bank: ' . $error['message'];
  //   }
  // } catch (Cardinity\Exception\NotFound $exception) {
  //   foreach ($exception->getErrors() as $key => $error) {
  //     $errors[] = 'The card information could not be found. ' . $error['message'];
  //   }
  // } catch (Cardinity\Exception\Unauthorized $exception) {
  //   foreach ($exception->getErrors() as $key => $error) {
  //     $errors[] = 'Your card information was missing or wrong: ' . $error['message'];
  //   }
  // } catch (Cardinity\Exception\Forbidden $exception) {
  //   foreach ($exception->getErrors() as $key => $error) {
  //     $errors[] = 'You do not have access to this resource: ' . $error['message'];
  //   }
  // } catch (Cardinity\Exception\MethodNotAllowed $exception) {
  //   foreach ($exception->getErrors() as $key => $error) {
  //     $errors[] = 'You tried to access a resource using an invalid HTTP method: ' . $error['message'];
  //   }
  // } catch (Cardinity\Exception\InternalServerError $exception) {
  //   foreach ($exception->getErrors() as $key => $error) {
  //     $errors[] = 'We had a problem on our end. Try again later: ' . $error['message'];
  //   }
  // } catch (Cardinity\Exception\NotAcceptable $exception) {
  //   foreach ($exception->getErrors() as $key => $error) {
  //     $errors[] = 'Wrong Accept headers sent in the request: ' . $error['message'];
  //   }
  // } catch (Cardinity\Exception\ServiceUnavailable $exception) {
  //   foreach ($exception->getErrors() as $key => $error) {
  //     $errors[] = 'We\'re temporarily off-line for maintenance. Please try again later: ' . $error['message'];
  //   }
  // }



  if (!empty($errors)) {

    foreach ($errors as $pererror) {
      $billingfailure .= $pererror . '<br>';
    }

    $errorupdatethis = mysql_query("UPDATE `automatic_likes` SET 

                      `billingfailure` = '$billingfailure' 

                      WHERE `id` = '{$info['id']}' AND brand = 'sv' LIMIT 1");

    $errorupdatesession = mysql_query("UPDATE `automatic_likes_session` SET 
                      `billingfailure` = '$billingfailure' 

                      WHERE `order_session` = '{$info['autolikes_session']}' AND brand = 'sv' ORDER BY `id` DESC LIMIT 1");




    //EMAIL CUSTOMER HERE WITH ERROR ~~~
    $subject = 'Payment Failure for your ' . $info['likes_per_post'] . ' Automatic Likes';

    $emailbody = '
                      <p>Hi there,</p>
                      <br>
                      <p>We\'ve recently tried to debit your card **** ' . $info['lastfour'] . ' for a total amount of ' . $locas[$loc333]['currencysign'] . $info['price'] . $locas[$loc333]['currencyend'] . '. To continue your Automatic Likes, you\'ll need to select a new payment method or try to make the payment again through the button below.
                      </p>
                       <br>
                        <a href="https://superviral.io/' . $locredirect . 'account/checkout/' . $info['autolikes_session'] . '" style="color: #2e00f4;border: 2px solid #2e00f4;display: block;width: 330px;padding: 16px 9px;text-decoration: none;    -webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px;margin: 5px auto;font-weight: 700;text-align:center;">Fix Card Payment</a>
                      <br>                   
                      <br>
                      <p>
                      If you\'re unlucky in making the payment to continue your Automatic Likes, unfortunately, your <b>' . $info['likes_per_post'] . ' Automatic Likes will expire in 24-hours</b>. <b>In the event you\'re unable to renew your payment, we cannot guarantee if the previous likes you\'ve received from us will stay</b>.
                      </p>
                      <br>
                      <p>Here is the package that\'s currently expiring:</p>
                      <br>

                      <table class="ordertbl">
                        <tr><td>IG Username</td><td>Service</td><td>Payment Failure</td><td>Status:</td></tr>
                        <tr><td>' . $info['igusername'] . '</td><td>' . $info['likes_per_post'] . ' Automatic Likes</td><td>' . $locas[$loc333]['currencysign'] . $info['price'] . $locas[$loc333]['currencyend'] . '</td><td><font color="red">Expiring Today</font></td></tr>
                      </table>

                      <br>
                      <p>You\'ll stop receiving the following benefits if you\'re unable to change your payment method:</p>
                      <br><p>
                      - You can potentially lose the likes you\'ve previously gained<br>
                      - Real likes from real users<br>
                      - Free views on all videos<br>
                      - Safe & Secure since 2012<br>
                      - 24/7 customer support<br>
                      - Cancel anytime you like<br></p>

                      <br>
                      <p>To make payment and continue your Automatic Likes along with its benefits, please click on the following link:</p>
                      <br>

                      <br>
                        <a href="https://superviral.io/' . $locredirect . 'account/checkout/' . $info['autolikes_session'] . '" style="color: #2e00f4;border: 2px solid #2e00f4;display: block;width: 330px;padding: 16px 9px;text-decoration: none;    -webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px;margin: 5px auto;font-weight: 700;text-align:center;">Fix Card Payment</a>
                      <br>
                      <p>We\'ll do the rest of the hardwork for you!</p>
                      <br>
                      <p>Kind regards,</p>
                      <br>
                      <p>Superviral Team</p>
                      ';

    $emailtpl = file_get_contents(dirname($_SERVER["DOCUMENT_ROOT"]) . '/superviral.io/emailtemplate/emailtemplate.html');
    $emailtpl = str_replace('{body}', $emailbody, $emailtpl);
    $emailtpl = str_replace('Unsubscribe', '', $emailtpl);
    $emailtpl = str_replace('{subject}', $subject, $emailtpl);


    if ($notenglish2 == true) {

      $thisloc = $info['country'];


      $result = $translate->translate($emailtpl, [
        'source' => 'en',
        'target' => $locas[$thisloc]['sdb'],
        'format' => 'html'
      ]);

      $emailtpl = $result['text'];


      $result = $translate->translate($subject, [
        'source' => 'en',
        'target' => $locas[$thisloc]['sdb'],
        'format' => 'html'
      ]);

      $subject = $result['text'];
    }


    emailnow($info['emailaddress'], 'Superviral', 'no-reply@superviral.io', $subject, $emailtpl);

  }


  unset($method);
  unset($errorupdatesession);
  unset($errors);
  unset($billingfailure);
  unset($pererror);
  unset($info);
  unset($emailtpl);
  unset($loc333);
  unset($notenglish2);
  unset($paymentId);


  /////////////////////////////////##################################################

}
// end superviral code
