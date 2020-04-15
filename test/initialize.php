<?php
define("TEST",true);
define("DEVELOPMENT",false);
define("PRODUCTION",false);
define("ATK14_DOCUMENT_ROOT",__DIR__ . "/");

define("ATK14_USE_SMARTY3",true);
define("ATK14_SMARTY_DEFAULT_MODIFIER",'h');
define("ATK14_SMARTY_DIR_PERMS",0755);
define("ATK14_SMARTY_FILE_PERMS",0644);
define("ATK14_SMARTY_FORCE_COMPILE",true);

define("TEMP",__DIR__ . "/tmp/");
Files::Mkdir(__DIR__ . "/tmp/smarty/".posix_getpid()."/templates_c");
Files::Mkdir(__DIR__ . "/tmp/smarty/".posix_getpid()."/cache");


$ATK14_GLOBAL = new Atk14Global();

require(__DIR__ . "/iobject.php");
require(__DIR__ . "/image.php");
require(__DIR__ . "/../src/app/helpers/modifier.markdown.php");
require(__DIR__ . "/../src/app/helpers/modifier.safe_markdown.php");
require(__DIR__ . "/../src/app/helpers/block.markdown.php");
require(__DIR__ . "/../src/app/helpers/block.safe_markdown.php");
require(__DIR__ . "/../vendor/atk14/core/src/atk14_smarty_utils.php");
require(__DIR__ . "/../vendor/autoload.php");
