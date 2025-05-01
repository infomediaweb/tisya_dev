import { ref } from 'vue'

import { Alignment } from '@ckeditor/ckeditor5-alignment';
import { Autoformat } from '@ckeditor/ckeditor5-autoformat'
import { Bold, Italic, Underline } from '@ckeditor/ckeditor5-basic-styles'
import { BlockQuote } from '@ckeditor/ckeditor5-block-quote'
import { Essentials } from '@ckeditor/ckeditor5-essentials'
import { Heading } from '@ckeditor/ckeditor5-heading';
import { Image, ImageInsert,  ImageCaption, ImageStyle, ImageToolbar, ImageUpload } from '@ckeditor/ckeditor5-image'
import { Indent } from '@ckeditor/ckeditor5-indent'
import { Link } from '@ckeditor/ckeditor5-link'
import { List } from '@ckeditor/ckeditor5-list'
//import { MediaEmbed } from '@ckeditor/ckeditor5-media-embed'
import { Paragraph } from '@ckeditor/ckeditor5-paragraph'
import { PasteFromOffice } from '@ckeditor/ckeditor5-paste-from-office'
import { Table, TableToolbar } from '@ckeditor/ckeditor5-table'
import { SourceEditing } from '@ckeditor/ckeditor5-source-editing'
import { TextTransformation } from '@ckeditor/ckeditor5-typing'

import CKEditorUploadAdapter from '@utils/ckeditor-upload-adapter'


export const editorConfig = ref({
    plugins: [
        Alignment,
        Autoformat,
        BlockQuote,
        Bold,
        Essentials,
        Heading,
        Image,
        ImageCaption,
        ImageStyle,
        ImageToolbar,
        ImageUpload,
        ImageInsert,
        Indent,
        Italic,
        Link,
        List,
        //MediaEmbed,
        Paragraph,
        PasteFromOffice,
        SourceEditing,
        TextTransformation,
        Table,
        TableToolbar,
        Underline  
    ],
    toolbar: {
        items: [
            'undo',
            'redo',
            '|',
            'sourceEditing',
            '|',
            'heading',
            '|',
            'alignment',
            'bold',
            'italic',
            'underline',
            'blockQuote',
            '|',
            'link',
            'ImageInsert',
            'insertTable',
            // 'mediaEmbed', 
            '|',
            'bulletedList',
            'numberedList',
            'outdent',
            'indent',
        ] 
    },
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
})

export const onEditorReady = (editor) => {
    editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
        return new CKEditorUploadAdapter(loader)
    }
}