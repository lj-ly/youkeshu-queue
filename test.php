<?php
/**
 * Created by PhpStorm.
 * User: ycz
 * Date: 2018/06/21
 * Time: 19:00
 */

use demo\DemoJob;

require './vendor/autoload.php';

$demo = new DemoJob();

$demo->config->setDBConfig('aa','111');

$demo->run();