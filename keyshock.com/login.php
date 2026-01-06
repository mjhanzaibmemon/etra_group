<?php

if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
header('Content-type: text/html; charset=utf-8');

////////////////////////////////////////// ENCRYPTION FUNCTION



$tpl = @file_get_contents("login.html");


if($filled=='1'){

$error1_1= '<span class="message"><font color="red">*</font>We couldn\'t recognise this email. Double check it\'s the correct email.</span><br>';


$error2_1 = '<span class="message"><font color="red">*</font>Incorrect email or password! Please try again.</span><br>';


}

if(!empty($_GET['postjob'])){

$topmsg = '<div style="background-color:#fec34d;color:white;width:100%;height:40px;padding: 10px 0;margin:0;color:white;">To post a job advert you must first login to your Recruiter\'s Account.<br>Please <a href="http://keyshock.com/extra/contact-us">contact us</a> if you are having any issues</div>';

}





$tpl = str_replace('{email}', $email, $tpl);
$tpl = str_replace('{password}', '', $tpl);

$tpl = str_replace('{topmsg}', $topmsg, $tpl);


$tpl = str_replace('{errors}', $error1_1.$error2_1.$error2_2, $tpl);



echo $tpl;


?>