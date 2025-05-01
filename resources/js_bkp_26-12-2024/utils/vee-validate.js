import { defineRule, configure } from 'vee-validate'
import { alpha, required, email, min, max, confirmed, numeric, integer, min_value, max_value, image, ext, size, url, regex } from '@vee-validate/rules'


const veeValidate = () => {
    defineRule("alpha", alpha)
    defineRule("required", required)
    defineRule("email", email)
    defineRule("min", min)
    defineRule("max", max)
    defineRule("confirmed", (value, [target]) => {
        if(value === target){
            return
        }
        return 'Password and confirm password does not match'
    })
    defineRule("numeric", numeric)
    defineRule("min_value", min_value)
    defineRule("max_value", max_value)
    defineRule("image", image)
    defineRule("ext", ext)
    defineRule("size", size)
    defineRule("url", url)
    defineRule("decimal", (value) => {
        let regex = /^[0-9]*\.?[0-9]*$/
        if(regex.test(value)){
           return  
        }
        return 'The value must be a number'
    })
    configure({
        validateOnInput: true,
        generateMessage: (context) => {
            const messages = {
                alpha: 'Invalid field type. Only alphabetic characters are allowed.',
                required: 'This field is required.',
                email: 'The value must be a valid email address.',
                min: `Enter a minimum of ${context.rule.params[0]} characters.`,
                max: `Enter a maximum of ${context.rule.params[0]} characters.`,
                numeric: `The value must be a number.`,
                min_value: `The value must be greater than ${context.rule.params[0]}`,
                max_value: `The value must be less than ${context.rule.params[0]}`,
                image: `Invalid file type. Only images are allowed.`,
                ext: `Invalid file type. Only ${context.name} files (${context.rule.params}) are allowed for upload.`,
                size: `Maximum ${parseInt(context.rule.params[0] / 1024)}MB Allowed.`,
                url: `Invalid field type. Only URLs are allowed.`
            }
            return  messages[context.rule.name]
        }
    });
}

export default veeValidate