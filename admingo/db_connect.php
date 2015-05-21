<?php
class db_connect {
  private $db;
  
  public function __construct($host, $user, $password, $dbname) {
    $this->db = @mysql_connect($host, $user, $password);
  	@mysql_query("SET NAMES utf8");
    @mysql_query("SET time_zone = " . @mysql_real_escape_string($_SESSION['offset']));
  	if (!$this->db) {die("Could not connect: " . mysql_error());}  	
	  @mysql_select_db($dbname, $this->db);
  } 
  
  public function execute($sql, $returnResult = true) {
    $result = mysql_query($sql) or die("Invalid query: ". $sql .': ' . mysql_error());
    //$this->log($sql);
    if ($returnResult) return $result; 
    else return;
  }
  
  private function log($log) {
    $filename = $_SERVER['DOCUMENT_ROOT'].'/log.txt';  
    $handle = fopen($filename, 'a');
    $log = $log.chr(13).chr(10);
    fwrite($handle, $log);
    fclose($handle);  
  } 
}

$db = new db_connect($config->db_host, $config->db_user, $config->db_password, $config->db_name);

?>