<script src="/ckeditor/ckeditor.js"></script>
<?php if (l("lang__name") !== "en"): ?><script src="/ckeditor/translations/<?= l("lang__name") ?>.js"></script><?php endif; ?>

<script>
    let editor;
    let lastData = null;

    ClassicEditor
        .create(document.querySelector( '#editor-box' ), {
            toolbar: ["undo", "redo", "|", "heading", "|", "bold", "italic", {
                label: "<?= l("lang_editor_items") ?>",
                icon: "text",
                items: ["subscript", "superscript", "strikethrough", "code", "underline", "removeFormat"]
            }, "|", "alignment", "bulletedList", "numberedList", "|", "link", "blockQuote", <?php if (isset($_COOKIE["DeltaKiosk"])): ?> "insertTable", <?php else: ?> {
                label: "<?= l("lang_editor_add") ?>",
                icon: "plus",
                items: ["insertImage", "insertTable", "mediaEmbed"]
            }, <?php endif; ?> "|", "specialCharacters"],
            language: {
                ui: "<?= l("lang__name") ?>",
                content: "<?= l("lang__name") ?>"
            },
            simpleUpload: {
                uploadUrl: '/embed/'
            },
            fullPage: true
        })
        .then((newEditor) => {
            editor = window.error = newEditor;

            setInterval(() => {
                if (lastData) {
                    if (lastData !== editor.getData()) {
                        if (window.oneditorchange) window.oneditorchange(editor.getData());
                        lastData = editor.getData();
                    }
                } else {
                    lastData = editor.getData();
                }
            });
        })
