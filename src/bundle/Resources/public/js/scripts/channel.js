(function (global, doc, eZ, React, ReactDOM, Translator) {

    //initializations
    const platformDropdown = doc.querySelector('#channel_form_platformId');

    //methods
    const getResponseStatus = (response) => {
        if (!response.ok) {
            throw Error(response.statusText);
        }

        return response;
    };

    const toggleFields = (selectedValue) => {
        const firebaseFieldSelectors = ['#channel_form_firebaseMessagingSenderId', '#channel_form_firebaseProjectId', '#channel_form_firebaseApiKey', '#channel_form_firebaseAppId', '#channel_form_fallbackUrl'];
        if (selectedValue == 7) {
            firebaseFieldSelectors.forEach((firebaseFieldSelector) => {
                show(doc.querySelector(firebaseFieldSelector).closest("div"));
            });

            show(doc.querySelector('#channel_form_serviceWorkerPath'));
        } else {
            firebaseFieldSelectors.forEach((firebaseFieldSelector) => {
                hide(doc.querySelector(firebaseFieldSelector).closest("div"));
            });

            hide(doc.querySelector('#channel_form_serviceWorkerPath'));
        }
    };
    const show = (element) => {
        element.style.display = 'block';
    };
    const hide = (element) => {
        element.style.display = 'none';
    };

    //event listeners
    platformDropdown.addEventListener('change', (event) => {
        toggleFields(platformDropdown.value);
    });

    //actions on load
    toggleFields(doc.querySelector("#channel_form_platformId").value);

})(window, window.document, window.eZ, window.React, window.ReactDOM, window.Translator);