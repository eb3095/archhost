<?php

require_once("lib/SecureMySQL.php");
require_once("config.php");
require_once("classes/Applications.php");
require_once("classes/ArchHost.php");
require_once("classes/Database.php");
require_once("classes/Domain.php");
require_once("classes/Pool.php");
require_once("classes/Site.php");
require_once("classes/User.php");
require_once("commands/adduser.php");
require_once("commands/removeuser.php");
require_once("commands/addsite.php");
require_once("commands/removesite.php");
require_once("commands/enablesite.php");
require_once("commands/disablesite.php");
require_once("commands/adddomain.php");
require_once("commands/removedomain.php");
require_once("commands/adddatabase.php");
require_once("commands/removedatabase.php");
require_once("commands/addpool.php");
require_once("commands/removepool.php");
require_once("commands/getpoolfree.php");
require_once("commands/getpoolsfree.php");
require_once("commands/getsitefree.php");
require_once("commands/getsitesfree.php");
require_once("commands/mountsites.php");
require_once("commands/changedatabasepassword.php");
require_once("commands/regenerate.php");

// Applications
require_once("applications/Wordpress.php");
