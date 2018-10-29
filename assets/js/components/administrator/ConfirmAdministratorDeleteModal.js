import HandleAdministratorDeleteModal from "./HandleAdministratorDeleteModal";

export default class {
    constructor() {
        document.addEventListener('click', this.showConfirmationModal.bind(this));
    }

    showConfirmationModal(e) {
        if (!e.target.classList.contains('delete-administrator') && !e.target.parentNode.classList.contains('delete-administrator')) {
            return;
        }

        e.preventDefault();

        const url = e.target.classList.contains('delete-administrator') ? e.target.getAttribute('href') : e.target.parentNode.getAttribute('href');

        const request = new XMLHttpRequest();

        request.addEventListener('load', this.renderModal.bind(this));
        request.open('GET', url);
        request.send();
    }

    renderModal(response) {
        const parsedHtml = new DOMParser().parseFromString(response.target.responseText, 'text/html');

        document.querySelector('body').appendChild(parsedHtml.querySelector('.modal'));

       new HandleAdministratorDeleteModal(document.querySelector('.modal')).show();
    }
}
