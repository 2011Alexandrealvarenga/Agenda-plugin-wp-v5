jQuery(document).ready(function($) {
  // Inicializa o Flatpickr
  var datePicker = flatpickr('#agenda-date-picker', {
      defaultDate: 'today', // Define a data inicial como hoje
      dateFormat: 'd/m/Y', // Formato da data (dia/mês/ano)
      onChange: function(selectedDates, dateStr, instance) {
          // Atualiza o input com a data selecionada
          $('#agenda-date-picker').val(dateStr);
          loadEvents(dateStr); // Carrega os eventos para a data selecionada
      },
      onDayCreate: function(dObj, dStr, fp, dayElem) {
          // Destaca os dias que têm eventos
          if (agenda_event_dates.includes(dayElem.dateObj.toISOString().split('T')[0])) {
              dayElem.classList.add('event-day'); // Adiciona uma classe para destacar
          }
      }
  });

  // Botão "Dia Anterior"
  $('#prev-day').on('click', function() {
      var currentDate = datePicker.selectedDates[0];
      if (currentDate) {
          var prevDate = new Date(currentDate);
          prevDate.setDate(prevDate.getDate() - 1); // Subtrai um dia
          datePicker.setDate(prevDate); // Atualiza o calendário
          var formattedDate = formatDate(prevDate); // Formata a data como DD/MM/YYYY
          $('#agenda-date-picker').val(formattedDate); // Atualiza o input
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
          var formattedDate = formatDate(nextDate); // Formata a data como DD/MM/YYYY
          $('#agenda-date-picker').val(formattedDate); // Atualiza o input
          loadEvents(nextDate.toISOString().split('T')[0]); // Carrega os eventos
      }
  });

  // Função para formatar a data como DD/MM/YYYY
  function formatDate(date) {
      var day = String(date.getDate()).padStart(2, '0'); // Dia (com zero à esquerda)
      var month = String(date.getMonth() + 1).padStart(2, '0'); // Mês (com zero à esquerda)
      var year = date.getFullYear(); // Ano
      return `${day}/${month}/${year}`; // Retorna a data no formato DD/MM/YYYY
  }

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