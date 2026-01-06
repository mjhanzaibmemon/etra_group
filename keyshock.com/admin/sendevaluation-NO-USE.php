<?php

if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
header('Content-type: text/html;');

include('../db.php');
include('adminheader.php');

//THIS WAS A MANUAL SYSTEM, I HAD TO COPY AND PASTE THE CODE TO GMAIL AND SEND IT OFF WHEN IT COULD BEEN DONE AUTOMATICALLY - OUT OF USE AS THIS IS NOW AUTOMATICALLY DONE

die;

$id = addslashes($_GET['id']);

if(empty($id)){

$q = mysql_query("SELECT * FROM `evaluation` WHERE `request` = '2' LIMIT 1");
$info = mysql_fetch_array($q);
if(mysql_num_rows($q)==0){$displaynone = 'display:none;';}else{
header("Location: sendevaluation.php?id=".$info['id']);die;}

}

if(($_GET['done']=='true')&&(!empty($id))){

mysql_query("UPDATE `evaluation` SET `request` = '3' WHERE `id` = '$id' LIMIT 1");
header("Location: sendevaluation.php?");die;

}
 

$q = mysql_query("SELECT * FROM `evaluation` WHERE `request` = '2' AND `id` = '$id' LIMIT 1");
if(mysql_num_rows($q)==0){$displaynone = 'display:none;';}
$info = mysql_fetch_array($q);

$firstname = $info['name'];
$firstname = explode(' ', $firstname);$firstname = $firstname[0];
?>

<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Keyshock Admin</title>

<style>


.field{padding:10px;width:100%;color:black;    }
.field:hover{color:black;border-color:black;}

.field1{padding:10px;border:1px solid grey;background-color:white;width:100%;color:grey;line-height: 20px;}


table tr td{padding:10px;vertical-align: top;}



.greenbtn {
    color: white;
    letter-spacing: 1px;
    font-size: 16px;
    height: 45px;
    padding: 0 20px;
    line-height: 43px;
    background: #559057;
    background: url(/imgs/lock_icon.png) no-repeat 17px 15px, -moz-linear-gradient(top, #559057 0%, #4e9a50 100%);
    background: url(/imgs/lock_icon.png) no-repeat 17px 15px, -webkit-linear-gradient(top, #559057 0%,#4e9a50 100%);
    background: url(/imgs/lock_icon.png) no-repeat 17px 15px, linear-gradient(to bottom, #559057 0%,#4e9a50 100%);
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#559057', endColorstr='#4e9a50',GradientType=0 );
}
.greenbtn {
    font-family: 'PT Sans';
    height: 39px;
    line-height: 39px;
    width: 100px;
    display: inline-block;
    border-radius: 3px;
    border: 1px solid #aeaeae;
    font-weight: bold;
    margin-top: 20px;
    text-shadow: none;
    background: #ffffff;
    background: -moz-linear-gradient(top, #ffffff 0%, #ededed 100%);
    background: -webkit-linear-gradient(top, #ffffff 0%,#ededed 100%);
    background: linear-gradient(to bottom, #ffffff 0%,#ededed 100%);
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#ededed',GradientType=0 );
    text-decoration: none;
    text-align: center;
    margin-bottom: 40px;
    border: 1px solid #438625;
    color: white;
    background: #68c43e;
    background: -moz-linear-gradient(top, #68c43e 0%, #408223 100%);
    background: -webkit-linear-gradient(top, #68c43e 0%,#408223 100%);
    background: linear-gradient(to bottom, #68c43e 0%,#408223 100%);
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#68c43e', endColorstr='#408223',GradientType=0 );
    color: white;
    font-size: 18px;
}

</style>

</head>

<body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

<?=$header ?>

<div id="email" style="display:none"><?=$info['email'] ?></div>
<div id="subject" style="display:none">Re: CV: Evaluation</div>















<div style="<?=$displaynone ?>padding:10px 30px;">
  

<table style="width:100%;">




  <tr><td style="width:150px;">Email:</td>
  <td><div class="field"><?=$info['email'] ?></div></td></tr>
  
  <tr><td style="width:150px;">Name:</td>
  <td><div class="field"><?=$info['name'] ?></div></td></tr>


  <tr><td>Message:</td>
  <td><textarea class="field1" style="font-family:arial;height:380px;" onclick="this.select()">Hello <?=$firstname ?>,
 
Thank you for your patience. We are pleased to confirm that our CV analyst and CV expert who specialises in your profession, have completed a full evaluation. Please read below for our scores and expert analysis:


<?=$info['evaluation'] ?>,

From above you can see great advantages and a great amount of potential your CV can reach. The potential can definitely help you attain higher paying salaries in the profession you are targeting. If you would like our team to write a powerful CV, we can definitely offer this by assigning our CV analyst and two of our CV Experts to write a cutting edge CV that will represent you. We can do this at £149 with 3 CV Changes (Customised Premium CV Package). Undeniably, we have the greatest advantages:

- Trusted CV partner to industry leading job boards – through trust of our extensive CV experience.
- Undeniable Success Rate – our testimonials show how Keyshock has become a leader in CV expertise.
- 100% Satisfaction Guarantee – we will revise your CV until you are satisfied
- Premium support included. We are your family. Our support team are ready to help you with any changes.

We look forward to helping you reach the highest salaries in your career through your CV. If you’re interested in optimising your CV for higher salaries in your profession, please do not hesitate to contact us.

Level 2 Evaluation Team,
Keyshock CV Experts</textarea></td></tr>

<tr><td></td><td><a class="greenbtn" href="?done=true&id=<?=$info['id'] ?>">Approved</a></td></tr>

</table>



</div>


    <script type="text/javascript">


  function copyToClipboardMSG(element) {
  var $temp = $("<input>");
  $("body").append($temp);
  $temp.val(message).select();
 
  document.execCommand("copy");
  $temp.remove();
  }


  function copyToClipboard(element) {
  var $temp = $("<input>");
  $("body").append($temp);
  $temp.val($(element).text()).select();
 
  document.execCommand("copy");
  $temp.remove();
}
    </script>


    <script src='autosize.js'></script>
    <script type="text/javascript">

    autosize(document.querySelectorAll('textarea'));


    </script>



</body>

</html>