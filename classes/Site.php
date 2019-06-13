<?php

class Site {

  private $id;
  private $name;
  private $user;
  private $size;
  private $pool;
  private $app;
  private $domains = array();
  private $databases = array();


  public function __construct($user, $data, $load=true) {
    $this->name = $data['name'];
    $this->user = $user;
    $this->app = $data['app'];
    $this->size = $data['size'];
    if ($load) {
      $this->load();
      $this->id = $data['id'];
      $pool['name'] = $data['pool'];
      $this->pool = new Pool($user->getArchHost(), $pool);
    }
  }

  public function getApp() {
    return $this->app;
  }

  public function getUser() {
    return $this->user;
  }

  public function getName() {
    return $this->name;
  }

  public function getSize() {
    return $this->size;
  }

  public function getDomains() {
    return $this->domains;
  }

  public function load() {
    unset($this->sites);
    $this->sites = array();
    unset($this->databases);
    $this->databases = array();
    $mysql = $this->user->getArchHost()->getMySQL();

    $query['table'] = "domains";
    $query['where']["site"] = $this->name;
    $query['fields'][] = "*";

    $result = $mysql->read($query);
    foreach ($result as $row) {
      $this->domains[] = new Domain($this, $row);
    }

    unset($query);
    $query['table'] = "db";
    $query['where']["site"] = $this->name;
    $query['fields'][] = "*";

    $result = $mysql->read($query);
    foreach ($result as $row) {
      $this->databases[] = new Database($this, $row);
    }
  }

  public function getDatabases() {
    return $this->databases;
  }

  public function getFreeSpace() {
    return $this->pool->getLVFreeSpace($this->name);
  }

  public function getUsedSpace() {
    return $this->pool->getLVUsedSpace($this->name);
  }

  public function getUsedSpacePercent() {
    return $this->pool->getLVUsedSpacePercent($this->name);
  }

  public function mount() {
    exec("mount /dev/".$this->pool->getName()."/".$this->name." /users/".$this->user->getName()."/".$this->name);
    exec("rm -rf /users/".$this->user->getName()."/".$this->name."/lost+found");
  }

  public function unmount() {
    exec("umount /users/".$this->user->getName()."/".$this->name);
  }

  public function getDirectory() {
    return "/users/".$this->user->getName()."/".$this->name;
  }

  public function isEnabled() {
    return file_exists("/etc/nginx/sites-enabled/".$this->name.".conf");
  }

  public function enable() {
    exec("ln -s /etc/nginx/sites-available/".$this->name.".conf /etc/nginx/sites-enabled/".$this->name.".conf");
  }

  public function disable() {
    unlink("/etc/nginx/sites-enabled/".$this->name.".conf");
  }

  public function regenerateNginxConfig() {
    if ($this->isEnabled()) {
      $this->disable();
      $enabled = true;
    } else {
      $enabled = false;
    }
    if (file_exists("/etc/nginx/sites-available/".$this->name.".conf")) {
      unlink("/etc/nginx/sites-available/".$this->name.".conf");
    }
    $domains = array();
    foreach ($this->domains as $domain) {
      $domains[] = $domain->getDomain();
    }
    if (count($domains)<1) {
      $domains[] = "none.archhost.com";
    }
    exec("cp /opt/archhost/templates/nginx/template.conf /etc/nginx/sites-available/".$this->name.".conf");
    exec("sed -i -e 's/{DOMAIN}/".implode(" ",$domains)."/g' /etc/nginx/sites-available/".$this->name.".conf");
    exec("sed -i -e 's/{USER}/".$this->user->getName()."/g' /etc/nginx/sites-available/".$this->name.".conf");
    exec("sed -i -e 's/{SITE}/".$this->name."/g' /etc/nginx/sites-available/".$this->name.".conf");
    if ($enabled) {
      $this->enable();
    }
    $this->user->getArchHost()->reloadNginx();
  }

  public function regeneratePHPFPMConfig() {
    if (file_exists("/etc/php/php-fpm/".$this->name.".conf")) {
      unlink("/etc/php/php-fpm/".$this->name.".conf");
    }
    exec("cp /opt/archhost/templates/php-fpm/template.conf /etc/php/php-fpm.d/".$this->name.".conf");
    exec("sed -i -e 's/{USER}/".$this->user->getName()."/g' /etc/php/php-fpm.d/".$this->name.".conf");
    exec("sed -i -e 's/{SITE}/".$this->name."/g' /etc/php/php-fpm.d/".$this->name.".conf");
    $this->user->getArchHost()->reloadPHPFPM();
  }

  public function create() {
    $this->pool = $this->user->getArchHost()->getLowestPool();
    $this->pool->create($this->name,$this->size);
    $this->pool->format($this->name);

    exec("mkdir /users/".$this->user->getName()."/".$this->name);
    $this->mount();
    exec("chown ".$this->user->getName().":".$this->user->getName()." /users/".$this->user->getName()."/".$this->name);

    $this->regenerateNginxConfig();
    $this->regeneratePHPFPMConfig();

    $mysql = $this->user->getArchHost()->getMySQL();

    $query['table'] = "sites";
    $query['fields']['name']=$this->name;
    $query['fields']['size']=$this->size;
    $query['fields']['pool']=$this->pool->getName();
    if ($this->app != "") {
      $query['fields']['app']=$app;
    }
    $query['fields']['user']=$this->user->getName();

    $affected = $mysql->insert($query);
    $this->id = $affected[0]['id'];

    if ($this->app != "") {
      $app = $this->user->getArchHost()->getApplications()->getApplication($this->app);
      $app->install($this);
    }

    $this->enable();
  }

  public function addDomain($domain) {
    $data['domain'] = $domain;
    $domain = new Domain($this, $data);
    $this->domains[] = $domain;
    $domain->create();
    $this->regenerateNginxConfig();
  }

  public function getDomain($domain) {
    foreach ($this->domains as $dom) {
      if ($dom->getDomain() == $domain) {
        return $dom;
      }
    }
  }

  public function removeDomain($domain) {
    $dom = $this->getDomain($domain);
    $dom->delete();
    if (($key = array_search($dom, $this->domains)) !== false) {
      unset($this->domains[$key]);
    }
    $this->regenerateNginxConfig();
  }

  public function addDatabase($database) {
    $data['name'] = $this->name."_".$database;
    $database = new Database($this,  $data);
    $this->databases[] = $database;
    return $database->create();
  }

  public function getDatabase($database) {
    foreach ($this->databases as $db) {
      if ($db->getName() == $database) {
        return $db;
      }
    }
  }

  public function removeDatabase($database) {
    $db = $this->getDatabase($database);
    $db->delete();
    if (($key = array_search($db, $this->databases)) !== false) {
      unset($this->databases[$key]);
    }
  }

  public function delete() {
    $this->disable();

    unlink("/etc/nginx/sites-available/".$this->name.".conf");
    unlink("/etc/php/php-fpm.d/".$this->name.".conf");
    $this->user->getArchHost()->reloadNginx();
    $this->user->getArchHost()->reloadPHPFPM();

    $this->unmount();

    $this->pool->remove($this->name);
    exec("rm -rf /users/".$this->user->getName()."/".$this->name);
    foreach ($this->domains as $domain) {
      $dom->delete();
    }

    foreach ($this->databases as $database) {
      $database->delete();
    }

    $mysql = $this->user->getArchHost()->getMySQL();

    $query['table'] = "sites";
    $query['where']['name']=$this->name;

    $mysql->delete($query);
  }
}
