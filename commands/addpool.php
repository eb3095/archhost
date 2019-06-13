<?php

class addpool {

  public function run($archhost, $argv){

    if (count($argv)<1) {
      msg("You need to specify a pool!");
      exit();
    }

    $name = $argv[0];

    if ($archhost->poolExists($name)){
      msg("That pool already exists!");
      exit();
    }

    $archhost->addPool($name);
    msg("Pool ".$name." added!");
    exit();
  }

}
