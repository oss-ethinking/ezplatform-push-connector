(function (global, doc, eZ, React, ReactDOM, Translator) {

    //initializations
    const testButton = doc.querySelector('#main_settings_form_test');
    const settingsAlertContainer = doc.querySelector("#pushConnectorSettingsAlerts");

    //methods
    const getResponseStatus = (response) => {
        if (!response.ok) {
            throw Error(response.statusText);
        }

        return response;
    };

    const getAlertBody = (message, type) => {

        let alertBody = '<div class="alert  alert-' + type + '">' +
            '<a class="close" data-dismiss="alert" href="#">&times;</a>' +
            '<ul>' +
            '<li>' +
            message +
            '</li>' +
            '</ul>' +
            '</div>';
        return alertBody;
    };

    //event handlers
    testButton.addEventListener('click', (event) => {
        const domain = doc.querySelector('#main_settings_form_domain').value;
        const username = doc.querySelector('#main_settings_form_username').value;
        const password = doc.querySelector('#main_settings_form_password').value;

        const request = new Request(domain + '/push-admin-api/endpoints/get', {
            method: 'GET',
            headers: {'Authorization': "Basic " + btoa(username + ":" + password)},
            mode: 'cors'
        });


        fetch(request)
            .then(getResponseStatus)
            .then((response) => {
                let successAlert = settingsAlertContainer.querySelector('.alert-success');
                if (typeof (successAlert) != 'undefined' && successAlert != null) {
                    //clear existing alerts if any
                    settingsAlertContainer.innerHTML = '';
                }
                settingsAlertContainer.innerHTML = getAlertBody('Connection success', 'success');
            })
            .catch((error) => {
                settingsAlertContainer.innerHTML = getAlertBody('Connection failed', 'error');
            });
    });


})(window, window.document, window.eZ, window.React, window.ReactDOM, window.Translator);