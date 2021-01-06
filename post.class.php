<?php
require_once("database.php");

class post{
    public $id_creator;
    public $id_kategory;
    public $title;
    public $content;

    function __construct($id_creator, $id_kategory, $title, $content){
        $this->id_creator = $id_creator;
        $this->id_kategory = $id_kategory;
        $this->title = $title;
        $this->content = $content;
}
}
?>