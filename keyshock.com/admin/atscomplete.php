<?php

if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
header('Content-type: text/html;');

include('../db.php');
include('adminheader.php');

$now = time();
$id = addslashes($_GET['id']);

//
if($_POST['submit']=='Scan HTML'){

function GetBetween($content,$start,$end){
$r = explode($start, $content);
    if (isset($r[1])){
        $r = explode($end, $r[1]);
        return $r[0];
    }
    return '';
}

$viewsource = $_POST['firstpage'];
$viewsource2 = $_POST['secondpage'];

$viewsource = preg_replace( "/\r|\n/", "", $viewsource);
$viewsource2 = preg_replace( "/\r|\n/", "", $viewsource2);


if(!empty($viewsource)){
$skillsection = GetBetween($viewsource,'<div class="card-title mb-4">Your Skills Cloud</div>','<a class="btn btn-primary btn-block"');
$skillsection = explode('</g></g>', $skillsection);
foreach($skillsection as $persection){
  if(empty(Strip_tags($persection)))continue;
  $skillsectionref .= Strip_tags($persection).',';}

echo $skillsectionref.'<br><br>';
$allskills = explode(',',$skillsectionref);
$allskillsamt = count($allskills);

$firstrowamt = round($allskillsamt * 0.20);
$secondrowamt = round($allskillsamt * 0.30);
$thirdrowamt = $allskillsamt * 0.50;

echo 'Amounts to assign: '.$firstrowamt.'<br>'.$secondrowamt.'<br>'.$thirdrowamt.'<br><br><br><br>';

print_r($allskills);

echo '<br><br><br>';

for ($x = 1; $x <= $firstrowamt; $x++) {$firstrow .= $allskills[$x - 1].',';unset($allskills[$x - 1]);$oldx = $x;}

$secondrowamt = $secondrowamt + $oldx;
for ($x = $oldx; $x <= $secondrowamt; $x++) {$secondrow .= $allskills[$x - 1].',';unset($allskills[$x - 1]);}
$secondrow = ltrim($secondrow,',');


$thirdrow = implode(',', $allskills);

$bothskills2 = explode(',', str_replace('#',',',$firstrow.$secondrow.$thirdrow));
$bothskills = $bothskills2[0].','.$bothskills2[1];


$firstrow = trim($firstrow,',');
$secondrow = trim($secondrow,',');
$thirdrow = trim($thirdrow,',');

$allrows = $firstrow.'#'.$secondrow.'#'.$thirdrow;

$updatehtmlq = mysql_query("UPDATE `evaluation` SET 
  `atsbestfit` = '$bothskills', 
  `atstopskills` = '$allrows'  
 WHERE `id` = '{$_POST['id']}' LIMIT 1");


}
//









if(!empty($viewsource2)){

$viewsource2 = $_POST['secondpage'];
$viewsource2 = stripslashes(preg_replace( "/\r|\n/", "", $viewsource2));
$viewsource2 = GetBetween($viewsource2,'<div id="app">','<script type="text/javascript">');

//2ND PAGE

$atsemail = GetBetween($viewsource2,'Current email address: <strong>','</strong>');
$location = GetBetween($viewsource2,'your address:</p><p><span><strong>','</p>');$location = str_replace('<strong>', ', ', $location);
$years = GetBetween($viewsource2,'with your <strong>','</strong>');
$contactnumber = GetBetween($viewsource2,'</p>  <p><strong>','</strong>');

$years = str_replace('years', '', $years);$years = trim($years);

$mostrecentjob = addslashes(ucwords(GetBetween($viewsource2,'</thead><tbody><tr class=""><td>','</td>')));
$mostrecentemployer = addslashes(ucwords(GetBetween($viewsource2,'</td><td>','</td>')));

$location = addslashes($location);$location = rtrim($location,', ');$location = strip_tags($location);
$atsemail = addslashes(strip_tags($atsemail));

//IF EMPTY THEN IN VALUATION PAGE - THEN SHOW AS NOT FOUND

echo 'Name: '.$name.' <br>';
echo 'Desc: '.$desc.' <br>';
echo 'Years: '.$years.' <br>';
echo 'Recent Employer: '.$mostrecentemployer.' <br>';
echo 'Recent Job: '.$mostrecentjob.' <br>';
echo 'Location: '.$location.' <br>';
echo 'ATS Contact Number: '.$contactnumber.' <br>';
echo 'ATS Email: '.$email.' <br>';



$updatehtmlq = mysql_query("UPDATE `evaluation` SET 
  `atsdesc` = '$desc',  
  `atsrecentemployer` = '$mostrecentemployer', 
  `atsposition` = '$mostrecentjob', 
  `atscontactinfo` = '$contactnumber', 
  `atslocation` = '$location', 
  `atsexperience` = '$years', 
  `atsemail` = '$atsemail'
 WHERE `id` = '{$_POST['id']}' LIMIT 1");



}

//REDIRECT TO SEE NEW RESULTS AND AUTOMATION
if($updatehtmlq){header("Location: /admin/atscomplete.php?id=".$_POST['id']);}else{die('UNABLE TO UPDATE HTML Q');}

}

//GET FILE SIZE AND CV FILE TYPE
if(!empty($_GET['fetchid'])){

$fetchidq = mysql_query("SELECT * FROM `evaluation` WHERE `id` = '{$_GET['fetchid']}' LIMIT 1");
$fetchidres = mysql_fetch_array($fetchidq);

mysql_query("UPDATE `evaluation` SET `cvsize` = '{$info['size']}',`cvformat` = '$thecvformat' WHERE `id` = '{$fetchidres['id']}' LIMIT 1");

}

if(empty($id)){

$q = mysql_query("SELECT * FROM `evaluation` WHERE `request` = '2' LIMIT 1");
$info = mysql_fetch_array($q);
if(mysql_num_rows($q)==0){$displaynone = 'display:none;';}else{
header("Location: atscomplete.php?id=".$info['id']);die;
}
}



if(($_POST['submit']=='Send Off')&&(!empty($id))){

echo 'SENDING OFF<hr>';

$q = mysql_query("SELECT * FROM `evaluation` WHERE `request` = '2' AND `id` = '$id' LIMIT 1");
if(mysql_num_rows($q)==0)die('ERROR 202');

$info = mysql_fetch_array($q);

$info['name'] = ucwords($info['name']);


/// DB QUERIES
mysql_query("UPDATE `evaluation` SET `request` = '3',`lastemail` = '$now',`lastupdated` = '$now' WHERE `id` = '{$info['id']}' LIMIT 1");

$nowtime = time() - (259200);

mysql_query("INSERT INTO `cvreviewemail` SET 
  `cvmd5` = '{$info['md5']}', 
  `emailaddress` = '{$info['email']}', 
  `lastdone` = '$nowtime', 
  `name` = '{$info['name']}' 
  ");

$lastinsertid = mysql_insert_id();
$lastinsertidmd5 = md5('rabban'.$lastinsertid);

mysql_query("UPDATE `cvreviewemail` SET `md5` = '$lastinsertidmd5' WHERE `id` = '{$lastinsertid}' LIMIT 1");
////////


header("Location: atscomplete.php");die;

}


if(($_POST['submit']=='Update')&&(!empty($id))){

$md5 = md5('rabban'.$id);

foreach ($_POST as $key => $input_arr) {
$_POST[$key] = addslashes($input_arr);
} 



$_POST['atsdesc'] = trim(addslashes($_POST['atsdesc']));
$_POST['visual'] = trim(addslashes($_POST['visual']));
$_POST['cvcontents'] = trim(addslashes($_POST['cvcontents']));

if($_POST['atsrecentemployer']=='N/A')$_POST['atsrecentemployer']=='';

$updateq = mysql_query("UPDATE `evaluation` SET 
  `md5` = '$md5',
  `name` = '{$_POST['name']}',
  `atsemail` = '{$_POST['email']}',
  `atsdesc` = '{$_POST['atsdesc']}',
  `visual` = '{$_POST['visual']}',
  `cvcontents` = '{$_POST['cvcontents']}',
  `atsrecentemployer` = '{$_POST['atsrecentemployer']}',
  `atsposition` = '{$_POST['atsposition']}',
  `atscontactinfo` = '{$_POST['atscontactinfo']}',
  `atslocation` = '{$_POST['atslocation']}',
  `atsexperience` = '{$_POST['atsexperience']}',
  `atsrecenteducation` = '{$_POST['atsrecenteducation']}',
  `atsbestfit` = '{$_POST['atsbestfit']}',
  `atstopskills` = '{$_POST['atstopskills']}',
  `done` = '1',
  `request` = '2' WHERE `id` = '$id' LIMIT 1");

if(!$updateq){echo 'THE UPDATE NOT WORKING';}

}


if(($_POST['submit']=='Delete')&&(!empty($id))){


mysql_query("DELETE FROM `evaluation` WHERE `id` = '$id' LIMIT 1");

header("Location: atscomplete.php");die;

}
 

$q = mysql_query("SELECT * FROM `evaluation` WHERE `request` = '2' AND `id` = '$id' LIMIT 1");
if(mysql_num_rows($q)==0){$displaynone = 'display:none;';
}else{
$info = mysql_fetch_array($q);

foreach ($info as $key => $input_arr) {
$info[$key] = stripslashes($input_arr);
} 

$info['name'] = ucwords($info['name']);

$firstname = explode(' ', $info['name']);
$firstname = ucfirst($firstname[0]);




//AUTOMATED ATS DESC FILLER
if((empty($info['atsdesc']))&&(!empty($info['name']))&&(!empty($info['atstopskills']))&&(!empty($info['atsexperience']))){
  

$atskills = str_replace('#', '', $info['atsbestfit']);
$atskills = explode(',', $atskills);

if($info['atsexperience']=='0'){$typeofexp= 'LOWER';}
if($info['atsexperience']=='1'){$typeofexp= 'LOWER-TO-MID ';}
if($info['atsexperience']=='2'){$typeofexp= 'MID-TO-INTERMEDIATE ';}
if($info['atsexperience']=='3'){$typeofexp= 'INTERMEDIATE-TO-EXPERIENCED';}
if($info['atsexperience'] > '3'){$typeofexp= 'EXPERIENCED-TO-ADVANCED';}//NEEDS LOOKING INTO


$info['atsdesc'] = $info['name'].'\'s experience appears to be concentrated in '.$atskills[0].', with exposure to '.$atskills[1].'. '.$info['name'].'\'s experience appears to be '.$typeofexp.', with about '.$info['atsexperience'].' years of experience.';

}



$info['visual'] = str_replace('<br>',"\n",$info['visual']);
$info['cvcontents'] = str_replace('<br>',"\n",$info['cvcontents']);}

?>

<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Winning CV Admin</title>
<style>

.greenbtn{font-family:arial!important;}
.greenbtn:hover{cursor: pointer;}

@media (max-width: 850px) {
.nav{display:none;}
}

.field{padding:10px;border:1px solid grey;background-color:white;width:100%;color:black;}
.field:hover,.field:focus{color:black;border-color:black;outline:none;}

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
    width: 130px;
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

.fetch{    display: block;
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
    width: 100px;
    border: 1px solid #808080;
    margin-bottom: 9px;}

</style>

</head>

<body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

<?=$header ?>

<div id="email" style="display:none"><?=$info['email'] ?></div>
<div id="subject" style="display:none">Re: CV: Evaluation</div>















<div style="<?=$displaynone ?>padding:10px 30px;">


<form method="POST">
<input type="hidden" name="id" value="<?=$info['id'] ?>">
  <div style="    background-color: #cce3ff;width:100%;
    border: 1px solid #76b4ff;">
  <table style="width:100%;">
  <tr><td>HTML COPY PASTE</td><td></textarea></td></tr>
  <tr><td style="width: 150px;">CV Value HTML</td><td><textarea class="field" name="firstpage"></textarea></td></tr>
  <tr><td>CV Checker HTML</td><td><textarea class="field" name="secondpage"></textarea></td></tr>
  <tr><td></td><td><input type="submit" name="submit" value="Scan HTML" class="greenbtn" style="    margin-bottom: 10px;"></td></tr>
  </table>
  </div>
</form>


  
<form method="POST">
<table style="width:100%;">



<input type="hidden" name="id" value="<?=$info['id'] ?>">

    <tr><td class="firsttd">CV FILE:</td>
  <td><a style="display: block;
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
    border: 1px solid #808080;" href="https://s3-eu-west-1.amazonaws.com/keyshock/<?=$info['s3name'] ?>">Click To Download CV</a></td></tr>


  <tr><td style="width:150px;">CV File:</td>
  <td><?=$info['s3name']?></td></tr>


  <tr><td style="width:150px;">Name:</td>
  <td>
  <input class="field" type="input" name="name" value="<?=$info['name']?>"></td></tr>

  <tr><td>Filesize:</td>
  <td><a class="fetch" href="atscomplete.php?id=<?=$info['id'] ?>&fetchid=<?=$info['id']?>">Fetch File Size</a>
  <input class="field" type="input" name="filetype" value="<?=$info['cvsize']?>"></td></tr>
  
  <tr><td>Filetype:</td>
  <td><a class="fetch" href="atscomplete.php?id=<?=$info['id'] ?>&fetchid=<?=$info['id']?>">Fetch File Type</a>
  <input class="field" type="input" name="filetype" value="<?=$info['cvformat']?>"></td></tr>


  <tr><td>Visual:</td>
  <td><textarea class="field" name="visual" style="font-family:arial;height:380px;"><?=$info['visual']?>
  </textarea></td></tr>

  <tr><td>CV Writing:</td>
  <td><textarea class="field" name="cvcontents" style="font-family:arial;height:380px;"><?=$info['cvcontents']?>
  </textarea></td></tr>

 <tr style="background-color: #002d61;color: white;font-weight: bold;"><td>FIRST SECTION: CV VALUE</td><td></td></tr>

  <tr><td>ATS All Skills</td>
  <td><input class="field" type="input" name="atstopskills" value="<?=$info['atstopskills']?>"></td></tr>

  <tr><td>ATS Best & Top Fit</td>
  <td><input class="field" type="input" name="atsbestfit" value="<?=$info['atsbestfit']?>" placeholder="Space out with ',' "></td></tr>

 <tr style="background-color: #002d61;color: white;font-weight: bold;"><td>SECOND SECTION: CV CHECKER</td><td></td></tr>


  <tr><td>ATS Experience:</td>
  <td><input class="field" type="input" name="atsexperience" value="<?=$info['atsexperience']?>"></td></tr>

  <tr><td>ATS Recent Position:</td>
  <td><input class="field" type="input" name="atsposition" value="<?=$info['atsposition']?>"></td></tr>


  <tr><td>ATS Recent Employers:</td>
  <td><input class="field" type="input" name="atsrecentemployer" value="<?=$info['atsrecentemployer']?>"></td></tr>

  <tr><td>ATS Location:</td>
  <td><input class="field" type="input" name="atslocation" value="<?=$info['atslocation']?>"></td></tr>

  <tr><td>ATS Contact Number:</td>
  <td><input class="field" type="input" name="atscontactinfo" value="<?=$info['atscontactinfo']?>"></td></tr>

  <tr><td style="width:150px;">ATS Email:</td>
  <td>
  <input class="field" type="input" name="email" value="<?=$info['atsemail']?>"></td></tr>
  
  <tr><td>ATS Recent Education:</td>
  <td><input class="field" type="input" name="atsrecenteducation" value="<?=$info['atsrecenteducation']?>"></td></tr>



  <tr><td>ATS Description:</td>
  <td><textarea class="field" name="atsdesc" style="font-family:arial;height:380px;"><?=$info['atsdesc']?></textarea></td></tr>

<tr><td></td><td><input type="submit" name="submit" value="Update" class="greenbtn">

<a class="greenbtn" style="    height: 37px;
    width: 70px;
    margin-left: 12px;
    background: #5d5d5d;
    border-color: grey;" href="https://www.keyshock.com/cv-review/?preview=1&id=<?=md5('rabban'.$info['id'])?>" target="_blank">Preview</a>
    <input type="submit" name="submit" value="Send Off" class="greenbtn" style="float:right;"></td></tr>

</table>



<div style="height:300px;">

<input type="submit" name="submit" value="Delete" class="greenbtn" style="    margin-top: 300px;
    margin-left: 188px;
    background: none;
    background-color: #d00000;
    margin-bottom: 29px;
    border: 1px solid #820000;">

</div>

</form>


</div>


    <script src='autosize.js'></script>
    <script type="text/javascript">

    autosize(document.querySelectorAll('textarea'));


    </script>



</body>

</html>