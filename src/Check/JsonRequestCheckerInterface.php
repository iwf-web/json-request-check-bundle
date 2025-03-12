<?php

namespace IWF\JsonRequestCheckBundle\Check;

use Symfony\Component\HttpFoundation\Request;

interface JsonRequestCheckerInterface
{
    public function check(Request $request): JsonRequestCheckResult;

    public function supports(Request $request): bool;
}