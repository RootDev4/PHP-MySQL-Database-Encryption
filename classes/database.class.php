<?php
if (!defined('ROOT')) { die(); }

class Database
{
    public
        $pdo = null,
        $table = '',
        $database = '',
        $hide_default_dbs = true;
        
    public function __construct()
    {
        if (isset($_GET['showAllDatabases'])) { $this->hide_default_dbs = false; }
    }
    
    private function connect()
    {
        require('./config.php');
        $this->pdo = new PDO('mysql:host='.$hostname.';dbname='.$this->database.';charset=utf8', $username, $password);
    }
    
    public function testConnection()
    {
        try
        {
            self::connect();
        
            if (isset($this->table) && !empty($this->table))
            {
                $stmnt = $this->pdo->query('SHOW TABLES LIKE \''.$this->table.'\'');
                return ($stmnt->rowCount() > 0) ? true : false;
            }
            
            return true;
        }
        catch (PDOException $e)
        {
            return false;
        }
    }
    
    public function showDatabases()
    {
        self::connect();
        
        $databases = array();
        $default_db = array('information_schema', 'mysql', 'performance_schema', 'phpmyadmin', 'test', 'cdcol');
        
        $stmnt = $this->pdo->query('SHOW DATABASES');
        
        while (($db = $stmnt->fetchColumn(0)) !== false)
        {
            if ($this->hide_default_dbs)
            {
                if (!in_array($db, $default_db)) { array_push($databases, $db); }
            }
            else
            {
                array_push($databases, $db);
            }
        }
        
        return $databases;
    }
    
    public function listTables()
    {
        self::connect();
        
        $tables = array();
        $stmnt = $this->pdo->query('SHOW TABLES', PDO::FETCH_NUM);

        while ($row = $stmnt->fetch())
        {
            array_push($tables, $row[0]);
        }
        
        return $tables;
    }
    
    public function listColumns()
    {
        self::connect();
        
        $stmnt = $this->pdo->query('DESCRIBE '.$this->database.'.'.$this->table);
        return $stmnt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function countRows()
    {
        self::connect();
        
        $stmnt = $this->pdo->query('SELECT 1 FROM '.$this->database.'.'.$this->table);
        return $stmnt->rowCount();
    }
}

$DBASE = new Database;
?>