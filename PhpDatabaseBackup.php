<?php
/*
* https://www.tutorialspoint.com/php/perform_mysql_backup_php.htm
*/
class PhpDatabaseBackup
{
    private $dbhost;
    private $dbuser;
    private $dbpass;
    private $dbname;

    public function __construct(array $param)
    {

        $this->dbhost = $param['dbhost'];
        $this->dbuser = $param['dbuser'];
        $this->dbpass = $param['dbpass'];
        $this->dbname = $param['dbname'];
    }

    public function mysql()
    {
        $db_connect = mysqli_connect($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname);
        $db_connect->set_charset("utf8");

        $tables = array();
        $sql = "SHOW TABLES";
        $result = mysqli_query($db_connect, $sql);

        while ($row = mysqli_fetch_row($result)) {
            $tables[] = $row[0];
        }

        $sqlScript = "";
        foreach ($tables as $table) {

            $query = "SHOW CREATE TABLE $table";
            $result = mysqli_query($db_connect, $query);
            $row = mysqli_fetch_row($result);

            $sqlScript .= "\n\n" . $row[1] . ";\n\n";


            $query = "SELECT * FROM $table";
            $result = mysqli_query($db_connect, $query);

            $columnCount = mysqli_num_fields($result);

            for ($i = 0; $i < $columnCount; $i++) {
                while ($row = mysqli_fetch_row($result)) {
                    $sqlScript .= "INSERT INTO $table VALUES(";
                    for ($j = 0; $j < $columnCount; $j++) {
                        $row[$j] = $row[$j];

                        if (isset($row[$j])) {
                            if (is_numeric($row[$j])) {
                                $sqlScript .= $row[$j];
                            } else {
                                $sqlScript .= "'" . $row[$j] . "'";
                            }
                        } else {
                            $sqlScript .= "''";
                        }
                        if ($j < ($columnCount - 1)) {
                            $sqlScript .= ',';
                        }
                    }
                    $sqlScript .= ");\n";
                }
            }

            $sqlScript .= "\n";
        }

        if (!empty($sqlScript)) {
            $backup_file_name = $this->dbname . '_backup_' . time() . '.sql';
            $fileHandler = fopen($backup_file_name, 'w+');
            $number_of_lines = fwrite($fileHandler, $sqlScript);
            fclose($fileHandler);

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($backup_file_name));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($backup_file_name));
            ob_clean();
            flush();
            readfile($backup_file_name);
            exec('rm ' . $backup_file_name);
        }
    }
}
