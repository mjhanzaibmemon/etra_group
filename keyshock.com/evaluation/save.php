<?php


include('../db.php');


$show=$_GET['show'];
$q=$_GET['q'];
$uid=$_GET['uid'];

if(empty($uid))die;

if($show=='name')$sql=mysql_query("UPDATE `evaluation` SET `name` = '$q' WHERE `id` = '$uid' AND `ipaddress` = '{$_SERVER['REMOTE_ADDR']}' LIMIT 1");

if($show=='email')$sql=mysql_query("UPDATE `evaluation` SET `email` = '$q' WHERE `id` = '$uid' AND `ipaddress` = '{$_SERVER['REMOTE_ADDR']}' LIMIT 1");

if($show=='contactnumber')$sql=mysql_query("UPDATE `evaluation` SET `contactnumber` = '$q' WHERE `id` = '$uid' AND `ipaddress` = '{$_SERVER['REMOTE_ADDR']}' LIMIT 1");

if($show=='cvraw')$sql=mysql_query("UPDATE `evaluation` SET `cvraw` = '$q' WHERE `id` = '$uid' AND `ipaddress` = '{$_SERVER['REMOTE_ADDR']}' LIMIT 1");

if($show=='targetedjob')$sql=mysql_query("UPDATE `evaluation` SET `profession` = '$q' WHERE `id` = '$uid' AND `ipaddress` = '{$_SERVER['REMOTE_ADDR']}' LIMIT 1");




?>