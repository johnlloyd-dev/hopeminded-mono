<template>
    <div>
        <div class="d-flex justify-content-center">
            <table class="table table-bordered table-responsive table-striped">
                <thead>
                    <tr class="bg-secondary">
                        <th class="text-white text-center">Alphabets</th>
                        <th class="text-white text-center">Hand Signs</th>
                    </tr>
                </thead>
                <tbody v-if="data.length == 0">
                    <tr>
                        <td colspan="4" class="text-center fw-bold">No data found</td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr v-for="item in data" :key="item.id">
                        <td class="fw-bold text-center">{{ item.letter.toUpperCase() }}</td>
                        <td class="text-center"><img :src="item.image" width="70" alt="Letter Hand Sign"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            data: [],
        }
    },
    created() {
        this.getHandSigns()
    },
    computed: {

    },
    methods: {
        async getHandSigns() {
            const alphabetLetters = await axios.get('json/balloon-game.json')
            this.data = alphabetLetters.data
        },
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
    overflow-x: hidden;
}
</style>
