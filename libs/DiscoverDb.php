<?php
include("TableProcessor.php");
include("ArrayDiffMultidimensional.php");
include("helper.php");

class DiscoverDb
{

    private $db;

    private string $cachfilePath;

    private string $rootfilePath;

    private array $config;

    public array $tables;

    private string $dbName;

    public function __construct($db)
    {
        $this->db = $db;
        $this->cachfilePath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR;
        $this->rootfilePath = dirname(__DIR__) . DIRECTORY_SEPARATOR;
        $this->config = include(dirname(__DIR__) . DIRECTORY_SEPARATOR . "config.php");

        include($this->rootfilePath . "dbconfig.php");
        $this->dbName = $db;

        if (!file_exists($this->rootfilePath . "tables.php")) {
            $this->getTables();
            $this->tables = include($this->rootfilePath . 'tables.php');
        } else {
            $this->tables = include($this->rootfilePath . 'tables.php');
        }
    }

    public function beforeSnapshot()
    {
        $tableMatrix = $this->getRecordsCount();

        $this->createJsonFile('old_snapshot.json', $tableMatrix);
        // track updates
        $this->updateSetUp("before");
    }

    public function afterSnapshot()
    {
        $tableMatrix = $this->getRecordsCount();
        $this->createJsonFile('new_snapshot.json', $tableMatrix);
        // track updates
        $this->updateSetUp("after");
    }

    public function compare()
    {
        // Load JSON files into PHP arrays
        $file1 = file_get_contents($this->cachfilePath . 'old_snapshot.json');
        $file2 = file_get_contents($this->cachfilePath . 'new_snapshot.json');

        $json1 = json_decode($file1, true);
        $json2 = json_decode($file2, true);

        // Check if both files are valid JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            die("Error decoding JSON files.");
        }

        // Compare the two JSON arrays
        $differences = (new ArrayDiffMultidimensional)::compareInsertsDeletes($json1, $json2);

        // Output the differences
        if (empty($differences['differences'])) {
            echo "\e[32mThere are no changes for Inserts and Deletes.\e[0m\n";
        } else {
            echo "Differences found:\n";
            foreach ($differences['differences'] as $difference) {
                echo $difference . "\n";
            }
            echo "------------------------------------------------\n";
            if ($differences['deletes'] === []) {
                echo "Deletes found: 0\n";
            } else {
                echo "Deletes found:\n";
                foreach ($differences['deletes'] as $delete) {
                    echo $delete . "\n";
                }
            }
            if ($differences['inserts'] === []) {
                echo "Inserts found: 0\n";
            } else {
                echo "Inserts found:\n";
                foreach ($differences['inserts'] as $inserts) {
                    echo $inserts . "\n";
                }
            }
            echo "------------------------------------------------\n";
        }
        $this->compareUpdates();
    }

    public function compareUpdates()
    {
        $file1 = file_get_contents($this->cachfilePath . 'before_processed_tables.json');
        $file2 = file_get_contents($this->cachfilePath . 'after_processed_tables.json');

        $json1 = json_decode($file1, true);
        $json2 = json_decode($file2, true);

        // Check if both files are valid JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            die("Error decoding JSON files.");
        }

        // Compare the two JSON arrays
        $differences = (new ArrayDiffMultidimensional)::compare($json1, $json2);

        // Output the differences
        if (empty($differences)) {
            echo "\e[32mThere are no changes for Updates.\e[0m\n";
        } else {
            echo "Updates found:\n";
            foreach ($differences as $difference) {
                if ($difference['rows'] !== 0) {
                    foreach ($difference['rows'] as $row) {
                        $output = explode("\\", $row);
                        echo "  table ";
                        echo "'" . $output[0] . "'";
                        echo "  [UPDATE] PK Column Name: ";
                        echo " '" . $output[1] . "'";
                        echo "  PK Row ID: ";
                        echo $output[2];
                        echo "\n";
                    }
                }
            }
            echo "------------------------------------------------\n";
        }
    }

    public function getRecordsCount(): array
    {
        $coll = [];
        foreach ($this->tables as $table) {
            $this->db->query("SELECT * from $table");
            $coll[$table] = $this->db->rowCount();
        }
        return $coll;
    }

    public function createJsonFile($fileName, $data)
    {
        $file = $this->cachfilePath . $fileName;
        if (file_exists($file)) {
            $this->deleteFile($file);
        }
        $data = $this->formatArrayToJson($data);
        file_put_contents($file, $data);
    }

    public function createPHPFile($fileName, $data)
    {
        $file = $this->rootfilePath . $fileName;
        if (file_exists($file)) {
            $this->deleteFile($file);
        }
        file_put_contents($file, $data);
    }

    public function createAnyFile($filePathName, $data)
    {
        file_put_contents($filePathName, $data);
    }

    public function deleteFile($fileName)
    {
        unlink($fileName);
    }

    private function formatArrayToJson($data)
    {
        $coll = [];
        foreach ($data as $key => $value) {
            $coll[] = "\"$key\":$value";
        }
        return "{" . implode(",", $coll) . "}";
    }

    public function exportFile($fileSplit)
    {
        $fileName = explode("=", $fileSplit)[1];
        $filePathName = $this->rootfilePath . $this->config['file_save_dir'] . DIRECTORY_SEPARATOR . $fileName;
        echo "Starting to generate file...\n";
        if (file_exists($filePathName)) {
            echo "\e[31mExport error, file exists!\e[0m\n";
            exit();
        } else {
            ob_start();
            $this->compare();
            $data = ob_get_clean();
            $this->createAnyFile($filePathName, $data);
        }
        echo "Done.\n";
    }

    private function getTablesService()
    {
        $this->db->query("SHOW TABLES");
        $dbTables = [];
        foreach ($this->db->resultset() as $table) {
            $dbTables[] = $table->{"Tables_in_" . $this->dbName};
        }
        return $dbTables;
    }

    public function checkTablesFile()
    {
        echo "Checking `tables.php` file...\n";
        if (file_exists($this->rootfilePath . "tables.php")) {
            $tables = include($this->rootfilePath . "tables.php");
            if (!empty($tables[0])) {
                echo "There is a table and the file looks to be ok, its good practice to double check `tables.php` to make sure all tables are there. \n";
            } else {
                echo "Error, `tables.php` file didn't generate properly.\n";
            }
        }
        echo "File check complete.\n";
    }

    public function getTables()
    {
        echo "Fetching tables...\n";
        echo "Generating `tables.php` file...\n";
        $dbTables = $this->getTablesService();
        echo "Generating PHP File...\n";
        $data = "<?php\n" . "return ['" . implode("','", $dbTables) . "'];\n";
        $this->createPHPFile("tables.php", $data);
        $this->checkTablesFile();
        echo "Done.\n";
    }

    public function updateSetUp($tag)
    {
        $tables = $this->getTablesService();
        $tableProcessor = new TableProcessor($tables, $this->db, $this->rootfilePath, $this->dbName);
        $tableProcessor->processTables($tag);
    }
}

