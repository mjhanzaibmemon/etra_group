<?php

// display php errors
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);


$host = $_SERVER['HTTP_HOST']; // Get the current host (e.g., anuj.etra.group)
$subdomain = explode('.', $host)[0]; // Get the first part of the domain
$initial = $subdomain . '.';
$subdomain = '/' . $subdomain . '/etra.group';
if (!empty($initial) && $initial != "etra.") $_SERVER['DOCUMENT_ROOT'] = $_SERVER['DOCUMENT_ROOT'] . $subdomain;

require_once '../sm-db.php';
$time = time();
class Api
{
    public function setApiKey($value)
    {
        $this->api_key = $value;
    }
    public function setApiUrl($value)
    {
        $this->api_url = $value;
    }


    public function services()
    { // get services
        return json_decode($this->connect(array(
            'key' => $this->api_key,
            'action' => 'services',
        )));
    }


    private function connect($post)
    {
        $_post = array();
        if (is_array($post)) {
            foreach ($post as $name => $value) {
                $_post[] = $name . '=' . urlencode($value);
            }
        }

        $ch = curl_init($this->api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        if (is_array($post)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, join('&', $_post));
        }
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        $result = curl_exec($ch);
        if (curl_errno($ch) != 0 && empty($result)) {
            $result = false;
        }
        curl_close($ch);
        return $result;
    }
}

$api = new Api();

$api->setApiKey($fulfillment_api_key);
$api->setApiUrl($fulfillment_url);

// call services
$services = $api->services();

// loop through services
if (empty($services)) {
    echo "API failed";
} else {
    foreach ($services as $service) {
        // set variables
        $data = [
            'api' => $service,
            'sid' => $service->service,
            'name' => $service->name,
            'price' => $service->rate,
            'min' => $service->min,
            'max' => $service->max,
            'socialmedia' => set_social($service->name),
            'type' => set_type($service->name),
            'refill' => set_refill($service->name),
            'guarantee' => set_guarantee($service->category),
        ];

        if (filter_services($data) == 'skip') {
            // echo json_encode($data);
            // echo '<hr>';
            continue;
        }

        // check query
        $check = mysql_query("SELECT * FROM `jap_sid_packages` WHERE `sid` = '" . $data['sid'] . "' AND `socialmedia` = '" . $data['socialmedia'] . "' AND `type` = '" . $data['type'] . "' LIMIT 1");
        if (mysql_num_rows($check) > 0) {
            echo 'Already exists ' . $data['sid'] . '<br><hr>';
            continue;
        }

        $query = "INSERT INTO `jap_sid_packages` 
        (`sid`, `name`, `socialmedia`, `type`, `min`, `max`, `price`, `refill`, `premium`, `qc_score`, `added`, `updated`)
        VALUES
        (
            {$data['sid']},
            '" .($data['name']) . "',
            '" .($data['socialmedia']) . "',
            '" .($data['type']) . "',
            {$data['min']},
            {$data['max']},
            {$data['price']},
            {$data['refill']},
            0,
            0,
            {$time},
            {$time}
        )";
        
         $run = mysql_query($query);
         if (!$run) {
             echo '<br>Error in query:';
         } else {
             echo '<br>Inserted successfully ' . $data['sid'] . '<br><br>';
        }
    }
}

