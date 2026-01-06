<?php

if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
header('Content-type: text/html; charset=utf-8');

include('db.php');
include('header.php');
/*
$adid = addslashes($_GET['adid']);

$message= '  <form method="POST">  <h3>Don\'t miss out on the latest jobs - just like this!</h3><div class="subcontainer"><input type="hidden" name="adid" value="'.$adid.'"><input class="form-control" name="email" placeholder="Enter your email"><button id="store_email" class="btn btn-blue">Yes, please!</button><button onclick="window.parent.close_emailpop();" class="btn btn-default" style="margin-left: 10px;background: rgba(146, 146, 146, 0.49);border: none;">No, I dont want the latest jobs</button></div></form>';

$_POST['email'] = addslashes($_POST['email']);
$_POST['adid'] = addslashes($_POST['adid']);


if(!empty($_POST['email'])&&(!empty($_POST['adid'] ))){

$ip = $_SERVER['REMOTE_ADDR'];

$url = "http://ipinfo.io/{$ip}?token=df6e32b636675a";

$curl = curl_init(); 
curl_setopt($curl, CURLOPT_URL, $url); 
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); 
curl_setopt($curl, CURLOPT_TIMEOUT, 10);

$get = curl_exec($curl);

curl_close($curl);

$get =  json_decode($get);

$country = trim($get->country);

$now = time();

if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {

$insertq = mysql_query("INSERT INTO `subscription`(`advert_id`, `emailaddress`, `country`, `added`) VALUES ('{$_POST['adid']}','{$_POST['email']}','$country','$now')");

if($insertq)$message= '    <h3>Thank you! We\'ve successfully subscribed you to our new job notifications!</h3>';

}

else{$message= '  <form method="POST">  <h3>Please enter your correct email address*</h3><div class="subcontainer"><input type="hidden" name="adid" value="'.$adid.'"><input class="form-control" name="email" placeholder="Enter your email"><button id="store_email" class="btn btn-blue">Sign Up Now!</button></div></form>';}


}
*/

$message= '<h3>Get an Award Winning CV NOW!</h3><button onclick="window.location=\'http://keyshock.com/cv-writing\';" id="store_email" class="btn btn-blue">Change My CV NOW! 
&raquo;</button>';

echo '<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Keyshock Subscription</title>
</head>
<link rel="stylesheet" type="text/css" href="style.css">
<style>

h3 {
    margin: 15px 0 15px;
    font-size: 21px;
    text-align: center;
}


.form-control{display: block;
    box-sizing: border-box;
    width: 100%;
    height: 34px;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.42857143;
    color: #555;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ccc;
    border-radius: 4px;
    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);
    box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);
    -webkit-transition: border-color ease-in-out .15s, -webkit-box-shadow ease-in-out .15s;
    -o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
    transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;}


.form-control{
    max-width: 390px;
    margin-right: 10px;
    float: left;
}




.subcontainer{    max-width: 800px;
    width: 100%;}

//


.btn {
    font-weight: bold;
    text-shadow: none;
    float:left;
}


.btn {
    display: inline-block;
    margin-bottom: 0;
    font-weight: normal;
    text-align: center;
    vertical-align: middle;
    -ms-touch-action: manipulation;
    touch-action: manipulation;
    cursor: pointer;
    background-image: none;
    border: 1px solid transparent;
    white-space: nowrap;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.42857143;
    border-radius: 4px;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

.btn-blue {
    background-color: #5cb85c;
    border-color: #4cae4c;
    color:white;
}

body, input, textarea, button {
    font: 13px \'Open Sans\', Arial, sans-serif;
}

input, button, select, textarea {
    font-family: inherit;
    font-size: inherit;
    line-height: inherit;
}

button, html input[type="button"], input[type="reset"], input[type="submit"] {
    -webkit-appearance: button;
    cursor: pointer;
}


@media (max-width: 750px) {

h3{margin-bottom: 0;
    text-align: left;
    padding-left: 10px;}

.subcontainer{max-width: 100%;
    width: 100%;
    box-sizing: border-box;
    padding: 10px;}

.form-control{max-width: 100%;margin:0;width:100%;}

.btn{    float: left;
    margin-top: 10px;}

}



@media (max-width: 450px) {

h3{margin-bottom: 0;
    text-align: left;
    padding-left: 10px;}

.subcontainer{max-width: 450px;
    width: 100%;
    box-sizing: border-box;
    padding: 10px;}

.form-control{max-width: 100%;margin:0;width:100%;}

.btn{    float: left;
    margin-top: 10px;}




}




</style>
<body>

<div align="center">

'.$message.'


</div>


</body>

</html>';

?>