<?php


class GameMaster {

    protected $msconnect;
    protected $lvl;
    protected $beasts;
    protected $hungers;
    public $notices = array();

    public function __construct($msconnect, $lvl){
        
        if($lvl < 8){
            $this->notices[] = "Can't be here without GM account!";
            $this->printNotices();
            throw new Exception("Can't be here without GM account!");
        }
        $this->msconnect = $msconnect;
        $this->lvl = $lvl;
        $this->beasts = array("rena"=>"renabeasthunger", "bol"=>"bolbeasthunger", "joc"=>"jocbeasthunger");
        require("config.php");
        
        $this->hungers = array(
            "rena"=>$renabeasthunger,
            "bol"=>$bolbeasthunger,
            "joc"=>$jocbeasthunger,
            );   
    }
    
    
    public function printGamaContent(){
        $this->printInfoBlocks();
        
        $stor = new WebStorage($this->msconnect, null);
        $catalog = $stor->getCatalogItems();
        $this->printItemCatalog($catalog);
    }


    protected function setHungerOfBeast($beast, $hunger){
        if(array_key_exists($beast, $this->beasts)){
            $line = "\$" . $this->beasts[$beast] . " = " . $hunger . ";\n";
            $this->rewriteFile($line, $this->beasts[$beast]);
            $this->hungers[$beast] = $hunger;
        } else {
            return "This Beast doesn't exist!";
        }
        return true;    
    }
    
    
    protected function rewriteFile($lineToWrite, $changedVar){
        $reading = fopen('config.php', 'r');
        $writing = fopen('config.tmp', 'wb');

        $replaced = false;

        while (!feof($reading)) {
          $line = fgets($reading);
          if (stristr($line, $changedVar)) {
            $line = $lineToWrite;
            $replaced = true;
          }
          fputs($writing, $line);
        }
        fclose($reading); fclose($writing);
        if ($replaced) {
          unlink('config.php');
          rename('config.tmp', 'config.php');          
        } else {
          unlink('config.tmp');
        }    
    }
    
    protected function printInfoblocks(){
        echo "<div class='infoblock'>
            <h2>Beast Hungers</h2>
            <hr>
            <form action='' method='POST'>";   
        foreach($this->beasts as $key=>$val){
            echo "<b><input type='number' name='{$key}' value='". $this->hungers[$key] ."' /></b>
                <label for='{$key}'>{$key}</label><br />";
            
        }            
            
        echo "<br /><center><input type='submit' value='Change Hunger' /></center>
            </form>
            </div>
            ";
        //demence ctlcode
        echo "<div class='infoblock'><h2>Change CtlCode</h2><hr><form action='' method='POST'>
              <b><input type='text' name='char' id='char' size='12' /></b><label for='char'>Character</label><br />
              <b><input type='number' name='code' id='code' /></b><label for='code'>CtlCode</label><br />
              <b><input type='password' name='authorization' id='authorization' size='12' /></b><label for='auth'>Authorization</label><br />
              <br /><center><input type='submit' value='change CtlCode' /></center></form><br />
              </div>
              <div class='infoblock'><h2>CtlCode info</h2><hr>
              <b>0</b><span>normal player:</span><br />
              <b>1</b><span>baned player:</span><br />
              <b>8</b><span>game master:</span><br />
              <b>12</b><span>invisible GM:</span><br />
              <b>24</b><span>level 2 GM:</span><br /></div>";            
    }
    
    public function inspectChangesAndProceed(array $newHungers){
        foreach($this->beasts as $key => $val){
            if($this->hungers[$key] != $newHungers[$key]){
                $result = $this->setHungerOfBeast($key, $newHungers[$key]);
                if ($result === true){
                    $this->notices[] = "Hunger of " . $key . " successfully set!";
                } else {
                    $this->notices[] = $result;
                }
            }
        }
    $this->printNotices(); 
    }
    
    protected function printNotices(){
      if($this->notices){
        foreach($this->notices as $notice){
            echo "<span id='restricted'>". $notice ."</span><br />";
        }
      }
    }
    
    public function authorizeAndProceed($auth, $char, $level){
        if($auth == "klasickedeltakrupky"){
            $this->changeCTL($char, $level);
        } else {
            $this->notices[] = "Wrong authorization, you can't change CtlCode";
        }
        $this->printNotices();
    }
    
    protected function changeCTL($char, $level){
        $sql = "UPDATE Character SET CtlCode = ". $level ." WHERE Name = '". $char ."'";
        $result = odbc_exec($this->msconnect, $sql);
        if($result){
            $this->notices[] = "CtlCode of " . $char . " successfully set to " . $level;
        } else {
            $this->notices[] = "Something went wrong";
        }        
    }
    
    protected function printItemCatalog($catalog){
        $haveImage = 0;
        $allItems = 0;
        $DS = DIRECTORY_SEPARATOR;
        echo '<br /><br /><h2 class="default" style="margin-bottom: -20px;">Item skins coverage</h3>';
        echo "<br /><br /><table><tr><th>name</th><th>code</th><th>excFlag</th><th>x</th><th>y</th><th>ma obrazek</th>";
        foreach($catalog as $itemCode => $sub){
          foreach($sub as $speslExc => $item){
              $file = dirname(__DIR__).$DS.'pics'.$DS.'items'.$DS.$item['item_code'].($item['exc_flag'] ? 80 : 0).'.png';
              $imageExists = file_exists($file);
              echo "<tr" . ($imageExists ? ' class="highlight-havefile"': '') . "><td>".$item['name']."</td><td>".$item['item_code']."</td><td>".$item['exc_flag']."</td><td>".$item['x']."</td><td>".$item['y']."</td><td>".$imageExists."</td></tr>";
              if($imageExists){
                  $haveImage++;
              }
              $allItems++;
          }        
        }
        echo "</table><br />";
        echo "allItems:".$allItems."<br />";
        echo "haveImage:".$haveImage."<br />";
        $gotPerc = round($haveImage/$allItems, 4) * 100; 
        echo 'percentage: [';
        for ($i = 0; $i < 100; $i++) {
          if ($i < $gotPerc) {
            echo '<span class="success-span">█</span>';
            continue;
          }
          echo '<span>█</span>';
        }
        echo '] <span class="success-span"' . "> {$gotPerc}%</span><br />";        
    }
}

?>