<?php

namespace IWF\JsonRequestCheckBundle\Check;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Request;

#[AutoconfigureTag('iwf.jsonRequestChecker')]
interface JsonRequestCheckerInterface
{
    public function check(Request $request): JsonRequestCheckResult;

    public function supports(Request $request): bool;
}