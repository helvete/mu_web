<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 12.8.15
 * Time: 20:44
 */

class Statistics {

    protected $connect;
    protected $period = 0;  //not supported yet
    protected $vip = 0;     //not supported yet
    protected $limit= 0;
    //...

    public function __construct($msconnect, array $params = array()){
        if(!$msconnect || !is_array($params)){
            throw new UnexpectedValueException('Wrong parameters sent! Need odbc connection, want params as array');
        }
        require("resolveClass.php");
        $this->connect = $msconnect;
        if(array_key_exists('period', $params) && is_numeric($params['period'])){
            $this->period = $params['period'];
        }
        if(array_key_exists('limit', $params) && is_numeric($params['limit'])){
            $this->limit = $params['limit'];
        }
        if(array_key_exists('vip', $params) && $params['period'] == 1){
            $this->vip = true;
        }
   }


    //posilat query bez SELECTU - pokud bude potreba neco jinyho nez select tak udelat dalsi fci
    protected function doThatQuery($sql, $withoutLimit = false){
        if($this->limit && !$withoutLimit){
            $limitouos = "SELECT TOP " . $this->limit . " ";
        } else {
            $limitouos = "SELECT ";
        }
        $result = odbc_exec($this->connect, $limitouos . $sql);
        $toReturn = array();
        $j = 0;
        while(odbc_fetch_row($result)){
            for($i=1;$i<=odbc_num_fields($result);$i++){
                $toReturn[$j][$i -1] = odbc_result($result,$i);
            }
            $j++;
        }
        if(!$toReturn){
            throw new Exception('No data acquired from DB!');
        }
        return $toReturn;
    }


    public function acquireCharacterData(){
        $tempLimit = $this->limit;
        $this->limit = null;
        $sql = "Name, cLevel, Class, COALESCE(Reset, 0) FROM Character WHERE COALESCE(CtlCode, 0) = 0";
        $result = $this->doThatQuery($sql);
        $this->limit = $tempLimit;
        return $result;
    }
    
    public function acquireWealthData(){
        $sql1 = "AccountID, Money FROM Character WHERE COALESCE(CtlCode, 0) = 0";
        $sql2 = "AccountID, Money FROM warehouse";
        $characterMoney = $this->doThatQuery($sql1, 'bez limitu!');
        $warehouseMoney = $this->doThatQuery($sql2, 'bez limitu!');
        
        $totalMoney = array();
        foreach($characterMoney as $money){
            $totalMoney[$money[0]] += $money[1];
        }
        
        foreach($warehouseMoney as $money){
            $totalMoney[$money[0]] += $money[1];
        }
        arsort($totalMoney);
        $sliced  = array_slice($totalMoney, 0, $this->limit);
        return array("data"=>$sliced, "total"=>count($sliced));
    
    }
    
    public function acquireOnlineData(){
        $sql = "AccountID, TimeOnlineMinutes FROM AccountStatistics ORDER BY TimeOnlineMinutes DESC";
        $onliners = $this->doThatQuery($sql);
        $templist = array();
        foreach($onliners as $onliner){
            $templist[$onliner[0]] = $onliner[1] / 60;
        }
        return array("data"=>$templist, "total"=>count($templist));
    }
    
    public function acquireBeastData(){
        $tempLimit = $this->limit;
        $sql = "AccountID, RenaBeastFeed, BolBeastFeed, JocBeastFeed FROM AccountStatistics";
        $result = $this->doThatQuery($sql);
        $this->limit = $tempLimit;
        $tunedList = array();
        foreach($result as $row){
            $tunedList["rena"][$row[0]] = $row[1];
            $tunedList["bol"][$row[0]] = $row[2];
            $tunedList["joc"][$row[0]] = $row[3];
        }
        return $tunedList;
    }    

    public function classDistribution($data = array()){
        $datatemp = array_count_values($data);
        arsort($datatemp);
        $dataToReturn = array();
        $total = 0;
        foreach($datatemp as $key => $value){
            $dataToReturn[resolveClass($key)] = $value;
            $total += $value;
        }
        return array("data"=>$dataToReturn, "total"=>$total);
    }
    
    public function classDistributionReseters($classes, $resets){
        $data = array();
        for($i = 0; $i <= count($resets); $i++){
            if($resets[$i]){
               $data[] = $classes[$i]; 
            }
        }
        $datatemp = array_count_values($data);
        arsort($datatemp);
        $dataToReturn = array();
        $total = 0;
        foreach($datatemp as $key => $value){
            $dataToReturn[resolveClass($key)] = $value;
            $total += $value;
        }
        return array("data"=>$dataToReturn, "total"=>$total);
    }