function filter_services($data)
{
    global $data;

    $service = $data['api'];
    $type = $data['type'];
    $socialmedia = $data['socialmedia'];
    $name = $data['name'];
    $refill = $data['refill'];
    $price = $data['price'];

    if(stripos(strtolower($name),'live') == true || stripos(strtolower($name),'story') || stripos(strtolower($name),'profile')|| stripos(strtolower($name),'reel') || stripos(strtolower($name),'igtv') || stripos(strtolower($name),'highlight')){
        return 'skip';
    }

    if (is_country($name)) {
        return 'skip';
    }

    if ($type == 'followers' && $socialmedia == 'instagram' && set_guarantee($service->category) !== 1) {
        // print_r($service);
        // echo '<br><b>Guarentee not found</b><br>';            
        return 'skip';
    }

    if($type == 'views' && !stripos(strtolower($name), '10M/Day') && !stripos(strtolower($name), '5M/Day')){
        return 'skip';
    }

    if($type == 'views' && !stripos(strtolower($name),'0-1') && !stripos(strtolower($name),'0 - 1')){
        return 'skip';
    }

    if($type == 'views' && stripos(strtolower($name),'auto')){
        return 'skip';
    }

    if($type == 'comments' && stripos(strtolower($name), 'Refill: No')){
        return 'skip';
    }

    if ($type == 'likes' && $price > 1.5) {
        // print_r($service);
        // echo '<br><b>Price too high</b><br>';            
        return 'skip';
    }
    if ($type == 'followers' && $price > 3) {
        // print_r($service);
        // echo '<br><b>Price too high</b><br>';            
        return 'skip';
    }
    if ($type == 'comments' && $price > 11) {
        // print_r($service);
        // echo '<br><b>Price too high</b><br>';            
        return 'skip';
    }

    if ($type == 'comments' && stripos(strtolower($name), 'Custom') === false) {
        // print_r($service);
        // echo '<br><b>Comments not allowed</b><br>';            
        return 'skip';
    }

    if ($type !== 'likes' && $type !== 'followers' && $type !== 'views' && $type !== 'comments') {
        // print_r($service);
        // echo '<br><b>Type not found</b><br>';
        return 'skip';
    }
    if ($socialmedia !== 'instagram' && $socialmedia !== 'tiktok') {
        // print_r($service);
        // echo '<br><b>Social not found</b><br>';
        return 'skip';
    }
    if ($refill !== 180 && $refill !== 365 && $type !== 'views' && $type !== 'comments') {
        // print_r($service);
        // echo '<br><b>Refill not found</b><br>';
        return 'skip';
    }

    return 'pass';
}


function set_social($name)
{
    $name = strtolower($name);
    if (strpos($name, 'instagram') !== false) {
        return 'instagram';
    } elseif (strpos($name, 'tiktok') !== false) {
        return 'tiktok';
    }
}

function set_refill($name)
{
    $matches = 0;

    if (preg_match('/Refill:.*([0-9]+)(d|Day|day|days|Days)?]/i', $name, $match)) {
        $matches = $match[0];
        $matches = preg_replace('/\D/', '', $matches);        
        $matches = (int)$matches;
    }
    return $matches;
}

function set_type($name)
{
    $name = strtolower($name);
    if (strpos($name, 'like') !== false) {
        return 'likes';
    } elseif (strpos($name, 'view') !== false) {
        return 'views';
    } elseif (strpos($name, 'follower') !== false) {
        return 'followers';
    } elseif (strpos($name, 'comment') !== false) {
        return 'comments';
    }
}

function set_guarantee($category)
{

    if ($category == 'Instagram Auto Likes') {
        return 1;
    }

    if (strpos($category, '[Guaranteed]') !== false) {
        return 1;
    } else {
        return 0;
    }
}

