<?php
define("TEST",true);
define("DEVELOPMENT",false);
define("PRODUCTION",false);
define("ATK14_DOCUMENT_ROOT",__DIR__ . "/");
define("ATK14_SMARTY_DIR_PERMS",0x755);
define("ATK14_SMARTY_FILE_PERMS",0x644);
define("ATK14_SMARTY_FORCE_COMPILE",true);
define("ATK14_USE_SMARTY3",true);
define("TEMP",__DIR__ . "/tmp/");

$ATK14_GLOBAL = new Atk14Global();

require(__DIR__ . "/functions.php");
require(__DIR__ . "/iobject.php");
require(__DIR__ . "/image.php");
require(__DIR__ . "/../src/app/helpers/modifier.markdown.php");
require(__DIR__ . "/../src/app/helpers/modifier.safe_markdown.php");
require(__DIR__ . "/../src/app/helpers/block.markdown.php");
require(__DIR__ . "/../src/app/helpers/block.safe_markdown.php");
require(__DIR__ . "/../vendor/atk14/core/src/atk14_smarty_utils.php");
require(__DIR__ . "/../vendor/autoload.php");
