<?php

$host = $_SERVER['HTTP_HOST']; // Get the current host (e.g., anuj.etra.group)
$subdomain = explode('.', $host)[0]; // Get the first part of the domain
$initial = $subdomain . '.';
$subdomain = '/'. $subdomain . '/etra.group';
if(!empty($initial) && $initial != "etra.") $_SERVER['DOCUMENT_ROOT'] = $_SERVER['DOCUMENT_ROOT'] . $subdomain;

require_once  $_SERVER['DOCUMENT_ROOT'] . '/admin/common/core/layout.php';

$tpl = file_get_contents('tpl.html');

// $comp = addslashes($_GET['comp']);

// if(!empty($comp)) $brand = $comp;
// // country
// $country = mysql_query("SELECT id,country FROM `content` WHERE brand = '$brand' AND country != 'ww' group by `country`");


// $countryHtml = "";
// while ($countryData = mysql_fetch_array($country)) {

//     $countryHtml .= '<option value="' . $countryData['country'] . '">' . $countryData['country'] . '</option>';
// }

// // page

// $page = mysql_query("SELECT id,`page` FROM `content` WHERE brand = '$brand' group BY `page`;");

// $pageHtml = "";
// while ($pageData = mysql_fetch_array($page)) {

//     $pageHtml .= '<option value="' . $pageData['page'] . '">' . $pageData['page'] . '</option>';
// }

// // name

// $name = mysql_query("SELECT id,`name` FROM `content` WHERE brand = '$brand' group BY `name`;");

// $nameHtml = "";
// while ($nameData = mysql_fetch_array($name)) {

//     $nameHtml .= '<option value="' . $nameData['name'] . '">' . $nameData['name'] . '</option>';
// }


// $tpl = str_replace('{countryHtml}',$countryHtml,$tpl);
// $tpl = str_replace('{pageHtml}',$pageHtml,$tpl);
// $tpl = str_replace('{nameHtml}',$nameHtml,$tpl);


// content
$contentHQ = mysql_query("SELECT * FROM `content_history` WHERE approved = '0' AND `url` IS NOT NULL order by id desc limit 50");


$contentHtml = "";
if(mysql_num_rows($contentHQ) == 0){
    $msg = "All caught!!";
}
while ($contentHData = mysql_fetch_array($contentHQ)) {

        $contentQ = mysql_query("SELECT * FROM `content` WHERE `url` = '{$contentHData['url']}' AND `name` = '{$contentHData['name']}' order by id desc limit 1");
        $contentData = mysql_fetch_array($contentQ);

        $contentSnippet = tpl_get('contentHtml', $tpl);
        $contentSnippet = str_replace("{url}", $contentHData['url'], $contentSnippet);
        $contentSnippet = str_replace("{name}", $contentHData['name'], $contentSnippet);
        $contentSnippet = str_replace("{content_data}", $contentData['content'], $contentSnippet);
        $contentSnippet = str_replace("{content_history_data}", $contentHData['content'], $contentSnippet);
        $contentSnippet = str_replace("{cid}", $contentData['id'], $contentSnippet);
        $contentSnippet = str_replace("{chid}", $contentHData['id'], $contentSnippet);
        $contentHtml .= $contentSnippet;
}


$tpl = tpl_replace('contentHtml',$contentHtml,$tpl);
$tpl = str_replace('{msg}',$msg,$tpl);



output($tpl, $options);
