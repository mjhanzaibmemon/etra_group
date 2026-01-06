<?

if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
header('Content-type: text/html;');

////////////////////////



include('db.php');
include('header.php');

$tpl = file_get_contents('improvements-cv-finish.html');


$id = addslashes($_GET['id']);


$q = mysql_query("SELECT * FROM `cv` WHERE `id` = '$id' LIMIT 1");

$info = mysql_fetch_array($q);
$packagetype = $info['packagetype'];

$tpl = str_replace('{header}',$header,$tpl);
$tpl = str_replace('{footer}',$footer,$tpl);


$tpl = str_replace('{ordernum}',$info['id'],$tpl);
$tpl = str_replace('{packagetype}',$jrpackages[$packagetype]['title'],$tpl);
$tpl = str_replace('{email}',$info['email'],$tpl);


echo $tpl;


?>