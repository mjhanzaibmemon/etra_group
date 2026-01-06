<?php

if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
header('Content-type: text/html;');


die;

$maxopenreview = 3 + 1;
$minmarketing = 2;

$funnelstate = $_GET['funnelstate'];
$personinfo['opened'] = $_GET['opened'];


echo 'The Get Funnel State: '.$_GET['funnelstate'].'<br>';


if(($funnelstate >= $maxopenreview)&&($personinfo['opened']=='0'))$funnelstate = 0;//ITS NOT BEEN OPENED, RESTART TO FUNNEL STATE 0 IT IS BELOW 3, ONE BELOW 3
if(($funnelstate <= $minmarketing )&&($personinfo['opened']!=='0'))$funnelstate = 3;//ITS BEEN OPENED GO TO FUNNEL STATE 3, WHERE MARKETING CAN BEGIN ON FUNNEL STATE 3


echo 'The Result: '.$funnelstate.'<br>';



die;


include('../db.php');
include('adminheader.php');


function GetBetween($content,$start,$end){
$r = explode($start, $content);
    if (isset($r[1])){
        $r = explode($end, $r[1]);
        return $r[0];
    }
    return '';
}


$viewsource = $_POST['viewsource'];

$viewsource = preg_replace( "/\r|\n/", "", $viewsource);

$name = trim(strip_tags(stripslashes(GetBetween($viewsource,'<h2>Summary</h2>','\'s experience'))));
$desc = stripslashes(strip_tags(GetBetween($viewsource,'<h2>Summary</h2>','</p>')));

$firstskill = GetBetween($desc,'to be concentrated in',',');
$secondskill = GetBetween($desc,'exposure to ','.');

//COUNT 
if(substr_count($firstskill, '/') > 2){$firstskill = explode('/', $firstskill);$firstskill = $firstskill[0].'/'.$firstskill[1].'/'.$firstskill[2];}
if(substr_count($secondskill, '/') > 2){$secondskill = explode('/', $secondskill);$secondskill = $secondskill[0].'/'.$secondskill[1].'/'.$secondskill[2];}

//YERAS
preg_match('/[0-9]{0,4} years/Usi', $desc,$years);
$years = trim(str_replace(' years','',$years[0]));

//MANGEMENT SCORE
if (strpos($desc, 'no management experience') !== false) {$management = '0';}

$skillcloud = GetBetween($viewsource,'var wordcloud_list = ','];');

preg_match_all('#"(.*)"#Usi', $skillcloud, $allrows);
foreach($allrows[1] as $skill)$allskills .=stripslashes($skill).', ';

$allskills = explode(', ', $allskills);
$firstrow = $allskills[0].','.$allskills[2].','.$allskills[3];
$firstrow = rtrim($firstrow,',');$firstrow = rtrim($firstrow,',');$firstrow = rtrim($firstrow,',');

$secondrow = $allskills[4].','.$allskills[5].','.$allskills[6];
$secondrow = rtrim($secondrow,',');$secondrow = rtrim($secondrow,',');$secondrow = rtrim($secondrow,',');


$thirdrow = $allskills[7].','.$allskills[8].','.$allskills[9].','.$allskills[10];
$thirdrow = rtrim($thirdrow,',');$thirdrow = rtrim($thirdrow,',');$thirdrow = rtrim($thirdrow,',');


echo 'Name: '.$name.' <br>';

echo 'Desc: '.$desc.' <br>';

echo 'First skill: '.$firstskill.' <br>';

echo 'Second skill: '.$secondskill.' <br>';

echo 'Years: '.$years.' <br>';

echo 'Management: '.$management.' <br>';

echo 'First row skills: '.$firstrow.' <br>';

echo 'Second row skills: '.$secondrow.' <br>';

echo 'Third row skills: '.$thirdrow.' <br>';

echo '<div><pre>'.htmlspecialchars($viewsource).'</pre></div>';

?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Keyshock Admin</title>

<style type="text/css">textarea{padding:10px;width:100%;height:100%;box-sizing:border-box;}</style>
</head>
<body style="max-width:100%;">
  
<form method="POST"><textarea name="viewsource"></textarea>
<input type="submit" name="submit" value="submit"></form>

</body>
</html>