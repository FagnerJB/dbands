<?php

if (isset($_GET)) {
   // VIDEO + PLAYLIST =======================================================
   if (isset($_GET['list']) and isset($_GET['v'])) {
      $url = 'https://dbands.com.br/tv/' . $_GET['v'] . '/' . $_GET['list'];
   }

   // PLAYLIST ===============================================================
   elseif (isset($_GET['list'])) {
      $url = 'https://dbands.com.br/tv/' . $_GET['list'];
   }

   // VIDEO ==================================================================
   elseif (isset($_GET['v'])) {
      $url = 'https://dbands.com.br/tv/' . $_GET['v'];
   }

   if (isset($url)) {
      header('Location: ' . $url);
      exit;
   }
}
