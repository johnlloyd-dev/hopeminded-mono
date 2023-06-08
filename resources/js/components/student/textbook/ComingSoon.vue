<template>
    <div class="body position-relative">
        <div class="position-absolute top-0 end-0">
            <img width="100" src="/images/main-logo.png" class="logo" alt="Hopeminded Logo">
        </div>
        <button type="button" @click="navigate" class="btn btn-secondary mt-5 mx-5 rounded-0">
            Back
        </button>
        <h1 class="start-50 top-50 translate-middle position-absolute fw-bold">Coming Soon...</h1>
    </div>
</template>

<script>
export default {
    methods: {
        navigate() {
            this.$router.push('/student-textbook')
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
    position: relative;
}
</style>