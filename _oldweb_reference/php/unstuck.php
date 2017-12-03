<?PHP 

require("config.php");

function unstPrintContent($acc){
    $sanitizer = new sanitizer(true);
    $acc = $sanitizer->sanitizeSQL($sanitizer->validateLengths($acc));
    if ($acc) {
        if (isset($_GET["char"]) && isset($_GET["acc"]) && $acc == $_GET["acc"]) {
            $charget = $sanitizer->sanitizeSQL($sanitizer->validateLengths($_GET["char"]));
            $accget = $sanitizer->sanitizeSQL($sanitizer->validateLengths($_GET["acc"]));
            if (unstuckfunc($charget, $accget)) {
                echo "<span id='success'>Character '" . $charget . "' was succesfully moved! </span><br />";
            } else {
                echo "<span id='warning'>Character not moved. Is your Character offline?</span><br />";
            }
        }
        $chars = getChars($acc);
        foreach ($chars as $char) {
            printform($char, $acc);
        }
    } else {
        echo "<span id='warning'>Meddling with login detected!</span><br />";
    }
}

  
function printform($char, $acc){ 
   echo "<div id='obalec'><b>". $char ."</b><br /><form name='unstuckform' method='get' action='index.php'>              
              <input type='hidden' name='char' value='". $char ."'>
              <input type='hidden' name='acc' value='". $acc ."'>
              <input type='hidden' name='navi' value='unst'><br />
              <input type='submit' name='Submit' value='Unstuck character!'>  
          </form></div>";
}

function getChars($acc){
  global $msconnect;
  $query = "SELECT Name FROM Character WHERE AccountID = '". $acc ."'";
  
  $result = odbc_exec($msconnect, $query);
  $chars = array();
  while(odbc_fetch_row($result)){
    $chars[] = odbc_result($result, 1);
  }
  return $chars;
}

function unstuckfunc($name, $acc){
global $msconnect;

 $query = "UPDATE Character SET
            MapNumber = 0, 
            MapPosX = 125, 
            MapPosY = 125            
          FROM Character JOIN MEMB_STAT 
            ON Character.AccountID = MEMB_STAT.memb___id
            COLLATE Latin1_general_CI_AI          
          WHERE (Character.Name = '". $name ."')
          AND (Character.AccountID = '". $acc ."')
          AND (MEMB_STAT.ConnectStat = 0)	
		      ";    
 $result = odbc_exec($msconnect, $query);
  
 if(odbc_num_rows($result)) {
    return true; 
 } else {
    return false;
 }
}    

?>
