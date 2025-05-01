<template>
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div class="toast border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="row flex-nowrap align-items-center">
                <div class="col-auto">
                    <div id="icon" class="ms-2 fs-4 bi"></div>
                </div>
                <div class="col ps-0">
                    <div id="toastContent" class="toast-body p-0"></div>
                </div>
                <div class="col-auto">
                    <span class="close-btn" data-bs-dismiss="toast" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
<style lang="scss" scoped>
    .btn-close-white{
        width: 16px;
        height: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background-color: #fff;
        border: 0;
        filter: opacity(0.7);
        transition: .3s ease-in-out;
        &:hover{
            filter: opacity(1);
        }
        .btn-close{
            width: 0.35em;
            height: 0.35em;
        }
    }
</style>