<?php

require ("config.php");
$sanitizer = new sanitizer(true);

$notices = array(
"<span id='success'>Account was created Succesfully! Your account information was sent to your e-mail</span><br />",
"<span id='success'>Account was created Succesfully! Your account information will be sent to your e-mail</span><br />",
"<span id='warning'>Account was not created! Try again later, or contact administrator, please</span><br />",
"<span id='warning'>This account name is taken. Choose another one, please</span><br />",
"<span id='warning'>Some of input characters are not allowed or you entered invalid e-mail. Read info about restricted characters please</span><br />",
"<span id='warning'>Wrong Captcha entered!</span><br />");


function sentRegiForm(){
global $msconnect;

if(isset($_POST["name"]) && isset($_POST["pass"]) && isset($_POST["mail"])){
  if($_POST["captcha"] != $_SESSION["digit"]){
      $getik = "&notice=5";
      header("Location: {$_SERVER['HTTP_REFERER']}$getik");
  } else { 
    $name = $_POST["name"];
    $pass = $_POST["pass"];
    $mail = $_POST["mail"];
    $getik = "";
  
    if(checkvalues(trim($name), trim($pass), trim($mail))){
      if(check($name, $msconnect)){
        if(createacc($name, $pass, $mail, $msconnect)){
          if(sendmail($name, $pass, $mail)){
            $getik = 0;
          } else {
            file_put_contents("errorlog.txt", "neodeslany e-mail: " . $mail . " od uctu: " . $name . " s heslem: " . $pass . PHP_EOL, FILE_APPEND);
            $getik = 1;
          }               
        } else {
          file_put_contents("errorlog.txt", "nezalozeny ucet, mail: " . $mail . " name: " . $name . " passw: " . $pass . PHP_EOL, FILE_APPEND);
          $getik =2;
        }
      } else {
        $getik = 3;
      }
    } else {
      $getik = 4;
    }
    $getik = "&notice=" . $getik;
    header("Location: {$_SERVER['REQUEST_URI']}$getik");
  }
}}

function regiPrintContent(){
  global $notices;
  if(isset($_GET["notice"])){
    echo $notices[$_GET["notice"]];
  }
  showform();
}

  function showform(){
  
    echo "<div id='clearF'></div><span id='restricted'>Login and Password can be 4-10 chars long.<br /> Restricted characters:  Space and ;,\"}{'</span><div id='obalec'>";
    echo "<form name='registrationform' method='post' action='index.php?navi=regi' id='regiform'>
            
              <label for='name'>Account Login:</label>
              <input name='name' type='text' id='name' maxlength='10' size='15'><br />
              <label for='pass'>Account Password:</label>
              <input name='pass' type='password' id='pass' maxlength='10' size='15'><br />
              <label for='mail'>Email Address:</label>
              <input name='mail' type='text' id='mail' size='15'><br /><br />
              <p><img src='./pics/captcha.php' width='120' height='30' border='1' alt='CAPTCHA'></p>
              <p><input type='text' size='6' maxlength='5' name='captcha' value='' title='please type digits from image here'></p>
              <input type='submit' name='Submit' value='Create new Account!'>  
          </form></div>";  
  
  }

  function check($name, $msconnect){    //uz zkontrolovana hodnota name
    $query = "SELECT count(*) 
              FROM MEMB_INFO
              WHERE memb___id = '" . $name . "'";
    $result = odbc_exec($msconnect, $query);
    
    while(odbc_fetch_row($result)){
    for($i=1;$i<=odbc_num_fields($result);$i++){
      if(odbc_result($result,$i) >= 1){
        return false;
      }
      return true;
    }
  }}

  function checkvalues($name, $pass, $mail){
      global $sanitizer;
      $name = $sanitizer->validateLengths($name) ? $sanitizer->sanitizeSQL($name) : false;
      $pass = $sanitizer->validateLengths($pass) ? $sanitizer->sanitizeSQL($pass) : false;
      $mail = $sanitizer->validateMail($mail) ? $sanitizer->sanitizeSQL($mail) : false;

      if ($name && $pass && $mail) {
          return true;
      }
      return false;
  }

  function createacc($name, $pass, $mail, $msconnect){
    $queryidentityset = "SET IDENTITY_INSERT MEMB_INFO ON";
    $querymemb = "SET ANSI_WARNINGS  OFF; INSERT INTO MEMB_INFO 
            (memb_guid,memb___id,memb__pwd,memb_name,sno__numb,post_code,
            addr_info,addr_deta,tel__numb,mail_addr,phon_numb,fpas_ques,
            fpas_answ,job__code,appl_days,modi_days,out__days,true_days,
            mail_chek,bloc_code,ctl1_code) 
            VALUES ('1','$name','$pass','$name', '1','1234',
            '11111','1234','12343','$mail','$mail','0',
            '0','1','" . date('Y-m-d H:i:00') . "','2003-01-01','2003-01-01','2003-01-01',
            '1','0','1'); SET ANSI_WARNINGS ON;";
    
    $querycurr = "INSERT INTO VI_CURR_INFO 
            (ends_days,chek_code,used_time,memb___id,memb_name,memb_guid,
            sno__numb,Bill_Section,Bill_value,Bill_Hour,Surplus_Point,
            Surplus_Minute,Increase_Days )  
            VALUES ('2005','1',1234,'$name','$name',1,
            '7','6','3','6','6','1905-06-26 10:36:00','0' )";
            
    $querystatistics = "INSERT INTO AccountStatistics 
            (AccountID, RenaBeastFeed, BolBeastFeed, JocBeastFeed, TimeOnlineMinutes) 
            VALUES ('$name', 0, 0, 0, 0)";    
   
    $res0 = odbc_exec($msconnect, $queryidentityset);
    $res1 = odbc_exec($msconnect, $querymemb);
    $res2 = odbc_exec($msconnect, $querycurr);
    $res3 = odbc_exec($msconnect, $querystatistics);
    
    if($res0 != false && $res1 != false && $res2 != false && $res3 != false){
      return true;
    }
    return false;  
  }  
  
  function sendmail($name, $pass, $mail){
    $subj = "Storm 97d Mu-server: Account registration info"; 
    $message = "Hello ". $name .", \n You have registered new account with this credentials: \n Login: ". $name ." \n Passw: ". $pass ." \n \n
    If someone else used your E-mail without your approval,\n contact the server administrator please (jimmmy.chief@gmail.com). \n \n
    This e-mail was automatically generated, please do not reply to it.\n ";    
    $carray = array("token" => "5It8VdndMKiG8QfBsYCQBw", "emailsubject" => $subj, "emailbody" => $message, "addressee" => $mail, "sender" => "automat@storm97d.cz");
    $cinit = curl_init();
    curl_setopt($cinit, CURLOPT_URL, "https://m.bahno.net");
    curl_setopt($cinit, CURLOPT_POST, 1);
    curl_setopt($cinit, CURLOPT_RETURNTRANSFER, 1);                            
    curl_setopt($cinit, CURLOPT_TIMEOUT, 4);
    curl_setopt($cinit, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($cinit, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($cinit, CURLOPT_POSTFIELDS, http_build_query($carray));
    
    $response = curl_exec($cinit);
    
                          
    if($response === ""){
      curl_close($cinit);
      return true;      
    }
    curl_close($cinit);
    return false;  
  }
?>