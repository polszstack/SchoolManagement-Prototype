document.addEventListener('DOMContentLoaded', function () {

    const calendarEl = document.getElementById('homeCalendar');

    
  fetch(`${BASE_URL}/students/count`)
  .then(response => response.json())
  .then(result => {
    const obj = { val: 0 }; 

    gsap.to(obj, {
      val: result.totalActiveStudent, 
      duration: 2,                   
      ease: "power1.out",
      roundProps: "val",             
      onUpdate: () => {
        document.getElementById("enrolled").textContent = obj.val.toLocaleString();
      }
    });
  });

  const progress = { value: 0 };

  gsap.to(progress, {
    value: cpu,
    duration: 2, 
    onUpdate: () => {
      document.getElementById("cpuBar").style.width = progress.value + "%";
    },
    ease: "power1.out"
  });

    const calendar = new FullCalendar.Calendar(calendarEl, {
          initialView: 'dayGridMonth',
          height: 400,
          aspectRatio: 1.35, 

        headerToolbar: {
             left: 'prev,next today',
             center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
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

         buttonText: {
                today: 'Today',
                month: 'Month',
                week: 'Week',
                day: 'Day'
            },

    });

    calendar.render()



      /*  ========================================================================================= 
       |                                                                                         |
       |     Activity Chart                                                                 |
       |                                                                                         |
       ========================================================================================= 
    */

  const ctx = document.getElementById('activityChart').getContext('2d');

// Initialize empty Chart.js line chart
let activityChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [],
        datasets: [{
            label: 'Activity',
            data: [], 
            borderWidth: 2,
            borderColor: 'rgba(54, 162, 235, 1)',
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            fill: true,
            tension: 0.3
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#eee' } },
            x: { grid: { display: false } }
        }
    }
});

  updateChart();

  // Handle period change
  document.getElementById('period').addEventListener('change', function() {
      const period = this.value;
      updateChart(period);
  });


  function updateChart(periodDays = '7days') {
    
    fetch(`${BASE_URL}/activity/api?range=${periodDays}`) 
        .then(res => res.json())
        .then(data => {

          const countsByDate = data.reduce((acc, item) => {
                const date = item.created_at.split(' ')[0]; 
                acc[date] = (acc[date] || 0) + 1;
                return acc;
            }, {});

            const sortedLabels = Object.keys(countsByDate).sort((a, b) => new Date(a) - new Date(b));


            const sortedCounts = sortedLabels.map(label => countsByDate[label]);
           
            activityChart.data.labels = sortedLabels; 
            activityChart.data.datasets[0].data = sortedCounts; 
            
            activityChart.update();

          
           
        })
        .catch(err => console.error('Error fetching activity:', err));
    }

    navigator.storage.estimate().then(data => {
    console.log("Used:", data.usage);
    console.log("Quota:", data.quota);
    });

  
 });

