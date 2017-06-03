<?php


return $__CONFIG = [
  'queue' => [
    'class' => 'db',
    'queueName' => 'default_queue',
  ],
  'redis' => [
    'scheme' => 'tcp',
    'host' => '127.0.0.1',
    'port' => 6379,
    //'password' => '123456',
    'db' => 0,
  ],
  //数据库连接池
  'db' => [
    'default' => [
      //数据库连接参数
      'params' => [
        'host'       => 'amazondbm.kokoerp.com',
        'port'       => 3306,
        'user'       => 'amazon',
        'password'   => 'z7D1fnFkOA4WDGNW',
        'database'   => 'amazon',
        'charset'    => 'utf8',
        'tableName' => 'adv_sys_queue'
      ]
    ],
  ],
];