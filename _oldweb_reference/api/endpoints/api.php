<?php

class Api {

    const DB_MU_ONLINE = "MuOnline";
    const DB_WAREHOUSE = "WebWarehouse";

  private $dbConn;

  public function __construct($mssqlConnection){
      $this->dbConn = $mssqlConnection;
  }
  
  
  public function run(){
      if(!isset($_REQUEST['method']) || !isset($_REQUEST['database']) || (!isset($_REQUEST['table']) && $_REQUEST['method'] != "SHOW")){
         die('some required params not sent');
      }
      
      $method = $_REQUEST['method'];
      unset($_REQUEST['method']);
      $database = $_REQUEST['database'];
      unset($_REQUEST['database']);
      $table = $_REQUEST['table'];
      unset($_REQUEST['table']);
      
      $id = null;
      if(isset($_REQUEST['id'])){
          $id = $_REQUEST['id'];
          unset($_REQUEST['id']);
      }
      //TODO - spravne pojmenovani sloupce id -> Memb___id napr.
      switch($method){
          case 'GET':
              $this->handleGet($database, $table, $id, $_REQUEST);
              break;
          case 'PUT':
              if(!$id){ die('changing all records in table is denied!'); }
              $this->handlePut($database, $table, $id, $_REQUEST);
              break;
          case 'POST':
              $this->handlePost($database, $table, $_REQUEST);
              break;
          case 'DELETE':
              if(!$id){ die('id required for deleting'); }
              if($database == 'MuOnline'){ die('deleting from database '.$database.' is denied'); }
              $this->handleDelete($database, $table, $id);
              break;
          case 'SHOW':
              $this->handleShow($database);
              break;                
          default:
              die('unsupported method');
      }      
  }
  
  public function handleGet($database, $table, $id = null, $request = array()){
      
      $sql = "SELECT * FROM ".$database.".dbo.".$table;
      if($id){
          $sql .= " WHERE id = ".$id." ";
      } else {
          $sql .= " WHERE 1 = 1 ";
      }
      $sql .= $this->addRequestParamsIntoSql($request, 'AND');
      
      $result = $this->doQuery($sql, 'GET');
      $this->showResult($result);
      return $result;
  }

    public function handlePut($database, $table, $id = null, $request = array()){
      $sql = "UPDATE ".$database.".dbo.".$table." SET ";
      
      $params = $this->addRequestParamsIntoSql($request, ', ');
      //remove first comma
      $params = substr($params, 2);      
      $sql .= $params;      
      $sql .= " WHERE id = ".$id." ";
      
      $this->doQuery($sql, 'PUT');
      $this->showResult('update successful');
  }

    public function handlePost($database, $table, $request){
      throw new Exception('not implemented yet');
      $sql = "INSERT INTO ".$database.".dbo.".$table;
  }

    public function handleDelete($database, $table, $id){
      $sql = "DELETE FROM ".$database.".dbo.".$table." WHERE id = ".$id;
      
      $this->doQuery($sql, 'DELETE');
      $this->showResult('delete successful');
  }

    public function handleShow($database){
      $sql = "SELECT *
              FROM ".$database.".dbo.sysobjects
              WHERE (xtype = 'U')";
      $result = $this->doQuery($sql, 'GET');
      $this->showResult($result);
        return $result;
  }



  private function addRequestParamsIntoSql($request, $glue = 'AND'){
      $stringed = "";
      foreach($request as $name => $value){
          if($name == 'PHPSESSID'){
              continue;
          }      
          if(is_array($value)){
              $stringed .= " ".$glue." ".$name. " IN (".implode(',', $value).") ";
          }
          if(is_numeric($value)){
              $stringed .= " ".$glue." ".$name. " = ".$value. " ";
          } elseif(is_string($value)){
              $stringed .= " ".$glue." ".$name. " = '".$value."' ";
          }
      }
      return $stringed;      
  }
  
  private function doQuery($sql, $method){
      $result = odbc_exec($this->dbConn, $sql);
      
      if ($result === false) {
          throw new Exception('query failed! sql:'.$sql);
      }
      
      $res = array();
      $dzej = 0;
      
      if ($method != 'GET') {
          return true;
      }
      
      while(odbc_fetch_row($result)){
          for($i=1;$i<=odbc_num_fields($result);$i++){
            $res[$dzej][odbc_field_name($result, $i)] = odbc_result($result,$i);      
          }
          $dzej++;
      }
      return $res;
  }
  
  private function showResult($result){
      //header("Content-type: text/json");
      //echo json_encode($result);
  }

}