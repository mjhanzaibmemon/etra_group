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

// url
$urlQ = mysql_query("SELECT id,`url` FROM `content` WHERE brand = '$brand' AND country != 'ww' group by `url`");


$urlHtml = "";
while ($urlData = mysql_fetch_array($urlQ)) {

    $urlHtml .= '<option value="' . $urlData['url'] . '">';
}

$url_submit = addslashes($_POST['url_submit']);
$url = addslashes($_POST['url']);

if (!empty($url_submit)) {

    $url = !empty($url) ? $url : addslashes($_POST['main_url']);

    $contentHtml = "";
    $nameArr = [];

    // 1. Fetch all content table data into array
    $contentDataArr = [];
    $content = mysql_query("SELECT * FROM `content` WHERE `url` = '$url' ORDER BY id DESC");
    while ($row = mysql_fetch_array($content)) {
        $contentDataArr[$row['name']] = $row; // use name as key
        $nameArr[] = $row['name'];
    }

    // 2. Fetch tags from content_page_arrange
    $tagsResult = mysql_query("SELECT tags FROM content_page_arrange WHERE url = '$url' LIMIT 1");
    $tagsData   = mysql_fetch_array($tagsResult);
    $tagsArr    = [];

    if ($tagsData && !empty($tagsData['tags'])) {
        $tagsArr = json_decode($tagsData['tags'], true); // array of tags
    }

    // 3. First render by tags order
    foreach ($tagsArr as $tag) {
        if (isset($contentDataArr[$tag])) {
            $row = $contentDataArr[$tag];
            $contentSnippet = tpl_get('content_input', $tpl);
            $contentSnippet = str_replace("{updatedBy}", $row['updated_by'], $contentSnippet);
            $contentSnippet = str_replace("{content}", htmlspecialchars($row['content'], ENT_QUOTES, 'UTF-8'), $contentSnippet);
            $contentSnippet = str_replace("{name}", $row['name'], $contentSnippet);
            $contentHtml   .= $contentSnippet;
        } else {
            // Tag not in content table → blank placeholder
            $contentSnippet = tpl_get('content_input', $tpl);
            $contentSnippet = str_replace("{updatedBy}", "", $contentSnippet);
            $contentSnippet = str_replace("{content}", "", $contentSnippet);
            $contentSnippet = str_replace("{name}", $tag, $contentSnippet);
            $contentHtml   .= $contentSnippet;
        }
    }

    // 4. Render any leftover content.name not in tags
    foreach ($contentDataArr as $name => $row) {
        if (!in_array($name, $tagsArr)) {
            $contentSnippet = tpl_get('content_input', $tpl);
            $contentSnippet = str_replace("{updatedBy}", $row['updated_by'], $contentSnippet);
            $contentSnippet = str_replace("{content}", htmlspecialchars($row['content'], ENT_QUOTES, 'UTF-8'), $contentSnippet);
            $contentSnippet = str_replace("{name}", $row['name'], $contentSnippet);
            $contentHtml   .= $contentSnippet;
        }
    }
}


// arrange by file

$arrangeByFile = addslashes($_POST['arrangeByFile']);


if (!empty($arrangeByFile)) {


    $url_file = addslashes($_POST['url_file']);
    $url = addslashes($_POST['main_url']);
    
    $url_file = str_replace('https://', '', $url_file);

    if ($_SERVER['SERVER_NAME'] == "etra.lcl") {
        $url_file = str_replace('.io', '', $url_file); // exclude this line 
    }
    // echo $url_file . "<br>";
    $htmlContent = file_get_contents(dirname($_SERVER['DOCUMENT_ROOT']) . '/' . $url_file);

    if($htmlContent === false) {
        $error =  "Error: Unable to read the file.";
    }

    $cleanHtml = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $htmlContent);

    preg_match_all('/\{[a-zA-Z0-9_-]+\}/', $cleanHtml, $matches);

    $placeholders = array_unique($matches[0]);
    // print_r($placeholders);
    $i = 0;
    $placeholderArr = [];
    foreach ($placeholders as $placeholder) {
        $placeholder = str_replace(['{', '}'], '', $placeholder);
        if (in_array($placeholder, $nameArr)) {
            // echo $i. '). '. $placeholder . "<br>";
            $placeholderArr[] = $placeholder;
            $i++;
        }
        
    }

    session_start();
    $adminName = $_SESSION['first_name'];
    // save in db placeholderArr
    $checkExisting = mysql_query("SELECT id FROM `content_page_arrange` WHERE `url` = '$url' limit 1");
    if (mysql_num_rows($checkExisting) == 1) {

        // update
        $update = mysql_query("UPDATE `content_page_arrange` set `url` = '$url', `tags` = '" . json_encode($placeholderArr) . "', updated_by = '$adminName' WHERE `url` = '$url' limit 1");

    }else{
        // insert
        $insert = mysql_query("INSERT INTO `content_page_arrange` set `url` = '$url', `tags` = '" . json_encode($placeholderArr) . "', updated_by = '$adminName'");
    }

    if($insert || $update){
        $error = "Arranged by file successfully. Total " . count($placeholderArr) . " items found.";

    }else{
        $error = "Error: Unable to arrange by file.";
    }
    // echo $i;
} 


$tpl = str_replace('{urlHtml}', $urlHtml, $tpl);
$tpl = tpl_replace('content_input', $contentHtml, $tpl);
$tpl = str_replace('{inp_url}', $url, $tpl);
$tpl = str_replace('{error}', $error, $tpl);




output($tpl, $options);
