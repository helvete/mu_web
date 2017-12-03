<?PHP include("config.php"); 
  require('resolveClass.php');



 $query = "
		SELECT DISTINCT mb.memb___id, mb.ConnectTM, ch.Name, ch.cLevel, ch.Class, ch.MapNumber, COALESCE(gm.G_Name, ''), COALESCE(ch.Reset, 0), ch.CtlCode
		FROM MEMB_STAT mb
    INNER JOIN AccountCharacter ac 
			ON mb.memb___id = ac.Id
		INNER JOIN CHARACTER ch 
			ON mb.memb___id = ch.AccountID
        AND ch.Name = ac.GameIDC
    LEFT JOIN GuildMember gm
      ON gm.Name = ch.Name
    WHERE mb.ConnectStat > 0
    ORDER BY ch.CtlCode, ch.Name
		";
    
    $query2= 'SELECT AccountID, Name, LDate, cLevel, Class, MapNumber
        FROM CHARACTER
        WHERE AccountID IN
         (SELECT memb___id FROM MEMB_STAT
          WHERE ConnectStat > 0)
        ORDER BY AccountID DESC, LDate DESC';

    $result = odbc_exec($msconnect, $query);
    $chars = array();
    while(odbc_fetch_row($result)){
      $accId = odbc_result($result,1);
      $chars[$accId]['accLvl'] = odbc_result($result,9);
      for($i=1;$i<=odbc_num_fields($result);$i++){
         //$row1[$i-1] = odbc_result($result,$i);
         $col = odbc_result($result,$i);
         
        if ($i === 1){
          continue;
        }
        
        if ($i === 2){
          $now = new DateTime();
          $nowSec = $now->format('U');
          
          $before = new DateTime($col);
          $beforeSec = $before->format('U');
          $howlong = round((($nowSec - $beforeSec) / 60), 0);
          $chars[$accId][] = floor($howlong /60) . "h " . $howlong % 60 . "m";
        }
        if ($i === 3 || $i === 4 || $i === 7 || $i === 8){
          $chars[$accId][] = $col;
        }
        
        if ($i === 6){
          $chars[$accId][] = getMap($col);
        }
        if ($i === 5){
          $chars[$accId][] = resolveClass($col);
        }
                           
      }
    }
    printTable($chars);



function printTable($chars){

  echo "<table cellpadding='5' border='1'><tr bgcolor='#ff996e'><th>Online Time</th><th>Name</th><th>Level</th><th>Class</th><th>Map</th><th>Guild</th><th>Reset</th></tr>";

  foreach($chars as $row){
    echo $row['accLvl'] > 0 ? "<tr style='background-color: #e0e033'>": "<tr>";
    for($i = 0; $i< count($row); $i++){
      if($i == 7){
        continue;
      }
      echo "<td>" . $row[$i] . "</td>";    
    }
    echo "</tr>";  
  }
  if(count($chars) == 0){
    echo "<tr><td colspan='7' class='centered'>No players online!</td></tr>";
  }
  echo "</table>";
}  
    
function getMap($mapNumber){
  $map = -1;
  if ($mapNumber == 0) { 
  $map = 'Lorencia';
  }
  if ($mapNumber == 1) { 
  $map = 'Dungeon';
  }
  if ($mapNumber == 2) { 
  $map = 'Davias';
  }
  if ($mapNumber == 3) { 
  $map = 'Noria';
  }
  if ($mapNumber == 4) { 
  $map = 'Lost tower';
  }
  if ($mapNumber == 5) { 
  $map = 'Exile';
  }
  if ($mapNumber == 6) { 
  $map = 'Arena';
  }
  if ($mapNumber == 7) { 
  $map = 'Atlans';
  }
  if ($mapNumber == 8) { 
  $map = 'Tarkan';
  }
  if ($mapNumber == 9) { 
  $map = 'Devil Square';
  }
  if ($mapNumber == 10) { 
  $map = 'Icarus';
  }
  if ($mapNumber == 11) { 
  $map = 'Blood castle 1';
  }
  if ($mapNumber == 12) { 
  $map = 'Blood castle 2';
  }
  if ($mapNumber == 13) { 
  $map = 'Blood castle 3';
  }
  if ($mapNumber == 14) { 
  $map = 'Blood castle 4';
  }
  if ($mapNumber == 15) { 
  $map = 'Blood castle 5';
  }
  if ($mapNumber ==16) { 
  $map = 'Blood castle 6';
  }
  return $map;
}
?>
