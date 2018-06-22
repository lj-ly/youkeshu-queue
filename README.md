### Queue for PHP

> 新增在外部设置队列具体配置方法。

调用任务类中间加一层 `Base` 用来设置自定义的队列配置

```php
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
```

---

默认队列 `基础` 配置：

```php
[
     'class' => 'db',  // 使用哪个驱动
     'queueName' => 'default_queue',  // 队列名称
     'num' => 5, // 跑多少个进程，默认5个,可选值(1-20)
     'attempts' => 5, // 队列运行失败尝试次数
]
```

更改方法：

```php
$this->config->setQueueConfig('num',10);//将队列进程改为10
```

---

默认队列 `Redis` 配置：

```php
[
     'scheme' => 'tcp',
     'host' => '127.0.0.1',
     'port' => 6379,
     //'password' => '123456',
     'db' => 0,
]
```

更改方法：

```php
$this->config->setRedisConfig('port',7777);//将 Redis 端口 改为 7777
```

---

默认队列 `数据库` 配置：

```php
[
     'default' => [
           //数据库连接参数
           'params' => [
             'host'       => '127.0.0.1',  // 数据库连接地址
             'port'       => 3306, // 端口
             'user'       => 'root',  // 用户名
             'password'   => 'Ab123456', // 密码
             'database'   => 'test',  // 数据库名
             'charset'    => 'utf8',  // 编码
             'tableName' => 'sys_queue'  // 队列表名
           ]
         ],
]
```

更改方法：（**建议将数据库配置放入 `Base` 中统一配置**）

```php
$dbConfig = [
    'params' => [
             'host'       => '192.168.13.14',  // 数据库连接地址
             'port'       => 3306, // 端口
             'user'       => 'root',  // 用户名
             'password'   => 'root', // 密码
             'database'   => 'queue',  // 数据库名
             'charset'    => 'utf8',  // 编码
             'tableName' => 'sys_self_queue'  // 队列表名
           ]
];

$this->config->setDBConfig('default',$dbConfig);//将数据库配置改为本地
```
