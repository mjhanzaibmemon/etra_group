<?php

if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
header('Content-type: text/html;');

include('../db.php');
include('adminheader.php');

$user = addslashes(trim($_GET['user']));
$id = addslashes($_GET['id']);

if(empty($user)){die('NO USER');}

if($_GET['user']=='rabban'){}else{exit('Wrong User: '.$user);}

if(empty($id)){

    $q = mysql_query("SELECT * FROM `evaluation` WHERE `request` = '1' AND `bywho` = '$user' ORDER BY `id` ASC LIMIT 1");
    $info = mysql_fetch_array($q);
    if(mysql_num_rows($q)==0){

    //NO EVALUATION FOUNDV - FIND A EVALUATION TO SET TO THIS USER
    $checkq = mysql_query("SELECT * FROM `evaluation` WHERE `request` = '1' AND `bywho` = '' ORDER BY `id` ASC LIMIT 1");
    $checkinfo = mysql_fetch_array($checkq);

    if(mysql_num_rows($checkq)==0){die('<style>body{font-family:arial;margin:10px;padding:10px;}</style>None Left for today. Thank you.');}else{//FINAL CHECK

    //FOUND AN EVALUATION AND SET IT AS PERMANENT TO THIS USER
    $sndq = mysql_query("UPDATE `evaluation` SET `bywho` = '$user' WHERE `id` = '{$checkinfo['id']}' LIMIT 1");
 
    header("Location: atsrequest.php?id=".$checkinfo['id'].'&user='.$user);die;

    }

    }else{


    $checkq = mysql_query("SELECT * FROM `evaluation` WHERE `request` = '1' AND `bywho` = '$user' LIMIT 1");
    $checkinfo = mysql_fetch_array($checkq);
    header("Location: atsrequest.php?id=".$checkinfo['id'].'&user='.$user);die;

    }
}//ALL WORKING NEEDS TO ASSIGN AN ID

if($_POST['submit']=='Needs Looking At'){

    mysql_query("UPDATE `evaluation` SET `request` = '4' WHERE `id` = '$id' LIMIT 1");header("Location: atsrequest.php?&user=".$user);die;

}

