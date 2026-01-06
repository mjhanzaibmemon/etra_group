<?php


////////////////// ABOVE ALL IS IP RESCTRICTER



// Detect the subdomain dynamically
$host = $_SERVER['HTTP_HOST']; // Get the current host (e.g., anuj.superviral.io)
$subdomain = explode('.', $host)[0]; // Get the first part of the domain
$initial = $subdomain . '.';
$subdomain = '/'. $subdomain . '/etra.group';
if(!empty($initial) && $initial != "keyshock.") $_SERVER['DOCUMENT_ROOT'] = $_SERVER['DOCUMENT_ROOT'] . $subdomain;

$blacklist_limit = 7;

global $sesusernamev2;
global $sespasswordv2;
global $cloudwatchkey;
global $cloudwatchpassword;
global $rapidapihost;
global $rapidapikey;

require dirname($_SERVER["DOCUMENT_ROOT"]) .'/etra.group/loadParamStore.php';
// require_once dirname($_SERVER["DOCUMENT_ROOT"]) . '/etra.group/common/aws-sdk/aws-autoloader.php';

use Aws\CloudWatch\CloudWatchClient;
// loadEnv('/home/etra/.env');


function sendCloudwatchData($namespace, $metricName, $func, $dimensions, $data) {
    global $cloudwatchkey, $cloudwatchpassword;
    // Initialize CloudWatch client
    $cloudWatchClient = new CloudWatchClient([
        'region' => 'us-east-2',
        'version' => 'latest',
        'credentials' => [
            'key'    => $cloudwatchkey,
            'secret' => $cloudwatchpassword,
        ],
    ]);
    try {
        // Send custom revenue metric data to CloudWatch USD
        $cloudWatchClient->putMetricData([
            'Namespace' => $namespace,
            'MetricData' => [
                [
                    'MetricName' => $metricName,
                    'Dimensions' => [
                        [
                            'Name' => $func,
                            'Value' => $dimensions
                        ],
                    ],
                    'Unit' => 'None', // Or 'Currency' if appropriate
                    'Value' => $data, // Use the calculated revenue value
                ],
            ],
        ]);
    
        error_log($metricName . ' metric data sent successfully');
    } catch (Exception $e) {
        error_log('Error sending metric data: ' . $e->getMessage());
    }
}



$conn = '';

//if($_GET['loadnew']=='true'){loadEnv('/home/etra/.env');}



$protocol = 'https://'; 
$siteDomain = $protocol. $_SERVER['SERVER_NAME'];



// Setup database connection
$conn = mysql_connect ('localhost' , $dbUser , $dbPass) or die(mysql_error());

mysql_select_db ($dbName , $conn);


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