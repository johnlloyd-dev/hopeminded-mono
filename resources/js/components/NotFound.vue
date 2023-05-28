<template>
    <div style="height: 100vh" class="position-relative">
        <div>
            <button @click="navigate()" class="btn btn-secondary rounded-0 my-3 mx-3">
                Back
            </button>
        </div>
        <h1 class="position-absolute top-50 start-50 translate-middle">Page not found</h1>
    </div>
</template>

<script>
    export default {
        methods: {
            navigate() {
                this.$router.go(-1)
            }
        },
    }
</script>

<style lang="scss" scoped>

</style>