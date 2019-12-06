<?php declare(strict_types=1);

namespace App\Exception\Handler;

use App\Exception\ApiException;
use App\Exception\UserException;
use App\Exception\InvalidTokenHttpException;
use App\Exception\NeedLoginHttpException;
use App\Exception\UnauthorizedHttpException;
use Swoft\Error\Annotation\Mapping\ExceptionHandler;
use Swoft\Http\Message\Response;
use Swoft\Http\Server\Exception\Handler\AbstractHttpErrorHandler;
use Swoft\Limiter\Exception\RateLImiterException;
use Throwable;


/**
 * Class ApiExceptionHandler
 *
 * @since 2.0
 *
 * @ExceptionHandler({RateLImiterException::class,ApiException::class,UserException::class,InvalidTokenHttpException::class,NeedLoginHttpException::class,UnauthorizedHttpException::class})
 */
class ApiExceptionHandler extends AbstractHttpErrorHandler
{
    /**
     * @param Throwable $except
     * @param Response  $response
     *
     * @return Response
     */
    public function handle(Throwable $except, Response $response): Response
    {
        $data = [
            'code'  => $except->getCode() != 0 ?$except->getCode():500,
            'message' => $except->getMessage(),
            'data'=>''
        ];
        return $response->withData($data);
    }
}
