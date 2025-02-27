<?php 
// imagem no input
function meu_plugin_enqueue_styles() {
  // Caminho para o diretório do plugin
  $plugin_url = plugin_dir_url( __FILE__ );

  // Adiciona o CSS
  wp_enqueue_style( 'meu-plugin-style', $plugin_url . 'assets/css/style-plugin.css' );

  // Passar o caminho da imagem para o CSS via inline style (usando wp_add_inline_style)
  $custom_css = "

          #agenda-date-picker{
            padding-right: 40px; 
            height: 30px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-image: url('" . $plugin_url . "/assets/img/calender-red.svg'); 
            background-repeat: no-repeat;
            background-position: right 10px center; 
            background-size: 20px 20px;
            padding: 0 8px;
          }
  ";

  // Adiciona o CSS inline ao site
  wp_add_inline_style( 'meu-plugin-style', $custom_css );
}
add_action( 'wp_enqueue_scripts', 'meu_plugin_enqueue_styles' );