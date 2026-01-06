<?

$ordersession = addslashes($_COOKIE['ordersession']);

if(empty($ordersession)) {


	header('Location: /404');


}else{

$checkifexistsq = mysql_query("SELECT * FROM `order_session` WHERE `id` = '$ordersession' AND `ipaddress` = '{$_SERVER['REMOTE_ADDR']}' LIMIT 1");
if(mysql_num_rows($checkifexistsq)=='0'){exit('NO ORDER SESSION');
	header('Location: /404');}

}

$info = mysql_fetch_array($checkifexistsq);

?>