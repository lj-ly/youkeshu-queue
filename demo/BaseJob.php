<?php
/**
 * Created by PhpStorm.
 * User: ycz
 * Date: 2018/06/21
 * Time: 18:52
 */

namespace demo;


use queue\ActiveJob;

abstract class BaseJob extends ActiveJob
{
  public function __construct()
  {
    parent::__construct();
    //设置队列配置
    $this->config->setDBConfig('default', []);
  }
}