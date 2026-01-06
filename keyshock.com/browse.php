<?php

if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
header('Content-type: text/html; charset=utf-8');

//echo $_SERVER['REQUEST_URI'];

include('db.php');
include('header.php');

$typeurl = $_GET['type'];
$areaurl = $_GET['area'];

$areaq = $_GET['areaq'];
$catsq = $_GET['catsq'];
$areaq1 = $_GET['areaq1'];
$catsq1 = $_GET['catsq1'];

$tpl = file_get_contents('browse.html');


function ago($time)

{$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
   $lengths = array("60","60","24","7","4.35","12","10");
   $now = time();
       $difference     = $now - $time;
   for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
       $difference /= $lengths[$j];
   }
   $difference = round($difference);
   if($difference != 1) {
       $periods[$j].= "s";
   }   return "$difference $periods[$j] ago";}

//TRACE URLS AND FIND IT ON MYSQL

if(!empty($typeurl)){$q = mysql_query("SELECT * FROM `cats_uk` WHERE `url` = '$typeurl' LIMIT 1");$typefetch = mysql_fetch_array($q);}

if(!empty($areaurl)){$q = mysql_query("SELECT * FROM `areas_uk` WHERE `url` = '$areaurl' LIMIT 1");$areafetch = mysql_fetch_array($q);}

//////////////////////////////////////////

if($_GET['searchall']=='1'){


$title = "Jobs in UK";
$searchdesc = "Job oppurtunities found in UK.";

}

if((!empty($areaurl))&&(empty($typeurl))){

$extq = " `location` LIKE '%{$areafetch['name']}%' ";
$title = "Jobs in {$areafetch['name']}";
$searchdesc = "Job oppurtunities found in {$areafetch['name']}.";
/*$extrightbar2 = "Choose another area";
/*$allcats = '/jobs-in-'.$areafetch['url'];
$alllocations = '/search-jobs';*/

}

if((!empty($typeurl))&&(empty($areaurl))){

$extq = " `description` LIKE '%{$typefetch['name']}%' ";
$title = "{$typefetch['name']} Jobs";
$searchdesc = "{$typefetch['name']} Job oppurtunities have been found.";
/*$extrightbar1 = "Other Job Types";
$allcats = '/search-jobs';
$alllocations = '/'.$typefetch['url'].'-jobs';*/

}

if((!empty($typeurl))&&(!empty($areaurl))){

$extq = " MATCH(`cats`) AGAINST ('{$typefetch['id']}' IN BOOLEAN MODE) AND MATCH(`locationhref`) AGAINST ('{$areafetch['id']}' IN BOOLEAN MODE) ";
$title = "{$typefetch['name']} Jobs in {$areafetch['name']}";
$searchdesc = "{$typefetch['name']} Job Oppurtunities in {$areafetch['name']}.";

}


if(!empty($extq))$extq = "AND $extq";

//NAVIGATIONS

$q = mysql_query("SELECT * FROM `cats_uk`");
while($row = mysql_fetch_array($q)){

$cats .= '<a href="/'.$row['url'].'-jobs">'.$row['name'].' jobs</a><br>';

}

$q = mysql_query("SELECT * FROM `areas_uk`");
while($row = mysql_fetch_array($q)){

$name = $row['name'];

$areas .= '<a href="/jobs-in-'.$row['url'].'">'.$name.' jobs</a><br>';

}

//LISTINGS

$q = mysql_query("SELECT * FROM `listings123_uk` WHERE `done` = '3' {$extq} LIMIT 30");

$qcount = mysql_num_rows($q);

while($row = mysql_fetch_array($q)){

$row['description'] = str_replace('£','&pound;',$row['description']);

$row['location'] = strip_tags($row['location']);

$row['salary'] = strip_tags($row['salary']);


if(!empty($row['salary']))$salarybar = '<div class="bar"><div class="heading">Salary:</div><div class="headinginfo">'.trim($row['salary']).'</div></div>';

 $row['description'] = iconv(mb_detect_encoding($row['description'], mb_detect_order(), true), "UTF-8", $row['description']);

$listings .= '<div class="listing">
	<a href="/view/'.$row['url'].'-'.$row['id'].'" class="title">'.strip_tags($row['title']).'</a>
	<div class="desc">'.substr(strip_tags($row['description']), 0, 400).'...</div>
	
	'.$salarybar.'
	
	<div class="bar"><div class="heading">Location:</div><div class="headinginfo">'.trim($row['location']).'</div></div>
	
	</div>';
unset($salarybar);

}

if(empty($extrightbar1))$extrightbar1 = 'Refine by category';
if(empty($extrightbar2))$extrightbar2 = 'Refine by location';

$tpl = str_replace('{header}',$header,$tpl);
$tpl = str_replace('{footer}',$footer,$tpl);

$tpl = str_replace('{title}',$title,$tpl);
$tpl = str_replace('{searchdesc}',$qcount.' '.$searchdesc,$tpl);
$tpl = str_replace('{extrightbar1}',$extrightbar1,$tpl);
$tpl = str_replace('{extrightbar2}',$extrightbar2,$tpl);
$tpl = str_replace('{allcats}',$allcats,$tpl);
$tpl = str_replace('{alllocations}',$alllocations,$tpl);
$tpl = str_replace('{catshref}',$cats,$tpl);
$tpl = str_replace('{locationshref}',$areas,$tpl);
$tpl = str_replace('{listings}',$listings,$tpl);


echo $tpl;

?>