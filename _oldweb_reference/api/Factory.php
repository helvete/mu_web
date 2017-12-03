<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 2.12.17
 * Time: 17:41
 */


class Factory {

    public static function getDatabaseConnection() {
        require("..".DIRECTORY_SEPARATOR."config.php");
        return $msconnect;
    }

    public static function getDbApi(){
        $conn = Factory::getDatabaseConnection();
        return new Api($conn);
    }
}