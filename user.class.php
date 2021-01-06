<?php
require_once("database.php");

class user{
    public $username;
    public $mail;
    public $password;

    function __construct($username, $mail, $password){
        $this->username = $username;
        $this->mail = $mail;
        $this->password = $password;
}
}
?>