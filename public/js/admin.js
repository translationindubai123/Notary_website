document.addEventListener('click', function (event) {
    const toolbarButton = event.target.closest('[data-rich-text-editor] [data-command], [data-rich-text-editor] [data-action="link"]');
    if (toolbarButton) {
        const editor = toolbarButton.closest('[data-rich-text-editor]');
        const area = editor.querySelector('[data-editor]');
        area.focus();
        if (toolbarButton.dataset.command) {
            document.execCommand(toolbarButton.dataset.command, false);
        } else {
            const url = window.prompt('Enter a web, email, or phone link:');
            if (url) {
                document.execCommand('createLink', false, url.trim());
            }
        }
        normalizeFontSizes(area);
        syncRichText(editor);
        return;
    }

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

function normalizeFontSizes(editor) {
    editor.querySelectorAll('font').forEach(function (font) {
        const sizeClass = {
            1: 'rich-text-size-small',
            2: 'rich-text-size-small',
            5: 'rich-text-size-large',
            6: 'rich-text-size-large',
            7: 'rich-text-size-large'
        }[font.getAttribute('size')];
        if (!sizeClass) {
            font.replaceWith(...font.childNodes);
            return;
        }

        const span = document.createElement('span');
        span.className = sizeClass;
        span.append(...font.childNodes);
        font.replaceWith(span);
    });
}

function syncRichText(editor) {
    const area = editor.querySelector('[data-editor]');
    const source = editor.querySelector('.rich-text-source');
    normalizeFontSizes(area);
    source.value = area.innerHTML;
}

document.querySelectorAll('[data-rich-text-editor]').forEach(function (editor) {
    const area = editor.querySelector('[data-editor]');
    const source = editor.querySelector('.rich-text-source');
    const toolbar = editor.querySelector('[data-toolbar]');
    const sizeSelect = editor.querySelector('[data-font-size]');
    let savedRange = null;

    function saveSelection() {
        const selection = window.getSelection();
        if (selection.rangeCount && area.contains(selection.anchorNode)) {
            savedRange = selection.getRangeAt(0).cloneRange();
        }
    }

    function restoreSelection() {
        if (!savedRange) {
            return;
        }
        const selection = window.getSelection();
        selection.removeAllRanges();
        selection.addRange(savedRange);
    }

    area.addEventListener('keyup', saveSelection);
    area.addEventListener('mouseup', saveSelection);
    area.addEventListener('input', function () {
        saveSelection();
        syncRichText(editor);
    });
    toolbar.querySelectorAll('button').forEach(function (button) {
        button.addEventListener('mousedown', function (event) {
            event.preventDefault();
        });
    });
    sizeSelect.addEventListener('mousedown', saveSelection);
    sizeSelect.addEventListener('change', function () {
        const sizes = { small: '2', normal: '3', large: '5' };
        if (sizes[sizeSelect.value]) {
            area.focus();
            restoreSelection();
            document.execCommand('fontSize', false, sizes[sizeSelect.value]);
            normalizeFontSizes(area);
            syncRichText(editor);
        }
        sizeSelect.value = '';
    });

    editor.closest('form').addEventListener('submit', function () {
        syncRichText(editor);
    });

    area.hidden = false;
    toolbar.hidden = false;
    source.hidden = true;
});
