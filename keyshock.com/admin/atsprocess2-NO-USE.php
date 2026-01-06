<?php

if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
header('Content-type: text/html;');

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

$viewsource = stripslashes(preg_replace( "/\r|\n/", "", $viewsource));

$mostrecentjob = ucwords(GetBetween($viewsource,'</th></tr><tr><td>','</td><td>'));

$mostrecentemployer = ucwords(GetBetween($viewsource,'</td><td>','</td>'));

preg_match_all('#<p class="indent">(.*)</p>#Usi', $viewsource, $primarydetails);

$location = strip_tags(str_replace('<br />',', ',$primarydetails[1][0]));$location = rtrim($location,', ');
$contactnumber = strip_tags($primarydetails[1][1]);
$email = strip_tags($primarydetails[1][2]);

//IF EMPTY THEN IN VALUATION PAGE - THEN SHOW AS NOT FOUND

echo 'Recent Employer: '.$mostrecentemployer.' <br>';

echo 'Recent Job: '.$mostrecentjob.' <br>';

echo 'Location: '.$location.' <br>';

echo 'ATS Contact Number: '.$contactnumber.' <br>';

echo 'ATS Email: '.$email.' <br>';



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