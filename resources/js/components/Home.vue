<template>
    <body class="position-relative">
        <div class="card rounded-0 position-relative">
            <div class="card-body">
                <div class="text-center">
                    <img width="150" src="/images/main-logo.png" class="logo my-3 " alt="Hopeminded Logo">
                    <h1 class="card-title text-center mb-3">HOPEMINDED</h1>
                </div>
                <h3 class="text-center font-weight-bold next-step">
                    Login as
                </h3>
                <div class="d-flex justify-content-around mb-5">
                    <div class="buttons">
                        <button @click="teacherLoginRedirect" class="btn-hover color-1">Teacher</button>
                        <button @click="adminLoginRedirect" class="btn-hover color-2">Admin</button>
                        <button @click="studentLoginRedirect" class="btn-hover color-3">Student</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-100 mt-5 text-center custom-background">
            <div class="row p-5">
                <div class="col-lg-12 d-flex align-items-center justify-content-center">
                    <h1 class="mt-3 fw-bolder text-white">How to use Hopeminded?</h1>
                </div>
                <div class="col-lg-12">
                    <div class="p-3 mb-3">
                        <iframe width="860" height="615" src="https://www.youtube.com/embed/nMLB0MFHJXk"
                            title="YouTube video player" frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
        <footer-component class="w-100"></footer-component>
    </body>
</template>

<script>
export default {
    methods: {
        studentLoginRedirect() {
            this.$router.push({ path: '/student-login' });
        },
        teacherLoginRedirect() {
            this.$router.push({ path: '/teacher-login' });
        },
        adminLoginRedirect() {
            this.$router.push({ path: '/admin-login' });
        }
    },
};
</script>

<style lang="scss" scoped>
@import url('https://fonts.googleapis.com/css?family=Poppins:900i');

body {
    background-image: url("/images/animated-children.jpg");
    background-size: cover;
    background-repeat: no-repeat;
    background-attachment: fixed;
    font-family: "Arial", sans-serif;
    color: #333;
    padding: 0;
    margin: 0;
    height: 100vh;
    display: flex;
    flex-direction: column;
    overflow-x: hidden;
    align-items: center;
}

.card-title {
    font-family: 'Bangers', cursive;
    font-size: 80px;
    font-weight: bolder;
}

.card {
    background-color: rgba(255, 255, 255, 0.5);
    border: none;
}

.next-step {
    margin-bottom: 3rem;
    font-weight: bolder;
}

//Stylish Button
.buttons {
    text-align: center;
}

.logo {
    box-shadow: rgba(135, 206, 250, 0.4) 5px 5px, rgba(135, 206, 250, 0.3) 10px 10px, rgba(135, 206, 250, 0.2) 15px 15px, rgba(135, 206, 250, 0.1) 20px 20px, rgba(135, 206, 250, 0.05) 25px 25px;
}

.btn-hover {
    width: 200px;
    font-size: 16px;
    font-weight: 600;
    color: #fff;
    cursor: pointer;
    margin: 20px;
    height: 55px;
    text-align: center;
    border: none;
    background-size: 300% 100%;

    border-radius: 50px;
    moz-transition: all .4s ease-in-out;
    -o-transition: all .4s ease-in-out;
    -webkit-transition: all .4s ease-in-out;
    transition: all .4s ease-in-out;
}

.btn-hover:hover {
    background-position: 100% 0;
    moz-transition: all .4s ease-in-out;
    -o-transition: all .4s ease-in-out;
    -webkit-transition: all .4s ease-in-out;
    transition: all .4s ease-in-out;
}

.btn-hover:focus {
    outline: none;
}

.btn-hover.color-1 {
    background-image: linear-gradient(to right, #25aae1, #40e495, #30dd8a, #2bb673);
    box-shadow: 0 4px 15px 0 rgba(49, 196, 190, 0.75);
}

.btn-hover.color-2 {
    background-image: linear-gradient(to right, #f5ce62, #e43603, #fa7199, #e85a19);
    box-shadow: 0 4px 15px 0 rgba(229, 66, 10, 0.75);
}

.btn-hover.color-3 {
    background-image: linear-gradient(to right, #667eea, #764ba2, #6B8DD6, #8E37D7);
    box-shadow: 0 4px 15px 0 rgba(116, 79, 168, 0.75);
}

.container {
    width: 100%;
    height: 600px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background: #FFF;
}

.custom-background {
    position: relative;
    background-image: url('/images/night-background.jpg');
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
}
</style>
