<template>
    <Field
        :name="fieldName"
        v-model="data"
        v-slot="{ field, value, handleChange, handleBlur, errorMessage }"
        :rules="filedRules">
        <ckeditor
            v-bind="field"
            :editor="ClassicEditor"
            :config="editorConfig"
            :disableTwoWayDataBinding="disableTwoWayDataBinding"
            :class="{'border-danger': errorMessage}"
            :model-value="value"
            @ready="onEditorReady"
            @input="handleChange"
            @blur="handleBlur"
        />
    </Field>
</template>
<script setup>
    import { ref, reactive, toRef, computed } from 'vue'
    import { ClassicEditor } from '@ckeditor/ckeditor5-editor-classic'
    import { Alignment } from '@ckeditor/ckeditor5-alignment'
    import { Autoformat } from '@ckeditor/ckeditor5-autoformat'
    import { Bold, Italic, Underline, Strikethrough, Subscript, Superscript } from '@ckeditor/ckeditor5-basic-styles'
    import { BlockQuote } from '@ckeditor/ckeditor5-block-quote'
    import { Essentials } from '@ckeditor/ckeditor5-essentials'
    import { Font } from '@ckeditor/ckeditor5-font'
    import { Heading } from '@ckeditor/ckeditor5-heading';
    import { Image, ImageInsert, ImageResize,  ImageCaption, ImageStyle, ImageToolbar, ImageUpload } from '@ckeditor/ckeditor5-image'
    import { Indent, IndentBlock } from '@ckeditor/ckeditor5-indent'
    import { Link } from '@ckeditor/ckeditor5-link'
    import { List } from '@ckeditor/ckeditor5-list'
    import { MediaEmbed } from '@ckeditor/ckeditor5-media-embed'
    import { Paragraph } from '@ckeditor/ckeditor5-paragraph'
    import { PasteFromOffice } from '@ckeditor/ckeditor5-paste-from-office'
    import { Table, TableToolbar } from '@ckeditor/ckeditor5-table'
    import { SourceEditing } from '@ckeditor/ckeditor5-source-editing'
    import { TextTransformation } from '@ckeditor/ckeditor5-typing'

    import CKEditorUploadAdapter from '@utils/ckeditor-upload-adapter'

    import VideoUploadAdapter from '@utils/ckeditor-video-upload-adapter'

    import { Field } from 'vee-validate'

    const data = defineModel('data')
    const props = defineProps({
        name: String,
        rules: String,
        imageUpload: Boolean,
        mediaEmbed: Boolean,
        toolbar: Array
    })

    const fieldName = toRef(props, 'name')
    const filedRules = toRef(props, 'rules')
    const imageUpload = toRef(props, 'imageUpload')
    const mediaEmbed = toRef(props, 'mediaEmbed')
    const toolbar = toRef(props, 'toolbar')

    // const { errorMessage, handleBlur, handleChange } = useField(fieldName, filedRules)

    const disableTwoWayDataBinding = ref(true)

    const toolbarItem = computed(() => {
        let item = [
            'undo',
            'redo',
            '|',
            'sourceEditing',
            '|',
            'heading',
            '|',
            'alignment',
            'fontColor',
            'fontBackgroundColor',
            'bold',
            'italic',
            'underline',
            'blockQuote',
            'strikethrough',
            'subscript',
            'superscript',
            '|',
            'link',
            //'insertImage',
            //'insertTable',
            //'mediaEmbed',
            '|',
            'bulletedList',
            'numberedList',
            'outdent',
            'indent',
        ]

        if(imageUpload.value){
            let insertImageIdx = item.indexOf('link')
            item.splice(insertImageIdx + 1, 0, 'insertImage')
        }
        if(mediaEmbed.value){
            let insertImageIdx = imageUpload.value ? item.indexOf('insertImage') : item.indexOf('link')
            item.splice(insertImageIdx + 1, 0, 'mediaEmbed')
        }
        else if(toolbar.value?.length){
            return item.filter(item => toolbar.value.includes(item))
        }

        return item

    })


    const colors = computed(() => {
        let item = [
            {
                color: '#8AB445',
                label: 'Apple Green'
            },
            {
                color: '#94D626',
                label: 'Yellow Green'
            },
            {
                color: '#726659',
                label: 'Pastel Brown',
                hasBorder: true
            },
            {
                color: '#AAA096',
                label: 'Greige'
            },
            {
                color: '#F1F2E3',
                label: 'Ivory'
            },
            {
                color: '#E6DDD4',
                label: 'Light Nude'
            },
            {
                color: '#F7F2EC',
                label: 'Isabelline'
            },
            {
                color: '#F9F9F9',
                label: 'White Smoke',
                hasBorder: true
            },
            {
                color: '#343741',
                label: 'Squid Ink',
            },
            {
                color: '#000000',
                label: 'Black'
            },
            {
                color: '#231F20',
                label: 'Raisin Black'
            },
            {
                color: '#ffffff',
                label: 'White',
                hasBorder: true
            }
        ]

        return item
    })


    const editorConfig = ref({
        plugins: [
            Alignment,
            Autoformat,
            BlockQuote,
            Bold,
            Essentials,
            Font,
            Heading,
            Image,
            ImageCaption,
            ImageStyle,
            ImageToolbar,
            ImageUpload,
            ImageInsert,
            ImageResize,
            Indent,
            IndentBlock,
            Italic,
            Link,
            List,
            MediaEmbed,
            Paragraph,
            PasteFromOffice,
            SourceEditing,
            Strikethrough,
            Subscript,
            Superscript,
            TextTransformation,
            Table,
            TableToolbar,
            Underline
        ],
        toolbar: toolbarItem,
        image: {
            toolbar: [
                'imageTextAlternative',
                'toggleImageCaption',
                'imageStyle:inline',
                'imageStyle:block',
                'imageStyle:side',
            ]
        },
        table: {
            contentToolbar: [
                'tableColumn',
                'tableRow',
                'mergeTableCells'
            ]
        },
        fontColor: {
            colors: colors
        },
        fontBackgroundColor:{
            colors: colors
        },
        extraPlugins: [customUploadAdapterPlugin]
    })


    function customUploadAdapterPlugin(editor) {
        editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
            return new CKEditorUploadAdapter(loader)
        }
    }

    const onEditorReady = (editor) => {
        if(editor.ui.isReady){
            disableTwoWayDataBinding.value = false
        }
    }

</script>
