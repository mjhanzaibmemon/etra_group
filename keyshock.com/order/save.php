<?php


include('../db.php');


$show= addslashes($_GET['show']);
$q= addslashes($_GET['q']);
$uid= addslashes($_GET['uid']);

if(empty($uid))die;

if($show=='name')$sql=mysql_query("UPDATE `cv` SET `name` = '$q' WHERE `id` = '$uid' AND `ipaddress` = '{$_SERVER['REMOTE_ADDR']}' LIMIT 1");

if($show=='email')$sql=mysql_query("UPDATE `cv` SET `email` = '$q' WHERE `id` = '$uid' AND `ipaddress` = '{$_SERVER['REMOTE_ADDR']}' LIMIT 1");

if($show=='contactnumber')$sql=mysql_query("UPDATE `cv` SET `contactnumber` = '$q' WHERE `id` = '$uid' AND `ipaddress` = '{$_SERVER['REMOTE_ADDR']}' LIMIT 1");

if($show=='cvraw')$sql=mysql_query("UPDATE `cv` SET `cvraw` = '$q' WHERE `id` = '$uid' AND `ipaddress` = '{$_SERVER['REMOTE_ADDR']}' LIMIT 1");

if($show=='targetedjob')$sql=mysql_query("UPDATE `cv` SET `targetedjob` = '$q' WHERE `id` = '$uid' AND `ipaddress` = '{$_SERVER['REMOTE_ADDR']}' LIMIT 1");


?>