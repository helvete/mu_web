<?php
include(dirname(__FILE__) . "\..\\config.php"); 

 $query = "Select AccountID, master.dbo.fn_varbintohexstr(Items) 
            From warehouse JOIN MEMB_STAT 
            ON warehouse.AccountID = MEMB_STAT.memb___id 
            WHERE Money = ". $luckbotzen ."
            AND MEMB_STAT.ConnectStat = 0 
           ";
    
    $result = odbc_exec($msconnect, $query);
    $rows = array();
    
    while(odbc_fetch_row($result)){
        $row = array();
        for($i=1;$i<=odbc_num_fields($result);$i++){
            $row[$i-1] = odbc_result($result,$i);                 
        }
        $rows[] = $row;
    }
    
    foreach($rows as $row){
        
        $huhu = explode("0x", $row[1]);     //odebrat 0x ze zacatku
        $items = str_split($huhu[1], 20);   //roztrhat jednotlive itemy
        $firstitem = array_shift($items);   //vybrat si prvni item (vlevo nahore v bedne)
        
        if($firstitem != "ffffffffffffffffffff"){             //pokud prvni item neni prazdne policko
          if($neware = bolbeast($items, $bolbeasthunger)){                    //a pokud je dost ren (tak je sezer)
            if($newitem = addLuck($firstitem)){             //a vyexcell item
              $newvault = $newitem . implode("", $neware);    //seradit novou podobu inventare (exc item, min ren)
            
              $updatequery = "
                UPDATE warehouse
                SET Items = master.dbo.udf_HexStrToVarBin('". $newvault ."'), 
                Money = 0
                WHERE AccountID = '". $row[0] ."'            
              ";
              
              $statisticsquery = "
                UPDATE AccountStatistics
                SET BolBeastFeed = BolBeastFeed + $bolbeasthunger
                WHERE AccountID = '$row[0]'";
            
              $result2 = odbc_exec($msconnect, $updatequery);
              $resultstatistics = odbc_exec($msconnect, $statisticsquery);
              if(!$result2 || !$resultstatistics){
                file_put_contents(dirname(__FILE__) . "/errorlog.txt", "db problem s itemem/statistikou: " . $newitem . " na uctu: " . $row[0] . PHP_EOL, FILE_APPEND);
              } else {
                file_put_contents(dirname(__FILE__) . "/luckulog.txt", "luckovanej item: " . $newitem . PHP_EOL, FILE_APPEND);
              }
            
            } else {
              file_put_contents(dirname(__FILE__) . "/lucklog.txt", "nelze pridat luck: " . $firstitem . PHP_EOL, FILE_APPEND);
            }          
          } else {
            file_put_contents(dirname(__FILE__) . "/lucklog.txt", "nedostatek bol mel acc: " . $row[0] . PHP_EOL, FILE_APPEND);
          }
        } else {
          file_put_contents(dirname(__FILE__) . "/lucklog.txt", "prazdne policko mel acc: " . $row[0] . PHP_EOL, FILE_APPEND);
        }
    }
    
    
    
    function addLuck($item){
       require (dirname(__FILE__) . '/../php/item.php');

      $iteminstance = new item($item);
      $result = $iteminstance->addItemLuck();
      if(!$result){
        return false;
      }
      return $iteminstance->returnHex(); 
    }
    
    function bolbeast(array $items, $beasthunger){
      for($i=0; $i< count($items); $i++){
        if(substr($items[$i], 0, 2) == "cb" && substr($items[$i], 14, 2) == "80"){         //bol
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