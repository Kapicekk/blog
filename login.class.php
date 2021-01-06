<?php
require_once("database.php");

class Login{
    private $connection;
    private $username;
    private $password;
    public function __construct($username, $password){
        $this->username = $username;
        $this->password = $password;
        $this->connection = new DBconnection();
    }

    public function login(){
        $sql = "SELECT users.username, users.password FROM users WHERE username = '$this->username'";
        foreach($this->connection->query($sql) as $row){
            $passwordHash = $row["password"];
            if(password_verify($this->password, $passwordHash)){
                return true;
            }
            
            else{
                return false;
            }
        }
        
    }

    

}
