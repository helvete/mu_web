<?php


class item{

  private $itemCode = 0;
  private $itemLevelLuckSkillOpt = 0;
  private $itemDur = 0;
  private $itemSerial = 0;
  private $itemExcopt = 0;
  private $itemAnc = 0;
  
  public $name;
  public $dimensionX = 1;
  public $dimensionY = 1;
  
  private $codeWhole;

  public function __construct($param){
    if($param == null || strlen($param) != 20){
      throw new Exception('Invalid item hex code provided');
    }
    
    $this->chunkCode($param);
    $this->codeWhole = $param;   
  }

  protected function chunkCode($code){
    $chunky = str_split($code, 2);
    $this->itemCode = $chunky[0];
    $this->itemLevelLuckSkillOpt = $chunky[1];
    $this->itemDur = $chunky[2];
    $this->itemSerial = $chunky[3] . $chunky[4] . $chunky[5] . $chunky[6];
    $this->itemExcopt = $chunky[7];
    $this->itemAnc = $chunky[8] . $chunky[9];
  }                       

  
  public function addExcellOpt(){
    if(!$this->isExcellable()){
      return false;
    }
    $temporalItemExcopt = hexdec($this->itemExcopt);
    $tempCutted = 0;
    
    // store item distinguisher
    if ($temporalItemExcopt >= 128){
      $temporalItemExcopt -= 128;
      $tempCutted += 128;
    }
    // store item +16 option
    if ($temporalItemExcopt >= 64){
      $temporalItemExcopt -= 64;
      $tempCutted += 64;
    }
    // process exc opts only
    $whichToAdd = $this->addRandomExcopt($temporalItemExcopt);
    if($whichToAdd !== false){
      $this->itemExcopt = dechex($temporalItemExcopt + $whichToAdd + $tempCutted);
      // return what has been added
      return $whichToAdd;
    }
    // nothing has been added, already full opt
    return false;
  }
  
  //tunic bizuterie
  public function addItemLevel(){
      $isIt = $this->isItShiny();
      if($isIt){
          $tmpLvl = hexdec($this->itemLevelLuckSkillOpt) + 8;
          $this->itemLevelLuckSkillOpt = dechex($tmpLvl);
          return true;  
      } else {
          return false;
      }  
  }
  
  public function addItemLuck(){
      if($this->isLuckable()){
          $tmpLuck = hexdec($this->itemLevelLuckSkillOpt) + 4;
          $this->itemLevelLuckSkillOpt = dechex($tmpLuck);
          return true;               
      }
      return false;
  }
  
  protected function addRandomExcopt($option){
      $remaining = array(1, 2, 4, 8, 16, 32);
      
      if($option == 63){
        return false;     //FO item   
      }      
      if(hexdec($this->itemExcopt) >= 128 && hexdec($this->itemCode) >= 128 && hexdec($this->itemCode) <= 134){
          //jsou to kridla
          $kridla = true;   
      }      
      if($option >= 32) {
        $tmp = array_keys($remaining, 32); 
        unset($remaining[$tmp[0]]);
        $option -= 32;
      } else if ($kridla){
          $tmp = array_keys($remaining, 32); 
          unset($remaining[$tmp[0]]);
      }
      if($option >= 16) {
        $tmp = array_keys($remaining, 16);
        unset($remaining[$tmp[0]]);
        $option -= 16;
      }
      if($option >= 8) {
        $tmp = array_keys($remaining, 8);
        unset($remaining[$tmp[0]]);
        $option -= 8;
      }
      if($option >= 4) {
        $tmp = array_keys($remaining, 4);
        unset($remaining[$tmp[0]]);
        $option -= 4;
      }
      if($option >= 2) {
        $tmp = array_keys($remaining, 2);
        unset($remaining[$tmp[0]]);
        $option -= 2;
      }
      if($option >= 1) {
        $tmp = array_keys($remaining, 1);
        unset($remaining[$tmp[0]]);
      }
            
      shuffle($remaining);      
      return $remaining[0];  
  }
  
  public function returnHex(){
  
    $def = array(
      'Code' => 2,
      'LevelLuckSkillOpt' => 2,
      'Dur' => 2,
      'Serial' => 8,
      'Excopt' => 2,
      'Anc' => 4,
    );
    $finalHex = '';
    foreach ($def as $name => $zerosCount) {
      $varName = "item$name";
      $value = $this->$varName;
      if (strlen($value) < $zerosCount) {
        $value = str_repeat('0', $zerosCount - strlen($value)) . $value;   
      }
      
      $finalHex .= $value;
    }
    return $finalHex;  
  }
  
