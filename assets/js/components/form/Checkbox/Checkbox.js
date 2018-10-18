export default class {
    constructor(element) {
        this.element   = element;
        this.container = this.getOrCreateContainer();

        this.createBody();

        this.handleSelectionKeyboardEvent();
    }

    getOrCreateContainer() {
        if (this.element.parentNode.tagName === 'LABEL') {
            this.element.parentNode.classList.add('checkbox');
            this.element.parentNode.tabIndex = 0;

            return this.element.parentNode;
        }

        const container = document.createElement('label');

        container.classList.add('checkbox');

        this.element.parentNode.insertBefore(container, this.element);

        container.appendChild(this.element);

        container.tabIndex = 0;

        return container;
    }

    createBody() {
        const body  = document.createElement('div');
        const check = document.createElement('div');

        body.classList.add('body');
        check.classList.add('check');

        body.appendChild(check);

        this.element.parentNode.insertBefore(body, this.element.nextSibling);
    }

    handleSelectionKeyboardEvent() {
        this.container.addEventListener('keydown', (e) => {
            if (e.keyCode !== 32) {
                return;
            }

            e.preventDefault();
            e.stopPropagation();

            this.element.checked = !this.element.checked;
        });
    }
}
