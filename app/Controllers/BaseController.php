<?php
declare(strict_types=1);
namespace App\Controllers;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

abstract class BaseController
{
    protected Connection $database;
    public function __construct()
    {
        $connectionParams = [
            'dbname' => 'databasearticles',
            'user' => 'root',
            'host'=> 'localhost',
            'driver'=> 'pdo_mysql',
        ];
        $this ->database = DriverManager::getConnection($connectionParams);
    }
}