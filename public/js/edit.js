   function previewImage(event, ...previewIds) {
        const reader = new FileReader();
        reader.onload = function() {
            previewIds.forEach(id => {
                const output = document.getElementById(id);
                if (output) {
                    output.src = reader.result;
                    output.style.display = 'block';
                }
            });
        };
        reader.readAsDataURL(event.target.files[0]);
    }
        // Restrict DOB to 18+ years
        document.addEventListener('DOMContentLoaded', () => {
            const dobInput = document.getElementById('dateOfBirth');
            if (dobInput) {
                const today = new Date();
                const minYear = today.getFullYear() - 18;
                const maxDate = new Date(minYear, today.getMonth(), today.getDate());
                dobInput.max = maxDate.toISOString().split('T')[0];
            }
        });