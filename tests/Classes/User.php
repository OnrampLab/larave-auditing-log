<?php

namespace OnrampLab\AuditingLog\Tests\Classes;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as BaseUser;

class User extends BaseUser
{
    use HasFactory;

    protected $fillable = [
        'email',
    ];

    protected static function newFactory(): Factory
    {
        return UserFactory::new();
    }
}
