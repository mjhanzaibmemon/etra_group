<?php

if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
header('Content-type: text/html; charset=utf-8');

include('db.php');


$id = $_GET['id'];

if($id=='1'){$tpl = @file_get_contents("about.html");$d[1] = 'class="selected" ';$h1 = 'About Us';$class = 'about';}
if($id=='2'){$tpl = @file_get_contents("brands.html");$d[2] = 'class="selected" ';$h1 = 'Stores and Brands';$class = 'brands';}
if($id=='3'){$tpl = @file_get_contents("accessibility.html");$d[3] = 'class="selected" ';$h1 = 'Accessiblity';$class = 'access';}
if($id=='4'){$tpl = @file_get_contents("terms-of-use.html");$d[4] = 'class="selected" ';$h1 = 'Terms of Use';$class = 'terms';}
if($id=='5'){$tpl = @file_get_contents("contact-us.html");$d[5] = 'class="selected" ';$h1 = 'Contact Us';$class = 'contact';}
if($id=='6'){$tpl = @file_get_contents("privacy-policy.html");$d[6] = 'class="selected" ';$h1 = 'Privacy Policy';$class = 'privacy';}


$header = '<div id="header" class="h'.$class.'" align="center"><div class="l'.$class.'" align="center"><div id="headerholder" align="center">
<img border="0" id="logo" src="http://keyshock.com/imgs/logo.png"><br>
<ul id="navigation">
<li><a href="/">Home</a></li>
<li><a '.$d[5].'href="/extra/contact-us">Contact Us</a></li>
<li><a '.$d[3].'href="/extra/accessibility">Accessiblity</a></li>
<li><a '.$d[4].'href="/extra/terms-of-use">Terms of Use</a></li>
<li><a '.$d[6].'href="/extra/privacy-policy">Privacy policy</a></li>
</ul></div>


<div class="hwidth" align="center">
<h1 class="large"><img border="0" src="/imgs/ext-'.$class.'.png"></h1>
{extcontent}</div>
</div></div>';

$footer = '<div id="footer" align="center"><div id="footerholder" align="center"><img id="logo" border="0" src="http://keyshock.com/imgs/logofooter.png" width="141" ><div id="footernavholder"><ul class="footernav"><li><a href="/">Home</a></li>
<li><a href="/extra/accessibility">
	Accessibility</a></li><li><a href="/extra/terms-of-use">
	Terms of use</a></li><li><a href="/extra/contact-us">
	Contact us</a></li><li><a href="/extra/privacy-policy">
	Privacy policy</a></li></ul><font class="copyright">&#169; 2017 Keyshock Job Finder All Rights Reserved.</font></div></div></div>';


if($id==5){
$content = '<form name="contactform" method="post">
<table class="contactformtbl" width="609">
<tr>
 <td valign="top" width="404">
  <input  type="text" placeholder="Full name" name="first_name" maxlength="50" size="54">
 </td>
</tr>
<tr>
 <td valign="top" width="404">
  <input  type="text" placeholder="Contact Number" name="contact_number" maxlength="50" size="54">
 </td>
</tr>
<tr>
 <td valign="top" width="404">
  <input  type="text" placeholder="Email address" name="email" maxlength="80" size="54">
 </td>
</tr>
<tr>
 <td valign="top" width="404">
  <textarea  placeholder="Message" name="comments" maxlength="1000" cols="46" rows="8"></textarea>
 </td>
</tr>
<tr>
 <td style="text-align:right;">
  <input type="submit" value="Send">
 </td>
</tr>
</table>
</form>';

if(!empty($_POST['email'])) {
     
// EDIT THE 2 LINES BELOW AS REQUIRED
$email_to = "r.faruqui@live.co.uk";
$email_subject = "Keyshock Support Form";
     
 /*   function died($error) {
echo '<p><b><font face="Arial">We are very sorry, but there were error(s) found with the form you submitted.</font></b></p>
<p><b><font face="Arial">These errors appear below.<br>
<font color="#FF0000">'.$error.'</font><br>
Please go back and fix these errors.</font></b></p>';
        die();
    }*/
     
    // validation expected data exists
    if(!isset($_POST['first_name']) ||
        !isset($_POST['contact_number']) ||
        !isset($_POST['email']) ||
        !isset($_POST['comments'])) {
       // died('We are sorry, but there appears to be a problem with the form you submitted.');       
    }
     
    $first_name = $_POST['first_name']; // required
    $contact_number = $_POST['contact_number']; // required
    $email_from = $_POST['email']; // required
    $comments = $_POST['comments']; // required
     
    $error_message = "";
    /*$email_exp = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
  if(!preg_match($email_exp,$email_from)) {
    $error_message .= 'The Email Address you entered does not appear to be valid.<br />';
  }
    $string_exp = "/^[A-Za-z .'-]+$/";
  if(!preg_match($string_exp,$first_name)) {
    $error_message .= 'The First Name you entered does not appear to be valid.<br />';
  }
  if(!preg_match($string_exp,$last_name)) {
    $error_message .= 'The Last Name you entered does not appear to be valid.<br />';
  }
  if(strlen($comments) < 2) {
    $error_message .= 'The Comments you entered do not appear to be valid.<br />';
  }
  if(strlen($error_message) > 0) {
    died($error_message);
  }*/
    $email_message = "Form details below.\n\n";
     
    function clean_string($string) {
      $bad = array("content-type","bcc:","to:","cc:","href");
      return str_replace($bad,"",$string);
    }
     
    $email_message .= "First Name: ".clean_string($first_name)."\n";
    $email_message .= "Contact Number: ".clean_string($contact_number)."\n";
    $email_message .= "Email: ".clean_string($email_from)."\n";
    $email_message .= "Message: ".clean_string($comments)."\n";
     
     
// create email headers
$headers = 'From: '.$email_from."\r\n".
'Reply-To: '.$email_from."\r\n" .
'X-Mailer: PHP/' . phpversion();
mail($email_to, $email_subject, $email_message, $headers);  
 
 
$message = '<b>Thank you for contacting us. We will be in touch with you within 48 working hours.</b>';
$tpl = str_replace('{content}', $message, $tpl);
}
else 
{
$tpl = str_replace('{content}', $content, $tpl);
}

}


$header = str_replace('{extcontent}', $extcontent, $header);
$tpl = str_replace('{header}', $header, $tpl);
$tpl = str_replace('{footer}', $footer, $tpl);
$tpl = str_replace('{h1}', $h1, $tpl);
$tpl = str_replace('{class}', $class, $tpl);


echo $tpl;


?>