<?php

class removedomain {

  public function run($archhost, $argv){

    if (count($argv)<1) {
      msg("You need to specify a domain!");
      exit();
    }

    $name = $argv[0];

    if (!$archhost->domainExists($name)){
      msg("That database does not exist!");
      exit();
    }

    $archhost->getDomain($name)->delete();
    msg("Domain ".$name." deleted!");
  }

}
