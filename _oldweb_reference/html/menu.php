



<div id="menu">

<?php
$itemsMenu = array("home"=>"Server info", "regi"=>"Account registration", "onli"=>"Online list", "topl"=>"Top list", "down"=>"Downloads", "stat"=>"Statistics", "gldi"=>"Guild list");
$itemsMenuLogged = array("poin"=>"Distribute points", "unst"=>"Unstuck character", "acci"=>"Account Information", "stor"=>"Web Storage");
$itemsMenuGM = array("gama"=>"Game Master");
if(isset($_SESSION["user"])){
  $itemsMenu += $itemsMenuLogged;
  $form = "/logoutform.php";
  if($_SESSION["ctl"] >= 8){
      $itemsMenu += $itemsMenuGM;
  }
} else {
    $form = "/loginform.php";

}


foreach ($itemsMenu as $itemkey=>$itemval) {
  echo "<span "; 
  if(isset($_GET["navi"]) && $_GET["navi"] == $itemkey){
    echo "id='menulight'>";
  } else {
    echo ">";
  }
  echo "<a href='index.php?navi=". $itemkey ."'>". $itemval ."</a></span>";
}

$chall = 'pepper';  //challenge generovat nebo tahat z db
include(dirname(__FILE__) . $form);

?>

</div>