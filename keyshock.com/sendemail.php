<?php

if(substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
header('Content-type: text/html; charset=utf-8');
echo '<meta charset="UTF-8"><style>body{font-family:arial;font-size:10pt;}</style>';

include_once('../extdb.php');


//VARIABLES AND FUNCTION

$whatdate = '1510772473';

function ago($time)
{$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
   $lengths = array("60","60","24","7","4.35","12","10");
   $now = time();
       $difference     = $now - $time;
       $tense         = 'ago';
   for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
       $difference /= $lengths[$j];
   }
   $difference = round($difference);
   if($difference != 1) {
       $periods[$j].= "s";
   }   return "$difference $periods[$j] ago";}

date_default_timezone_set('Europe/London');


//

$q = mysql_query("SELECT * FROM `applicants` WHERE `emailaddress` != '' AND `done` = '0' AND `unsubscribe` = '0' AND `country` != 'IT' AND `country` != ''  LIMIT 1");
if(mysql_num_rows($q)==0){$q = mysql_query("SELECT * FROM `subscription` WHERE `emailaddress` != '' AND `done` = '0' AND `unsubscribe` = '0' AND `country` != 'IT' AND `country` != '' LIMIT 1");

if(mysql_num_rows($q)==0){echo 'Subscribers list is complete for this week - THANK YOU!';die;}
}


$personinfo = mysql_fetch_array($q);


//Search for all listing subscriptions and applicants listings
$searchq = mysql_query("SELECT * FROM `applicants` WHERE `emailaddress` = '{$personinfo['emailaddress']}' AND `done` = '0' AND `unsubscribe` = '0'");
while($listinginfo = mysql_fetch_array($searchq)){

	$expirydate = $whatdate + 604800;

$formattedtime = ago($whatdate);

if ((strpos($formattedtime, 'second') !== false)||(strpos($formattedtime, 'minute') !== false)||(strpos($formattedtime, 'hour') !== false)) {$formattedtime =  'Today!';}


	$updateq = mysql_query("UPDATE `listings123_uk` SET `postedtime` = '$whatdate', `ended` = '$expirydate' WHERE `id` = '{$listinginfo['advert_id']}' LIMIT 1");
	$fetchlistinginfoq = mysql_query("SELECT `id`, `jrtitle`,`location`,`url` FROM `listings123_uk` WHERE `id` = '{$listinginfo['advert_id']}' LIMIT 1");

	if(mysql_num_rows($fetchlistinginfoq)=='0')continue;

	$fetchlistinginfo = mysql_fetch_array($fetchlistinginfoq); 

	$results .= '								<table bgcolor="white" style="background-color:white;margin-bottom:15px;"><tr><td bgcolor="white" style="background-color:white;padding:15px;" >
									<p><a href="http://keyshock.com/view/'.$fetchlistinginfo['url'].'-'.$listinginfo['advert_id'].'?jrindexed=true" style="    font-size: 19px;display:block;    font-weight: bold;    color: black;    text-decoration: none;">'.$fetchlistinginfo['jrtitle'].'
									</a></p> 
									<span style="font-size:15px;color:black;">Posted '.$formattedtime.' - '.$fetchlistinginfo['location'].'</span>
								</td><td><a href="http://keyshock.com/view/'.$listinginfo['advert_url'].'-'.$listinginfo['advert_id'].'?email=true" style="display:block;line-height:1.5"><img src="http://keyshock.com/imgs/arrow.png"></a></td></tr></table>';

}


//Search for all listing subscriptions and applicants listings
$searchq = mysql_query("SELECT * FROM `subscription` WHERE `emailaddress` = '{$personinfo['emailaddress']}' AND `done` = '0' AND `unsubscribe` = '0'");
while($listinginfo = mysql_fetch_array($searchq)){

	$expirydate = $whatdate + 604800;

$formattedtime = ago($whatdate);

if ((strpos($formattedtime, 'second') !== false)||(strpos($formattedtime, 'minute') !== false)||(strpos($formattedtime, 'hour') !== false)) {$formattedtime =  'Today!';}



	$updateq = mysql_query("UPDATE `listings123_uk` SET `postedtime` = '$whatdate', `ended` = '$expirydate' WHERE `id` = '{$listinginfo['advert_id']}' LIMIT 1");
	$fetchlistinginfoq = mysql_query("SELECT `id`, `jrtitle`,`location`,`url` FROM `listings123_uk` WHERE `id` = '{$listinginfo['advert_id']}' LIMIT 1");

	if(mysql_num_rows($fetchlistinginfoq)=='0')continue;

	$fetchlistinginfo = mysql_fetch_array($fetchlistinginfoq); 

	$results .= '								<table bgcolor="white" style="background-color:white;margin-bottom:15px;"><tr><td bgcolor="white" style="background-color:white;padding:15px;" >
									<p><a href="http://keyshock.com/view/'.$fetchlistinginfo['url'].'-'.$listinginfo['advert_id'].'?jrindexed=true" style="    font-size: 19px;display:block;    font-weight: bold;    color: black;    text-decoration: none;">'.$fetchlistinginfo['jrtitle'].'
									</a></p> 
									<span style="font-size:15px;color:black;">Posted '.$formattedtime.' - '.$fetchlistinginfo['location'].'</span>
								</td><td><a href="http://keyshock.com/view/'.$listinginfo['advert_url'].'-'.$listinginfo['advert_id'].'?email=true" style="display:block;line-height:1.5"><img src="http://keyshock.com/imgs/arrow.png"></a></td></tr></table>';

}


//SEND EMAIL WITH

$message = file_get_contents('sendemail.html');

$message = str_replace('{results}',$results,$message);
$message = str_replace('{unsub}','id='.urlencode($personinfo['id']).'&adid='.urlencode($personinfo['advert_id']).'&email='.urlencode($personinfo['emailaddress']),$message);


$to = trim($personinfo['emailaddress']);
$subject = "Today's new job offers"; 
$headers .= "From: Keyshock Job Finder<info@keyshock.com>\r\n";
$headers .= "Reply-To: evaluation-team@Keyshock.co.uk\r\n";
$headers .= 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

if ( mail($to,$subject,$message,$headers) ) {
   echo "The email has been sent!".$info['email'];
   } else {
   echo "The email has failed!".$info['email'];
   }


mysql_query("UPDATE `subscription` SET `done` = '1' WHERE `emailaddress` = '{$personinfo['emailaddress']}'");
mysql_query("UPDATE `applicants` SET `done` = '1' WHERE `emailaddress` = '{$personinfo['emailaddress']}'");

echo $message;

echo $to;

echo '<meta http-equiv="refresh" content="0.5">';

?>