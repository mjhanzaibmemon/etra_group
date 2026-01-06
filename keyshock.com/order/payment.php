<?php
/*

remove $plogordersession
remove $lognow



*/

// start time
$start_time = microtime(true);

error_reporting(E_ALL); // Error/Exception engine, always use E_ALL

ini_set('ignore_repeated_errors', TRUE); // always use TRUE

ini_set('display_errors', false); // Error/Exception display, use FALSE only in production environment or real server. Use TRUE in development environment

ini_set('log_errors', TRUE); // Error/Exception file logging engine.
ini_set('error_log', '/var/www/html/errors.log'); // Logging file path


if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
header('Content-type: text/html; charset=utf-8');

$db=1;

include('../db.php');
include('../header.php');


$priceamount= addslashes($_POST['packagePrice']);

if(empty($priceamount) || !is_numeric($priceamount)){
   $priceamount = 25;
}

$order_id = rand(100000,999999);

$tpl = file_get_contents('payment.html');

$tpl = str_replace('{applepay_price}', (float)$priceamount * 100, $tpl);
$tpl = str_replace('{applepay_card_price}', (float)$priceamount, $tpl);
$tpl = str_replace('{ev_appid}', $ev_appid, $tpl);
$tpl = str_replace('{ev_team_id}', $ev_team_id, $tpl);
$tpl = str_replace('{keyshock_merchant_id}', trim($keyshock_merchant_id), $tpl);
$tpl = str_replace('{header}', ($header), $tpl);
$tpl = str_replace('{footer}', ($footer), $tpl);
$tpl = str_replace('{order_id}', ($order_id), $tpl);

/*
$tpl = str_replace('{body}', $body, $tpl);
$tpl = str_replace('{discounturl}', $discounturl, $tpl);
$tpl = str_replace('{discountnotifcart}', $discountnotifcart, $tpl);
$tpl = str_replace('{packagetitle}', $packagetitle, $tpl);
$tpl = str_replace('{error0}', $showerror0, $tpl);
$tpl = str_replace('{error1}', $showerror1, $tpl);
$tpl = str_replace('{error2}', $showerror2, $tpl);
$tpl = str_replace('{inpnum}', $inpnumre, $tpl);
$tpl = str_replace('{inpdate}', $inpdatere, $tpl);
$tpl = str_replace('{inpcvc}', $inpcvcre, $tpl);
$tpl = str_replace('{pan}', $pan, $tpl);
$tpl = str_replace('{cardholdername}', $cardholdername, $tpl);
$tpl = str_replace('{emailaddress}', $info['emailaddress'], $tpl);

$tpl = str_replace('{sdblivecheckout}', $locredirect, $tpl);
$tpl = str_replace('{loc}', $loc, $tpl);
$tpl = str_replace('{loclinkforward}', $loclinkforward, $tpl);
$tpl = str_replace('{loclink}', $loclink, $tpl);
$tpl = str_replace('{ordersession}', $info['order_session'], $tpl);
$tpl = str_replace('{back}','/'.$loclinkforward.$locas[$loc]['order'].'/'.$locas[$loc]['order2'].'/', $tpl);
$tpl = str_replace('{redirect}', 'https://superviral.io/'.$loclinkforward.$locas[$loc]['order'].'/'.$locas[$loc]['order3-processing'].'/', $tpl);

$tpl = str_replace('{displayaccountbtn}', $displayaccountbtn, $tpl);

$tpl = str_replace('{applepayredirectsuccess}', 'https://superviral.io/'.$loclinkforward.$locas[$loc]['order'].'/'.$locas[$loc]['order3-processing'].'/', $tpl);

$tpl = str_replace('{currencycode}', $locas[$loc]['currencypp'], $tpl);
$tpl = str_replace('{countrycode}', $locas[$loc]['countrycode'], $tpl);
$tpl = str_replace('{applepayuserid}', $applepayuserid, $tpl);
$tpl = str_replace('{userBlackList}', $userBlackList, $tpl);
$tpl = str_replace('{bindSavePaymentCheckbox}', $checkForSavePaymentChecbox, $tpl);
$tpl = str_replace('{cardbrand}', $card_brand, $tpl);
$tpl = str_replace('{secondh2}', $secondh2, $tpl);
$tpl = str_replace('{cardresults}', $cardresults, $tpl);
// $tpl = str_replace('{styleCheckCardAvaialble}', $styleCheckCardAvaialble, $tpl);
// $tpl = str_replace('{styledefaultCardAvaialble}', $styledefaultCardAvaialble, $tpl);

$tpl = str_replace('{newcardprimaryclass}', $newcardprimaryclass, $tpl);
$tpl = str_replace('{onlymethodavailable}', $onlymethodavailable, $tpl);
$tpl = str_replace('{submitBtn}', $submitBtn, $tpl);
$tpl = str_replace('{googlev3recaptchakey}', $googleV3ClientKey, $tpl);
$tpl = str_replace('{recaptchaUrl}', $recaptchaUrl, $tpl);


*/
// End timer
$end_time = microtime(true);

// Calculate execution time in seconds
$execution_time_sec = $end_time - $start_time;

echo $tpl;



?>