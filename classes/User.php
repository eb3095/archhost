<?php

class User {

  private $archhost;
  private $id;
  private $name;
  private $sites = array();

  public function __construct($archhost, $data, $load=true) {
    $this->name = $data['name'];
    $this->archhost = $archhost;
    if ($load) {
      $this->id = $data['id'];
      $this->load();
    }
  }

  public function getName() {
    return $this->name;
  }

  public function load() {
    unset($this->sites);
    $this->sites = array();
    unset($this->databases);
    $this->databases = array();
    $mysql = $this->archhost->getMySQL();

    $query['table'] = "sites";
    $query['where']["user"] = $this->name;
    $query['fields'][] = "*";

    $result = $mysql->read($query);
    foreach ($result as $row) {
      $this->sites[] = new Site($this, $row);
    }
  }

  public function getSites() {
    return $this->sites;
  }

  public function getArchHost() {
    return $this->archhost;
  }

  public function getSite($site) {
    foreach ($this->sites as $web) {
      if ($web->getName() == $site) {
        return $web;
      }
    }
  }

  public function create() {
    exec("mkdir /users/".$this->name);
    exec("mkdir /users/".$this->name."/sites");
    exec("mkdir /users/".$this->name."/logs");
    exec("mkdir /users/".$this->name."/tmp");
    exec("useradd -d /users/".$this->name." ".$this->name);
    exec("chown -R ".$this->name.":".$this->name." /users/".$this->name);

    $mysql = $this->archhost->getMySQL();

    $query['table'] = "users";
    $query['fields']['name']=$this->name;

    $affected = $mysql->insert($query);
    $this->id = $affected[0]['id'];
  }

  public function delete() {
    foreach ($this->sites as $site) {
      $site->delete();
    }

    exec("rm -rf /users/".$this->name);

    exec("userdel ".$this->name);

    $mysql = $this->archhost->getMySQL();

    $query['table'] = "users";
    $query['where']['name']=$this->name;

    $mysql->delete($query);
  }

}
