<?php
require_once('libs/Database.php');
require_once('libs/DiscoverDb.php');

$db = new Database;
$rc = new DiscoverDb($db);

$debug = $rc->getConfig()["debug"];

if ($argc > 1) {
    for ($i = 1; $i < $argc; $i++) {
        switch ($argv[$i]) {
            case "before":
                $rc->beforeSnapshot();
                break;
            case "complete":
                $rc->afterSnapshot();
                $rc->compare();
                break;
            case "after":
                $rc->afterSnapshot();
                break;
            case "compare":
                $rc->compare();
                break;
            case str_contains($argv[$i], 'export'):
                $rc->exportFile($argv[$i]);
                break;
            case "get-tables":
                $rc->getTables();
                break;
            case "help" || "h":
                helpMenu();
                break;
        }
    }
} else {
    echo "No arguments passed.\n";
}

if ($debug) {
    echo "Memory consumed: " . convert(memory_get_usage()) . "\n";
    echo "Peak usage: " . convert(memory_get_peak_usage()) . " of memory.\n";
}
