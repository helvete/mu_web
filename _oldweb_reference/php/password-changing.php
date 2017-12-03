<?php



function printPasswordChangeBox($hash){
  echo "<div class='infoblock statblock'>
        <b>Password change</b><br /><br />
        <form name='PasswordChangeForm' action='index.php?navi=pach' method='post'>
        <b><input name='newpass' type='password' id='newpass' maxlength='10' size='12'></b>
        <label for='newpass'>New Pwd:</label><br />
        <input name='hash' type='hidden' id='hash' value='" . $hash . "'><br /><br />
        <b><input type='submit' name='submit' value='Change Password!'></b></form>
        </div>";

}


function ChangeThePass(){
    global $msconnect;
    $sanitak = new sanitizer(true);
    $newpass = $sanitak->validateLengths($_POST['newpass']) ? $sanitak->sanitizeSQL($_POST['newpass']) : false;
    $hash = $sanitak->sanitizeSQL($_POST['hash']);
    $timeOK = date("ymdH", time());
    
    if($newpass && $hash && $timeOK){
          $sql = "UPDATE MEMB_INFO SET memb__pwd = '". $newpass ."',
                  fpas_answ = 0,
                  fpas_ques = 0
                  WHERE fpas_answ = '". $hash ."'
                  AND CONVERT(int, fpas_ques) >= ". $timeOK ."";
          $result = odbc_exec($msconnect, $sql);
          
          if(!odbc_num_rows ($result) > 0){          
              echo "<span id='warning'>Invalid data to change password.</span><br /><br /><br />";
              return false;
          }
          return true;              
    } else {
      echo "<span id='warning'>Potentially dangerous data inputed!</span><br /><br /><br />";
      return false;      
    }
}



function printLastChanceForm(){
   echo "<div class='infoblock statblock'>
        <form name='WantToChangeForm' action='index.php?navi=pach&action=verify' method='post'>
        <b><input name='account' type='text' id='account' value='' size='10'></b>
        <label for='account'>Login:</label><br />
        <b><input name='email' type='text' id='email' value='' size='10'></b>
        <label for='email'>E-mail:</label>
        <p style='text-align: center;'><img src='../pics/captcha.php' width='120' height='30' border='1' alt='CAPTCHA'></p>
        <p style='text-align: center;'><input type='text' size='6' maxlength='5' name='captcha' value='' title='please type digits from image here'></p>
        <b><input type='submit' name='submit' value='I want to change Password!'></b></form>
        </div>";

}

function generateHash(){
    $sul = "majoranka";
    $date = date("YmdHis", time());
    return hash_hmac("md5", $date, $sul, false);
}

function verifyData(){
    global $msconnect;
    $sanitak = new sanitizer(true);
    $login = $sanitak->sanitizeSQL($_POST['account']);
    $mail = $sanitak->sanitizeSQL($_POST['email']);
    
    if($login && $mail){
        $hash = generateHash();
        $sql = "UPDATE MEMB_INFO SET fpas_ques = '". date("ymdH", time()+ 86400) ."', fpas_answ = '". $hash ."' WHERE memb___id = '". $login ."' AND mail_addr = '". $mail ."'";
        $result = odbc_exec($msconnect, $sql);
        if(odbc_num_rows ($result) > 0){
            if(sendMail($mail, $hash, $login)){
                printMessage(1);
            }
            else {
                printMessage(0);
            }
        } else {
            printMessage(-1);
        }
    } else {
        printMessage(-1);
    }
}

function sendMail($mail, $hash, $login){
    $link = "http://193.85.144.110/index.php?navi=pach&hash=" . $hash;
    $subj = "Storm 97d Mu-server: Password change"; 
    $message = "Hello, ". $login .",\n To change password, please follow this link:\n ". $link ."\n \n
    If you did not initiate password changing, ignore this e-mail,\n or contact the server administrator (jimmmy.chief@gmail.com). \n \n
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

function printMessage($ok){
    
    
    if($ok == 1){
      echo "<span id='success'>Link to change password was sent to your E-mail</span><br />";
    }
    if($ok == 0){
      echo "<span id='warning'>Mail was not sent to you, for some unknown reasons</span><br />";
    }
    if($ok < 0){
      echo "<span id='warning'>Incorrect data provided!</span><br />";    
    }
    if($ok == 2){
      echo "<span id='success'>Password was successfuly changed!</span><br />";
    }
    if($ok == 3){
      echo "<span id='warning'>Wrong captcha entered!</span><br />";
    }    
}

//handleni stavu zmeny hesla, sleepy na horsi BF utok
if(isset($_GET['print'])){
    printMessage($_GET['print']);
}

if($_GET['action'] == "init"){
    sleep(1);
    printLastChanceForm();  
}

if($_GET['action'] == "verify"){
    sleep(1);
    if($_POST["captcha"] != $_SESSION["digit"]){
        printMessage(3);
        printLastChanceForm();
    } else {
        verifyData();
    }
}

if(isset($_GET['hash'])){
    sleep(1);
    printPasswordChangeBox($_GET['hash']);

}

if(isset($_POST['hash'])){
    sleep(1);
    if(ChangeThePass()){
        printMessage(2);
    }
    
}
?>
