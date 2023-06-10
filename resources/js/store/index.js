import { createStore } from "vuex";
let alphabets = [];
let flags = [];
let data = [];
let newData = [];
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
            const alphabets = await axios.get(`/api/alphabets-letters/get?user=${'student'}`);
            const flags = await axios.get(`/api/flags/alphabet-letters`);
            let data = [];
            for (let i = 0; i <= alphabets.data.length - 1; i++) {
                const letter1 = alphabets.data[i].letter;
                const letter2 = JSON.parse(flags.data.attributes);
                alphabets.data[i]["isDone"] = letter2[letter1];
            }
            for (let i = 0; i < alphabets.data.length; i += 5) {
                if (i <= 15) {
                    const chunk = alphabets.data.slice(i, i + 5);
                    data.push(chunk);
                } else {
                    const chunk = alphabets.data.slice(i, i + 6);
                    data.push(chunk);
                    break;
                }
            }
            context.commit("SET_ALPHABET_LETTERS_DATA", data);
        },
        async setVowelConsonants(context, { isConsonant, pageFlag, counter }) {
            if (counter == 0) {
                alphabets = [];
                flags = [];
                data = [];
                newData = [];
                alphabets = await axios.get(`/api/vowels-consonants/get?user=${'student'}`);
                flags = await axios.get(`/api/flags/vowel-consonants`);
            }
            alphabets.data.forEach((element) => {
                for (let i = 0; i <= element.length - 1; i++) {
                    const letter1 = element[i].letter;
                    const letter2 = JSON.parse(flags.data.attributes);
                    element[i]["isDone"] = letter2[letter1];
                }
                newData.push(element);
            });
            if (isConsonant) {
                data = [];
                for (let i = 0; i < newData[1].length; i += 5) {
                    if (i <= 10) {
                        const chunk = newData[1].slice(i, i + 5);
                        data.push(chunk);
                    } else {
                        const chunk = newData[1].slice(i, i + 6);
                        data.push(chunk);
                        break;
                    }
                }
                data = data[pageFlag];
            } else {
                data = [];
                data.push(newData[0]);
                data = data[0];
            }
            context.commit("SET_VOWEL_CONSONANTS_DATA", data);
        },
        async setAlphabetWords(context) {
            let alphabets = await axios.get(
                `/api/alphabets-words/get?user=${'student'}`
            );
            let newData = []
            alphabets.data.forEach(element => {
                var variableName = element.letter;
                var value = element.attributes;

                var obj = {};
                obj[variableName] = value;
                newData.push(obj);
            });
            let flags = await axios.get(`/api/flags/alphabet-words`);
            let data = [];
            let attributes = JSON.parse(flags.data.attributes);
            newData.forEach(function (element, index) {
                let key = Object.keys(element);
                let flagData = attributes[key];
                Object.values(element).forEach((element) => {
                    element.forEach((element2, index) => {
                        if (flagData.includes(index)) {
                            element2.isDone = true;
                        } else {
                            element2.isDone = false;
                        }
                        element[index] = element2;
                    });
                    let obj = {};
                    obj[key] = element; // Using computed property names
                    data.push(obj);
                });
            });
            context.commit("SET_ALPHABET_WORDS_DATA", data);
        },
        async getQuizInfo(context) {
            const data = await axios.get("/api/quiz/info/get");
            context.commit("SET_QUIZ_INFO", data.data);
        },
        async setSelectedChapter(context, chapter) {
            context.commit("SET_SELECTED_CHAPTER", chapter);
        },
    },
});
