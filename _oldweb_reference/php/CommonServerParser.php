<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 22.9.15
 * Time: 22:57
 */

class CommonServerParser {

    protected $loadedLines;
    protected $experience;
    protected $guildCreateLevel;
    protected $itemDrop;
    protected $bohDropRate;
    protected $renaDropRate;

    public function __construct($path){
        $file = fopen($path, "r");
        if($file) {
            while (!feof($file)) {
                $line = fgets($file);
                if(strlen($line) > 0 && strpos($line, ";") != 0) {
                    $this->loadedLines[] = $line;
                }
            }
            fclose($file);
        }
    }

    protected function finder($lookForThis){
        $value = "";

        foreach($this->loadedLines as $line){
            $LwithoutComment = explode(";", $line);
            $lineChunks = explode(" ", $LwithoutComment[0]);

            if($lineChunks[0] == $lookForThis){
                return $lineChunks[2];
            }
        }
        return false;
    }

    public function parseDataIntoProperties(){
        $this->experience = $this->finder("AddExperience");
        $this->guildCreateLevel = $this->finder("GuildCreateLevel");
        $this->itemDrop = $this->finder("ItemDropPer");
        $this->bohDropRate = $this->finder("BoxOfGoldDropRate") / 100;
        $this->renaDropRate = $this->finder("EventChipDropRateForBoxOfGold");
    }

    /**
     * @return mixed
     */
    public function getExperience()
    {
        return $this->experience;
    }

    /**
     * @return mixed
     */
    public function getGuildCreateLevel()
    {
        return $this->guildCreateLevel;
    }

    /**
     * @return mixed
     */
    public function getItemDrop()
    {
        return $this->itemDrop;
    }

    /**
     * @return mixed
     */
    public function getBohDropRate()
    {
        return $this->bohDropRate;
    }

    /**
     * @return mixed
     */
    public function getRenaDropRate()
    {
        return $this->renaDropRate;
    }
}