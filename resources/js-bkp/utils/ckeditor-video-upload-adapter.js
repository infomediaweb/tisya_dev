import axios from "axios"
import Plugin from '@ckeditor/ckeditor5-core/src/plugin'
import { Locale } from '@ckeditor/ckeditor5-utils'
import { addToolbarToDropdown, CollapsibleView, ButtonView, SplitButtonView, createDropdown } from '@ckeditor/ckeditor5-ui'
import media from '@ckeditor/ckeditor5-media-embed/theme/icons/media.svg'

class VideoUploadAdapter extends Plugin {
    init() {
        const editor = this.editor
        const locale = new Locale()
        const upload = new ButtonView()
        const collapse = new CollapsibleView()

        upload.set({
            label: 'Upload from computer',
            icon: media,
            withText: true,
            tooltip: 'Upload from computer'
        })

        collapse.set({
            label: 'Insert video via URL',
        })

        const buttons = [ upload, collapse ]

        editor.ui.componentFactory.add('mediaEmbed', () => {
            const splitButtonDropdown = createDropdown(locale, SplitButtonView)

            addToolbarToDropdown(splitButtonDropdown, buttons)

            splitButtonDropdown.buttonView.set({
                label: 'Upload video',
                icon: media,
                withText: false,
                tooltip: 'Upload video from computer'
            })

            return splitButtonDropdown

        })

    }
}
  
export default VideoUploadAdapter