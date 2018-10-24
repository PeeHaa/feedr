import buildFormBody from "../../helpers/buildFormBody";
import HandleAddAdministratorsModal from "./HandleAddAdministratorsModal";

export default class {
    constructor(form) {
        this.running      = false;
        this.form         = document.querySelector('.searchUsers');
        this.submitButton = this.form.querySelector('button i');

        this.form.addEventListener('submit', this.processSubmit.bind(this));
    }

    processSubmit(event) {
        event.preventDefault();

        if (this.running) {
            return;
        }

        this.disableSubmitButton();

        const request = new XMLHttpRequest();

        request.addEventListener('load', this.processSuccessfulRequest.bind(this));
        request.addEventListener('error', this.processFailedRequest.bind(this));
        request.open('POST', this.form.getAttribute('action'));
        request.send(buildFormBody(this.form));
    }

    processSuccessfulRequest(response) {
        const parsedHtml = new DOMParser().parseFromString(JSON.parse(response.target.responseText)['content'], 'text/html');

        document.querySelector('body').appendChild(parsedHtml.querySelector('.modal'));

        new HandleAddAdministratorsModal(document.querySelector('.modal')).show();

        this.enableSubmitButton();
    }

    processFailedRequest() {
        this.enableSubmitButton();
    }

    disableSubmitButton() {
        this.submitButton.classList.remove('icon-search');
        this.submitButton.classList.add('icon-spinner2');

        this.running = true;
    }

    enableSubmitButton() {
        this.submitButton.classList.add('icon-search');
        this.submitButton.classList.remove('icon-spinner2');

        this.running = false;
    }
}
