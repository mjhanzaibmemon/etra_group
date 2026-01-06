<?
$conn = '';

$dbUser = "keyshock_ext";
$dbPass = "rabban123";
$dbName = "keyshock_keyshock";

// Setup database connection
$conn = mysql_connect ('localhost' , $dbUser , $dbPass);
mysql_select_db ($dbName , $conn);

mysql_connect("localhost", "$dbUser", "$dbPass") or die(mysql_error());

date_default_timezone_set('Europe/London');


function mysql_connect($server,$username,$password){
	return mysqli_connect($server,$username,$password);
}

function mysql_select_db($database_name,$link){
	return mysqli_select_db($link,$database_name);
}

function mysql_query($query){ global $conn;
	return mysqli_query($conn,$query);
}

function mysql_fetch_array($result){
	return mysqli_fetch_assoc($result);
}

function mysql_num_rows($result){
	return mysqli_num_rows($result);
}

function mysql_insert_id(){ global $conn;
	return mysqli_insert_id($conn);
}

?>