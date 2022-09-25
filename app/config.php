<?php

use Api\Services\Auth\DbUserProvider;
use Api\Services\Generator\CodeGeneratorStrategy;
use Api\Services\Generator\NumbersGeneratorStrategy;
use Api\Services\SmsProvider\DbMockService;
use Api\Services\SmsProvider\SmsProviderService;
use Core\Auth\Providers\UserProvider;

return [
    'path' => __DIR__,

    'db' => [
        'host'     => getenv('MYSQL_HOST') ?? 'localhost',
        'dbname'   => getenv('MYSQL_DATABASE') ?? 'otp',
        'username' => getenv('MYSQL_USER') ?? 'otp',
        'password' => getenv('MYSQL_PASSWORD') ?? 'otp',
        'charset'  => 'utf8'
    ],

    'api' => [
        'path_prefix'                => '/var/app/api/',
        'code_generation_throttle'   => 60, //1 min
        'code_verification_throttle' => 60, //1 min
        'code_verification_attempts' => 3,
    ],

    //dependency injection definitions
    'di'  => [
        CodeGeneratorStrategy::class => NumbersGeneratorStrategy::class,
        SmsProviderService::class    => DbMockService::class,
        UserProvider::class          => DbUserProvider::class
    ],

    'jwt' => [
        'cipher'         => 'AES-256-CBC',
        'encryption_key' => getenv('APP_KEY') ?: 'otp_app_key',
        'token_lifetime' => 1800 //30 minutes
    ],
];
