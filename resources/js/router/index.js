import { createWebHistory, createRouter } from "vue-router";
import { store } from "../store";

const home = () => import("../components/Home.vue");
const mainAuth = () => import("../components/MainAuth.vue");
const notFound = () => import("../components/NotFound.vue");
const studentDashboard = () => import("../components/student/main/Dashboard.vue");
const teacherDashboard = () => import("../components/teacher/main/Dashboard.vue");

const studentTextbook = () => import("../components/student/main/Textbook.vue");
const studentReport = () => import("../components/student/main/Report.vue");
const certificates = () => import("../components/student/main/Certificates.vue");
const contact = () => import("../components/student/main/Contact.vue");
const review = () => import("../components/student/main/Review.vue");

const studentLogin = () => import("../components/student/auth/Login.vue");
const teacherLogin = () => import("../components/teacher/auth/Login.vue");
const studentRegister = () => import("../components/student/auth/Register.vue");
const teacherRegister = () => import("../components/student/auth/Register.vue");
const adminLogin = () => import("../components/admin/auth/Login.vue");

const hangmanGame = () => import("../components/student/game/Hangman.vue");
const quizHangmanGame = () => import("../components/student/game/quiz/Hangman.vue");

const typingBalloon = () => import("../components/student/game/Balloon.vue");
const quizTypingBalloon = () => import("../components/student/game/quiz/Balloon.vue");

const memoryGame = () => import("../components/student/game/Memory.vue");
const quizMemoryGame = () => import("../components/student/game/quiz/Memory.vue");

const alphabetByLetters = () => import("../components/student/textbook/AlphabetLetters.vue");
const alphabetByWords = () => import("../components/student/textbook/AlphabetWords.vue");
const vowelConsonants = () => import("../components/student/textbook/VowelConsonants.vue");

const studentManagement = () => import("../components/teacher/main/StudentManagement.vue");
const sectionManagement = () => import("../components/teacher/main/SectionManagement.vue");
const textbookManagement = () => import("../components/teacher/main/TextbookManagement.vue");
const activityManagement = () => import("../components/teacher/main/ActivityManagement.vue");
const configuration = () => import("../components/teacher/main/Configuration.vue");
const studentsView = () => import("../components/admin/layouts/Students.vue");

const teacherManagement = () => import("../components/admin/main/TeacherManagement.vue");
const quizReport = () => import("../components/student/reports/QuizReports.vue");
const studentQuizReport = () => import("../components/teacher/reports/Reports.vue");
const comingSoon = () => import("../components/student/textbook/ComingSoon.vue");
const viewTextbook = () => import("../components/teacher/layouts/Textbook.vue");

const editProfile = () => import("../components/student/layouts/EditProfile.vue");

const studentSection = () => import("../components/teacher/layouts/StudentSection.vue");

