<template>
    <div class="card mt-3">
        <div class="card-header">
            <h3 class="fw-bold mb-2">{{ gameName }}</h3>
        </div>
        <div class="card-body">
            <div class="skill-test mb-3">
                <div class="mb-3">
                    <button class="btn btn-secondary d-block fw-bold rounded-0 w-25 d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse"
                        data-bs-target="#skillTestReport" aria-expanded="false" aria-controls="skillTestReport">
                        <span>Skill Test Report</span>
                        <i class="fas fa-chevron-circle-down"></i>
                    </button>
                    <!-- <div style="height: 60px">
                        <span>
                            Perfect score:
                        </span>
                        <div class="d-flex justify-content-between align-items-center w-100 p-3">
                            <template v-if="isEditPerfectScore">
                                <input type="text" class="form-control w-75" placeholder="Enter perfect score">
                                <button @click="isEditPerfectScore = !isEditPerfectScore"
                                    class="btn btn-warning btn-sm">
                                    <i class="fas fa-save"></i>
                                </button>
                            </template>
                            <template v-else>
                                <h6 class="mb-0 w-75 text-center">0/10</h6>
                                <button @click="isEditPerfectScore = !isEditPerfectScore"
                                    class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </template>
                        </div>
                    </div> -->
                </div>
                <div class="collapse rounded-0" id="skillTestReport">
                    <div class="card card-body rounded-0">
                        <template v-if="isLoading">
                            <ul class="o-vertical-spacing o-vertical-spacing--l">
                                <li class="blog-post o-media">
                                    <div class="o-media__figure">
                                        <span class="skeleton-box" style="width:100px;height:80px;"></span>
                                    </div>
                                    <div class="o-media__body">
                                        <div class="o-vertical-spacing">
                                            <h3 class="blog-post__headline">
                                                <span class="skeleton-box" style="width:55%;"></span>
                                            </h3>
                                            <p>
                                                <span class="skeleton-box" style="width:80%;"></span>
                                                <span class="skeleton-box" style="width:90%;"></span>
                                                <span class="skeleton-box" style="width:83%;"></span>
                                                <span class="skeleton-box" style="width:80%;"></span>
                                            </p>
                                            <div class="blog-post__meta">
                                                <span class="skeleton-box" style="width:70px;"></span>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </template>
                        <table v-else class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center" scope="col">Alphabet</th>
                                    <th class="w-75 text-center" scope="col">Score Sheet</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in allAphabets" :key="item">
                                    <th class="text-center" scope="row">{{ item.toUpperCase() }}</th>
                                    <td>
                                        <template v-if="!skillTest.hasOwnProperty(item)">
                                            <div>
                                                <p class="text-danger">No submitted skill test for this alphabet yet.</p>
                                            </div>
                                        </template>
                                        <template v-else>
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">Object</th>
                                                        <th class="w-50" scope="col">Skill Test Video</th>
                                                        <th class="w-25" scope="col">Score</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="data in skillTest[item]" :key="data.id">
                                                        <th scope="row">{{ data.object }}</th>
                                                        <td>
                                                            Play Skill Test Video
                                                            <button @click="playSkillTestVideo(data.file_url)" class="btn">
                                                                <i class="fas fa-play-circle fa-lg"></i>
                                                            </button>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex justify-content-around align-items-center">
                                                                <template v-if="isEditScore">
                                                                    <input type="text" class="form-control w-75">
                                                                    <button @click="isEditScore = !isEditScore"
                                                                        class="btn btn-success btn-sm">
                                                                        <i class="fas fa-save"></i>
                                                                    </button>
                                                                </template>
                                                                <template v-else>
                                                                    <h6 class="mb-0 w-75 text-center">0/10</h6>
                                                                    <button @click="isEditScore = !isEditScore"
                                                                        class="btn btn-success btn-sm">
                                                                        <i class="fas fa-edit"></i>
                                                                    </button>
                                                                </template>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </template>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <hr>
            <div class="quiz-report">
                <div>
                    <button class="btn btn-secondary d-block fw-bold rounded-0 w-25 d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse"
                        data-bs-target="#quizReport" aria-expanded="false" aria-controls="quizReport">
                        Quiz Report <i class="fas fa-chevron-circle-down"></i>
                    </button>
                </div>
                <div class="collapse" id="quizReport">
                    <div class="card card-body rounded-0">
                        Some placeholder content for the collapse component. This panel is hidden by default but revealed
                        when the user activates the relevant trigger.
                    </div>
                </div>
            </div>
            <!-- Modal -->
            <div class="modal fade rounded-0" id="skillTestVideoModal" tabindex="-1" aria-labelledby="skillTestVideoModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <button type="button" class="btn-close text-end" data-bs-dismiss="modal" aria-label="Close"></button>
                        <div class="modal-body d-flex justify-content-center">
                            <div class="embed-responsive embed-responsive-21by9">
                                <iframe height="500" width="500" class="embed-responsive-item" :src="skillTestVideoUrl"
                                    allowfullscreen></iframe>
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
    props: ['gameName', 'studentId', 'flag'],
    data() {
        return {
            indexes: {
                skillTest: 'a'
            },
            isEditScore: false,
            isEditPerfectScore: false,
            isLoading: false,
            skillTestVideoUrl: null,
            skillTest: []
        }
    },
    created() {
        this.getSkillTest()
    },
    watch: {
        async flag() {
            this.skillTest = []
            this.isLoading = true
            await this.getSkillTest()
        }
    },
    computed: {
        allAphabets() {
            return Array.from({ length: 26 }, (_, i) => String.fromCharCode(97 + i));
        }
    },
    methods: {
        async getSkillTest() {
            this.isLoading = true
            try {
                const data = await axios.get(`/api/skill-test/fetch/${this.studentId}/${this.flag}`)
                const mergedObject = data.data.reduce((result, item) => {
                    const { letter, ...rest } = item;

                    if (result[letter]) {
                        result[letter].push(rest);
                    } else {
                        result[letter] = [rest];
                    }

                    return result;
                }, {});

                this.skillTest = mergedObject
                this.indexes.skillTest = Object.keys(mergedObject)[0]
            } catch (error) {
                console.log(error)
            } finally {
                this.isLoading = false
            }
        },
        playSkillTestVideo(video_url) {
            this.skillTestVideoUrl = video_url
            $('#skillTestVideoModal').modal('show')
        }
    }
}
</script>

<style lang="scss" scoped>
.skeleton-box {
    display: inline-block;
    height: 1em;
    position: relative;
    overflow: hidden;
    background-color: #DDDBDD;

    &::after {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        transform: translateX(-100%);
        background-image: linear-gradient(90deg,
                rgba(#fff, 0) 0,
                rgba(#fff, 0.2) 20%,
                rgba(#fff, 0.5) 60%,
                rgba(#fff, 0));
        animation: shimmer 5s infinite;
        content: '';
    }

    @keyframes shimmer {
        100% {
            transform: translateX(100%);
        }
    }
}

.blog-post {
    &__headline {
        font-size: 1.25em;
        font-weight: bold;
    }

    &__meta {
        font-size: 0.85em;
        color: #6b6b6b;
    }
}

// OBJECTS

.o-media {
    display: flex;

    &__body {
        flex-grow: 1;
        margin-left: 1em;
    }
}

.o-vertical-spacing {
    >*+* {
        margin-top: 0.75em;
    }

    &--l {
        >*+* {
            margin-top: 2em;
        }
    }
}
</style>
