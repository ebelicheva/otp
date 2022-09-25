<?php

declare(strict_types=1);

namespace Core;

use Core\Db\Connection;
use Core\Router\Router;

class App
{
    protected Config $config;

    public function __construct(Config|array $config)
    {
        if (is_array($config)) {
            $config = Config::getInstance()->setFromArray($config);
        }

        $this->config = $config;
    }

    public function init(): App
    {
        foreach ($this->config->getAllOptions() as $section => $settings) {
            $method = 'init' . ucfirst($section);
            if (method_exists($this, $method)) {
                $this->$method((array)$settings);
            }
        }

        return $this;
    }

    /**
     * @param array $options
     * @return Connection
     */
    public function initDb(array $options): Connection
    {
        $connection = Connection::getInstance();
        $connection->connect($options);

        return $connection;
    }

    /**
     * @return Router
     */
    public function getRouter(): Router
    {
        $requestData = [...$_GET, ...$_POST];

        if (($_SERVER['CONTENT_TYPE'] ?? '') === 'application/json') {
            $jsonData = file_get_contents('php://input');

            try {
                $json = json_decode($jsonData, true, 512, JSON_THROW_ON_ERROR);

                if ($json) {
                    $requestData = [...$requestData, ...$json];
                }
            } catch (\Throwable $e) {
                //TODO: log
            }
        }

        return new Router(
            $_SERVER['REDIRECT_URL'] ?? '/',
            $_SERVER['REQUEST_METHOD'] ?? 'get',
            $requestData,
            getallheaders()
        );
    }
}
