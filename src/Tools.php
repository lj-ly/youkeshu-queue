<?php

namespace queue;

/**
 * Class Tools
 * @package queue
 * @author longli
 */
class Tools
{
  /**
   * 获取队列
   * @return QueueInterface
   * @throws \Exception
   */
  public static function getQueue(QueueConfig $config)
  {
    switch($config->getConfig('queue.class'))
    {
      case 'db':
        return (new DbQueue($config));
      case 'redis';
        return (new RedisQueue($config));
      default:
        throw new \Exception("配置有误");
    }
  }
}
