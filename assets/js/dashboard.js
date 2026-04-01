document.addEventListener('DOMContentLoaded', function() {
    const moodSelector = document.getElementById('mood-selector-ui');
    const moodPanel = document.getElementById('mood-panel');
    const responseMessage = document.getElementById('mood-response-message');

    if (moodSelector) {
        moodSelector.addEventListener('click', function(event) {
            const moodOption = event.target.closest('.mood-option');
            if (!moodOption) return;

            const moodValue = moodOption.dataset.moodValue;

            // Send the data using Fetch API
            fetch('app/submit_mood.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'mood_value=' + encodeURIComponent(moodValue)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Display success message
                    responseMessage.innerHTML = `
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-r-lg shadow-sm mb-6" role="alert">
                            <p class="font-bold text-sm">Berhasil!</p>
                            <p class="text-sm">${data.message}</p>
                        </div>`;

                    // Update the UI to show the 'submitted' state
                    moodPanel.innerHTML = `
                        <div class="p-8 md:p-12 text-center">
                            <div class="mood-submitted-view">
                                <div class="w-24 h-24 bg-green-100 text-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <i class="fas fa-check-circle text-5xl"></i>
                                </div>
                                <h4 class="text-2xl font-bold text-green-600 mb-2">Terima kasih!</h4>
                                <p class="text-gray-500">Anda sudah mencatat suasana hati hari ini. Kembali lagi besok!</p>
                            </div>
                        </div>`;

                } else {
                    // Display error message
                    responseMessage.innerHTML = `
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg shadow-sm mb-6" role="alert">
                            <p class="font-bold text-sm">Gagal!</p>
                            <p class="text-sm">${data.message || 'Terjadi kesalahan yang tidak diketahui.'}</p>
                        </div>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                responseMessage.innerHTML = `
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg shadow-sm mb-6" role="alert">
                        <p class="font-bold text-sm">Gagal!</p>
                        <p class="text-sm">Terjadi kesalahan jaringan. Silakan coba lagi.</p>
                    </div>`;
            });
        });
    }
});
