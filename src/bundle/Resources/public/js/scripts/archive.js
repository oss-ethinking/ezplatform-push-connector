(function (global, doc, eZ, React, ReactDOM, Translator) {
    const token = doc.querySelector('meta[name="CSRF-Token"]').content;
    const handleUpdateError = eZ.helpers.notification.showErrorNotification;
    const statusLinks = doc.querySelectorAll('.status-link');
    let isOpeningDetailsPopup = false;
    let isInfinityScrollProcessing = false;
    let paginationLastPage = false;
    let paginationCurrentPage = 2;

    const getResponseStatus = (response) => {
        if (!response.ok) {
            throw Error(response.statusText);
        }

        return response;
    };

    const showDetailsPopup = (id) => {
        if (isOpeningDetailsPopup) {
            return;
        }

        doc.querySelector('.push-delivery-modal-body').innerHTML = '';
        isOpeningDetailsPopup = true;

        const request = new Request(Routing.generate('ezplatform.push.archive.details', { id: id }), {
            method: 'GET',
            headers: { 'X-CSRF-Token': token },
            mode: 'same-origin',
            credentials: 'same-origin',
        });

        fetch(request)
            .then(getResponseStatus)
            .then((response) => {
                isOpeningDetailsPopup = false;
                response.text().then(templateHtml => {
                    doc.querySelector('.push-delivery-modal-body').innerHTML = templateHtml;
                });
            })
            .catch(handleUpdateError);
    };

    const addMoreElements = (page) => {
        isInfinityScrollProcessing = true;

        const request = new Request(Routing.generate('ezplatform.push.archive.more', { page: page }), {
            method: 'GET',
            headers: { 'X-CSRF-Token': token },
            mode: 'same-origin',
            credentials: 'same-origin',
        });

        fetch(request)
            .then(getResponseStatus)
            .then((response) => {
                response.text().then(templateHtml => {
                    if (templateHtml) {
                        doc.querySelector('#pushConnectorArchiveTableBody').innerHTML += templateHtml;
                        paginationCurrentPage = page + 1;
                    } else {
                        paginationLastPage = true;
                    }
                });

                isInfinityScrollProcessing = false;
            })
            .catch(handleUpdateError);
    };

    statusLinks.forEach((statusLink) => {
        statusLink.addEventListener('click', (event) => {
            const id = statusLink.getAttribute("data-id");
            showDetailsPopup(id);
        });
    });

    window.onscroll = function() {
        const winTop = window.scrollY, docHeight = document.body.scrollHeight, winHeight = document.body.offsetHeight;
        const scrollTrigger = 0.80;

        if ((winTop / (docHeight - winHeight)) > scrollTrigger) {
            if (paginationLastPage === false && isInfinityScrollProcessing === false) {
                addMoreElements(paginationCurrentPage);
            }
        }
    };
})(window, window.document, window.eZ, window.React, window.ReactDOM, window.Translator);