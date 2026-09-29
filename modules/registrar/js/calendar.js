document.addEventListener('DOMContentLoaded', function () {

    const calModal = new bootstrap.Modal(document.getElementById('calendarModal'));

    const calendarEl = document.getElementById('calendarDiv');

    const calendar = new FullCalendar.Calendar(calendarEl, {

          themeSystem: 'bootstrap5',
         timeZone: 'Asia/Manila',
          locale: 'en-ph',
          initialView: 'dayGridMonth',
          height: 500,
          aspectRatio: 1.6,

        headerToolbar: {
             left: 'prev,next today',
             center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        buttonText: {
                today: 'Today',
                month: 'Month',
                week: 'Week',
                day: 'Day'
            },
        selectable:true,
         eventSources: [
           
            {
            events: function(info, successCallback, failureCallback) {
                var year = info.start.getFullYear();
                fetch(`https://date.nager.at/api/v3/PublicHolidays/${year}/PH`)
                .then(res => res.json())
                .then(data => {
                    const holidays = data.map(h => ({ title: h.name, start: h.date, allDay: true, color: 'red', description: h.name, type: 'holiday' }));
                    successCallback(holidays);

                   
                });
            }
            },
            {
                id: 'db-events',          
                url: `${BASE_URL}/calendar/events`,
                method: 'GET',
                failure: function() {
                    alert('There was an error fetching events!');
                }
            }
        ],
         eventClick: function(e) {
            
               

                document.getElementById('modalTitle').textContent = e.event.title;
                document.getElementById('start').value = formatPH(e.event.start);
                document.getElementById('end').value = formatPH(e.event.end);
                document.getElementById('header').style = `background-color: ${e.event.backgroundColor}`;
                document.getElementById('description').value = e.event.extendedProps.description ?? 'No Description';


                if (e.event.extendedProps.type === 'holiday') {
                    
                    setTimeout(()=>{
                        confetti({
                        particleCount: 150,
                        spread: 100,
                        origin: { y: 0.6 }
                    });

                    },1000)
              }

                calModal.show();


                  
            
             },

       

    });

    calendar.render()
 });


 function formatPH(date) {
    return new Intl.DateTimeFormat('en-PH', {
        timeZone: 'Asia/Manila',
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    }).format(date);
}




