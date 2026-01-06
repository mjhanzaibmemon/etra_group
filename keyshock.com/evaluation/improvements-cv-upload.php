<?

if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
header('Content-type: text/html;');

include('../db.php');
include('../header.php');


function ago($time)
{$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
   $lengths = array("60","60","24","7","4.35","12","10");
   $now = time();
       $difference     = $now - $time;
       $tense         = 'ago';
   for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
       $difference /= $lengths[$j];
   }
   $difference = round($difference);
   if($difference != 1) {
       $periods[$j].= "s";
   }   return "$difference $periods[$j] ago";}

///////////////////////////MAIN VARIABLES

$tpl = file_get_contents('improvements-cv-upload.html');

if($_GET['rabban']=='true'){
$tpl = file_get_contents('improvements-cv-upload-test.html');
;}

$uid = addslashes($_GET['uid']);
$packagetype = addslashes($_GET['packagetype']);


///////////////////////////INSERT INTO DATABASE IF UNIQUE ID ISNT FOUND
    
        if(empty($uid)){

            $now = time();
            $insert= mysql_query("INSERT INTO `evaluation` SET `added` = '$now',`ipaddress` = '{$_SERVER['REMOTE_ADDR']}'");
            $uid = mysql_insert_id();

            header('Location: /evaluation/improvements-cv-upload.php?&uid='.$uid);

            die;

        }else{

            $q = mysql_query("SELECT * FROM `evaluation` WHERE `id` = '$uid' AND `ipaddress` = '{$_SERVER['REMOTE_ADDR']}' LIMIT 1");
            $info = mysql_fetch_array($q);

            if(mysql_num_rows($q)=='0'){header('Location: /evaluation/improvements-cv-upload.php');die;}

        }


///////////////////////////AMAZON S3

 
if($_POST['submit']=='Attach CV'){

        function getExtension($str) 
        {
        $i = strrpos($str,".");
        if (!$i) { return ""; }
        $l = strlen($str) - $i;
        $ext = substr($str,$i+1,$l);
        return $ext;
        }


        // Bucket Name
        $bucket="keyshock";
        if (!class_exists('S3'))require_once('../S3.php');

        $msg='';
        if($_SERVER['REQUEST_METHOD'] == "POST")
        {
        $name = $_FILES['file']['name'];
        $size = $_FILES['file']['size'];
        $tmp = $_FILES['file']['tmp_name'];
        $ext = getExtension($name);

        if(strlen($name) > 0)
        {

        // File size validation
        if($size<(1024*1024 * 3))
        {
        //Rename image name. 
        $actual_image_name = $uid."-".str_replace(' ','',$name);

        if($s3->putObjectFile($tmp, $bucket , $actual_image_name, S3::ACL_PUBLIC_READ) )
        {
        $msg = "S3 Upload Successful."; 
        $s3file=$actual_image_name;
        //echo 'S3 File URL:'.$s3file;
        }
        else
        $msg = "S3 Upload Fail.";

        }
        else
        $msg = "The maximum size for your CV is 2 MB";

        }}


    $time = time();
    $info['s3filetime'] = time();
    $info['s3name'] = $actual_image_name;

    mysql_query("UPDATE `evaluation` SET `s3name` = '{$actual_image_name}',`s3filetime` = '$time' WHERE `id` = '$uid' LIMIT 1");


}

if($_POST['submit']=='Get a Free CV Review'){

$uid = addslashes($_POST['uid']);

$md5 = addslashes(md5('rabban'.$_POST['uid']));

mysql_query("UPDATE `evaluation` SET `md5` = '$md5' WHERE `id` = '$uid' LIMIT 1"); 

header('Location: improvements-cv-finish.php?id='.$md5);

die;

}


if(!empty($_GET['cancel']))$errors = '<div id="error" style="    margin-bottom: 27px;    border: 1px solid #b80000;    padding: 12px;    color: #b80000;">Please complete payment through PayPal to complete your application form.</div>';


$tpl = str_replace('{header}',$header,$tpl);
$tpl = str_replace('{footer}',$footer,$tpl);

$tpl = str_replace('{errors}',$errors,$tpl);
$tpl = str_replace('{uniqueid}',$uid,$tpl);
$tpl = str_replace('{id}',$info['id'],$tpl);
$tpl = str_replace('{fullname}',$info['name'],$tpl);
$tpl = str_replace('{emailaddress}',$info['email'],$tpl);
$tpl = str_replace('{cvraw}',$info['cvraw'],$tpl);

if(!empty($info['s3name'])){$tpl = str_replace('{currents3file}','<div style="margin-bottom:5px;color:black;">'.str_replace($uid.'-','',$info['s3name']).' - Attached '.ago($info['s3filetime']).'</div>',$tpl);}else{$tpl = str_replace('{currents3file}','',$tpl);}

$tpl = str_replace('{uploadforms}',$uploadforms,$tpl);
$tpl = str_replace('{totalprice}',$jrpackages[$packagetype]['price'],$tpl);

///////////////////////////INSERT INTO DATABASE IF UNIQUE ID ISNT FOUND

if(isset($_SERVER['HTTPS'])) {
    if ($_SERVER['HTTPS'] == "on") {
        $tpl = str_replace('http://','https://',$tpl);
    }
}


echo $tpl;

?>