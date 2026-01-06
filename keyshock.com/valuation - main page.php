<?php

include('db.php');



$form = '<div style="padding:10px;">

<span style="font-size: 18px;
    font-weight: bold;
    text-align: center;
    display: block;
    margin-bottom: 10px;
    margin-top: 10px;">Professional CV Evaluation by Keyshock</span>

<div style="font-size:15px;"><b>Our expert\'s Confidentiality Guarantee:</b>
We take privacy very seriously and guarantee total non-disclosure of your information. Enquiries made and CVs/documents sent to us are kept in 100% confidentiality. We will never share client information to a third party without the consent of the client or a clear legal reason. You will never receive spam or marketing emails from us. 
</div>
<form action="" method="POST" enctype="multipart/form-data" name="form1" id="form1">
<div class="username">Your full name:*<br>
<input type="text" name="first_name" placeholder="Your full name"></div>

<div class="username">Your profession:*<br>
<input type="text" name="profession" placeholder="Your profession"></div>

<div class="username">Your email address:*<br>
<input type="text" name="email" placeholder="Your email address"></div>

<div class="username">Your contact number:*<br>
<input type="text" name="contact_number" placeholder="Your contact number"></div>

<div class="username">Please upload the CV that you want a professional evaluation on:*<br>
<input name="theFile" type="file" /></div>

<input class="apply" name="submit" type="submit" value="Request Professional Evaluation Now &raquo;">

</form></div>';



      function rand_string( $length ) {
                $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";  

                $size = strlen( $chars );
                    for( $i = 0; $i < $length; $i++ ) {
                        $str .= $chars[ rand( 0, $size - 1 ) ];
                    }

                    return $str;
                }




if(!empty($_POST['submit'])) {
 

/////////////////////////////////

/////////////////////////////////




// EDIT THE 2 LINES BELOW AS REQUIRED
$email_to = "r.faruqui@live.co.uk";
$email_subject = "Keyshock Evaluation Form";
     
    // validation expected data exists
    if(!isset($_POST['first_name']) ||
        !isset($_POST['contact_number']) ||
        !isset($_POST['email']) ||
        !isset($_POST['comments'])) {
       // died('We are sorry, but there appears to be a problem with the form you submitted.');       
    }
     
    $first_name = $_POST['first_name']; // required
    $contact_number = $_POST['contact_number']; // required
    $profession = $_POST['profession']; // required
    $email_from = $_POST['email']; // required
    $email_fromconfirmed = $_POST['emailconfirm']; // required
     
    $error_message = "";

    $email_message = "Form details below.\n\n";

    function clean_string($string) {
      $bad = array("content-type","bcc:","to:","cc:","href");
      return str_replace($bad,"",$string);
    }
     
    $email_message .= "First Name: ".clean_string($first_name)."\n\n";
    $email_message .= "Profession: ".clean_string($profession)."\n\n";
    $email_message .= "Contact Number: ".clean_string($contact_number)."\n\n";
    $email_message .= "Email address: ".clean_string($email_from)."\n\n";
    $email_message .= "Confirm Email address: ".clean_string($email_fromconfirmed)."\n\n";
    $email_message .= "CV filename: ".clean_string($fileName)."\n\n";
     

    $first_name = addslashes($first_name);
    $profession = addslashes($profession);
    $contact_number = addslashes($contact_number);
    $email_from = addslashes($email_from);
    $fileName = addslashes($fileName);


// create email headers
$headers = 'From: '.'evaluations@keyshock.com'."\r\n".
'Reply-To: '.$email_from."\r\n" .
'X-Mailer: PHP/' . phpversion();
//mail($email_to, $email_subject, $email_message, $headers);  
//mail("support@Keyshock.co.uk", $email_subject, $email_message, $headers);  

$form = '<div class="emailsuccess">We\'ve sent your CV to the Keyshock Evaluation Team. A specialist of our evaluation team will get back to you within 2 working day.</div>';

$now = time();

$q = mysql_query("INSERT INTO `evaluation` SET 
	`name` = '$first_name',
	`email` = '$email_from',
	`profession` = '$profession',
	`contactnumber` = '$contact_number',
	`filename` = '$fileName',
	`added` = '$now'");

///////////////////////////////////////////////////

if(!empty($contact_number)){

        $names = explode(' ',trim($first_name));
        //echo $names[0];


        $number = $contact_number;
        
        if (0 === strpos($number, '+44')) {
          // echo 'STARTS with 0';
           $number = ltrim($number,'+44');
           $finalnumber = '44'.$number;
        }
        if (0 === strpos($number, '0')) {
          // echo 'STARTS with 0';
           $number = ltrim($number,'0');
           $finalnumber = '44'.$number;
        }
        else{
          //  echo 'STARTS WITHOUT 0';
            $finalnumber = '44'.$number;
        }

        function mhttp_build_query($formData, $prefix = "") {
          foreach ($formData as $key => $value) {
             if (is_numeric($key))
               $key = $prefix . $key;
             $qData[] = "$key=" . urlencode($value);
          }
         return implode("&", $qData);
        }



         $formData = array(
            'api_key' => 'c8c72491',
            'api_secret' => 'eba1890a99f74f21',
            'to' => $finalnumber,
            'from' => '441632960061',
            'text' => 'Hello '.ucwords($names[0]).', this is a notification from Keyshock CV Experts. Thank you for sending your CV (CV-0127381) for a professional evaluation. Our team will get back to you within 2 working days. Thank you.',);

        $url = 'https://rest.nexmo.com/sms/json?'.mhttp_build_query($formData);

		$ch = curl_init($url);
		 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);

}
///////////////////////////////////////////////////

}








?><!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Contact our Friendly Support Team - Keyshock</title>
<link href="https://fonts.googleapis.com/css?family=PT+Sans" rel="stylesheet">
<link href="style.css" rel="stylesheet">
<style>

    .emailsuccess{padding: 10px;
    border: 1px solid green;
    margin-top: 20px;
    background-color: #d8ffd8;}

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
    width: 335px;
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
    font-family: 'Lato' ,Arial;}

.message{    background: #5fcf80;
    color: #fff;
    text-shadow: 0 1px 0 rgba(0,0,0,0.11);
    -webkit-border-radius: 3px;
    font-size: 17px;
    font-weight: bold;
    padding: 14px 15px;}







</style>
</head>

<body>




<?=$form ?>






</body>

</html>