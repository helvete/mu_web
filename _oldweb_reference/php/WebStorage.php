<?php

class WebStorage {
    
    protected $dbConn;
    protected $accountId;
    protected $gameWarehouseHelper;
    
    protected $webStorageContent;
    public $gameStorageItems;
    protected $grid; 
    protected $nulledGrid;
    
    public $catalogItems;
    
    protected $notShowSisky = array(
      "rena",
      "jewel",
    );
    
    protected $itemNameCache = array(0 => array(), 128 => array());

    public function __construct($msconnect, $accountId){
        $this->dbConn = $msconnect;
        $this->accountId = $accountId;
        $this->gameWarehouseHelper = new GameWarehouseHelper();
        
        $this->catalogItems = $this->getCatalogItems();
        $this->gameWarehouseHelper->catalogItems = $this->catalogItems;
        
    }
    
    public function printStorage(){
    
        //tohle chce po zmenach znovu nacist -> proc tim headerem nefunguje?
        $gameStorageContent = $this->getContentOfGameStorage();
        $money = $gameStorageContent['money'];
        $this->gameStorageItems = $gameStorageContent['items'];
        $itemsParsed = $this->parseItems($this->gameStorageItems);
        $this->webStorageContent = $this->getContentOfWebStorage();
        
        $this->grid = $this->gameWarehouseHelper->makeGrid($this->gameStorageItems);
        $this->nulledGrid = $this->gameWarehouseHelper->nullTakenPositions($this->grid);   
    
        //todo -> switch aktualni akce a pak print herni storage a web storage, taky check jestli je uzivatel online (pak neprintit formulare)
        $action = isset($_POST["action"]) ? $_POST["action"] : null;
        if($action){
            switch($action){
                case "to-web":
                    $item = isset($_POST["item"]) ? $_POST["item"] : null;
                    $quantity = isset($_POST["quantity"]) ? (int)$_POST["quantity"] : 1;
                    if(!is_int($quantity) || $quantity <= 0){
                        throw new Exception("kvantita musi byt kladne cislo!");
                    }                    
                    if($item){                        
                        $i = new item($item);
                        $infoItemCatalog = $this->getInfoItemCatalog($i);
                        //zkouknu, jestli tam item je..
                        $itemsToMove = $this->getItemsToMoveFromStorage($item, $itemsParsed, $quantity);
                        
                        //prdnem do web bedny
                        $this->putItemIntoWebStorage($i, $this->accountId, $infoItemCatalog, $quantity, $itemsToMove);
                        //smazem z herni bedny
                        $this->removeItemFromGameStorage($this->gameStorageItems, $this->accountId, $itemsToMove); 
                    }                    
                    break;
                case "to-game":
                
                    $item = isset($_POST["item"]) ? $_POST["item"] : null;
                    if($item){
                        $i = new item($item);
                        $this->putItemIntoGameStorage($i);
                    }
                
                    break;    
            }
            //redirect
            ob_end_clean();
            header('Location:index.php?navi=stor');
        }
        
        $this->gameWarehouseHelper->drawGrid($this->nulledGrid, $itemsParsed);
        $this->showGameItems($itemsParsed);
        $this->showWebItems($this->webStorageContent);        
        
    }
    
    
    
    
    protected function getContentOfWebStorage(){
    
        //todo -> pridat do query excellentni opty z vazebni tabulky
        
        $sql = "SELECT WebWarehouse.dbo.item_storage.id, WebWarehouse.dbo.item_storage.id_owner, 
                      WebWarehouse.dbo.item_storage.id_item_catalog,
                      WebWarehouse.dbo.item_storage.hex_code, WebWarehouse.dbo.item_storage.levelup, 
                      WebWarehouse.dbo.item_storage.optionup, WebWarehouse.dbo.item_storage.has_special_opt, 
                      WebWarehouse.dbo.item_storage.quantity, WebWarehouse.dbo.item_storage.has_luck, 
                      WebWarehouse.dbo.item_storage.has_skill, WebWarehouse.dbo.item_catalogue.name
                FROM WebWarehouse.dbo.item_storage 
                INNER JOIN WebWarehouse.dbo.item_catalogue ON WebWarehouse.dbo.item_storage.id_item_catalog = WebWarehouse.dbo.item_catalogue.id
                WHERE (WebWarehouse.dbo.item_storage.id_owner = '".$this->accountId."')";
        $result = odbc_exec($this->dbConn, $sql);
        
        $items = array();

        while(odbc_fetch_row($result)){
        
            $item = array();
            $item['id'] = odbc_result($result, 1);
            $item['id_owner'] = odbc_result($result, 2);
            $item['id_item_catalog'] = odbc_result($result, 3);
            $item['hex_code'] = odbc_result($result, 4);
            $item['levelup'] = odbc_result($result, 5);
            $item['optionup'] = odbc_result($result, 6);
            $item['has_special_opt'] = odbc_result($result, 7);
            $item['quantity'] = odbc_result($result, 8);
            $item['has_luck'] = odbc_result($result, 9);
            $item['has_skill'] = odbc_result($result, 10);
            $item['name'] = odbc_result($result, 11);

            $items[] = $item;            
        }
        return $items;        
    }

