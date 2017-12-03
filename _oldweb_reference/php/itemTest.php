<?php
require ('item.php');

$hnunu = array (
'205cff00000000413824',
);

foreach ($hnunu as $nu){
  $iteminstance = new item($nu);
  echo "<pre>".print_r($iteminstance, 1)."</pre>";exit;
}
 

?>