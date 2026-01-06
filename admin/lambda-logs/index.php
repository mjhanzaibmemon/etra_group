<?php

$host = $_SERVER['HTTP_HOST']; // Get the current host (e.g., anuj.etra.group)
$subdomain = explode('.', $host)[0]; // Get the first part of the domain
$initial = $subdomain . '.';
$subdomain = '/'. $subdomain . '/etra.group';
if(!empty($initial) && $initial != "etra.") $_SERVER['DOCUMENT_ROOT'] = $_SERVER['DOCUMENT_ROOT'] . $subdomain;

require_once  $_SERVER['DOCUMENT_ROOT'] . '/admin/common/core/layout.php';

$tpl = file_get_contents('tpl.html');

// $pageQuery = 'SELECT DISTINCT `page` FROM lambda_logs';
// $resultPage = mysql_query($pageQuery);
$ec2Pages = ['cron-slow-queries-log',
'crons-acquired-fraud-check', 'crons-al-billing', 'Crons-Emailer', 'crons-revenue-notification', 'applepay', 'free-likes', 'free-trial',
'order3-acquired', 'order3-cardinity', 'tt-free-likes', 'tt-free-trial', 'account-order3-acquired','free-instagram-followers','free-instagram-likes',
'free-tiktok-followers', 'free-tiktok-likes', 'applepay-stripe', 'stripe-webhook', 'al-stripe'
];

$lambdaPages = ['autofulfill-lambda', 'refills-lambda', 'autofulfill-free-lambda', 'initiate-tests', 'autolikes-lambda', 'download-thumbs-lambda', 'download-thumbs-retry-lambda'];


$pages = $lambdaPages;
natcasesort($pages);

foreach($pages as $page){

    $pages .= '<option>'. $page .'</option>';
}   


$tpl = str_replace('{pages}',$pages, $tpl);
$tpl = str_replace('{ec2Page}',implode(',',$ec2Pages), $tpl);
$tpl = str_replace('{lambdaPage}',implode(',',$lambdaPages), $tpl);

output($tpl, $options);