  private function isExcellable(){
    
    $Type = str_split($this->itemCode, 1);
    $Exc = hexdec($this->itemExcopt);
    
    switch ($Type[0]){
      case '0': 
      case '1':
      case '2':
      case '4':
      case '6':
        return true;
      case '3':
      case '5':
      case '7':
        if($Exc >= 128){
          return true;
        }
        break;
      case '8':
        if($Exc < 128 || $Type[1] <= '6'){
          return true;
        }
        break; 
      case 'a':
        if($Exc >= 128 && ($Type[1] == 8 || $Type[1] == 9 || $Type[1] == 'c' || $Type[1] == 'd')){
          return true;
        }
      case 'c':
      case '9':
      case 'e';
      case 'f';
        if($Exc < 128){
          return true;
        }
        break;
    }
    return false;  
  }
  
  //je to bizu?
  private function isItShiny(){
      if ($this->itemCode == "ac" || $this->itemCode == "ad" || $this->itemCode == "a8" || $this->itemCode == "a9"){
          if (hexdec($this->itemLevelLuckSkillOpt) <= 67 && hexdec($this->itemExcopt) >= 128) { //8*8 za lvl a 3*1 za opt
              return true;
          }
      }
      return false;
  }
  
  //jde to lucknout?
  private function isLuckable(){
    $Type = str_split($this->itemCode, 1);
    $Exc = hexdec($this->itemExcopt);
    
    if($Exc >= 128){    
      switch ($Type[0]){
        case '0':
        case '1':
        case '2':
        case '3':
        case '4':
        case '5':
        case '6':
        case '7':
          return !$this->hasLuck();
          break;
        case '8':
          if($Type[1] >= 0 && $Type[1] <= 6){
              return !$this->hasLuck();
              break;
          }  
      }
      return false; 
    }
    return !$this->hasLuck();
  }
  
  //ma item luck?
  public function hasLuck(){
      $tmpLuck = hexdec($this->itemLevelLuckSkillOpt);
      if($tmpLuck >= 128){
          $tmpLuck -= 128;
      }
      $tmpLuck = $tmpLuck % 8;
      
      if($tmpLuck >= 4){
          return true;
      }
      return false;  
  }
  
  public function getItemLvl(){
      $tmpIL = hexdec($this->itemLevelLuckSkillOpt);
      if($tmpIL >= 128){
          $tmpIL -= 128;
      }
      return floor($tmpIL / 8);
  }
  
  public function getItemOpt(){
      $tmpIO = hexdec($this->itemLevelLuckSkillOpt);
      $tmpIO2 = hexdec($this->itemExcopt);
      
      if($tmpIO2 >= 128){
          $tmpIO2 -= 128;
      }
      if($tmpIO2 >= 64){
          return 4;
      }
      if($tmpIO >= 128){
          $tmpIO -= 128;
      }
      $tmpIO = $tmpIO % 8;
      if($tmpIO >= 4){
          $tmpIO -= 4;
      }
      return $tmpIO;      
  }
  
  public function getItemLuckSkill(){
      $tmpILS = hexdec($this->itemLevelLuckSkillOpt);
      $hasIt = array("luck"=>0, "skill"=>0);
      
      if($tmpILS >= 128){
          $hasIt['skill'] = 1;
          $tmpILS -= 128;
      }
      $tmpILS = $tmpILS % 8;
      
      if($tmpILS >= 4){
          $hasIt['luck'] = 1;      
      }
      return $hasIt;  
  }
  
  public function getItemSerial(){
      return $this->itemSerial;
  }
  
  public function getItemExcOpts(){ //kody nebo nazvy
      $opts = array();  //vyresit poradi
      if(!$this->isExcellable()){
          return "000000";
      }
      
      $tmpIEO = hexdec($this->itemExcopt);      
      if($tmpIEO >= 128){
          $tmpIEO -= 128;
      }
      if($tmpIEO >= 64){
          $tmpIEO -= 64;
      }
      //ohandlit kridla
      return "zatim nic";
  
  }
  
  public function getItemCode(){
      return $this->itemCode;
  }
  
  public function getItemExcIncreased($hex = false){
  
      $exc = hexdec($this->itemExcopt);
      if($exc >= 128){
        if($hex){
          return 80;
        }
        return 128;
      }
      return 0;  
  }
  
  public function getWholeCode(){
      return $this->codeWhole;
  }
  
  public function getItemName(){
  
  
  }

}
?>