<template>
    <div class="d-flex align-items-center flex-column">
        <div class="card w-75 mb-3">
            <div class="card-body">
                <div class="quiz-graph mb-3">
                    <h5 class="fw-bold">Top 10 Students</h5>
                    <HorizontalBarChart v-if="Object.keys(chartData).length" :chartData="chartData" />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import HorizontalBarChart from '../layouts/HorizontalBarChart.vue'

export default {
    name: 'StudentPerformanceReport',
    components: {
        HorizontalBarChart
    },
    data() {
        return {
            chartData: {}
        }
    },
    computed: {

    },
    mounted() {
        this.chartData = {
                labels: ['Student 1', 'Student 2', 'Student 3', 'Student 4', 'Student 5', 'Student 6', 'Student 7', 'Student 8', 'Student 9', 'Student 10'],
                datasets: [
                    {
                        data: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
                        label: 'Quiz',
                        backgroundColor: ['#198754', '#6c757d'],
                    }
                ]
            }
    },
    methods: {

    },
}
</script>

<style lang="scss" scoped>
.all-records-list:hover {
    color: blue;
    cursor: pointer;
}

.table-header-position {
    position: fixed;
    top: 0;
    width: 73vw;
    background-color: skyblue;
    z-index: 999;
}
</style>
