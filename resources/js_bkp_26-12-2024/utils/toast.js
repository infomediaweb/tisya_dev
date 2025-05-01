import * as bootstrap from 'bootstrap'

export const toast = (message, messageType, delay = 2000) => {
    let toastEl = document.querySelector('.toast')
    let toastIconEl = document.querySelector('#icon')
    let toastContentEl = document.querySelector('#toastContent')

    if(message){
        toastContentEl.innerHTML = message
    }
    
    if(messageType == 'error'){
        toastEl.classList.add('text-bg-danger')
        toastIconEl.classList.add('bi-info-circle')
    }
    else if(messageType == 'success'){
        toastEl.classList.add('text-bg-success')
        toastIconEl.classList.add('bi-check-circle')
    }
    else{
        // toastEl.classList.add('text-bg-success');
        // toastIconEl.classList.add('bi-check-circle');
    }

    toastEl.addEventListener('show.bs.toast', () => {
        toastEl.classList.remove('show')
    });

    toastEl.addEventListener('hide.bs.toast', () => {
        toastEl.classList.remove(toastEl.classList[2])
        toastIconEl.classList.remove(toastIconEl.classList[3])
    });
    
    return bootstrap.Toast.getOrCreateInstance(toastEl, {
        animation: false,
        delay: delay
    });
}
