<?php

namespace dbp\Ad;

class Register
{
   public function __construct()
   {
      add_action('admin_init', [$this, 'registers_settings']);

      if (!is_bot()) {
         add_action('wp_enqueue_scripts', [$this, 'adds_style'], 15);
         add_action('wp_body_open', [$this, 'adds_scripts'], 99);
         add_action('cav_head_scripts', [$this, 'adds_footer'], 99);
      }
   }

   public function adds_footer(): void
   {
      echo <<<'HTML'
      <script>
      function refreshAds(){
         document.querySelectorAll('.adsbygoogle').forEach(function(el) {
            el.textContent = '';
            el.style.height = 0;
            el.removeAttribute('data-adsbygoogle-status')
         });
         (adsbygoogle = window.adsbygoogle || []).push({})
      }
      refreshAds()
      </script>
      HTML;
   }

   public function adds_scripts(): void
   {
      echo '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7971845527728300" crossorigin="anonymous"></script>';
   }

   public function adds_style(): void
   {
      $ad_sizes = <<<'CSS'
      .slot-aside,.slot-footer{padding: 2px; background: rgba(128,128,128,.4) }
      .slot-aside { min-width: 336px; max-height: 360px; width: 100%; }
      @media(min-width: 592px) { .slot-aside { width: 568px; } }
      @media(min-width: 832px) { .slot-aside { width: 808px; } }
      @media(min-width: 1024px) { .slot-aside { width: 448px; } }
      .slot-footer { width: 100%; max-width:728px; max-height: 360px }
      CSS;

      wp_add_inline_style('main', $ad_sizes);
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
