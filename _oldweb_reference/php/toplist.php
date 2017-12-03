<?PHP 
  require('resolveClass.php');
  $sanitizer = new sanitizer(false);

  if(@$_GET["howmany"] != NULL){
      $howmany = $sanitizer->sanitizeSQL($sanitizer->numerize($_GET["howmany"])) ? $sanitizer->sanitizeSQL($_GET["howmany"]) : 10;
  } else {
    $howmany = 10;
  }

  if(@$_GET["orderby"] != NULL){
      $order = $sanitizer->sanitizeSQL($_GET["orderby"]) ? $sanitizer->sanitizeSQL($_GET["orderby"]) : "Reset DESC";
  } else {
    $order = "Reset DESC";
  }
  
  if(@$_GET["class"] !== NULL){
      $class = (int)$_GET["class"];
     // $class = $sanitizer->sanitizeSQL($sanitizer->numerize($_GET["class"])) ? $sanitizer->sanitizeSQL($_GET["class"]) : "";
  } else {
    $class = -1;
  }

  $rows = getdata($howmany, $order, $class);

  echo "<style>";
  switch($order){
    case "Name":
      echo "#ch, #ch a {background-color: black; color: #ff996e}";
      break;
      
    case "cLevel DESC":
      echo "#le, #le a {background-color: black; color: #ff996e}";
      break;      
  
    case "Class":
      echo "#cl, #cl a {background-color: black; color: #ff996e}";
      break;
      
    case "Strength DESC":
      echo "#st, #st a {background-color: black; color: #ff996e}";
      break;
      
    case "Dexterity DESC":
      echo "#ag, #ag a {background-color: black; color:#ff996e}";
      break;
      
    case "Vitality DESC":
      echo "#vi, #vi a {background-color: black; color: #ff996e}";
      break;
      
    case "Energy DESC":
      echo "#en, #en a {background-color: black; color: #ff996e}";
      break;
      
    case "Money DESC":
      echo "#mo, #mo a {background-color: black; color: #ff996e}"; 
      break;
      
    case "PkCount DESC":
      echo "#ki, #ki a {background-color: black; color: #ff996e}";
      break;
      
    case "Reset DESC":
      echo "#re, #re a {background-color: black; color: #ff996e}";
      break;
  }
  echo "</style>";
  

  echo "<table cellpadding='5' border='1'>";
  echo "<tr bgcolor='#ff996e'>
  <th id='ch'><a href='index.php?navi=topl&orderby=Name&howmany=". $howmany ."&class=". $class ."'>Character</a></th>
  <th id='le'><a href='index.php?navi=topl&orderby=cLevel%20DESC&howmany=". $howmany ."&class=". $class ."'>Level</a></th>
  <th id='cl'><a href='index.php?navi=topl&orderby=Class&howmany=". $howmany ."&class=". $class ."'>Class</a></th>
  <th id='st'><a href='index.php?navi=topl&orderby=Strength%20DESC&howmany=". $howmany ."&class=". $class ."'>Strength</a></th>
  <th id='ag'><a href='index.php?navi=topl&orderby=Dexterity%20DESC&howmany=". $howmany ."&class=". $class ."'>Agility</a></th>
  <th id='vi'><a href='index.php?navi=topl&orderby=Vitality%20DESC&howmany=". $howmany ."&class=". $class ."'>Vitality</a></th>
  <th id='en'><a href='index.php?navi=topl&orderby=Energy%20DESC&howmany=". $howmany ."&class=". $class ."'>Energy</a></th>
  <th id='mo'><a href='index.php?navi=topl&orderby=Money%20DESC&howmany=". $howmany ."&class=". $class ."'>Money</a></th>
  <th id='ki'><a href='index.php?navi=topl&orderby=PkCount%20DESC&howmany=". $howmany ."&class=". $class ."'>Kills</a></th>
  <th id='re'><a href='index.php?navi=topl&orderby=Reset%20DESC&howmany=". $howmany ."&class=". $class ."'>Reset</a></th>";

  foreach($rows as $row){
    echo "<tr>";
    for($i = 1; $i<= count($row); $i++){
      if ($i === 8) {
      	$row[$i] = round($row[$i]/1000000, 1) . "M";
      }
      echo "<td>" . $row[$i] . "</td>";    
    }
    echo "</tr>";  
  }
  echo "<tr bgcolor='#ff996e' align='center'><td colspan='10'>
        <form name='vokaz' method='get' action='index.php'>
        <label for='class'>Class:</label>
          <select name='class' size='1'>"; 
