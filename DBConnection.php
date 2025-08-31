<?php
if (session_status() === PHP_SESSION_NONE)
session_start();
date_default_timezone_set('Asia/Karachi');
if(!is_dir(__DIR__.'/db'))
    mkdir(__DIR__.'/db');
if(!defined('db_file')) define('db_file',__DIR__.'/db/voting_db.db');
function my_udf_md5($string) {
    return md5($string);
}

Class DBConnection extends SQLite3{
    protected $db;
    function __construct(){
        $this->open(db_file);
        $this->createFunction('md5', 'my_udf_md5');
        $this->exec("PRAGMA foreign_keys = ON;");

        $this->exec("CREATE TABLE IF NOT EXISTS `admin_list` (
            `admin_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `fullname` INTEGER NOT NULL,
            `username` TEXT NOT NULL,
            `password` TEXT NOT NULL,
            `type` INTEGER NOT NULL Default 1,
            `status` INTEGER NOT NULL Default 1,
            `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )"); 

        //User Comment
        // Type = [ 1 = Administrator, 2 = Cashier]
        // Status = [ 1 = Active, 2 = Inactive]

        $this->exec("CREATE TABLE IF NOT EXISTS `election_list` (
            `election_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `title` TEXT NOT NULL,
            `status` INTEGER NOT NULL Default 0,
            `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )"); 


        $this->exec("CREATE TABLE IF NOT EXISTS `position_list` (
            `position_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `name` TEXT NOT NULL,
            `max` INTEGER NOT NULL Default 1,
            `status` INTEGER NOT NULL Default 0,
            `order_by` INTEGER NOT NULL Default 0,
            `type` INTEGER NOT NULL Default 1,
            `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )"); 

        $this->exec("CREATE TABLE IF NOT EXISTS `region_list` (
            `region_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `name` TEXT NOT NULL,
            `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )"); 
        $this->exec("CREATE TABLE IF NOT EXISTS `province_list` (
            `province_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `region_id` INTEGER NOT NULL,
            `name` TEXT NOT NULL,
            `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`region_id`) REFERENCES `region_list`(`region_id`) ON DELETE CASCADE
        )");
        $this->exec("CREATE TABLE IF NOT EXISTS `district_list` (
            `district_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `province_id` INTEGER NOT NULL,
            `name` TEXT NOT NULL,
            `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`province_id`) REFERENCES `province_list`(`province_id`) ON DELETE CASCADE
        )");
        $this->exec("CREATE TABLE IF NOT EXISTS `city_list` (
            `city_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `district_id` INTEGER NOT NULL,
            `name` TEXT NOT NULL,
            `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`district_id`) REFERENCES `district_list`(`district_id`) ON DELETE CASCADE
        )");

        $this->exec("CREATE TABLE IF NOT EXISTS `candidate_list` (
            `candidate_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `position_id` INTEGER NOT NULL,
            `firstname` TEXT NOT NULL,
            `middlename` TEXT NULL,
            `lastname` TEXT NOT NULL,
            `suffix` TEXT NULL,
            `scope_id` INTEGER NULL,
            `election_id` INTEGER NOT NULL,
            `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`election_id`) REFERENCES `election_list`(`election_id`) ON DELETE CASCADE,
            FOREIGN KEY (`position_id`) REFERENCES `position_list`(`position_id`) ON DELETE CASCADE
        )");

        $this->exec("CREATE TABLE IF NOT EXISTS `voter_list` (
            `voter_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `election_id` INTEGER NOT NULL,
            `firstname` TEXT NOT NULL,
            `middlename` TEXT NULL,
            `lastname` TEXT NOT NULL,
            `username` TEXT NOT NULL,
            `password` TEXT NOT NULL,
            `gender` TEXT NOT NULL,
            `dob` TEXT NOT NULL,
            `contact` TEXT NOT NULL,
            `city_id` INTEGER NOT NULL,
            `status` INTEGER NOT NULL Default 0,
            `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`election_id`) REFERENCES `election_list`(`election_id`) ON DELETE CASCADE,
            FOREIGN KEY (`city_id`) REFERENCES `city_list`(`city_id`) ON DELETE CASCADE
        )");

            $this->exec("CREATE TABLE IF NOT EXISTS `vote_list` (
                `vote_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
                `election_id` INTEGER NOT NULL,
                `voter_id` INTEGER NOT NULL,
                `position_id` INTEGER NOT NULL,
                `candidate_id` INTEGER NOT NULL,
                `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (`election_id`) REFERENCES `election_list`(`election_id`) ON DELETE CASCADE,
                FOREIGN KEY (`voter_id`) REFERENCES `voter_list`(`voter_id`) ON DELETE CASCADE,
                FOREIGN KEY (`position_id`) REFERENCES `position_list`(`position_id`) ON DELETE CASCADE,
                FOREIGN KEY (`candidate_id`) REFERENCES `candidate_list`(`candidate_id`) ON DELETE CASCADE
            )");
        
        // Position Type
        // 1 = National,
        // 2 = Regional,
        // 3 = Provincial,
        // 4 = District,
        // 5 = City/Municipal
       
        // $this->exec("CREATE TRIGGER IF NOT EXISTS updatedTime_prod AFTER UPDATE on `vacancy_list`
        // BEGIN
        //     UPDATE `vacancy_list` SET date_updated = CURRENT_TIMESTAMP where vacancy_id = vacancy_id;
        // END
        // ");
        $this->exec("INSERT or IGNORE INTO `admin_list` VALUES (1,'Administrator','admin',md5('admin123'),1,1, CURRENT_TIMESTAMP)");

        $current_election = $this->query("SELECT * FROM `election_list` where status = 1");
        $result = $current_election->fetchArray();
        if($result){
            foreach($result as $k => $v){
                if(!is_numeric($k))
                $_SESSION['election'][$k] = $v;
            }
        }else{
            if(isset($_SESSION['election'])){
                unset($_SESSION['election']);
            }
        }

    }
    function isMobileDevice(){
        $aMobileUA = array(
            '/iphone/i' => 'iPhone', 
            '/ipod/i' => 'iPod', 
            '/ipad/i' => 'iPad', 
            '/android/i' => 'Android', 
            '/blackberry/i' => 'BlackBerry', 
            '/webos/i' => 'Mobile'
        );
    
        //Return true if Mobile User Agent is detected
        foreach($aMobileUA as $sMobileKey => $sMobileOS){
            if(preg_match($sMobileKey, $_SERVER['HTTP_USER_AGENT'])){
                return true;
            }
        }
        //Otherwise return false..  
        return false;
    }
    function __destruct(){
         $this->close();
    }
}

if (!file_exists(db_file) || filesize(db_file) === 0) {
    echo "Database is corrupted or missing. Recreating...";
    $conn = new DBConnection();
} else {
    // Create the connection normally
    $conn = new DBConnection();
}