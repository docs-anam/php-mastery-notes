<?php

namespace Mukhoiran\MVCProject\Middleware;

interface Middleware
{
    public function before(): void;
}