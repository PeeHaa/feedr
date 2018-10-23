export default class {
    constructor(modal) {
        this.modal = modal;
    }

    close() {
        this.modal.classList.remove('show');

        setTimeout(() => {
            this.modal.parentNode.removeChild(this.modal);
        }, 1000);
    }
}