    public function printStatContent(){        
        $data = $this->acquireCharacterData();
        $classes = array();
        $levels = array();
        $resets = array();

        foreach($data as $dat){
            $classes[] = $dat[2];
            $levels[] = $dat[1];
            $resets[] = $dat[3];
        }   //array characteru
        
        $classDistri = $this->classDistribution($classes);
        $this->drawGraph($classDistri, "Class Distribution");
        
        $classDistriRes = $this->classDistributionReseters($classes, $resets);
        $this->drawGraph($classDistriRes, "Class Distribution Resers");
        
        $wealthData = $this->acquireWealthData();
        $this->drawGraph($wealthData, "Account Wealth", 50);
        
        $onliners = $this->acquireOnlineData();
        $this->drawGraph($onliners, "Hours Online", 50);
        
        $beasts = $this->acquireBeastData();
        arsort($beasts["rena"]);
        arsort($beasts["bol"]);
        arsort($beasts["joc"]);
        $rena["data"] = array_slice($beasts["rena"], 0, $this->limit);
        $rena["total"] = count($rena["data"]);
        $bol["data"] = array_slice($beasts["bol"], 0, $this->limit);
        $bol["total"] = count($bol["data"]);
        $joc["data"] = array_slice($beasts["joc"], 0, $this->limit);
        $joc["total"] = count($joc["data"]);
        
        $this->drawGraph($rena, "Rena Used", 50);
        $this->drawGraph($bol, "Box of Luck Used", 50);
        $this->drawGraph($joc, "Jewel of Creation Used", 50);
        
        $resetSpeed = $this->getResetSpeed();
        $this->drawGraph($resetSpeed, "Hours per Reset", 50, true);        
    }


    public function drawGraph($data, $header, $headerSpace = 25, $reverse = false){
        $total = $data['total'];
        $data = $data['data'];
        echo "<div class='infoblock statisticsblock'><table><caption class='odsazenej'><strong>{$header}</strong></caption>
            <tr><td colspan='2' height='25'><hr></td></tr>";
        $barvar = array("R"=>0, "G"=>0, "B"=>0);
        $barvar["R"] = (reset($data) *15) % 256;
        $barvar["G"] = (reset($data) *28) % 256;
        $barvar["B"] = (reset($data) *79) % 256;
        if(!$reverse){
            $delitelSirky = reset($data);
        } else {
            $delitelSirky = end($data);
        }
        
        foreach($data as $key => $value){
            if($value >= 1000){
                 $visibleValue = ($value / 1000);
                 if($visibleValue > 1000){                      
                     $visibleValue = round($visibleValue / 1000, 2) . "M"; 
                 } else {
                      $visibleValue = round($visibleValue, 2) . "K";
                 }                 
            } else {
              $visibleValue = round($value, 2);
            }
            $sirka = (($value / $delitelSirky)*100)+2;
            
            $barvar = $this->makeTintOfColor($barvar);
            $colorstring = "#" . str_pad(dechex($barvar["R"]), 2, 0) . str_pad(dechex($barvar["G"]), 2, 0) . str_pad(dechex($barvar["B"]), 2, 0); 
	          $barva = substr($colorstring, 0, 7);
            //$barvaRevers = $this->inverseHex($barva);
            echo "<tr><td style='width: {$headerSpace}%; text-align: left;'><strong>$key</strong></td>
            <td><div style='width: {$sirka}%; background-color: {$barva}; font-size: xx-small; line-height: 175%; color: #000000; font-weight: bold; border-radius: 3px;' title='{$visibleValue}'>
            {$visibleValue}</div></td></tr>";
        }
        echo "</table></div>";
    }
    
    
    
    function inverseHex( $color ){
     $color       = TRIM($color);
     $prependHash = FALSE;
 
     IF(STRPOS($color,'#')!==FALSE) {
          $prependHash = TRUE;
          $color       = STR_REPLACE('#',NULL,$color);
     }
 
     SWITCH($len=STRLEN($color)) {
          CASE 3:
               $color=PREG_REPLACE("/(.)(.)(.)/","\\1\\1\\2\\2\\3\\3",$color);
          CASE 6:
               BREAK;
          DEFAULT:
               TRIGGER_ERROR("Invalid hex length ($len). Must be (3) or (6)", E_USER_ERROR);
     }
 
     IF(!PREG_MATCH('/[a-f0-9]{6}/i',$color)) {
          $color = HTMLENTITIES($color);
          TRIGGER_ERROR( "Invalid hex string #$color", E_USER_ERROR );
     }
 
     $r = DECHEX(255-HEXDEC(SUBSTR($color,0,2)));
     $r = (STRLEN($r)>1)?$r:'0'.$r;
     $g = DECHEX(255-HEXDEC(SUBSTR($color,2,2)));
     $g = (STRLEN($g)>1)?$g:'0'.$g;
     $b = DECHEX(255-HEXDEC(SUBSTR($color,4,2)));
     $b = (STRLEN($b)>1)?$b:'0'.$b;
 
     RETURN ($prependHash?'#':NULL).$r.$g.$b;
    }
    
    function makeTintOfColor(array $color, $numOfCycles = 1) {
	     if(count($color) != 3) return false;
	     if($numOfCycles == 0) return $color;
	     for($i = 1; $i <= $numOfCycles; $i++) {
		      $color["R"] = $color["R"] + (0.25 * (255 - $color["R"]));
		      $color["G"] = $color["G"] + (0.25 * (255 - $color["G"]));
		      $color["B"] = $color["B"] + (0.25 * (255 - $color["B"]));
	     }
       return $color;
    }
    
    function getResetSpeed(){
        $tempLim = $this->limit;
        $this->limit = null;
        $sql = "DISTINCT TOP ". $tempLim ." CharacterID, MIN(HoursTaken) AS Time
                FROM CharacterReset
                JOIN Character ON CharacterReset.CharacterID = Character.Name AND COALESCE(Character.CtlCode, 0) = 0 
                GROUP BY CharacterID
                ORDER BY Time";
        $result = $this->doThatQuery($sql);
        $this->limit = $tempLim;
        
        $dataToReturn = array();
        foreach($result as $value){
            $dataToReturn[$value[0]] = $value[1];
        }        
        return array("data"=>$dataToReturn, "total"=>count($dataToReturn));
    }
}