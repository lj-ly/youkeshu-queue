<?php

namespace queue;

/**
 * Class ActiveJob
 * @package queue
 * @author longli
 */
abstract class ActiveJob
{
  /**
   * 序列化
   * @var array
   */
  public $serializer = ['serialize', 'unserialize'];
  /**
   * @var array|QueueConfig
   */
  public $config = [];

  /**
   * 运行队列
   * @return boolean
   */
  abstract public function run();

  /**
   * 检测数据的合法性
   * @return boolean
   */
  abstract public function check();

  public function __construct()
  {
    $this->config = new QueueConfig();
  }

  /**
   * 获取队列名
   * @return string
   */
  public function queueName($queue = null)
  {
    if(empty($queue))
    {
      return $this->config['queue']['queueName'];
    }
    return $queue;
  }

  /**
   * 获取队列实例
   * @return QueueInterface
   * @throws \Exception
   */
  public function getQueue()
  {
    return Tools::getQueue($this->config);
  }

  /**
   * 重新放入队列
   * @param integer $delay
   * @return string
   * @throws \Exception
   */
  public function push($delay = 0)
  {
    return $this->getQueue()->push([
      'serializer' => $this->serializer,
      'object' => call_user_func($this->serializer[0], $this),
    ], $this->queueName(), $delay);
  }
}
