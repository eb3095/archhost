<?php

class Pool {

  private $id;
  private $name;
  private $archhost;

  public function __construct($archhost, $data) {
    $this->archhost = $archhost;
    $this->name = $data['name'];
    if (isset($data['id'])) {
      $this->id = $data['id'];
    }
  }

  public function getFreeSpace() {
    return explode(".",str_replace("m","",exec("export LVM_SUPPRESS_FD_WARNINGS=true;pvs --units m | grep ".$this->name." | awk '{print $6}'")))[0];
  }

  public function getTotalSpace() {
    return explode(".",str_replace("m","",exec("export LVM_SUPPRESS_FD_WARNINGS=true;pvs --units m | grep ".$this->name." | awk '{print $5}'")))[0];
  }

  public function getUsedSpace() {
    return $this->getTotalSpace() - $this->getFreeSpace();
  }

  public function getUsedPercent() {
    return explode(".",($this->getUsedSpace() / $this->getTotalSpace()) * 100)[0];
  }

  public function hasSpace($value) {
    return $value <= $this->getFreeSpace();
  }

  public function create($name, $size) {
    exec("export LVM_SUPPRESS_FD_WARNINGS=true;lvcreate -W y ".$this->name." --name ".$name." -L ".$size."m");
  }

  public function getLVFreeSpace($name) {
    return str_replace("M","",exec("df /dev/mapper/".$this->name."-".$name." -B m | tail -1 | awk '{print $4}'"));
  }

  public function getLVUsedSpace($name) {
    return str_replace("M","",exec("df /dev/mapper/".$this->name."-".$name." -B m | tail -1 | awk '{print $3}'"));
  }

  public function getLVUsedSpacePercent($name) {
    return str_replace("%","",exec("df /dev/mapper/".$this->name."-".$name." -B m | tail -1 | awk '{print $5}'"));
  }

  public function format($name) {
    exec("mkfs.ext4 -F /dev/".$this->name."/".$name);
  }

  public function remove($name) {
    exec("export LVM_SUPPRESS_FD_WARNINGS=true;lvremove -f /dev/".$this->name."/".$name);
  }

  public function import() {
    $mysql = $this->archhost->getMySQL();

    $query['table'] = "pools";
    $query['fields']['name']=$this->name;

    $affected = $mysql->insert($query);
    $this->id = $affected[0]['id'];
  }

  public function delete() {
    $mysql = $this->archhost->getMySQL();

    $query['table'] = "pools";
    $query['where']['name']=$this->name;

    $affected = $mysql->delete($query);
    $this->id = $affected[0]['id'];
  }

  public function getName() {
    return $this->name;
  }

  public function getArchHost() {
    return $this->archhost;
  }
}
