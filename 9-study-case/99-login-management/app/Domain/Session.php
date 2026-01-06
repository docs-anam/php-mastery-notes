<?php

namespace Mukhoiran\LoginManagement\Domain;

class Session
{
    public ?string $session_token = null;
    public ?string $username = null;
}