<?php

class changedatabasepassword {

  public function run($archhost, $argv){

    if (count($argv)<1) {
      msg("You need to specify a database!");
      exit();
    }

    $name = $argv[0];

    if (!$archhost->databaseExists($name)){
      msg("That database doesn't exist!");
      exit();
    }

    $db = $archhost->getDatabase($name);
    $password = $db->generateNewPassword();
    msg("Database ".$db->getName()." password now set to '".$password."'!");
  }

}
