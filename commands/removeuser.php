<?php

class removeuser {

  public function run($archhost, $argv){

    if (count($argv)<1) {
      msg("You need to specify a user!");
      exit();
    }

    $name = $argv[0];

    if (!$archhost->userExists($name)){
      msg("That user does not exist!");
      exit();
    }

    $archhost->getUser($name)->delete();
    msg("User ".$name." deleted!");
  }

}
