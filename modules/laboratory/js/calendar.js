document.addEventListener('DOMContentLoaded', function () {



    const calendarEl = document.getElementById('homeCalendar');

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
            // {
            //     id: 'db-events',          
            //     url: `${BASE_URL}/calendar/events`,
            //     method: 'GET',
            //     failure: function() {
            //         alert('There was an error fetching events!');
            //     }
            // }
        ],
         
       

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
