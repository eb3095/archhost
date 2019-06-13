<?php

class removedatabase {

  public function run($archhost, $argv){

    if (count($argv)<1) {
      msg("You need to specify a database!");
      exit();
    }

    $name = $argv[0];

    if (!$archhost->databaseExists($name)){
      msg("That database does not exist!");
      exit();
    }

    $archhost->getDatabase($name)->delete();
    msg("Database ".$name." deleted!");
  }

}
