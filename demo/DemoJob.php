<?php
/**
 * Created by PhpStorm.
 * User: ycz
 * Date: 2018/06/21
 * Time: 18:56
 */

namespace demo;


class DemoJob extends BaseJob
{

  /**
   * 运行队列
   * @return boolean
   */
  public function run()
  {
    print_r($this->config);
    // TODO: Implement run() method.
  }

  /**
   * 检测数据的合法性
   * @return boolean
   */
  public function check()
  {
    // TODO: Implement check() method.
  }
}

