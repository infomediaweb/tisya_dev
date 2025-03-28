export const validateFile = (file, error, size = 2) => {
   const allowedTypes = ['image/jpg', 'image/jpeg', 'image/png', 'image/webp', 'image/svg+xml', 'video/mp4', 'application/pdf']
   const maxSizeInBytes = size * 1024 * 1024

   if(file.size > maxSizeInBytes){
      return false
   }
   if (error) {
      return false
   }
   if (!allowedTypes.includes(file.type)) {
      return false
   }
}