const routes = [
    {
        name: "main-auth",
        path: "/",
        component: mainAuth,
        meta: {
            title: `Main Auth Page`,
        },
    },
    {
         path: '/:pathMatch(.*)*',
        component: notFound
    },
    {
        name: "student-dashboard",
        path: "/student-dashboard",
        component: studentDashboard,
        meta: {
            title: `Student Dashboard`,
        },
    },
    {
        name: "teacher-dashboard",
        path: "/teacher-dashboard",
        component: teacherDashboard,
        meta: {
            title: `Teacher Dashboard`,
        },
    },
    {
        name: "student-textbook",
        path: "/student-textbook",
        component: studentTextbook,
        meta: {
            title: `Student Textbook`,
        },
    },
    {
        name: "student-report",
        path: "/student-report",
        component: studentReport,
        meta: {
            title: `Student Report`,
        },
    },
    {
        name: "contact-us",
        path: "/contact-us",
        component: contact,
        meta: {
            title: `Contact Us`,
        },
    },
    {
        name: "student-certificates",
        path: "/student-certificates",
        component: certificates,
        meta: {
            title: `Certificates`,
        },
    },
    {
        name: "review-us",
        path: "/review-us",
        component: review,
        meta: {
            title: `Review Us`,
        },
    },
    {
        name: "student-login",
        path: "/student-login",
        component: studentLogin,
        meta: {
            title: `Student Login`,
        },
    },
    {
        name: "teacher-login",
        path: "/teacher-login",
        component: teacherLogin,
        meta: {
            title: `Teacher Login`,
        },
    },
    {
        name: "admin-login",
        path: "/admin-login",
        component: adminLogin,
        meta: {
            title: `Admin Login`,
        },
    },
    {
        name: "teacher-register",
        path: "/teacher-register",
        component: teacherRegister,
        meta: {
            title: `Teacher Register`,
        },
    },
    {
        name: "student-register",
        path: "/student-register",
        component: studentRegister,
        meta: {
            title: `Student Register`,
        },
    },
    {
        name: "hangman-game",
        path: "/hangman-game",
        component: hangmanGame,
        meta: {
            title: `Hangman Game`,
        },
    },
    {
        name: "quiz-hangman-game",
        path: "/quiz-hangman-game",
        component: quizHangmanGame,
        meta: {
            title: `Quiz Hangman Game`,
        },
    },
    {
        name: "typing-balloon",
        path: "/typing-balloon",
        component: typingBalloon,
        meta: {
            title: `Typing Balloon`,
        },
    },
    {
        name: "quiz-typing-balloon",
        path: "/quiz-typing-balloon",
        component: quizTypingBalloon,
        meta: {
            title: `Quiz Typing Balloon`,
        },
    },
    {
        name: "quiz-memory-game",
        path: "/quiz-memory-game",
        component: quizMemoryGame,
        meta: {
            title: `Quiz Memory Game`,
        },
    },
    {
        name: "memory-game",
        path: "/memory-game",
        component: memoryGame,
        meta: {
            title: `Memory Game`,
        },
    },
    {
        name: "alphabet-by-letters",
        path: "/alphabet-by-letters",
        component: alphabetByLetters,
        meta: {
            title: `Alphabet By Letters`,
        },
    },
    {
        name: "alphabet-by-words",
        path: "/alphabet-by-words",
        component: alphabetByWords,
        meta: {
            title: `Alphabet By Words`,
        },
    },
    {
        name: "vowel-consonants",
        path: "/vowel-consonants",
        component: vowelConsonants,
        meta: {
            title: `Vowel and Consonants`,
        },
    },
    {
        name: "student-management",
        path: "/student-management",
        component: studentManagement,
        meta: {
            title: `Student Management`,
        },
    },
    {
        name: "textbook-management",
        path: "/textbook-management",
        component: textbookManagement,
        meta: {
            title: `Textbook Management`,
        },
    },
    {
        name: "section-management",
        path: "/section-management",
        component: sectionManagement,
        meta: {
            title: `Section Management`,
        },
    },
    {
        name: "view-textbook-management",
        path: "/textbook-management/:textbookFlag",
        component: viewTextbook,
        meta: {
            title: `Textbook Management`,
        },
    },
    {
        name: "activity-management",
        path: "/activity-management",
        component: activityManagement,
        meta: {
            title: `Activity Management`,
        },
    },
    {
        name: "configuration",
        path: "/configuration",
        component: configuration,
        meta: {
            title: `Configuration`,
        },
    },
    {
        name: "quiz-report",
        path: "/quiz-report/:gameId",
        component: quizReport,
        meta: {
            title: `Quiz Report`,
        },
    },
    {
        name: "coming-soon",
        path: "/coming-soon",
        component: comingSoon,
        meta: {
            title: `Coming Soon`,
        },
    },
    {
        name: "teacher-management",
        path: "/teacher-management",
        component: teacherManagement,
        meta: {
            title: `Teacher Management`,
        },
    },
    {
        name: "student-view",
        path: "/teacher-management/:teacherId",
        component: studentsView,
        meta: {
            title: `View Students`,
        },
    },
    {
        name: "student-quiz-report",
        path: "/student-quiz-report/:studentId",
        component: studentQuizReport,
        meta: {
            title: `StudentQuizReport`,
        },
    },
    {
        name: "edit-profile",
        path: "/student/edit-profile",
        component: editProfile,
        meta: {
            title: `Edit Profile`,
        },
    },
    {
        name: "student-section",
        path: "/section/:sectionId",
        component: studentSection,
        meta: {
            title: `Section`,
        },
    },
];
const router = createRouter({
    history: createWebHistory(),
    routes,
});

// function isLoggedIn() {
//     return localStorage.getItem("auth");
// }

// router.beforeEach((to, from, next) => {
//     if (to.matched.some(record => record.meta.authOnly)) {
//         if (!isLoggedIn()) {
//             next({
//                 path: "/student-login",
//                 query: { redirect: to.fullPath }
//             });
//         } else {
//             next();
//         }
//     } else if (to.matched.some(record => record.meta.guestOnly)) {
//         if (isLoggedIn()) {
//             next({
//                 path: "/student-dashboard",
//                 query: { redirect: to.fullPath }
//             });
//         } else {
//             next();
//         }
//     } else {
//         next(); // make sure to always call next()!
//     }
// });

export default router;
