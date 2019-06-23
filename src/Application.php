<?php

    declare(strict_types=1);

    namespace LoSys;

    use Dotenv\Dotenv;

    final class Application
    {

      private $basePath;
      private $publicPath;

      function __construct (string $basePath)
      {
        // $this->basePath = $basePath;
        // Dotenv::create($this->basePath)->safeLoad();

        echo "hello world";
      }
    }
