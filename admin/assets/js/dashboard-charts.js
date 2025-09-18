document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('moodChart').getContext('2d');
    let moodChart;

    // Function to update summary cards
    const updateSummaryCards = (summary) => {
        document.getElementById('total-students').textContent = summary.total_students || '0';
        document.getElementById('total-teachers').textContent = summary.total_teachers || '0';
        document.getElementById('avg-student-mood').textContent = summary.avg_student_mood_today || 'N/A';
        document.getElementById('avg-teacher-mood').textContent = summary.avg_teacher_mood_today || 'N/A';
    };

    const renderChart = async (filter = 'daily') => {
        try {
            const response = await fetch(`api_mood_data.php?filter=${filter}`);
            if (!response.ok) throw new Error('Network response was not ok');

            const apiResponse = await response.json();
            const { chartData, summary } = apiResponse;

            // Update summary cards (we only need to do this once, but it's fine for this app)
            if (summary) {
                updateSummaryCards(summary);
            }

            if (moodChart) {
                moodChart.destroy();
            }

            moodChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: chartData.datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, max: 5, ticks: { stepSize: 1 } }
                    },
                    plugins: {
                        legend: { position: 'top' },
                        title: { display: true, text: `Tren Suasana Hati` }
                    }
                }
            });
        } catch (error) {
            console.error('Failed to fetch or render chart:', error);
            document.getElementById('moodChart').style.display = 'none';
            const errorContainer = document.querySelector('.card-body');
            if(errorContainer) {
                errorContainer.innerHTML += '<p class="text-danger">Tidak dapat memuat data grafik.</p>';
            }
        }
    };

    // Event listeners for filter buttons
    const filterButtons = document.querySelectorAll('.btn-group .btn');
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Manage active state for buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            const filter = this.id.replace('filter-', '');
            renderChart(filter);
        });
    });

    // Initial chart render on page load
    renderChart('daily');
});
