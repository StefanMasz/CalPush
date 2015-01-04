<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/Res/constants.php';
require_once 'Classes/Controller/CalpushController.php';

date_default_timezone_set("Europe/Berlin");

$push = new calpushController();
echo $push->updateCalendar();