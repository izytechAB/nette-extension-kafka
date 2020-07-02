<?PHP
require __DIR__ . '/../vendor/autoload.php';

Tester\Environment::setup();
date_default_timezone_set('Europe/Stockholm');

define('TMP_DIR', '/tmp/tests');