import * as bootstrap from 'bootstrap'

export const dialog = (message, confirmFun, type) => {
    const body = document.querySelector('body')
    const wrapper = document.createElement('div')

    wrapper.className = 'modal'
    wrapper.setAttribute('id', 'modal-dialog')

    wrapper.innerHTML = `<div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-type">
                <i class="bi bi-question-lg"></i>
            </div>
            <div class="modal-body">
                <div class="row gy-4 text-center justify-content-center">
                    <div class="col-12">
                        <div class="title">${message}</div>
                    </div>
                    <div class="col-12">
                        <div class="row gx-3 justify-content-center">
                            <div class="col-auto">
                                <button type="button" id="confirm" class="btn btn-save btn-primary">OK</button>
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-save btn-outline-primary close-btn" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>`

    body.append(wrapper)

    const modalEl = document.getElementById('modal-dialog')
    const confirmEl = document.getElementById('confirm')

    const getInstance = new bootstrap.Modal(modalEl, {
        keyboard: false
    })

    confirmEl.addEventListener('click', () => {
        getInstance.hide()
        confirmFun()
    })

    modalEl.addEventListener('hidden.bs.modal', event => {
        event.target.remove()
        getInstance.dispose()
    })

    return getInstance
}
