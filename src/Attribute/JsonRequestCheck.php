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

namespace IWF\JsonRequestCheckBundle\Attribute;

use Attribute;

/**
 * Attribute to limit the maximum JSON content size for a controller action.
 *
 * This attribute is used to protect against HashDos attacks by limiting
 * the size of JSON payloads that can be submitted to a specific endpoint.
 *
 * Example usage:
 * ```php
 * #[Route('/api/data', methods: ['POST'])]
 * #[JsonRequestCheck(maxJsonContentSize: 5120)] // Limit to 5KB
 * public function apiEndpoint(Request $request): Response
 * {
 *     // Your code here...
 * }
 * ```
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class JsonRequestCheck
{
    /**
     * @param int $maxJsonContentSize Maximum allowed JSON content size in bytes
     */
    public function __construct(
        public readonly int $maxJsonContentSize,
    ) {
        if ($maxJsonContentSize <= 0) {
            throw new \InvalidArgumentException('Maximum JSON content size must be a positive integer');
        }
    }

    /**
     * Get the maximum JSON content size.
     */
    public function getMaxJsonContentSize(): int
    {
        return $this->maxJsonContentSize;
    }
}