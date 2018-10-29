import buildFormBody from "../../helpers/buildFormBody";

export default class {
    constructor(modal) {
        this.modal = modal;
        this.form  = this.modal.querySelector('form');

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
        const feedId = JSON.parse(response.target.responseText)['id'];

        const row = document.querySelector('.table.feeds tr[data-id="' + feedId + '"]');

        row.parentNode.removeChild(row);

        this.close();
    }

    processFailedRequest() {
        this.close();
    }
}
