<?


$ip = $_SERVER['REMOTE_ADDR'];

$url = "http://freegeoip.net/json/".$ip;
/*
$curl = curl_init(); 
curl_setopt($curl, CURLOPT_URL, $url); 
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); 
curl_setopt($curl, CURLOPT_TIMEOUT, 10);

$get = curl_exec($curl);

curl_close($curl);

$get = json_decode($get);

$ipinfo = json_decode(json_encode($get), true);
*/

echo $ipinfo['country_code'];

?>