    protected function getContentOfGameStorage(){
    
        $sql = "SELECT master.dbo.fn_varbintohexstr(Items), Money
                FROM warehouse
                WHERE (AccountID = '".$this->accountId."')";
                
        $result = odbc_exec($this->dbConn, $sql);
        
        $row = array();

        while(odbc_fetch_row($result)){
            $row['items'] = odbc_result($result, 1);
            $row['money'] = odbc_result($result, 2);            
        }        
        return $row;    
    }
    
    protected function parseItems($items){
    
        $items = substr($items, 2);
        
        $codes = str_split($items, 20);
        $itemArray = array();
        
        foreach($codes as $code){
            $item = new item($code);
            if(isset($this->catalogItems[$item->getItemCode()][$item->getItemExcIncreased()])) {
              $item->name = $this->catalogItems[$item->getItemCode()][$item->getItemExcIncreased()]['name'];
            }
            $itemArray[] = $item; 
        }
        return $itemArray;
    }
    
    protected function putItemIntoWebStorage($item, $idOwner, $infoItemCatalog, $quantity = 1, $itemsToMove){
        //kdyz je quantifiable tak zkoukni, jestli uz to tam nema
        if($infoItemCatalog['is_quantifiable'] && $this->isItemInWebStorage($infoItemCatalog['id'])){
            $sql = "UPDATE WebWarehouse.dbo.item_storage 
                    SET quantity = quantity + ".$quantity." 
                    WHERE id_owner = '".$idOwner."' AND id_item_catalog = ".$infoItemCatalog['id'];        
        } else {
            $luckSkill = $item->getItemLuckSkill();
            $sql = "INSERT INTO WebWarehouse.dbo.item_storage
                    (id_owner, id_item_catalog, hex_code, levelup, optionup, has_luck, has_skill, quantity)
                    VALUES ('".$idOwner."', ".$infoItemCatalog['id'].", '".$item->getWholeCode()."', ".
                    $item->getItemLvl().", ".$item->getItemOpt().", ".$luckSkill["luck"].
                    ", ".$luckSkill["skill"].", ".$quantity.")";
            //todo spesl opt pro kridla!
        }        
        odbc_exec($this->dbConn, $sql);
        
        if($infoItemCatalog['is_quantifiable'] && count($itemsToMove)){
            $sql = "SELECT id FROM WebWarehouse.dbo.item_storage WHERE id_owner = '".$idOwner."' AND id_item_catalog = ".$infoItemCatalog['id'];
            $result = odbc_exec($this->dbConn, $sql);
            while(odbc_fetch_row($result)){
              $id = odbc_result($result, 1);            
            }
            if($id){
              $sql = "INSERT INTO WebWarehouse.dbo.item_hex_stackable (id_item_storage, hex_code) VALUES (".$id.", '";
              foreach($itemsToMove as $itemToMove){
                $sqlNow = $sql.$itemToMove->getWholeCode()."')";
                odbc_exec($this->dbConn, $sqlNow);
              }              
            }
        }        
    }
    
    //TODO -> $quantity
    protected function putItemIntoGameStorage($item){
        
        $code = $item->getItemCode();
        //zkusime, jestli to nejni stackable item, pokud jo, vezmem si unikatni z tabulky item_hex_stackable a smazem v ni radek
        $code = $this->getUniqueHexCodeForStackableItem($code, true);
        
        $exc = $item->getItemExcIncreased();
    
        if(isset($this->catalogItems[$code][$exc]) && $this->catalogItems[$code][$exc]['x']){
            $a = array('x'=>$this->catalogItems[$code][$exc]['x'], 'y'=>$this->catalogItems[$code][$exc]['y']);
        } else {
            throw new Exception('rozmery predmetu nenalezeny. kod: '.$i->getWholeCode());
            //nenasel jsem shit, doufam ze je 1x1
            $a = array('x'=>1, 'y' => 1);
        }
        
        $coords = $this->gameWarehouseHelper->getInsertableCoordinates($this->nulledGrid, $a);
        
        $newGrid = $this->gameWarehouseHelper->insertItemToCoordinates($this->grid, $item->getWholeCode(), $coords);
        $wh = $this->gameWarehouseHelper->implodeGrid($newGrid);
        
        $updatequery = "
                UPDATE warehouse
                SET Items = master.dbo.udf_HexStrToVarBin('". $wh ."')
                WHERE AccountID = '". $this->accountId ."'";
        
        //nejprve vymazu z web storage a pak ulozim aby se to nezdvojilo pri chybe, hoho
        $this->removeItemFromWebStorage($item);
        odbc_exec($this->dbConn, $updatequery);
        
    }
    
    protected function removeItemFromWebStorage($item){
        
        $selSql = "SELECT id, quantity FROM WebWarehouse.dbo.item_storage 
                  WHERE id_owner ='".$this->accountId."' 
                  AND hex_code = '".$item->getWholeCode()."'";
        
        $result = odbc_exec($this->dbConn, $selSql);
        while(odbc_fetch_row($result)){
            $id = odbc_result($result, 1);
            $quantityStored = odbc_result($result, 2);            
        }
        if($quantityStored == 1){
            $sql = "DELETE FROM WebWarehouse.dbo.item_storage WHERE id = $id";
        } else if($quantityStored > 1){
            $sql = "UPDATE WebWarehouse.dbo.item_storage SET quantity = quantity - 1 
                    WHERE id = ".$id;
        } else {
            throw new Exception('v db nemas ulozenej item: '.$item->getWholeCode(). 'vlastnik: '.$this->accountId);
        }
        odbc_exec($this->dbConn, $sql);
    }
    
    public function removeItemFromGameStorage($storage, $idOwner, $itemsToMove){
        
        foreach($itemsToMove as $itemToMove){
            $storage = preg_replace('~'.$itemToMove->getWholeCode().'~Ui', 'ffffffffffffffffffff', $storage, 1);
        }
        
        $updatequery = "
                UPDATE warehouse
                SET Items = master.dbo.udf_HexStrToVarBin('". $storage ."')
                WHERE AccountID = '". $idOwner ."'            
              ";
        odbc_exec($this->dbConn, $updatequery);      
    
    }
    
    protected function changeOwnerOfItemInWebStorage($item, $owner){
    
    }
    
    protected function showGameItems($items){
        
        echo "<div class='game-storage-holder'>
            <h2>Game Warehouse items</h2>
            <hr>";
        $kamenoReny = array();    
        
        foreach($items as $item){
        
            $code = $item->getItemCode();
            if($code == "ff"){
                continue;
            }
            $excPlus = $item->getItemExcIncreased();
            if(!array_key_exists($code, $this->itemNameCache[$excPlus])){
                $this->itemNameCache[$excPlus][$code] = $this->getItemName($item);
            }
            $name = $this->itemNameCache[$excPlus][$code];            
            
            $neukazujSisky = false;
            foreach($this->notShowSisky as $siska){
                if(strpos(strtolower($name), $siska) !== false){
                    $neukazujSisky = true;
                }
            }
            
            if($neukazujSisky == false){
                echo $name;
                if($item->hasLuck()){
                    echo " +L";
                }
                $luckSkill = $item->getItemLuckSkill();
                if($luckSkill["skill"]){
                    echo " +S";
                } 
                echo " +". $item->getItemLvl() ." +". ($item->getItemOpt()*4) ." exc: ". $item->getItemExcOpts();
                echo $this->getFormString('to-web', $item->getWholeCode(), false);
                echo "<br />";
            } else {
                $kamenoReny[$name]['count'] += 1;
                $kamenoReny[$name]['code'] = $item->getWholeCode();
            }
                        
                    
        }
        
        foreach($kamenoReny as $name => $info){
            echo $name . " ". $info['count'] . "x". $this->getFormString('to-web', $info['code'], true) ."<Br />";
        }    
        
        echo "</div>";    
    }
    
    protected function showWebItems($items){
    
        echo "<div class='game-storage-holder'>
            <h2>Web Warehouse items</h2>
            <hr>";
            
        $kamenoReny = array();    
        
        foreach($items as $item){
            
            $neukazujSisky = false;
            foreach($this->notShowSisky as $siska){
                if(strpos(strtolower($item["name"]), $siska) !== false){
                    $neukazujSisky = true;
                }
            }            
            
            if($neukazujSisky !== false){
                $kamenoReny[$item["name"]]['count'] += $item["quantity"];
                $kamenoReny[$item["name"]]['code'] = $item['hex_code'];
            } else {
              echo $this->getFormString('to-game', $item['hex_code'], false);
              echo " ". $item["name"];
              echo " +". $item["levelup"] ." +". ($item["optionup"]*4);
              if($item["has_luck"]){
                  echo " +L";
              }
              if($item["has_skill"]){
                  echo " +S";
              }
              echo "<br />";
            }            
        }
       
        foreach($kamenoReny as $name => $options){
            echo $this->getFormString('to-game', $options['code'], false);
            echo " ". $name . " ". $options['count'] . "x<Br />"; 
        }
            
        echo "</div>";
    }
    
    protected function getItemName($item){
        
        $code = $item->getItemCode();
        $excInc = $item->getItemExcIncreased();
        
        $sql = "SELECT name
                FROM WebWarehouse.dbo.item_catalogue
                WHERE (item_code = '".$code."') AND (exc_flag = ".$excInc.")";       
                       
        $result = odbc_exec($this->dbConn, $sql);
        $name = "nazev nenalezen";
        while(odbc_fetch_row($result)){
            $name = odbc_result($result, 1);            
        }
        
        if($name == "nazev nenalezen"){
            //var_dump($code);
            //var_dump($excInc);
            //exit;
        }
        
        return $name;
    }
    
    protected function getInfoItemCatalog($item){
    
        $code = $item->getItemCode();
        $excInc = $item->getItemExcIncreased();
        
        $sql = "SELECT id, is_quantifiable
                FROM WebWarehouse.dbo.item_catalogue
                WHERE (item_code = '".$code."') AND (exc_flag = ".$excInc.")";       
                       
        $result = odbc_exec($this->dbConn, $sql);
        while(odbc_fetch_row($result)){
            $id = odbc_result($result, 1);
            $is_quantifiable = odbc_result($result, 2);            
        }
        if(!isset($id)){
            throw new Exception("nenalezeny kod itemu: ".$code." exc: ".$excInc);
        }
        return array('id'=>$id, 'is_quantifiable' => $is_quantifiable);               
    }
    
    protected function getItemsToMoveFromStorage($itemCode, $parsedStorage, $quantity = 1){
        $count = 0;
        $selectedItems = array();
        //stejnej item s libovolnym serial number
        $pattern = '~'.substr($itemCode, 0, 6).'[0-9a-fA-F]{8}'.substr($itemCode, 14, 2).'~Ui';
        
        //vyjimka pro jewel of life -> aby mohl bejt +0/1/2/3
        if (substr($itemCode, 0, 2) == 'd0' && substr($itemCode, 14, 2) == '80') {
            $pattern = '~'.substr($itemCode, 0, 2).'[0-9a-fA-F]{12}'.substr($itemCode, 14, 2).'~Ui';
        }

        foreach($parsedStorage as $storedItem){            
            if(preg_match($pattern, $storedItem->getWholeCode())){
                $count++;
                $selectedItems[] = $storedItem;
            }
            if($count >= $quantity){
                return $selectedItems;
            }        
        }
        throw new Exception('v bedne neni/nejsou itemy k presunuti');        
    }
    
    protected function isItemInWebStorage($idItemCatalogue){
        //todo -> tohle spojit s funkci getItemsToMoveFromStorage, takze obe storage mit jako sadu objektu typu item
        
        foreach($this->webStorageContent as $itemArray){
            if($itemArray['id_item_catalog'] == $idItemCatalogue){
                return true;
            }
        }
        return false;        
    }
    
    public function getCatalogItems(){
        $sql = "SELECT id, name, item_code, exc_flag, opt_flag, level_up, is_quantifiable, dimension_x, dimension_y
                FROM WebWarehouse.dbo.item_catalogue";       
                       
        $result = odbc_exec($this->dbConn, $sql);
        $items = array();
        while(odbc_fetch_row($result)){
            
            $item = array(
                'id' => odbc_result($result, 1),
                'name' => odbc_result($result, 2),
                'item_code' => odbc_result($result, 3),
                'exc_flag' => odbc_result($result, 4),
                'opt_flag' => odbc_result($result, 5),
                'level_up' => odbc_result($result, 6),
                'is_quantifiable' => odbc_result($result, 7),
                'x' => odbc_result($result, 8),
                'y' => odbc_result($result, 9),
            );
            $items[$item['item_code']][$item['exc_flag']] = $item;             
        }
        return $items;    
    }
    
    
    protected function getFormString($type, $itemCode, $quantifiable = false){
        
        if($quantifiable){
            $quant = "<input type='text' name='quantity' value='1' size='2' />";
        } else {
            $quant = "";
        }
        
        if($type == 'to-web'){
            $sipka = "=>";
        } else {
            $sipka = "<=";
        }
        
        $form = "<form method='post' action='' class='one-line'>
        <input type='hidden' name='action' value='".$type."' />
        <input type='hidden' name='item' value='".$itemCode."' />
        ".$quant."
        <input type='submit' name='submit' value='".$sipka."' />
        </form>";
        
        return $form;    
    }
    
    protected function getUniqueHexCodeForStackableItem($itemCode, $deleteIt = false){
        $sql = "SELECT TOP 1 WebWarehouse.dbo.item_hex_stackable.id, WebWarehouse.dbo.item_hex_stackable.hex_code
                FROM WebWarehouse.dbo.item_storage 
                INNER JOIN WebWarehouse.dbo.item_hex_stackable ON WebWarehouse.dbo.item_storage.id = WebWarehouse.dbo.item_hex_stackable.id_item_storage
                WHERE (WebWarehouse.dbo.item_storage.hex_code = '".$itemCode."')";
        $result = odbc_exec($this->dbConn, $sql);
        while(odbc_fetch_row($result)){
            $id = odbc_result($result, 1);
            $hexCode = odbc_result($result, 2);            
        }
        
        if($id && $deleteIt){
            $sql = "DELETE FROM WebWarehouse.dbo.item_hex_stackable WHERE WebWarehouse.dbo.item_hex_stackable.id = ".$id;
            odbc_exec($this->dbConn, $sql);
        }
        return isset($hexCode) ? $hexCode : $itemCode;        
    }
    



}