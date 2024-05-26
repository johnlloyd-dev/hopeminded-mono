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
        this.getTopStudents();
    },
    methods: {
        async getTopStudents() {
            try {
                const { data } = await axios.get('api/students/rank/top-ten');

                const labels = [];
                const scores = [];

                for (let index = 0; index < 10; index++) {
                    if (data[index]) {
                        labels.push(data[index]['full_name']);
                        scores.push(data[index]['overall_percentage']);
                    } else {
                        labels.push(this.ordinalSuffixOf(index+1) + ' (To Be Determined)');
                        scores.push(0);
                    }
                }

                this.chartData = {
                    labels: labels,
                    datasets: [
                        {
                            data: scores,
                            label: 'Grade Percentage',
                            backgroundColor: ['#198754'],
                        }
                    ]
                }

            } catch (error) {
                console.log(error)
            }
        },
        ordinalSuffixOf(i) {
            let j = i % 10,
                k = i % 100;
            if (j === 1 && k !== 11) {
                return i + "st";
            }
            if (j === 2 && k !== 12) {
                return i + "nd";
            }
            if (j === 3 && k !== 13) {
                return i + "rd";
            }
            return i + "th";
        }
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
