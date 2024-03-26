<template>
    <div class="body position-relative">
        <div class="d-flex">
            <teacher-sidebar-component @collapse="onCollapse" />
            <div :class="collapsed ? 'collapsed-menu' : 'not-collapsed-menu'" class="main-content mx-5 w-100 h-100">
                <div class="d-flex justify-content-center">
                    <div class="card rounded-0 py-5 px-5" style="width: 100%;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Textbook Management </h4>
                        </div>
                        <div class="main-content mt-3">
                            <div class="row">
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div style="height: 300px" class="card textbook-al">
                                        <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                            <button @click="navigate('alphabet-letters')" class="btn btn-lg btn-primary w-75 position-relative rounded-0 btn-shadow fw-bold">
                                                Alphabets/Letters
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div style="height: 300px" class="card textbook-vc">
                                        <div class="card-body flex-column d-flex align-items-center justify-content-center">
                                            <button @click="navigate('vowel-consonants')" class="btn btn-lg btn-success w-75 position-relative rounded-0 btn-shadow fw-bold">
                                                Vowel/Consonants
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div style="height: 300px" class="card textbook-aw">
                                        <div class="card-body flex-column d-flex align-items-center justify-content-center">
                                            <button @click="navigate('alphabet-words')" class="btn btn-lg btn-danger w-75 position-relative rounded-0 btn-shadow fw-bold">
                                                Alphabets/Words
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div style="height: 300px" class="card textbook-n">
                                        <div class="card-body flex-column d-flex align-items-center justify-content-center">
                                            <button @click="navigate('numbers')" class="btn btn-lg btn-warning w-75 position-relative rounded-0 btn-shadow fw-bold">
                                                Numbers
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button type="button" @click="seed" class="btn-primary btn-sm btn rounded-0 position-absolute bottom-0 end-0">
            Seed
        </button>
    </div>
</template>

<script>
import swal from 'sweetalert2'
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
        navigate(flag) {
            this.$router.push({path: `/textbook-management/${flag}`})
        },
        async seed() {
            try {
                const response = await axios.post('/api/seed')
                if (response.status === 200) {
                    swal.fire('Success', response.data.message, 'success')
                }
            } catch (error) {
                console.log(error)
            }
        }
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
}

.card {
    background-color: rgba(255, 255, 255, 0.5);
    border: none;
}

.card-title {
    font-weight: bold;
}

.card-sl {
    border-radius: 8px;
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    width: 250px;
    height: 270px;
}

.card-image {
    display: flex;
    justify-content: center;
}

.card-image img {
    width: 230px;
    height: 230px;
    border-radius: 8px 8px 0px 0;
}

.card-action {
    position: relative;
    float: right;
    margin-top: -25px;
    margin-right: 20px;
    z-index: 2;
    color: #E26D5C;
    background: #fff;
    border-radius: 50%;
    padding: 15px;
    font-size: 15px;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.2), 0 1px 2px 0 rgba(0, 0, 0, 0.19);
}

.card-action:hover {
    color: #fff;
    background: #E26D5C;
    animation: pulse 1.5s infinite;
    -webkit-animation: pulse 1.5s infinite;
}

.card-heading {
    font-size: 18px;
    font-weight: bold;
    background: #fff;
    padding: 10px 15px;
    border-radius: 8px;
}

.card-text {
    padding: 10px 15px;
    background: #fff;
    font-size: 14px;
    color: #636262;
}

.card-button {
    display: flex;
    justify-content: center;
    padding: 10px 0;
    width: 100%;
    background-color: #1F487E;
    color: #fff;
    border-radius: 0 0 8px 8px;
}

.card-button:hover {
    text-decoration: none;
    background-color: #1D3461;
    color: #fff;

}

.textbook-al, .textbook-vc, .textbook-aw, .textbook-n {
    position: relative;
}

.textbook-al::before, .textbook-vc::before, .textbook-aw::before, .textbook-n::before {
    content: "";
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
    position: absolute;
    top: 0px;
    right: 0px;
    bottom: 0px;
    left: 0px;
    opacity: 0.75
}

.textbook-al::before{
    background-image: url("/images/textbook-AL.jpg");
}

.textbook-vc::before {
    background-image: url("/images/textbook-VC.jpg");
}

.textbook-aw::before {
    background-image: url("/images/textbook-AW.jpg");
}

.textbook-n::before {
    background-image: url("/images/textbook-N.jpg");
}

.btn-shadow {
    box-shadow: 3px 3px 3px rgb(255, 255, 255);
}

@keyframes pulse {
    0% {
        -moz-transform: scale(0.9);
        -ms-transform: scale(0.9);
        -webkit-transform: scale(0.9);
        transform: scale(0.9);
    }

    70% {
        -moz-transform: scale(1);
        -ms-transform: scale(1);
        -webkit-transform: scale(1);
        transform: scale(1);
        box-shadow: 0 0 0 50px rgba(90, 153, 212, 0);
    }

    100% {
        -moz-transform: scale(0.9);
        -ms-transform: scale(0.9);
        -webkit-transform: scale(0.9);
        transform: scale(0.9);
        box-shadow: 0 0 0 0 rgba(90, 153, 212, 0);
    }
}

@-webkit-keyframes pulse {
    0% {
        -moz-transform: scale(0.9);
        -ms-transform: scale(0.9);
        -webkit-transform: scale(0.9);
        transform: scale(0.9);
    }

    70% {
        -moz-transform: scale(1);
        -ms-transform: scale(1);
        -webkit-transform: scale(1);
        transform: scale(1);
        box-shadow: 0 0 0 50px rgba(90, 153, 212, 0);
    }

    100% {
        -moz-transform: scale(0.9);
        -ms-transform: scale(0.9);
        -webkit-transform: scale(0.9);
        transform: scale(0.9);
        box-shadow: 0 0 0 0 rgba(90, 153, 212, 0);
    }
}
</style>
