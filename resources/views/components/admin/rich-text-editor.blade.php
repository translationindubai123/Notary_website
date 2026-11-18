@props(['name', 'value' => '', 'label' => 'Description', 'rows' => 6])

<div class="rich-text-editor" data-rich-text-editor>
    <div class="rich-text-toolbar" role="toolbar" aria-label="Description formatting" data-toolbar hidden>
        <button class="rich-text-button" type="button" data-command="bold" aria-label="Bold" title="Bold"><strong>B</strong></button>
        <button class="rich-text-button" type="button" data-command="italic" aria-label="Italic" title="Italic"><em>I</em></button>
        <button class="rich-text-button" type="button" data-command="underline" aria-label="Underline" title="Underline"><u>U</u></button>
        <label class="visually-hidden" for="{{ $name }}-font-size">Font size</label>
        <select class="rich-text-size" id="{{ $name }}-font-size" aria-label="Font size" data-font-size>
            <option value="">Font size</option>
            <option value="small">Small</option>
            <option value="normal">Normal</option>
            <option value="large">Large</option>
        </select>
        <button class="rich-text-button" type="button" data-command="insertUnorderedList" aria-label="Bulleted list" title="Bulleted list">• List</button>
        <button class="rich-text-button" type="button" data-action="link" aria-label="Insert hyperlink" title="Insert hyperlink">Link</button>
    </div>
    <div class="rich-text-area" contenteditable="true" role="textbox" aria-multiline="true" aria-label="{{ $label ?? 'Description' }}" data-editor hidden>{!! \App\Support\RichText::sanitize($value ?? '') !!}</div>
    <textarea class="form-control rich-text-source" id="{{ $name }}" name="{{ $name }}" rows="{{ $rows ?? 6 }}">{{ $value ?? '' }}</textarea>
</div>
