<?php
include(dirname(__FILE__) . "\..\\config.php"); 

 $query = "Select AccountID, master.dbo.fn_varbintohexstr(Items) 
            From warehouse JOIN MEMB_STAT 
            ON warehouse.AccountID = MEMB_STAT.memb___id 
            WHERE Money = ". $excellbotzen ."
            AND MEMB_STAT.ConnectStat = 0 
           ";
    
    $result = odbc_exec($msconnect, $query);
    $rows = array();
    $klicpico = 0;

    while(odbc_fetch_row($result)){
         for($i=1;$i<=odbc_num_fields($result);$i++){
            $rows[$klicpico][$i-1] = odbc_result($result,$i);                 
         }
         $klicpico++;
   }
         
      foreach($rows as $row){     
        $huhu = explode("0x", $row[1]);     //odebrat 0x ze zacatku
        $items = str_split($huhu[1], 20);   //roztrhat jednotlive itemy
        $firstitem = array_shift($items);   //vybrat si prvni item (vlevo nahore v bedne)
        
        if($firstitem != "ffffffffffffffffffff"){             //pokud prvni item neni prazdne policko
          if($neware = renabeast($items, $renabeasthunger)){                    //a pokud je dost ren (tak je sezer)
            if($newitem = excellent($firstitem)){             //a vyexcell item
              $newvault = $newitem . implode("", $neware);    //seradit novou podobu inventare (exc item, min ren)
            
              $updatequery = "
                UPDATE warehouse
                SET Items = master.dbo.udf_HexStrToVarBin('". $newvault ."'), 
                Money = 0
                WHERE AccountID = '". $row[0] ."'            
              ";
              
              $statisticsquery = "
                UPDATE AccountStatistics
                SET RenaBeastFeed = RenaBeastFeed + $renabeasthunger
                WHERE AccountID = '$row[0]'";
            
              $result2 = odbc_exec($msconnect, $updatequery);
              $resultstatistics = odbc_exec($msconnect, $statisticsquery);
              if(!$result2 || !$resultstatistics){
                file_put_contents(dirname(__FILE__) . "/errorlog.txt", "db problem s itemem/statistikou: " . $newitem . " na uctu: " . $row[0] . PHP_EOL, FILE_APPEND);
              } else {
                file_put_contents(dirname(__FILE__) . "/excellog.txt", "excellenej item: " . $newitem . PHP_EOL, FILE_APPEND);
              }
            
            } else {
              file_put_contents(dirname(__FILE__) . "/excellog.txt", "fullopt item: " . $firstitem . PHP_EOL, FILE_APPEND);
            }          
          } else {
            file_put_contents(dirname(__FILE__) . "/excellog.txt", "nedostatek ren mel acc: " . $row[0] . PHP_EOL, FILE_APPEND);
          }
        } else {
          file_put_contents(dirname(__FILE__) . "/excellog.txt", "prazdne policko mel acc: " . $row[0] . PHP_EOL, FILE_APPEND);
        }
    }
    
    
    
    function excellent($item){
       require_once (dirname(__FILE__) . '/../php/item.php');
/*+0 : No exe options
+1 : Increases acquisition rate of Zen after Hunting monster +40%
+2 : Defence success rate +10%
+4 : Reflect damage +5%
+8 : Damage decrease +4%
+16 : Increase Max mana +4%
+32 : Increase Max HP +4%
+63 : To all the above listed options
*/
      $iteminstance = new item($item);
      $newopt = $iteminstance->addExcellOpt();
      if(!$newopt){
        return false;
      }
      return $iteminstance->returnHex(); 
    }
    
    function renabeast(array $items, $beasthunger){
      for($i=0; $i< count($items); $i++){
        if(substr($items[$i], 0, 2) == "d5"){
         $items[$i] = "ffffffffffffffffffff";
         $beasthunger -= 1;
        }      
        if($beasthunger == 0){
          return $items;
        }
      }      
      return false;    
    
    }
?>