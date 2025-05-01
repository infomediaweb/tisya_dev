import dayjs from 'dayjs'

export const format = (date) => {
    return dayjs(date).format('D MMMM, YYYY')
}

export const dateRangeFormat = (date) => {
    let startDate = dayjs(date[0]).format('D MMMM, YYYY')
    let endDate = dayjs(date[1]).format('D MMMM, YYYY')

    return `${startDate} - ${endDate}`
}


export const shortFormat = (date) => {
    return dayjs(date).format('D MMM, YY')
}

export const standardFormat = (date) => {
    return dayjs(date).format('YYYY-MM-DD')
}

export const currFormat = (num) => {
    const curr = new Intl.NumberFormat('en-IN', {
        maximumFractionDigits: 2,
        currency: 'INR'
    })
     return curr.format(num)
}


export const currFormatTotal = (num) => {
    const curr = new Intl.NumberFormat('en-IN', {
        maximumFractionDigits: 0,
        currency: 'INR'
    })
     return curr.format(num)
}
