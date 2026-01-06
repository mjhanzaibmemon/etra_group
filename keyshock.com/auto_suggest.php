<?
include ('db.php');

$show=$_GET['show'];
$q=$_GET['q'];

if($show=='type')$sql=mysql_query("SELECT * FROM `cats_uk` WHERE `name` LIKE '%{$q}%' LIMIT 5");


if($show=='area')$sql=mysql_query("SELECT * FROM `areas_uk` WHERE `name` LIKE '%{$q}%' LIMIT 5");




while($row=mysql_fetch_array($sql)){ $search=$row['name']; $bold=preg_replace("#$q#i", "<b>$0</b>", $search); $x++;
echo "<div onselectstart=\"return false;\" q=\"{$search}\" onmousedown=\"setTimeout('clearInterval(timerSuggest);',10)\" onclick=\"selectSuggest('".$row['name']."')\" class=\"suggest\" id=l{$x}>{$bold}</div>";}



?>
