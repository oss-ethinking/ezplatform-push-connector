(function (global, doc, eZ, React, ReactDOM, Translator) {

    //initializations
    const token = doc.querySelector('meta[name="CSRF-Token"]').content;
    const platformDropdown = doc.querySelector('#channel_form_platformId');
    const generateButton = doc.querySelector('#channel_form_generate');
    const handleUpdateError = eZ.helpers.notification.showErrorNotification;
    const copyButton = doc.querySelector('#pushConnectorChannelEmbedCodeCopyButton');
    const clipboard = new ClipboardJS(copyButton);

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
    generateButton.addEventListener('click', (event) => {
        const request = new Request(Routing.generate('ezplatform.push.channel.embedCode.generate', {id: doc.querySelector("#channel_form_id").value}), {
            method: 'GET',
            headers: {'X-CSRF-Token': token},
            mode: 'same-origin',
            credentials: 'same-origin',
        });

        fetch(request)
            .then(getResponseStatus)
            .then((response) => {
                response.text().then(templateHtml => {
                    doc.querySelector("#pushConnectorChannelEmbedCode").textContent = templateHtml;
                });
            })
            .catch(handleUpdateError);
        show(doc.querySelector("#pushConnectorChannelCopyDiv"));
        show(doc.querySelector("#pushConnectorChannelEmbedCodeDiv"));
    });

    //actions on load
    toggleFields(doc.querySelector("#channel_form_platformId").value);
    hide(doc.querySelector("#pushConnectorChannelCopyDiv"));
    hide(doc.querySelector("#pushConnectorChannelEmbedCodeDiv"));

})(window, window.document, window.eZ, window.React, window.ReactDOM, window.Translator);