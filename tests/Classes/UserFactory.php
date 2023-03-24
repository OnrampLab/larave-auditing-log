<?php

namespace OnrampLab\AuditingLog\Tests\Classes;

use Orchestra\Testbench\Factories\UserFactory as BaseUserFactory;

class UserFactory extends BaseUserFactory
{
    protected $model = User::class;
}
