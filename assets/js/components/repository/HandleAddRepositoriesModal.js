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
        }, 1500);
    }

    submit(event) {
        if (this.running) {
            return;
        }

        event.preventDefault();

        this.disableSubmitButton();

        const request = new XMLHttpRequest();

        request.addEventListener('load', this.processSuccessfulRequest.bind(this));
        request.addEventListener('error', this.processFailedRequest.bind(this));
        request.open('POST', this.form.getAttribute('action'));
        request.send(buildFormBody(this.form));
    }

    disableSubmitButton() {
        this.form.querySelector('button[type="submit"]').textContent = '';

        const spinner = document.createElement('i');

        spinner.classList.add('icon-spinner2');

        this.form.querySelector('button[type="submit"]').appendChild(spinner);

        this.running = true;
    }

    processSuccessfulRequest(response) {
        const result = JSON.parse(response.target.responseText);

        Object.keys(result.repositories).forEach((key) => {
            this.insertNewRepository(result.repositories[key], result.feed);

            this.livePreview.subscribeNewRepository(result.repositories[key].fullName);
        });

        document.querySelector('form.searchRepositories input[name="query"]').value = '';

        this.close();
    }

    processFailedRequest() {
        this.close();
    }

    insertNewRepository(repository, feed) {
        const noResultsRow = document.querySelector('table.repositories tr td[colspan="2"]');

        if (noResultsRow) {
            noResultsRow.parentNode.parentNode.removeChild(noResultsRow.parentNode);
        }

        const rows = document.querySelectorAll('table.repositories tr');

        for (let i = 0; i < rows.length; i++) {
            if (repository.fullName.toLowerCase() > rows[i].querySelector('td:nth-child(1)').textContent.toLowerCase()) {
                continue;
            }

            rows[i].parentNode.insertBefore(this.buildNewRepositoryRow(repository, feed), rows[i]);

            return;
        }

        document.querySelector('table.repositories tbody').appendChild(this.buildNewRepositoryRow(repository, feed));
    }

    buildNewRepositoryRow(repository, feed) {
        const row = document.createElement('tr');

        const nameColumn = document.createElement('td');
        const actionsColumn  = document.createElement('td');

        actionsColumn.classList.add('actions');

        const deleteButton = document.createElement('a');

        deleteButton.classList.add('btn', 'btn-danger', 'delete-repository');
        deleteButton.setAttribute('href', '/feeds/' + feed.id + '/' + feed.slug + '/repositories/' + repository.id + '/delete');

        const deleteButtonIcon = document.createElement('i');

        deleteButtonIcon.classList.add('icon-cross');

        deleteButton.appendChild(deleteButtonIcon);

        nameColumn.textContent = repository.fullName;
        actionsColumn.appendChild(deleteButton);

        row.dataset.id = repository.id;

        row.appendChild(nameColumn);
        row.appendChild(actionsColumn);

        return row;
    }
}
