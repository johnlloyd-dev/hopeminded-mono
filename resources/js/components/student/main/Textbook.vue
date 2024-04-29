<template>
    <div class="body">
        <div class="d-flex">
            <student-sidebar-component @collapse="onCollapse" />
            <div :class="collapsed ? 'collapsed-menu' : 'not-collapsed-menu'" class="main-content mx-5 w-100 h-100">
                <div class="textbook-content">
                    <div class="card rounded-0 py-5 px-5" style="width: 100%">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Textbooks - Chapter {{ selectedChapter }}</h4>
                            <img src="/images/kids.png" alt="Kids" width="150">
                        </div>
                        <template v-if="!isSwitching">
                            <div style="height: 29rem;">
                                <div class="row mb-3">
                                    <div class="col-lg-12 mt-2">
                                        <div class="btn-group">
                                            <button class="btn btn-lg btn-success dropdown-toggle rounded-0" type="button"
                                                id="defaultDropdown" data-bs-toggle="dropdown" data-bs-auto-close="true"
                                                aria-expanded="false">
                                                Select Chapter
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="defaultDropdown">
                                                <li><button class="dropdown-item"
                                                        :class="{ active: selectedChapter == 1 }"
                                                        @click="selectChapter(1)">Chapter 1</button></li>
                                                <li><button class="dropdown-item"
                                                        :class="{ active: selectedChapter == 2 }"
                                                        @click="selectChapter(2)">Chapter 2</button></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3">
                                        <div :class="{ 'opacity-75 pe-none': isProcessing }" class="card" style="width: 18rem;">
                                            <img src="/images/alphabets.png" class="card-img-top" alt="...">
                                            <div class="card-body">
                                                <h5 class="card-title">Alphabets/Letters</h5>
                                                <router-link v-if="selectedChapter == 1" to="/alphabet-by-letters"
                                                    class="btn btn-primary">Start the
                                                    lesson</router-link>
                                                <router-link v-else to="/coming-soon" class="btn btn-primary">Start the
                                                    lesson</router-link>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div :class="{ 'opacity-75 pe-none': skillTestAL.data_items && skillTestAL.data_items.length < quantityRequirement?.value || isProcessing }" class="card" style="width: 18rem;">
                                            <img src="/images/vc.png" class="card-img-top" alt="...">
                                            <div class="card-body">
                                                <h5 class="card-title">Vowels/Consonants</h5>
                                                <router-link v-if="selectedChapter == 1" to="/vowel-consonants"
                                                    class="btn btn-primary">Start the
                                                    lesson</router-link>
                                                <router-link v-else to="/coming-soon" class="btn btn-primary">Start the
                                                    lesson</router-link>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div :class="{ 'opacity-75 pe-none': (skillTestAL.data_items && skillTestVC.data_items.length < quantityRequirement?.value) || isProcessing }" class="card" style="width: 18rem;">
                                            <img src="/images/words.png" class="card-img-top" alt="...">
                                            <div class="card-body">
                                                <h5 class="card-title">Alphabets/Words</h5>
                                                <router-link v-if="selectedChapter == 1" to="/alphabet-by-words"
                                                    class="btn btn-primary">Start the
                                                    lesson</router-link>
                                                <router-link v-else to="/coming-soon" class="btn btn-primary">Start the
                                                    lesson</router-link>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <!-- <div :class="{ 'opacity-75 pe-none': (skillTestAL.data_items && skillTestVC.data_items.length < quantityRequirement?.value) || isProcessing }" class="card" style="width: 18rem;"> -->
                                        <div class="card" style="width: 18rem;">
                                            <img src="/images/numbers.png" class="card-img-top" alt="...">
                                            <div class="card-body">
                                                <h5 class="card-title">Numbers</h5>
                                                <router-link v-if="selectedChapter == 1" to="/numbers"
                                                    class="btn btn-primary">Start the
                                                    lesson</router-link>
                                                <router-link v-else to="/coming-soon" class="btn btn-primary">Start the
                                                    lesson</router-link>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <div class="d-flex justify-content-center align-items-center" style="height: 29rem;">
                                <div class="spinner-border" style="width: 3rem; height: 3rem;" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import AlphabetsLetters from '../textbook/AlphabetLetters.vue'
import AlphabetsWords from '../textbook/AlphabetWords.vue'
import VowelConsonants from '../textbook/VowelConsonants.vue'
export default {
    components: {
        AlphabetsLetters,
        AlphabetsWords,
        VowelConsonants
    },
    data() {
        return {
            collapsed: false,
            data: [],
            subject: 1,
            pageFlag: 1,
            selectedChapter: 1,
            imgSrc: null,
            isSwitching: false,
            isProcessing: false,
            skillTestAL: {
                data: []
            },
            skillTestAW: {
                data: []
            },
            skillTestVC: {
                data: []
            },
        };
    },
    mounted() {
        if (localStorage.getItem('selectedChapter') == undefined || localStorage.getItem('selectedChapter') == null) {
            this.selectedChapter = 1
        } else {
            this.selectedChapter = localStorage.getItem('selectedChapter')
        }
        this.getSkillTest()
        this.getQuantityRequirement('skill_test')
    },
    computed: {
        ...mapGetters({
            quantityRequirement: 'quantityRequirement'
        })
    },
    methods: {
        ...mapActions({
            getQuantityRequirement: 'getQuantityRequiement'
        }),
        selectChapter(value) {
            this.selectedChapter = value
            localStorage.setItem('selectedChapter', value)
            this.isSwitching = true
            setTimeout(() => {
                this.isSwitching = false
            }, 500);
        },
        onCollapse(isCollapsed) {
            if (isCollapsed) {
                this.collapsed = true;
            } else {
                this.collapsed = false;
            }
        },
        setPageButton(flag) {
            if (flag == 2) {
                if (this.pageFlag != 5) {
                    this.pageFlag++
                }
            } else {
                this.pageFlag--
            }
        },
        async getSkillTest() {
            this.isProcessing = true
            try {
                const data1 = await axios.get(`/api/skill-test/fetch/alphabet-letters`)
                const data2 = await axios.get(`/api/skill-test/fetch/vowel-consonants`)
                const data3 = await axios.get(`/api/skill-test/fetch/alphabet-words`)

                if(data1.status === 200 && data2.status === 200 && data3.status === 200) {
                    this.skillTestAL = data1.data
                    this.skillTestVC = data2.data
                    this.skillTestAW = data3.data
                }

            } catch (error) {
                console.log(error)
            } finally {
                this.isProcessing = false
            }
        },
        selectSubject(subject) {
            this.subject = subject
        }
    },
};
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
    color: #e26d5c;
    background: #fff;
    border-radius: 50%;
    padding: 15px;
    font-size: 15px;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.2), 0 1px 2px 0 rgba(0, 0, 0, 0.19);
}

.card-action:hover {
    color: #fff;
    background: #e26d5c;
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
    background-color: #1f487e;
    color: #fff;
    border-radius: 0 0 8px 8px;
}

.card-button:hover {
    text-decoration: none;
    background-color: #1d3461;
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
}</style>
