// admin-script.js
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.wrap form');

    form.addEventListener('submit', function(event) {
        const apiKeyInput = document.querySelector('input[name="2good_vs_api_key"]');
        
        // Trim the input value and check if it's empty or just whitespace
        if (!apiKeyInput.value.trim()) {
            event.preventDefault();
            alert('Please enter a valid YOUTUBE API KEY. It should not be empty or just spaces.');
        }
    });
});
