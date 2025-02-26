jQuery(document).ready(function($) {
  // Inicializa o Flatpickr
  var datePicker = flatpickr('#agenda-date-picker', {
      defaultDate: 'today', // Define a data inicial como hoje
      dateFormat: 'Y-m-d', // Formato da data
      onChange: function(selectedDates, dateStr, instance) {
          // Atualiza o input com a data selecionada
          $('#agenda-date-picker').val(dateStr);
          loadEvents(dateStr); // Carrega os eventos para a data selecionada
      }
  });

  // Botão "Dia Anterior"
  $('#prev-day').on('click', function() {
      var currentDate = datePicker.selectedDates[0];
      if (currentDate) {
          var prevDate = new Date(currentDate);
          prevDate.setDate(prevDate.getDate() - 1); // Subtrai um dia
          datePicker.setDate(prevDate); // Atualiza o calendário
          $('#agenda-date-picker').val(prevDate.toISOString().split('T')[0]); // Atualiza o input
          loadEvents(prevDate.toISOString().split('T')[0]); // Carrega os eventos
      }
  });

  // Botão "Próximo Dia"
  $('#next-day').on('click', function() {
      var currentDate = datePicker.selectedDates[0];
      if (currentDate) {
          var nextDate = new Date(currentDate);
          nextDate.setDate(nextDate.getDate() + 1); // Adiciona um dia
          datePicker.setDate(nextDate); // Atualiza o calendário
          $('#agenda-date-picker').val(nextDate.toISOString().split('T')[0]); // Atualiza o input
          loadEvents(nextDate.toISOString().split('T')[0]); // Carrega os eventos
      }
  });

  // Carrega eventos para a data selecionada
  function loadEvents(date) {
      $.ajax({
          url: agenda_ajax.ajax_url,
          type: 'POST',
          data: {
              action: 'agenda_load_events',
              date: date
          },
          success: function(response) {
              $('.agenda-events').html(response); // Exibe os eventos na página
          }
      });
  }
});