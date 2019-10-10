<?php
namespace App\Exception;

/**
 * Class ApiException
 *
 * @since 2.0
 */
class ApiException extends \Exception
{

    /**
     * ApiException constructor.
     * @param string $message
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct($message = "", $code = 500, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
