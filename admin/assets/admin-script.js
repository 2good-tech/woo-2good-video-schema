// admin-script.js
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.wrap form');
    const apiKeyInput = document.querySelector('input[name="2good_vs_api_key"]');

    form.addEventListener('submit', function(event) {
        
        // Trim the input value and check if it's empty or just whitespace
        if (!apiKeyInput.value.trim()) {
            event.preventDefault();
            alert('Please enter a valid YOUTUBE API KEY. It should not be empty or just spaces.');
        }
    });

    var getAllSchemasButton = document.getElementById('parse_all_schemas');

    if (getAllSchemasButton) {
        getAllSchemasButton.addEventListener('click', function() {
            
            if (!apiKeyInput.value.trim() || apiKeyInput.value.length <= 38) {
                alert('Please enter a valid YOUTUBE API KEY. It should not be empty or just spaces.');
            }
            getAllSchemas();
        });
    }
    
    function getAllSchemas() {
        jQuery.post(ajaxurl, { 
            action: 'parse_all_schemas',
            apiKey: apiKeyInput.value.trim()
        }, function(response) {
            console.log(response);
    
            // Parse JSON response
            var data = JSON.parse(response);
            if (data.success) {
                alert(data.message + " Total schemas added: " + data.count);
            } else {
                alert("There was an error processing your request.");
            }
        });
    }
   
});