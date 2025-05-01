<template>
    <div class="page-wrap property-add">
        <div class="page-title mb-4">
            <div class="row gy-3 align-items-center">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">New Booking</h1>
                </div>
                <div class="col-auto">
                    <router-link :to="{ name: 'manage-booking' }" class="btn rounded-pill btn-secondary-light">
                        <i class="icon-list me-2"></i>
                        Manage
                    </router-link>
                </div>
            </div>
        </div>

        <ul class="list-tab list-group mb-4">
            <li class="list-group-item">
                <router-link :to="{name: 'by-location'}">By Location</router-link>
            </li>
            <li class="list-group-item">
                <router-link :to="{name: 'by-property'}" class="active">By Property</router-link>
            </li>
        </ul>

    </div>
</template>