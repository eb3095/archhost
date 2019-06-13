<?php
class SecureMySQL {
    private $database;
    private $host;
    private $user;
    private $password;
    private $port;
    private $connection="term";
    private $open;

    public function __construct($config) {
        $this->database=$config['database'];
        $this->host=$config['host'];
        $this->user=$config['user'];
        $this->password=$config['password'];
        $this->port=$config['port'];
    }

    public function open(){
        if ($this->open) {
          return;
        }
        $this->connection=new mysqli($this->host, $this->user, $this->password, $this->database, $this->port);
        if (mysqli_connect_errno()){
            throw Exception("Failed to connect to MySQL: " . mysqli_connect_error());
        }
        $this->open = true;
    }

    public function isOpen() {
      return $this->$open;
    }

    public function close(){
        mysqli_close($this->connection);
        $this->connection="term";
        $this->$open = false;
    }

    public function read($data){
        if ($this->connection=="term"){
            throw Exception("No connection to MySQL Server.");
        }

        $where=array();
        $select=array();

        foreach ($data['fields'] as $value) {
          $select[] = $value;
        }

        if (isset($data['where'])){
          foreach ($data['where'] as $key => $value) {
            $where[] = $key."='".str_replace("'", "", $value)."'";
          }
        }

        $sql = "SELECT ".implode(",",$select)." FROM ".$data['table'];
        if (count($where) > 0) {
          $sql .= " WHERE ".implode(",",$where).";";
        } else {
          $sql .= ";";
        }

        // mysqli_report(MYSQLI_REPORT_ERROR);
        // msg($sql);
        $res=$this->connection->query($sql);

        $rows = array();
        while ($row = $res->fetch_assoc()){
          $rows[] = $row;
        }

        return $rows;
    }

    public function insert($data){
      $fields = array();
      $values = array();

      foreach ($data['fields'] as $key => $value) {
        $fields[] = $key;
        $values[] = "'".str_replace("'", "", $value)."'";
      }

      $sql = "INSERT INTO ".$data['table']." (".implode(",",$fields).") VALUES (".implode(",",$values).");";
      $res=$this->connection->query($sql);
      return $res;
    }

    public function update($data){
      $set = array();
      $where = array();

      foreach ($data['fields'] as $key => $value) {
        $set[] = $key."='".str_replace("'", "", $value)."'";
      }

      foreach ($data['where'] as $key => $value) {
        $where[] = $key."='".str_replace("'", "", $value)."'";
      }

      $sql = "UPDATE ".$data['table']." SET ".implode(",",$set)." WHERE ".implode(",",$where).";";
      $res=$this->connection->query($sql);
      return $res;
    }

    public function delete($data){
      $where = array();

      foreach ($data['where'] as $key => $value) {
        $where[] = $key."='".str_replace("'", "", $value)."'";
      }

      $sql = "DELETE FROM ".$data['table']." WHERE ".implode(",",$where).";";
      $res=$this->connection->query($sql);
      return $res;
    }
}
