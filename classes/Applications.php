<?php

class Applications {

  public function getApplication($app) {
    switch ($app) {
      case "wordpress":
        return new Wordpress();
    }
  }

}
