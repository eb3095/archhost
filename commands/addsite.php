<?php

class addsite {

  public function run($archhost, $argv){

    // Argument Assignments
    $ctr=0;
    foreach($argv as $argument) {
      if (strpos($argument, '=') == false) {
        msg("Invalid argument (Did you forget the =): ".$argument);
        $ctr++;
        continue;
      }

      $parts = explode("=",$argument);
      $arg = $parts[0];
      $value = $parts[1];

      switch ($arg) {
        case "site":
          $site = $value;
          break;
        case "app":
          $app = $value;
          break;
        case "size":
          $size = $value;
          break;
        case "user":
          $user = $value;
          break;
        default:
          msg("Invalid argument: ".$argument);
          exit();
      }
      $ctr++;
    }

    if (!isset($user)) {
      msg("You need to specify a user!");
      exit();
    }
    if (!isset($site)) {
      msg("You need to specify a site!");
      exit();
    }
    if (!isset($size)) {
      $size="1000";
    }
    if (!isset($app)) {
      $app="";
    }

    if (!$archhost->userExists($user)) {
      msg("That user does not exist!");
      exit();
    }
    if ($archhost->siteExists($site)) {
      msg("That site exists!");
      exit();
    }

    $data['name'] = $site;
    $data['app'] = $app;
    $data['size'] = $size;
    $website = new Site($archhost->getUser($user),$data,false);
    $website->create();
    $website->enable();
    msg("Site ".$site." created!");
  }

}
