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

/**
 * Exception thrown when a JSON payload exceeds the maximum allowed size.
 *
 * This exception results in a HTTP 413 Payload Too Large response.
 */
final class PayloadTooLargeException extends HttpException
{
    public const HTTP_STATUS_CODE = 413; // Payload Too Large

    /**
     * @var null|int The size of the received payload in bytes
     */
    private ?int $receivedLength;

    /**
     * @var null|int The maximum allowed payload size in bytes
     */
    private ?int $allowedLength;

    /**
     * Create a new PayloadTooLargeException.
     *
     * @param null|string           $message      Custom error message (if null, a message will be generated)
     * @param array<string, mixed>  $errorContext Context describing the violation; reads `receivedLength` and `allowedLength` keys
     * @param null|\Throwable       $previous     Previous exception
     * @param int                   $code         Error code
     * @param array<string, string> $headers      Additional HTTP headers to include in the response
     */
    public function __construct(
        ?string $message = null,
        array $errorContext = [],
        ?\Throwable $previous = null,
        int $code = 0,
        array $headers = [],
    ) {
        $this->receivedLength = $errorContext['receivedLength'] ?? null;
        $this->allowedLength = $errorContext['allowedLength'] ?? null;

        $message ??= $this->generateDefaultMessage();

        parent::__construct(self::HTTP_STATUS_CODE, $message, $previous, $headers, $code);
    }

    /**
     * Get the size of the received payload in bytes.
     */
    public function getReceivedLength(): ?int
    {
        return $this->receivedLength;
    }

    /**
     * Get the maximum allowed payload size in bytes.
     */
    public function getAllowedLength(): ?int
    {
        return $this->allowedLength;
    }

    /**
     * Generate a default error message based on available information.
     */
    private function generateDefaultMessage(): string
    {
        if ($this->receivedLength !== null && $this->allowedLength !== null) {
            return \sprintf(
                'JSON payload too large: %d bytes received, maximum allowed is %d bytes',
                $this->receivedLength,
                $this->allowedLength,
            );
        }

        if ($this->receivedLength !== null) {
            return \sprintf(
                'JSON payload too large: %d received bytes exceeding maximum allowed bytes',
                $this->receivedLength,
            );
        }

        if ($this->allowedLength !== null) {
            return \sprintf(
                'JSON payload too large: maximum allowed bytes (%d) exceeded',
                $this->allowedLength,
            );
        }

        return 'JSON payload too large';
    }
}
