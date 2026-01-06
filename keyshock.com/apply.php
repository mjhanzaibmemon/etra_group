<?php

if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
header('Content-type: text/html; charset=utf-8');

include('db.php');
include('header.php');

$id = addslashes($_GET['id']);



mysql_query("UPDATE `listings123_uk` SET `applyview` = `applyview` + 1,`views` = `views` + 1 WHERE `md5string` = '$id' LIMIT 1");

$q = mysql_query("SELECT * FROM `listings123_uk` WHERE `md5string` = '$id' LIMIT 1");

if(mysql_num_rows($q)=='0')exit('Not available');

$row = mysql_fetch_array($q);


if(!empty($_POST['submit'])){


$newtpl = @file_get_contents('newsletter.html');

$newtpl = str_replace('{jobtitle}',$row['title'],$newtpl);
$newtpl = str_replace('{fullname}',$_POST['fullname'],$newtpl);
$newtpl = str_replace('{email}',$_POST['email'],$newtpl);
$newtpl = str_replace('{contactnumber}',$_POST['contactnumber'],$newtpl);
$newtpl = str_replace('{coverletter}',$_POST['coverletter'],$newtpl);

//SEND REGISTRATION EMAIL
$to = $userinfo['applyemailaddress'];

$subject = 'Keyshock Job Applicant!';
$sentby = 'info@keyshock.com';

$headers = "Keyshock Job Applicant <". strip_tags($sentby) . "> \r\n";
$headers .= "Reply-To: ". strip_tags($sentby) . "\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

//mail($to, $subject, $tpl, $headers);

$form = '<div style="padding:10px;">

<div class="message">Thank you for applying! Your cover letter has been sent directly to the recruiter. We wish you the best in this job oppurtunity!</div>

</div>';

mysql_query("UPDATE `listings123_uk` SET `applybtn` = `applybtn` + 1 WHERE `md5string` = '$id' LIMIT 1");

$now = time();

$_POST['fullname'] = addslashes($_POST['fullname']);
$_POST['contactnumber'] = addslashes($_POST['contactnumber']);
$_POST['email'] = addslashes($_POST['email']);

mysql_query("INSERT INTO `applicants` 
    SET `advert_id` = '{$row['id']}', 
    `advert_url` = '{$row['url']}', 
    `name` = '{$_POST['fullname']}', 
    `contactnumber` = '{$_POST['contactnumber']}', 
    `emailaddress` = '{$_POST['email']}',
    `added` = '$now' ");

if(!empty($_POST['email'])){

function split_name($name) {
    $name = trim($name);
    $last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
    $first_name = trim( preg_replace('#'.$last_name.'#', '', $name ) );
    return array($first_name, $last_name);
}

$firstname = split_name($_POST['fullname']);

$message = '<img src="https://keyshock.com/imgs/imgwhite.png" width="234" height="61" style="width:234px;height:61px;"><br>Hello '.$firstname[0].',
<br><br>
Thank you very much for submitting your CV to apply for the job position. The Keyshock team wish you all the best and hope you get the job! As CV experts we recommend you to ensure that your CV is keyword optimized so that you are not filtered out by an Applicant Tracking System (ATS) used by most employers.
<br><br>
If you want our CV Experts to analyze your CV for just $1, we include the following:
<br><br>
- The raw results of running your CV through an Applicant Tracking System<br>
- Your CV rank in 3-tiers for the keywords it is most optimized for<br>
- Suggest for grammatical + structuring  improvements<br>
- Completed in 48-hours time and emailed to you<br>
- Safe & Secure Checkout<br>
- 100% Satisfaction Guarantee
<br><br>
<a href="https://keyshock.com/evaluation/improvements-cv-upload.php">Get Professional Evaluation Now »</a>
<br><br>
Kind regards,
<br><br>
Robert Parker<br>
Senior CV Expert<br>
Keyshock CV Experts<br>
<br>
<br>
E: robert-parker@keyshock.com<br>
A: 160 Kemp House, City Road, London EC1V 2NX<br>
<img src="https://keyshock.com/imgs/imgwhite.png" width="234" height="61" style="width:234px;height:61px;">';

$headers = "From: $sentby\r\n";
mail($_POST['email'], 'New Evaluation Request', $message,$headers);

}


}

else{

$form = '<div style="padding:10px;"><form method="POST">
<div class="username">Your full name:*<br>
<input type="text" name="fullname" placeholder="Your full name..."></div>

<div class="username">Your email address:*<br>
<input type="text" name="email" placeholder="Your email address"></div>

<div class="username">Your contact number:*<br>
<input type="text" name="contactnumber" placeholder="Your contact number"></div>

<div class="username">Your cover letter:*<br>
<textarea name="coverletter" placeholder="Your cover letter" style="height: 207px;">Hello, I would like to apply for the job position of '.ucwords($row['title']).'. Please refer to the rest of this letter. Looking forward to hearing from you.</textarea></div>
<input class="apply" name="submit" type="submit" value="Apply for this job &raquo;">

</form></div>';

}

echo '<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<title>Keyshock.com Application Form</title>
</head>
<link rel="stylesheet" type="text/css" href="style.css">
<style>

input{font-size:16px;border:1px solid #ccc;padding:7px;}

.apply{font-size: 13px;
    height: 46px;text-decoration:none;
    position: relative;
    display: inline-block;
    padding: 10px 20px;
    transition: all 0.6s ease 0s;
    -webkit-transition: all 0.6s ease 0s;
    -moz-transition: all 0.6s ease 0s;
    -o-transition: all 0.6s ease 0s;
    -ms-transition: all 0.6s ease 0s;
    -webkit-backface-visibility: hidden;
    -webkit-border-radius: 3px !important;
    -moz-border-radius: 3px !important;
    -ms-border-radius: 3px !important;
    -o-border-radius: 3px !important;
    border-radius: 3px !important;
    font-weight: 700;
    color: #fff;
    background-color: #5cb85c;
    border-color: #4cae4c;
    outline: 0;
    border: 0;
    width: 175px;
    font-size: 17px;
    margin-top: 2px;margin-top:10px;}
    
    .apply:hover{opacity:0.8;cursor:pointer;}
    
.username,.password,.fullname{width:100%;padding: 10px 0;border-bottom:1px dashed #ccc;height: initial;}

input,textarea,.inputfn{padding: 10px;
width: 100%;
height: 43px;
font-size: 16px;
border:1px solid #ADA9A9;
-webkit-box-sizing: border-box;
-moz-box-sizing: border-box;
box-sizing: border-box;
    font-family: \'Lato\' ,Arial;}

.message{    background: #5fcf80;
    color: #fff;
    text-shadow: 0 1px 0 rgba(0,0,0,0.11);
    -webkit-border-radius: 3px;
    font-size: 17px;
    font-weight: bold;
    padding: 14px 15px;}



</style>
<body>';

echo $form;

echo '<div></div>

</body>

</html>';

?>