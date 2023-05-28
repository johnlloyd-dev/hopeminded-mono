<template>
    <div>
        <!-- <h3 class="fw-bold mt-5 mb-2">{{ gameName }}</h3> -->
        <!-- <div class="d-flex justify-content-center">
                    <table class="table table-bordered table-responsive">
                        <thead>
                            <tr>
                                <th scope="col">Date/Time</th>
                                <th>Score</th>
                                <th>Mark</th>
                            </tr>
                        </thead>
                        <tbody v-if="reports.length == 0">
                            <tr>
                                <td colspan="4" class="text-center fw-bold">No data found</td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr v-for="item in reports" :key="item.id">
                                <td>{{ new Date(item.created_at).toLocaleString() }}</td>
                                <td>{{ item.total_score }}</td>
                                <td>{{ item.mark }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="reports.length !== 0" class="d-flex justify-content-around">
                    <button class="btn btn-secondary rounded-0">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="btn btn-secondary rounded-0">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div> -->
    </div>
</template>

<script>
export default {
    data() {
        return {
            alphabetWords: []
        }
    },
    created() {
        this.getTextbooks()
    },
    computed: {

    },
    methods: {
        async getTextbooks() {
            this.alphabetWords = await axios.get('/storage/json/alphabets-with-words.json')
        },
        navigate() {
            this.$router.push('/textbook-management')
        }
    },
}
</script>

<style lang="scss" scoped>
.body {
    background-image: url("/images/background.jpg");
    background-size: cover;
    background-repeat: no-repeat;
    background-attachment: fixed;
    color: #333;
    padding: 0;
    margin: 0;
    height: 100vh;
}
</style>