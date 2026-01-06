<?php

$host = $_SERVER['HTTP_HOST']; // Get the current host (e.g., anuj.etra.group)
$subdomain = explode('.', $host)[0]; // Get the first part of the domain
$initial = $subdomain . '.';
$subdomain = '/' . $subdomain . '/etra.group';
if (!empty($initial) && $initial != "etra.") $_SERVER['DOCUMENT_ROOT'] = $_SERVER['DOCUMENT_ROOT'] . $subdomain;

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . '/etra.group/sm-db.php';

// global $url;

$date = date('YmdHis');

$multiCurl = [];
$mh = curl_multi_init();

$q = mysql_query("SELECT id, payment_id, `order_session`
                                FROM orders
                                WHERE added >= UNIX_TIMESTAMP(DATE_FORMAT(NOW() - INTERVAL 1 HOUR, '%Y-%m-%d %H:00:00'))
                                AND added < UNIX_TIMESTAMP(DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00'))
                                ORDER BY added DESC limit 5;");


while ($data = mysql_fetch_array($q)) {

        $token_data = [
          'app_id' => $acquired_app_id,
          'app_key' => $acquired_app_key
        ];

        $url = $acquired_base_url . 'transactions/';

        $login_url = $acquired_base_url . 'login';
        $Data = json_encode($token_data);

        $curl = curl_init($login_url);

        // Check if $Data is already a JSON string
        $jsonData = is_array($Data) ? json_encode($Data) : $Data;

        curl_setopt_array($curl, [
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Connection: Keep-Alive",
                "Accept: application/json"
            ],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $jsonData,
            CURLOPT_TIMEOUT => 10,  // Limit execution time
            CURLOPT_CONNECTTIMEOUT => 5,  // Connection time limit
            CURLOPT_TCP_FASTOPEN => true,  // Faster TCP connection
            CURLOPT_FRESH_CONNECT => false,  // Allow Keep-Alive
            CURLOPT_FORBID_REUSE => false,  // Keep-Alive enabled
            CURLOPT_NOSIGNAL => 1,  // Prevent multi-threading issues
            CURLOPT_DNS_CACHE_TIMEOUT => 300,  // Cache DNS for 5 min
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,  // Prefer IPv4
        ]);

        $json_response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // Check for cURL errors
        if ($json_response === false) {
            $error = curl_error($curl);
            curl_close($curl);
            echo json_encode(["error" => "cURL Error: " . $error]);
        }

        curl_close($curl);
        // print_r($json_response);die;

        $getToken = json_decode($json_response, true);

        if ($getToken != "Curl Error") {
          $token = $getToken['access_token'];
        }
   
    $url .= $payment_id;
 
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/json",  "Authorization: $token",));

    curl_multi_add_handle($mh, $ch);
    $multiCurl[] = $ch;
}


$running = null;
do {
    curl_multi_exec($mh, $running);
} while ($running);

foreach ($multiCurl as $ch) {
    $response_data = curl_multi_getcontent($ch);

    writeCloudWatchLog('crons-acquired-fraud-check', 'Order session ' . $data['order_session'] . ' Curl response: ' . $response_data);

    $response_data = json_decode($response_data, true);

    if (strpos($response_data['status'], 'fraud') !== false) {

        echo 'fraud';

        mysql_query("UPDATE orders SET `refund` = '1' , `disputed` = '0' WHERE `order_session` = '{$data['order_session']}' ORDER BY id DESC LIMIT 1");

        $checkExist = mysql_query("SELECT * FROM `blacklist` WHERE emailaddress = '{$dataOS['emailaddress']}' OR igusername = '{$dataOS['igusername']}'OR ipaddress = '{$dataOS['ipaddress']}'");
        if (mysql_num_rows($checkExist) == 0) {
            mysql_query("INSERT INTO blacklist SET emailaddress = '{$dataOS['emailaddress']}', igusername = '{$dataOS['igusername']}',ipaddress = '{$dataOS['ipaddress']}', `billingname` = '{$dataOS['payment_billingname_crdi']}', added = '$now', brand = 'sv' ");
            echo 'insert';
            writeCloudWatchLog('crons-fraud-check', "Inserted blacklist {$dataOS['emailaddress']} or {$dataOS['igusername']} or {$dataOS['ipaddress']}");
        }
    }

    curl_multi_remove_handle($mh, $ch);
    curl_close($ch);
}

curl_multi_close($mh);

// function sha256hash_status($param, $secret)
// {
//     if (in_array($param['status_request_type'], ['ORDER_ID_ALL', 'ORDER_ID_FIRST', 'ORDER_ID_LAST', 'ORDER_ID_SUCCESS'])) {
//         $str = $param['timestamp'] . $param['status_request_type'] . $param['company_id'] . $param['merchant_order_id'];
//     } elseif (in_array($param['status_request_type'], ['TRANSACTION_ID', 'TRANSACTION_ID_CHILDREN_ALL', 'TRANSACTION_ID_CHILDREN_FIRST', 'TRANSACTION_ID_CHILDREN_LAST', 'TRANSACTION_ID_CHILDREN_SUCCESS'])) {
//         $str = $param['timestamp'] . $param['status_request_type'] . $param['company_id'] . $param['transaction_id'];
//     } elseif (in_array($param['status_request_type'], ['BIN'])) {
//         $str = $param['timestamp'] . $param['status_request_type'] . $param['company_id'] . $param['bin'];
//     }
//     return hash('sha256', $str . $secret);
// }
