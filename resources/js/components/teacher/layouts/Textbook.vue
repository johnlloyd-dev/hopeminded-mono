<template>
    <div class="body">
        <div class="d-flex">
            <div class="main-content mx-5 w-100 h-100">
                <div class="my-3">
                    <button @click="navigate()" class="btn-secondary btn rounded-0">
                        Back
                    </button>
                    <alphabet-letters-component v-if="textbookFlag == 'alphabet-letters'"></alphabet-letters-component>
                    <alphabet-words-component v-if="textbookFlag == 'alphabet-words'"></alphabet-words-component>
                    <vowel-consonants-component v-if="textbookFlag == 'vowel-consonants'"></vowel-consonants-component>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    created() {
        
    },
    computed: {
        textbookFlag() {
            return this.$route.params.textbookFlag
        }
    },
    methods: {
        navigate() {
            this.$router.push({ path: `/textbook-management` })
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
}
</style>