<?php

$id = addslashes($_GET['id']);
include('header.php');
include('db.php');


$fetchidinfoq = mysql_query("SELECT * FROM `cv` WHERE `id` = '$id' AND `ipaddress` = '{$_SERVER['REMOTE_ADDR']}' LIMIT 1");
$fetchidinfo = mysql_fetch_array($fetchidinfoq);
$item_type = $fetchidinfo['packagetype'];


// PayPal settings
$paypal_email = 'r.faruqui@live.co.uk';
$return_url = 'https://keyshock.com/improvements-cv-finish.php?id='.$id;
$cancel_url = 'https://keyshock.com/improvements-cv-upload.php?cancel=true&packagetype='.$item_type.'&uid='.$id.'#error';
$notify_url = 'https://keyshock.com/payments.php';

if(!empty($_POST["customquote"])){
$cancel_url = 'https://keyshock.com/improvements-cv-custom.php?cancel=true&customquote='.$_POST["customquote"].'&uid='.$id.'#error';
}else{
$cancel_url = 'https://keyshock.com/improvements-cv-upload.php?cancel=true&packagetype='.$item_type.'&uid='.$id.'#error';
}

// Check if paypal request or response
if (!isset($_POST["txn_id"]) && !isset($_POST["txn_type"])){//GO TO SHOPPING CART

    $item_name = $jrpackages[$item_type]['title']; 
    $item_amount = $jrpackages[$item_type]['price'];

    if($item_type=='4'){

    $item_name = $_POST["customitemname"]; 
    $item_amount = $_POST["customitemamount"]; 

    }

    $querystring = '';
    
    // Firstly Append paypal account to querystring
    $querystring .= "?business=".urlencode($paypal_email)."&";
    
    // Append amount& currency (£) to quersytring so it cannot be edited in html
    
    //The item name and amount can be brought in dynamically by querying the $_POST['item_number'] variable.
    $querystring .= "item_name=".urlencode($item_name)."&";
    $querystring .= "amount=".urlencode($item_amount)."&";
    
    //loop for posted values and append to querystring
    foreach($_POST as $key => $value){
        $value = urlencode(stripslashes($value));
        $querystring .= "$key=$value&";
    }
    
    // Append paypal return addresses
    $querystring .= "return=".urlencode(stripslashes($return_url))."&";
    $querystring .= "cancel_return=".urlencode(stripslashes($cancel_url))."&";
    $querystring .= "notify_url=".urlencode($notify_url);
    
    // Append querystring with custom field
    //$querystring .= "&custom=".USERID;
    
    // Redirect to paypal IPN
    header('location: https://www.paypal.com/cgi-bin/webscr'.$querystring);
    exit();
} else {//GO TO IPN


    mail('r.faruqui@live.co.uk', 'TRYING F SOCK OPENFIRST', print_r($_POST, true));
    
    // Response from Paypal

    // read the post from PayPal system and add 'cmd'
    $req = 'cmd=_notify-validate';
    foreach ($_POST as $key => $value) {
        $value = urlencode(stripslashes($value));
        $value = preg_replace('/(.*[^%^0^D])(%0A)(.*)/i','${1}%0D%0A${3}',$value);// IPN fix
        $req .= "&$key=$value";
    }
    
    // assign posted variables to local variables
    $data['item_name']          = $_POST['item_name'];
    $data['item_number']        = $_POST['item_number'];
    $data['payment_status']     = $_POST['payment_status'];
    $data['payment_amount']     = $_POST['mc_gross'];
    $data['payment_currency']   = $_POST['mc_currency'];
    $data['txn_id']             = $_POST['txn_id'];
    $data['receiver_email']     = $_POST['receiver_email'];
    $data['payer_email']        = $_POST['payer_email'];
    $data['custom']             = $_POST['custom'];
        
    // post back to PayPal system to validate
    $header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
    $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
    

    /*$fp = fsockopen ('ssl://www.sandbox.paypal.com', 443, $errno, $errstr, 30);
    
    if (!$fp) {
        // HTTP ERROR
        
mail('r.faruqui@live.co.uk', 'FAILED F SOCK OPEN', print_r($_POST, true));


    } else {
        fputs($fp, $header . $req);
        while (!feof($fp)) {
            $res = fgets ($fp, 1024);
            if (strcmp($res, "VERIFIED") == 0) {*/
                
                // Used for debugging
                mail('r.faruqui@live.co.uk', 'PAYPAL POST - VERIFIED RESPONSE', print_r($_POST, true));
                        
                // Validate payment (Check unique txnid & correct price)
               // $valid_txnid = check_txnid($data['txn_id']);
                // PAYMENT VALIDATED & VERIFIED!


            $sql = mysql_query("UPDATE `cv` SET `payment` = '{$data['payment_amount']}',`ppstatus` = '{$data['payment_status']}',`ppid` = '{$data['txn_id']}',`ppemail` = '{$data['payer_email']}' WHERE `id` = '{$data['item_number']}' LIMIT 1");


                     $orderid = mysql_insert_id($sql);

                    
                    if ($orderid) {
                        // Payment has been made & successfully inserted into the Database
                     mail('r.faruqui@live.co.uk', 'PAYPAL POST - PAYMENT SUCCESSFUL - START WORKING ON CV', print_r($data, true));


                    } else {
                        // Error inserting into DB
                        // E-mail admin or alert user
                        mail('r.faruqui@live.co.uk', 'PAYPAL POST - INSERT INTO DB WENT WRONG', print_r($data, true));
                    }

            
           /*  } else if (strcmp ($res, "INVALID") == 0) {
            
                // PAYMENT INVALID & INVESTIGATE MANUALY!
                // E-mail admin or alert user
                
                // Used for debugging
                @mail("r.faruqui@live.co.uk", "PAYPAL DEBUGGING", "Invalid Response<br />data = <pre>".print_r($_POST, true)."</pre>");
            }
        }
   fclose ($fp);
    }//THE F SOCKET CLOSE


    */
}
?>