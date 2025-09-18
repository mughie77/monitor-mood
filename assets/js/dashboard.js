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
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            ${data.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>`;

                    // Update the UI to show the 'submitted' state
                    moodPanel.innerHTML = `
                        <div class="card-header">
                            <h4 class="mb-0">How are you feeling today?</h4>
                        </div>
                        <div class="card-body">
                            <div class="card-mood-submitted p-4">
                                <h5 class="text-success">Thanks for sharing your mood today!</h5>
                                <p class="mb-0">You can share your mood again tomorrow.</p>
                            </div>
                        </div>`;

                } else {
                    // Display error message
                    responseMessage.innerHTML = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            ${data.message || 'Terjadi kesalahan yang tidak diketahui.'}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                responseMessage.innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Terjadi kesalahan jaringan. Silakan coba lagi.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>`;
            });
        });
    }
});
