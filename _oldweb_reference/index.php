<?php

ob_start();
 
$time = -microtime(true);

ini_set('date.timezone', 'Europe/Prague');

ini_set('session.cookie_httponly', true);
//ini_set('session.cookie_secure', true);            //https priprava
@session_start();
session_regenerate_id();
include (dirname(__FILE__) . "\\php\\sanitizer.php");
include (dirname(__FILE__) . "\\php\\cisla-pras.php");

if(!empty($_POST["name"]) && !empty($_POST["pass"]) && empty($_GET["navi"])){
  sleep(1);
  $resID = validateCredenc($_POST["name"], $_POST["pass"]);
  if($resID != false){
    $_SESSION['user'] = $resID['login'];
    $_SESSION['ctl'] = $resID['ctl'];
  }
}
if(!empty($_GET["logout"])){
  unset($_SESSION['user']);
	session_destroy();
}

if(!empty($_GET["navi"]) && $_GET["navi"] == "regi"){
  include dirname(__FILE__) . "\\php\\registration.php";
  sentRegiForm();
}
if(!empty($_GET["navi"]) && $_GET["navi"] == "poin"){
  include dirname(__FILE__) . "\\php\\distribute-points.php";
  execute($statsname, $msconnect);
}

if(!isset($_GET["navi"])){
  $_GET["navi"] = "home";
} 
include dirname(__FILE__) . "\\html\\header.php";
include dirname(__FILE__) . "\\html\\menu.php";



generateContent($_GET["navi"]);

echo "</body>";

include dirname(__FILE__) . "\\html\\footer.php";


function generateContent($navi){
    global $msconnect;
    require "config.php";
echo "<div id='content'>";
  switch($navi){
    case "regi":
      regiPrintContent();
      break;
    case "topl":
      include dirname(__FILE__) . "\\php\\toplist.php";
      break;
    case "onli":
      include dirname(__FILE__) . "\\php\\onlinelist.php";
      break;
    case "poin":
      poinPrintContent($_SESSION['user']);     //acc!!  
      break;
    case "unst":
      include dirname(__FILE__) . "\\php\\unstuck.php";
      unstPrintContent($_SESSION['user']);     //acc!  
      break;
    case "down":
      include dirname(__FILE__) . "\\html\\download.htm";  
      break;
    case "acci":
      include dirname(__FILE__) . "\\php\\accountInfo.php";
      acciPrintContent($_SESSION['user']);
      break;
    case "pach":
      include dirname(__FILE__) . "\\php\\password-changing.php";
      break;
    case "stat":
      include dirname(__FILE__) . "\\php\\Statistics.php";
      $stats = new Statistics($msconnect, array("limit"=> 10));
      $stats->printStatContent();
      break;
    case "gama":
      include dirname(__FILE__) . "\\php\\GameMaster.php";
      include dirname(__FILE__) . "\\php\\WebStorage.php";
      include dirname(__FILE__) . "\\php\\GameWarehouseHelper.php";
      try{
          $gama = new GameMaster($msconnect, $_SESSION['ctl']);
          if(isset($_POST['rena']) && isset($_POST['bol']) && isset($_POST['joc'])){
              $gama->inspectChangesAndProceed(array("rena"=>$_POST['rena'], "bol"=>$_POST['bol'], "joc"=>$_POST['joc']));           
          } elseif (isset($_POST['authorization'])){
              $gama->authorizeAndProceed($_POST['authorization'], $_POST['char'], $_POST['code']);
          }
          $gama->printGamaContent();
      } catch (Exception $e){
          include dirname(__FILE__) . "\\html\\home.php"; 
      }
      break;
    case "gldi":
      include dirname(__FILE__) . "\\php\\GuildInfo.php";
      $gldi = new GuildInfo($msconnect);
      $gldi->printGldiContent();
      break;
    
    case "stor":
      include dirname(__FILE__) . "\\php\\WebStorage.php";
      include dirname(__FILE__) . "\\php\\GameWarehouseHelper.php";
      include dirname(__FILE__) . "\\php\\item.php";
      $stor = new WebStorage($msconnect, $_SESSION['user']);
      try {
          $stor->printStorage();
      } catch(Exception $e){
          echo "<span id='warning'>".$e->getMessage()."</span>";
      }      
      break;      
    case "home":
      
    default:
      require_once (dirname(__FILE__) . "\\php\\CommonServerParser.php");
      $parser = new CommonServerParser($commonserver_path);
      $parser->parseDataIntoProperties();
      include dirname(__FILE__) . "\\html\\home.php";
  }  
echo "</div>"; 
}

function validateCredenc($name, $pass){
    require "config.php";
    $sanitizer = new sanitizer(false);
    $name = $sanitizer->sanitizeSQL($sanitizer->validateLengths($name));
    //todo zkontrolovat query
    $query = "SELECT mi.memb__pwd, MAX(c.CtlCode) AS ctl
              FROM MEMB_INFO mi INNER JOIN
              Character c ON mi.memb___id = c.AccountID 
              COLLATE Latin1_general_CI_AI
              WHERE (mi.memb___id = '". $name ."')
              GROUP BY mi.memb__pwd";
    $result = odbc_exec($msconnect, $query);
    $ctl = 0;
    while(odbc_fetch_row($result)){
      $row = odbc_result($result, 1);
      $ctl = odbc_result($result, 2);      
    }
        
    if($row) {
      $pwd = $row;
    } else {
      return false;
    }
    
    $challenge = "pepper";
    $pwfromdb = hash_hmac("md5", $pwd, $challenge, false);
    //var_dump($pwfromdb . "<-|->" . $pass);
    //exit;
    if($pwfromdb == $pass) {
        return array("login"=>$name, "ctl"=>$ctl);
    } else {
        return false;
    }
}

ob_end_flush();

?>