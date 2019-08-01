<?php

class ArchHost {

  private $mysql;
  private $mysqlConfig;
  private $applications;

  public function __construct($mysql) {
    $this->mysqlConfig = $mysql;
    $this->mysql = new SecureMySQL($mysql);
    $this->mysql->open();
    $applications = new Applications();
  }

  public function getApplications(){
    return $applications;
  }

  public function getMySQL() {
    return $this->mysql;
  }

  public function getMySQLPassword() {
    return $this->mysqlConfig['password'];
  }

  public function getMySQLUser() {
    return $this->mysqlConfig['user'];
  }

  public function getUser($user) {
    $query['table'] = "users";
    $query['fields'][] = "*";
    $query['where']['name'] = $user;

    $result = $this->mysql->read($query);

    return new User($this,$result[0]);
  }

  public function addUser($user) {
    $data['name']=$user;
    $user = new User($this, $data, false);
    $user->create();
    $this->users[] = $user;
    return $user;
  }

  public function removeUser($user) {
    $usr = $this->getUser($user);
    $usr->delete();
    if (($key = array_search($usr, $this->users)) !== false) {
      unset($this->users[$key]);
    }
  }

  public function reloadNginx() {
    exec("systemctl reload nginx");
  }

  public function reloadPHPFPM() {
    exec("systemctl reload php-fpm");
  }

  public function domainExists($domain) {
    $data['table'] = "domains";
    $data['fields'][] = "*";
    $data['where']['domain'] = $domain;
    $result = $this->mysql->read($data);
    return count($result) > 0;
  }

  public function siteExists($site) {
    $data['table'] = "sites";
    $data['fields'][] = "*";
    $data['where']['name'] = $site;
    $result = $this->mysql->read($data);
    return count($result) > 0;
  }

  public function poolExists($name) {
    $data['table'] = "pools";
    $data['fields'][] = "*";
    $data['where']['name'] = $name;
    $result = $this->mysql->read($data);
    return count($result) > 0;
  }

  public function getSite($site) {
    $data['table'] = "sites";
    $data['fields'][] = "*";
    $data['where']['name'] = $site;
    $result = $this->mysql->read($data);
    return $this->getUser($result[0]['user'])->getSite($site);
  }

  public function getSites() {
    $data['table'] = "sites";
    $data['fields'][] = "*";
    $result = $this->mysql->read($data);
    $sites = array();
    foreach ($result as $row) {
      $sites[] = $this->getSite($row['name']);
    }
    return $sites;
  }

  public function getDatabase($database) {
    $data['table'] = "db";
    $data['fields'][] = "*";
    $data['where']['name'] = $database;
    $result = $this->mysql->read($data);
    return $this->getUser($result[0]['user'])->getSite($result[0]['site'])->getDatabase($database);
  }

  public function getDomain($domain) {
    $data['table'] = "domains";
    $data['fields'][] = "*";
    $data['where']['domain'] = $domain;
    $result = $this->mysql->read($data);
    return $this->getUser($result[0]['user'])->getSite($result[0]['site'])->getDomain($domain);
  }

  public function userExists($user) {
    $data['table'] = "users";
    $data['fields'][] = "*";
    $data['where']['name'] = $user;
    $result = $this->mysql->read($data);
    return count($result) > 0;
  }

  public function databaseExists($db) {
    $data['table'] = "db";
    $data['fields'][] = "*";
    $data['where']['name'] = $db;
    $result = $this->mysql->read($data);
    return count($result) > 0;
  }

  public function getPools() {
    $query['table'] = "pools";
    $query['fields'][] = "*";

    $result = $this->mysql->read($query);

    $pools = array();
    foreach ($result as $row) {
      $pools[] = new Pool($this, $row);
    }

    return $pools;
  }

  public function addPool($name) {
    $data['name'] = $name;
    $pool = new Pool($this, $data);
    $pool->import();
  }

  public function removePool($name) {
    $pool = $this->getPool($name);
    $pool->delete();
  }

  public function getPool($name) {
    foreach ($this->getPools() as $pool) {
      if ($pool->getName() == $name) {
        return $pool;
      }
    }
  }

  public function getLowestPool() {
    $pools = $this->getPools();
    $pool;
    $size=0;
    foreach ($pools as $vg) {
      if ($vg->getFreeSpace() > $size) {
        $size = $vg->getFreeSpace();
        $pool = $vg;
      }
    }
    return $pool;
  }

  public function isRoomFoor($size) {
    $pools = $this->getPools();
    foreach ($pools as $vg) {
      if ($vg->getFreeSpace() >= $size) {
        return true;
      }
    }
    return $false;
  }

}
