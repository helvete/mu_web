<?PHP include(dirname(__FILE__) . "\..\\config.php"); 


  $queryselect = "SELECT Name, AccountID FROM Character
                  JOIN MEMB_STAT 
                    ON Character.AccountID = MEMB_STAT.memb___id
                  COLLATE Latin1_general_CI_AI
                  WHERE (clevel >= 350) 
                    AND (MEMB_STAT.ConnectStat = 0) 
                    AND (COALESCE(Reset, 0) < 50)
                    AND (master.dbo.fn_varbintohexstr(Inventory) COLLATE Latin1_general_CI_AI NOT LIKE '%[a-e, 1-9]%')";



 $query = "UPDATE Character
            SET clevel = 1, 
            Experience = 0, 
            LevelUpPoint = 350 * (COALESCE(Reset, 0) + 1),
            Strength = 20, 
            Dexterity = 20, 
            Vitality = 20, 
            Energy = 20, 
            MapNumber = 0, 
            MapPosX = 130, 
            MapPosY = 130,
            Reset = COALESCE(Reset, 0) + 1
          FROM Character JOIN MEMB_STAT 
            ON Character.AccountID = MEMB_STAT.memb___id
          COLLATE Latin1_general_CI_AI
          WHERE (clevel >= 350) 
            AND (MEMB_STAT.ConnectStat = 0) 
            AND (COALESCE(Reset, 0) < 50)
            AND (master.dbo.fn_varbintohexstr(Inventory) COLLATE Latin1_general_CI_AI NOT LIKE '%[a-e, 1-9]%')		
		      ";
    
    $resultnames = odbc_exec($msconnect, $queryselect);    
          
    $toInsert = array();
    while(odbc_fetch_row($resultnames)){
        $toInsert[] = odbc_result($resultnames,1);
        echo "nalezen char k resetu:" . odbc_result($resultnames,1) . PHP_EOL;
        $queryshit = "SELECT TOP 1 MAX(ResetTime)as rt FROM CharacterReset WHERE CharacterID = '". odbc_result($resultnames,1) ."'";
        $resultshit = odbc_exec($msconnect, $queryshit);
        $resultshitdate = null;
        while(odbc_fetch_row($resultshit)){
            $time = odbc_result($resultshit,1);
        }
        if(!$time){
                $sql = "SELECT appl_days FROM MEMB_INFO WHERE memb___id = '".odbc_result($resultnames,2)."'";
                $result = odbc_exec($msconnect, $sql);
                while(odbc_fetch_row($result)){
                    $time = odbc_result($result, 1);
                }                
        }
        $resultshitdate = (time() - strtotime($time))/ 3600;
        echo "hodin od posledniho resetu:". $resultshitdate . PHP_EOL;
        $queryinsert = "INSERT INTO CharacterReset (CharacterID, ResetTime, HoursTaken) VALUES ('" . odbc_result($resultnames,1) . "', '". date("Y-m-d H:i:s") ."', ". $resultshitdate .")";
        $resultinsert = odbc_exec($msconnect, $queryinsert);
    }
    
    $result = odbc_exec($msconnect, $query);     
    if(!$result){
      file_put_contents("../errorlog.txt", "Characters not reset! DB problem." . PHP_EOL, FILE_APPEND);
    }
?>
