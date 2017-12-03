<?php

class GameWarehouseHelper {

  const MURMORY = 'items';

  public $catalogItems;

  public function drawGrid($grid, $itemsParsed){    
  	echo "<div style=\"font-family: courier;\">";
  
  	foreach($grid as $rowKey => $row){
  		foreach($row as $colKey => $col){
        $baseImagePath = '/pics/'.self::MURMORY.'/';
  			if($col == "ffffffffffffffffffff"){
  				echo '<div class="item empty"><img src="'.$baseImagePath.'empty.png" class="item-image" title="empty slot" /></div>';
  			} else if($col == "00000000000000000000") {
  				//zabrane?
          echo '<div class="item taken"><img src="'.$baseImagePath.'taken.png" class="item-image" /></div>';
  			} else {
          $item = $itemsParsed[($rowKey*8)+$colKey];
          //$dimensions = $this->getItemDimensions($item);
          echo '<div class="item">';
        
  				$uc = $item->getItemCode().$item->getItemExcIncreased(true);
          $path = $baseImagePath.$uc.'.png';
          if(file_exists(dirname(__DIR__).$path)) {
            echo '<img src="'.$path.'" class="item-image" title="'.$item->name.'" />';
          } else {
            $path = str_replace($uc, 'unknown', $path);
            echo '<img src="'.$path.'" class="item-image" title="'.$item->name.'" />';
          }
          echo '</div>';
  			}
  		}
  		echo "<br />";
  	}
  	echo "<br /></div>";
  }
  
  
  public function makeGrid($warehouse){
    //0x na zacatku
  	$warehouse = substr($warehouse, 2);
    $rows =  str_split($warehouse, (20*8));
  	$grid = array();
  	foreach($rows as $key => $value){
  		$grid[$key] = str_split($value, 20);
  	}
  	return $grid;
  }
  
  public function nullTakenPositions($grid){
  	//todo items instead code?
  	foreach($grid as $rowNum => $row){
  		foreach($row as $colNum => $cell){
  			if($cell != "ffffffffffffffffffff" && $cell != "00000000000000000000"){
  				$dimensions = $this->getItemDimensions($cell);
  
  				for($x = 0; $x < $dimensions['x']; $x++){
  					for($y = 0; $y < $dimensions['y']; $y++){
  						//prvni item nechci smazat
              if($x == 0 && $y == 0){ continue; }
              //vynulujeme zabrane pole
  						$grid[$rowNum+$y][$colNum+$x] = "00000000000000000000";
  					}
  				}
  			}
  		}
  	}
  	return $grid;
  }
  
  
  
  public function getInsertableCoordinates($nulledWarehouseGrid, $dimensions = array('x'=>1, 'y' =>1)){
  
  	foreach($nulledWarehouseGrid as $rowNum => $row) {
  		foreach ($row as $colNum => $cell) {
  			try {
  				if ($cell == "ffffffffffffffffffff") {
  
  					for ($y = 0; $y < $dimensions['x']; $y++) {
  
  						for ($i = 0; $i < $dimensions['y']; $i++) {
  
  							if ($colNum + $y >= 8 || ($row[$colNum + $y] != "ffffffffffffffffffff")) {
  								throw new Exception();
  							}
  							if ($colNum + $i >= 8 || $rowNum + $y >= 15 || $nulledWarehouseGrid[$rowNum + $y][$colNum + $i] != "ffffffffffffffffffff") {
  								throw new Exception();
  							}
  						}
  
  					}
  					return array('x' => $rowNum, 'y' => $colNum);
  				}
  			} catch (Exception $e){}
  		}
  	}
  	throw new Exception('item cannot be placed in warehouse');
  }
  
  public function insertItemToCoordinates($grid, $item, $coordinates){
  	$grid[$coordinates['x']][$coordinates['y']] = $item;
  	return $grid;
  }
  
  public function implodeGrid($grid){
  	$rows = array();
  	foreach($grid as $row){
  		$rows[] = implode('', $row);
  	}
  	$whole = implode('', $rows);
  	return "0x".$whole;
  }
  
  
  
  
  
  public function getItemDimensions($item){
  	
    if($item instanceOf item) {
      $i = $item;
    } else {
      $i = new item($item);
    }
    
    $code = $i->getItemCode();
    $exc = $i->getItemExcIncreased();
    
    if(isset($this->catalogItems[$code][$exc]) && $this->catalogItems[$code][$exc]['x']){
        $a = array('x'=>$this->catalogItems[$code][$exc]['x'], 'y'=>$this->catalogItems[$code][$exc]['y']);
    } else {
        //throw new Exception('rozmery predmetu nenalezeny. kod: '.$i->getWholeCode());
        //nenasel jsem shit, doufam ze je 1x1
        $a = array('x'=>1, 'y' => 1);
    }
    return $a;
  }

}