<template>
    <div class="body">
        <div class="d-flex">
            <student-sidebar-component @collapse="onCollapse" />
            <div :class="collapsed ? 'collapsed-menu' : 'not-collapsed-menu'" class="main-content mx-5 w-100 h-100">
                <div class="d-flex justify-content-center">
                    <div class="card rounded-0 py-5 px-5" style="width: 100%;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Play Trial</h4>
                            <img src="/images/kids.png" alt="Kids" width="150">
                        </div>

                        <div class="card-body">
                            <div class="row d-flex justify-content-between mb-5">
                                <div class="col-lg-3 col-md-12 col-sm-12 p-0">
                                    <div id="memoryGame" class="card-sl card-animate-hover card-animation animate__animated">
                                        <div class="card-image bg-card">
                                            <img src="/images/memory.png" />
                                        </div>
                                        <button @click="gameNavigate('/memory-game', 'memoryGame')" type="button" class="card-action btn"><i class="fa fa-play"></i></button>
                                        <div class="card-heading">
                                            Memory Game
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-12 col-sm-12 p-0">
                                    <div id="typingBalloon" class="card-sl card-animate-hover card-animation animate__animated">
                                        <div class="card-image bg-card">
                                            <img src="/images/balloon.png" />
                                        </div>
                                        <button @click="gameNavigate('/typing-balloon', 'typingBalloon')" type="button" class="card-action btn"><i class="fa fa-play"></i></button>
                                        <div class="card-heading">
                                            Typing Balloon
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-12 col-sm-12 p-0">
                                    <div id="hangmanGame" class="card-sl card-animate-hover card-animation animate__animated">
                                        <div class="card-image bg-card">
                                            <img src="/images/hangman.png" />
                                        </div>
                                        <button @click="gameNavigate('/hangman-game', 'hangmanGame')" type="button" class="card-action btn"><i class="fa fa-play"></i></button>
                                        <div class="card-heading">
                                            Hangman
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-12 col-sm-12 p-0">
                                    <div id="matchingCards" class="card-sl card-animate-hover card-animation animate__animated">
                                        <div class="card-image bg-card">
                                            <img src="/images/matching-game.jpg" />
                                        </div>
                                        <button @click="gameNavigate('/matching-cards', 'matchingCards')" type="button" class="card-action btn"><i class="fa fa-play"></i></button>
                                        <div class="card-heading">
                                            Matching Cards
                                        </div>
                                    </div>
                                </div>
                            </div>
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
        gameNavigate(page, cardId) {
            var element = document.getElementById(cardId);
            element.classList.remove("card-animation");
            element.classList.remove("card-animate-hover");
            element.classList.add("animate__zoomOutUp");

            setTimeout(() => {
                this.$router.push(page)
            }, 1000)
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
    overflow-x: hidden;
}

.card {
    background-color: rgba(255, 255, 255, 0.5);
    border: none;
}

.card-title {
    font-weight: bold;
}

.card-sl {
    border-radius: 8px !important;
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    width: 250px;
    height: 270px;
    position: relative;
    z-index: 999
}

.card-animate-hover:hover {
    animation: scaleUpDown 1s ease-in-out infinite;
}

@keyframes scaleUpDown {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.03);
    }
    100% {
        transform: scale(1);
    }
}

.card-image {
    display: flex;
    justify-content: center;
    border-radius: 8px 8px 0px 0;
}

.card-image img {
    width: 230px;
    height: 230px;
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
    border-radius: 0 0 8px 8px;
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

.card-animation {
    transition: 1s box-shadow;
}

.card-animation:hover {
    box-shadow: 0 5px 35px 0px rgba(255, 0, 0, 0.1);
}

.card-animation:hover::before, .card-animation:hover::after {
  display: block;
  content: '';
  position: absolute;
  width: 250px;
  height: 270px;
  background: #FDA8CF;
  border-radius: 5px;
  z-index: -1;
  animation: 1s clockwise infinite;
}

.card-animation:hover:after {
  background: #F3CE5E;
  animation: 2s counterclockwise infinite;
}

@keyframes clockwise {
  0% {
    top: -5px;
    left: 0;
  }
  12% {
    top: -2px;
    left: 2px;
  }
  25% {
    top: 0;
    left: 5px;
  }
  37% {
    top: 2px;
    left: 2px;
  }
  50% {
    top: 5px;
    left: 0;
  }
  62% {
    top: 2px;
    left: -2px;
  }
  75% {
    top: 0;
    left: -5px;
  }
  87% {
    top: -2px;
    left: -2px;
  }
  100% {
    top: -5px;
    left: 0;
  }
}

@keyframes counterclockwise {
  0% {
    top: -5px;
    right: 0;
  }
  12% {
    top: -2px;
    right: 2px;
  }
  25% {
    top: 0;
    right: 5px;
  }
  37% {
    top: 2px;
    right: 2px;
  }
  50% {
    top: 5px;
    right: 0;
  }
  62% {
    top: 2px;
    right: -2px;
  }
  75% {
    top: 0;
    right: -5px;
  }
  87% {
    top: -2px;
    right: -2px;
  }
  100% {
    top: -5px;
    right: 0;
  }
}

.bg-card {
    background: #aed7fd;
    /* background: radial-gradient(circle, rgba(174,209,238,1) 0%, rgba(148,233,226,1) 100%); */
}
</style>
