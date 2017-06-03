<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace queue\console;
require_once(dirname(dirname(dirname(__DIR__))) . '/vendor/autoload.php');
use queue\Tools;
use queue\DbQueue;
use queue\RedisQueue;


/**
 * Job queue
 */
class QueueListener
{
  /**
   * @var integer
   * Delay after each step (in seconds)
   */
  public $usleep = 200;

  /**
   * @var integer
   * Delay before running first job in listening mode (in seconds)
   */
  public $_timeout;

  public $_daemonize = false;

  /**
   * @var bool
   * Need restart job if failure or not
   */
  public $restartOnFailure = true;

  /**
   * @var string
   * Queue component ID
   */
  public $queue = 'default_queue';


  public function __construct()
  {
    $options = getopt('', ['queue::']);
    if(!isset($options['queue']))
      $this->queue = self::getDefaultQueueName();
    $this->listen($this->queue);
  }

  /**
   * Process a job
   *
   * @param string $queue
   * @throws \Exception
   */
  public function work($queue)
  {
    $this->process($queue);
  }

  /**
   * Continuously process jobs
   *
   * @param string $queue
   * @return bool
   * @throws \Exception
   */
  public function listen($queue)
  {
    if(class_exists('\swoole_process'))
    {
      $this->sProcess($queue);
    }
    else
    {
      while(true)
      {
        if(!$this->process($queue))
        {
          usleep($this->usleep);
        }
      }
    }
  }

  /**
   * Process one unit of job in queue
   *
   * @param string $queue
   * @return bool
   */
  protected function process($queue)
  {
    usleep($this->usleep);
    $message = $this->getQueue()->pop($queue);
    if($message)
    {
      try
      {
        /** @var \xutl\queue\ActiveJob $job */
        $job = call_user_func($message['body']['serializer'][1], $message['body']['object']);

        if($job->run() || (bool)$this->restartOnFailure === false)
        {
          $this->getQueue()->delete($message);
        }
        else
        {
          $this->getQueue()->release($message, 60);
        }
        return true;
      }
      catch(\Exception $e)
      {
        $this->getQueue()->delete($message);
      }
    }
    return false;
  }

  private $workers = [];

  /**
   * @param $queuqName
   */
  protected function sProcess($queuqName)
  {
    ini_set('memory_limit', '1024M');
    $this->usleep *= 10;
    $config = self::getConfig();
    $workerNum = isset($config['queue']['num']) && $config['queue']['num'] > 0 ? $config['queue']['num'] : 20;
    while($workerNum--)
    {
      $process = new \swoole_process(function(\swoole_process $process) use ($queuqName)
      {
        $this->process($queuqName);
      });
      $pid = $process->start();
      $this->workers[$pid] = $process;
    }
    while(true)
    {
      $ret = \swoole_process::wait();
      if($ret)
      {
        $pid = $ret['pid'];
        $worker = $this->workers[$pid];
        $npid = $worker->start();
        $this->workers[$npid] = $worker;
        unset($this->workers[$pid]);
      }
    }
  }

  private function getQueue()
  {
    return Tools::getQueue();
  }

  public static function getDefaultQueueName()
  {
    $config = self::getConfig();
    return $config['queue']['queueName'];
  }

  public static function getConfig()
  {
    return require(dirname(__DIR__) . '/config.php');
  }
}

new QueueListener();

?>
