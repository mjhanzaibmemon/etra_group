<?php

if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
header('Content-type: text/html;');

include('db.php');
include('header.php');

date_default_timezone_set('Europe/London');

$id = $_GET['id'];

$q = mysql_query("SELECT * FROM `evaluation` WHERE `md5` = '$id' LIMIT 1");
if(mysql_num_rows($q)=='0'){exit('ERROR - NOT FOUND');}

$info = mysql_fetch_array($q);

foreach ($info as $key => $input_arr) {
$info[$key] = stripslashes($input_arr);
} 

$now = time();

if(($_GET['preview']!=='1')&&($info['opened']=='0')){mysql_query("UPDATE `cvreviewemail` SET `opened` = '$now' WHERE `id` = '{$info['id']}' LIMIT 1");}
 
//WORK OUT PERCENTAGE
$percentage = round((($info['cvsize'] / 1000)) / 2.125);
$percentage2 = $percentage;
if($percentage > 97)$percentage2 = 97;

//WORK OUT FILE TYPE
if (stripos($info['s3name'], '.doc') !== false){$filetype['1'] = ' dr-selected';$filetypedesc = 'Good news: your CV is saved in a recent version of Microsoft Word. An overwhelming majority of CVs look like yours and Applicant Tracking Systems (ATS) <questionmark> love it.';}

if (stripos($info['s3name'], '.pdf') !== false){$filetype['2'] = ' dr-selected';$filetypedesc = 'Unfortunately your CV is saved in a version of Adobe Acrobat. A minority of CVs look like yours and Applicant Tracking Systems (ATS) do not favour the PDF format. We recommend using a format that Applicant Tracking Systems (ATS) <questionmark> can easily read.';}

if (stripos($info['s3name'], '.rtf') !== false){$filetype['3'] = ' dr-selected';$filetypedesc = 'Unfortunately your CV is saved in an old version of Microsoft Word. A minority of CVs look like yours and Applicant Tracking Systems (ATS) <questionmark> do not favour the old Microsoft Word format. We recommend using a format that Applicant Tracking Systems can easily read.';}

if (stripos($info['s3name'], '.txt') !== false){$filetype['4'] = ' dr-selected';$filetypedesc = 'Unfortunately your CV is saved in an old version of Notepad. A minority of CVs look like yours and Applicant Tracking Systems (ATS) <questionmark> do not favour the old Notepad format. The Notepad format is quite unprofessional. We recommend using a format that Applicant Tracking Systems can easily read.';}

if (empty($filetypedesc)){$filetype['5'] = ' dr-selected';$filetypedesc = 'Unfortunately your CV is saved in an undetectable format. A minority of CVs look like yours and Applicant Tracking Systems (ATS) <questionmark> do not favour undetectable formats. We recommend using a format that Applicant Tracking Systems can easily read.';}

$filetype = '<li class="icon-doc'.$filetype['1'].'"><span class="fileIcon"></span><span class="filetype">Doc Filetype</span><span class="filePercentage">64%</span></li>
<li class="icon-pdf'.$filetype['2'].'"><span class="fileIcon"></span><span class="filetype">PDF Filetype</span><span class="filePercentage">19%</span></li>
<li class="icon-rtf'.$filetype['3'].'"><span class="fileIcon"></span><span class="filetype">RTF Filetype</span><span class="filePercentage">8%</span></li>
<li class="icon-txt'.$filetype['4'].'"><span class="fileIcon"></span><span class="filetype">TXT Filetype</span><span class="filePercentage">7%</span></li>
<li class="icon-other'.$filetype['5'].'"><span class="fileIcon"></span><span class="filetype">Other Filetype</span><span class="filePercentage">2%</span></li>';

