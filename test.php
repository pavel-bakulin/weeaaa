<?php

  require_once "admingo/config.php";
	require_once "admingo/db_connect.php";
	require_once "admingo/lib.php";
	
      send_mime_mail($_SERVER['SERVER_NAME'], 
               "shol31@yandex.ru", 
               $_SERVER['SERVER_NAME'], 
               "nikitagolberg@gmail.com", 
               'UTF8',  // кодировка, в которой находятся передаваемые строки 
               'windows-1251', // кодировка, в которой будет отправлено письмо 
               '$subject', 
               '$body');  	
?>