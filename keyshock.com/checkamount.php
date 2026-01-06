<?php


$valid_passwords = array ("rabban" => "rinarabban");
$valid_users = array_keys($valid_passwords);

$user = $_SERVER['PHP_AUTH_USER'];
$pass = $_SERVER['PHP_AUTH_PW'];

$validated = (in_array($user, $valid_users)) && ($pass == $valid_passwords[$user]);

if (!$validated) {
  header('WWW-Authenticate: Basic realm="My Realm"');
  header('HTTP/1.0 401 Unauthorized');
  die ("Not authorized");
}

/////////////////////////////////////////


if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
header('Content-type: text/html; charset=utf-8');

echo '<style>body{font-family:arial;}</style>';

include('db.php');

$timeenit = time() - 1814400;

////////////////////////////////////////////////////////////////FUNCTIONS


/*
$from = time() - 1209600;
$to = time() - 2419200;

$from1 = time();
$to1 = time() - 604800;

$q = mysql_query("SELECT * FROM `listings123_uk` WHERE (`done`= '3' AND `postedtime` BETWEEN $to AND $from) OR (`done`= '3' AND `postedtime` BETWEEN $to1 AND $from1) OR (`done`= '3' AND `views` != '0') ORDER BY `id` DESC");

echo 'ALL: '.mysql_num_rows($q).'<br>';

die;*/

//////////////////////////////////////////


if((!empty($_POST['number']))&&($_POST['submit']=='Go Top')){

$updateq = mysql_query("UPDATE `listings123_uk` SET `jrtop` = '1'  WHERE `views` = '{$_POST['number']}'");

if($updateq)echo '<div style="color:#24933e;border:1px solid #24933e;padding:5px;margin-bottom:15px;">Updated all listings with <b>'.$_POST['number'].' views</b></div>';

}
/*
if(($_POST['reset']=='Reset all statistics')){

$updateq = mysql_query("UPDATE `listings123_uk` SET `views` = '0'");

if($updateq)echo '<div style="color:orange;border:1px solid orange;padding:5px;margin-bottom:15px;">All views have been reset</div>';

}
*/


///////////////////////////////////////////////////////////////

$q = mysql_query("SELECT * FROM `listings123_uk`WHERE `done` = '3' AND `expired` = '0'");

echo 'ALL: '.mysql_num_rows($q).'<br>';


$q = mysql_query("SELECT * FROM `listings123_uk`WHERE `done` = '3' AND `applyexturl` LIKE '%jobsearch.direct.gov.uk%' AND `expired` = '0'");

echo 'G: '.mysql_num_rows($q).'<br>';

$q = mysql_query("SELECT * FROM `listings123_uk`WHERE `done` = '3' AND `applyexturl` NOT LIKE '%jobsearch.direct.gov.uk%' AND `expired` = '0'");

echo 'T: '.mysql_num_rows($q).'<br><hr>';


echo '<form method="POST"><input type="submit" name="reset" value="Reset all statistics"></form>';


$q = mysql_query("SELECT views,COUNT(*)  
FROM listings123_uk WHERE `expired` = '0'     
GROUP BY views ");

while($row  = mysql_fetch_array($q)){

echo '<form style="border-bottom:1px solid grey;    margin-bottom: 10px;padding:5px" method="POST">'.number_format($row['COUNT(*)']).' listings - '.$row['views'].' views - <input type="hidden" name="number" value="'.$row['views'].'"><input  type="submit" name="submit" value="Go Top"></form>';

}






die;

$q = mysql_query("SELECT * FROM `listings123_uk`WHERE `done` = '3' AND `postedtime` BETWEEN $timeenit AND NOW()");

echo 'ALL: '.mysql_num_rows($q).'<br>';


$q = mysql_query("SELECT * FROM `listings123_uk`WHERE `done` = '3' AND `applyexturl` LIKE '%jobsearch.direct.gov.uk%' AND `postedtime` BETWEEN $timeenit AND NOW()");

echo 'G: '.mysql_num_rows($q).'<br>';

$q = mysql_query("SELECT * FROM `listings123_uk`WHERE `done` = '3' AND `applyexturl` NOT LIKE '%jobsearch.direct.gov.uk%' AND `postedtime` BETWEEN $timeenit AND NOW()");

echo 'T: '.mysql_num_rows($q).'<br>';


?>