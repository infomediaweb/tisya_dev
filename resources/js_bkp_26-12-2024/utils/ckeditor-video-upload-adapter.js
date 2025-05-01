import axios from "axios"
import Plugin from '@ckeditor/ckeditor5-core/src/plugin'
import { Locale, Collection } from '@ckeditor/ckeditor5-utils'
import { 
    addListToDropdown,
    addToolbarToDropdown, 
    ToolbarLineBreakView, 
    CollapsibleView, 
    ButtonView, 
    SplitButtonView, 
    createDropdown,
    InputView,
    createLabeledInputText,
    LabeledFieldView,
    ViewModel,
    ToolbarView   
} from '@ckeditor/ckeditor5-ui'
import media from '@ckeditor/ckeditor5-media-embed/theme/icons/media.svg'
import checkIcon from '@ckeditor/ckeditor5-core/theme/icons/check.svg'

class VideoUploadAdapter extends Plugin {
    init() {
        const editor = this.editor
        const locale = new Locale()
        const collection = new Collection()
        const toolbarCompact = new ToolbarView(locale)
        const newLine = new ToolbarLineBreakView(locale)
        const input = new LabeledFieldView(locale, createLabeledInputText)
        const upload = new ButtonView()
        const saveButton = new ButtonView()
        const collapse = new CollapsibleView()

        input.set({
            label: 'Insert video via URL',
        })

        saveButton.set({
            label: 'Save',
            withText: false,
            icon: checkIcon,
            class: 'ck-button-save'
        })

        collection.add({
            type: 'button',
            model: new ViewModel( {
                label: 'Upload from computer',
                icon: media,
                withText: true,
                tooltip: 'Upload from computer'
            })
        })

        collapse.set({
            label: 'Insert video via URL',
            isCollapsed: true,
        })

        collection.add({
            type: 'collapsible',
            model: new ViewModel({
                label: 'Insert video via URL',
                isCollapsed: true,
            }),
        })

        const button = [upload, collapse]
        
        editor.ui.componentFactory.add('mediaEmbed', () => {
            const splitButtonDropdown = createDropdown(locale, SplitButtonView)

            splitButtonDropdown.buttonView.set({
                label: 'Upload video',
                icon: media,
                withText: false,
                tooltip: 'Upload video from computer'
            })

            addListToDropdown(splitButtonDropdown, collection)

            
            return splitButtonDropdown
        })
    }
}
  
export default VideoUploadAdapter