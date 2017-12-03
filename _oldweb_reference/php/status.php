<?php

include ("config.php");

if(serveron()){
  echo "Server status: <div class='status-ball green' title='Online'>".numonline($msconnect)."</div>";
} else {
  echo "Server status: <div class='status-ball red' title='Offline'>&nbsp;</div>";
}

echo '<br />Time: <span id="server-time" data-time="'.time().'">'.date('H:i:s').'</span>';




  function serveron(){     //nastaveni ip a portu dat do configu

  $ts_ip = "127.0.0.1";
  $ts_port = "44405";
  if ($fp=@fsockopen($ts_ip,$ts_port,$ERROR_NO,$ERROR_STR, (float)0.5)) { 
	   fclose($fp); 
	   return true; 
	} else { 
	   return false; 
	}
  }
  
  
  function numonline($msconnect){
  
  $sql = odbc_exec($msconnect, "SELECT count(*) FROM MEMB_STAT WHERE ConnectStat = 1");
  while(odbc_fetch_row($sql)){
    for($i=1;$i<=odbc_num_fields($sql);$i++){
      return odbc_result($sql,$i);
    }
  } 
  }
  
  /*function numitem($msconnect){
  
  $sql = odbc_exec($msconnect, "SELECT ItemCount FROM GameServerInfo");
  while(odbc_fetch_row($sql)){
    for($i=1;$i<=odbc_num_fields($sql);$i++){
      return odbc_result($sql,$i);
    }
  }  
  }*/
  
  
  
  
  
?>

<script type="text/javascript">

  function incrementTime(){
      
      var serverTime = $('#server-time');
      var newDate = serverTime.data('time')+1;
      serverTime.data('time', newDate);
      
      serverTime.text(moment(newDate, 'X').format('HH:mm:ss'));
  }

  $(document).ready(function(){
    setInterval(incrementTime, 1000);
  });

</script>