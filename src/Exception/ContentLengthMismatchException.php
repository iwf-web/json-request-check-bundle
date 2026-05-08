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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class ContentLengthMismatchException extends HttpException
{
    /**
     * @param array<string, string> $headers
     */
    public function __construct(
        string $message = 'Content-Length header does not match actual content length',
        ?\Throwable $previous = null,
        array $headers = [],
        ?int $code = 0,
    ) {
        parent::__construct(Response::HTTP_BAD_REQUEST, $message, $previous, $headers, $code);
    }
}
