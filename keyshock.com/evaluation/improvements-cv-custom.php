<?

if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
header('Content-type: text/html;');

include('db.php');
include('header.php');


/////////////////////////////////


function encrypt_decrypt($action, $string) {

    $output = false;
    $encrypt_method = "AES-256-CBC";

    // hash
    $key = hash('sha256', $secret_key);
    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning

    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    if( $action == 'encrypt' ) {
    $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
    $output = base64_encode($output);}

    else if( $action == 'decrypt' ){
    $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;}


/////////////////////////////////


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

$tpl = file_get_contents('improvements-cv-custom.html');
$uid = addslashes($_GET['uid']);
$customquote = unserialize(encrypt_decrypt('decrypt', $_GET['customquote']));


$name = $customquote[0];
$price = $customquote[1];
$date = $customquote['date'];
$emailaddress = $customquote[3];
$contactnumber = $customquote[4];
$targetedjob = $customquote[5];
$packagename = $customquote[6];
$now = time();

///////////////////////////INSERT INTO DATABASE IF UNIQUE ID ISNT FOUND

var_dump($customquote);

if(!empty($_GET['customquote'])){
    
        if(empty($uid)){

            $now = time();
            $insert= mysql_query("INSERT INTO `cv` SET
                `added` = '$now',
                `packagetype` = '4',
                `ipaddress` = '{$_SERVER['REMOTE_ADDR']}',
                `name` = '$name',
                `email` = '$emailaddress',
                `contactnumber` = '$contactnumber',
                `targetedjob` = '$targetedjob',
                `customprice` = '$price',
                `custompackage` = '$packagename'");
            $uid = mysql_insert_id();

            header('Location: https://keyshock.com/improvements-cv-custom.php?uid='.$uid.'&customquote='.$_GET['customquote']);

            die;

        }else{

            $q = mysql_query("SELECT * FROM `cv` WHERE `id` = '$uid' AND `ipaddress` = '{$_SERVER['REMOTE_ADDR']}' LIMIT 1");
            $info = mysql_fetch_array($q);

            if(mysql_num_rows($q)=='0'){header('Location: https://keyshock.com/improvements-cv-upload.php?customquote'.$_GET['customquote']);die;}

        }

}else{

        header('Location: https://keyshock.com/cv-writing.html');die;

}

///////////////////////////AMAZON S3


if(!empty($_GET['cancel']))$errors = '<div id="error" style="    margin-bottom: 27px;    border: 1px solid #b80000;    padding: 12px;    color: #b80000;">Please complete payment through PayPal to complete your custom application form.</div>';

$tpl = str_replace('{header}',$header,$tpl);
$tpl = str_replace('{footer}',$footer,$tpl);

$tpl = str_replace('{packagetitle}',$packagename,$tpl);
$tpl = str_replace('{errors}',$errors,$tpl);
$tpl = str_replace('{uniqueid}',$uid,$tpl);
$tpl = str_replace('{fullname}',$name,$tpl);
$tpl = str_replace('{emailaddress}',$emailaddress,$tpl);
$tpl = str_replace('{contactnumber}',$contactnumber,$tpl);
$tpl = str_replace('{cvraw}',$info['cvraw'],$tpl);
$tpl = str_replace('{targetedjob}',$info['targetedjob'],$tpl);
$tpl = str_replace('{uploadforms}',$uploadforms,$tpl);
$tpl = str_replace('{totalprice}',$price,$tpl);
$tpl = str_replace('{customquote}',$_GET["customquote"],$tpl);




///////////////////////////INSERT INTO DATABASE IF UNIQUE ID ISNT FOUND

if(isset($_SERVER['HTTPS'])) {
    if ($_SERVER['HTTPS'] == "on") {
        $tpl = str_replace('http://','https://',$tpl);
    }
}


echo $tpl;

?>