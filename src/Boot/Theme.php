<?php

  // TODO: refactor this Environment stuff
  // TODO: clean up - what can we “magic“ and what needs to be moved to the theme?
  // TODO: use env function for ENV TYPE

  namespace LoSys\Boot;

  #use app\services\Environment;

  class Theme
  {
    private $env;

    public static function boot ()
    {
      #$this->env = new Environment;
      self::editor_menu_priviledges();


      add_action('admin_init', [__CLASS__, 'admin_init']);
      add_action('after_setup_theme', [__CLASS__, 'after_setup_theme']);
      add_action('wp_head', [__CLASS__, 'wp_head']);

      // Preservers ACF Serial Number
      add_filter( 'wpmdb_preserved_options', [__CLASS__, 'wpmdb_preserved_options']);

      // Sets site as public/private
      add_action('init', [__CLASS__, 'init']);
    }

    private static function editor_menu_priviledges ()
    {
      // get the the role object
      $role_object = get_role( 'editor' );
      // add $cap capability to this role object
      $role_object->add_cap( 'edit_theme_options' );
    }

    public static function init ()
    {
      if(ENV_TYPE == 'staging' && get_option('blog_public') == '1') {
        update_option('blog_public', '0');
      }

      if(ENV_TYPE == 'production' && get_option('blog_public') == '0'){
        update_option('blog_public', '1');
      }
    }

    public static function admin_init ()
    {
      global $wp_rewrite;
      $pattern = '/%postname%/';
      $wp_rewrite->set_permalink_structure($pattern);
    }

    public static function after_setup_theme ()
    {
      // Show the admin bar.
      show_admin_bar(false);

      // Add post thumbnails support.
      add_theme_support('post-thumbnails');

      // Add title tag theme support.
      add_theme_support('title-tag');

      // Add HTML5 support.
      add_theme_support('html5', [
        'caption',
        'comment-form',
        'comment-list',
        'gallery',
        'search-form',
        'widgets',
      ]);
    }

    public static function wp_head ()
    {
?>
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta charset="utf-8">
<?php
    }

    public static function wpmdb_preserved_options ($options)
    {
      $options[] = 'acf_pro_license';
      return $options;
    }
  }
