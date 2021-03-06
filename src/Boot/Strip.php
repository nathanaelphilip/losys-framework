<?php

  namespace LoSys\Boot;

  class Strip {
    public static function boot () {
      add_action( 'init', [__CLASS__, 'init']);

      add_filter( 'intermediate_image_sizes_advanced', [__CLASS__, 'disable_remove_default_images'] );
      add_filter( 'jpeg_quality', [__CLASS__, 'disable_jpeg_quality'] );
      add_filter( 'tiny_mce_plugins', [__CLASS__, 'disable_emojis_tinymce'] );
      add_filter( 'wp_resource_hints', [__CLASS__, 'disable_emojis_remove_dns_prefetch'], 10, 2 );
      add_filter( 'emoji_svg_url', '__return_false' );

      add_action('admin_head', [__CLASS__, 'admin_head']);

      add_action('admin_menu', [__CLASS__, 'admin_menu']);
    }

    public static function init ()
    {
      remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
      remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
      remove_action( 'wp_print_styles', 'print_emoji_styles' );
      remove_action( 'admin_print_styles', 'print_emoji_styles' );
      remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
      remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
      remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
      remove_action( 'wp_head',  'rest_output_link_wp_head' );
      remove_action( 'wp_head',  'wp_oembed_add_discovery_links' );
      remove_action( 'template_redirect', 'rest_output_link_header', 11, 0 );
      remove_action('wp_head', 'wp_generator');
      remove_action ('wp_head', 'rsd_link'); # put back if client is editing via 3rd party
      remove_action( 'wp_head', 'wlwmanifest_link');
      remove_action( 'wp_head', 'wp_shortlink_wp_head');
    }

    public static function disable_jpeg_quality ()
    {
      return 100;
    }

    public static function disable_remove_default_images ( $sizes ) {
      unset( $sizes['thumbnail']); // 150px
      unset( $sizes['medium']); // 300px
      unset( $sizes['large']); // 1024px
      unset( $sizes['medium_large']); // 768px
      return $sizes;
    }

    public static function disable_emojis_tinymce( $plugins ) {
      if ( is_array( $plugins ) ) {
        return array_diff( $plugins, ['wpemoji'] );
      } else {
        return [];
      }
    }

    public static function disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
      if ( 'dns-prefetch' == $relation_type ) {
        $emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );
        $urls = array_diff( $urls, [$emoji_svg_url] );
      }

      return $urls;
    }

    public static function admin_head () {
      remove_action('admin_notices', 'update_nag', 3);
    }

    public static function admin_menu () {
      $items = [
        //'themes.php', // appearance
        'edit-comments.php', // comments
        'index.php', // dashboard
        'link-manager.php', // links
      ];

      foreach ($items as $item) {
        remove_menu_page($item);
      }

      $items = [
        ['themes.php', 'theme-editor.php'],
        ['themes.php', 'customize.php'],
        ['plugins.php', 'plugin-install.php'],
        ['plugins.php', 'plugin-editor.php'],
        ['tools.php', 'import.php'],
        ['tools.php', 'export.php'],
      ];

      foreach ($items as $item) {
        remove_submenu_page($item[0], $item[1]);
      }

      $boxes = [
        'commentsdiv' => 'post',
        'commentstatusdiv' => 'post',
        'linkadvanceddiv' => 'link',
        'linktargetdiv' => 'link',
        'linkxfndiv' => 'link',
        'postcustom' => 'post',
        'postexcerpt' => 'post',
        'revisionsdiv' => 'post',
        'slugdiv' => 'post',
        'sqpt-meta-tags' => 'post',
        'tagsdiv-post_tag' => 'post',
        'trackbacksdiv' => 'post',
      ];

      foreach ($boxes as $id => $page) {
        remove_meta_box($id, $page, 'normal');
      }
    }

}
