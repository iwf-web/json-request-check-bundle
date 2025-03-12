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
use Throwable;

/**
 * Exception thrown when a JSON payload exceeds the maximum allowed size.
 *
 * This exception results in a HTTP 413 Payload Too Large response.
 */
final class PayloadTooLargeException extends HttpException
{
    public const int HTTP_STATUS_CODE = 413; // Payload Too Large

    /**
     * @var int|null The size of the received payload in bytes
     */
    private ?int $receivedLength;

    /**
     * @var int|null The maximum allowed payload size in bytes
     */
    private ?int $allowedLength;

    /**
     * Create a new PayloadTooLargeException.
     *
     * @param int|null $receivedLength The size of the received payload in bytes
     * @param int|null $allowedLength The maximum allowed payload size in bytes
     * @param string|null $message Custom error message (if null, a message will be generated)
     * @param Throwable|null $previous Previous exception
     * @param int $code Error code
     * @param array $headers Additional HTTP headers to include in the response
     */
    public function __construct(
        ?string $message = null,
        array $errorContext = [],
        ?Throwable $previous = null,
        int $code = 0,
        array $headers = []
    ) {
        $this->receivedLength = $errorContext['receivedLength'] ?? null;
        $this->allowedLength = $errorContext['allowedLength'] ?? null;

        $message = $message ?? $this->generateDefaultMessage();

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
            return sprintf(
                'JSON payload too large: %d bytes received, maximum allowed is %d bytes',
                $this->receivedLength,
                $this->allowedLength
            );
        }

        if ($this->receivedLength !== null) {
            return sprintf(
                'JSON payload too large: %d received bytes exceeding maximum allowed bytes',
                $this->receivedLength
            );
        }

        if ($this->allowedLength !== null) {
            return sprintf(
                'JSON payload too large: maximum allowed bytes (%d) exceeded',
                $this->allowedLength
            );
        }

        return 'JSON payload too large';
    }
}