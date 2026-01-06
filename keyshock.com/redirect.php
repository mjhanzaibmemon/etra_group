<?php

if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
header('Content-type: text/html; charset=utf-8');

include('db.php');
include('header.php');


$areaq = $_GET['areaq'];
$catsq = $_GET['catsq'];

$areaq1 = $_GET['area'];
$catsq1 = $_GET['type'];

///////////////////////////////////////////////// SORT OUT URL


//if((empty($areaq))&&(empty($catsq))){

if((empty($catsq1))&&(empty($areaq1))){


header('Location: /search-jobs');die;

}


if((!empty($areaq1))&&(empty($catsq1))){

$q = mysql_query("SELECT * FROM `areas` WHERE `name` = '$areaq1' LIMIT 1");
if(mysql_num_rows($q)==0)$q = mysql_query("SELECT * FROM `areas` WHERE `name` LIKE '%$areaq1%' LIMIT 1");

$arearow = mysql_fetch_array($q);

header('Location: /jobs-in-'.$arearow['url']);die;

}

if((!empty($catsq1))&&(empty($areaq1))){

$q = mysql_query("SELECT * FROM `cats` WHERE `name` = '$catsq1' LIMIT 1");
if(mysql_num_rows($q)==0)$q = mysql_query("SELECT * FROM `cats` WHERE `name` LIKE '%$catsq1%' LIMIT 1");

$catsrow = mysql_fetch_array($q);

header('Location: /'.$catsrow['url'].'-jobs');die;

}

if((!empty($catsq1))&&(!empty($areaq1))){

$q = mysql_query("SELECT * FROM `cats` WHERE `name` = '$catsq1' LIMIT 1");
if(mysql_num_rows($q)==0){$q = mysql_query("SELECT * FROM `cats` WHERE `name` LIKE '%{$catsq1}%' LIMIT 1");}
$catsrow = mysql_fetch_array($q);

$q = mysql_query("SELECT * FROM `areas` WHERE `name` = '$areaq1' LIMIT 1");
if(mysql_num_rows($q)==0){$q = mysql_query("SELECT * FROM `areas` WHERE `name` LIKE '%$areaq1%' LIMIT 1");}
$arearow = mysql_fetch_array($q);

//echo 'Location: /'.$catsrow['url'].'-jobs-in-'.$arearow['url'];die;

header('Location: /'.$catsrow['url'].'-jobs-in-'.$arearow['url']);die;

}





//}

/////////////////////////////////////////////////


if(!empty($type))


//header("Location: /".$url);

?>