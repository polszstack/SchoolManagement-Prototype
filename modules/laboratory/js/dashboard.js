const ctx = document.getElementById('myAreaChart');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Mar', 'May', 'Jul', 'Sep', 'Nov', 'Dec'],
        datasets: [{
            label: 'Overview',
            data: [0, 10000, 10000, 15000, 20000, 25000, 40000],
            tension: 0.4,
            fill: true,
            backgroundColor: 'rgba(78,115,223,0.2)',
            borderColor: 'rgba(78,115,223,1)',
            pointBackgroundColor: 'rgba(78,115,223,1)',
            pointBorderColor: '#fff',
            pointRadius: 4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

const pieCtx = document.getElementById('myPieChart');

new Chart(pieCtx, {
    type: 'doughnut',
    data: {
        labels: [' Barrowed', 'Damage Equipment', 'Available'],
        datasets: [{
            data: [55, 30, 15],
            backgroundColor: [
                '#4e73df',
                '#1cc88a',
                '#36b9cc'
            ],
            hoverOffset: 6
        }]
    },
    options: {
        cutout: '70%',
        plugins: {
            legend: {
                display: false
            }
        }
    }
});