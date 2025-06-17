<?php

namespace dbp\Ad;

class Register
{
   public function __construct()
   {
      add_action('admin_init', [$this, 'registers_settings']);

      if (!is_bot()) {
         add_action('wp_body_open', [$this, 'adds_scripts'], 99);
         add_action('cav_head_scripts', [$this, 'adds_footer'], 99);
      }
   }

   public function adds_footer(): void
   {
      echo <<<'SCRIPT'
      <script>
      function refreshAds(){
         document.querySelectorAll('.adsbygoogle').forEach(function(el) {
            el.removeAttribute('data-adsbygoogle-status')
         });
         (adsbygoogle = window.adsbygoogle || []).push({})
      }
      refreshAds()
      </script>
      SCRIPT;
   }

   public function adds_scripts(): void
   {
      echo '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7971845527728300" crossorigin="anonymous"></script>';
   }

   public function echo_field($args): void
   {
      $label = $args['label_for'];
      $value = get_option($label, '');

      echo "<input id=\"{$label}\" class=\"{$args['class']}\" name=\"{$label}\" type=\"{$args['type']}\" value=\"{$value}\" />";
   }

   public function registers_settings(): void
   {
      register_setting('reading', 'dbands-ads-placeholder-footer');
      register_setting('reading', 'dbands-ads-placeholder-aside');
      register_setting('reading', 'dbands-ads-placeholder-link');

      add_settings_section(
         'dbands-ads',
         esc_html__('Placeholder para Ads', 'dbands'),
         '__return_empty_string',
         'reading',
      );

      add_settings_field(
         'dbands-ads-placeholder-footer',
         esc_html__('Imagem para footer', 'dbands'),
         [$this, 'echo_field'],
         'reading',
         'dbands-ads',
         [
            'label_for' => 'dbands-ads-placeholder-footer',
            'type'      => 'number',
            'class'     => 'small-text',
         ],
      );

      add_settings_field(
         'dbands-ads-placeholder-aside',
         esc_html__('Imagem para sidebar', 'dbands'),
         [$this, 'echo_field'],
         'reading',
         'dbands-ads',
         [
            'label_for' => 'dbands-ads-placeholder-aside',
            'type'      => 'number',
            'class'     => 'small-text',
         ],
      );

      add_settings_field(
         'dbands-ads-placeholder-link',
         esc_html__('Link para placeholder ads', 'dbands'),
         [$this, 'echo_field'],
         'reading',
         'dbands-ads',
         [
            'label_for' => 'dbands-ads-placeholder-link',
            'type'      => 'url',
            'class'     => 'regular-text',
         ],
      );
   }
}
