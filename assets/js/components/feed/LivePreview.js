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
            this.subscribeNewRepository(repositoryRow.querySelector('td').textContent)
        });
    }

    subscribeNewRepository(name) {
        this.websocket.subscribeToChannel(name);
    }

    processNewReleases(message) {
        if (message.command !== 'newReleases') {
            return;
        }

        Object.keys(message.releases).forEach((releaseId) => {
            this.processNewRelease(message.releases[releaseId]);
        });
    }

    processNewRelease(release) {
        if (!this.isReleaseNew(release)) {
            return;
        }

        const releaseHtml = this.buildReleaseHtml(release);

        for (let i = 0; i < this.previewPanel.querySelectorAll('.media').length; i++) {
            if (this.previewPanel.querySelectorAll('.media')[i].dataset.timestamp < release.publishedDate) {
                this.previewPanel.querySelectorAll('.media')[i].before(releaseHtml);
                return;
            }
        }

        this.previewPanel.appendChild(releaseHtml);
    }

    isReleaseNew(release) {
        for (let i = 0; i < this.previewPanel.querySelectorAll('.media').length; i++) {
            if (this.previewPanel.querySelectorAll('.media')[i].dataset.id == release.id) {
                return false;
            }
        }

        return true;
    }

    buildReleaseHtml(release) {
        const mediaElement = document.createElement('div');
        const avatar       = document.createElement('img');
        const mediaBody    = document.createElement('div');
        const title        = document.createElement('h5');
        const link         = document.createElement('a');
        const content      = document.createElement('p');

        mediaElement.classList.add('media');
        mediaElement.dataset.id        = release.id;
        mediaElement.dataset.timestamp = release.publishedDate;

        avatar.classList.add('mr3');
        avatar.setAttribute('src', release.repository.owner.avatarUrl);
        avatar.setAttribute('alt', 'avatar');

        mediaBody.classList.add('media-body');

        title.classList.add('mt-0');

        link.setAttribute('href', release.url);
        link.setAttribute('target', '_blank');

        content.textContent = release.body;
        link.textContent    = release.name + ' - ' + release.repository.fullName;

        title.appendChild(link);
        mediaBody.appendChild(title);
        mediaBody.appendChild(content);

        mediaElement.appendChild(avatar);
        mediaElement.appendChild(mediaBody);

        return mediaElement;
    }
}
