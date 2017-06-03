<?php

namespace queue;

/**
 * ActiveJob
 * @author Alexander Kochetov <creocoder@gmail.com>
 */
abstract class ActiveJob
{
    /**
     * @var array
     */
    public $serializer = ['serialize', 'unserialize'];

    /**
     * Runs the job.
     */
    abstract public function run();

    /**
     * 检测数据的合法性
     * @return bool
     */
    abstract public function check();

    /**
     * @return string
     */
    public function queueName($queue = null)
    {
      if(empty($queue))
      {
        $config = require(__DIR__ . '/config.php');
        return $config['queue']['queueName'];
      }
      return $queue;
    }

    /**
     * @return QueueInterface
     */
    public function getQueue()
    {
      return Tools::getQueue();
    }

    /**
     * Pushs the job.
     *
     * @param integer $delay
     * @return string
     */
    public function push($delay = 0)
    {
        return $this->getQueue()->push([
            'serializer' => $this->serializer,
            'object' => call_user_func($this->serializer[0], $this),
        ], $this->queueName(), $delay);
    }
}
