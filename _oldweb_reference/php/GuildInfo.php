<?php



class GuildInfo {
    protected $msconnect;

    public function __construct($msconnect){
        $this->msconnect = $msconnect;
    }
    
    public function printGldiContent(){
       $data = $this->getGuildData();
       if(count($data) > 0){
        $printoid .= "<table border='1' cellpadding='5' id='list'>
                      <th class='mark'>Mark</th>
                      <th class='gname'>Name</th>
                      <th class='score'>Resets</th>
                      <th class='gmaster'>Guild Master</th>
                      <th class='gnotice'>Notice</th>";
        foreach($data as $gname => $guild){
            $printoid .= "<tr><td class='mark'><center>" .
            $guild["mark_image"] . 
            "</center></td><td class='gname'>" .
            $gname .
            "</td><td class='score'>" . 
            $guild["score"] .
            "</td><td class='gmaster'>" . 
            $guild["master"] .
            "</td><td>" . 
            $guild["notice"] . 
            "</td></tr>"
            ;
        
        }
        $printoid .= "</table>";    
      }
      print($printoid);
    }
    
    
    public function getGuildData(){
        $glist = array();
        $sql = "SELECT G_Name, G_Master, G_Score, G_Mark, G_Notice FROM Guild ORDER BY G_Score DESC";
        $result = odbc_exec($this->msconnect, $sql);
        
        while(odbc_fetch_row($result)){
        
            $resetQuery = "SELECT SUM(COALESCE(Character.Reset, 0)) AS resets
                          FROM GuildMember 
                          INNER JOIN Character ON GuildMember.Name = Character.Name
                          WHERE (GuildMember.G_Name = '".odbc_result($result, 1)."')";
            $res = odbc_exec($this->msconnect, $resetQuery);
            while(odbc_fetch_row($res)){
                $score = odbc_result($res, 1);
            }              
        
            $glist[odbc_result($result, 1)] = array(
            "name"=>odbc_result($result, 1), 
            "score"=>$score, 
            "master"=>odbc_result($result, 2), 
            "notice"=>odbc_result($result, 5),
            "markB"=>odbc_result($result, 4),
            "mark_image"=>"",
            );
            
            $glist[odbc_result($result, 1)]["mark_image"] = $this->SYlogo($glist[odbc_result($result, 1)]["markB"]);     
        }        
        return $glist;        
    }   


/*
[  "MuMark to HTML" Script]
[            Author: Savoy]
[     savoy___@hotmail.com]
*/
public function SYlogo($code,$xy=3) {
    // Turn hex into dec
    $code = urlencode(bin2hex($code));
    $color[0] = ''; 
    $color[1]='#000000'; 
    $color[2]='#8c8a8d'; 
    $color[3]='#ffffff'; 
    $color[4]='#fe0000'; 
    $color[5]='#ff8a00'; 
    $color[6]='#ffff00'; 
    $color[7]='#8cff01'; 
    $color[8]='#00ff00'; 
    $color[9]='#01ff8d'; 
    $color['a']='#00ffff'; 
    $color['b']='#008aff'; 
    $color['c']='#0000fe'; 
    $color['d']='#8c00ff'; 
    $color['e']='#ff00fe'; 
    $color['f']='#ff008c'; 
    // Set the default zero position.
    $i = 0; 
    $ii = 0;
    // Create the table
    $it = '<table style=\'width: '.(8*$xy).'px;height:'.(8*$xy).'px\' border=0 cellpadding=0 cellspacing=0><tr>';
    // Start the logo drawing cycle for each color slot
    while ($i<64) {
        // Get the slot color number
        $place = $code{$i};
        // Increase the slot
        $i++;$ii++;
        // Get the color of the slot
        $add = $color[$place];
        // Create the slot with its color
        if($add){
            $it .= '<td class=\'guildlogo\' style=\'background-color: '.$add.';\' width=\''.$xy.'\' height=\''.$xy.'\'></td>';
        } else {
            $it .= '<td class=\'guildlogo\' width=\''.$xy.'\' height=\''.$xy.'\'></td>';
        }
        // In case we have a new line
        if ($ii==8) { 
            $it .=  '</tr>'; 
            if ($ii != 64) $it .='<tr>';
            $ii =0; 
        }
    }
    // Finish the table off
    $it .= '</table>';
    // What do we output
    return $it;
}












}