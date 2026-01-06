<?

if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
header('Content-type: text/html;');

////////////////////////


include('../db.php');
include('../header.php');

$tpl = file_get_contents('improvements-cv-finish.html');


$id = addslashes($_GET['id']);

if(empty($id)){die;}

$q = mysql_query("SELECT * FROM `evaluation` WHERE `md5` = '$id' AND `request` = '0' LIMIT 1");

$info = mysql_fetch_array($q);

if((!empty($id))&&($info['request']=='0')){

$emailtpl = file_get_contents('sendcvemail.html');


$message = file_get_contents('../ext/sendcvemail.html');

$count = str_word_count($info['name']);

if($count == 0){$info['name']='there';}
if($count == 2){$first= explode(' ',$info['name']);$info['name'] = $first[0];}

$signature = '
Robert Parker<br>
Winning CV Team<br>
Keys Hock Group<br>
<img src="https://keyshock.com/imgs/rob-sig.png" alt="Robert Parker" width="200" height="54" style="width:200px;height:54px;" />
<br><br>
Call our team at: 0121 285 5044<br>
E: robert@keyshock.com<br>
A: Keys Hock Group, 160 Kemp House, City Road, London EC1V 2NX';


$link = 'https://keyshock.com/cv-writing/?email=true';
$subject = $info['name'].': your CV critique request';
$content = '<div style="padding:20px;">Hi '.$info['name'].',
<br>
<br>
Thanks for submitting your CV for a critique. Welcome to Winning CV, the trusted leader in helping professionals like you, tell the best version of your career story.
<br>
<br>
Here\'s what you can expect from your CV review:
<br>
<br>
- Free, confidential, personalised evaluation from trusted experts
<br>
- Objective feedback on layout, language and how well your CV communicates your skills and expertise
<br>
- Personalised recommendations on how to make your CV stronger
<br>
<br>
Our team of CV experts know what recruiters are looking for and how to present yourself on paper to maximise opportunities.
<br>
<br>
Keep an eye on your inbox over the next few days for an email when your personalised critique is ready, or check back here. We know how important this job hunt is for you, that\'s why we want to get the best of your CV.
<br>
<br>
In the meantime, check out our career advice and learn more about Winning CV\'s services.
<br>
<br>
<a href="'.$link.'" style="border-radius: 5px; padding: 15px 30px; 
border: 1px solid #178e17; display: inline-block; font-size:17px; color:#ffffff;     background: #5cb85c;
text-decoration: none; font-family:sans-serif;">LEARN MORE &raquo;</a>
<br>
<br>
Thanks!
<br>
<br>
'.$signature.'


</div>';



$message = str_replace('{subject}',$subject,$message);
$message = str_replace('{content}',$content,$message);
$message = str_replace('{unsub}','id='.urlencode($personinfo['id']).'&adid='.urlencode($personinfo['advert_id']).'&email='.urlencode($personinfo['emailaddress']),$message);


$to = trim($info['email']);


$headers .= "From: Winning CV<info@keyshock.com>\r\n";
$headers .= "Reply-To: info@keyshock.com\r\n";
$headers .= 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";


if (mail($to,$subject,$message,$headers)) {
   // '<font color="green">The email has been sent!</font><br>';
   } else {
   // The email has failed! Unsubscribed
   //mysql_query("UPDATE `allemails` SET `unsubscribe` = '1' WHERE `id` = '{$personinfo['id']}' LIMIT 1");
   }



mysql_query("UPDATE `evaluation` SET `request` = '1' WHERE `md5` = '$id' LIMIT 1");


}


$tpl = str_replace('{header}',$header,$tpl);
$tpl = str_replace('{footer}',$footer,$tpl);


$tpl = str_replace('{ordernum}',$info['id'],$tpl);
$tpl = str_replace('{packagetype}',$jrpackages[$packagetype]['title'],$tpl);
$tpl = str_replace('{email}',$info['email'],$tpl);


echo $tpl;


?>