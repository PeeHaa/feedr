export default class {
    constructor(form) {
        this.form   = form;
        this.method = form.hasAttribute('method') ? form.getAttribute('method').toUpperCase() : 'GET';
        this.action = form.getAttribute('action');
    }

    process() {
        const request = new XMLHttpRequest();

        request.addEventListener('load', this.processSuccessfulRequest.bind(this));
        request.addEventListener('error', this.processFailedRequest.bind(this));
        request.open(this.method, this.action);
        request.send(this.buildFormBody());
    }

    buildFormBody() {
        const formBody = new FormData();

        this.form.querySelectorAll('input, select, textarea').forEach((element) => {
            if (!element.hasAttribute('name')) {
                return;
            }

            formBody.append(element.getAttribute('name'), element.value);
        });

        return formBody;
    }

    processSuccessfulRequest(xhr) {
        const parsedHtml = new DOMParser().parseFromString(JSON.parse(xhr.target.responseText)['content'], 'text/html');

        document.querySelector('body').appendChild(parsedHtml.querySelector('.modal'));

        document.querySelector('.modal').classList.add('show');
    }

    processFailedRequest() {

    }
}
