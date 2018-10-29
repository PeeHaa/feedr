import buildFormBody from "../../helpers/buildFormBody";

export default class {
    constructor(modal, livePreview) {
        this.modal       = modal;
        this.livePreview = livePreview;
        this.form        = this.modal.querySelector('form');

        this.modal.addEventListener('click', (event) => {
            if ('dismiss' in event.target.dataset === false) {
                return;
            }

            event.preventDefault();

            this.close();
        });

        this.form.addEventListener('submit', this.submit.bind(this));
    }

    show() {
        this.modal.classList.add('show');
    }

    close() {
        this.modal.classList.remove('show');

        setTimeout(() => {
            this.modal.parentNode.removeChild(this.modal);
        }, 500);
    }

    submit(event) {
        event.preventDefault();

        const request = new XMLHttpRequest();

        request.addEventListener('load', this.processSuccessfulRequest.bind(this));
        request.addEventListener('error', this.processFailedRequest.bind(this));
        request.open('POST', this.form.getAttribute('action'));
        request.send(buildFormBody(this.form));
    }

    processSuccessfulRequest(response) {
        const repository = JSON.parse(response.target.responseText)['repository'];

        const row = document.querySelector('.table.repositories tr[data-id="' + repository.id + '"]');

        row.parentNode.removeChild(row);

        this.livePreview.unsubscribeFromRepository(repository.fullName);

        this.close();
    }

    processFailedRequest() {
        this.close();
    }
}
