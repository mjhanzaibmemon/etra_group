<?



if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
header('Content-type: text/html;');

include('db.php');

$perpage = 10000;

if(isset($_GET["page"])){
$page = intval($_GET["page"]);
}
else {
$page = 1;
}
$calc = $perpage * $page;
$start = $calc - $perpage;

//$q = mysql_query("SELECT * FROM `listings123_uk` WHERE `done` = '3' ORDER BY `id` DESC");
$q = mysql_query("SELECT * FROM `listings123_uk` WHERE `done`= '3' ORDER BY `id` DESC LIMIT $start, $perpage");


while($row = mysql_fetch_array($q)){


echo 'http://keyshock.com/view/'.htmlspecialchars($row['url'].'-'.$row['id'], ENT_NOQUOTES, 'UTF-8', false)."\n";


}


?>