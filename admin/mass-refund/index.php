<?php

$host = $_SERVER['HTTP_HOST']; // Get the current host (e.g., anuj.etra.group)
$subdomain = explode('.', $host)[0]; // Get the first part of the domain
$initial = $subdomain . '.';
$subdomain = '/' . $subdomain . '/etra.group';
if (!empty($initial) && $initial != "etra.") $_SERVER['DOCUMENT_ROOT'] = $_SERVER['DOCUMENT_ROOT'] . $subdomain;
$awsNotNeeded = true;
require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/common/core/layout.php';

$urlKey = str_replace('app_', '', $ev_appid);
$evervaultApiKey = $ev_key;
$evervaultAppId = $ev_appid;
$stripeSecretKey = $ev_stripe_skey;
$stripePublishableKey = $ev_stripe_pkey;


$tpl = file_get_contents('tpl.html');

$id = addslashes($_GET['id']);

$orderid = addslashes($_POST['orderids']);
$order_session = addslashes($_POST['order_sessions']);

$alorderid = addslashes($_POST['alorderids']);
$alautolikesid = addslashes($_POST['alautolikesids']);

$customamount = addslashes($_POST['percentages']);
$undoRefund = addslashes($_POST['undoRefund']);

$orderidArr = explode(',', $orderid);
$orderSessionArr = explode(',', $order_session);
$alorderidArr = explode(',', $alorderid);
$alautolikesidArr = explode(',', $alautolikesid);
$customamountArr = explode(',', $customamount);
// use Google\Cloud\Translate\V2\TranslateClient;

// require dirname($_SERVER["DOCUMENT_ROOT"]).'/etra.group/common/gtranslate/index.php';

// $translate = new TranslateClient(['key' => $googletranslatekey]);



// require_once dirname($_SERVER["DOCUMENT_ROOT"]) . '/etra.group/common/cardinity-php-master/vendor/autoload.php';

// /* Start to develop here. Best regards https://php-download.com/ */

// use Cardinity\Client;
// use Cardinity\Method\Payment;
// use Cardinity\Exception;
// use Cardinity\Method\ResultObject;
// use Cardinity\Method\Refund;

// $client = Client::create([
// 	'consumerKey' => $cardinitykey,
// 	'consumerSecret' => $cardinitysecret,
// ]);

// if (!empty($undoRefund) && !empty($orderid)) {

// 	// undo refund
// 	$updateq = mysql_query("UPDATE `orders` SET `refund` = '0', refundamount = '0' WHERE `id` = '$orderid' LIMIT 1");
// 	// if($updateq)$success = '<div class="emailsuccess">Undo refund</div>';

// 	// if(!empty($success))$tpmsg = '<div style="padding:10px;">'.$tpmsg.'</div>';
// }

// if (!empty($undoRefund) && !empty($alorderid)) {

// 	// undo refund
// 	$updateq = mysql_query("UPDATE `automatic_likes_billing` SET `refunded` = '0', amount = '0' WHERE `id` = '$alorderid' LIMIT 1");


// 	// if($updateq)$success = '<div class="emailsuccess">Undo refund</div>';

// 	// if(!empty($success))$tpmsg = '<div style="padding:10px;">'.$tpmsg.'</div>';
// }






