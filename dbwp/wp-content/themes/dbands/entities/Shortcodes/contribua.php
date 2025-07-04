<?php

namespace dbt\Shortcodes;

class contribua
{
   public function __construct()
   {
      add_shortcode('contribua', [$this, 'content']);
   }

   public function content()
   {
      $categories = '';

      foreach (get_categories() as $category) {
         $categories .= <<<HTML
         <option value="{$category->term_id}">{$category->name} - {$category->description}</option>
         HTML;
      }

      $bands = '';

      foreach (get_tags() as $band) {
         $bands .= <<<HTML
         <option value="{$band->term_id}">{$band->name}</option>
         HTML;
      }

      $user_name  = '';
      $user_email = '';
      $disabled   = '';

      if (is_user_logged_in()) {
         $user       = wp_get_current_user();
         $user_name  = $user->display_name;
         $user_email = $user->user_email;
         $disabled   = 'disabled';
      }

      $ajax_nonce = wp_create_nonce('form_new_post_draft');

      $output = <<<HTML
      <form class="form-new-draft">
      <div class="form-group mb-3">
         <label for="post-author">Autor</label>
         <input id="post-author" class="form-control" type="text" name="post_author" value="{$user_name}" placeholder="Seu nome" required{$disabled}>
      </div>
      <div class="form-group mb-3">
         <label for="post-email">E-mail <em>(não será divulgado)</em></label>
         <input id="post-email" class="form-control" type="email" name="post_email" value="{$user_email}" placeholder="Seu e-mail" required{$disabled}>
      </div>
      <div class="form-group mb-3">
         <label for="post-title">Título</label>
         <input id="post-title" class="form-control" type="text" name="post_title" placeholder="Título da publicação" required>
      </div>
      <div class="form-group mb-3">
         <label for="post-except">Resumo <em>(usado nos compartilhamentos)</em></label>
         <textarea id="post-except" class="form-control" name="post_except" placeholder="Resuma em poucas palavras a publicação" required></textarea>
      </div>
      <div class="form-group mb-3">
         <label for="post-category">Categoria</label>
         <select id="post-category" class="form-control" name="post_category" required>
            <option disabled selected>Selecione a que mais se encaixe</option>
            {$categories}
         </select>
      </div>
      <div class="form-group mb-3">
         <label for="post-tag">Bandas mencionadas</label>
         <select id="post-tag" class="form-control" name="post_tags[]" multiple required>
            {$bands}
            <option value="-1">Não está listada (será criada)</option>
         </select>
      </div>


      <div class="form-group mb-3">
         <label for="post-thumb">Miniatura</label>
         <input id="post-thumb" name="post_thumb" type="hidden" value required>
         <button type="button" class="button" data-media-uploader-target="#post-thumb" data-media-thumbnail-target="">Selecionar imagem</button>
      </div>
      <input type="hidden" name="_ajax_nonce" value="{$ajax_nonce}">
      HTML;

      ob_start();
      wp_editor('', 'post_content', [
         'default_editor' => 'tinymce',
         'textarea_rows'  => 15,
         'quicktags'      => false,
         'media_buttons'  => false,
      ]);
      $output .= ob_get_clean();

      $output .= <<<'HTML'
      <div class="mt-3">
      <button class="btn btn-primary submit-new-draft" type="submit">Enviar publicação</button>
      <span class="form-msg-error text-danger"></span>
      </div>
      </form>
      HTML;

      return $output;
   }
}
