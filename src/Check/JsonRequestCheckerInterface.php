<?php

namespace IWF\JsonRequestCheckBundle\Check;

use IWF\JsonRequestCheckBundle\Attribute\JsonRequestChecker;
use Symfony\Component\HttpFoundation\Request;

#[JsonRequestChecker]
interface JsonRequestCheckerInterface
{
    public function check(Request $request): JsonRequestCheckResult;

    public function supports(Request $request): bool;
}