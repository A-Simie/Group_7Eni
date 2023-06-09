<?php

use JetBrains\PhpStorm\NoReturn;

class DatabaseConnect extends PDO
{
    //constructor
    public function __construct()
    {
        parent::__construct("mysql:host=localhost;dbname=preston_db",
            "root",
            "",
            array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }

    #[NoReturn] public function __destruct()
    {
        die();
    }


}
