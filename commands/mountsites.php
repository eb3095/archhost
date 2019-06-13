<?php

class mountsites {

  public function run($archhost, $argv){
    foreach ($archhost->getSites() as $site) {
      $site->mount();
    }
    msg("Sites have been mounted!");
  }

}
