<?php declare(strict_types=1);


namespace App\Http\Controller;

use Exception;
use function sgo;
use Swlib\Http\ContentType;
use Swlib\Saber;
use Swlib\SaberGM;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Bean\BeanFactory;
use Swoft\Consul\KV;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Redis\Exception\RedisException;
use Swoft\Redis\Pool;
use Swoft\Redis\Redis;

/**
 * Class RedisController
 *
 * @since 2.0
 * @Controller("redis")
 */
class RedisController
{

    /**
     * @Inject()
     * @var KV
     */
    public $kv;

    /**
     * @Inject()
     * @var Pool
     */
    private $redis;

    /**
     * @RequestMapping("poolSet")
     */
    public function poolSet(): array
    {
        $key   = 'key';
        $value = uniqid();

        $this->redis->set($key, $value);

        $get = $this->redis->get($key);

        $isError = $this->redis->call(function (\redis $redis) {
            $redis->eval('returnxxxx 1');

            return $redis->getLastError();
        });

        return [$get, $value, $isError];
    }

    /**
     * @RequestMapping()
     */
    public function set(): array
    {
        $key   = 'key';
        $value = uniqid();

        $this->redis->zAdd($key, [
            'add'    => 11.1,
            'score2' => 11.1,
            'score3' => 11.21
        ]);

        $get = $this->redis->sMembers($key);

        return [$get, $value];
    }


    /**
     * @RequestMapping("str")
     */
    public function str(): array
    {
        $key    = 'key';
        $result = Redis::set($key, 'key');

        $keyVal = Redis::get($key);

        $isError = Redis::call(function (\redis $redis) {
            $redis->eval('return 1');

            return $redis->getLastError();
        });

        $data = [
            $result,
            $keyVal,
            $isError,
            1
        ];

        $this->kv->put("/dev/redis/host",'127.0.0.1');
        $this->kv->put("/dev/redis/port",'6379');
        $result = $this->kv->get("/dev/redis/host")->getResult();

        $value = $result[0]['Value'];
        //需要base64解密
        var_dump(base64_decode($value));



        return $data;
    }

    /**
     * Only to use test. The wrong way to use it
     *
     * @RequestMapping("release")
     *
     * @return array
     * @throws RedisException
     */
    public function release(): array
    {
        sgo(function () {
            Redis::connection();
        });

        Redis::connection();

        return ['release'];
    }

    /**
     * Only to use test. The wrong way to use it
     *
     * @RequestMapping("ep")
     *
     * @return array
     */
    public function exPipeline(): array
    {
        sgo(function () {
            Redis::pipeline(function () {
                throw new Exception('');
            });
        });

        Redis::pipeline(function () {
            throw new Exception('');
        });

        return ['exPipeline'];
    }

    /**
     * Only to use test. The wrong way to use it
     *
     * @RequestMapping("et")
     *
     * @return array
     */
    public function exTransaction(): array
    {
        sgo(function () {
            Redis::transaction(function () {
                throw new Exception('');
            });
        });

        Redis::transaction(function () {
            throw new Exception('');
        });

        return ['exPipeline'];
    }


    /**
     * @RequestMapping("http")
     */
    public function http():array
    {
        //带请求头的操作
        $url = "http://localhost:8000/v1/region/province?page=0";
        $saber = Saber::create([
            'base_uri' => $url,
            'headers' => [
                'Accept-Language' => 'en,zh-CN;q=0.9,zh;q=0.8',
                'Content-Type' => ContentType::JSON,
                'DNT' => '1',
                'User-Agent' => null,
                'Joke'=>'shenbasego!',
                'Device'=>'web',
            ]
        ]);
        $result =  $saber->get('/v1/region/province?page=0');
//        $result =   SaberGM::get($url)->withHeaders([
//            'Joke'=>'shenbasego!',
//            'Device'=>'web',
//        ])->getParsedJsonArray();



        return $result->getParsedJsonArray();
    }
}
