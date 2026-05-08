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

/**
 * JSON Request Check Bundle
 *
 * @package   JsonRequestCheckBundle
 * @author    IWF Web Solutions <web-solutions@iwf.ch>
 * @copyright Copyright (c) 2025-2026 IWF Web Solutions <web-solutions@iwf.ch>
 * @license   https://github.com/iwf-web/json-request-check-bundle/blob/main/LICENSE.txt MIT License
 * @link      https://github.com/iwf-web/json-request-check-bundle
 */

namespace IWFWeb\JsonRequestCheckBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class JsonRequestValidationException extends HttpException
{
    public const HTTP_STATUS_CODE = 400;

    /** @var array<string, mixed> */
    private array $errorContext;

    /**
     * @param array<string, mixed> $errorContext
     */
    public function __construct(string $message, array $errorContext = [], ?\Throwable $previous = null)
    {
        parent::__construct(self::HTTP_STATUS_CODE, $message, $previous);
        $this->errorContext = $errorContext;
    }

    /**
     * @return array<string, mixed>
     */
    public function getErrorContext(): array
    {
        return $this->errorContext;
    }
}