if(($_POST['submit']=='Finished Evaluation')&&(!empty($id))){

if($_POST['uneven']=='1'){$visual .= 'We’ve all been told that appearances do not matter as much as substance, but in the case of your CV this just isn’t true. I found your design to be visually uneven. The appearance is not polished, and it doesn’t say "high potential" as your experience suggests. You must remember that your CV is your marketing tool. It is the first impression a potential employer has of you.<br><br>';}


if($_POST['contactdetails']=='1'){$visual .= 'Make certain that the additional pages of your CV include your contact details on them. If a hiring manager prints your CV and the pages are accidentally separated, the manager is still able to identify the additional pages. They will not spend time trying to place a page that has been separated and will move on to the next CV.<br><br>';}

if($_POST['refavailable']=='1'){$visual .= 'Having a References section with a list of names and contact details, or even having a note reading \'References Available\' is unnecessary. Employers typically assume that you’ll have this on hand during interviews.<br><br>';}


if($_POST['bullets']=='1'){$visual .= 'I liked your use of bullets to add emphasis, but you probably want to consider limiting them in some areas to increase the impact to the employer. If they see too many bullets, they might find it difficult to zero in on the most important information. Size and type of bullets are also a consideration. Although seemingly minor, visual impact of a resume is the key to ensuring that an employer reads it thoroughly.<br><br>';}
if($_POST['shortsentences']=='1'){$visual .= 'Some of your phrases or statements are too short. Brevity is important, but if the statement is too short to convey something new or interesting about you, it should be omitted, or lengthened until it does.<br><br>';}

if($_POST['personaldesc']=='1'){$cvwriting .= 'Your CV doesn\'t include a summary section, which is the key component that compels the hiring manager to keep reading. The career summary content should provide hiring managers with a brief, yet detailed synopsis of what you have to offer as a potential employee. The purpose of this section is to define you as a professional and highlight the areas that are most relevant to your career level and job target.<br><br>';}

if($_POST['personaldesc']=='2'){$cvwriting .= 'Your career summary is not as powerful as it should be. As this is a key component intended to compel the hiring manager to keep reading, this section should define you as a professional and ought to contain elements most relevant to your career level and job target.<br><br>';}




if($_POST['passivelanguage']=='1'){

if(!empty($_POST['achieverreference'])){$_POST['achieverreference'] = nl2br($_POST['achieverreference']);}

    $cvwriting .= 'From the way the CV is worded, you come across as a "doer," as opposed to an "achiever." Too many of your job descriptions are task-based rather than results-based. This means that they tell what you did rather than what you achieved. This is a common mistake for non-professional CV writers. To be effective and create excitement, a strong CV helps the hiring executive envisage you delivering similar achievements at his or her company. Here are some examples of task-based sentences in your CV:<br><br> - <font class="achieverquote">'.$_POST['achieverreference1'].'</font><br> - <font class="achieverquote">'.$_POST['achieverreference2'].'</font><br><br>';}

if($_POST['doerachiever']=='1'){$cvwriting .= 'Employers want to know about your previous contributions and more specifically, how you made a difference at your last position. More importantly, they want to know how you are going to make a significant difference at their company.<br><br>
When I read your CV, I did not find the kind of compelling language that would bring your work to life. Instead, I saw many passive words and non-action verbs. Phrases like “preparing ” and “ability to” are overused, monotonous, and add little value to your CV. Strong action verbs, used with compelling language are what\'s needed to outline exemplary achievements. Now, let’s put it all together. Here’s a real life example taken from a former client’s CV. By changing the language, we helped to improve the perception of the candidate.<br><br><ul>
    
    <li><b>Passive language / Doing:</b> Negotiated contracts with vendors</li>
    <li><b>Action language / Achieving:</b> Slashed payroll/benefits administration costs 30% by negotiating pricing and fees, while ensuring the continuation and enhancements of services.</li>

</ul>';}

$visual = addslashes($visual);
$cvwriting = addslashes($cvwriting);
$atsrecenteducation = addslashes($_POST['recenteducation']);

mysql_query("UPDATE `evaluation` SET `request` = '2', `visual` = '$visual',`cvcontents` = '$cvwriting',`bywho` = '$user',`atsrecenteducation` = '$atsrecenteducation' WHERE `id` = '$id' LIMIT 1");


header("Location: atsrequest.php?user=$user");die;
}

 
$q = mysql_query("SELECT * FROM `evaluation` WHERE `request` = '1' AND `id` = '$id' AND `bywho` = '$user' LIMIT 1");
if(mysql_num_rows($q)==0){die('ID NOT FOUND OR ALREADY DONE');}
$info = mysql_fetch_array($q);



//CHECK DUPLICATE
if(!empty($info['email'])){

$checkduplciateq = mysql_query("SELECT * FROM `evaluation` WHERE `email` = '{$info['email']}' AND `request` = '2' LIMIT 1");
if(mysql_num_rows($checkduplciateq)=='1'){
$fetchduplicateq = mysql_fetch_array($checkduplciateq);
$delq = mysql_query("DELETE FROM `evaluation` WHERE `id` = '{$info['id']}' LIMIT 1");
if($delq){echo 'DELETED DUPLICATE';}
header("Location: atsrequest.php?user=$user");die;
}

}else{
//NO EMAIL
$delq = mysql_query("DELETE FROM `evaluation` WHERE `id` = '{$info['id']}' LIMIT 1");
if($delq){echo 'DELETED - NO EMAIL';header("Location: atsrequest.php?user=$user");}

die;
}

if(empty($info['s3name'])){
//NO CV
$delq = mysql_query("DELETE FROM `evaluation` WHERE `id` = '{$info['id']}' LIMIT 1");
if($delq){echo 'DELETED - NO FILE';}
header("Location: atsrequest.php?user=$user");die;

}

$firstname = explode(' ', $info['name']);
$firstname = ucfirst($firstname[0]);

$downloadcv = '<a style="display: block;
    text-align: center;
    padding: 4px 5px;
    margin-bottom: 5px;
    text-decoration: none!important;
    background: #f6f6f6;
    border: 1px solid #eaeaea;
    -webkit-border-radius: 3px;
    border-radius: 3px;
    font-size: 13px;
    font-weight: bold;
    color: rgb(102, 102, 102);
    width: 150px;
    border: 1px solid #808080;" href="https://s3-eu-west-1.amazonaws.com/keyshock/'.$info['s3name'].'">Click To Download CV</a>';
//

$leftodo = '<div style="    font-size: 16px;
    background-color: #c5c5c5;
    padding: 4px;
    font-family: monospace;
    border-radius: 5px;
    display: inline-block;">'.mysql_num_rows(mysql_query("SELECT * FROM `evaluation` WHERE `request` = '1' AND `bywho` = ''")).' left</div>';

?>

<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Winning CV Admin</title>

<style>

body{padding:0;margin:0;background-color: #e3e3e3;  font-family: arial; }

.header{     background-color: #002d61;
    height: 51px;
   padding:13px 30px;}

.field{padding:10px;border:1px solid grey;background-color:white;width:100%;color:grey;line-height: 20px;}
.field:hover,.field:focus{color:black;border-color:black;outline:0;}

table tr td{    padding: 15px 0px;vertical-align: top;}



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
    height: 39px;
    line-height: 39px;
    width: 230px;
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

.greenbtn:hover {cursor:pointer;}


.firsttd{font-size:15px;}


label {
    display: block;
    padding-left: 15px;
    text-indent: -15px;
    font-size: 16px;
    margin-bottom: 19px;color:black;
}
input {
  width: 13px;
  height: 13px;
  padding: 0;
  margin:0;
  vertical-align: bottom;
  position: relative;
top: -3px;
  *overflow: hidden;
}

</style>
</head>

<body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

<div class="header" style="display:none"><img src="" style="width: 210px;"></div>

<?=$header?>

<div id="email" style="display:none"><?=$info['email'] ?></div>
<div id="subject" style="display:none">Re: CV: Evaluation</div>














<form method="POST" action="?&user=<?=$user?>&id=<?=$id?>">
<div style="padding:10px 30px;">
  

<table style="width:100%;">

<tr><td><?=$leftodo?></td><td></td></tr>

  <tr><td class="firsttd">CV FILE:</td>
  <td><?=$downloadcv?></td></tr>

  <tr><td  class="firsttd" style="width:150px;">Full name:</td>
  <td style="    color: #002d61;
    font-weight: bold;"><?=ucwords($info['name']) ?></td></tr>
  

  <tr style="display:none;"><td class="firsttd">Profession:</td>
  <td style="color: green;"><?=ucwords($info['profession']) ?></td></tr>
  

  <tr><td class="firsttd">Recent education:</td>
  <td style="color: green;"><input style="width:100%;padding:10px;" type="input" name="recenteducation" placeholder="e.g. Birmingham University"></td></tr>


  <tr><td class="firsttd">Choose either:</td><td>

<label><input type="radio" name="personaldesc" value="1"/> No summary/personal description on the CV</label>
<label><input type="radio" name="personaldesc" value="2"/> Shit summary/personal description on the CV</label>

</td></tr>

<tr><td class="firsttd">Your evaluation:</td><td style="color: green;">
<label><input type="checkbox" name="uneven" value="1"/> Uneven CV presentation/Uneven CV design</label>
<label><input type="checkbox" name="contactdetails" value="1"/> Applicant's Contact details not on all pages</label>
<label><input type="checkbox" name="refavailable" value="1"/> "Reference available" is needed or showing a reference section</label>
<label><input type="checkbox" name="bullets" value="1"/> There is bullet points on the CV</label>
<label><input type="checkbox" name="shortsentences" value="1"/> Short sentences (ALWAYS TICK)</label>
<label><input type="checkbox" name="passivelanguage" value="1"/> Passive language instead of action (ALWAYS TICK)</label>
<label><input type="checkbox" name="doerachiever" value="1" onclick="showMe('achiref1');"> Doer rather than a achiever - needs two examples from the work experience/past job experiences (ALWAYS TICK):</label>




<div id="achiref1" style="line-height: 41px;display:none">- <input style="width:75%;padding:10px;" type="input" name="achieverreference1" placeholder="e.g. Birmingham University"></div>
<div id="achiref2" style="line-height: 41px;display:none">- <input style="width:75%;padding:10px;" type="input" name="achieverreference2" placeholder="e.g. Birmingham University"></div>

</td></tr>


<tr><td></td><td>
<input type="submit" class="greenbtn" name="submit" value="Finished Evaluation">
<input style="float: right;
    background: none;
    background-color: grey;
    border: 1px solid #4c4c4c;" type="submit" class="greenbtn" name="submit" value="Needs Looking At"></td></tr>

</table>



</div>
</form>


<script>



    function showMe (box) {
        


        var chboxs = document.getElementsByName("doerachiever");
        var vis = "none";

        for(var i=0;i<chboxs.length;i++) { 
            if(chboxs[i].checked){
             vis = "block";
                break;
            }
        }

        document.getElementById("achiref1").style.display = vis;
        document.getElementById("achiref2").style.display = vis;
        
    
    }

</script>


</body>

</html>