<?php

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

include('db.php');

$q = mysql_query("SELECT * FROM `listings123_uk` ORDER BY `postedtime` DESC LIMIT 6000");
//$q = mysql_query("SELECT * FROM `listings123_uk` WHERE (`done`= '3' AND `views` != '0') ORDER BY `views` DESC");


$i = 1;

while($row  = mysql_fetch_array($q)){

echo $i.' - '.$row['id'].' - '.$row['title'].' - '.$row['recruitername'].' - '.$row['location'].' - '.ago($row['postedtime']).' - '.$row['views'].'Vs<hr>';

$i++;

}


?>
