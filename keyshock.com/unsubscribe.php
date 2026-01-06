<?php

if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
header('Content-type: text/html;');

include('db.php');

$id = addslashes($_GET['id']);
$adid = addslashes($_GET['adid']);
$email = addslashes($_GET['email']);

$unsub = 'id='.urlencode($id).'&adid='.urlencode($adid).'&email='.urlencode($email);

if((empty($id))||(empty($email)))exit('ERROR 50231');

if($_GET['go']=='true'){

  $q = mysql_query("SELECT * FROM `applicants` WHERE `id` = '$id' AND `emailaddress` = '$email' LIMIT 1");
if(mysql_num_rows($q)==0){$q = mysql_query("SELECT * FROM `subscription` WHERE `id` = '$id' AND `advert_id` = '$adid' AND `emailaddress` = '$email' LIMIT 1");}

if(mysql_num_rows($q)==0){die;}



mysql_query("UPDATE `subscription` SET `unsubscribe` = '1' WHERE `emailaddress` = '{$email}' AND `id` = '$id'");
mysql_query("UPDATE `applicants` SET `unsubscribe` = '1' WHERE `emailaddress` = '{$email}' AND `id` = '$id'");
mysql_query("UPDATE `cvlist` SET `unsubscribe` = '1' WHERE `emailaddress` = '{$email}' AND `id` = '$id'");

die('<style>body{font-family:arial;}</style><h3 style="text-align:center;">You\'ve been unsubscribed. Please join us again. Thank you.</h3>');

}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="viewport" content="width=device-width" />

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Keyshock New Job Offers</title>
  
<link rel="stylesheet" type="text/css" href="email.css" />

<style type="text/css">body{padding-top:0;}</style>

</head>
 
<body bgcolor="#FFFFFF">

<!-- HEADER -->



<!-- BODY -->
<table class="body-wrap">
  <tr>
    <td></td>
    <td class="container" bgcolor="#FFFFFF">

      <div class="content">
      <table>
        <tr>
          <td>
            

              <table class="topcolor" border="0" bgcolor="#203791" style="background-color:#203791;"><tr><td style="text-align: center;    background-color: #3a6688;padding: 20px 10px;" align="center">




              <img src="http://keyshock.com/imgs/logo2018.png" />
              
              </td></tr>


              <tr>
                

                <td style="    padding: 0px 15px 15px 15px;">
                  

                <h3 style="color:white;text-align:center;">Unsubscribe from Keyshock Job Notifications</h3>


                </td>


              </tr>


              <tr><td style="    text-align: center;"><a style="    margin-left: 10px;
    background: white;color:black;text-decoration:none;
    border: none;

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
    user-select: none;margin-bottom:20px;" href="unsubscribe.php?<?=$unsub ?>&go=true">I don't want anymore notifications/emails - Unsubscribe me</a></td></tr>


              </table>
                








</body>
</html>