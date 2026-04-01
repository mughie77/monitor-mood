document.addEventListener('DOMContentLoaded', function() {
    const moodCtx = document.getElementById('moodChart').getContext('2d');
    const bullyingCtx = document.getElementById('bullyingChart').getContext('2d');
    let moodChart;
    let bullyingChart;

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

    const updateDashboardSummaries = (summary, activeFilterButton) => {
        const studentMood = summary.avg_student_mood_period;
        const teacherMood = summary.avg_teacher_mood_period;
        const periodText = `(${activeFilterButton.textContent})`;

        document.getElementById('total-students').textContent = summary.total_students || '0';
        document.getElementById('total-teachers').textContent = summary.total_teachers || '0';

        document.querySelectorAll('.period-text').forEach(span => {
            span.textContent = periodText;
        });

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

    const renderCharts = async (filter = 'daily') => {
        try {
            const response = await fetch(`api_mood_data.php?filter=${filter}`);
            if (!response.ok) throw new Error('Network response was not ok');

            const apiResponse = await response.json();
            const { chartData, summary, range_start, range_end, bullyingChartData } = apiResponse;

            const activeFilterButton = document.getElementById(`filter-${filter}`);
            if (summary && activeFilterButton) {
                updateDashboardSummaries(summary, activeFilterButton);
            }

            // Render Mood Chart
            if (moodChart) moodChart.destroy();
            let moodChartTitle = 'Tren Suasana Hati';
            if (range_start && range_end) moodChartTitle += `: ${range_start} - ${range_end}`;
            moodChart = new Chart(moodCtx, {
                type: 'line',
                data: chartData,
                options: {
                    responsive: true, maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true, max: 5, ticks: { stepSize: 1 } } },
                    plugins: {
                        legend: { position: 'top' },
                        title: { display: true, text: moodChartTitle },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) label += ': ';
                                    const value = context.parsed.y.toFixed(2);
                                    label += `${value} (${getMoodDescription(value)})`;
                                    return label;
                                }
                            }
                        }
                    }
                }
            });

            // Render Bullying Chart
            if (bullyingChart) bullyingChart.destroy();
            if (bullyingChartData) {
                bullyingChart = new Chart(bullyingCtx, {
                    type: 'bar',
                    data: bullyingChartData,
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
                        plugins: { legend: { display: false } }
                    }
                });
            }

        } catch (error) {
            console.error('Failed to fetch or render chart:', error);
            const errorContainer = document.querySelector('.card-body');
            if(errorContainer) errorContainer.innerHTML += '<p class="text-danger">Tidak dapat memuat data grafik.</p>';
        }
    };

    const filterButtons = [
        document.getElementById('filter-daily'),
        document.getElementById('filter-monthly'),
        document.getElementById('filter-yearly')
    ];
    filterButtons.forEach(button => {
        if (!button) return;
        button.addEventListener('click', function() {
            filterButtons.forEach(btn => {
                btn.classList.remove('active-filter');
                btn.classList.add('text-gray-500', 'hover:text-gray-700');
            });
            this.classList.add('active-filter');
            this.classList.remove('text-gray-500', 'hover:text-gray-700');
            const filter = this.id.replace('filter-', '');
            renderCharts(filter);
        });
    });

    renderCharts('daily');
});
