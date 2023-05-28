<template>
    <body class="position-relative">
        <div class="card rounded-0">
            <div class="card-body">
                <h1 class="card-title text-center mb-3">HOPEMINDED</h1>
                <h3 class="text-center font-weight-bold next-step">
                    Login as
                </h3>
                <div class="d-flex justify-content-around">
                    <div class="buttons">
                        <button @click="teacherLoginRedirect" class="btn-hover color-1">Teacher</button>
                        <button @click="adminLoginRedirect" class="btn-hover color-2">Admin</button>
                        <button @click="studentLoginRedirect" class="btn-hover color-3">Student</button>
                    </div>
              </div>
            </div>
        </div>
        <footer-component class="position-absolute bottom-0 start-50 translate-middle-x w-100"></footer-component>
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
</style>
