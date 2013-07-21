<?php
/**
 * @name MySQL class for CPF
 * @version 0.5 [July 14, 2012]
 * @author Scott W Coppen
 * @fileoverview
 * MySQL database handling class for CPF (Content Presentation Framework)
 */

/*
 * Copyright 2012 Scott W Coppen
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

require_once("CPF/php/Interfaces.php");

class MySQL implements IDatabase
{
    private $mConnection;
    private $mDatabase;
    private $mQueryResults = array();
    private $mLogFile;
    private $mLastError;

    protected function __construct($logFile = '')
    {
        $mLogFile = $logFile;
    }

    private function __clone() { }

    public function isConnected() { return $this->mConnection; }

    public function getDatabase() { return $this->mDatabase; }

    public function getLastError() { return $this->mLastError; }

    protected function connect($dbServer, $user, $passwd)
    {
        $this->mConnection = mysql_connect($dbServer, $user, $passwd);
        if (!$this->mConnection)
        {
            $this->mLastError = mysql_error();
	    error_log("MySQL::Connect() failed - ".$this->mLastError);
            return false;
        }

        if ($this->mLogFile)
            error_log("MySQL::Connect() to ".$dbServer." succeeded",
                      3, $this->mLogFile);
        return true;
    }

    protected function open($dbName)
    {
        if (!$this->mConnection)
        {
            $this->mLastError = "open() called without a connection";
            error_log("MySQL::".$this->mLastError);
            return false;
        }

        if (!mysql_select_db($dbName, $this->mConnection))
        {
            $this->mLastError = mysql_error();
            error_log("MySQL::open() failed - ".$this->mLastError);
            return false;
        }

        if ($this->mLogFile)
            error_log("MySQL::open() of ".$dbName." succeeded",
                      3, $this->mLogFile);
        $this->mDatabase = $dbName;
        return true;
    }
   
    protected function query($dbQuery, $forceRefresh = false)
    {
        if (!$this->mDatabase)
        {
            $this->mLastError = "query() called without active database";
            error_log("MySQL::".$this->mLastError);
            return;
        }

        if (!$forceRefresh && isset($this->mQueryResults[$dbQuery]))
            return $this->mQueryResults[$dbQuery];

        $res = mysql_query($dbQuery, $this->mConnection);
        if (!$res)
        {
            $this->mLastError = mysql_error();
            error_log("MySQL::".$this->mLastError);
            return;
        }

        if ($this->mLogFile)
            error_log("MySQL::query() of ".$sql." succeeded",
                      3, $this->mLogFile);
        $this->mQueryResults[$dbQuery] = $res;
        return $dbQuery;
    }

    public function select($query, $forceRefresh = false)
    {
        $rc = $this->query($query, $forceRefresh);
        if ($rc == $query)
            return mysql_num_rows($this->mQueryResults[$query]);

        return $rc;
    }

    protected function insert($tableName, $rowkvpairs)
    {
        $types = $this->getTableDataTypes($tableName);
        $columns = array();
        $data = array();

        foreach ($rowkvpairs as $key => &$value)
        {
            if (!array_key_exists($key, $types))
                continue;

            $columns[] = "`".$key."`";
            if ($value != "")
                $data[] = "'".mysql_real_escape_string(urldecode($value))."'";
            else
                $data[] = "NULL";
        }

        $cols = implode(",", $columns);
        $values = implode(",", $data);

        $sql = "INSERT INTO `".$tableName."` "
              ."(".$cols.") VALUES (".$values.");";
        if ($sql == $this->query($sql))
            return $this->mQueryResults[$sql];
    }
    
    protected function update($tableName, $rowkvpairs)
    {
        $types = $this->getTableDataTypes($tableName);
        $data = array();
        $where = "";

        foreach ($rowkvpairs as $key => &$value)
        {
            if (!array_key_exists($key, $types))
                continue;

            if ($key == '_id')
                $where = mysql_real_escape_string($value);

            if ($value != "")
                $data[] = "`".$key."`='".mysql_real_escape_string(urldecode($value))."'";
            else
                $data[] = "`".$key."`='NULL'";
        }

        $sql = "UPDATE `".$tableName."` SET "
              .implode(",", $data)." WHERE "
              ."`_id`=".$where.";";
        if ($sql == $this->query($sql))
            return $this->mQueryResults[$sql];
    }

    protected function queryTableAttributesRow($tableName)
    {
        if (!$this->mDatabase)
        {
            $this->mLastError = "queryTableAttributesRow() called without active database";
            error_log("MySQL::".$this->mLastError);
            return;
        }

        $query = "SELECT * FROM `information_schema`.`TABLES` "
                ."WHERE `TABLE_SCHEMA`='".$this->mDatabase."' "
                ."  AND `TABLE_NAME`='".$tableName."' "
                ."LIMIT 1";

        $res = mysql_query($query, $this->mConnection);
        if (!$res)
        {
            $this->mLastError = mysql_error();
            error_log("MySQL::queryTableAttributesRow() failed - ".$this->mLastError);
            return;
        }

        return mysql_fetch_array($res);
    }
    
    protected function querySchemaRow($tableName, $columnName)
    {
        if (!$this->mDatabase)
        {
            $this->mLastError = "querySchemaRow() called without active database";
            error_log("MySQL::".$this->mLastError);
            return;
        }

        $query = "SELECT * FROM `information_schema`.`COLUMNS` "
                ."WHERE `TABLE_SCHEMA`='".$this->mDatabase."' "
                ."  AND `TABLE_NAME`='".$tableName."' "
                ."  AND `COLUMN_NAME`='".$columnName."' "
                ."LIMIT 1";

        $res = mysql_query($query, $this->mConnection);
        if (!$res)
        {
            $this->mLastError = mysql_error();
            error_log("MySQL::querySchemaRow() failed - ".$this->mLastError);
            return;
        }

        return mysql_fetch_array($res);
    }
    
    protected function querySchemaColumn($tableName, $schemaName)
    {
        if (!$this->mDatabase)
        {
            $this->mLastError = "querySchemaColumn() called without active database";
            error_log("MySQL::".$this->mLastError);
            return;
        }

        $query = "SELECT COLUMN_NAME, ".$schemaName." "
                ."FROM `information_schema`.`COLUMNS` "
                ."WHERE `TABLE_SCHEMA`='".$this->mDatabase."' "
                ."  AND `TABLE_NAME`='".$tableName."' ";

        $res = mysql_query($query, $this->mConnection);
        if (!$res)
        {
            $this->mLastError = mysql_error();
            error_log("MySQL::querySchemaColumn() failed - ".$this->mLastError);
            return;
        }

        $col = array();
        while ($row = mysql_fetch_array($res))
        {
          if ($row[1])
            $col[$row[0]] = $row[1];
        }

        return $col;
    }
    
    public function getLastInsertID()
    {
        if (!$this->mDatabase)
        {
            $this->mLastError = "flushTable() called without active database";
            error_log("MySQL::".$this->mLastError);
            return;
        }

        return mysql_insert_id();
    }

    public function getTableAutoIncrement($tableName)
    {
        $row = $this->queryTableAttributesRow($tableName);
        if ($row['AUTO_INCREMENT'] == 'NULL')
          return 0;

        return $row['AUTO_INCREMENT'];
    }

    public function getTableComment($tableName)
    {
        return $this->queryTableAttributesRow($tableName);
    }

    public function getTableIsNullables($tableName)
    {
        return $this->querySchemaColumn($tableName, "IS_NULLABLE");
    }

    public function getTableDataTypes($tableName)
    {
        return $this->querySchemaColumn($tableName, "DATA_TYPE");
    }

    public function getTableColumnTypes($tableName)
    {
        return $this->querySchemaColumn($tableName, "COLUMN_TYPE");
    }

    public function getTableColumnComments($tableName)
    {
        return $this->querySchemaColumn($tableName, "COLUMN_COMMENT");
    }

    public function getTableNumericPrecisions($tableName)
    {
        return $this->querySchemaColumn($tableName, "NUMERIC_PRECISION");
    }
    
    public function getTableCharacterMaxLengths($tableName)
    {
        return $this->querySchemaColumn($tableName, "CHARACTER_MAXIMUM_LENGTH");
    }

    public function getTableCharacterOctetLengths($tableName)
    {
        return $this->querySchemaColumn($tableName, "CHARACTER_OCTET_LENGTH");
    }

    public function getTableEnumeratorColumn($tableName)
    {
        $datatypes = $this->getTableDataTypes($tableName);
        $isNullables = $this->getTableIsNullables($tableName);
        foreach ($datatypes as $key => &$value)
        {
            if ($isNullables[$key] == "YES")
                continue;

            if (($value == "enum") || ($value == "varchar"))
                return $key;
        }

        return "_id";
    }

    public function getTableEnumerationArray($tableName)
    {
        return $this->getTableEnumerationArrayWhere($tableName);
    }

    protected function getTableEnumerationArrayWhere($tableName, $where = "1")
    {
        $colEnum = $this->getTableEnumeratorColumn($tableName);
        $query = "SELECT _id, ".$colEnum." FROM `".$tableName."` "
                ."WHERE ".$where;
        $res = $this->query($query);

        $arr = array();
        while ($row = $this->fetchRow($res))
        {
            if ($row[0])
                $arr[$row[0]] = $row[1];
        }

        return $arr;
    }

    protected function getTableEnumerationArrayAdvanced($tableName, $tableref,
            $prewhere, $where = "1")
    {
        $colEnum = $this->getTableEnumeratorColumn($tableName);
        $query = "SELECT ".$tableref."._id, ".$tableref.".".$colEnum." "
                ."FROM `".$tableName."` ".$tableref." "
                .$prewhere." WHERE ".$where;
        $res = $this->query($query);

        $arr = array();
        while ($row = $this->fetchRow($res))
        {
            if ($row[0])
                $arr[$row[0]] = $row[1];
        }

        return $arr;
    }

    public function fetchRow($queryId)
    {
        if (!isset($this->mQueryResults[$queryId]))
        {
            $this->mLastError = "fetchRow() called without valid query";
            error_log("MySQL::".$this->mLastError);
            return false;
        }

        $row = mysql_fetch_array($this->mQueryResults[$queryId]);
        if (!$row)
            $this->mQueryResults[$queryId] = '';

        return $row;
    }

    public function close()
    {
        $this->mDatabase = ''; 
    }

    protected function disconnect()
    {
        if ($this->mConnection)
        {
            @mysql_close($this->mConnection);
            $this->mConnection = '';
        } 
    }
}

?>
