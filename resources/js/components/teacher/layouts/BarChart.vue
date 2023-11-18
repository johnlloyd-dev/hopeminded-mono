<template>
    <Bar id="my-chart-id" :options="chartOptions" :data="chartData" :style="myStyles" />
</template>

<script>
import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

export default {
    name: 'BarChart',
    components: { Bar },
    props: {
        chartData: Object
    },
    data() {
        return {
            chartOptions: {
                responsive: true
            }
        }
    },
    computed: {
        myStyles() {
            return {
                height: `${300}px`,
                position: 'relative'
            }
        }
    }
}
</script>
