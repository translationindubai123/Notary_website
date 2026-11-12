document.addEventListener('click', function (event) {
    const removeButton = event.target.closest('.remove-row');
    if (removeButton) {
        const rows = removeButton.closest('.repeater-list');
        if (rows && rows.children.length > 1) {
            removeButton.closest('.repeater-row').remove();
        } else if (removeButton.closest('.repeater-row')) {
            removeButton.closest('.repeater-row').querySelectorAll('input, textarea').forEach(function (field) {
                field.value = '';
            });
        }
        return;
    }

    const addButton = event.target.closest('.add-row');
    if (!addButton) {
        return;
    }

    const list = document.getElementById(addButton.dataset.target);
    const template = document.getElementById(addButton.dataset.template);
    if (!list || !template || list.children.length >= 50) {
        return;
    }

    const row = template.content.cloneNode(true);
    const index = list.children.length;
    row.querySelectorAll('[data-name]').forEach(function (field) {
        field.name = field.dataset.name.replace('INDEX', index);
    });
    list.appendChild(row);
});
