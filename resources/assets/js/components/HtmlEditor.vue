<template>
    <div style="border: 1px solid #ccc;">
        <Toolbar
            :editor="editor"
            :default-config="toolbarConfig"
            mode="default"
            style="border-bottom: 1px solid #ccc"
        />
        <Editor
            :default-config="editorConfig"
            mode="default"
            v-model="html"
            :style="{height: height + 'px', 'overflow-y': 'hidden'}"
            @onCreated="onCreated"
            @onChange="onChange"
        />
    </div>
</template>

<script>
    import '@wangeditor/editor/dist/css/style.css';
    import { Editor, Toolbar } from '@wangeditor/editor-for-vue';

    export default {
        components: {
            Editor,
            Toolbar,
        },
        props: {
            content: {
                type: String,
                default: '',
            },
            height: {
                type: Number,
                default: 300,
            },
        },
        data() {
            return {
                editor: null,
                html: this.content,
                toolbarConfig: {},
                editorConfig: {},
            };
        },
        watch: {
            content(value) {
                if (this.editor && value !== this.editor.getHtml()) {
                    this.html = value;
                }
            },
        },
        methods: {
            onCreated(editor) {
                this.editor = Object.seal(editor);
            },
            onChange(editor) {
                this.$emit('change', editor.getHtml());
            },
        },
        beforeUnmount() {
            if (this.editor) {
                this.editor.destroy();
                this.editor = null;
            }
        },
    }
</script>
