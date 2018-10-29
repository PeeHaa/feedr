import websocket from "../../helpers/websocket";

export default class {
    constructor() {
        this.previewPanel = document.querySelector('.live-preview');

        if (!this.previewPanel) {
            return;
        }

        this.websocket = websocket();

        this.websocket.connect(this.subscribeExistingRepositories.bind(this), this.processNewReleases.bind(this));
    }

    subscribeExistingRepositories() {
        document.querySelectorAll('.table.repositories tr').forEach((repositoryRow) => {
            this.websocket.subscribeToChannel(repositoryRow.querySelector('td').textContent);
        });
    }

    processNewReleases() {

    }
}
