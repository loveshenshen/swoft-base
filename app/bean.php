<?php

use App\Common\DbSelector;
use App\Process\MonitorProcess;
use Swoft\Crontab\Process\CrontabProcess;
use Swoft\Db\Pool;
use Swoft\Http\Server\HttpServer;
use Swoft\Task\Swoole\SyncTaskListener;
use Swoft\Task\Swoole\TaskListener;
use Swoft\Task\Swoole\FinishListener;
use Swoft\Rpc\Client\Client as ServiceClient;
use Swoft\Rpc\Client\Pool as ServicePool;
use Swoft\Rpc\Server\ServiceServer;
use Swoft\Http\Server\Swoole\RequestListener;
use Swoft\WebSocket\Server\WebSocketServer;
use Swoft\Server\SwooleEvent;
use Swoft\Db\Database;
use Swoft\Redis\RedisDb;

return [
    'logger'            => [
        'flushRequest' => false,
        'enable'       => false,
        'json'         => false,
    ],
    'httpServer'        => [
        'class'    => HttpServer::class,
        'port'     => 18306,
        'listener' => [
            'rpc' => bean('rpcServer')
        ],
        'process'  => [
//            'monitor' => bean(MonitorProcess::class)
//            'crontab' => bean(CrontabProcess::class)
        ],
        'on'       => [
//            SwooleEvent::TASK   => bean(SyncTaskListener::class),  // Enable sync task
            SwooleEvent::TASK   => bean(TaskListener::class),  // Enable task must task and finish event
            SwooleEvent::FINISH => bean(FinishListener::class)
        ],
        /* @see HttpServer::$setting */
        'setting'  => [
            'task_worker_num'       => 12,
            'task_enable_coroutine' => true
        ]
    ],
    'httpDispatcher'    => [
        // Add global http middleware
        'middlewares'      => [
            \App\Http\Middleware\FavIconMiddleware::class,
            // \Swoft\Whoops\WhoopsMiddleware::class,
            // Allow use @View tag
            \Swoft\View\Middleware\ViewMiddleware::class,
            \App\Http\Middleware\CorsMiddleware::class,
//            \App\Http\Middleware\AuthMiddleware::class,
//            \App\Http\Middleware\HeaderMiddleware::class,

            \App\Http\Middleware\ResponseMiddleware::class,
        ],
        'afterMiddlewares' => [
            \Swoft\Http\Server\Middleware\ValidatorMiddleware::class
        ]
    ],
    'db'                => [
        'class'    => Database::class,
        'dsn'      => 'mysql:dbname=car;host=114.55.92.151',
        'username' => 'root',
        'password' => 'super!@#',
    ],
    'db.pool'          => [
        'class'    => Pool::class,
        'database' => bean('db')
    ],
    'redis'      => [
        'class'         => \Swoft\Redis\RedisDb::class,
        'host'          => '127.0.0.1',
        'port'          => 6379,
        'database'      => 0,
//        'retryInterval' => 10,
//        'readTimeout'   => 0,
//        'timeout'       => 2,
        'option'        => [
            'prefix'      => '',
            'serializer' => Redis::SERIALIZER_PHP
        ],
    ],
    'redis.pool'     => [
        'class'   => \Swoft\Redis\Pool::class,
        'redisDb' => \bean('redis'),
        'minActive'   => 10,
        'maxActive'   => 20,
        'maxWait'     => 0,
        'maxWaitTime' => 0,
        'maxIdleTime' => 60,
    ],


    'migrationManager'  => [
        'migrationPath' => '@app/Migration',
    ],
    'user'              => [
        'class'   => ServiceClient::class,
        'host'    => '127.0.0.1',
        'port'    => 18307,
        'setting' => [
            'timeout'         => 0.5,
            'connect_timeout' => 1.0,
            'write_timeout'   => 10.0,
            'read_timeout'    => 0.5,
        ],
        'packet'  => bean('rpcClientPacket')
    ],
    'user.pool'         => [
        'class'  => ServicePool::class,
        'client' => bean('user')
    ],
    'rpcServer'         => [
        'class' => ServiceServer::class,
    ],
    'wsServer'          => [
        'class'   => WebSocketServer::class,
        'port'    => 8308,
        'on'      => [
            // Enable http handle
//            SwooleEvent::REQUEST => bean(RequestListener::class),
            SwooleEvent::TASK   => \bean(TaskListener::class),  // Enable task must task and finish event
            SwooleEvent::FINISH => \bean(FinishListener::class)
            /* @see HttpServer::$setting */
        ],
        'debug'   => 1,
        // 'debug'   => env('SWOFT_DEBUG', 0),
        /* @see WebSocketServer::$setting */
        'setting' => [
            'log_file' => alias('@runtime/swoole.log'),
            'task_worker_num'       => 4,
            'task_enable_coroutine' => true
        ],
        'process' => [
            //进程初始化
            'crontab' => bean(Swoft\Crontab\Process\CrontabProcess::class),
            'pubsub' => bean(\App\Process\PubSubProcess::class)
        ],
    ],
    'tcpServer'         => [
        'port'  => 18309,
        'debug' => 1,
    ],
    /** @see \Swoft\Tcp\Protocol */
    'tcpServerProtocol' => [
        // 'type'            => \Swoft\Tcp\Packer\JsonPacker::TYPE,
        'type' => \Swoft\Tcp\Packer\SimpleTokenPacker::TYPE,
        // 'openLengthCheck' => true,
    ],
    'cliRouter'         => [
        // 'disabledGroups' => ['demo', 'test'],
    ],
    'consul' => [
        'host' => '127.0.0.1'
    ],
];
