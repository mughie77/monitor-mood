document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('moodChart').getContext('2d');
    let moodChart;

    const getMoodDescription = (moodValue) => {
        const value = Math.round(moodValue);
        switch (value) {
            case 1: return 'Sedih';
            case 2: return 'Biasa';
            case 3: return 'Senang';
            case 4: return 'Bersemangat';
            case 5: return 'Luar Biasa';
            default: return 'Tidak Diketahui';
        }
    };

    // This function now updates summary cards and their titles
    const updateDashboardSummaries = (summary, activeFilterButton) => {
        const studentMood = summary.avg_student_mood_period;
        const teacherMood = summary.avg_teacher_mood_period;
        const periodText = `(${activeFilterButton.textContent})`;

        // Update total counts
        document.getElementById('total-students').textContent = summary.total_students || '0';
        document.getElementById('total-teachers').textContent = summary.total_teachers || '0';

        // Update period text in card titles
        document.querySelectorAll('.period-text').forEach(span => {
            span.textContent = periodText;
        });

        // Update average mood values and descriptions
        if (studentMood !== 'N/A') {
            document.getElementById('avg-student-mood').textContent = `${studentMood} (${getMoodDescription(studentMood)})`;
        } else {
            document.getElementById('avg-student-mood').textContent = 'N/A';
        }

        if (teacherMood !== 'N/A') {
            document.getElementById('avg-teacher-mood').textContent = `${teacherMood} (${getMoodDescription(teacherMood)})`;
        } else {
            document.getElementById('avg-teacher-mood').textContent = 'N/A';
        }
    };

    const renderChart = async (filter = 'daily') => {
        try {
            const response = await fetch(`api_mood_data.php?filter=${filter}`);
            if (!response.ok) throw new Error('Network response was not ok');

            const apiResponse = await response.json();
            const { chartData, summary, range_start, range_end } = apiResponse;

            // Find the active button to pass its text to the summary function
            const activeFilterButton = document.querySelector(`.btn-group .btn#filter-${filter}`);
            if (summary && activeFilterButton) {
                updateDashboardSummaries(summary, activeFilterButton);
            }

            if (moodChart) {
                moodChart.destroy();
            }

            let chartTitle = 'Tren Suasana Hati';
            if (range_start && range_end) {
                chartTitle += `: ${range_start} - ${range_end}`;
            }

            moodChart = new Chart(ctx, {
                type: 'line',
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true, max: 5, ticks: { stepSize: 1 } } },
                    plugins: {
                        legend: { position: 'top' },
                        title: { display: true, text: chartTitle },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) label += ': ';
                                    if (context.parsed.y !== null) {
                                        const value = context.parsed.y.toFixed(2);
                                        label += `${value} (${getMoodDescription(value)})`;
                                    }
                                    return label;
                                }
                            }
                        }
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
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            const filter = this.id.replace('filter-', '');
            renderChart(filter);
        });
    });

    // Initial chart render on page load
    renderChart('daily');
});
