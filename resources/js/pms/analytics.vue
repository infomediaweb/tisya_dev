<template>
    <div class="page-wrap dashboard-wrap">
        <div class="page-title">
            <div class="row gy-3 align-items-center justify-content-end">
                <div class="col-12">
                    <div class="row gy-3">
                        <div class="col align-self-end">
                            <h1 class="h2 mb-0">Analytics</h1>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="search-filter">
                        <div class="row gy-3 gx-2">
                            <div class="col-auto position-relative">                               
                                <div class="row justify-content-xl-end gy-3 gx-2">
                                    <div class="col-12 col-lg col-xl-auto">
                                        <div class="row gy-3 gx-2">
                                            <div class="col">
                                                <Field
                                                    name="from-date"
                                                    v-model="rangeDate"
                                                    v-slot="{ field }"
                                                    
                                                    >
                                                    <DatePicker
                                                        ref="fromDateRef"
                                                        v-bind="field"
                                                        v-model="rangeDate"
                                                        :format="prevFormat"
                                                        range      
                                                        :enable-time-picker="false"
                                                        :input-class-name="`form-control`"
                                                        placeholder="From - To"
                                                        @cleared="onDateCleared"
                                                        auto-apply
                                                        style="min-width: 240px;"
                                                    />
                                                </Field>
                                            </div>                                            
                                        </div>
                                    </div>
                                    

                                    <div class="col-12 col-md-auto">
                                        <div class="btn-group gap-1">
                                            <button class="btn btn-primary btn-icon" @click="onFilterSearch()"><span class="bi bi-search"></span></button>

                                            <button class="btn btn-icon btn-clear btn-warning" @click="onReset()"><span class="bi bi-arrow-clockwise"></span></button>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            <!-- <div class="col-auto ms-xxl-auto">
                                <select 
                                    class="form-control" 
                                    v-model="daysWiseModel"
                                    @change="onDaysChange">
                                    <option value="" disabled selected>Select days</option>
                                    <option value="7">Last 7 days</option>
                                    <option value="30">Last 30 days</option>
                                    <option value="60">Last 60 days</option>
                                    <option value="90">Last 90 days</option>
                                </select>
                            </div> -->
                        </div>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="search-filter">
                        <div class="row gx-2">
                            <div class="col-auto">
                                <select 
                                    class="form-select" 
                                    v-model="daysWiseModel"
                                    @change="onFilterChange">
                                    <option value="" disabled selected>Select days</option>
                                    <option value="7_next">Next 7 days</option>
                                    <option value="7">Last 7 days</option>
                                    <option value="30">Last 30 days</option>
                                    <option value="60">Last 60 days</option>
                                    <option value="90">Last 90 days</option>
                                </select>
                            </div>
                            <div class="col-auto position-relative">
                                <a 
                                    href="javascript:void(0)" 
                                    class="btn btn-sm btn-outline-dark filter"
                                    @click="onCallbackFilterToggle()">
                                    <i class="bi bi-sort-down me-2"></i> Filter
                                </a>
                                
                                <Transition name="cus-fade">
                                    <div class="filterOption" v-if="isFilterShow" @click.stop>
                                        <div class="row align-items-center pb-3">
                                            <div class="col">
                                                <h6 class="mb-0"><b>Location</b></h6>
                                            </div>
                                            <div class="col-auto" v-if="route.query.location_id || route.query.ru_property_id">
                                                <a 
                                                    href="javascript:void(0)" 
                                                    class="text-primary text-decoration-none fw-bold fs-14"
                                                    @click.prevent.stop="clearFilter">
                                                    <i class="bi bi-x-lg me-1"></i>
                                                    <small>Clear All</small>
                                                </a>
                                            </div>
                                        </div>
                                        <ul>
                                            <li v-for="(obj, idx) in locationList">
                                                <a 
                                                    href="javascript:void(0)"
                                                    @click.prevent.stop="locationID = Number(obj.location_id)"
                                                    :class="{'active': locationID == Number(obj.location_id)}">
                                                    {{ obj.location_name }}
                                                </a>
                                            </li>
                                        </ul>

                                        <h6 class="mt-3 mb-2"><b>Property Name</b></h6>                                
                                        <div class="formwrap">
                                            <div class="row g-0 align-items-center">
                                                <div class="col">
                                                    <div class="property-filtter-wrap">
                                                        <div class="input-group">
                                                            <input 
                                                                type="text" 
                                                                class="form-control"
                                                                aria-label="Input group example" 
                                                                aria-describedby="basic-addon1"
                                                                @keyup="propertyFilter"
                                                                v-model="propertyModel"
                                                            />
                                                            
                                                            <!-- <button class="btn btn-dark btn-md input-group-text" id="basic-addon1">
                                                                <i class="bi bi-search"></i>
                                                            </button> -->
                                                        </div>
                                                        

                                                        <Transition name="cus-fade">
                                                            <div class="property-filter" v-if="propertyModel?.length > 2 && !isPropertyFilterShow">
                                                                <ul class="list-unstyled m-0">
                                                                    <template v-if="propertyFilterList.length">
                                                                        <li v-for="(obj, idx) in propertyFilterList">
                                                                            <a 
                                                                                href="javascript:void(0)" 
                                                                                @click.prevent.stop="ruPropertyID = Number(obj.ru_property_id), onClickPropertyItem($event, Number(obj.ru_property_id))"
                                                                                :class="{'active': Number(obj.ru_property_id) == route.query.ru_property_id}">
                                                                                {{ obj.property_name }}
                                                                            </a>
                                                                        </li>
                                                                    </template>
                                                                    
                                                                    <template v-else>
                                                                        <li>
                                                                            <a href="javascript:void(0)" class="pe-none fw-bold">No Record Found.</a>
                                                                        </li>
                                                                    </template>
                                                                </ul>
                                                            </div>
                                                        </Transition>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </Transition>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <section class="section">
            <div class="page-content">
                <template v-if="route.query.location_id || route.query.ru_property_id">
                    <div class="filter-display mb-4">
                        <div class="row g-2">
                            <div class="col-auto" v-for="(obj, idx) in filterDisplayItem">
                                <span class="badge rounded-pill text-bg-primary d-flex align-items-center" v-if="obj.name">
                                    {{ obj.name }} <button @click="clearDisplayFilter($event, idx)" type="button" class="btn-none text-white"><i class="bi bi-x-lg ms-1"></i></button>
                                </span>
                            </div>
                        </div>
                        
                    </div>
                </template>



                <div class="row g-4 chartRow">

                    <!--Line Chart Start-->
                    <div class="col-12 col-xl-4" v-for="(obj, idx) in lineChartList">
                        <a href="javascript:void(0)" :class="{'pe-none':  !obj?.url}" @click="router.push({ name: obj?.url, state:{
                           days: daysWiseModel,
                           location: locationID,
                           property_id: propertyID
                        } })" class="card card-chartBox bg-transparent h-100 text-decoration-none">
                            <div class="card-body p-0">
                                <div class="d-flex justify-content-center align-items-center h-100 py-5" v-if="isLineChartLoading">
                                    <div class="spinner-border" role="status"></div>
                                </div>
                                <template v-else>
                                    <div class="row align-items-center m-3">
                                        <div class="col-12">
                                            <h5>{{ obj?.title }}</h5>
                                        </div>
                                        <div class="col-auto">
                                            <h2 :class="idx % 2 === 0 ? 'text-primary' : 'text-secondary'">
                                                <template v-if="obj?.type.toLowerCase() == 'price'">
                                                    {{ currFormat(obj?.value, 'currency') }}
                                                </template>

                                                <template v-else-if="obj?.type.toLowerCase() == 'number'">
                                                    {{ currFormat(obj?.value) }}
                                                </template>

                                                <template v-else>
                                                    {{ `${currFormat(obj?.value)}%` }}
                                                </template>
                                            </h2>
                                        </div>
                                        <div class="col-auto">
                                            <span 
                                                class="rounded-pill py-1 px-2"
                                                :class="obj?.percentage < 0 ? 'text-danger bg-danger-light' : 'text-success bg-success-light'" v-show="daysWiseModel">
                                                <template v-if="obj?.percentage != 0">
                                                    <i :class="['bi', obj?.percentage < 0 ? 'bi-arrow-down' : 'bi-arrow-up']"></i> 
                                                </template>
                                                {{ Math.abs(obj?.percentage) }}%
                                            </span>
                                        </div>
                                    </div>
                                    <div class="chartBox" v-if="obj?.chartData">
                                        <Line 
                                            v-if="obj?.data.length"
                                            :data="obj?.chartData" 
                                            :options="lineChartOptions" 
                                        />
                                    </div>
                                </template>
                            </div>
                        </a>
                    </div>
                    <!--Line Chart End-->



                    <!--Average Start-->
                    <div class="col-12 col-xl-4 d-flex">
                        <div class="card card-chartBox bg-transparent w-100">

                            <div class="d-flex justify-content-center py-5" v-if="isAverageLoading">
                                <div class="spinner-border" role="status"></div>
                            </div>


                            <template v-else>
                                <div 
                                    v-for="(obj, idx) in averageList" 
                                    class="card-body p-0 d-flex flex-column justify-content-center" 
                                    :class="{'border-top': idx == 1}">
                                    <div class="row align-items-center m-3">
                                        <div class="col-12">
                                            <h5>{{ obj?.title }}</h5>
                                        </div>
                                        <div class="col-auto">
                                            <h2 :class="idx == 0 ? 'text-secondary' : 'text-primary'">{{ obj?.value }} {{ obj?.name }}</h2>
                                        </div>                                        
                                    </div>
                                </div>
                            </template>

                        </div>
                    </div>
                    <!--Average End-->



                    <!--Weekly Chart Start-->
                    <div class="col-12 col-xl-8 d-flex">
                        <div class="card card-chartBox w-100 bg-transparent">
                            <div class="card-body p-4">
                                <div class="row align-items-center mb-4">
                                    <div class="col">
                                        <h5>Weekly Revenue Analysis</h5>
                                    </div>
                                    <div class="col-auto">
                                        <ul class="tabs">
                                            <li v-for="(obj) in weeklyChartTabs">
                                                <a 
                                                    href="javascript:void(0);" 
                                                    :class="{'active': activeWeeklychart.toLowerCase() == obj.toLowerCase()}"
                                                    @click="activeWeeklychart = obj, fetchWeeklyReport()">
                                                    {{ obj }}
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="tabContent">
                                    <div class="d-flex justify-content-center py-5" v-if="isWeeklyChartLoading">
                                        <div class="spinner-border" role="status"></div>
                                    </div>

                                    <template v-else>                                       
                                        <Line :data="weeklyLineChart" v-if="weeklyChartData?.length" />
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div> 
                    <!--Weekly Chart End-->


                    <!--Channel Chart Start-->
                    <div class="col-12 col-xl-4 d-flex">
                        <div class="card card-chartBox bg-transparent w-100">

                            <div class="d-flex justify-content-center align-items-center py-5 h-100" v-if="isChannelChartLoading">
                                <div class="spinner-border" role="status"></div>
                            </div>
                            

                            <template v-else>
                                <div class="card-body p-4">
                                    <div class="row gy-3 gy-md-0 align-items-center">
                                        <div class="col-12">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <h5>Channel Distribution / Revenue</h5>
                                                </div>
                                                <div class="col-auto">
                                                    <ul class="tabs">
                                                        <li v-for="(obj) in weeklyChartTabs">
                                                            <a 
                                                                href="javascript:void(0);"
                                                                :class="{'active': activeChannelchart.toLowerCase() == obj.toLowerCase()}"
                                                                @click="activeChannelchart = obj, fetchChannelRevenue()">
                                                                {{ obj }}
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <h2 :class="activeChannelchart.toLowerCase() == 'net' ? 'text-primary' : 'text-secondary'">{{ currFormat(channelChartList?.total_channel_revenue, 'currency') }}</h2>
                                        </div>
                                    </div>
                                    
                                    <div class="chartBoxPie mt-4" v-if="channelChartList?.channel_distribution_revenue?.length">
                                        <Doughnut :data="ChannelDoughnutChart" :options="ChannelDoughnutChartOption"></Doughnut>
                                    </div>
                                    
                                    <div class="channelList mt-4">

                                        <div 
                                            v-for="(obj, idx) in channelChartList?.channel_distribution_revenue"
                                            class="row gx-3 align-items-center my-2">
                                            <div class="col-auto">
                                                <span class="colorDot" :style="{backgroundColor: colorChannel(obj?.channel.toLowerCase())}"></span>
                                            </div>
                                            <div class="col">
                                                <h5 :style="{color: `${colorChannel(obj?.channel.toLowerCase())}!important`}">{{ obj?.channel }}</h5>
                                            </div>
                                            <div class="col-auto">
                                                {{ obj?.total_count }} / <span :style="{color: colorChannel(obj?.channel.toLowerCase())}">{{ currFormat(obj?.total_amount, 'currency') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>


                        </div>
                    </div>
                    <!--Channel Chart End-->


                </div>
            </div>
        </section>
    </div>
</template>
<script setup>
    import axios from 'axios'
    import {
        Chart as ChartJS,
        CategoryScale,
        LinearScale,
        PointElement,
        LineElement,
        ArcElement,
        Title,
        Tooltip,
        Legend,
        Filler
    } from 'chart.js'
    import { Line, Doughnut } from 'vue-chartjs'
    import { ref, onMounted, nextTick, computed, watch } from 'vue'
    import { useRoute, useRouter } from 'vue-router'
    import { Field } from 'vee-validate'
    import { standardFormat, currFormat, shortFormat } from '@utils/common'
//  import { format, standardFormat, currFormat, shortFormat } from '@utils/common'


    ChartJS.register(
        CategoryScale,
        LinearScale,
        PointElement,
        LineElement,
        ArcElement,
        Title,
        Tooltip,
        Legend,
        Filler
    )


    const router = useRouter()
    const route = useRoute()



    //Ref
    const locationList = ref([])
    const propertyList = ref([])
    const propertyFilterList = ref([])
    const channelChartList = ref([])
    const averageList = ref([])
    const weeklyChartData = ref([])

    const isFilterShow = ref(false)
    const activeWeeklychart = ref('Net')
    const activeChannelchart = ref('Net')
    
    const isWeeklyChartLoading = ref(false)
    const isLineChartLoading = ref(false)
    const isChannelChartLoading = ref(false)
    const isAverageLoading = ref(false)
    const isPropertyFilterShow = ref(false)

    const lineChartList = ref(Array(5))


    //V-Model 
    const propertyID = ref('')
    const propertyModel = ref()
    const daysWiseModel = ref('30')
    const locationID = ref()
    const ruPropertyID = ref()


    const weeklyChartTabs = ref({
        Net: 'Net',
        Gross: 'Gross'
    })


    const fromDate = ref()
    const rangeDate = ref()
    const toDate = ref()
    const fromDateRef = ref()


    watch(rangeDate, (newVal, oldVal) => {
        if(newVal){
            fromDate.value = newVal[0]
            toDate.value = newVal[1]
        }
    })

    const prevFormat = (date) => {
        return `${shortFormat(date[0])} - ${shortFormat(date[1])}`
    }


    const onDateCleared = ()=>{       
        rangeDate.value = ''
        fromDate.value = ''           
        toDate.value = ''
    }



    const onFilterSearch = ()=>{      
        if( fromDate.value){
            daysWiseModel.value = ""
        }    
        
        if(!locationID.value && !ruPropertyID.value){
            fetchAllChartAPIs()
        }
       
    }

    const onReset = ()=>{       
        daysWiseModel.value = "30"
        rangeDate.value = ''       
        fromDate.value = ''           
        toDate.value = ''
        if(!locationID.value && !ruPropertyID.value){
            fetchAllChartAPIs()
        }
    }





    //For color channel 
    const colorChannel = (value) => {
        let colorCode = ''

        if(value == 'airbnb') colorCode = '#3A603A'
        else if(value == 'ru') colorCode = '#343741'
        else if(value == 'makemytrip') colorCode = '#D9A750'
        else if(value == 'website') colorCode = '#976425'
        else if(value == 'quotation') colorCode = '#888888'
        else colorCode = '#231F20'

        return colorCode
    }



    // For line chart 
    const lineChart = (labels, data, backgroundColor, borderColor) => {
        return {
            labels: labels,
            datasets: [{
                // label: 'Ticket Sales',
                data: data,
                backgroundColor: backgroundColor,
                borderColor: borderColor,
                hoverOffset: 5,
                tension: 0.4,
                fill: true,
            }]
        }
    }



    // For line chart options
    const lineChartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                enabled: false
            }
        },
        scales: {
            x: {
                display: false,
                grid: {
                    display: false
                }
            },
            y: {
                display: false,
                grid: {
                    display: false
                }
            }
        },
        layout: {
            padding: 0
        },
        elements: {
            point: {
                radius: 0
            },
            line: {
                tension: 0.4
            }
        }
    }



    // For weekly line chart
    const weeklyLineChart = ref({        
            labels: [],
            datasets: [{
                label: '',
                data: [],
                backgroundColor: '',
                borderColor: '',
                hoverOffset: 5,
                tension: 0.4,
                fill: true,
            }]        
    })



    // For channel chart
    const ChannelDoughnutChart = ref({
            labels: [],
            datasets: [{
                label: 'Gross Revenue',
                data: [],
                backgroundColor: [],
                borderColor: [],
                hoverOffset: 2,
            }]        
    })



    // For channel chart option
    const ChannelDoughnutChartOption = {
        responsive: true,
        maintainAspectRatio: false,
        onClick:function(event, elements){
            if (elements.length > 0) {         
                const chart = elements[0].element.$context.chart;           
                const index = elements[0].index;
                const label = chart.data.labels[index];
                router.push({ name: 'net-revenue', state:{
                    days: daysWiseModel.value, 
                    location: locationID.value || '',
                    type: activeChannelchart.value.toLowerCase(),
                    channel: label,
                    from_date: fromDate.value && standardFormat(fromDate.value) || null,           
                    to_date: toDate.value && standardFormat(toDate.value) || null 
                } })                     
            }
        },
        onHover: (event, chartElement) => {
        const canvas = event.native.target;

        // Change cursor style if hovering over a segment
        canvas.style.cursor = chartElement.length ? 'pointer' : 'default';
      },
        plugins: {
            legend: {
                display: false
            }
        }
    }



    // For fetch loction
    const fetchLocation = () => {
        axios.get('api/dashboard/location').then(res => {
            if(res.data.status){
                locationList.value = res.data.location
            }
        })
        .catch(error => {

        })
    }



    // For fetch property
    const fetchProperty = () => {
        axios.get('api/dashboard/property').then(res => {
            if(res.data.status){
                propertyList.value = res.data.property
                propertyFilterList.value = propertyList.value
            }
        })
        .catch(error => {

        })
    }



    // For fetch all chart data
    const fetchLineChartData = () => {
        isLineChartLoading.value = true
        
        axios.post('api/linechart', {
            days: daysWiseModel.value,
            location_id: locationID.value,
            ru_property_id: ruPropertyID.value,
            from_date: fromDate.value && standardFormat(fromDate.value) || null,           
            to_date: toDate.value && standardFormat(toDate.value) || null 
        }).then(res => {
            if(res.data.status){
                lineChartList.value = res.data.data.map((item, idx) => {
                    if(item.data){
                        let labels = item.data.map(dataPoint => dataPoint.label)
                        let data = item.data.map(dataPoint => parseInt(dataPoint.price))

                        let backgroundColor, borderColor

                        if(idx % 2 == 0){
                            backgroundColor = 'rgb(227, 238, 227)'
                            borderColor = 'rgb(71, 100, 72)'
                        }
                        else{
                            backgroundColor = 'rgb(243,236,224)'
                            borderColor = 'rgb(217,167,80)'
                        }

                        return {
                            ...item,
                            chartData: lineChart(labels, data, backgroundColor, borderColor)
                        }
                    }
                    else{
                        return {
                            ...item
                        }
                    }
                })
                console.log("lineChartList.value", lineChartList.value)
            }
        })
        .catch(error => {

        })
        .finally(() => {
            setTimeout(() => {
                isLineChartLoading.value = false
            }, 400)
        })
    }



    // For Weekly report chart data
    const fetchWeeklyReport = () => {
        isWeeklyChartLoading.value = true

        axios.post('api/weeklyreport', {
            type: activeWeeklychart.value.toLowerCase(),
            days: daysWiseModel.value,
            location_id: locationID.value,
            ru_property_id: ruPropertyID.value,
            from_date: fromDate.value && standardFormat(fromDate.value) || null,           
            to_date: toDate.value && standardFormat(toDate.value) || null 
        }).then(res => {
            console.log("res.data", res.data)
            if(res.data.status){
                weeklyChartData.value = res.data.data
                
                if(res.data.data.length){
                    const labels = res.data.data.map((item, idx) => `Week ${idx + 1}`)
                    const data = res.data.data.map(item => parseInt(item.total_revenue))
                    const chartDatasets = weeklyLineChart.value.datasets[0]

                    

                    if(activeWeeklychart.value.toLowerCase() == 'net'){
                        chartDatasets.backgroundColor = 'rgb(227,238,227)'
                        chartDatasets.borderColor = 'rgb(71,100,72)'
                    }
                    else if(activeWeeklychart.value.toLowerCase() == 'gross'){
                        chartDatasets.backgroundColor = 'rgb(243,236,224)'
                        chartDatasets.borderColor = 'rgb(217,167,80)'
                    }

                    chartDatasets.label = `${activeWeeklychart.value} Revenue`
                    chartDatasets.data = data
                    weeklyLineChart.value.labels = labels   

                    //console.log("weeklyLineChart.value",weeklyLineChart.value)
                }
            }
        })
        .catch(error => {
            
        })
        .finally(() => {
            setTimeout(() => {
               isWeeklyChartLoading.value = false
            }, 400)
        })
    }



    // For channel wise revenue chart data
    const fetchChannelRevenue = () => {
        isChannelChartLoading.value = true

        axios.post('api/channelrevenue', {
            type: activeChannelchart.value.toLowerCase(),
            days: daysWiseModel.value,
            location_id: locationID.value,
            ru_property_id: ruPropertyID.value,
            from_date: fromDate.value && standardFormat(fromDate.value) || null,           
            to_date: toDate.value && standardFormat(toDate.value) || null 
        }).then(res => {
            if(res.data.status){
                channelChartList.value = res.data.data

                if(channelChartList.value?.channel_distribution_revenue.length){
                    const list = channelChartList.value?.channel_distribution_revenue

                    const chartDatasets = ChannelDoughnutChart.value.datasets[0]

                    const color = list.map(item => colorChannel(item.channel.toLowerCase()))
                    const labels = list.map(item => item.channel)
                    const data = list.map(item => parseInt(item.total_amount))
                    
                    chartDatasets.data = data
                    chartDatasets.backgroundColor = color
                    chartDatasets.borderColor = color
                    ChannelDoughnutChart.value.labels = labels 
                }
            }
        })
        .catch(error => {

        })
        .finally(() => {
            setTimeout(() => {
                isChannelChartLoading.value = false
            }, 400)
        })
    }



    //for fetch dashboard average
    const fetchAverage = () => {
        isAverageLoading.value = true

        axios.post('api/dashboard', {
            days: daysWiseModel.value,
            location_id: locationID.value,
            ru_property_id: ruPropertyID.value,
            from_date: fromDate.value && standardFormat(fromDate.value) || null,           
            to_date: toDate.value && standardFormat(toDate.value) || null 
        }).then(res => {
            if(res.data.status){
                averageList.value = res.data.data
            }
        })
        .catch(error => {

        })
        .finally(() => {
            setTimeout(() => {
                isAverageLoading.value = false
            })
        })
    }


    // For property filter
    const propertyFilter = (e) => {
        isPropertyFilterShow.value = false
        
        let value = e.target.value.toLowerCase()
        propertyFilterList.value = propertyList.value.filter(item => item.property_name.toLowerCase().includes(value))
    }



    //For toggle filter on click
    const onCallbackFilterToggle = () => {
        isFilterShow.value = !isFilterShow.value

        if(!isFilterShow.value){
            propertyModel.value = ''
        }
        
        if(route.query.ru_property_id){
            propertyModel.value = getActivePropertyName(propertyList, route.query.ru_property_id)
            isPropertyFilterShow.value = true
        } 
    }

    


    //For fetch all apis function
    const fetchAllChartAPIs = () => {
        fetchLineChartData()
        fetchAverage()
        fetchWeeklyReport()
        fetchChannelRevenue()
    }



    // For filter value change then fetch all apis
    const onFilterChange = () => {
        rangeDate.value = ''       
        fromDate.value = ''           
        toDate.value = ''


        if(!locationID.value && !ruPropertyID.value){
            router.replace({ 
                query: {} 
            })
        }
        else{
            router.replace({ 
                query: {
                    location_id: locationID.value,
                    ru_property_id: ruPropertyID.value
                } 
            })
        }

        fetchAllChartAPIs()
        
    }


    // For clear filter values
    const clearFilter = () => {
        locationID.value = ''
        ruPropertyID.value = ''
        propertyModel.value = ''
    }



    //For clear display filter
    const clearDisplayFilter = async (e, idx) => {
        let currentQuery = { ...route.query }
        let queryKeyRemove = Object.keys(currentQuery)[idx]

        delete currentQuery[queryKeyRemove]

        await router.replace({ query: currentQuery })

        nextTick()

        locationID.value = route.query.location_id
        ruPropertyID.value = route.query.ru_property_id

        propertyID.value = ''
    }



    //For display filter 
    const filterDisplayItem = computed(() => {
        const filterArray = []
        const queryList = Object.values(route.query)
        
        if(queryList.length){
            queryList.forEach((item, idx) => {
                if(item == route.query.location_id){
                    filterArray.push({
                        id: route.query.location_id,
                        name: locationList.value.find(item => Number(item.location_id) == Number(route.query.location_id))?.location_name
                    })
                }
                else if(item == route.query.ru_property_id){
                    filterArray.push({
                        id: route.query.ru_property_id,
                        name: getActivePropertyName(propertyList, route.query.ru_property_id)
                    })

                    propertyID.value = propertyList.value.find(item => Number(item.ru_property_id) == Number(route.query.ru_property_id))?.id 
                } 
            })
        }
        

        return filterArray
    })



    //For get active property name function
    const getActivePropertyName = (list, id) => {
        return list.value.find(item => Number(item.ru_property_id) == id)?.property_name
    }



    //For on click property item and set value property model
    const onClickPropertyItem = (e, id) => {
        propertyModel.value = getActivePropertyName(propertyFilterList, id)
        isPropertyFilterShow.value = true
    }



    //For check is there value in any of both ref and run function
    watch([locationID, ruPropertyID], ([newLocationID, newPropertyID]) => {
        if(newLocationID || newPropertyID && !newLocationID || !newPropertyID){
            onFilterChange()
        } 
    })



    onMounted(() => {
        fetchLocation()
        fetchProperty()

        locationID.value = route.query.location_id
        ruPropertyID.value = route.query.ru_property_id

        if(!locationID.value && !ruPropertyID.value){
            fetchAllChartAPIs()
        }
        
       
       

        document.addEventListener('click', (e) => {
            if(!e.target.closest('a.filter')){
                isFilterShow.value = false
                propertyModel.value = ''
            }
        })
    })


</script>
