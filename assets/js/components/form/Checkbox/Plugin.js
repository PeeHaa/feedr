import Checkbox from './Checkbox';

class Plugin {
    init() {
        document.querySelectorAll('input[type="checkbox"]').forEach(this.convertSelectWhenNeeded.bind(this));
    }

    convertSelectWhenNeeded(element) {
        new Checkbox(element);
    }
}

export default function() {
    new Plugin().init();
}
