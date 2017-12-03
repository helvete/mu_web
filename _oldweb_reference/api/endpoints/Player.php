<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 2.12.17
 * Time: 17:03
 */

class Player {

    const TABLE_CHARACTER = 'Character';


    private $dbApi;

    public function __construct() {
        $this->dbApi = Factory::getDbApi();
    }


    /**
     * @url GET list
     * @return array
     */
    public function getPlayerList(){
        $players = $this->dbApi->handleGet(Api::DB_MU_ONLINE, Player::TABLE_CHARACTER);
        return $players;
    }


}