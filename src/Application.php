<?php

    declare(strict_types=1);

    // TODO: rework this so i can understand it
    // TODO: not sure if i need all this public path stuff

    namespace LoSys;

    use Dotenv\Dotenv;
    use Symfony\Component\HttpFoundation\Request;

    final class Application
    {

      private $basePath;
      private $publicPath;

      function __construct (string $basePath)
      {
        $this->basePath = $basePath;
        Dotenv::create($this->basePath)->safeLoad();
      }

      public function run ()
      {
        // For developers: WordPress debugging mode.
        $debug = getenv('WP_DEBUG', false);
        define('WP_DEBUG', $debug);
        define('WP_DEBUG_LOG', getenv('WP_DEBUG_LOG', false));
        define('WP_DEBUG_DISPLAY', getenv('WP_DEBUG_DISPLAY', $debug));
        define('SCRIPT_DEBUG', getenv('SCRIPT_DEBUG', $debug));
        // The database configuration with database name, username, password,
        // hostname charset and database collae type.
        define('DB_NAME', getenv('DB_NAME'));
        define('DB_USER', getenv('DB_USER'));
        define('DB_PASSWORD', getenv('DB_PASSWORD'));
        define('DB_HOST', getenv('DB_HOST'));
        define('DB_CHARSET', getenv('DB_CHARSET', 'utf8mb4'));
        define('DB_COLLATE', getenv('DB_COLLATE', 'utf8mb4_unicode_ci'));
        // Detect HTTPS behind a reverse proxy or a load balancer.
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            $_SERVER['HTTPS'] = 'on';
        }
        // Set the unique authentication keys and salts.
        define('AUTH_KEY', getenv('AUTH_KEY'));
        define('SECURE_AUTH_KEY', getenv('SECURE_AUTH_KEY'));
        define('LOGGED_IN_KEY', getenv('LOGGED_IN_KEY'));
        define('NONCE_KEY', getenv('NONCE_KEY'));
        define('AUTH_SALT', getenv('AUTH_SALT'));
        define('SECURE_AUTH_SALT', getenv('SECURE_AUTH_SALT'));
        define('LOGGED_IN_SALT', getenv('LOGGED_IN_SALT'));
        define('NONCE_SALT', getenv('NONCE_SALT'));
        // Set the home url to the current domain.
        $request = Request::createFromGlobals();
        define('WP_HOME', getenv('WP_URL', $request->getSchemeAndHttpHost()));
        // Set the WordPress directory path.
        define('WP_SITEURL', getenv('WP_SITEURL', sprintf('%s/%s', WP_HOME, getenv('WP_DIR', 'cms'))));
        // Set the WordPress content directory path.
        define('WP_CONTENT_DIR', getenv('WP_CONTENT_DIR', $this->getPublicPath()));
        define('WP_CONTENT_URL', getenv('WP_CONTENT_URL', WP_HOME));
        // Set the WordPress plugin directory path.
        define('WP_PLUGIN_DIR', getenv('WP_PLUGIN_DIR', $this->getPublicPath() .DIRECTORY_SEPARATOR. 'extensions'));
        define('WP_PLUGIN_URL', getenv('WP_PLUGIN_URL', 'extensions'));
        // Set the WordPress uploads directory path.
        define('UPLOADS', getenv('UPLOADS', $this->getPublicPath() .DIRECTORY_SEPARATOR. 'media'));
        // Set the trash to less days to optimize WordPress.
        define('EMPTY_TRASH_DAYS', getenv('EMPTY_TRASH_DAYS', 7));
        // Set the default WordPress theme.
        define('WP_DEFAULT_THEME', getenv('WP_THEME', 'LoSys'));
        // Constant to configure core updates.
        define('WP_AUTO_UPDATE_CORE', getenv('WP_AUTO_UPDATE_CORE', 'minor'));
        // Specify the number of post revisions.
        define('WP_POST_REVISIONS', getenv('WP_POST_REVISIONS', 2));
        // Cleanup WordPress image edits.
        define('IMAGE_EDIT_OVERWRITE', getenv('IMAGE_EDIT_OVERWRITE', true));
        // Prevent file edititing from the dashboard.
        define('DISALLOW_FILE_EDIT', getenv('DISALLOW_FILE_EDIT', true));
        // Set the absolute path to the WordPress directory.
        if (!defined('ABSPATH')) {
          define('ABSPATH', sprintf('%s/%s/', $this->getPublicPath(), getenv('WP_DIR', 'cms')));
        }
      }

      public function getBasePath(): string
      {
        return $this->basePath;
      }

      public function getPublicPath(): string
      {
        if (is_null($this->publicPath)) {
          return $this->basePath;
        }

        return $this->publicPath;
      }

      public function setPublicPath(string $publicPath)
      {
          $this->publicPath = $publicPath;
      }
    }
