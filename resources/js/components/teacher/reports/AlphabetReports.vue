<template>
    <div class="card mt-3">
        <div class="card-header">
            <h3 class="fw-bold mb-2">{{ gameName }}</h3>
        </div>
        <div class="card-body">
            <div class="skill-test mb-3">
                <div class="mb-3">
                    <button
                        class="btn btn-secondary d-block fw-bold rounded-0 w-100 d-flex justify-content-between align-items-center"
                        type="button" data-bs-toggle="collapse" data-bs-target="#skillTestReport" aria-expanded="false"
                        aria-controls="skillTestReport">
                        <span>Skill Test Report</span>
                        <i class="fas fa-chevron-circle-down"></i>
                    </button>
                </div>
                <div class="collapse show rounded-0" id="skillTestReport">
                    <p class="alert alert-warning rounded-0 fw-bold" role="alert">Note: For scoring, <code>[5]</code> pts
                        for wrong mark and <code>[10]</code> pts for correct mark.</p>
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
                        <tbody>
                            <tr v-for="(item) in allAphabets" :key="item">
                                <td>
                                    <div class="card rounded-0 border-bottom-0 p-2">
                                        <div class="row p-1">
                                            <div class="col-2 d-flex align-items-center">
                                                <h5 class="mb-0">Alphabet: <span class="fw-bold text-danger">{{
                                                    item.toUpperCase() }}</span></h5>
                                            </div>
                                            <div v-if="skillTest.hasOwnProperty(item)"
                                                class="col-2 d-flex align-items-center">
                                                <h5 class="mb-0">Object: <span class="fw-bold text-danger">{{
                                                    skillTest[item][0].object }}</span></h5>
                                            </div>
                                            <div v-if="skillTestRetake.hasOwnProperty(item)"
                                                class="col-2 d-flex align-items-center">
                                                <h5 class="mb-0">AllowedRetake: <span class="fw-bold text-danger">[ {{ skillTestRetake.hasOwnProperty(item) ? skillTestRetake[item].allowed_retake : 0 }} ]</span></h5>
                                            </div>
                                            <!-- <div v-if="skillTest.hasOwnProperty(item)"
                                                class="col-5 d-flex align-items-center">
                                                <h5 class="mb-0">Allowed Retake:</h5>
                                                <span class="ms-3">
                                                    <template
                                                        v-if="!showSkillTestRetakeModify.hasOwnProperty(item) || !showSkillTestRetakeModify[item]">
                                                        <span style="border-bottom: 3px solid black"
                                                            class="h5 fw-bold me-3 text-center text-danger">{{
                                                                skillTestRetake[item].allowed_retake }}</span>
                                                        <button
                                                            @click="showSkillTestRetakeModify[item] = true, allowedSkillTestAttempt[item] = skillTestRetake[item].allowed_retake"
                                                            class="btn btn-success rounded-0 btn-sm border-0">
                                                            <i class="fas fa-edit fa-sm"></i>
                                                        </button>
                                                    </template>
                                                    <template v-else>
                                                        <form
                                                            @submit.prevent="allowRetakeSkillTest(skillTestRetake[item], item)"
                                                            class="d-flex align-items-center">
                                                            <div>
                                                                <input type="number" v-model="allowedSkillTestAttempt[item]"
                                                                    class="form-control form-control-sm rounded-0" min="1">
                                                            </div>
                                                            <button :disabled="allowedSkillTestAttempt[item] < 1"
                                                                type="submit"
                                                                class="btn btn-primary rounded-0 btn-sm border-0">
                                                                <i class="fas fa-save fa-sm"></i>
                                                            </button>
                                                            <button @click="showSkillTestRetakeModify[item] = false"
                                                                type="button"
                                                                class="btn btn-secondary rounded-0 btn-sm border-0">
                                                                <i class="fas fa-close fa-sm"></i>
                                                            </button>
                                                        </form>
                                                    </template>
                                                </span>
                                            </div> -->
                                        </div>
                                    </div>
                                    <table class="table table-bordered">
                                        <thead class="table-secondary">
                                            <tr>
                                                <th style="width: 15%">Attempt No.</th>
                                                <th style="width: 20%">Date/Time</th>
                                                <th style="width: 20%">Skill Test Video</th>
                                                <th style="width: 20%">Mark</th>
                                                <th>Score</th>
                                            </tr>
                                        </thead>
                                        <tbody v-if="skillTest.hasOwnProperty(item)">
                                            <tr class="fw-bold" v-for="(data, index) in skillTest[item]" :key="data.id">
                                                <td>{{ index + 1 }}</td>
                                                <td>{{ new Date(data.created_at).toLocaleString() }}</td>
                                                <td> <button @click="playSkillTestVideo(skillTest[item][0].file_url)"
                                                        class="btn border-0">
                                                        <i class="fas fa-play-circle fa-lg text-danger"></i>
                                                    </button>
                                                </td>
                                                <td>
                                                    <span>
                                                        <template v-if="!data.is_display">
                                                            <div class="d-flex justify-content-around">
                                                                <span class="fw-bold"
                                                                    :class="data.status === 'pending' ? 'text-warning' : (data.status === 'correct' ? 'text-success' : 'text-danger')">{{
                                                                        data.status === 'pending' ? '[UNCHECKED]' :
                                                                        `[${data.status.toUpperCase()}]` }}</span>
                                                                <button
                                                                    @click="data.is_display = !data.is_display, newScore[data.letter] = data.score"
                                                                    class="btn btn-success rounded-0 btn-sm border-0">
                                                                    <i class="fas fa-edit fa-sm"></i>
                                                                </button>
                                                            </div>
                                                        </template>
                                                        <template v-else>
                                                            <div class="update-mark">
                                                                <div class="card rounded-0 position-relative">
                                                                    <button type="button"
                                                                        @click="data.is_display = !data.is_display"
                                                                        class="btn border-0 rounded-0 position-absolute top-0 start-100 translate-middle">
                                                                        <i class="fas fa-close fa-lg"></i>
                                                                    </button>
                                                                    <h6 class="fw-bold p-2">Mark: <span class="fw-bold"
                                                                            :class="data.status === 'pending' ? 'text-warning' : (data.status === 'correct' ? 'text-success' : 'text-danger')">{{
                                                                                data.status === 'pending' ? '[UNCHECKED]' :
                                                                                `[${data.status.toUpperCase()}]` }}</span></h6>
                                                                    <div
                                                                        class="card-body d-flex justify-content-around p-0">
                                                                        <div class="d-flex flex-column">
                                                                            <button type="button"
                                                                                @click="markSkillTest(data, 'correct')"
                                                                                class="btn rounded-0 text-success p-0 border-0 zoom-button">
                                                                                <i class="fas fa-check-circle h1"></i>
                                                                            </button>
                                                                            <span>Correct</span>
                                                                        </div>
                                                                        <div class="d-flex flex-column">
                                                                            <button type="button"
                                                                                @click="markSkillTest(data, 'wrong')"
                                                                                class="btn rounded-0 text-danger p-0 border-0 zoom-button">
                                                                                <i class="fas fa-times-circle h1"></i>
                                                                            </button>
                                                                            <span>Wrong</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <small v-if="data.error_message" class="text-danger">Score
                                                                must not be greater than
                                                                the perfect score.</small>
                                                        </template>
                                                    </span>
                                                </td>
                                                <td>
                                                    {{ data.score + '/' + data.perfect_score }}
                                                </td>
                                            </tr>
                                            <tr class="fw-bold border-0">
                                                <td colspan="3" class="border-0"></td>
                                                <td class="text-end border-0">Highest Score:</td>
                                                <td class="border-0">{{ highestScore[item] }}/10</td>
                                            </tr>
                                        </tbody>
                                        <tbody v-else>
                                            <tr class="fw-bold text-center">
                                                <td class="text-danger" colspan="6">No skill test submitted yet</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Skill Test Summary -->
                <div v-if="Object.keys(skillTest).length" class="alert alert-success d-flex justify-content-around"
                    role="alert">
                    <div class="w-50 me-5">
                        <h6 class="fw-bold">Skill Test Score Summary</h6>
                        <p class="d-flex">Submitted:
                            <span class="fw-bold ms-1">{{ Object.keys(skillTest).length }}/26</span>
                            <span class="fw-bold ms-1 text-danger"
                                v-if="Object.keys(skillTest).length !== 26">(INCOMPLETE)</span>
                        </p>
                        <h6>Choose a percentage for a passing score:</h6>
                        <div class="d-flex justify-content-around">
                            <div v-for="(percentage, index) in percentages" :key="index" class="form-check">
                                <input @click="updatePassingPercentage('skill_test')"
                                    v-model="selectedPercentage.skill_test" :value="percentage.value"
                                    class="form-check-input" type="radio" :id="`percentageOptions${index}`">
                                <label class="form-check-label fw-bold" :for="`percentageOptions${index}`">
                                    {{ percentage.name }}
                                </label>
                                <i v-if="percentage.value == selectedPercentage.skill_test"
                                    class="fas fa-check fa-lg ms-1"></i>
                            </div>
                        </div>
                        <hr>
                        <p :class="{ 'mb-0': Object.keys(skillTest).length !== 26 }">{{ Object.keys(skillTest).length !== 26
                            ? 'Partial Average' : 'Average' }}: <span class="fw-bold">{{ skillTestAverageScore.display }}</span></p>
                        <p v-if="Object.keys(skillTest).length !== 26" class="fw-bold">Note: <span>This average is
                                considered partial due to the student's incomplete skill test submissions.</span></p>
                        <h6>Score Percentage: <span class="fw-bold">{{ calculatePercentage(skillTestAverageScore.score)
                        }}%</span></h6>
                        <h6>Mark: <span class="fw-bold"
                                :class="calculatePercentage(skillTestAverageScore.score) < selectedPercentage.skill_test ? 'text-danger' : 'text-success'">{{
                                    (calculatePercentage(skillTestAverageScore.score) < selectedPercentage.skill_test)
                                    ? 'FAILED' : 'PASSED' }}</span>
                        </h6>
                    </div>
                    <div class="w-50 ms-5">
                        <div style="height: 100%" class="border border-secondary rounded p-3">
                            <template
                                v-if="!showSkillTestRetakeModify.hasOwnProperty(filteredFlag) || showSkillTestRetakeModify[filteredFlag] == false">
                                <!-- <h4 class="text-center fw-bold">Allowed Retake: <span class="fw-bold text-danger">{{
                                    Object.keys(skillTestMainRetake).length ? skillTestMainRetake.main_retake : 0
                                }}</span></h4> -->
                                <!-- <button @click="showSkillTestRetakeModify[filteredFlag] = true"
                                    :disabled="Object.keys(skillTest).length !== 26"
                                    class="btn btn-secondary border border-secondary p-4">
                                    <i class="fas fa-edit fa-lg h1"></i>
                                </button> -->
                                <div class="text-center">
                                    <button @click="allowSkillTest()" class="btn btn-secondary border border-secondary p-4">
                                        <i class="fas fa-edit fa-lg h1"></i>
                                    </button>
                                    <p class="text-center fw-bold h4 mt-1">Update Retake</p>
                                    <p class="fw-bold alert alert-warning mt-3 mb-0 text-start" role="alert">Note:
                                    <ul class="text-justify">
                                        <li>Button is disabled if the submitted skill tests are not complete.</li>
                                        <li>By updating the allowed retake for skill test, it will allow student to
                                            retake/resubmit skill tests for each alphabet.</li>
                                    </ul>
                                    </p>
                                </div>
                            </template>
                            <template v-else-if="showSkillTestRetakeModify[filteredFlag] == true">
                                <div class="alert alert-warning" role="alert">
                                    <h6 class="fw-bold">-Display-</h6>
                                    <p class="fw-bold"><input class="form-check-input me-1" type="checkbox" disabled
                                            id="flexCheckDefault">Alphabet <span class="text-danger">(Total Allowed
                                            Retake)</span></p>
                                    <p class="mb-0">Note: Some checkboxes are disabled if student did not submit a skill test for the
                                        alphabet yet.</p>
                                </div>
                                <form @submit.prevent="allowRetakeSkillTest()">
                                    <div class="form-check ms-1">
                                        <input @change="selectAll()" :disabled="!Object.keys(skillTestRetake).length" v-model="selectAllSkillTestRetake" class="form-check-input" type="checkbox"
                                            id="flexCheckDefault">
                                        <label class="form-check-label fw-bold" for="flexCheckDefault">
                                            Select All
                                        </label>
                                    </div>
                                    <div class="row mb-3">
                                        <div v-for="(item) in allAphabets" :key="item" class="col-lg-2">
                                            <div class="form-check d-flex justify-content-around">
                                                <input :disabled="!skillTestRetake.hasOwnProperty(item)" v-model="allowedskillTestRetake" class="form-check-input" type="checkbox" :value="item"
                                                    id="flexCheckDefault">
                                                <label class="form-check-label fw-bold" for="flexCheckDefault">
                                                    {{ item.toUpperCase() }} <span class="text-danger">( {{ skillTestRetake.hasOwnProperty(item) ? skillTestRetake[item].allowed_retake : 0 }} )</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center d-flex justify-content-center align-items-center">
                                        <div class="text-center me-3">
                                            <input type="number" v-model="allowedSkillTestAttempt[filteredFlag]"
                                                class="form-control" min="0">
                                        </div>
                                        <button :disabled="allowedSkillTestAttempt[filteredFlag] < 0 || allowedskillTestRetake.length === 0" type="submit"
                                            class="btn btn-primary border-0 me-1">
                                            <i class="fas fa-save fa-lg"></i>
                                        </button>
                                        <button @click="showSkillTestRetakeModify[filteredFlag] = false" type="button"
                                            class="btn btn-secondary border-0 ms-1">
                                            <i class="fas fa-close fa-lg"></i>
                                        </button>
                                    </div>
                                </form>
                            </template>
                        </div>
                    </div>
                </div>
                <!-- End of Skill Test Summary -->
            </div>
            <hr>
            <div class="quiz-report mb-3">
                <div class="mb-3">
                    <button
                        class="btn btn-secondary d-block fw-bold rounded-0 w-100 d-flex justify-content-between align-items-center"
                        type="button" data-bs-toggle="collapse" data-bs-target="#quizReport" aria-expanded="false"
                        aria-controls="quizReport">
                        Quiz Report <i class="fas fa-chevron-circle-down"></i>
                    </button>
                </div>
                <div class="collapse show mb-3" id="quizReport">
                    <div v-if="reports.length" class="d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold my-3">Perfect Score: <span class="text-danger">{{ reports.length ?
                            reports[0].perfect_score : '(No data found)' }}</span></h6>
                        <!-- <div class="d-flex">
                            Allowed Retake:
                            <span class="ms-3">
                                <template
                                    v-if="!showQuizRetakeModify.hasOwnProperty(filteredFlag) || !showQuizRetakeModify[filteredFlag]">
                                    <span style="border-bottom: 3px solid black"
                                        class="h5 fw-bold me-3 text-center text-danger">{{
                                            quizReportRetake[filteredFlag].allowed_retake }}</span>
                                    <button @click="allowQuiz()" class="btn btn-success rounded-0 btn-sm border-0">
                                        <i class="fas fa-edit fa-sm"></i>
                                    </button>
                                </template>
                                <template v-else>
                                    <form @submit.prevent="allowRetakeQuiz(quizReportRetake[filteredFlag], filteredFlag)"
                                        class="d-flex align-items-center">
                                        <div>
                                            <input type="number" v-model="allowedQuizAttempt[filteredFlag]"
                                                class="form-control form-control-sm rounded-0" min="1">
                                        </div>
                                        <button :disabled="allowedQuizAttempt[filteredFlag] < 1" type="submit"
                                            class="btn btn-primary rounded-0 btn-sm border-0">
                                            <i class="fas fa-save fa-sm"></i>
                                        </button>
                                        <button @click="showQuizRetakeModify[filteredFlag] = false" type="button"
                                            class="btn btn-secondary rounded-0 btn-sm border-0">
                                            <i class="fas fa-close fa-sm"></i>
                                        </button>
                                    </form>
                                </template>
                            </span>
                        </div> -->
                        <button @click="viewWeaknesses()" class="btn btn-success btn-sm rounded-0 text-end">
                            Weaknesses <i class="fas fa-book-reader"></i>
                        </button>
                    </div>
                    <div class="card card-body rounded-0">
                        <table class="table table-bordered table-responsive">
                            <thead class="table-secondary">
                                <tr>
                                    <th>Attempt No.</th>
                                    <th>Date/Time</th>
                                    <th>Score</th>
                                    <th>Percentage</th>
                                    <th>Mark</th>
                                </tr>
                            </thead>
                            <tbody v-if="isReportsLoading">
                                <tr>
                                    <td colspan="6" class="text-center fw-bold">Loading...</td>
                                </tr>
                            </tbody>
                            <tbody v-else-if="!isReportsLoading && reports.length == 0">
                                <tr>
                                    <td colspan="6" class="text-center fw-bold">No data found</td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr class="fw-bold" v-for="item in reports" :key="item.id">
                                    <td>{{ item.attempt_number }}</td>
                                    <td>{{ new Date(item.created_at).toLocaleString() }}</td>
                                    <td>{{ item.total_score }}</td>
                                    <td>{{ item.percentage }}%</td>
                                    <td>{{ upperCaseFirstLetter(item.mark) }} </td>
                                </tr>
                                <tr class="fw-bold border-0">
                                    <td colspan="1" class="border-0"></td>
                                    <td class="text-end border-0">Highest Score:</td>
                                    <td class="border-0">{{ quizReportHighestScore[$parent.gameId] }}/{{
                                        reports[0].perfect_score }}</td>
                                    <td class="border-0" colspan="2"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div v-if="reports.length" class="alert alert-success d-flex justify-content-around" role="alert">
                    <div class="w-50 me-5">
                        <h6 class="fw-bold">Quiz/Game Score Summary</h6>
                        <p>Attempts Taken: <span class="fw-bold">{{ reports.length }}</span></p>
                        <h6>Choose a percentage for a passing score:</h6>
                        <div class="d-flex justify-content-around">
                            <div v-for="(percentage, index) in percentages" :key="index" class="form-check">
                                <input @click="updatePassingPercentage('quiz')" v-model="selectedPercentage.quiz"
                                    :value="percentage.value" class="form-check-input" type="radio"
                                    :id="`percentageOptions${index}`">
                                <label class="form-check-label fw-bold" :for="`percentageOptions${index}`">
                                    {{ percentage.name }}
                                </label>
                                <i v-if="percentage.value == selectedPercentage.quiz" class="fas fa-check fa-lg ms-1"></i>
                            </div>
                        </div>
                        <hr>
                        <p>Highest Score: <span class="fw-bold">{{ quizAverageScore.display }}</span></p>
                        <h6>Score Percentage: <span class="fw-bold">{{ calculatePercentage(quizAverageScore.score,
                            reports[0].perfect_score) }}%</span></h6>
                        <h6>Mark: <span class="fw-bold"
                                :class="calculatePercentage(quizAverageScore.score, reports[0].perfect_score) < selectedPercentage.quiz ? 'text-danger' : 'text-success'">{{
                                    (calculatePercentage(quizAverageScore.score, reports[0].perfect_score) <
                                        selectedPercentage.quiz) ? 'FAILED' : 'PASSED' }}</span>
                        </h6>
                    </div>
                    <div class="ms-5 w-50 text-center">
                        <div style="height: 96%"
                            class="border border-secondary rounded p-3 text-center d-flex align-items-center justify-content-center flex-column">
                            <h4 class="fw-bold">Allowed Retake: <span class="fw-bold text-danger">{{
                                quizReportRetake[filteredFlag].allowed_retake }}</span></h4>
                            <template
                                v-if="!showQuizRetakeModify.hasOwnProperty(filteredFlag) || showQuizRetakeModify[filteredFlag] == false">
                                <button @click="allowQuiz()" class="btn btn-secondary border border-secondary p-4">
                                    <i class="fas fa-edit fa-lg h1"></i>
                                </button>
                            </template>
                            <template
                                v-else-if="showQuizRetakeModify.hasOwnProperty(filteredFlag) && showQuizRetakeModify[filteredFlag] == true">
                                <form @submit.prevent="allowRetakeQuiz(quizReportRetake[filteredFlag], filteredFlag)"
                                    class="text-center">
                                    <div class="mb-3">
                                        <input style="font-size: 30px;" type="number"
                                            v-model="allowedQuizAttempt[filteredFlag]" class="form-control" min="0">
                                    </div>
                                    <button :disabled="allowedQuizAttempt[filteredFlag] < 0" type="submit"
                                        class="btn btn-primary btn-lg border-0 me-1">
                                        <i class="fas fa-save fa-lg"></i>
                                    </button>
                                    <button @click="showQuizRetakeModify[filteredFlag] = false" type="button"
                                        class="btn btn-secondary btn-lg border-0 ms-1">
                                        <i class="fas fa-close fa-lg"></i>
                                    </button>
                                </form>
                            </template>
                        </div>
                        <!-- <p v-if="Object.keys(skillTest).length !== 26" class="position-absolute bottom-0 start-50 translate-middle-x fw-bold">Note: Button is disabled if the submitted skill test is not complete.</p> -->
                    </div>
                </div>
            </div>
            <hr>
            <!-- Summary/Average -->
            <!-- <div class="score-tally mt-3">
                <h4 class="fw-bold">Summary / Average</h4>
                <table class="table table-bordered table-responsive w-50">
                    <thead class="table-secondary">
                        <tr>
                            <th>Activity</th>
                            <th>Average Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-bold">Skill Test</td>
                            <td class="fw-bold">{{ skillTestAverageScore.display }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Quiz</td>
                            <td class="fw-bold">{{ quizAverageScore.display }}</td>
                        </tr>
                    </tbody>
                </table>
            </div> -->
            <!-- End of Summary/Certificates -->
            <div class="certificate my-3">
                <div class="card">
                    <img :src="certificate.file" alt="">
                    <div class="card-body">
                        <h3 class="mb-3 fw-bold">Upload Certificates</h3>
                        <form @submit.prevent="uploadCertificate" enctype="multipart/form-data">
                            <div class="mb-3">
                                <input ref="fileInput" accept="image/*, .doc, .docx, .pdf" name="file" @change="parseFile"
                                    type="file" class="form-control" id="certificate">
                                <small class="text-danger" v-if="errors && errors.file">{{ errors.file[0] }}</small>
                            </div>
                            <button v-if="!isProcessing" style="width: 180px" type="submit"
                                class="btn btn-success rounded-0 fw-bold">Upload
                                Certificate</button>
                            <button v-else disabled style="font-weight: bold; width: 180px;" type="button"
                                class="btn btn-success rounded-0 pb-0">
                                <Loading />
                            </button>
                        </form>
                        <hr>
                        <div class="certificates mt-3">
                            <h5 class="fw-bold">Files</h5>
                        </div>
                        <p v-if="isLoading">Loading...</p>
                        <p v-if="!isLoading && certificates.length === 0">No certificates added</p>
                        <li v-else v-for="item in certificates" :key="item.id" class="list-group-item">
                            <a target="_blank" :href="item.file_url" download style="margin-right: 20px">{{ item.file_name
                            }}</a>
                            <button type="button" @click="deleteCertificate(item.id, item.file)"
                                class="btn btn-danger btn-sm rounded-0"><i class="fas fa-trash-alt"></i></button>
                        </li>
                    </div>
                </div>
            </div>
            <!-- Modal -->
            <div class="modal fade rounded-0" id="skillTestVideoModal" tabindex="-1"
                aria-labelledby="skillTestVideoModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Skill Test Video</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body d-flex justify-content-center">
                            <div class="embed-responsive embed-responsive-21by9">
                                <iframe height="500" width="500" class="embed-responsive-item" :src="skillTestVideoUrl"
                                    allowfullscreen></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal -->
            <div class="modal fade rounded-0" id="weaknessModal" tabindex="-1" aria-labelledby="weaknessModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-fullscreen">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Weaknesses</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <template v-if="weaknessesDataLoading">
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
                            <div v-else class="accordion" id="accordionExample">
                                <div class="accordion-item">
                                    <div class="alert alert-warning" role="alert">
                                        <span class="fw-bold text-black">Note: </span>
                                        <p class="fw-bold">
                                            Hangman game is played by selecting the correct
                                            alphabet symbol based on the object image and text presented. If the selected
                                            alphabet sign did not match to the object name, it will be considered wrong.
                                        </p>
                                        <h6 class="fw-bold text-black">Legend: </h6>
                                        <ul>
                                            <li class="fw-bold">Wrong: <span style="background-color: #f8d7da;"
                                                    class="p-1 text-dark fw-bold">Red Background</span></li>
                                            <li class="fw-bold">Correct: <span style="background-color: #d1e7dd;"
                                                    class="p-1 text-dark fw-bold">Green Background</span></li>
                                        </ul>
                                    </div>
                                    <h2 class="accordion-header" id="headingOne">
                                        <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                            Quiz Answers/Responses
                                        </button>
                                    </h2>
                                    <div id="collapseOne" class="accordion-collapse collapse show"
                                        aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <template v-if="Object.keys(weaknessesData).length">
                                                <div v-for="(item, index) in Object.values(weaknessesData.data)"
                                                    :key="index" class="card mb-1">
                                                    <div class="card-header">
                                                        No. {{ index + 1 }}
                                                    </div>
                                                    <div class="card-body">
                                                        <template v-if="flag === 'alphabet-words'">
                                                            <table class="table table-bordered table-responsive">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width: 20%">Object Image</th>
                                                                        <th style="width: 20%">Object Name</th>
                                                                        <th style="width: 20%">Student Answer</th>
                                                                        <th style="width: 20%">Answer Image</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody v-if="!item.length">
                                                                    <tr>
                                                                        <td colspan="4" class="text-center fw-bold">No data
                                                                            found</td>
                                                                    </tr>
                                                                </tbody>
                                                                <tbody v-else>
                                                                    <tr :class="item2.attributes.mark === 'wrong' ? 'table-danger' : 'table-success'"
                                                                        class="fw-bold" v-for="item2 in item"
                                                                        :key="item2.id">
                                                                        <td>
                                                                            <img width="100"
                                                                                :src="item2.attributes.object_image">
                                                                        </td>
                                                                        <td>{{ item2.attributes.object_text }}</td>
                                                                        <td>{{ item2.attributes.answer }}</td>
                                                                        <td>
                                                                            <img width="100"
                                                                                :src="item2.attributes.answer_image">
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </template>
                                                        <template v-else-if="flag === 'alphabet-letters'">
                                                            <table class="table table-bordered table-responsive">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width: 20%">Alphabet</th>
                                                                        <th style="width: 20%">Object Image</th>
                                                                        <th style="width: 20%">Object Name</th>
                                                                        <th style="width: 20%">Student Answer</th>
                                                                        <th style="width: 20%">Answer Image</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody v-if="!item.length">
                                                                    <tr>
                                                                        <td colspan="4" class="text-center fw-bold">No data
                                                                            found</td>
                                                                    </tr>
                                                                </tbody>
                                                                <tbody v-else>
                                                                    <tr :class="item2.attributes.mark === 'wrong' ? 'table-danger' : 'table-success'"
                                                                        class="fw-bold" v-for="item2 in item"
                                                                        :key="item2.id">
                                                                        <td>{{ item2.attributes.alphabet }}</td>
                                                                        <td>
                                                                            <img width="100"
                                                                                :src="item2.attributes.object_image">
                                                                        </td>
                                                                        <td>{{ item2.attributes.object }}</td>
                                                                        <td>{{ item2.attributes.answer }}</td>
                                                                        <td>
                                                                            <img width="100"
                                                                                :src="item2.attributes.answer_image">
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </template>
                                                        <template v-else>
                                                            <table class="table table-bordered table-responsive">
                                                                <thead>
                                                                    <tr>
                                                                        <!-- <th style="width: 5%">No.</th>
                                                                        <th style="width: 75%">Selections/To Guess</th> -->
                                                                        <th style="width: 20%">Clicked Key/Alphabet</th>
                                                                        <th style="width: 20%">Key/Alphabet Image</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody v-if="!item.length">
                                                                    <tr>
                                                                        <td colspan="4" class="text-center fw-bold">No data
                                                                            found</td>
                                                                    </tr>
                                                                </tbody>
                                                                <tbody v-else>
                                                                    <tr :class="item2.attributes.mark === 'wrong' ? 'table-danger' : 'table-success'"
                                                                        class="fw-bold" v-for="item2 in item"
                                                                        :key="item2.id">
                                                                        <!-- <td>{{ index+1 }}</td> -->
                                                                        <!-- <td>
                                                                            <div class="row">
                                                                                <div v-for="option in item2.attributes.options" :key="option.letter" class="col-1">
                                                                                    <img style="width: 50px" :src="option.image" :alt="option.letter">
                                                                                    <p class="text-center">{{ option.letter }}</p>
                                                                                </div>
                                                                            </div>
                                                                        </td> -->
                                                                        <td>{{ item2.attributes.answer }}</td>
                                                                        <td>
                                                                            <img style="width: 50px"
                                                                                :src="item2.attributes.answer_image">
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>
                                            <div v-else>
                                                <p class="text-center">No record to display.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingThree">
                                        <button class="accordion-button collapsed fw-bold" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false"
                                            aria-controls="collapseThree">
                                            Answer Keys
                                        </button>
                                    </h2>
                                    <div id="collapseThree" class="accordion-collapse collapse"
                                        aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <template v-if="Object.keys(weaknessesData).length">
                                                <template v-if="flag === 'alphabet-letters'">
                                                    <table class="table table-bordered table-responsive">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 20%">Object Image</th>
                                                                <th style="width: 20%">Object Name</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody v-if="!weaknessesData.answer_key.length">
                                                            <tr>
                                                                <td colspan="2" class="text-center fw-bold">No data found
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                        <tbody v-else>
                                                            <tr class="fw-bold"
                                                                v-for="(item, index) in weaknessesData.answer_key"
                                                                :key="index">
                                                                <td>
                                                                    <img width="100" :src="item.image">
                                                                </td>
                                                                <td>{{ item.word.toUpperCase() }}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </template>
                                                <template v-else>
                                                    <table class="table table-bordered table-responsive">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 20%">Alphabet</th>
                                                                <th style="width: 20%">Image</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody v-if="!weaknessesData.answer_key.length">
                                                            <tr>
                                                                <td colspan="2" class="text-center fw-bold">No data found
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                        <tbody v-else>
                                                            <tr class="fw-bold"
                                                                v-for="(item, index) in weaknessesData.answer_key"
                                                                :key="index">
                                                                <td>{{ item.letter.toUpperCase() }}</td>
                                                                <td>
                                                                    <img width="100" :src="item.image">
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </template>
                                            </template>
                                            <template v-else>
                                                <div>
                                                    <p class="text-center">No record to display</p>
                                                </div>
                                            </template>
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
import swal from 'sweetalert2'
import Loading from '../../loading/Loading.vue'
export default {
    props: ['gameName', 'studentId', 'flag', 'reports', 'quizReportHighestScore', 'quizReportRetake'],
    components: {
        Loading
    },
    data() {
        return {
            indexes: {
                skillTest: 'a'
            },
            setScore: {
                score: 0,
                skill_test_id: null
            },
            setMark: {
                mark: null,
                skill_test_id: null
            },
            passingPercentage: [],
            certificates: [],
            errors: [],
            average: 0,
            isEditScore: false,
            isEditPerfectScore: false,
            isLoading: false,
            isProcessing: false,
            weaknessesDataLoading: false,
            skillTestVideoUrl: null,
            weaknessesData: {},
            skillTest: {},
            newScore: {},
            allowedskillTestRetake: [],
            allowedSkillTestAttempt: {},
            allowedQuizAttempt: {},
            highestScore: [],
            skillTestRetake: [],
            showSkillTestRetakeModify: {},
            showQuizRetakeModify: {},
            skillTestMainRetake: {},
            selectAllSkillTestRetake: false,
            certificate: {
                file: null
            },
            selectedPercentage: {
                skill_test: null,
                quiz: null
            },
            percentages: [
                { name: '50%', value: 50 },
                { name: '60%', value: 60 },
                { name: '75%', value: 75 },
                { name: '80%', value: 80 }
            ]
        }
    },
    created() {
        this.getSkillTest()
        this.getCertificates()
        this.getPassingPercentage()
    },
    watch: {
        async flag() {
            this.skillTest = {}
            this.isLoading = true
            this.showSkillTestRetakeModify = {}
            this.showQuizRetakeModify = {}
            this.weaknessesData = {}
            this.getSkillTest()
            this.getCertificates()
            this.getPassingPercentage()
        }
    },
    computed: {
        allAphabets() {
            return Array.from({ length: 26 }, (_, i) => String.fromCharCode(97 + i));
        },
        isReportsLoading() {
            return this.$parent.isLoading
        },
        gameId() {
            return this.$parent.gameId.toString()
        },
        skillTestAverageScore() {
            if (Object.keys(this.highestScore).length) {
                const scores = Object.values(this.highestScore);
                const totalScore = scores.reduce(function (a, b) { return a + b; }, 0);
                const count = scores.length

                return {
                    score: totalScore / count,
                    display: `${totalScore / count} / ${this.$parent.perfect_score.score}`
                };
            }
            return {
                score: 0,
                display: 0
            };
        },
        filteredFlag() {
            return this.flag.replace(/-/g, "_");
        },
        quizAverageScore() {
            if (Object.keys(this.quizReportHighestScore).length) {
                return {
                    score: this.quizReportHighestScore[this.gameId],
                    display: `${this.quizReportHighestScore[this.gameId]} / ${this.reports[0].perfect_score}`
                };
            }
            return {
                score: 0,
                display: 0
            };
        }
    },
    methods: {
        selectAll() {
            if (this.selectAllSkillTestRetake) {
                const retakes = this.skillTestRetake

                this.allowedskillTestRetake = Object.keys(retakes)
            } else {
                this.allowedskillTestRetake = []
            }
        },
        async getSkillTest() {
            this.isLoading = true
            this.highestScore = []
            try {
                const data = await axios.get(`/api/skill-test/fetch/${this.flag}?student_id=${this.studentId}`)
                const mergedObject = data.data.data.reduce((result, item) => {
                    const { letter, ...rest } = item;

                    if (result[letter]) {
                        result[letter].push({ ...rest, letter: item.letter, is_display: false, error_message: false });
                    } else {
                        result[letter] = [{ ...rest, letter: item.letter, is_display: false, error_message: false }];
                    }

                    return result;
                }, {});

                this.skillTest = mergedObject
                if (Object.keys(this.skillTest).length) {
                    this.highestScore = data.data.highest_score ?? 0
                    this.skillTestRetake = data.data.retake ?? []
                    this.skillTestMainRetake = data.data.main_retake ?? {}
                }

                this.indexes.skillTest = Object.keys(mergedObject)[0]
            } catch (error) {
                console.log(error)
            } finally {
                this.isLoading = false
            }
        },
        async getCertificates() {
            this.isLoading = true
            try {
                const response = await axios.get(`/api/certificates/student/${this.studentId}/game-id/${this.gameId}`)
                this.certificates = response.data
            } catch (error) {
                console.log(error)
            } finally {
                this.isLoading = false
            }
        },
        parseFile(event) {
            this.file = event.target.files[0];
        },
        async uploadCertificate() {
            try {
                this.isProcessing = true
                this.errors = []
                let formData = new FormData();
                formData.append("gameId", this.gameId);
                formData.append("studentId", this.studentId);
                formData.append("file", this.file);
                const response = await axios.post('/api/certificate/upload', formData);
                if (response.status) {
                    this.file = null
                    this.$refs.fileInput.value = '';
                    swal.fire('Success', response.data.message, 'success')
                    this.getCertificates()
                }
            } catch (error) {
                this.errors = error.response.data.errors;
                // Handle the error, e.g., show an error message
            } finally {
                this.isProcessing = false
            }
        },
        async deleteCertificate(id, filePath) {
            const data = { id: id, file: filePath }
            try {
                this.isProcessing = true
                const response = await axios.post('/api/certificate/delete', data)
                if (response.status) {
                    swal.fire('Success', response.data.message, 'success')
                    this.getCertificates()
                }
            } catch (error) {
                console.log(error)
            } finally {
                this.isProcessing = false
            }
        },
        allowQuiz() {
            this.showQuizRetakeModify[this.filteredFlag] = true
            this.allowedQuizAttempt[this.filteredFlag] = this.quizReportRetake[this.filteredFlag].allowed_retake
        },
        allowSkillTest() {
            this.showSkillTestRetakeModify[this.filteredFlag] = true
            this.allowedSkillTestAttempt[this.filteredFlag] = 0
            this.allowedskillTestRetake = []
        },
        async updateScore(data, index) {
            this.setScore.score = this.newScore[data.letter]
            this.setScore.skill_test_id = data.id

            if (this.setScore.score > data.perfect_score) {
                this.skillTest[data.letter][index].error_message = true
                return
            }

            try {
                const response = await axios.post(`/api/skill-test-score/modify`, this.setScore)
                this.getSkillTest()
                swal.fire('Success', response.data.message, 'success')
                this.skillTest[data.letter][index].error_message = false
            } catch (error) {
                console.log(error)
            }
        },
        async markSkillTest(data, mark) {
            try {
                this.setMark.mark = mark
                this.setMark.skill_test_id = data.id
                const response = await axios.post(`/api/skill-test-mark/modify`, this.setMark)
                this.getSkillTest()
                swal.fire('Success', response.data.message, 'success')
            } catch (error) {
                console.log(error)
            }
        },
        upperCaseFirstLetter(text) {
            return text.charAt(0).toUpperCase() + text.slice(1)
        },
        playSkillTestVideo(video_url) {
            this.skillTestVideoUrl = video_url
            $('#skillTestVideoModal').modal('show')
        },
        async viewWeaknesses() {
            try {
                $('#weaknessModal').modal('show')
                let gameFlag = null;
                switch (this.flag) {
                    case 'alphabet-words':
                        gameFlag = 'hangman-game'
                        break;
                    case 'alphabet-letters':
                        gameFlag = 'memory-game'
                        break;
                    case 'vowel-consonants':
                        gameFlag = 'typing-balloon'
                        break;
                }

                this.weaknessesDataLoading = true
                const response = await axios.get(`/api/get-mistakes/${this.studentId}?game_flag=${gameFlag}`)
                if (response.status == 200 && response.data.data.length) {
                    const groupedObjects = response.data.data.reduce((groups, obj) => {
                        const key = obj.quiz_report_id;

                        if (!groups[key]) {
                            groups[key] = [];
                        }

                        groups[key].push(obj);

                        return groups;
                    }, {});
                    this.weaknessesData.data = groupedObjects
                    this.weaknessesData.answer_key = response.data.answer_key
                }
            } catch (error) {
                console.log(error)
            } finally {
                this.weaknessesDataLoading = false
            }
        },
        // async allowRetakeSkillTest(data, item) {
        //     try {
        //         const response = await axios.put(`/api/retake/skill-test/allow/${data.id}`, { allowed_retake: this.allowedSkillTestAttempt[item] })
        //         swal.fire('Success', response.data.message, 'success')
        //         this.getSkillTest()
        //         this.showSkillTestRetakeModify[item] = false
        //     } catch (error) {
        //         console.log(error)
        //     }
        // },
        async allowRetakeSkillTest() {
            try {
                const response = await axios.put(`/api/retake/skill-test/allow/${this.studentId}?flag=${this.flag}`, { allowed_retake: this.allowedSkillTestAttempt[this.filteredFlag], alphabets: this.allowedskillTestRetake })
                swal.fire('Success', response.data.message, 'success')
                this.getSkillTest()
            } catch (error) {
                console.log(error)
            }
        },
        async allowRetakeQuiz(data, item) {
            try {
                const response = await axios.put(`/api/retake/quiz/allow/${data.id}`, { allowed_retake: this.allowedQuizAttempt[item] })
                swal.fire('Success', response.data.message, 'success')
                this.$parent.getReports()
                this.showQuizRetakeModify[item] = false
            } catch (error) {
                console.log(error)
            }
        },
        async getPassingPercentage() {
            try {
                const response = await axios.get(`/api/passing-percentage/get`)
                this.passingPercentage = response.data
                this.selectedPercentage.skill_test = response.data['skill_test'].percentage
                this.selectedPercentage.quiz = response.data['quiz'].percentage
            } catch (error) {
                console.log(error)
            }
        },
        async updatePassingPercentage(flag) {
            setTimeout(async () => {
                try {
                    await axios.put(`/api/passing-percentage/update/${this.passingPercentage[flag].id}`, { percentage: this.selectedPercentage[flag] })
                } catch (error) {
                    console.log(error)
                }
            }, 500)
        },
        calculatePercentage(score, perfectScore = 10) {
            return Math.round((score / perfectScore) * 100)
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

.zoom-button {
    transition: transform .2s;
    /* Animation */
}

.zoom-button:hover {
    transform: scale(1.1);
    /* (150% zoom - Note: if the zoom is too large, it will go outside of the viewport) */
}</style>
