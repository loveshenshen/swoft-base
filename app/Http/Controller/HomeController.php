<?php declare(strict_types=1);

namespace App\Http\Controller;

use App\Rpc\Lib\EmailInterface;
use ReflectionException;
use Swoft;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Context\Context;
use Swoft\Http\Message\ContentType;
use Swoft\Http\Message\Response;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\View\Renderer;
use Throwable;
use Swoft\Rpc\Client\Annotation\Mapping\Reference;
/**
 * Class HomeController
 * @Controller()
 */
class HomeController
{
    /**
     * @Reference(pool="user.pool", version="1.0")
     * @var EmailInterface
     */
    public $mailService;
    /**
     * @RequestMapping("/")
     * @throws Throwable
     */
    public function index(): Response
    {
        /** @var Renderer $renderer */
        $renderer = Swoft::getBean('view');
        $content  = $renderer->render('home/index');

        return Context::mustGet()->getResponse()->withContentType(ContentType::HTML)->withContent($content);
    }

    /**
     * @RequestMapping("/hello[/{name}]")
     * @param string $name
     *
     * @return Response
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function hello(string $name): Response
    {
        return Context::mustGet()->getResponse()->withContent('Hello' . ($name === '' ? '' : ", {$name}"));
    }

    /**
     * @RequestMapping("aspect")
     */
    public function aspect():string
    {
         $start =  microtime(true);
          $fib = function($n)use(&$fib){
             if($n <= 1){
                 return 1;
             }
             return $n * $fib($n - 1);
         };
          $result = $fib(100);
          $end = microtime(true);
          return "计算100的阶乘，结果为：{$result};总耗时：".($end - $start);
    }

    /**
     * 测试rpc
     * @RequestMapping("rpc")
     */
    public function rpc(){
       return $this->mailService->sendEmail("956167863@qq.com");
    }

}
