<template>
    <div class="body">
        <div class="d-flex">
            <student-sidebar-component @collapse="onCollapse" />
            <div :class="collapsed ? 'collapsed-menu' : 'not-collapsed-menu'" class="main-content mx-5 w-100 h-100">
                <div class="textbook-content">
                    <div class="custom-card rounded-0 py-5 px-5" style="width: 100%">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Top 10 Students</h4>
                            <img src="/images/kids.png" alt="Kids" width="150">
                        </div>
                        <h1 v-if="!isLoading" class="text-center mb-5 fw-bold">Rank: <u>{{ currentRank }}</u></h1>
                        <HorizontalBarChart v-if="Object.keys(chartData).length" :chartData="chartData" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import HorizontalBarChart from '../../teacher/layouts/HorizontalBarChart.vue'
export default {
    components: {
        HorizontalBarChart
    },
    data() {
        return {
            collapsed: false,
            isLoading: false,
            chartData: {},
            topStudents: []
        }
    },
    mounted() {
        this.getTopStudents();
    },
    computed: {
        currentRank() {
            const ownerId = localStorage.getItem('ownerId');
            const ownerRank = this.topStudents.find(data => data.id == parseInt(ownerId));

            if (ownerRank) {
                const index = this.topStudents.findIndex(data => data.id == parseInt(ownerId));

                if (index !== -1) {
                    return this.ordinalSuffixOf(index+1)
                }
            }

            return 'Ranked Above 10th'
        }
    },
    methods: {
        onCollapse(isCollapsed) {
            if (isCollapsed) {
                this.collapsed = true;
            } else {
                this.collapsed = false;
            }
        },
        async getTopStudents() {
            try {
                this.isLoading = true

                const { data } = await axios.get('api/students/rank/top-ten?flag=student');

                this.topStudents = data

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
            } finally {
                this.isLoading = false
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
};
</script>

<style scoped>
.body {
    background-image: url("/images/background.jpg");
    background-size: cover;
    background-repeat: no-repeat;
    background-attachment: fixed;
    color: #333;
    padding: 0;
    margin: 0;
    height: 100vh;
    overflow-x: hidden;
}

.custom-card {
    background-color: rgba(255, 255, 255, 0.5);
    border: none;
}

.card-title {
    font-weight: bold;
}
</style>