if (!empty($orderid) && empty($undoRefund) && empty($alorderid)) {


	for ($i = 0; $i < count($orderidArr); $i++) {


		$getrefundinfoq = mysql_query("SELECT * FROM `orders` WHERE `id` = '".$orderidArr[$i]."' AND `order_session` = '".$orderSessionArr[$i]."' LIMIT 1");
		$refundinfo = mysql_fetch_array($getrefundinfoq);

		$brandName = getBrandSelectedName($refundinfo['brand']);
		$brand = $refundinfo['brand'];
		$countryloc = $refundinfo['country'];

		$amount = $locas[$countryloc]['currencysign'] . $customamountArr[$i];

		$recipient = $refundinfo['emailaddress'];


		//die('Recipient: '.$recipient);



		$lastfour = $refundinfo['lastfour'];

		if ($lastfour == '0') $lastfour = '**** (ApplePay)';

		$ordernum = $refundinfo['id'];
		$service = $refundinfo['amount'] . ' Instagram ' . ucwords($refundinfo['packagetype']);
		$payment  = $refundinfo['price'];

		$orderDay = $refundinfo['added'];
		$curTime = date('Y-m-d H:i:s', time());
		$endTime = date('Y-m-d H:i:s', strtotime("tomorrow", $orderDay) - 1); // currentdate end time


		if (!empty($refundinfo['payment_id'])) {

			//echo 'PAYMENT: '.$payment.' - '.$customamount.'<hr>';

			$now2 = date('omdHis', time());
			$now2 = substr($now2, 4);
			$now2 = date("Y") . $now2;

			// Step 1: Get authentication token
			$token_data = [
				'app_id' => $acquired_app_id,
				'app_key' => $acquired_app_key
			];
		
			$token_response = curl_request($acquired_base_url . 'login', json_encode($token_data));
		
			if (empty($token_response['access_token'])) {
				$tpmsg .= '<div style="padding:10px;">Authentication failed with payment gateway - ID:'. $refundinfo['id'] .'</div>';
				continue;
			}
		
			$access_token = $token_response['access_token'];

			// $refundonacquired =  array(

			// 	"timestamp" => $now2,
			// 	"company_id" => $acquiredaccountid,
			// 	"company_pass" => $acquiredcompanypass,

			// 	"transaction" => array(
			// 		// "transaction_type" => 'REFUND',
			// 		"original_transaction_id" => $refundinfo['payment_id'],
			// 		// "amount" => $customamount,
			// 	),


			// );

			if ($endTime > $curTime) {
				$refundURL = $acquired_base_url. "transactions/{$refundinfo['payment_id']}/void";
				$curlPost = [
						'reference' => 'void ID '.$refundinfo['id']
				];
				// $transactType = "VOID";
				// $refundonacquired['transaction']["transaction_type"] = $transactType;
			} else {
				// $transactType = "REFUND";
				// $refundonacquired['transaction']["transaction_type"] = $transactType;
				// $refundonacquired['transaction']["amount"] = $customamountArr[$i];
				$refundURL = $acquired_base_url. "transactions/{$refundinfo['payment_id']}/refund";
				$curlPost = [
						'amount' => $customamountArr[$i],
						'reference' => 'refund ID '.$refundinfo['id']
				];
			}

			$curl = curl_init();

			curl_setopt_array($curl, [
				CURLOPT_URL => $refundURL ,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => json_encode($curlPost),
				CURLOPT_HTTPHEADER => [
					"accept: application/json",
					"content-type: application/json",
					'Authorization: Bearer ' . $access_token,
				],
			]);

			$json_response = curl_exec($curl);
			$err = curl_error($curl);
			curl_close($curl);
			if ($err) {
				echo "cURL Error #:" . $err;
				continue;
			} else {
				$response = json_decode($json_response, true);
			}

			// $request_hash = hash('sha256', $now2 . $transactType . $acquiredaccountid . $refundinfo['payment_id'] . $acquiredsecretpasscode);

			// $refundonacquired['request_hash'] = $request_hash;


			// $url = "https://gateway.acquired.com/api.php";
			// //$url = "https://qaapi.acquired.com/api.php";
			// $refundonacquired = json_encode($refundonacquired);

			// $curl = curl_init($url);
			// curl_setopt($curl, CURLOPT_HEADER, false);
			// curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			// curl_setopt(
			// 	$curl,
			// 	CURLOPT_HTTPHEADER,
			// 	array("Content-type: application/json")
			// );
			// curl_setopt($curl, CURLOPT_POST, true);
			// curl_setopt($curl, CURLOPT_POSTFIELDS, $refundonacquired);

			// $json_response = curl_exec($curl);

			// $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);


			// curl_close($curl);

			// $response = json_decode($json_response, true);

			/*
					echo '<pre>';
					print_r($response);
					echo '</pre>';
					*/
		}


		// if (preg_match("/[a-z]/i", $refundinfo['payment_id'])) {


		// 	try {

		// 		$cardinityamt = floatval($customamountArr[$i]);

		// 		$method = new Refund\Create(
		// 			$refundinfo['payment_id'],
		// 			$cardinityamt,
		// 			'my description'
		// 		);

		// 		$refund = $client->call($method);
		// 	} catch (\Throwable $e) {
		// 		// catches all ClientExceptions
		// 	} catch (RequestException $e) {
		// 		// catches all RequestExceptions
		// 	}
		// }

		// Check for Stripe payment_id (automatic likes billing)
		if(stripos($refundinfo['payment_id'], 'pi_') !== false || stripos($refundinfo['payment_id'], 'ch_') !== false){

			try {
				// Use Evervault relay for Stripe API
				$refundUrl = "https://api-stripe-com-app-$urlKey.relay.evervault.app/v1/refunds";

				$refundData = [];

				// Check if it's a payment intent or charge
				if (stripos($refundinfo['payment_id'], 'pi_') !== false) {
					$refundData['payment_intent'] = $refundinfo['payment_id'];
				} elseif (stripos($refundinfo['payment_id'], 'ch_') !== false) {
					$refundData['charge'] = $refundinfo['payment_id'];
				}

				// Add amount (Stripe expects amount in cents)
				$refundData['amount'] = (int)(floatval($customamountArr[$i]) * 100);
				$refundData['reason'] = 'requested_by_customer';

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $refundUrl);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($refundData));
				curl_setopt($ch, CURLOPT_HTTPHEADER, [
					"Content-Type: application/x-www-form-urlencoded",
            		"x-evervault-api-key: $evervaultApiKey",
            		"x-evervault-app-id: $evervaultAppId",
            		"Authorization: Bearer $stripeSecretKey"
				]);

				$refundResponse = curl_exec($ch);
				$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				curl_close($ch);

				$refundResult = json_decode($refundResponse, true);

				if ($httpCode == 200 && isset($refundResult['id'])) {
					$tpmsg = '<div class="emailsuccess">Stripe refund processed successfully: ' . $refundResult['id'] . '</div>';
				} else {
					$errorMsg = isset($refundResult['error']['message']) ? $refundResult['error']['message'] : 'Unknown error';
					$tpmsg = '<div class="emailfailed">Stripe refund failed: ' . $errorMsg . '</div>';
				}
			} catch (\Exception $e) {
				$tpmsg = '<div class="emailfailed">Stripe refund error: ' . $e->getMessage() . '</div>';
			}

		}



		if ($brand == 'sv' || $brand == 'fb')
			require(dirname($_SERVER["DOCUMENT_ROOT"]) . '/superviral.io/emailrefund.php');

		if ($brand == 'to')
			require(dirname($_SERVER["DOCUMENT_ROOT"]) . '/tikoid.com/emailrefund.php');

		/*echo 'Normal Recipient: '.$recipient.'<hr>';
	echo $bodyHtml;*/


		$now = time();

		$refundreason = ' - requested by customer';
		if ($refundinfo['refundamount'] == 'fraud') {
			$refundreason = ' - due to irregular card payment activity';
		}

		$refundprice = sprintf('%.2f', $info['price'] / 100);
		$refundtrackingmsg = $refundinfo['order_response'] . '~~~' . $now . '###A refund for ' . $customamountArrt[$i] . ' issued to the card ending with ' . $refundinfo['lastfour'] . $refundreason . '###0.2';

		$updateq = mysql_query("UPDATE `orders` SET `refund` = '2',`order_response` = '$refundtrackingmsg',`refundtime` = '$now' WHERE `id` = '" . $orderidArr[$i] . "' AND `order_session` = '" . $orderSessionArr[$i] . "' LIMIT 1");

		$refundreason = '';

		if ($updateq) $success = '<div class="emailsuccess">A refund of ' . $customamountArr[$i] . ' has been issued to Order #' . $orderidArr[$i] . $refundreason . '. Email sent to ' . $recipient . '</div>';

		if (!empty($tpmsg)) $tpmsg = '<div style="padding:10px;">' . $tpmsg . '</div>';
	}
} //END OF IF SOMETHING SUBMITTED