if($class == -1){
  echo "<option value='-1' selected='selected'>All";
} else {
  echo "<option value='-1'>All";
}
if($class === 0){      //trojity rovna se bodlo! jinak se "" == 0
  echo "<option value='0' selected='selected'>Dark Wizard";
} else {
  echo "<option value='0'>Dark Wizard";
} 
if($class == 1){
  echo "<option value='1' selected='selected'>Soul Master";
} else {
  echo "<option value='1'>Soul Master";
}
if($class == 16){
  echo "<option value='16' selected='selected'>Dark Knight";
} else {
  echo "<option value='16'>Dark Knight";
}
if($class == 17){
  echo "<option value='17' selected='selected'>Blade Knight";
} else {
  echo "<option value='17'>Blade Knight";
}
if($class == 32){
  echo "<option value='32' selected='selected'>Elf";
} else {
  echo "<option value='32'>Elf";
}
if($class == 33){
  echo "<option value='33' selected='selected'>Muse Elf";
} else {
  echo "<option value='33'>Muse Elf";
}
if($class == 48){
  echo "<option value='48' selected='selected'>Magic Gladiator";
} else {
  echo "<option value='48'>Magic Gladiator";
}        
  echo "</select>        

        <label for='howmany'>Count:</label>
          <select name='howmany' size='1'>"; 
if($howmany == 10){
  echo  "<option value='10' selected='selected'>10";      
} else {
  echo  "<option value='10'>10";        
}          
if($howmany == 50){
  echo  "<option value='50' selected='selected'>50";      
} else {
  echo  "<option value='50'>50";        
}
if($howmany == 100){
  echo  "<option value='100' selected='selected'>100";      
} else {
  echo  "<option value='100'>100";        
}
if($howmany == 200){
  echo  "<option value='200' selected='selected'>200";      
} else {
  echo  "<option value='200'>200";        
}          
    echo"</select>
        <input type='submit' value='Show!'>
        <input type='hidden' name='navi' value='topl'>
        </form>
        </td></tr></table>
  ";
  

  function getdata($howmany, $orderby, $class){

    include("config.php"); //connection!
    if($class !== "" && $class >= 0){
      $class = "AND Class = " . $class;
    } else {
      $class = "";
    }
    
    if($orderby == "Reset DESC"){
        $orderby = $orderby . ", cLevel DESC";
    }     
    $query = "SELECT TOP ". $howmany ." Name, cLevel, Class, Strength, Dexterity, Vitality, Energy, Money, PkCount, Reset
          FROM Character
          WHERE COALESCE(CtlCode, 0) = 0
          ". $class ."
          ORDER BY ". $orderby ."           		
		      ";
    
    //echo $query;
    //exit;
    
    $row = array();
    $rows = array();      
    
    $result = odbc_exec($msconnect, $query);    
    
    while(odbc_fetch_row($result)){
         for($i=1;$i<=odbc_num_fields($result);$i++){
         $row[$i] = odbc_result($result,$i);
         if($row[$i] == NULL){
            $row[$i] = 0;
         }
         if($i == 3){
            $row[$i] = resolveClass($row[$i]);
         }         
    }
    array_push($rows, $row);    
    }
    return $rows;
  }  
?>
