export default function(form) {
    const formBody = new FormData();

    form.querySelectorAll('input, select, textarea').forEach((element) => {
        if (!element.hasAttribute('name')) {
            return;
        }

        if (element.hasAttribute('type') && element.getAttribute('type') === 'checkbox' && !element.checked) {
            return;
        }

        formBody.append(element.getAttribute('name'), element.value);
    });

    return formBody;
}
