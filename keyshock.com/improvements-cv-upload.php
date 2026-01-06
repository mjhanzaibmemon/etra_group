<?

if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
header('Content-type: text/html;');

include('db.php');
include('header.php');


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
$uid = addslashes($_GET['uid']);
$packagetype = addslashes($_GET['packagetype']);



///////////////////////////INSERT INTO DATABASE IF UNIQUE ID ISNT FOUND

if(!empty($packagetype)){
    
        if(empty($uid)){

            $now = time();
            $insert= mysql_query("INSERT INTO `cv` SET `added` = '$now',`packagetype` = '$packagetype',`ipaddress` = '{$_SERVER['REMOTE_ADDR']}'");
            $uid = mysql_insert_id();

            header('Location: https://keyshock.com/improvements-cv-upload.php?packagetype='.$packagetype.'&uid='.$uid);

            die;

        }else{

            $q = mysql_query("SELECT * FROM `cv` WHERE `id` = '$uid' AND `ipaddress` = '{$_SERVER['REMOTE_ADDR']}' LIMIT 1");
            $info = mysql_fetch_array($q);

            if(mysql_num_rows($q)=='0'){header('Location: https://keyshock.com/improvements-cv-upload.php');die;}

        }

}else{


        header('Location: https://keyshock.com/cv-writing.html');die;

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
        if (!class_exists('S3'))require_once('S3.php');

        $msg='';
        if($_SERVER['REQUEST_METHOD'] == "POST")
        {
        $name = $_FILES['file']['name'];
        $size = $_FILES['file']['size'];
        $tmp = $_FILES['file']['tmp_name'];
        $ext = getExtension($name);

        if(strlen($name) > 0){}
        }

    $time = time();
    $info['s3filetime'] = time();
    $info['s3name'] = $actual_image_name;

    mysql_query("UPDATE `cv` SET `s3name` = '{$actual_image_name}',`s3filetime` = '$time' WHERE `id` = '$uid' LIMIT 1");


}


if(!empty($_GET['cancel']))$errors = '<div id="error" style="    margin-bottom: 27px;    border: 1px solid #b80000;    padding: 12px;    color: #b80000;">Please complete payment through PayPal to complete your application form.</div>';


$tpl = str_replace('{header}',$header,$tpl);
$tpl = str_replace('{footer}',$footer,$tpl);

$tpl = str_replace('{packagetitle}',$jrpackages[$packagetype]['title'],$tpl);
$tpl = str_replace('{errors}',$errors,$tpl);
$tpl = str_replace('{uniqueid}',$uid,$tpl);
$tpl = str_replace('{fullname}',$info['name'],$tpl);
$tpl = str_replace('{emailaddress}',$info['email'],$tpl);
$tpl = str_replace('{contactnumber}',$info['contactnumber'],$tpl);
$tpl = str_replace('{cvraw}',$info['cvraw'],$tpl);
$tpl = str_replace('{targetedjob}',$info['targetedjob'],$tpl);

if(!empty($info['s3name'])){$tpl = str_replace('{currents3file}','<div style="margin-bottom:5px;color:black;">'.str_replace($uid.'-','',$info['s3name']).' - Attached '.ago($info['s3filetime']).'</div>',$tpl);}else{$tpl = str_replace('{currents3file}','',$tpl);}

$tpl = str_replace('{uploadforms}',$uploadforms,$tpl);
$tpl = str_replace('{totalprice}',$jrpackages[$packagetype]['price'],$tpl);

///////////////////////////INSERT INTO DATABASE IF UNIQUE ID ISNT FOUND

if(isset($_SERVER['HTTPS'])) {
    if ($_SERVER['HTTPS'] == "on") {
        $tpl = str_replace('http://','https://',$tpl);
    }
}

echo '

<html>
<head>
<script>
function load()
{
document.paypal_form.submit()
}
</script>
</head>

<body onload="document.getElementById(\'paypal_form\').submit();">
<form action="payments.php?id='.$uid.'" method="post" id="paypal_form" name="paypal_form">
    <input type="hidden" name="cmd" value="_xclick" />
    <input type="hidden" name="no_note" value="1" />
    <input type="hidden" name="lc" value="UK" />
    <input type="hidden" name="currency_code" value="GBP" />
    <input type="hidden" name="bn" value="Jobrise_StandardCVImprovement_UK" />
    <input type="hidden" name="item_number" value="'.$uid.'" / >
    <input type="hidden" name="jr_item_type" value="{packageid}" / >



</form>
</body>
</html> ';

//echo $tpl;

?>