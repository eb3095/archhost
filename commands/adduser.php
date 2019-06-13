<?php

class adduser {

  public function run($archhost, $argv){

    if (count($argv)<1) {
      msg("You need to specify a user!");
      exit();
    }

    $name = $argv[0];

    if ($archhost->userExists($name)){
      msg("That user already exists!");
      exit();
    }

    $archhost->addUser($name);
    msg("User added!");
  }

}
