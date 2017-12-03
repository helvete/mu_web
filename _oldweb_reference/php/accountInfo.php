<?php

require("config.php");
require('resolveClass.php');


function acciPrintContent($acc){
  if(is_null($acc)){
    echo "<span id='warning'>To see information about characters in account, you must log in!</span><br />";
  } else {
    $info = getInfo($acc);    
    $statistics = getStatistics($acc);
    $timeOnline = calculateTime(intval($statistics['TimeOnlineMinutes']));
    
    printStatBox($info['registerTime'], $statistics, $timeOnline);
    foreach($info['chars'] as $value){
      echo printOneChar($value);
    }
    
    $resetSpeed = getResetSpeed($acc);
    printGraphSpeed($resetSpeed);
  }  
}


function printOneChar($array){
  $toReturn = "<div class='infoblock'>
  <b> $array[1] </b><span>Name: </span><br />
  <b> $array[2] </b><span>Level: </span><br />
  <b> ". resolveClass($array[3]) ." </b><span>Class: </span><br />
  <b> $array[4] </b><span>Guild: </span><br />
  <b> $array[5] </b><span>Reset: </span><br /></div>";
  
  return $toReturn;

}

function printStatBox($registerTime, $statistics, $timeOnline){
    echo "<div class='infoblock statblock'><span>Registered: " . $registerTime . "</span></br>
          <b>" . $statistics['RenaBeastFeed'] . "</b><span>Renas fed: </span><br />
          <b>" . $statistics['BolBeastFeed'] . "</b><span>BoLs fed: </span><br />
          <b>" . $statistics['JocBeastFeed'] . "</b><span>JoCs fed: </span><br />
          <strong>Played: </strong><br />";
          
    echo $timeOnline['days'] > 0 ? "<b>" . $timeOnline['days'] . "</b><span>days: </span><br />" : "";
    echo $timeOnline['hours'] > 0 ? "<b>" . $timeOnline['hours'] . "</b><span>hours: </span><br />" : "";
    echo $timeOnline['minutes'] > 0 ? "<b>" . $timeOnline['minutes'] . "</b><span>minutes: </span><br />" : "";
    echo $statistics['TimeOnlineMinutes'] > 0 ? "" : "<b>not a thing</b>";
    echo "</div>";
}

function getInfo($acc){
  global $msconnect;
  
  $sql = "SELECT ch.Name, ch.cLevel, ch.Class, COALESCE(gm.G_Name, ''), COALESCE(ch.Reset, 0), mi.appl_days, mi.mail_addr 
          FROM Character ch 
          LEFT JOIN MEMB_INFO mi
          ON ch.AccountID = mi.memb___id
          COLLATE Latin1_general_CI_AI
          LEFT JOIN GuildMember gm
          ON gm.Name = ch.Name
          WHERE ch.AccountID = '". $acc ."'";


  $result = odbc_exec($msconnect, $sql);
  $chars = array();
  $j = 0;
 
  while(odbc_fetch_row($result)){
      $registerTime = odbc_result($result,6);
      $mail = odbc_result($result, 7);
      for($i=1;$i<=odbc_num_fields($result);$i++){
          if($i == 6){
            continue;
          }
          if($i == 7){
            $j++;
            continue;
          }
          $chars[$j][$i] = odbc_result($result,$i);
      }
  }
  $registerTime = date('d.m.Y', strtotime($registerTime));
  return array("registerTime"=>$registerTime, "chars"=>$chars, "mail"=>$mail);  
}


function getStatistics($acc){
    global $msconnect;
    $toReturn = array();
    $query = "SELECT RenaBeastFeed, BolBeastFeed, 
              JocBeastFeed, TimeOnlineMinutes
              FROM AccountStatistics
              WHERE AccountID = '" . $acc . "'";

    $result = odbc_exec($msconnect, $query);
    while(odbc_fetch_row($result)){
      for($i=1; $i <= odbc_num_fields($result); $i++){
        $toReturn[odbc_field_name($result,$i)] = odbc_result($result,$i);
      }
    }

    if(count($toReturn) > 0){
      return $toReturn;
    }
    return false;
}

function calculateTime($minutes){
    $days = floor($minutes / 1440);
    $hours = floor(($minutes % 1440) / 60);
    $minutes = floor(($minutes % 1440) % 60);

    return array("days"=>$days, "hours"=>$hours, "minutes"=>$minutes);
}

function getResetSpeed($acc){
    global $msconnect;
    $sql = "SELECT CharacterID, ResetTime FROM CharacterReset 
            JOIN Character ON Character.Name = CharacterReset.CharacterID 
            WHERE Character.AccountID = '". $acc ."'
            AND COALESCE(Character.Reset, 0)> 0
            ORDER BY ResetTime";
            
    $result = odbc_exec($msconnect, $sql);
    $resultarray = array();
    while(odbc_fetch_row($result)){
        
        $resultarray[odbc_result($result, 1)][] = odbc_result($result, 2);
    
    }
    return $resultarray;            
}

function printGraphSpeed(array $data){
    echo "<div id='container' class='infoblock graphblock'></div>";    
    ?>
    <script type="text/javascript">
    $(function () {
    Highcharts.setOptions({
        global: {
            useUTC: false
        }
    });
     
    $('#container').highcharts({
        chart: {
            type: 'line',
        },
        title: {
            text: 'Reset Speed',
        },
        yAxis: {
            allowDecimals: false,
            min: 0,
            title: {
                text: 'Resets',
            }    
        },
        xAxis: {
            type: 'datetime',
        },
        series: [
            <?php            
            foreach($data as $char =>$restime){
                echo "{ name: '" . $char . "'," . PHP_EOL
                . "data: [";
                foreach($restime as $key => $time){
                    echo "[" . strtotime($time) . "000,";
                    echo $key+1 . "],";
                }
                echo "] },";
            }            
            ?>
        ]
    });
    });    
    </script>
    <?php
}
