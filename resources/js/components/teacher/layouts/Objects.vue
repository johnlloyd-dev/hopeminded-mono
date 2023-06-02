<template>
    <div>
        <div class="row" v-for="items, index in data" :key="index">
            <div class="col-12">
                <h3 class="fw-bold">{{ Object.keys(items)[0].toUpperCase() }}</h3>
                <table class="table table-bordered table-responsive table-striped">
                    <thead>
                        <tr class="bg-secondary">
                            <th class="text-white text-center">Object</th>
                            <th class="text-white text-center">Image</th>
                        </tr>
                    </thead>
                    <tbody v-if="data.length == 0">
                        <tr>
                            <td colspan="4" class="text-center fw-bold">No data found</td>
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <tr v-for="item, index in Object.values(items)[0]" :key="index">
                            <td class="fw-bold text-center">{{ item.object.toUpperCase() }}</td>
                            <td class="text-center"><img :src="item.image" width="150" alt="Letter Hand Sign"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <hr>
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
            const alphabetLetters = await axios.get('/storage/json/alphabets-with-words.json')
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