</script>
<style>
    .ck, .ck * {
        font-family: "Nunito", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", "Noto Sans", "Liberation Sans", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji" !important;
    }

    :root {
        /* Overrides the border radius setting in the theme. */
        --ck-border-radius: 4px;

        /* Overrides the default font size in the theme. */
        --ck-font-size-base: 14px;

        /* Helper variables to avoid duplication in the colors. */
        --ck-custom-background: var(--bs-tertiary-bg);
        --ck-custom-foreground: var(--palette-5);
        --ck-custom-border: var(--palette-3);
        --ck-custom-white: hsl(0, 0%, 100%);

        /* -- Overrides generic colors. ------------------------------------------------------------- */

        --ck-color-base-foreground: var(--ck-custom-background);
        --ck-color-focus-border: hsl(208, 90%, 62%);
        --ck-color-text: var(--palette-6);
        --ck-color-shadow-drop: hsla(0, 0%, 0%, 0.2);
        --ck-color-shadow-inner: hsla(0, 0%, 0%, 0.1);

        /* -- Overrides the default .ck-button class colors. ---------------------------------------- */

        --ck-color-button-default-background: var(--ck-custom-background);
        --ck-color-button-default-active-shadow: hsl(270, 2%, 23%);
        --ck-color-button-default-disabled-background: var(--ck-custom-background);

        --ck-color-button-on-background: var(--ck-custom-foreground);
        --ck-color-button-on-active-background: hsl(255, 4%, 14%);
        --ck-color-button-on-active-shadow: hsl(240, 3%, 19%);
        --ck-color-button-on-disabled-background: var(--ck-custom-foreground);

        --ck-color-button-action-background: hsl(168, 76%, 42%);
        --ck-color-button-action-hover-background: hsl(168, 76%, 38%);
        --ck-color-button-action-active-background: hsl(168, 76%, 36%);
        --ck-color-button-action-active-shadow: hsl(168, 75%, 34%);
        --ck-color-button-action-disabled-background: hsl(168, 76%, 42%);
        --ck-color-button-action-text: var(--ck-custom-white);

        --ck-color-button-save: hsl(120, 100%, 46%);
        --ck-color-button-cancel: hsl(15, 100%, 56%);

        /* -- Overrides the default .ck-dropdown class colors. -------------------------------------- */

        --ck-color-dropdown-panel-background: var(--ck-custom-background);
        --ck-color-dropdown-panel-border: var(--ck-custom-foreground);

        /* -- Overrides the default .ck-splitbutton class colors. ----------------------------------- */

        --ck-color-split-button-hover-background: var(--ck-color-button-default-hover-background);
        --ck-color-split-button-hover-border: var(--ck-custom-foreground);

        /* -- Overrides the default .ck-input class colors. ----------------------------------------- */

        --ck-color-input-background: var(--ck-custom-background);
        --ck-color-input-border: hsl(257, 3%, 43%);
        --ck-color-input-text: hsl(0, 0%, 98%);
        --ck-color-input-disabled-background: hsl(255, 4%, 21%);
        --ck-color-input-disabled-border: hsl(250, 3%, 38%);
        --ck-color-input-disabled-text: hsl(0, 0%, 78%);

        /* -- Overrides the default .ck-labeled-field-view class colors. ---------------------------- */

        --ck-color-labeled-field-label-background: var(--ck-custom-background);

        /* -- Overrides the default .ck-list class colors. ------------------------------------------ */

        --ck-color-list-background: var(--ck-custom-background);
        --ck-color-list-button-hover-background: var(--palette-5);
        --ck-color-list-button-on-background: var(--palette-5);
        --ck-color-list-button-on-text: var(--ck-color-base-background);

        /* -- Overrides the default .ck-balloon-panel class colors. --------------------------------- */

        --ck-color-panel-background: var(--ck-custom-background);
        --ck-color-panel-border: var(--ck-custom-border);

        /* -- Overrides the default .ck-toolbar class colors. --------------------------------------- */

        --ck-color-toolbar-background: var(--ck-custom-background);
        --ck-color-toolbar-border: var(--ck-custom-border);

        /* -- Overrides the default .ck-tooltip class colors. --------------------------------------- */

        --ck-color-tooltip-background: hsl(252, 7%, 14%);
        --ck-color-tooltip-text: hsl(0, 0%, 93%);

        /* -- Overrides the default colors used by the ckeditor5-image package. --------------------- */

        --ck-color-image-caption-background: var(--bs-body-bg);
        --ck-color-image-caption-text: var(--bs-body-color);

        /* -- Overrides the default colors used by the ckeditor5-widget package. -------------------- */

        --ck-color-widget-blurred-border: hsl(0, 0%, 87%);
        --ck-color-widget-hover-border: hsl(43, 100%, 68%);
        --ck-color-widget-editable-focus-background: var(--ck-custom-white);

        /* -- Overrides the default colors used by the ckeditor5-link package. ---------------------- */

        --ck-color-link-default: hsl(190, 100%, 75%);

        /* CUSTOM OPTIONS */
        --ck-color-button-on-color: var(--bs-link-color);
        --ck-color-button-on-hover-background: var(--palette-3);
        --ck-color-button-default-hover-background: var(--palette-4);
        --ck-color-list-button-on-background-focus: var(--palette-1);
        --ck-color-base-background: transparent;
        --ck-color-button-default-active-background: var(--palette-2);
    }

    :root {
        --ck-color-base-border: var(--bs-secondary-bg);
        --ck-focus-outer-shadow: transparent;
        --ck-focus-ring: 1px solid transparent;
    }

    .ck.ck-button.ck-on.ck-button_with-text {
        color: var(--bs-body-color) !important;
    }

    .ck.ck-content {
        background-color: var(--bs-body-bg) !important;
        color: var(--bs-body-color) !important;
    }

    .ck.ck-content.ck-focused {
        border-color: var(--bs-link-color) !important;
    }

    /*:root {
        --ck-color-base-border: var(--bs-secondary-bg);
        --ck-color-button-on-color: var(--bs-link-color);
        --ck-focus-outer-shadow: transparent;
        --ck-focus-ring: 1px solid transparent;
    }

    .ck.ck-content {
        background-color: var(--bs-body-bg) !important;
        color: var(--bs-body-color) !important;
    }

    .ck.ck-content.ck-focused {
        border-color: var(--bs-link-color) !important;
    }

    .ck.ck-toolbar.ck-toolbar_grouping {
        background-color: var(--bs-tertiary-bg) !important;
    }

    .ck-button, .ck-file-dialog-button .ck.ck-icon.ck-reset_all-excluded.ck-icon_inherit-color.ck-button__icon {
        filter: invert(1) hue-rotate(180deg);
    }

    .ck.ck-input.ck-input-text_empty.ck-input-text {
        background-color: var(--bs-tertiary-bg) !important;
    }

    .ck-toolbar__separator {
        background-color: var(--bs-body-bg) !important;
    }

    .ck-button.ck-on {
        background-color: var(--palette-5) !important;
        filter: none !important;
    }

    .ck.ck-reset.ck-dropdown__panel.ck-dropdown__panel_se {
        background-color: var(--bs-tertiary-bg) !important;
    }*/
</style>