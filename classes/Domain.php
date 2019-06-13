<?php

class Domain {

  private $id;
  private $domain;
  private $user;
  private $site;

  public function __construct($site, $data) {
    $this->domain = $data['domain'];
    $this->site = $site;
    if (isset($data['id'])) {
      $this->id = $data['id'];
    }
    $this->user = $site->getUser();
  }

  public function getUser() {
    return $this->user;
  }

  public function getSite() {
    return $this->site;
  }

  public function getDomain() {
    return $this->domain;
  }

  public function create() {
    $mysql = $this->user->getArchHost()->getMySQL();

    $query['table'] = "domains";
    $query['fields']['domain']=$this->domain;
    $query['fields']['user']=$this->user->getName();
    $query['fields']['site']=$this->site->getName();

    $affected = $mysql->insert($query);
    $this->id = $affected[0]['id'];
  }

  public function delete() {
    $mysql = $this->user->getArchHost()->getMySQL();

    $query['table'] = "domains";
    $query['where']['domain']=$this->domain;

    $mysql->delete($query);
  }
}
