<?php

namespace Mukhoiran\LoginManagement\Model;

class UserPasswordUpdateRequest
{
    public ?string $username = null;
    public ?string $oldPassword = null;
    public ?string $newPassword = null;
}