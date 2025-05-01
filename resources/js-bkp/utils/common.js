import dayjs from 'dayjs'

export const format = (date) => {
    return dayjs(date).format('D MMMM, YYYY')
}

export const currFormat = (num) => {
    const curr = new Intl.NumberFormat('en-IN', {
        maximumFractionDigits: 0,     
        currency: 'INR'
    })
     return curr.format(num)
}