function is_country($name)
{
    $keywords = [
        // Countries and regions
        "Afghanistan",
        "Albania",
        "Algeria",
        "Andorra",
        "Angola",
        "Antigua and Barbuda",
        "Argentina",
        "Armenia",
        "Australia",
        "Austria",
        "Azerbaijan",
        "Bahamas",
        "Bahrain",
        "Bangladesh",
        "Barbados",
        "Belarus",
        "Belgium",
        "Belize",
        "Benin",
        "Bhutan",
        "Bolivia",
        "Bosnia and Herzegovina",
        "Botswana",
        "Brazil",
        "Brunei",
        "Bulgaria",
        "Burkina Faso",
        "Burundi",
        "Cabo Verde",
        "Cambodia",
        "Cameroon",
        "Canada",
        "Central African Republic",
        "Chad",
        "Chile",
        "China",
        "Colombia",
        "Comoros",
        "Congo",
        "Costa Rica",
        "Croatia",
        "Cuba",
        "Cyprus",
        "Czechia",
        "Denmark",
        "Djibouti",
        "Dominica",
        "Dominican Republic",
        "Ecuador",
        "Egypt",
        "El Salvador",
        "Equatorial Guinea",
        "Eritrea",
        "Estonia",
        "Eswatini",
        "Ethiopia",
        "Fiji",
        "Finland",
        "France",
        "Gabon",
        "Gambia",
        "Georgia",
        "Germany",
        "Ghana",
        "Greece",
        "Grenada",
        "Guatemala",
        "Guinea",
        "Guinea-Bissau",
        "Guyana",
        "Haiti",
        "Honduras",
        "Hungary",
        "Iceland",
        "India",
        "Indonesia",
        "Iran",
        "Iraq",
        "Ireland",
        "Israel",
        "Italy",
        "Jamaica",
        "Japan",
        "Jordan",
        "Kazakhstan",
        "Kenya",
        "Kiribati",
        "Kuwait",
        "Kyrgyzstan",
        "Laos",
        "Latvia",
        "Lebanon",
        "Lesotho",
        "Liberia",
        "Libya",
        "Liechtenstein",
        "Lithuania",
        "Luxembourg",
        "Madagascar",
        "Malawi",
        "Malaysia",
        "Maldives",
        "Mali",
        "Malta",
        "Marshall Islands",
        "Mauritania",
        "Mauritius",
        "Mexico",
        "Micronesia",
        "Moldova",
        "Monaco",
        "Mongolia",
        "Montenegro",
        "Morocco",
        "Mozambique",
        "Myanmar",
        "Namibia",
        "Nauru",
        "Nepal",
        "Netherlands",
        "New Zealand",
        "Nicaragua",
        "Niger",
        "Nigeria",
        "North Korea",
        "North Macedonia",
        "Norway",
        "Oman",
        "Pakistan",
        "Palau",
        "Palestine",
        "Panama",
        "Papua New Guinea",
        "Paraguay",
        "Peru",
        "Philippines",
        "Poland",
        "Portugal",
        "Qatar",
        "Romania",
        "Russia",
        "Rwanda",
        "Saint Kitts and Nevis",
        "Saint Lucia",
        "Saint Vincent and the Grenadines",
        "Samoa",
        "San Marino",
        "Sao Tome and Principe",
        "Saudi Arabia",
        "Senegal",
        "Serbia",
        "Seychelles",
        "Sierra Leone",
        "Singapore",
        "Slovakia",
        "Slovenia",
        "Solomon Islands",
        "Somalia",
        "South Africa",
        "South Korea",
        "South Sudan",
        "Spain",
        "Sri Lanka",
        "Sudan",
        "Suriname",
        "Sweden",
        "Switzerland",
        "Syria",
        "Taiwan",
        "Tajikistan",
        "Tanzania",
        "Thailand",
        "Timor-Leste",
        "Togo",
        "Tonga",
        "Trinidad and Tobago",
        "Tunisia",
        "Turkey",
        "Turkmenistan",
        "Tuvalu",
        "Uganda",
        "Ukraine",
        "United Arab Emirates",
        "United Kingdom",
        "United States",
        "Uruguay",
        "Uzbekistan",
        "Vanuatu",
        "Vatican City",
        "Venezuela",
        "Vietnam",
        "Yemen",
        "Zambia",
        "Zimbabwe",
        "Antarctica",
        "Antigua",
        "Aruba",
        "Bermuda",
        "Bolivya",
        "Bosnia",
        "Bouvé",
        "BRITISH",
        "Cape Verde",
        "Cayman Islands",
        "Noel",
        "Islands",
        "Beach",
        "Czech Republic",
        "East Timor",
        "Unreal",
        "Falkland",
        "Faroe Islands",
        "Gibraltar",
        "Greenland",
        "Guadeloupe",
        "Hong Kong",
        "Macao",
        "Macedonia",
        "Martinique",
        "Caledonia",
        "Papua",
        "KSA",
        "Svalbard",
        "Trinidad",
        "WW",
        "Futuna",
        "Yugoslavia",
        // Continents and regions
        "Africa",
        "Asia",
        "Europe",
        "North America",
        "South America",
        "Oceania",
        // Common abbreviations
        "USA",
        "UK",
        "US/EU",
        // Languages (compressed)
        "Hispanic",
        "Arab",
        "English",
        "Mandarin",
        "Hindi",
        "Spanish",
        "Arabic",
        "French",
        "Russian",
        "Portuguese",
        "Swahili",
        "Japanese",
        "German",
        "Turkish",
        "Vietnamese",
        "Korean",
        "Italian",
        "Tagalog",
        "Persian",
        "Farsi",
        "Thai",
        "Polish",
        "Dutch",
        "Malay",
        "Greek",
        "Hebrew",
        "Zulu",
        "Burmese",
        "Myanmar"
    ];
    // check if any keyword is found in the name str_pos
    foreach ($keywords as $keyword) {
        if (stripos($name, $keyword) !== false) {
            return true;
        }
    }
    return false;
}
