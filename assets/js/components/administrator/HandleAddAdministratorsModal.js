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
        const administrators = JSON.parse(response.target.responseText);

        Object.keys(administrators).forEach((key) => {
            this.insertNewAdministrator(administrators[key]);
        });

        document.querySelector('form.searchUsers input[name="query"]').value = '';

        this.close();
    }

    processFailedRequest() {
        this.close();
    }

    insertNewAdministrator(administrator) {
        const rows = document.querySelectorAll('table.administrators tr');

        for (let i = 0; i < rows.length; i++) {
            if (administrator.username > rows[i].querySelector('td:nth-child(2)').textContent) {
                continue;
            }

            rows[i].parentNode.insertBefore(this.buildNewAdministratorRow(administrator), rows[i]);

            return;
        }

        document.querySelector('table.administrators tbody').appendChild(this.buildNewAdministratorRow(administrator));
    }

    buildNewAdministratorRow(administrator) {
        const row = document.createElement('tr');

        const avatarColumn   = document.createElement('td');
        const usernameColumn = document.createElement('td');
        const actionsColumn  = document.createElement('td');

        avatarColumn.classList.add('avatar');
        actionsColumn.classList.add('actions');

        const avatar       = document.createElement('img');
        const deleteButton = document.createElement('button');

        avatar.src = administrator.avatarUrl;

        deleteButton.classList.add('btn', 'btn-danger');
        deleteButton.textContent = 'D';

        avatarColumn.appendChild(avatar);
        usernameColumn.textContent = administrator.username;
        actionsColumn.appendChild(deleteButton);

        row.appendChild(avatarColumn);
        row.appendChild(usernameColumn);
        row.appendChild(actionsColumn);

        return row;
    }
}
