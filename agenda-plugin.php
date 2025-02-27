<?php
/*
Plugin Name: Agenda Plugin
Description: Plugin para gerenciar eventos na agenda.
Version: 1.0
Author: Seu Nome
*/

// Evita acesso direto ao arquivo
if (!defined('ABSPATH')) {
    exit;
}

include('functions.php');

// Cria o Custom Post Type "Agenda"
function agenda_create_post_type() {
    register_post_type('agenda',
        array(
            'labels' => array(
                'name' => __('Agenda'),
                'singular_name' => __('Evento')
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'editor', 'thumbnail'),
            'menu_icon' => 'dashicons-calendar',
        )
    );
}
add_action('init', 'agenda_create_post_type');

// Adiciona o metabox para a data do evento
function agenda_add_meta_box() {
    add_meta_box(
        'data_evento',
        'Data do Evento',
        'agenda_meta_box_callback',
        'agenda',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'agenda_add_meta_box');

// Callback para exibir o metabox
function agenda_meta_box_callback($post) {
  wp_nonce_field('agenda_save_meta_box_data', 'agenda_meta_box_nonce');

  $local_value = get_post_meta($post->ID, '_local_value', true);
  $data_evento = get_post_meta($post->ID, '_data_evento', true);
  $horario_inicio = get_post_meta($post->ID, '_horario_inicio', true);
  $horario_final = get_post_meta($post->ID, '_horario_final', true);

?>

<label for="local_value">Local:</label>
<input type="text" name="local_value" value="<?php echo esc_attr($local_value); ?>" class="widefat" />
<br>

<label for="data_evento">Data:</label>
<input type="date" name="data_evento" value="<?php echo esc_attr($data_evento); ?>" class="widefat" />
<br>

<label for="horario_inicio">Horário de inicio:</label>
<input type="time" name="horario_inicio" value="<?php echo esc_attr($horario_inicio); ?>" class="widefat" />
<br>

<label for="horario_final">Horário de encerramento:</label>
<input type="time" name="horario_final" value="<?php echo esc_attr($horario_final); ?>" class="widefat" />
<br>

<?php
}

// Salva os dados do metabox
function agenda_save_meta_box_data($post_id) {
  if (!isset($_POST['agenda_meta_box_nonce'])) {
      return;
  }
  if (!wp_verify_nonce($_POST['agenda_meta_box_nonce'], 'agenda_save_meta_box_data')) {
      return;
  }
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
      return;
  }
  if (!current_user_can('edit_post', $post_id)) {
      return;
  }
  if (isset($_POST['local_value'])) {
    update_post_meta($post_id, '_local_value', sanitize_text_field($_POST['local_value']));
  }

  if (isset($_POST['data_evento'])) {
    update_post_meta($post_id, '_data_evento', sanitize_text_field($_POST['data_evento']));
  }

  if (isset($_POST['horario_inicio'])) {
    update_post_meta($post_id, '_horario_inicio', sanitize_text_field($_POST['horario_inicio']));
  }

  if (isset($_POST['horario_final'])) {
    update_post_meta($post_id, '_horario_final', sanitize_text_field($_POST['horario_final']));
  }
  $data_evento = sanitize_text_field($_POST['data_evento']);
  update_post_meta($post_id, '_data_evento', $data_evento);
}
add_action('save_post', 'agenda_save_meta_box_data');

// Função para listar os eventos do dia
function agenda_list_events_today($date = null) {
    $date = $date ? $date : date('Y-m-d');
    $args = array(
        'post_type' => 'agenda',
        'meta_key' => '_data_evento',
        'meta_value' => $date,
        'orderby' => 'meta_value',
        'order' => 'ASC',
    );
    $query = new WP_Query($args);
    include('post-list.php');
    
    wp_reset_postdata();
}

// Função para exibir a navegação e o calendário
function agenda_display_navigation() {
    echo '<div class="agenda-navigation">';
    echo '<button id="prev-day">Dia Anterior</button>';
    echo '<input type="text" id="agenda-date-picker" value="' . date('Y-m-d') . '">';
    echo '<button id="next-day">Próximo Dia</button>';
    echo '</div>';
    echo '<div class="agenda-events">';
    agenda_list_events_today();
    echo '</div>';
}

// Adiciona o shortcode para exibir a agenda
function agenda_shortcode() {
    ob_start();
    agenda_display_navigation();
    return ob_get_clean();
}
add_shortcode('agenda', 'agenda_shortcode');

// Adiciona scripts e estilos para o Flatpickr
function agenda_enqueue_scripts() {
    // Flatpickr CSS
    wp_enqueue_style('flatpickr-css', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');

    // Flatpickr JS
    wp_enqueue_script('flatpickr-js', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.js', array('jquery'), null, true);

    // Script do plugin
    wp_enqueue_script('agenda-script', plugin_dir_url(__FILE__) . 'agenda.js', array('jquery', 'flatpickr-js'), null, true);

    // Localiza o script para passar variáveis do PHP para o JS
    wp_localize_script('agenda-script', 'agenda_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
    ));
}
add_action('wp_enqueue_scripts', 'agenda_enqueue_scripts');

// Função para carregar eventos via agenda_get_event_dates
function agenda_load_events() {
    $date = sanitize_text_field($_POST['date']);
    $args = array(
        'post_type' => 'agenda',
        'meta_key' => '_data_evento',
        'meta_value' => $date,
        'orderby' => 'meta_value',
        'order' => 'ASC',
    );
    $query = new WP_Query($args);
    include('post-list.php');
    wp_reset_postdata();
    wp_die();
}
add_action('wp_ajax_agenda_load_events', 'agenda_load_events');
add_action('wp_ajax_nopriv_agenda_load_events', 'agenda_load_events');

// Função para retornar as datas que possuem eventos
function agenda_get_event_dates() {
  $args = array(
      'post_type' => 'agenda',
      'posts_per_page' => -1,
      'meta_key' => '_data_evento',
      'orderby' => 'meta_value',
      'order' => 'ASC',
  );
  $query = new WP_Query($args);
  $event_dates = array();

  if ($query->have_posts()) {
      while ($query->have_posts()) {
          $query->the_post();
          $event_date = get_post_meta(get_the_ID(), '_data_evento', true);
          if (!in_array($event_date, $event_dates)) {
              $event_dates[] = $event_date;
          }
      }
  }
  wp_reset_postdata();

  return $event_dates;
}

// Função para passar as datas com eventos para o JavaScript
function agenda_localize_event_dates() {
  $event_dates = agenda_get_event_dates();
  wp_localize_script('agenda-script', 'agenda_event_dates', $event_dates);
}
add_action('wp_enqueue_scripts', 'agenda_localize_event_dates');

// Adiciona estilos personalizados
function agenda_enqueue_styles() {
  echo '
  <style>
      .flatpickr-day.event-day {
          background-color: #4CAF50; /* Cor de fundo para dias com eventos */
          color: white; /* Cor do texto */
          border-radius: 50%; /* Formato circular */
      }
  </style>
  ';
}
add_action('wp_head', 'agenda_enqueue_styles');