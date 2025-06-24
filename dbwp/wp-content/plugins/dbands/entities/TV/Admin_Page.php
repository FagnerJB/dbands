<?php

namespace dbp\TV;

class Admin_Page
{
   private $fields = [
      [
         'id'          => 'dbtv-channels',
         'type'        => 'array',
         'label'       => 'Lista de canais',
         'description' => 'Cada canal é uma lista de canais do YouTube.',
         'default'     => [],
      ],
   ];

   public function __construct()
   {
      add_action('admin_menu', [$this, 'register_page']);
      add_action('admin_init', [$this, 'register_sections']);
   }

   public function content_field($args): void
   {
      $all_channels = get_option($args['id'], $args['default']);

      if (!empty($all_channels)) {
         $all_channels = array_filter(
            $all_channels,
            fn($channel) => !empty($channel['title']) || !empty($channel['list']),
         );

         foreach ($all_channels as $index => $channel) {
            $this->content_input($args, $index, $channel);
         }
      }

      $this->content_input($args, count($all_channels));
   }

   public function content_input($args, $index, $channel = ['title' => '', 'list' => '']): void
   {
      $title = esc_attr($channel['title']);
      $list  = $channel['list'];

      echo "<p><input name=\"{$args['id']}[{$index}][title]\" type=\"text\" value=\"{$title}\" class=\"regular-text\" /></p>";

      echo "<p><textarea name=\"{$args['id']}[{$index}][list]\" class=\"regular-text\" rows=\"9\">{$list}</textarea></p>";
   }

   public function content_page(): void
   {
      echo '<div class="wrap">';
      echo '<h1>' . esc_html(get_admin_page_title()) . '</h1>';
      echo '<form action="options.php" method="post">';

      settings_fields('dbands');
      do_settings_sections('dbands');

      submit_button();

      echo '</form>';
   }

   public function register_page(): void
   {
      add_submenu_page(
         'index.php',
         'dbandsTV',
         'dbandsTV',
         'manage_options',
         'dbands',
         [$this, 'content_page'],
         99,
      );
   }

   public function register_sections(): void
   {
      add_settings_section(
         'dbtv-channels',
         'Canais',
         '',
         'dbands',
      );

      foreach ($this->fields as $args) {
         add_settings_field(
            $args['id'],
            $args['label'],
            [$this, 'content_field'],
            'dbands',
            'dbtv-channels',
            $args,
         );

         register_setting('dbands', $args['id'], $args);
      }
   }
}
