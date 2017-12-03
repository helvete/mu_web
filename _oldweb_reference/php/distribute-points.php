<?php

include ("config.php");

$statsname = array("LevelUpPoint", "Strength", "Dexterity", "Vitality", "Energy");

function execute($statsname, $msconnect){
  if(isset($_GET["points"]) && isset($_GET["stat"]) && isset($_GET["char"]) && strlen($_GET["stat"]) > 0){
      $sanitizer = new sanitizer(true);
      $points = $sanitizer->sanitizeSQL($sanitizer->numerize($_GET["points"]));
      $stat = $sanitizer->valueFromList($_GET["stat"], $statsname);
      $char = $sanitizer->sanitizeSQL($sanitizer->validateLengths($_GET["char"]));

    if($points && $stat && $char){

    if(isThereIt($char, $points, $msconnect) > 0){
      if(distribute($points, $stat, $char, $msconnect)){
        $notice = "<span id='success'>Character: '" . $char . "': added " . $points . " points to " . $stat . "</span>" . PHP_EOL;
      }
      else {
        file_put_contents("errorlog.txt", "body nepridany (db chyba, nebo online), char: " . $char . " pocet bodu: " . $points . " atribut: " . $stat . "</span>" . PHP_EOL, FILE_APPEND);
        $notice = "<span id='warning'>Character: '" . $char ."': Points were not added. Is your Character offline?</span>"; 
      }
    }
    else {
      $notice = "<span id='warning'>Character: '" . $char ."': Not enough points to distribute." . "</span>" . PHP_EOL;
    }
    }
    else {
      $notice = "<span id='warning'>Incorrect characters, accepting only positive integer numbers!" . "</span>" . PHP_EOL;
    }
    $notice = "notice=" . $notice;
    header("Location: index.php?navi=poin&$notice");
  }
}

function poinPrintContent($acc){
  global $statsname;
  global $msconnect;
  if(isset($_GET["notice"])){
      echo stripslashes($_GET["notice"]);
    }
    $stats = getCharStat($acc, $statsname, $msconnect);
    foreach($stats as $char => $stat){
      showform($char, $stat);
    }
}


function distribute($points, $stat, $char, $msconnect){
  $query = "UPDATE Character
              SET ". $stat ." = ". $stat ." + ". $points .",
              LevelUpPoint = LevelUpPoint - ". $points ."
            FROM Character JOIN MEMB_STAT 
              ON Character.AccountID = MEMB_STAT.memb___id
              COLLATE Latin1_general_CI_AI
            WHERE Name = '". $char ."'
              AND MEMB_STAT.ConnectStat = 0";
  $result = odbc_exec($msconnect, $query);  
  if($result != false && odbc_num_rows($result) >0){
      return true;
  }
  return false;
}

function isThereIt($char, $points, $msconnect){
  $query = "SELECT count(*) FROM Character
            WHERE Name = '". $char ."'
            AND LevelUpPoint >= ". $points ."
            ";

  $result = odbc_exec($msconnect, $query);
  
  while(odbc_fetch_row($result)){
    for($i=1;$i<=odbc_num_fields($result);$i++){
      $res = odbc_result($result,$i);      
    }
  }
  return $res;
}


  function showform($char, $stats){
    echo "<table cellpadding='5' border='1' class='pointstable'>
          <caption><h2>". $char ."</h2></caption>
          <tr bgcolor='#ff996e'>
          <th>Strength: </th>
          <th>Agility: </th>
          <th>Vitality: </th>
          <th>Energy: </th>
          <th>LevelUp points: </th></tr>
          <tr><td>". $stats['Strength'] ."</td>
          <td>". $stats['Dexterity'] ."</td>
          <td>". $stats['Vitality'] ."</td>
          <td>". $stats['Energy'] ."</td>
          <td>". $stats['LevelUpPoint'] ."</td></tr>
          </table>    
    ";  
    echo "<div id='obalec2'><b>". $char ."</b><br /><form name='charstat' method='get' action='index.php'>
              <label for='points'>Add: </label>
              <input name='points' type='text' id='points' maxlength='10' size='10'>
              <label for='stat'>points to Attribute: </label>
              <select name='stat' size='1'>
                <option value=''>Select Stat</option>
                <option value='Strength'>Strength</option>
                <option value='Dexterity'>Agility</option>
                <option value='Vitality'>Vitality</option>
                <option value='Energy'>Energy</option>
              </select>
              <input type='hidden' name='char' value='". $char ."'>
              <input type='hidden' name='navi' value='poin'>
              <input type='submit' name='Submit' value='Add points!'>  
          </form></div>";
  
  }
  
  function getCharStat($acc, $statsname, $msconnect){
    $query = "SELECT Name, LevelUpPoint, Strength, Dexterity, Vitality, Energy
              FROM Character
              WHERE AccountID = '". $acc ."'";
    
    $result = odbc_exec($msconnect, $query);
    $stats = array();
    while(odbc_fetch_row($result)){
      $name = odbc_result($result, 1);
      for($i=2;$i<=odbc_num_fields($result);$i++){
        $stats[$name][$statsname[$i-2]] = odbc_result($result,$i);      
      }
    }
    return $stats;    
  }


?>