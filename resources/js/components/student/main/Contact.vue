<template>
    <div class="body">
        <div class="d-flex">
            <student-sidebar-component @collapse="onCollapse" />
            <div :class="collapsed ? 'collapsed-menu' : 'not-collapsed-menu'" class="main-content mx-5 w-100 h-100">
                <div class="d-flex justify-content-center">
                    <div class="card rounded-0 py-5 px-5" style="width: 100%;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="fw-bold">Contact Us </h4>
                            <img src="/images/kids.png" alt="Kids" width="150">
                        </div>
                        <h3 class="title-header text-center text-decoration-underline">Magistech</h3>
                        <div class="row">
                            <div class="col-sm-12 col-lg-3 col-md-6">
                                <div class="profile-card">
                                    <div class="profile-img">
                                        <img src="/images/contact-us/mae.png" alt="Team Image" />
                                    </div>
                                    <div class="profile-content">
                                        <h2 class="title text-white fw-bold">May Ann Ruel
                                            <span class="text-white fw-bold">Hustler</span>
                                        </h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-lg-3 col-md-6">
                                <div class="profile-card">
                                    <div class="profile-img">
                                        <img src="/images/contact-us/marvin.png" alt="Team Image" />
                                    </div>
                                    <div class="profile-content">
                                        <h2 class="title text-white fw-bold">Marvin Flores
                                            <span class="text-white fw-bold">Hipster</span>
                                        </h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-lg-3 col-md-6">
                                <div class="profile-card">
                                    <div class="profile-img">
                                        <img src="/images/contact-us/thomas.png" alt="Team Image" />
                                    </div>
                                    <div class="profile-content">
                                        <h2 class="title text-white fw-bold">Thomas Vincent Paradiang
                                            <span class="text-white fw-bold">Hacker</span>
                                        </h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-lg-3 col-md-6">
                                <div class="profile-card">
                                    <div class="profile-img">
                                        <img src="/images/contact-us/kevin.png" alt="Team Image" />
                                    </div>
                                    <div class="profile-content">
                                        <h2 class="title text-white fw-bold">Kevin Placencia
                                            <span class="text-white fw-bold">Hacker</span>
                                        </h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="contact-information alert alert-warning" role="alert">
                            <h5 class="fw-bold text-danger">Email Address: </h5>
                            <ul>
                                <li>
                                    <p class="text-dark fw-bold">hopeminded22@gmail.com</p>
                                </li>
                            </ul>
                            <h5 class="fw-bold text-danger">Contact number:</h5>
                            <ul>
                                <li>
                                    <p class="text-dark fw-bold">+63985981702</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            collapsed: false
        }
    },
    methods: {
        onCollapse(isCollapsed) {
            if (isCollapsed) {
                this.collapsed = true
            } else {
                this.collapsed = false
            }
        },
    },
}
</script>

<style scoped>
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

.title-header {
    font-family: 'Bangers', cursive;
    font-size: 50px;
}

.card {
    background-color: rgba(255, 255, 255, 0.5);
    border: none;
    height: 100vh;
    position: relative;
}

.profile-card {
    position: relative;
    overflow: hidden;
    margin-bottom: 10px;
    box-shadow: 0px 2px 3px #222;
}

.profile-card:hover .profile-img img {
    transform: scale(1.2);
}

.profile-card .profile-img img {
    width: 100%;
    height: 310px;
    transition: transform 1s;
}

.profile-card .profile-content::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 50%;
    height: 40%;
    border: 3px solid #333;
    border-right: 3px solid transparent;
    border-bottom: 3px solid transparent;
    transform: scale(1, 1);
    transition: all 0.50s linear;
}

.profile-card .profile-content::after {
    content: "";
    position: absolute;
    bottom: 0;
    right: 0;
    width: 50%;
    height: 40%;
    border: 3px solid #222;
    border-left: 3px solid transparent;
    border-top: 3px solid transparent;
    transition: all 0.50s linear;

}

.profile-card:hover .profile-content:before {
    top: 20px;
    left: 20px;
}

.profile-card:hover .profile-content:after {
    bottom: 20px;
    right: 20px;
}

.profile-card .profile-content {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    padding: 50% 4%;
    text-align: center;

}

.profile-card .profile-content:hover {
    background-color: rgb(0, 0, 0, 0.5);
}

.profile-card .profile-content .title {
    font-size: 24px;
    color: #333;
    opacity: 0;
    letter-spacing: 2px;
    text-transform: uppercase;
    transform: translateY(-100px);
    transition: all 900ms cubic-bezier(0.68, -0.55, 0.265, 1.55) 0.20s;
}

.profile-card:hover .title {
    opacity: 1;
    transform: translateY(0);
}

.profile-card:hover .title::after {
    content: "";
    display: block;
    width: 50%;
    margin: 10px auto;
    border-bottom: 2px solid #222;
}

.profile-card .profile-content .title>span {
    display: block;
    margin: 5px 0;
    font-size: 14px;
    text-transform: capitalize;
}

.profile-card .profile-content .social-link {
    margin: 20px 0;
    padding: 0;
    opacity: 0;
    transform: translateY(100px);
    transition: all 600ms cubic-bezier(0.68, -0.55, 0.265, 1.55) 0.20s;
}

.profile-card:hover .social-link {
    opacity: 1;
    transform: translateY(0);
}

.profile-card .profile-content .social-link li {
    display: inline-block;
    list-style: none;
    margin: 0 4px;
}

.profile-card .profile-content .social-link li a {
    color: #333;
    width: 30px;
    height: 25px;
    line-height: 24px;
    text-align: center;
    border-radius: 5px;
    background-color: #fff;
}
</style>