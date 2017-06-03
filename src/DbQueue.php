<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace queue;

/**
 * Class DatabaseQueue
 * @package xutl\queue
 */
class DbQueue implements QueueInterface
{
  public $db = 'db';

  /**
   * @var int|null
   */
  protected $expire = null;

  /**
   * @var
   */
  protected $tableName = '';

  /**
   * @inheritdoc
   */
  public function __construct()
  {
      $__CONFIG = require (__DIR__ . '/config.php');
    /**
     * @var  array $__CONFIG
     */
    $this->db = new DbDriver($__CONFIG['db']);
    $this->db->setTableName($__CONFIG['db']['default']['params']['tableName']);
    $this->tableName = $this->db->getTableName();
  }

  /**
   * 推送任务到队列
   * @param mixed $payload
   * @param integer $delay
   * @param string $queue
   * @return string
   */
  public function push(ActiveJob $payload, $queue = null, $delay = 0)
  {
    if(empty($queue))
      $queue = $payload->queueName();
    $payload = json_encode(['payload' => $payload, 'object' => serialize($payload)]);
    $value = [
      ':queue' => $queue,
      ':attempts' => 0,
      ':reserved' => 0,
      ':reserved_at' => null,
      ':payload' => $payload, //Json::encode($payload),
      ':available_at' => time() + $delay,
      ':created_at' => time(),
    ];
    return $this->db->push($this->tableName, $value, '', true);
  }

  /**
   * 从队列弹出消息
   *
   * @param string|null $queue 队列名称
   * @return array|false
   */
  public function pop($queue)
  {
    if(!is_null($this->expire))
    {
      //将发布的消息重新放入队列
      $expired = time() + $this->expire;
      $sql = "UPDATE {$this->tableName} SET reserved=0, reserved_at=null, attempts=attempts+1
              WHERE queue=:queue AND reserved=1 AND reserved_at<=:expired";
      $args = [
        ':queue' => $queue,
        ':expired' => $expired
      ];
      $this->db->query($sql, $args);
    }
    /**
     * @var \PDO $pdo
     */
    $pdo = $this->db->getPdo();
    //准备事务
    try
    {
      $pdo->beginTransaction();
      if(($message = $this->receiveMessage($queue)) != null)
      {
        $sql = "UPDATE {$this->tableName} SET reserved=1, reserved_at=:reserved_at WHERE id=:id";
        $args = [
          ':reserved_at' => time(),
          ':id' => $message['id']
        ];
        $this->db->query($sql, $args, '', false);
        $pdo->commit();
        $pdo = null;
        $payload = json_decode($message['payload'], true);
        $message['body'] = array_merge($payload['payload'], ['object' => $payload['object']]);
        return $message;
      }
      if($pdo != null)
      {
        $pdo->commit();
        $pdo = null;
      }
    }
    catch(\PDOException $exception)
    {
      $pdo->rollBack();
    }
    return null;
  }

  /**
   * @param  string|null $queue
   * @return array|null
   */
  protected function receiveMessage($queue)
  {
    $sql = "SELECT * FROM {$this->tableName} WHERE
           queue=:queue AND reserved=:reserved AND available_at<=:available_at LIMIT 1 for update";
    $args = [
      ':queue' => $queue,
      ':reserved' => 0,
      ':available_at' => time()
    ];
    $message = $this->db->query($sql, $args, '', false);
    return !empty($message) ? current($message) : null;
  }

  /**
   * 清空队列
   *
   * @param string $queue
   */
  public function purge($queue)
  {
    $sql = "DELETE FROM {$this->tableName} WHERE queue=:queue";
    $this->db->query($sql, [':queue' => $queue]);
  }

  /**
   * 发布消息
   *
   * @param array $message
   * @param integer $delay
   */
  public function release(array $message, $delay = 0)
  {
    $sql = "UPDATE {$this->tableName} SET
    available_at=:available_at,
    reserved=:reserved,
    reserved_at=:reserved_at
    WHERE id=:id";
    $args = [
      ':available_at' => time() + $delay,
      ':reserved' => 0,
      ':reserved_at' => null,
      ':id' => $message['id']
    ];
    $this->db->query($sql, $args);
  }

  /**
   * 删除队列消息
   * @param array $message
   */
  public function delete(array $message)
  {
    $sql = "DELETE FROM {$this->tableName} WHERE id=:id";
    $this->db->query($sql, [':id' => $message['id']]);
  }
}
