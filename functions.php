<?php 
// imagem no input
function meu_plugin_enqueue_styles() {
  // Caminho para o diretório do plugin
  $plugin_url = plugin_dir_url( __FILE__ );

  // Adiciona o CSS
  wp_enqueue_style( 'meu-plugin-style', $plugin_url . 'assets/css/style-plugin.css' );

  // Passar o caminho da imagem para o CSS via inline style (usando wp_add_inline_style)
  $custom_css = "

          #agenda-datepicker{
            width: 240px;
            padding-right: 40px; /* Aumentando o espaço entre o texto e o ícone */
            height: 30px;
            border: 1px solid #ccc;
            border-radius: 5px;
          background-image: url('" . $plugin_url . "/assets/img/calender-red.svg'); /* Caminho para a imagem */            
          background-repeat: no-repeat;
            background-position: right 10px center; /* Ajustando a posição para dar mais espaço */
            background-size: 20px 20px; /* Tamanho do ícone */
          }
  ";

  // Adiciona o CSS inline ao site
  wp_add_inline_style( 'meu-plugin-style', $custom_css );
}
add_action( 'wp_enqueue_scripts', 'meu_plugin_enqueue_styles' );