///////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////

if (!empty($alorderid) && empty($undoRefund)) {


	for ($i = 0; $i < count($alorderidArr); $i++) {


		$getrefundinfoq = mysql_query("SELECT * FROM `automatic_likes_billing` WHERE `id` = '".$alorderidArr[$i]."' AND `auto_likes_id` = '".$alautolikesidArr[$i]."' LIMIT 1");
		$refundinfo = mysql_fetch_array($getrefundinfoq);
		$brand = $refundinfo['brand'];

		$getrefundaccountq = mysql_query("SELECT * FROM `accounts` WHERE `id` = '{$refundinfo['account_id']}' LIMIT 1");
		$getrefundaccountinfo = mysql_fetch_array($getrefundaccountq);

		$getrefundsubscriptionq = mysql_query("SELECT * FROM `automatic_likes` WHERE `id` = '{$refundinfo['auto_likes_id']}' LIMIT 1");
		$getrefundsubscriptioninfo = mysql_fetch_array($getrefundsubscriptionq);

		$refundinfo['country'] = $getrefundsubscriptioninfo['country'];

		$countryloc = $refundinfo['country'];

		$amount = $locas[$countryloc]['currencysign'] . $customamountArr[$i];

		$recipient = $getrefundaccountinfo['email'];



		// try {
		// 	$cardinityamt = floatval($customamountArr[$i]);

		// 	$method = new Refund\Create(
		// 		$refundinfo['payment_id'],
		// 		$cardinityamt,
		// 		'my description'
		// 	);

		// 	$refund = $client->call($method);
		// } catch (\Throwable $e) {
		// 	// catches all ClientExceptions
		// } catch (RequestException $e) {
		// 	// catches all RequestExceptions
		// }

		try {
				// Use Evervault relay for Stripe API
				$refundUrl = "https://api-stripe-com-app-$urlKey.relay.evervault.app/v1/refunds";

				$refundData = [];

				// Check if it's a payment intent or charge
				if (stripos($refundinfo['payment_id'], 'pi_') !== false) {
					$refundData['payment_intent'] = $refundinfo['payment_id'];
				} elseif (stripos($refundinfo['payment_id'], 'ch_') !== false) {
					$refundData['charge'] = $refundinfo['payment_id'];
				}

				// Add amount (Stripe expects amount in cents)
				$refundData['amount'] = (int)(floatval($customamountArr[$i]) * 100);
				$refundData['reason'] = 'requested_by_customer';

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $refundUrl);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($refundData));
				curl_setopt($ch, CURLOPT_HTTPHEADER, [
					"Content-Type: application/x-www-form-urlencoded",
            		"x-evervault-api-key: $evervaultApiKey",
            		"x-evervault-app-id: $evervaultAppId",
            		"Authorization: Bearer $stripeSecretKey"
				]);

				$refundResponse = curl_exec($ch);
				$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				curl_close($ch);

				$refundResult = json_decode($refundResponse, true);

				if ($httpCode == 200 && isset($refundResult['id'])) {
					$tpmsg = '<div class="emailsuccess">Stripe refund processed successfully: ' . $refundResult['id'] . '</div>';
				} else {
					$errorMsg = isset($refundResult['error']['message']) ? $refundResult['error']['message'] : 'Unknown error';
					$tpmsg = '<div class="emailfailed">Stripe refund failed: ' . $errorMsg . '</div>';
				}
			} catch (\Exception $e) {
				$tpmsg = '<div class="emailfailed">Stripe refund error: ' . $e->getMessage() . '</div>';
			}
		$recipient = $getrefundaccountinfo['email'];

		$lastfour = $refundinfo['lastfour'];
		if ($lastfour == '0') $lastfour = '**** (ApplePay)';
		if (empty($lastfour)) $lastfour = '****';

		$ordernum = $refundinfo['auto_likes_id'];
		$service = $refundinfo['likesperpost'] . ' Instagram Automatic Likes';
		$payment  = $refundinfo['amount'];

		if (empty($brand)) $brand = 'sv';

		if ($brand == 'sv' || $brand == 'fb')
			require(dirname($_SERVER["DOCUMENT_ROOT"]) . '/superviral.io/emailrefund.php');


		$now = time();

		$updateq = mysql_query("UPDATE `automatic_likes_billing` SET `refunded` = '$now' WHERE `id` = '".$alorderidArr[$i]."' AND `auto_likes_id` = '".$alautolikesidArr[$i]."' LIMIT 1");


		$refundreason = ' - requested by customer';
		if ($refundinfo['refundamount'] == 'fraud') {
			$refundreason = ' - due to irregular card payment activity';
		}


		$refundreason = '';


		if ($updateq) $success = '<div class="emailsuccess">A refund of £' . $customamountArr[$i] . ' has been issued to Subscription #' . $alorderidArr[$i] . $refundreason . '. Email sent to ' . $recipient . '</div>';

		if (!empty($tpmsg)) $tpmsg = '<div style="padding:10px;">' . $tpmsg . '</div>';
	}
} //END OF IF SOMETHING SUBMITTED




