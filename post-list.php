<?php 
if ($query->have_posts()) {
        
  while ($query->have_posts()) {
      $query->the_post();

        $evento_data = get_post_meta(get_the_ID(), '_data_evento', true);
        $local_value = get_post_meta(get_the_ID(), '_local_value', true);
        $horario_inicio = get_post_meta(get_the_ID(), '_horario_inicio', true);
        $horario_final = get_post_meta(get_the_ID(), '_horario_final', true);
        if ($evento_data) {
            $evento_data_formatada = date('d/m/Y', strtotime($evento_data)); 
        }
      ?>
         <div class="agenda-post">
           <div class="content-inside">
             <div class="data-left">
                 <span class="day"><?php echo date('d', strtotime($evento_data)); ?></span>
                 <span class="month"><b><?php echo ucfirst(date_i18n('M', strtotime($evento_data))); ?></b></span>
                 <span class="year"><b><?php echo date_i18n('Y', strtotime($evento_data));?></b></span>
             </div>
             <div class="content-date">                                 
                 <span class="local"><span class="local"><?php echo date('H:i', strtotime($horario_inicio));?></span> - <span class="local"><?php echo date('H:i', strtotime($horario_final));?></span></span>
                 <h3 class="title"><?php echo get_the_title(); ?></h3>
                 <span class="local"><?php echo $local_value;?></span>    
             </div>
           </div>
         </div>
  <?php
  
      }
} else {
  echo '<p>Nenhum evento para este dia.</p>';
}