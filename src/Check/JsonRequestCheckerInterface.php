<?php

/**
 * JSON Request Check Bundle
 *
 * @package   JsonRequestCheckBundle
 * @author    IWF Web Solutions <web-solutions@iwf.ch>
 * @copyright Copyright (c) 2025-2026 IWF Web Solutions <web-solutions@iwf.ch>
 * @license   https://github.com/iwf-web/json-request-check-bundle/blob/main/LICENSE.txt MIT License
 * @link      https://github.com/iwf-web/json-request-check-bundle
 */

namespace IWFWeb\JsonRequestCheckBundle\Check;

use IWFWeb\JsonRequestCheckBundle\Attribute\JsonRequestChecker;
use Symfony\Component\HttpFoundation\Request;

#[JsonRequestChecker]
interface JsonRequestCheckerInterface
{
    public function check(Request $request): JsonRequestCheckResult;

    public function supports(Request $request): bool;
}
