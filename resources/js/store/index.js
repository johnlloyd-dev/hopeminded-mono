import { createStore } from "vuex";
export const store = new createStore({
    namespaced: true,
    state: {
        authenticated: false,
        user: {},
        menuFlag: null,
        alphabetLetters: {},
        vowelConsonants: {},
        alphabetWords: {},
        quizInfo: {},
        selectedChapter: null,
        isLoading: false
    },
    getters: {
        authenticated(state) {
            return state.authenticated;
        },
        user(state) {
            return state.user;
        },
        menuFlag(state) {
            return state.menuFlag;
        },
        alphabetLetters(state) {
            return state.alphabetLetters;
        },
        vowelConsonants(state) {
            return state.vowelConsonants;
        },
        alphabetWords(state) {
            return state.alphabetWords;
        },
        quizInfo(state) {
            return state.quizInfo;
        },
        selectedChapter(state) {
            return state.selectedChapter;
        },
        isLoading(state) {
            return state.isLoading
        }
    },
    mutations: {
        SET_AUTHENTICATED(state, value) {
            state.authenticated = value;
        },
        SET_USER(state, value) {
            state.user = value;
        },
        SET_MENU_FLAG(state, menuFlag) {
            state.menuFlag = menuFlag;
        },
        SET_ALPHABET_LETTERS_DATA(state, data) {
            state.alphabetLetters = data;
        },
        SET_VOWEL_CONSONANTS_DATA(state, data) {
            state.vowelConsonants = data;
        },
        SET_ALPHABET_WORDS_DATA(state, data) {
            state.alphabetWords = data;
        },
        SET_QUIZ_INFO(state, data) {
            state.quizInfo = data;
        },
        SET_SELECTED_CHAPTER(state, data) {
            state.selectedChapter = data;
        },
        SET_ISLOADING(state, data) {
            state.isLoading = data
        }
    },
    actions: {
        login(context, { users }) {
            context.commit("SET_USER", users);
            context.commit("SET_AUTHENTICATED", true);
        },
        logout(context) {
            context.commit("SET_USER", {});
            context.commit("SET_AUTHENTICATED", false);
        },
        setMenuFlag(context, menuFlag) {
            context.commit("SET_MENU_FLAG", menuFlag);
        },
        async setAlphabetLetters(context, flag) {
            try {
                context.commit("SET_ISLOADING", true);
                const alphabets = await axios.get(`/api/alphabets-letters/get?user=${'student'}&chapter=1`);
                // const flags = await axios.get(`/api/flags/alphabet-letters`);
                // for (let i = 0; i <= alphabets.data.length - 1; i++) {
                //     const letter1 = alphabets.data[i].letter;
                //     const letter2 = JSON.parse(flags.data.attributes);
                //     alphabets.data[i]["isDone"] = letter2[letter1];
                // }
                context.commit("SET_ALPHABET_LETTERS_DATA", alphabets.data);
            } catch (error) {
                console.log(error)
            } finally {
                context.commit("SET_ISLOADING", false);
            }
        },
        async setVowelConsonants(context, flag) {
            try {
                context.commit("SET_ISLOADING", true);
                let newData = []
                const alphabets = await axios.get(`/api/vowels-consonants/get?user=${'student'}&chapter=1`);
                // const flags = await axios.get(`/api/flags/vowel-consonants`);
                // alphabets.data.forEach((element) => {
                //     for (let i = 0; i <= element.length - 1; i++) {
                //         const letter1 = element[i].letter;
                //         const letter2 = JSON.parse(flags.data.attributes);
                //         element[i]["isDone"] = letter2[letter1];
                //     }
                //     newData.push(element);
                // });
                context.commit("SET_VOWEL_CONSONANTS_DATA", alphabets.data);
            } catch (error) {
                console.log(error)
            } finally {
                context.commit("SET_ISLOADING", false);
            }
        },
        async setAlphabetWords(context) {
            try {
                context.commit("SET_ISLOADING", true);
                let alphabets = await axios.get(
                    `/api/alphabets-words/get?user=${'student'}&chapter=1`
                );
                let newData = []
                alphabets.data.forEach(element => {
                    var variableName = element.letter;
                    var value = element.attributes;

                    var obj = {};
                    obj[variableName] = value;
                    newData.push(obj);
                });
                // let flags = await axios.get(`/api/flags/alphabet-words`);
                // let data = [];
                // let attributes = JSON.parse(flags.data.attributes);
                // newData.forEach(function (element, index) {
                //     let key = Object.keys(element);
                //     let flagData = attributes[key];
                //     Object.values(element).forEach((element) => {
                //         element.forEach((element2, index) => {
                //             if (flagData.includes(index)) {
                //                 element2.isDone = true;
                //             } else {
                //                 element2.isDone = false;
                //             }
                //             element[index] = element2;
                //         });
                //         let obj = {};
                //         obj[key] = element; // Using computed property names
                //         data.push(obj);
                //     });
                // });
                context.commit("SET_ALPHABET_WORDS_DATA", newData);
            } catch (error) {
                console.log(error)
            } finally {
                context.commit("SET_ISLOADING", false);
            }
        },
        async getQuizInfo(context, tutorial) {
            let data = [];
            if(tutorial) {
                data = await axios.get("/api/quiz/info/get?flag=tutorial");
            } else {
                data = await axios.get("/api/quiz/info/get");
            }
            context.commit("SET_QUIZ_INFO", data.data);
        },
        async setSelectedChapter(context, chapter) {
            context.commit("SET_SELECTED_CHAPTER", chapter);
        },
    },
});
