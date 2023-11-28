<template>
    <div>
        <div class="row" v-for="items, index in data" :key="index">
            <div class="col-12">
                <h3 class="fw-bold">{{ items.letter.toUpperCase() }}</h3>
                <table class="table table-bordered table-responsive table-striped">
                    <thead>
                        <tr class="bg-secondary">
                            <th style="width: 50%" class="text-white text-center">Object</th>
                            <th class="text-white text-center">Image</th>
                        </tr>
                    </thead>
                    <tbody v-if="data.length == 0">
                        <tr>
                            <td colspan="2" class="text-center fw-bold">No data found</td>
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <tr>
                            <td class="fw-bold text-center">{{ items.object.toUpperCase() }}</td>
                            <td class="text-center"><img :src="items.image" width="150" alt="Letter Hand Sign"></td>
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
            const alphabetLetters = await axios.get('json/alphabets-with-words.json')
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
