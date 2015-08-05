<?php

/**
 * Created by PhpStorm.
 * User: Wojtek <woshiu@protonmail.ch>
 * Date: 04/08/2015
 */
class Db {

    protected $servername = "localhost";
    protected $username = "root";
    protected $password = "";
    protected $db = "msp_www_typo3";

    public function connect() {
        // Create connection
        $conn = new mysqli($this->servername, $this->username, $this->password,$this->db);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    }

    public function getCommaFields($conn, $table, $excepts = ""){
        // get a string with the names of the fields of the $table,
        // except the onews listed in '$excepts' param
        $out = "";
        if ($rs = $conn->query("SHOW COLUMNS FROM `$table`" )) {
            while($r = $rs->fetch_array()) if ( !stristr(",".$r['Field']."," ,  $excepts) ) $out.= ($out?",":"").$r['Field'];
        } else
            die($conn->error);
        return $out;
    }

    public function duplicateRow($conn, $table,$primaryField,$primaryIDvalue) {
        // duplicate one record in a table
        // and return the id
        $fields = $this->getCommaFields($conn, $table,$primaryField,$primaryField);
        $sql = "insert into $table ($fields) select $fields from $table where $primaryField='".$conn->escape_string($primaryIDvalue)."' limit 0,1";
        if (!$conn->query($sql)) die($conn->error().$sql);
        return $conn->insert_id;
    }
}