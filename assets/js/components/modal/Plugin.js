import Modal from './Modal';

class Plugin {
    init() {
        document.addEventListener('click', (e) => {
            if (!this.isValidTarget(e.target)) {
                return;
            }

            e.preventDefault();

            this.handleClose(e.target);
        });
    }

    isValidTarget(target) {
        if ('dismiss' in target.dataset === false) {
            return false;
        }

        return target.dataset.dismiss === 'modal';
    }

    handleClose(target) {
        const modal = this.getModal(target);

        if (modal === null) {
            return;
        }

        new Modal(modal).close();
    }

    getModal(target) {
        let parentNode = target.parentNode;

        do {
            console.log(parentNode);
           if (parentNode.classList.contains('modal')) {
               return parentNode;
           }
        } while (parentNode = parentNode.parentNode);

        return null;
    }
}

export default function() {
    new Plugin().init();
}
