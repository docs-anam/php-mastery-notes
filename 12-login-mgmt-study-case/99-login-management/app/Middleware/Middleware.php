<?php

namespace Mukhoiran\LoginManagement\Middleware;

interface Middleware
{
    public function before(): void;
}