///////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////


$count = mysql_query("SELECT * FROM `orders` WHERE `refund` = '1' AND `disputed` = '0'");
$totalleft = mysql_num_rows($count) . ' Refunds Remaining';
//$q = mysql_query("SELECT * FROM `orders` WHERE `refund` = '1' AND `disputed` = '0' AND `payment_id` LIKE '%-%' LIMIT 1");

$refundAmntq = mysql_query("SELECT `refundamount` FROM `orders` WHERE `refund` = '1' AND `disputed` = '0' group by `refundamount`");
$result .= '<tr>
							
							<td colspan = "8" style="position:relative;">
	                    	        <input type="submit" onclick="return confirm(\'Are you sure to mass refund?\');"
	                    	        name="massrefund" class="btn btn3 report nlbtn copy-buttonn " value="Mass Refund"
	                    	        fdprocessedid="a6wljh" style="background-color:#fff">
							</td>
						</tr>';
while ($refundAmntData = mysql_fetch_array($refundAmntq)) {

	$q = mysql_query("SELECT * FROM `orders` WHERE `refund` = '1' AND `disputed` = '0' AND `refundamount` = '{$refundAmntData['refundamount']}'");

	$total = mysql_num_rows($count) . ' Refunds Remaining';
	if ($total > 0) {
		$result .= '<tr style="background: #f1f1f1;"><td colspan="8"><h2>% ' . $refundAmntData['refundamount'] . ' Refunds</h2></td></tr>';
		$result .= "<tr> 
	                    <td>Company</td> 
	                    <td>Order Placed</td> 
	                    <td>ℹ Order ID</td>
	                    <td>Email</td>
	                    <td>Price</td>
	                    <td>Reason/amount</td>
	                    <td>Payment ID</td>
	                    <td>Amount</td>
	                </tr>";

		while ($infoa = mysql_fetch_array($q)) {

			$keyword = getSocialMediaSource($infoa['socialmedia']);
			$brandName = getBrandSelectedName($infoa['brand']);
			$brand = $refundinfo['brand'];

			if (strpos($infoa['payment_id'], 'pi_') !== false) $payment_idshow = 'Stripe: <a target="_BLANK" rel="noopener noreferrer" href="https://dashboard.stripe.com/payments/' . $infoa['payment_id'] . '">' . $infoa['payment_id'] . '</a>';

			// if (strpos($infoa['payment_id'], '-') !== false) $payment_idshow = 'Cardinity: <a target="_BLANK" rel="noopener noreferrer" href="https://my.cardinity.com/payment/show/' . $infoa['payment_id'] . '">' . $infoa['payment_id'] . '</a>';

			if (is_numeric($infoa['payment_id'])) $payment_idshow = 'Acquired: <a target="_BLANK" rel="noopener noreferrer" href="https://hub.acquired.com/#transactions/detail/' . $infoa['payment_id'] . '">' . $infoa['payment_id'] . '</a>';

			$result .= '<tr> 
							<td><img src="/admin/assets/icons/' . $brandName . '.svg"></td> 
							<td>' . date('l jS \of F Y H:i:s ', $infoa['added']) . '</td> 
							<td>' . $infoa['id'] . '</td>
							<td>' . $infoa['emailaddress'] . '</td>
							<td>Price: £' . sprintf('%.2f', $infoa['price'] / 100) . '</td>
							<td>' . $infoa['refundamount'] . '</td>
							<td>' . $payment_idshow . '</td>
							<input type="hidden" autocomplete="off" name="id" value="' . $infoa['id'] . '" class="input">
							<input type="hidden" name="orderid" value="' . $infoa['id'] . '">
							<input type="hidden" name="order_session" value="' . $infoa['order_session'] . '">
							<input type="hidden" name="percentage" value="' . sprintf('%.2f', $infoa['price'] / 100) . '">
							<td style="position:relative;">
								<span style="position: absolute;left: 25px;top: 22px;">£</span>
								' . sprintf('%.2f', $infoa['price'] / 100) . '
	                   		</td>
						</tr>
						';
		}
	}
}





