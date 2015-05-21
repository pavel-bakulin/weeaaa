<?php
class Config {
  public $default_id = 21; //корневой документ сайта  
  //public $default_sid = 126; //корневая папка сайта
  public $closed_sid = 0; //закрытая папка сайта
   
  //доступ к БД
  /*public $db_host = "localhost";  
  public $db_user = "root";
  public $db_password = "";
  public $db_name = "yomapic";*/
  public $vk_client_id = '4729716'; 
  public $vk_client_secret = 'WWTkqrUPtKuLHdDXvFBF';
  public $fb_app_id = '1549440818659649';
  public $fb_secret = 'f60c47dc4b73b2d087196fa358c779b2';   

  /*public $db_host = "localhost";  
  public $db_user = "weeaaa";
  public $db_password = "weeaaa";
  public $db_name = "user1100655_weeaaa";*/
  
  public $db_host = "localhost";  
  public $db_user = "weeaaa";
  public $db_password = "weeaaa";
  public $db_name = "user1186862_weeaaa";  
  
  //доступ к CMS
  public $admingo_login = "admin";
  
  //какие типы документов позволять создавать юзерам
  public $doctypes = array("user"=>"Клиент",
                           "material"=>"Материал",
                            "st"=>"Структурный шаблон",                            
                            "simple"=>"Функционал"
  );
  
  //показывать ли в CMS возможность экспорта в ЯМаркет
  public $yml = false; 
  
  public $showSystem = true;
  public $cms = 'admingo';
  public $email = 'shol31@yandex.ru';
  public $countonpage = 20;
  public $base_upfolder = '/uploads/';
  public $upfolder = '/uploads/';
  public $sub = false;
  public $rootOnly = false;    
}
$config = new Config();
?>