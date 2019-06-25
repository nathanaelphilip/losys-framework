<?php

  // TODO: refactor this Environment stuff
  // TODO: clean up - what can we “magic“ and what needs to be moved to the theme?
  // TODO: use env function for ENV TYPE

  namespace app\services\setup;

  use app\services\Environment;

  class Theme
  {
    private $env;

    public function __construct ()
    {
      $this->env = new Environment;
      $this->editor_menu_priviledges();


      add_action('admin_init', [$this, 'admin_init']);
      add_action('after_setup_theme', [$this, 'after_setup_theme']);
      add_action('wp_head', [$this, 'wp_head']);

      // Preservers ACF Serial Number
      add_filter( 'wpmdb_preserved_options', [$this, 'wpmdb_preserved_options']);

      // Sets site as public/private
      add_action('init', [$this, 'init']);
    }

    private function editor_menu_priviledges ()
    {
      // get the the role object
      $role_object = get_role( 'editor' );
      // add $cap capability to this role object
      $role_object->add_cap( 'edit_theme_options' );
    }

    public function init ()
    {
      if(ENV_TYPE == 'staging' && get_option('blog_public') == '1') {
        update_option('blog_public', '0');
      }

      if(ENV_TYPE == 'production' && get_option('blog_public') == '0'){
        update_option('blog_public', '1');
      }
    }

    public function admin_init ()
    {
      global $wp_rewrite;
      $pattern = '/%postname%/';
      $wp_rewrite->set_permalink_structure($pattern);
    }

    public function after_setup_theme ()
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

    public function wp_head ()
    {
?>
      <link rel="icon" href="<?= $this->env->tmpltd() ?>/assets/images/favicon.svg " />
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta charset="utf-8">
<?php
    }

    public function wpmdb_preserved_options ($options)
    {
      $options[] = 'acf_pro_license';
      return $options;
    }
  }
