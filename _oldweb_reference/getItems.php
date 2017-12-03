<?php
include(dirname(__FILE__) . "/config.php");

$query = "Select master.dbo.fn_varbintohexstr(Items) 
            From warehouse  
            WHERE warehouse.AccountID = 'test' 
           ";
    
    $result = odbc_exec($msconnect, $query);
    $row = array();

    while(odbc_fetch_row($result)){
         for($i=1;$i<=odbc_num_fields($result);$i++){
            $row[$i-1] = odbc_result($result,$i);                 
         }
        
        $huhu = explode("0x", $row[0]);     //odebrat 0x ze zacatku
        $items = str_split($huhu[1], 20);   //roztrhat jednotlive itemy
    }
    
    $toPrint = "";
    foreach ($items as $item){
      if($item != "ffffffffffffffffffff"){
          $toPrint .= $item . PHP_EOL;
      }
    }
    file_put_contents(dirname(__FILE__) . "/itemDump.txt", $toPrint);
?>