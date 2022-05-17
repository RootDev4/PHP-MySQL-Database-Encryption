<?php
/*
    phpmyadmin database encryption
*/

define('ROOT', true);

// Template class
require_once('./classes/template.class.php');

// Database class
require_once('./classes/database.class.php');

$TEMPLATE->showTemplate('header');

/*
    List all tables found for the selected database
*/
if (isset($_GET['db']) && !isset($_GET['tbl']))
{
    $DBASE->database = htmlspecialchars(trim($_GET['db']));
    $DBASE->table = '';
    
    if ($DBASE->testConnection())
    {
        $tables = '';
        
        foreach ($DBASE->listTables() as $table)
        {
            $tables .= '<li><a href="?db='.$DBASE->database.'&tbl='.$table.'" class="btn btn-link">'.$table.'</a></li>';
        }
        
        $TEMPLATE->showTemplate('select_table', array('available_tables' => $tables));
    }
    else
    {
        $TEMPLATE->showTemplate('database_not_found', array('database' => $DBASE->database));
    }
}

/*
    List all columns found for the selected table
*/
elseif (isset($_GET['db']) && isset($_GET['tbl']))
{
    /*
        Encrypt table
    */
    if (isset($_POST['encryptDB']))
    {
        $enc_key = (isset($_POST['key'])) ? $_POST['key'] : '';
        $columns = (isset($_POST['columns'])) ? $_POST['columns'] : array();
        $error = array();
        
        if (strlen($enc_key) < 6)
        {
            $error[] = 'Your entered encryption key is too short (at least 6 letters).';
        }
        elseif (count($columns) < 1)
        {
            $error[] = 'You didn\'t select any column of your table.';
        }
        
        if ($error)
        {
            $error_msg = '';
            
            foreach ($error as $e)
            {
                $error_msg .= '<li>'.$e.'</li>';
            }
            
            $TEMPLATE->showTemplate('error_message', array('message' => $error_msg));
        }
        else
        {
            // Encryption class
            require_once('./classes/encryption.class.php');
            
            $DBENC->database = htmlspecialchars(trim($_GET['db']));
            $DBENC->table = htmlspecialchars(trim($_GET['tbl']));
            
            $DBENC->key = $enc_key;
            $DBENC->cols = $columns;
            
            if ($DBENC->alterType())
            {
                $DBASE->database = $DBENC->database;
                $DBASE->table = $DBENC->table;
                
                $column_list = array();
                $sql_query = array();
                
                foreach ($DBASE->listColumns() as $column)
                {
                    array_push($column_list, $column['Field']);
                }
                
                foreach ($column_list as $col)
                {
                    $sql_query[] = (in_array($col, $DBENC->cols)) ? 'AES_DECRYPT(\''.$col.'\', \''.$DBENC->key.'\') AS '.$col : $col;
                }
                
                $sql_syntax = 'SELECT '.implode(', ', $sql_query).' FROM '.$DBASE->database.'.'.$DBASE->table;
                $TEMPLATE->showTemplate('encryption_successful', array('key' => $DBENC->key, 'sql' => $sql_syntax));
            }
            else
            {
                $TEMPLATE->showTemplate('encryption_failed', array('tmp_file' => $_SESSION['databuffer']));
            }
        }
    }
    
    else
    {
        $DBASE->database = htmlspecialchars(trim($_GET['db']));
        $DBASE->table = htmlspecialchars(trim($_GET['tbl']));
        
        if ($DBASE->testConnection())
        {
            $column_list = '';
            
            foreach ($DBASE->listColumns() as $column)
            {
                $column_name = $column['Field'];
                $column_type = $column['Type'];
                $column_auto = $column['Extra'];
                
                if ($column_auto == 'auto_increment')
                {
                    $cbox  = '<input type="checkbox" value="" disabled="disabled" />';
                    $cbox .= '<span class="aiInfo">You can\'t encrypt this column, because it is set to auto_increment value.</span>';
                }
                elseif ($column_type == 'blob')
                {
                    $cbox  = '<input type="checkbox" value="" disabled="disabled" />';
                    $cbox .= '<span class="aiInfo">You can\'t encrypt this column, because it already contains binary data.</span>';
                }
                else
                {
                    $cbox = '<input type="checkbox" name="columns[]" value="'.$column_name.'" />';
                }
                
                $column_list .= '<tr><td>'.$column_name.'</td><td>'.$column_type.'</td><td>'.$cbox.'</td></tr>';
            }
            
            $rows = ($DBASE->countRows() == 1) ? '1 row is' : $DBASE->countRows().' rows are';
            
            $TEMPLATE->showTemplate('list_columns', array('num_rows' => $rows, 'columns' => $column_list));
        }
        else
        {
            $TEMPLATE->showTemplate('table_not_found', array('database' => $DBASE->database, 'table' => $DBASE->table));
        }
    }
}

/*
    List all databases
*/
else
{
    $databases = '';
    
    foreach ($DBASE->showDatabases() as $db)
    {
        $databases .= '<li><a href="?db='.$db.'" class="btn btn-link">'.$db.'</a></li>';
    }
    
    $TEMPLATE->showTemplate('select_database', array('available_databases' => $databases));
    
    if ($DBASE->hide_default_dbs) { $TEMPLATE->showTemplate('default_databases'); }

}

$TEMPLATE->showTemplate('footer');
?> 