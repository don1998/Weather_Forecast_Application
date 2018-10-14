<?php
class Database{
 
    private $host = "localhost";     //Information to access the database
    private $db_name = "weather_forecast";
    private $username = "root";
    private $password = "";
    public $conn;
 
    public function getConnection(){
 
        $this->conn = null;
 
        try{//Establishing database connection
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
 
        return $this->conn;
    }

    public function getEmployees(){
        $conn = $this->getConnection();//Establishing database connection

        $sql="SELECT * FROM employee";
        $result=$conn->query($sql)->fetchAll();;


        $employeeArray = array();

        if (count($result)  > 0) {
            for($x=0; $x<count($result); $x++) {
            array_push($employeeArray,array($result[$x]["employee_id"],$result[$x]["email"],$result[$x]["city"],$result[$x]["name"],$result[$x]["role"]));//Selecting subset of employee information
            }
            return $employeeArray;
        }
        else {
            echo "0 results";
        }
    }
}