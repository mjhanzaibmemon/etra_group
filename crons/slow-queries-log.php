<?php

$host = $_SERVER['HTTP_HOST']; // Get the current host (e.g., anuj.etra.group)
$subdomain = explode('.', $host)[0]; // Get the first part of the domain
$initial = $subdomain . '.';
$subdomain = '/' . $subdomain . '/etra.group';
if (!empty($initial) && $initial != "etra.") $_SERVER['DOCUMENT_ROOT'] = $_SERVER['DOCUMENT_ROOT'] . $subdomain;

require_once '../sm-db.php';
// echo  '<pre style="white-space: pre-wrap; word-wrap: break-word;">';
$logFile = "/home/etra/top_20_grouped_slow_queries.txt"; // replace path as needed
$thresold = 500; // in milliseconds

if (!file_exists($logFile)) {
    die("Log file not found: $logFile");
}

$lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$queries = [];
$currentQueryLines = [];
$executionTime = null;
$insideQueryBlock = false;

foreach ($lines as $line) {
    $line = trim($line);


    if (preg_match('/^# Query \d+:/', $line)) {
        if (!empty($currentQueryLines)) {
            $queries[] = [
                'sql' => implode(' ', $currentQueryLines),
                'exec_time' => $executionTime
            ];
        }
        $currentQueryLines = [];
        $executionTime = null;
        $insideQueryBlock = true;
        continue;
    }

    
    if (preg_match('/^# Exec time\s+(.*)/', $line, $matches)) {
        
        $cols = preg_split('/\s+/', trim($matches[1]));
        if (isset($cols[1])) { // second column = total exec time
            $val = $cols[1];
            
            $executionTime = floatval(str_replace('s', '', $val));
        }
        continue;
    }

   
    if (preg_match('/^#/', $line)) {
        continue;
    }

    if($executionTime < $thresold) continue; // only log queries with exec time > 500ms
    
    if ($insideQueryBlock && !empty($line)) {
        
        $line = preg_replace('/\\\\G$/', '', $line);
        $currentQueryLines[] = $line;
    }
}

// Add the last query
if (!empty($currentQueryLines)) {
    $queries[] = [
        'sql' => implode(' ', $currentQueryLines),
        'exec_time' => $executionTime
    ];
}

// Output
$count = count($queries);
echo 'Total Slow Queries Found: ' . $count . "\n\n";
echo '<pre style="white-space: pre-wrap; word-wrap: break-word;">';
foreach ($queries as $idx => $q) {
    echo "===== QUERY " . ($idx + 1) . " =====\n";
    echo "Execution Time: " . $q['exec_time'] . "s\n";
    echo $q['sql'] . "\n\n";

    writeCloudWatchLog('cron-slow-queries-log', ' Query: '. $q['sql']);
    // sendCloudwatchData('EtraGroupCrons', 'slow-log-query', 'SlowQueries', 'slow-log-query-('. $q['sql'] .')', 1);
    sendCloudwatchData('EtraGroupCrons', 'slow-log-query', 'SlowQueries', 'slow-log-query-count', 1);

}

echo '</pre>';
