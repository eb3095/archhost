<?php

class adddomain {

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
        case "domain":
          $domain = $value;
          break;
        case "site":
          $site = $value;
          break;
        default:
          msg("Invalid argument: ".$argument);
          exit();
      }
      $ctr++;
    }
    if (!isset($site)) {
      msg("You need to specify a site!");
      exit();
    }

    if (!isset($domain)) {
      msg("You need to specify a name!");
      exit();
    }

    if ($archhost->domainExists($domain)) {
      msg("That domain exists!");
      exit();
    }
    if (!$archhost->siteExists($site)) {
      msg("That site does not exist!");
      exit();
    }

    $web = $archhost->getSite($site);
    $password = $web->addDomain($domain);
    msg("Domain ".$domain." added to ".$site."!");
  }

}
