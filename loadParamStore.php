<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
// 

// $host = $_SERVER['HTTP_HOST']; // Get the current host (e.g., anuj.superviral.io)
// $subdomain = explode('.', $host)[0]; // Get the first part of the domain
// $initial = $subdomain . '.';
// $subdomain = '/'. $subdomain . '/etra.group';
// if(!empty($initial) && $initial != "superviral.") $_SERVER['DOCUMENT_ROOT'] = $_SERVER['DOCUMENT_ROOT'] . $subdomain;

$requestUri = $_SERVER['REQUEST_URI'];
$currentUrl = $requestUri;
$parsedUrl = parse_url($currentUrl, PHP_URL_PATH);
$segments = explode('/', trim($parsedUrl, '/'));
$lastSegment = end($segments);

if(($lastSegment != 'refunds') && !$awsNotNeeded )
require_once dirname($_SERVER["DOCUMENT_ROOT"]) . '/etra.group/common/aws-sdk/aws-autoloader.php';

$redis = new Redis();
try {
    $redis->connect('127.0.0.1', 6379);
} catch (Exception $e) {
    echo "Redis connection failed: " . $e->getMessage(); die;
}

global $sesusernamev2;
global $sespasswordv2;
global $cloudwatchkey;
global $cloudwatchpassword;
global $rapidapihost;
global $rapidapikey;

use Aws\Ssm\SsmClient;
use Aws\Exception\AwsException;

function loadEnvFromParameterStore($prefix = '/', $useRedis = true, $timeout = 3600) {
    global $redis;

    $redisKey = "ssm_cache:" . md5($prefix);
    $cachedJson = $redis->get($redisKey);

    if ($useRedis && $cachedJson !== false) {

        // echo "oaded parameters from Redis cache.<br>";
        $loadedVars = json_decode($cachedJson, true);
        foreach ($loadedVars as $name => $value) {
            putenv("$name=$value");
            global $$name;
            $$name = $value;

            // echo "Loaded: $name = $value<br>";
        }
        return $loadedVars;
    }

    // echo "Fetching parameters from AWS SSM...<br>";

    try {
        $ssm = new SsmClient([
            'region' => 'us-east-2',
            'version' => 'latest',
        ]);

        $loadedVars = [];
        $nextToken = null;

        do {
            $result = $ssm->getParametersByPath([
                'Path' => $prefix,
                'WithDecryption' => true,
                'Recursive' => true,
                'NextToken' => $nextToken
            ]);

            foreach ($result['Parameters'] as $param) {
                $name = basename($param['Name']);
                $value = $param['Value'];

               
                putenv("$name=$value");

                
                global $$name;
                $$name = $value;

                $loadedVars[$name] = $value;
            }

            $nextToken = $result['NextToken'] ?? null;
        } while ($nextToken);

        // Save to Redis
        if ($useRedis && !empty($loadedVars)) {
            $redis->setex($redisKey, $timeout, json_encode($loadedVars));
        }

        return $loadedVars;

    } catch (AwsException $e) {
        echo "<strong style='color: red;'>AWS Error:</strong> " . $e->getAwsErrorMessage() . "<br>";
        error_log("AWS SSM error: " . $e->getMessage());
        return [];
    } catch (Exception $e) {
        echo "<strong style='color: red;'>General Error:</strong> " . $e->getMessage() . "<br>";
        error_log("General error: " . $e->getMessage());
        return [];
    }
}


$envVars = loadEnvFromParameterStore('/', true, 86400); // 1 day timeout
$loadParamErrorFlag = 0;
if (empty($envVars)) {
    echo "[⚠️] No parameters found or an error occurred.\n";
    $loadParamErrorFlag = 1;
}

// Debug output if needed:
// echo "<pre>"; print_r($envVars); echo "</pre>";
