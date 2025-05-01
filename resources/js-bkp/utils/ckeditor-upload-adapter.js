import axios from "axios"

class CustomUploadAdapter {
    constructor(loader) {
      this.loader = loader
    }

    upload() {
        return this.loader.file.then(uploadedFile => {
            return new Promise((resolve, reject) => {
                const formData = new FormData()
                formData.append('file', uploadedFile)
                formData.append('type', 'ckeditor')

                axios.post('/api/upload', formData).then(res => {
                    if (res.data.status) {
                        resolve({
                            default: res.data.filepath
                        });
                    } else {
                        reject(res.data.message)
                    }
                }).catch(res => {
                    reject('Upload failed')
                })
            })
        })
    }
  
    abort() {}
  }
  
  export default CustomUploadAdapter