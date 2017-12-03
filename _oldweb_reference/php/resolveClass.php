<?php
function resolveClass($charNumber){
    $class = -1;
      
    if($charNumber == 0){
      $class = 'DW';
    } 
    if ($charNumber == 1) {
      $class = 'SM';
    } 
    if($charNumber == 16){
      $class = 'DK';
    } 
    if($charNumber == 17){
      $class = 'BK';
    } 
    if($charNumber == 32){
      $class = 'Elf';
    } 
    if($charNumber == 33){
      $class = 'ME';
    } 
    if($charNumber == 48){
      $class = 'MG';
    }
     
    return $class;
}