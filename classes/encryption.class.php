<?php
if (!defined('ROOT')) { die(); }

class Encryption
{
    public
        $pdo = null,
        $key = '',
        $cols = array(),
        $table = '',
        $database = '',
        $tmp_table = '';
    
   private function connect()
    {
        require('./config.php');
        $this->pdo = new PDO('mysql:host='.$hostname.';dbname='.$this->database.';charset=utf8', $username, $password);
    }
    
    private function flushTable()
    {
        $this->pdo->query('TRUNCATE '.$this->database.'.'.$this->table);
    }
    
    private function duplicateTable()
    {
        self::connect();
        
        $this->tmp_table = $this->database.'.'.md5(uniqid(rand(), true));
        
        $this->pdo->query('CREATE TABLE '.$this->tmp_table.' LIKE '.$this->database.'.'.$this->table);
        $this->pdo->query('INSERT INTO '.$this->tmp_table.' SELECT * FROM '.$this->database.'.'.$this->table);
        
        return ($this->pdo->query('SHOW TABLES LIKE \''.$this->database.'.'.$this->table.'\'')) ? true : false;
    }
    
    private function removeDuplicateTable()
    {
        $this->pdo->query('DROP TABLE '.$this->tmp_table);
    }
    
    private function restoreTable()
    {
        self::connect();
        
        $stmnt = $this->pdo->query('SELECT * FROM '.$this->tmp_table);
        $dump = $stmnt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($dump) > 0)
        {
            foreach ($dump as $data)
            {
                $values = array();
                
                foreach ($data as $key => $column)
                {
                    if (in_array($key, $this->cols))
                    {
                        $values[] = 'AES_ENCRYPT(\''.$column.'\', \''.$this->key.'\')';
                    }
                    else
                    {
                        $values[] = $column;
                    }
                }
                
                $this->pdo->query('INSERT INTO '.$this->database.'.'.$this->table.' VALUES ('.implode(', ', $values).')');
            }
        }
    }
    
    public function alterType()
    {
        if ($this->duplicateTable())
        {
            $this->flushTable();
            
            foreach ($this->cols as $col)
            {
                $stmnt = $this->pdo->query('ALTER TABLE '.$this->database.'.'.$this->table.' CHANGE '.$col.' '.$col.' BLOB NOT NULL');
            }
            
            $this->restoreTable();
            $this->removeDuplicateTable();
            
            return true;
        }
        
        else
        {
            return false;
        }
    }
}

$DBENC = new Encryption;
?>