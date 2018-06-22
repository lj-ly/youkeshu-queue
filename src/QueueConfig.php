<?php
/**
 * Created by PhpStorm.
 * User: ycz
 * Date: 2018/06/21
 * Time: 17:22
 */

namespace queue;


use ArrayAccess;

class QueueConfig implements ArrayAccess
{
  /**
   * @var array
   */
  public $config = [];

  /**
   * QueueConfig constructor.
   */
  public function __construct()
  {
    $this->config = require(__DIR__ . '/config.php');
  }


  /**
   * 设置队列配置，配置详情参考 config.php => queue
   *
   * @param $key
   * @param $value
   * @return QueueConfig
   * @date 2018/06/22
   * @author ycz
   */
  public function setQueueBaseConfig($key, $value)
  {
    return $this->set($key, $value, 'queue');
  }

  /**
   * 设置 Redis 配置，配置详情参考 config.php => redis
   *
   * @param $key
   * @param $value
   * @return QueueConfig
   * @date 2018/06/22
   * @author ycz
   */
  public function setRedisConfig($key, $value)
  {
    return $this->set($key, $value, 'redis');
  }

  /**
   * 设置数据库配置， 配置详情参考 config.php => db
   *
   * @param $key
   * @param $value
   * @return QueueConfig
   * @date 2018/06/22
   * @author ycz
   */
  public function setDBConfig($key, $value)
  {
    return $this->set($key, $value, 'db');
  }


  /**
   * 获取配置 key  取多层级的值  key => 'db.default.params.host'
   *
   * @param null $key
   * @return array|mixed
   * @date 2018/06/22
   * @author ycz
   */
  public function getConfig($key = null)
  {
    if($key === null)
    {
      return $this->config;
    }
    $value = $this->config;

    foreach(explode('.', $key) as $item)
    {
      if(isset($value[$item]))
      {
        $value = &$value[$item];
      }
    }

    return $value;
  }

  /**
   * toArray
   *
   * @return array
   * @date 2018/06/22
   * @author ycz
   */
  public function toArray()
  {
    return $this->getConfig();
  }

  /**
   * 设置配置参数
   *
   * @param $key
   * @param $value
   * @param string $label
   * @return $this
   * @date 2018/06/22
   * @author ycz
   */
  private function set($key, $value, $label = '')
  {
    if($label === '')
    {
      $this->config[$key] = $value;

      return $this;
    }

    $this->config[$label][$key] = $value;

    return $this;
  }

  /**
   * Whether a offset exists
   * @link http://php.net/manual/en/arrayaccess.offsetexists.php
   * @param mixed $offset <p>
   * An offset to check for.
   * </p>
   * @return boolean true on success or false on failure.
   * </p>
   * <p>
   * The return value will be casted to boolean if non-boolean was returned.
   * @since 5.0.0
   */
  public function offsetExists($offset)
  {
    return !is_null($this->getConfig($offset));
  }

  /**
   * Offset to retrieve
   * @link http://php.net/manual/en/arrayaccess.offsetget.php
   * @param mixed $offset <p>
   * The offset to retrieve.
   * </p>
   * @return mixed Can return all value types.
   * @since 5.0.0
   */
  public function offsetGet($offset)
  {
    return $this->getConfig($offset);
  }

  /**
   * Offset to set
   * @link http://php.net/manual/en/arrayaccess.offsetset.php
   * @param mixed $offset <p>
   * The offset to assign the value to.
   * </p>
   * @param mixed $value <p>
   * The value to set.
   * </p>
   * @return void
   * @since 5.0.0
   */
  public function offsetSet($offset, $value)
  {
    $this->set($offset, $value);
  }

  /**
   * Offset to unset
   * @link http://php.net/manual/en/arrayaccess.offsetunset.php
   * @param mixed $offset <p>
   * The offset to unset.
   * </p>
   * @return void
   * @since 5.0.0
   */
  public function offsetUnset($offset)
  {
    $this->set($offset, null);
  }
}

