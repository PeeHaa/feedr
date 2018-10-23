import XhrSubmission from './XhrSubmission';

class Plugin {
    init() {
        document.addEventListener('submit', (e) => {
            if (!e.target.classList.contains('xhr')) {
                return;
            }

            e.preventDefault();

            this.handleForm(e.target);
        });
    }

    handleForm(form) {
        new XhrSubmission(form).process();
    }
}

export default function() {
    new Plugin().init();
}
