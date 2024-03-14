// admin-script.js
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.wrap form');
    const apiKeyInput = document.querySelector('input[name="2good_vs_api_key"]');
    //var apiKeyInput = document.getElementById('2good_vs_api_key');

    form.addEventListener('submit', function(event) {
        
        // Trim the input value and check if it's empty or just whitespace
        if (!apiKeyInput.value.trim()) {
            event.preventDefault();
            alert('Please enter a valid YOUTUBE API KEY. It should not be empty or just spaces.');
        }
    });

    function showLoadingSpinner(element) {
        element.innerHTML = 'Loading...';
    }

    var testAPIkeyButton = document.getElementById('test_youtube_api_key_button');
    var apiKeyResult = document.getElementById('test_youtube_api_key_result');
    
    if (testAPIkeyButton) {
        testAPIkeyButton.addEventListener('click', function() {
            var apiKey = apiKeyInput.value.trim();
            if (!apiKey || apiKey.length <= 38) {
                alert('Please enter a valid YOUTUBE API KEY.');
            }
            showLoadingSpinner(apiKeyResult);
            testAPIkey();
        });
    }

    function testAPIkey() {
        jQuery.post(ajaxurl, { 
            action: 'test_youtube_api_key'
        }, function(response) {
            //console.log(response);
            if (response === '200') {
                apiKeyResult.innerHTML = 'API Key is valid';
            } else {   
                apiKeyResult.innerHTML = 'API Key is invalid, code: ' + response + ' - Please check the key and try again.';
            }            
        }).always(function() {
            hideLoadingSpinner(apiKeyResult);
        });
    }

    var getAllSchemasButton = document.getElementById('parse_all_schemas');
    var getAllSchemasResult = document.getElementById('parse_all_schemas_result');

    if (getAllSchemasButton) {
        getAllSchemasButton.addEventListener('click', function() {
            
            if (!apiKeyInput.value.trim() || apiKeyInput.value.length <= 38) {
                alert('Please enter a valid YOUTUBE API KEY.');
            }
            showLoadingSpinner(getAllSchemasResult);
            getAllSchemas();
        });
    }
    
    function getAllSchemas() {
        jQuery.post(ajaxurl, { 
            action: 'parse_all_schemas',
            apiKey: apiKeyInput.value.trim()
        }, function(response) {
            //console.log(response);
    
            // Parse JSON response
            var data = JSON.parse(response);
            if (data.success) {
                getAllSchemasResult.innerHTML = 'Total schemas added: ' + data.count;
            } else {
                getAllSchemasResult.innerHTML = 'Error: ' + data.message;
            }
        }).always(function() {
            hideLoadingSpinner(getAllSchemasResult);
        });
    }
    
});