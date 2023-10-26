<template>
  <Bar
    id="my-chart-id"
    :options="chartOptions"
    :data="chartData"
    :style="myStyles"
  />
</template>

<script>
import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

export default {
  name: 'BarChart',
  components: { Bar },
  data() {
    return {
      chartData: {
        labels: [ 'Wrong Answers', 'Correct Answers', 'Pending/Unmarked' ],
            datasets: [
                {
                    data: [40, 20, 12],
                    label: 'Skill Test',
                    backgroundColor: '#f87979',
                }
            ]
      },
      chartOptions: {
        responsive: true
      }
    }
  },
  computed: {
    myStyles () {
      return {
        height: `${300}px`,
        position: 'relative'
      }
    }
  }
}
</script>
