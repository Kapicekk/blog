<?php
require_once("database.php");

class comment{
    public $id_user;
    public $id_post;
    public $comment;

    public function __construct($id_user, $id_post, $comment){
        $this->id_user = $id_user;
        $this->id_post = $id_post;
        $this->comment = $comment;

    }
}
?>