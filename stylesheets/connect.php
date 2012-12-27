<?php
// $url = parse_url(getenv("CLEARDB_DATABASE_URL"));
// echo "<pre>";
// print_r($url);
// echo "</pre>";

require_once('class.db.php'); /* MySQL database class @author gabe@fijiwebdesign.com */

$host = "us-cdbr-east-02.cleardb.com";
$user = "bc224528322894";
$password = "bddbb467";
$database = "heroku_2c906cb3b25061d";

// Database Connection
$config = array('host' => $host, 'user' => $user, 'password' => $password, 'database' => $database);
$DB = Database_Mysql::getInstance($config);
// $rows = $DB->getRowList("SELECT * FROM members");

// echo '<pre>';
// print_r($rows);
// echo '</pre>';
// echo $rows[0]->id;