<?php

if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
header('Content-type: text/html; charset=utf-8');

echo '<meta charset="UTF-8"><style>body{font-family:arial;font-size:10pt;}</style>';

include ('../db.php');

$now = time();

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


$q = mysql_query("SELECT * FROM `listings123_uk` WHERE `donecheck` = '0' AND `expiredtime` < '$now' AND `description` LIKE '%@%' ORDER BY `id` ASC LIMIT 1");

if(mysql_num_rows($q)==0){$q = mysql_query("SELECT * FROM `listings123_uk` WHERE `donecheck` = '0' AND `ended` < '$now' AND `description` REGEXP '[0-9]{4,}' ORDER BY `id` ASC LIMIT 1");}

if(mysql_num_rows($q)==0){echo '<b>NO jobs to approve. All done for now.</b>';die;}

$info = mysql_fetch_array($q);


//
$pattern = '/[0-9]{5,}/Usi';
preg_match($pattern, str_replace(' ','', strip_tags($info['description'])), $matches);
//

if(strpos($info['description'], '@') !== false){$emailfound = '- Found Email';}

if((!empty($matches[0]))||(strpos($info['description'], '@') !== false))
{$statusq = mysql_query("DELETE FROM `listings123_uk` WHERE `id` = '{$info['id']}' LIMIT 1");
	if($statusq)$status = '<font color="red"><b>DELETED '.$info['id'].'</b></font>';}


else

{$statusq = mysql_query("UPDATE `listings123_uk` SET `donecheck` = '1' WHERE `id` = '{$info['id']}' LIMIT 1");
	if($statusq)$status = '<font color="green"><b>KEEP '.$info['id'].'</b></font>';}

echo ' <meta http-equiv="refresh" content="0">';

echo $info['title'].' - '.$status.'<hr>';
echo $matches[0].$emailfound.'<hr>';
echo 'Expired: '.ago($info['ended']).' - '.$info['ended'].'<hr>';
echo $info['recruitername'].'<hr>';
echo $info['location'].'<hr>';
echo $info['description'].'<hr>';

?>