///////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////


if (mysql_num_rows($count) == '0') {

	$count = mysql_query("SELECT * FROM `automatic_likes_billing` WHERE `refunded` = '1' ");

	$totalleft = mysql_num_rows($count) . ' Refunds Remaining';



	$q = mysql_query("SELECT * FROM `automatic_likes_billing` WHERE `refunded` = '1' ");

	$totalleft = mysql_num_rows($q) . ' Refunds Remaining';
	$result = "<tr> 
				<td>Company</td> 
				<td>Order Placed</td> 
				<td>ℹ Automatic Likes Billing ID</td>
				<td>Email</td>
				<td>Price</td>
				<td>Reason/amount</td>
				<td>Payment ID</td>
				<td>Amount</td>
			   </tr>";
	//go into AL refund mode

	while ($infoa = mysql_fetch_array($q)) {

		$brandName = getBrandSelectedName($infoa['brand']);
		$brand = $refundinfo['brand'];

		$accountq = mysql_query("SELECT * FROM `accounts` WHERE `id` = '{$infoa['account_id']}' LIMIT 1");
		$accountinfo = mysql_fetch_array($accountq);

		//AL TABLE

		$result .= '<tr> 
						<td> <img src="/admin/assets/icons/' . $brandName . '.svg"> </td>
						<td> ' . date('l jS \of F Y H:i:s ', $infoa['added']) . '</td>
						<td> ' . $infoa['id'] . '</td>
						<td> ' . $infoa['email'] . '</td>
						<td>Price: £' . $infoa['amount']  . '</td>
						<td>' . $infoa['refundamount'] . '</td>
						<input type="hidden" autocomplete="off" name="id" value="' . $infoa['id'] . '" class="input">
						<input type="hidden" name="postemailaddress" value="' . $accountinfo['email'] . '">
						<input type="hidden" name="alorderid" value="' . $infoa['id'] . '">
						<input type="hidden" name="alautolikesid" value="' . $infoa['auto_likes_id'] . '">
						<input type="hidden" name="percentage" value="' . $infoa['amount'] . '">
						<td style="position:relative;"><span style="position: absolute;left: 25px;top: 22px;">£</span>
						' . $infoa['amount'] . '
						</td>
					</tr>
					';
	}

	$result .= '<tr>
					<td></td>
					<td style="position:relative;">
							<input type="submit" onclick="return confirm(\'Are you sure to mass refund?\');" name="massrefund" class="btn btn3 report copy-buttonn nlbtn" value="Mass Refund">
					</td>
				</tr>';
}


if (mysql_num_rows($count) == '0') $showorno = 'display:none;';


$tpl = str_replace('{totalleft}', $totalleft, $tpl);
$tpl = str_replace('{result}', $result, $tpl);

output($tpl, $options);
