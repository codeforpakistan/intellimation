<?php
session_start();
$DeveloperMode = FALSE;
GLOBAL $LYDBConnect;
$Settings = new Settings();
//$InternewwDBConnect = $Settings->myConnect();

if($DeveloperMode){
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
error_reporting(E_ALL); 
ini_set('display_errors', 'On'); 
}


class Settings {

    private $_DB_SERVER = 'localhost';
    private $_DB_NAME = '=';
    private $_DB_USERNAME = 'root';
    private $_DB_PASSWORD = '';   

    
    private $_RootURL = ""; 
    private $_ProjectName = "";
    private $_AdminEmailId = "info@dynamologic.com";
    private $_BusinessId = '';

    public function __construct() 
    { 
        $this->_ProjectName = "/intellimation/beta/";
        $this->_RootURL = "http://localhost:8080/intellimation/beta/";         
        $this->setTimeZone();
    }

//    public function myConnect() {
//        $Host = $this->_DB_SERVER;
//        $DBName = $this->_DB_NAME;
//        $User = $this->_DB_USERNAME;
//        $Password = $this->_DB_PASSWORD;
//        // echo $Host."===".$DBName."====".$User."====".$Password;
//        $a = new PDO("mysql:host={$Host};dbname={$DBName};", "{$User}", "{$Password}");
//        //print_r($a);exit;
//        $a->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
//
//        return $a;
//    }

    /*
     * This return the root url of project. 
     * @param: none
     * @return: Root URL of the project
     */

    public function GetRootURL() {
        return $this->_RootURL;
    }

    public function GetProjectPath() {
        return $_SERVER['DOCUMENT_ROOT'] . $this->_ProjectName;
    }

    public function setTimeZone()
    {
        date_default_timezone_set('US/Pacific');
    }
    
    public function GetAdminEmailID()
    {
       return $this->_AdminEmailId;
    }
    
    public function GetBusinessEmailID()
    {
       return $this->_BusinessId;
    }
   

}

global $RootURL;
global $DocumentRoot;
global $AdminEmail;
global $BusinessID;

$DocumentRoot = $Settings->GetProjectPath();
$RootURL = $Settings->GetRootURL();
$AdminEmail = $Settings->GetAdminEmailID();
$BusinessID = $Settings->GetBusinessEmailID();

require_once $DocumentRoot . 'app-code/common.code.php';
//require_once $DocumentRoot . 'component/head/head.php';
//require_once $DocumentRoot . 'component/header/header.ui.php';
//require_once $DocumentRoot . 'component/footer/footer.ui.php';

?>