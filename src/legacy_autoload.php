<?php

/**
 * Backward-compatibility shim for the legacy IWF\JsonRequestCheckBundle namespace.
 *
 * Resolves any reference to IWF\JsonRequestCheckBundle\* (the package's pre-rename
 * namespace) to the equivalent IWFWeb\JsonRequestCheckBundle\* class via class_alias.
 * Existing consumers that imported the old FQN keep working without code changes.
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

spl_autoload_register(static function (string $class): void {
    $legacyPrefix = 'IWF\JsonRequestCheckBundle\\';

    if (!str_starts_with($class, $legacyPrefix)) {
        return;
    }

    $target = 'IWFWeb\JsonRequestCheckBundle\\'.substr($class, strlen($legacyPrefix));

    if (class_exists($target) || interface_exists($target) || trait_exists($target)) {
        class_alias($target, $class);
    }
});
