import HandleRepositoryDeleteModal from "./HandleRepositoryDeleteModal";

export default class {
    constructor(livePreview) {
        this.livePreview = livePreview;

        document.addEventListener('click', this.showConfirmationModal.bind(this));
    }

    showConfirmationModal(e) {
        if (!e.target.classList.contains('delete-repository') && !e.target.parentNode.classList.contains('delete-repository')) {
            return;
        }

        e.preventDefault();

        const url = e.target.classList.contains('delete-repository') ? e.target.getAttribute('href') : e.target.parentNode.getAttribute('href');

        const request = new XMLHttpRequest();

        request.addEventListener('load', this.renderModal.bind(this));
        request.open('GET', url);
        request.send();
    }

    renderModal(response) {
        const parsedHtml = new DOMParser().parseFromString(response.target.responseText, 'text/html');

        document.querySelector('body').appendChild(parsedHtml.querySelector('.modal'));

       new HandleRepositoryDeleteModal(document.querySelector('.modal'), this.livePreview).show();
    }
}
