<?php

$host = $_SERVER['HTTP_HOST']; // Get the current host (e.g., anuj.etra.group)
$subdomain = explode('.', $host)[0]; // Get the first part of the domain
$initial = $subdomain . '.';
$subdomain = '/'. $subdomain . '/etra.group';
if(!empty($initial) && $initial != "etra.") $_SERVER['DOCUMENT_ROOT'] = $_SERVER['DOCUMENT_ROOT'] . $subdomain;

require_once '../sm-db.php';
/**
 * Run a stored procedure safely (procedural style)
 */
function runProcedure($sql) {

    
    $result = mysql_query($sql);
// echo "Running: $result\n";die;
    if ($result === false) {
        die("Query failed: ");
    }

    // // collect rows if any SELECT is inside procedure
    // $rows = [];
    // if ($result) {
    //     while ($row = mysql_fetch_array($result)) {
    //         $rows[] = $row;
    //     }
    // }

    // return $rows;
}

// Get the action from CLI argument or GET param
$action = $argv[1] ?? '';
if (empty($action)) {
    $action = addslashes($_GET['action'] ?? '');
}

switch ($action) {
    case 'sync':
        runProcedure("CALL sp_ts_sync_pending()");
        echo "Sync completed\n";
        break;

    case 'mature':
        runProcedure("CALL sp_ts_mature()");
        echo "Mature completed\n";
        break;

    case 'decay':
        runProcedure("CALL sp_ts_decay()");
        echo "Decay completed\n";
        break;

    case 'health':
        runProcedure("CALL sp_update_sid_health(7, 30, 0.15, 0.30, 24)");
        echo "Health update completed\n";
        break;

    default:
        echo "No valid action specified. Use sync, mature, decay, or health.\n";
        break;
}

// */15 * * * * php /path/to/ts_cron.php sync
// 0 * * * * php /path/to/ts_cron.php mature
// 0 0 * * * php /path/to/ts_cron.php decay
// */30 * * * * php /path/to/ts_cron.php health