$filetypedesc = str_replace('<questionmark>','<span class="dropdownquestionmark">?<div class="questionmarkmsg"><span style="font-size:17px;line-height:2.3em;"><b>What is an ATS?</b></span><br>
An <b>A</b>pplicant <b>T</b>racking <b>S</b>ystem (ATS)<br>determines a candidate’s experience by taking into account the contents of a resume and passing it through a proprietary algorithm to return a result.</div></span>',$filetypedesc);

$firstname = trim($info['name']);$firstname = explode(' ', $firstname);$firstname = $firstname[0];
$bestfit = explode(',', trim($info['atsbestfit']));

/////SKILLS
$allskills = explode('#',trim(ucwords($info['atstopskills'])));

$topskill = $allskills[0];
$corecomp = $allskills[1];
$lessweighted = $allskills[2];

$topskill = explode(',',$topskill);foreach ($topskill as $skill1){$topskills .= '<span>'.trim($skill1).'</span>';}
$corecomp = explode(',',$corecomp);foreach ($corecomp as $skill2){$corecomps .= '<span>'.trim($skill2).'</span>';}
$lessweighted = explode(',',$lessweighted);foreach ($lessweighted as $skill3){$lessweighteds .= '<span>'.trim($skill3).'</span>';}

//IF EMPTY
if(empty($info['atsdesc'])){$info['atsdesc'] = 'Description cannot be formed due to unsupported format on this particular CV.';}
if(empty($info['name'])){$info['name'] = 'Full name not detected.';}
if(empty($info['atsrecentemployer'])){$info['atsrecentemployer'] = 'Most recent employer not detected.';}
if(empty($info['atsposition'])){$info['atsposition'] = 'Most recent position not detected.';}
if(empty($info['atscontactinfo'])){$info['atscontactinfo'] = 'Contact number not detected.';}
if($info['atscontactinfo']=='Not found'){$info['atscontactinfo'] = 'Contact number not detected.';}
if(empty($info['atslocation'])){$info['atslocation'] = 'Location not detected.';}
if(empty($info['atsrecenteducation'])){$info['atsrecenteducation'] = 'Recent education not detected.';}
if(empty($info['atsmanagementscore'])){$info['atsmanagementscore'] = 'Management score not detected.';}
if(empty($info['atsexperience'])){$info['atsexperience'] = 'Experience not detected.';}else{$info['atsexperience'] = $info['atsexperience'].' Years';}

if (strpos($info['visual'], '<br>') === false) {$info['visual'] = nl2br($info['visual']);}
if (strpos($info['cvcontents'], '<br>') === false) {$info['cvcontents'] = nl2br($info['cvcontents']);}



$tpl = file_get_contents('evaluation-template.html');
$tpl = str_replace('{id}',$info['id'],$tpl);
$tpl = str_replace('{name}',ucfirst($firstname),$tpl);
$tpl = str_replace('{visual}',stripcslashes(rtrim($info['visual'],'<br><br>')),$tpl);
$tpl = str_replace('{cvwriting}',stripcslashes($info['cvcontents']),$tpl);
$tpl = str_replace('{filesize}',round(($info['cvsize'] / 1000)),$tpl);
$tpl = str_replace('{percentage}',$percentage,$tpl);
$tpl = str_replace('{percentage2}',$percentage2,$tpl);
$tpl = str_replace('{filetype}',$filetype,$tpl);
$tpl = str_replace('{filetypedesc}',$filetypedesc,$tpl);

$tpl = str_replace('{overalldesc}',$info['atsdesc'],$tpl);
$tpl = str_replace('{atsfullname}',$info['name'],$tpl);
$tpl = str_replace('{atsbestfit}',$info['atsbestfit'],$tpl);
$tpl = str_replace('{atsmanagementscore}',$info['atsmanagementscore'],$tpl);
$tpl = str_replace('{atsrecentemployer}',$info['atsrecentemployer'],$tpl);
$tpl = str_replace('{atsposition}',$info['atsposition'],$tpl);
$tpl = str_replace('{atscontactinfo}',$info['atscontactinfo'],$tpl);
$tpl = str_replace('{atslocation}',$info['atslocation'],$tpl);
$tpl = str_replace('{atsexperience}',$info['atsexperience'],$tpl);
$tpl = str_replace('{atsrecenteducation}',$info['atsrecenteducation'],$tpl);

$tpl = str_replace('{atsbestfit1}',$bestfit[0],$tpl);
$tpl = str_replace('{atsbestfit2}',$bestfit[1],$tpl);

$tpl = str_replace('{atstopskills}',$topskills,$tpl);

$tpl = str_replace('{atscorecomps}',$corecomps,$tpl);

$tpl = str_replace('{atslessweighted}',$lessweighteds,$tpl);

$tpl = str_replace('{atsemailaddress}',$info['email'],$tpl);
$tpl = str_replace('{md5}',$info['md5'],$tpl);
$tpl = str_replace('{date}',gmdate('jS F Y',$info['lastupdated']),$tpl);

$tpl = str_replace('{footer}',$footer,$tpl);

echo $tpl;

?>