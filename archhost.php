<?php

require_once("includes.php");

$archhost = new ArchHost($mysql);

function msg($string) {
  echo $string."\n";
}

// Throwaway first
array_shift($argv);

// Command Routing
$command = array_shift($argv);
switch ($command) {
  case "adduser":
    $commd = new adduser();
    break;
  case "removeuser":
    $commd = new removeuser();
    break;
  case "addsite":
    $commd = new addsite();
    break;
  case "removesite":
    $commd = new removesite();
    break;
  case "enablesite":
    $commd = new enablesite();
    break;
  case "disablesite":
    $commd = new disablesite();
    break;
  case "adddomain":
    $commd = new adddomain();
    break;
  case "removedomain":
    $commd = new removedomain();
    break;
  case "adddatabase":
    $commd = new adddatabase();
    break;
  case "removedatabase":
    $commd = new removedatabase();
    break;
  case "addpool":
    $commd = new addpool();
    break;
  case "removepool":
    $commd = new removepool();
    break;
  case "getpoolfree":
    $commd = new getpoolfree();
    break;
  case "getpoolsfree":
    $commd = new getpoolsfree();
    break;
  case "getsitefree":
    $commd = new getsitefree();
    break;
  case "getsitesfree":
    $commd = new getsitesfree();
    break;
  case "mountsites":
    $commd = new mountsites();
    break;
  case "changedatabasepassword":
    $commd = new changedatabasepassword();
    break;
  case "regenerate":
    $commd = new regenerate();
    break;
  default:
    msg("This is not a command: ".$command."!");
    exit();
}
if (isset($commd)) {
  $commd->run($archhost, $argv);
}
