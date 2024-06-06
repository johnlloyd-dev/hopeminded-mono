import './bootstrap';
import { createApp } from 'vue';

import { store } from './store';

import router from './router'
import 'animate.css';

const app = createApp({});

import Footer from './components/Footer.vue';
app.component('footer-component', Footer);

import StudentSidebar from './components/student/Sidebar.vue';
app.component('student-sidebar-component', StudentSidebar);

import TeacherSidebar from './components/teacher/Sidebar.vue';
app.component('teacher-sidebar-component', TeacherSidebar);

import AdminSidebar from './components/admin/Sidebar.vue';
app.component('admin-sidebar-component', AdminSidebar);

import Loading from './components/Loading.vue';
app.component('loading-component', Loading);

// import AlphabetLetters from './components/teacher/layouts/AlphabetLetters.vue';
// app.component('alphabet-letters-component', AlphabetLetters);

// import AlphabetWords from './components/teacher/layouts/AlphabetWords.vue';
// app.component('alphabet-words-component', AlphabetWords);

// import VowelConsonants from './components/teacher/layouts/VowelConsonants.vue';
// app.component('vowel-consonants-component', VowelConsonants);

import PasswordResetForm from './components/PasswordResetForm.vue';
app.component('password-reset-component', PasswordResetForm);

import VueSidebarMenu from 'vue-sidebar-menu'
import 'vue-sidebar-menu/dist/vue-sidebar-menu.css'

import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

app.use(VueSweetalert2);

app.use(VueSidebarMenu)

app.use(store)
app.use(router)

app.mount('#app');
