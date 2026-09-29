document.addEventListener('DOMContentLoaded', function() {
    initCourseChart();
    initStatAnimations();
});

function initCourseChart() {
    const canvas = document.getElementById('courseChart');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    
    // Get course data from data attributes
    const labelsAttr = canvas.dataset.labels;
    const valuesAttr = canvas.dataset.values;
    
    if (!labelsAttr || !valuesAttr || labelsAttr === '[]' || valuesAttr === '[]') {
        showNoDataMessage(canvas, 'No course data available');
        return;
    }
    
    try {
        const labels = JSON.parse(labelsAttr);
        const values = JSON.parse(valuesAttr).map(v => parseInt(v));
        
        // Check if all values are zero
        if (values.every(val => val === 0) || labels.length === 0) {
            showNoDataMessage(canvas, 'No course selection data');
            return;
        }
        
        // Create doughnut chart (more visually appealing for top courses)
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: ['#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6'],
                    borderWidth: 0,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#2c3e50',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return `Selections: ${context.raw}`;
                            }
                        }
                    }
                },
                animation: {
                    duration: 1500,
                    easing: 'easeInOutQuart'
                }
            }
        });
        console.log('Course chart created successfully');
    } catch (e) {
        console.error('Error parsing course data:', e);
        showNoDataMessage(canvas, 'Error loading course data');
    }
}

function initStatAnimations() {
    const statCards = document.querySelectorAll('.stat-card h2');
    statCards.forEach(card => {
        const targetValue = parseInt(card.innerText.replace(/,/g, '')) || 0;
        if (targetValue > 0) {
            animateValue(card, 0, targetValue, 1000);
        }
    });
}

function animateValue(element, start, end, duration) {
    if (start === end) return;
    
    const range = end - start;
    const increment = range / (duration / 16);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        if (current >= end) {
            element.innerText = end.toLocaleString();
            clearInterval(timer);
        } else {
            element.innerText = Math.floor(current).toLocaleString();
        }
    }, 16);
}

function showNoDataMessage(canvas, message) {
    const parent = canvas.parentElement;
    canvas.style.display = 'none';
    
    if (parent.querySelector('.no-data-message')) {
        return;
    }
    
    const messageDiv = document.createElement('div');
    messageDiv.className = 'no-data-message text-muted text-center py-5';
    messageDiv.innerHTML = `<i class="fas fa-chart-bar fa-2x mb-2"></i><br>${message}`;
    parent.appendChild(messageDiv);
}

function exportCourseChart() {
    const canvas = document.getElementById('courseChart');
    if (!canvas) {
        alert('Course chart not found');
        return;
    }
    
    const link = document.createElement('a');
    link.download = 'course-selections-chart.png';
    link.href = canvas.toDataURL('image/png');
    link.click();
}