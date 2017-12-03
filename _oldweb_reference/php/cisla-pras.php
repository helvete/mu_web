<?php

function transformNumbers($array, $indexes = array()){

  if(!is_array($array)){
    $conv = iconv('CP1250', 'UTF-8', $array);
    $expl = explode(',', $conv);
    $array = $expl[0];    
  }
  //var_dump(ord($array[5][1]));exit;
  
  foreach($indexes as $i){
    $conv = iconv('CP1250', 'UTF-8', $array[$i]);
    //$repl = str_replace("\u00A0", '', $conv);
    $expl = explode(',', $conv);
    $array[$i] = $expl[0];    
  }
  return $array;
}