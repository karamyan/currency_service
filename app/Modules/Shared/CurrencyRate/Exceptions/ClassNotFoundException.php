<?php
declare(strict_types=1);

namespace App\Modules\Shared\CurrencyRate\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * ClassNotFoundException exception class.
 */
class ClassNotFoundException extends Exception
{
    /**
     * ClassNotFoundException constructor.
     *
     * @param string $message
     * @param int $code
     * @param array $errors
     * @param Throwable|null $previous
     */
    public function __construct(string $message = "", int $code = Response::HTTP_BAD_REQUEST, array $errors = [], Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
