<?php

class Database {

  private $id;
  private $name;
  private $user;
  private $site;

  public function __construct($site, $data) {
    $this->name = $data['name'];
    $this->site = $site;
    $this->user = $site->getUser();
    if (isset($data['id'])) {
      $this->id = $data['id'];
    }
  }

  public function getUser() {
    return $this->user;
  }

  public function getSite() {
    return $this->site;
  }

  public function getName() {
    return $this->name;
  }

  private function rand_passwd( $length = 8, $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@$%^&*()_-=+[{:,<.>?}]' ) {
    return substr( str_shuffle( $chars ), 0, $length );
  }

  public function create() {
    $password = $this->rand_passwd(50);

    exec("mysql -u ".$this->user->getArchHost()->getMySQLUser()." --password='".$this->user->getArchHost()->getMySQLPassword()."' -e \"CREATE DATABASE ".$this->name.";\"");
    exec("mysql -u ".$this->user->getArchHost()->getMySQLUser()." --password='".$this->user->getArchHost()->getMySQLPassword()."' -e \"CREATE USER '".$this->name."'@'localhost' IDENTIFIED BY '".$password."';\"");
    exec("mysql -u ".$this->user->getArchHost()->getMySQLUser()." --password='".$this->user->getArchHost()->getMySQLPassword()."' -e \"GRANT ALL PRIVILEGES ON ".$this->name.".* TO '".$this->name."'@'localhost'; FLUSH PRIVILEGES;\"");

    $mysql = $this->user->getArchHost()->getMySQL();

    $query['table'] = "db";
    $query['fields']['name']=$this->name;
    $query['fields']['user']=$this->user->getName();
    $query['fields']['site']=$this->site->getName();

    $affected = $mysql->insert($query);
    $this->id = $affected[0]['id'];
    return $password;
  }

  public function delete() {
    exec("mysql -u ".$this->user->getArchHost()->getMySQLUser()." --password='".$this->user->getArchHost()->getMySQLPassword()."' -e \"DROP DATABASE ".$this->name.";\"");
    exec("mysql -u ".$this->user->getArchHost()->getMySQLUser()." --password='".$this->user->getArchHost()->getMySQLPassword()."' -e \"DROP USER '".$this->name."'@'localhost';\"");
    $mysql = $this->user->getArchHost()->getMySQL();

    $query['table'] = "db";
    $query['where']['name']=$this->name;
    $mysql->delete($query);
  }

  public function generateNewPassword() {
      $password = $this->rand_passwd(50);
      exec("mysql -u ".$this->user->getArchHost()->getMySQLUser()." --password='".$this->user->getArchHost()->getMySQLPassword()."' -e \"SET PASSWORD FOR '".$this->name."'@'localhost' = PASSWORD('".$password."');\"");
      return $password;
  }
}
