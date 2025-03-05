<?php
/*
 * This file is part of the IWFJsonRequestCheckBundle package.
 *
 * (c) IWF AG / IWF Web Solutions <info@iwf.ch>
 * Author: Nick Steinwand <n.steinwand@iwf.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace IWF\JsonRequestCheckBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class JsonRequestValidationException extends HttpException
{
    public const HTTP_STATUS_CODE = 400;

    private array $errorContext;

    public function __construct(string $message, array $errorContext = [], \Throwable $previous = null)
    {
        parent::__construct(self::HTTP_STATUS_CODE, $message, $previous);
        $this->errorContext = $errorContext;
    }

    public function getErrorContext(): array
    {
        return $this->errorContext;
    }
}