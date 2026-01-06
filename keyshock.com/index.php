<?php

if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
header('Content-type: text/html; charset=utf-8');

$indexhomepage = 1;

include('db.php');
include('header.php');

//$tpl = file_get_contents('index12.html');
$tpl = file_get_contents('indexnew.html');
/*
$q = mysql_query("SELECT * FROM `cats_uk` WHERE `itskill` = '0' LIMIT 30 ");
while($row = mysql_fetch_array($q)){

$cats .= '<a href="/'.$row['url'].'-jobs">'.$row['name'].' jobs</a>';

}



$q = mysql_query("SELECT * FROM `areas_uk`");
while($row = mysql_fetch_array($q)){

$areas .= '<a href="/jobs-in-'.$row['url'].'">'.$row['name'].' jobs</a>';

}
*/
$tpl = str_replace('{header}',$header,$tpl);
$tpl = str_replace('{footer}',$footer,$tpl);

$tpl = str_replace('{cats}',$cats,$tpl);
$tpl = str_replace('{areas}',$areas,$tpl);

echo $tpl;

?>