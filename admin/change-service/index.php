<?php
$host = $_SERVER['HTTP_HOST']; 
$subdomain = explode('.', $host)[0]; 
$initial = $subdomain . '.';
$subdomain = '/'. $subdomain . '/etra.group';
if(!empty($initial) && $initial != "etra.") $_SERVER['DOCUMENT_ROOT'] = $_SERVER['DOCUMENT_ROOT'] . $subdomain;

require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/common/core/layout.php';

global $aws_url;

$tpl = file_get_contents('tpl.html');

$category = $_GET['category'] ?? 'followers';
$jap_filter = $_GET['jap'] ?? 'jap1';
$japss = '{jap1}';
if($category == 'freeautoviews'){
    $jap_filter = 'views_jap1';
    $japss = '{views_jap1}';
}
$socialMedia = $_GET['socialMedia'] ?? 'ig';


$aws_url = "https://etra-live-japqc.s3.us-east-2.amazonaws.com/media/$category";

// $aws_url = "https://cdn.superviral.io/media/$category"; // for live

// Handle updates

if(isset($_POST['update'])){
	
    $id = addslashes($_POST['id']);
    $jap1 = addslashes($_POST['jap1_value']);
    $jap2 = addslashes($_POST['jap2_value']);
    $jap3 = addslashes($_POST['jap3_value']);

    // print_r($_POST);die;
    if(empty($jap1)) die('Id 1, can\'t be blank');

    if($category == "freeautolikes" || $category == "freeautoviews"){
        mysql_query("UPDATE automatic_likes_packages 
                     SET $jap_filter='$jap1'
                     WHERE id='$id' LIMIT 1");
    } else {
        mysql_query("UPDATE packages 
                     SET jap1='$jap1', jap2='$jap2', jap3='$jap3' 
                     WHERE id='$id' LIMIT 1");
    }
}

if (isset($_POST['massUpdate']) && !empty($_POST['mass_jap'])) {

    $old_japs = $_POST['old_mass_jap'];
    $new_japs = $_POST['mass_jap'];

    if ($category == "freeautolikes" || $category == "freeautoviews") {
        foreach ($old_japs as $i => $old) {
            $old = trim($old_japs[$i]);
            $new = trim($new_japs[$i]);

            // Skip rows with empty new value
            if ($old === '' || $new === '') {
                continue;
            }

            $old = intval($old);
            $new = intval($new);

            // Update only if row has matching old JAP
            mysql_query("
            UPDATE automatic_likes_packages
            SET $jap_filter = $new
            WHERE $jap_filter = $old
           
        ");
        }
    } else {

        foreach ($old_japs as $i => $old) {
            $old = trim($old_japs[$i]);
            $new = trim($new_japs[$i]);

            if ($old === '' || $new === '') {
                continue; // skip empty rows
            }

            $old = intval($old);
            $new = intval($new);
            // Update all 3 columns where old value exists
            mysql_query("
            UPDATE packages
        SET 
            jap1 = CASE WHEN jap1 = $old THEN $new ELSE jap1 END,
            jap2 = CASE WHEN jap2 = $old THEN $new ELSE jap2 END,
            jap3 = CASE WHEN jap3 = $old THEN $new ELSE jap3 END
        WHERE jap1 = $old OR jap2 = $old OR jap3 = $old
        ");
        }
    }


    // echo "<div style='color:green;font-weight:bold;'>✅ Mass JAP update successful!</div>";
}


// Table templates
$table_tpl = tpl_get('table', $tpl);
$row_tpl = tpl_get('row', $table_tpl);
if($category == "freeautoviews"){
    $row_tpl = str_replace('{japs}', $japss ?? 'views_jap1', $row_tpl);
}else{
    $row_tpl = str_replace('{japs}', $japss ?? 'jap1', $row_tpl);
}
// Handle premium
if(str_starts_with($category, 'premium')){
    $premiumCategory = $category;
    $premium = 1;
    $category = str_replace('premium', '', $category);
} else {
    $premium = 0;
}

// Fetch data
if($category == "freeautolikes" || $category == "freeautoviews"){
    $q = mysql_query("SELECT * FROM automatic_likes_packages WHERE brand='$brand' ORDER BY amount ASC");
} else {
    $q = mysql_query("SELECT * FROM packages 
                      WHERE brand='$brand' AND socialmedia='$socialMedia' AND type='$category' AND premium=$premium 
                      ORDER BY amount ASC");
}

// Define which JAPs to show
if($category == "freeautolikes") {
    $categoryJaps = ['jap1'];
} elseif($category == "freeautoviews") {
    $categoryJaps = ['views_jap1'];
} else {
    $categoryJaps = ['jap1','jap2','jap3'];
}

// Generate rows
$i = 1;
$massContent = '';
while($info = mysql_fetch_array($q)) {
    $info['row_num'] = $i;
    foreach($categoryJaps as $jap) {
        ${"row_".$jap} .= create_row($jap, $row_tpl, $info);
        $i++;
        $info['row_num'] = $i;
    }
}


// Fetch data
if($category == "freeautolikes" || $category == "freeautoviews"){
 $massQ = mysql_query("
        SELECT 
            $jap_filter AS jap_id,
            GROUP_CONCAT(CONCAT(amount, ' ', '$category') ORDER BY amount SEPARATOR ', ') AS package_names
        FROM automatic_likes_packages
        WHERE $jap_filter != 0
        GROUP BY $jap_filter
        ORDER BY MIN(amount) ASC
    ");
} else {
    $massQ = mysql_query("
SELECT 
    jap_id,
    GROUP_CONCAT(DISTINCT CONCAT(amount) ORDER BY amount SEPARATOR ', ') AS package_names
FROM (
    SELECT jap1 AS jap_id, amount FROM packages WHERE jap1 != 0 AND `type` = '$category' AND socialmedia='$socialMedia'
    UNION ALL
    SELECT jap2 AS jap_id, amount FROM packages WHERE jap2 != 0 AND `type` = '$category' AND socialmedia='$socialMedia'
    UNION ALL
    SELECT jap3 AS jap_id, amount FROM packages WHERE jap3 != 0 AND `type` = '$category' AND socialmedia='$socialMedia'
) AS all_japs
GROUP BY jap_id
ORDER BY jap_id;");
}

// Generate rows
$massContent = '';
while($info = mysql_fetch_array($massQ)) {

    $massContent .= '       <tr>
                                <td>'. $info['jap_id'] .'
                                    <input style="width:100%;" placeholder="old jap" type="hidden" name="old_mass_jap[]" value="'. $info['jap_id'] .'">
                                </td>
                                <td class="package">'. $info['package_names'] .' '. $info['type'] .'</td>
                                <td>
                                    <input style="width:100%;" placeholder="new jap" type="number" name="mass_jap[]" value="" required>
                                </td>
                            </tr>';

}


// Generate tables
$table = '';
foreach($categoryJaps as $jap) {
    $table .= create_tbl($jap, $table_tpl, ${"row_".$jap});
}

$tpl = tpl_replace('table', $table, $tpl);

// Notice messages
if(!empty($_POST['submit'])){
    $msg = addslashes($_POST['noticeMsg']);
    mysql_query("DELETE FROM notice_msg WHERE brand='$brand'");
    mysql_query("INSERT INTO notice_msg SET message='$msg', brand='$brand'");
}
if(!empty($_POST['delete'])){
    $deleteId = addslashes($_POST['deleteId']);
    mysql_query("DELETE FROM notice_msg WHERE id='$deleteId' AND brand='$brand'");
}

$query = mysql_query("SELECT * FROM notice_msg WHERE brand='$brand' LIMIT 1");
$data = mysql_fetch_array($query);

$tpl = str_replace('{msgId}', $data['id'] ?? '', $tpl);
$tpl = str_replace('{noticeMsgDisplay}', $data['message'] ?? '', $tpl);

// JAP dropdown options
if($category == "freeautolikes"){
    $japOption = '<option class="option-jap1" value="jap1">Jap1</option>';
} elseif($category == "freeautoviews"){
    $japOption = '<option class="option-views_jap1" value="jap1">Views Jap1</option>';
} else {
    $japOption = '<option class="option-jap1" value="jap1">Jap1</option>';
}


// load japs 
$serviceIds = '';
if($socialMedia == 'ig') $sm = 'instagram' ; else $sm = 'tiktok';

$serviceCat = $category;
if($serviceCat == 'freelikes' || $serviceCat == 'freeautolikes') $serviceCat = 'likes';
if($serviceCat == 'freeautoviews') $serviceCat = 'views';
if($serviceCat == 'freetrial') $serviceCat = 'followers';
$japQ = "SELECT * from jap_sid_packages WHERE socialmedia = '$sm' AND type = '$serviceCat'";
$resultQ = mysql_query($japQ);
while($resultJap = mysql_fetch_array($resultQ)){
    // print_r($resultJap);
    $serviceIds .= '<b>'.$resultJap['sid'] ."</b> - ". $resultJap['name'] ;
    $serviceIds .= '<br><br> <b>Min:</b> '.$resultJap['min'] ." <b>Max:</b> ". $resultJap['max'] ." <b>Refill:</b> ". $resultJap['refill'] ;
    $serviceIds .= '<hr>';    

}

$tpl = str_replace('{japOption}', $japOption, $tpl);
$tpl = str_replace('{serviceIds}', $serviceIds, $tpl);
$tpl = str_replace('{massContent}', $massContent, $tpl);

// Selected categories and filters
if(!empty($premiumCategory)){
    $tpl = str_replace('class="category-'.$premiumCategory.'"', 'class="category-'.$premiumCategory.'" selected', $tpl);
} else {
    $tpl = str_replace('class="category-'.$category.'"', 'class="category-'.$category.'" selected', $tpl);
}
$tpl = str_replace('class="option-'.$jap_filter.'"', 'class="option-'.$jap_filter.'" selected', $tpl);
$tpl = str_replace('class="option-'.$socialMedia.'"', 'class="option-'.$socialMedia.'" selected', $tpl);

output($tpl, $options);


// Functions
function create_row($jap, $row_tpl, $package){
	global $aws_url;
    $row = $row_tpl;

    $row = str_replace('{id}', $package['id'], $row);
    $row = str_replace('{i}', $package['row_num'], $row);
    $row = str_replace('{name}', $package['amount'].' '.$package['type'], $row);

    $row = str_replace('{views_jap1}', $package['views_jap1'] ?? '', $row);
    // Set values for all JAPs if exist
    $row = str_replace('{jap1}', $package['jap1'] ?? '', $row);
    $row = str_replace('{jap2}', $package['jap2'] ?? '', $row);
    $row = str_replace('{jap3}', $package['jap3'] ?? '', $row);

	if($package['type'] == "followers" || $package['type'] == 'likes'){
		$row = str_replace('{aws_url1}', $aws_url . '_'. $package['jap1'] .'.png' , $row);
    	$row = str_replace('{aws_url2}', $aws_url . '_'. $package['jap2'] .'.png' , $row);
    	$row = str_replace('{aws_url3}', $aws_url . '_'. $package['jap3'] .'.png' , $row);
    	$row = str_replace('{target}', 'target="_blank"' , $row);
		
	}else{
		$row = str_replace('{aws_url1}', '#' , $row);
        $row = str_replace('{aws_url2}', '#' , $row);
    	$row = str_replace('{aws_url3}', '#' , $row);
		$row = str_replace('{target}', '' , $row);
	}
    
    

    return $row;
}

function create_tbl($jap, $tbl, $row){
    global $jap_filter;
    $tbl = str_replace('{jap}', $jap, $tbl);
    $tbl = str_replace('{tbl_id}', $jap, $tbl);
    if($jap !== $jap_filter) $tbl = str_replace('{display_tbl}','tbl-hide',$tbl);
    $tbl = tpl_replace('row',$row,$tbl);
    return $tbl;
}
