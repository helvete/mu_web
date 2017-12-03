
<?php require ("config.php"); ?>

<div>
<div class="infoblock"><h2>Basic settings</h2>
<b>97d</b><span>Game version: </span><br />
<b><?php echo $parser->getExperience(); ?>x</b><span>Exp-rate: </span><br />
<b><?php echo $parser->getItemDrop(); ?>%</b><span>Drop-rate: </span><br />
<b>50</b><span>Max players online: </span><br />
<b>6/8</b><span>Points per level: </span><br />
<b><?php echo $parser->getGuildCreateLevel(); ?>lvl</b><span>Guild Create: </span><br />
</div>

<div class="infoblock"><h2>Jewels & BoH</h2>
<b>60%</b><span>Soul success rate: </span><br />
<b>75%</b><span>Soul s.rate +Luck: </span><br />
<b>50%</b><span>Life succes rate: </span><br />
<b><?php echo $parser->getBohDropRate(); ?>%</b><span>BoH drop rate: </span><br />
<b><?php echo $parser->getRenaDropRate(); ?>%</b><span>Rena drop rate: </span><br />
</div>

<div class="infoblock"><h2>Fixes</h2>
<span>255 potions in one slot</span><br />
<span>Added Mace of King</span><br />
<span>Added Storm set</span><br />
<span>Removed AA crossbow</span><br /> 
</div>

<div class="infoblock"><h2>Bots prices</h2>
<b><?php echo $renabeasthunger; ?> rena</b><span>Excell: </span><br />
<b><?php echo $bolbeasthunger; ?> BoL</b><span>Luck: </span><br />
<b><?php echo $jocbeasthunger; ?> JoC</b><span>Jewelry lvl+1: </span><br />
<b><?php echo $excellbotzen; ?> zen</b><span>Excell: </span><br /> 
<b><?php echo $luckbotzen; ?> zen</b><span>Luck: </span><br />
<b><?php echo $bizubotzen; ?> zen</b><span>Jewelry: </span><br />
</div>

<div class="infoblock"><h2>Bots times</h2>
<b id="reset-bot-time" title="xx:00"></b><span>Reset: </span><br />
<b>xx:01</b><span>Excell: </span><br />
<b>xx:10</b><span>Luck: </span><br />
<b>xx:50</b><span>Jewelry: </span><br /> 
</div>

    <div class="infoblock"><h2>GM team</h2>
<?php
    $sql = "SELECT Name FROM Character WHERE CtlCode = 8";
    $result = odbc_exec($msconnect, $sql);
    while(odbc_fetch_row($result)){ ?>
        <b><?php echo odbc_result($result,1); ?> </b><span>*</span><br />
        <?php } ?>

</div>

<div class="infoblock"><h2>User counts</h2>
  <?php
  echo "<b>". numacc($msconnect)
    ."</b>Account count: <br />";

  echo "<b>". numchar($msconnect)
    ."</b>Character count: <br />";

  echo "<b>". numguild($msconnect)
    ."</b>Guild count: <br />";
  ?>  
</div>

</div>

<script type="text/javascript">

  var resetTime = moment(<?php echo (strtotime(date('Y-m-d H:00:00'))+3600)*1000; ?>);
  var serverTime = moment(<?php echo (time())*1000; ?>);

  $('#reset-bot-time').text(serverTime.to(resetTime));
  
</script>

<?php

function numacc($msconnect){
  
  $sql = odbc_exec($msconnect, "SELECT count(*) FROM MEMB_INFO");
  while(odbc_fetch_row($sql)){
    for($i=1;$i<=odbc_num_fields($sql);$i++){
      return odbc_result($sql,$i);
    }
  }
  }
  
  function numchar($msconnect){
  
  $sql = odbc_exec($msconnect, "SELECT count(*) FROM Character");
  while(odbc_fetch_row($sql)){
    for($i=1;$i<=odbc_num_fields($sql);$i++){
      return odbc_result($sql,$i);
    }
  } 
  }
  
  function numguild($msconnect){
  
  $sql = odbc_exec($msconnect, "SELECT count(*) FROM Guild");
  while(odbc_fetch_row($sql)){
    for($i=1;$i<=odbc_num_fields($sql);$i++){
      return odbc_result($sql,$i);
    }
  }  
  }