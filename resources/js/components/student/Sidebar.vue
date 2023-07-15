<template>
    <sidebar-menu :menu="menu" :theme="theme" :collapsed="collapsed" @update:collapsed="onCollapse" @item-click="onItemClick">
        <template v-slot:header>
            <div :class="collapsed ? 'd-none' : 'visibility-visible'" class="d-flex align-items-center justify-content-center flex-column" style="height: 200px;">
                <div class="profile-image" :class="{ 'mb-3' : !collapsed }">
                    <img src="/images/user-icon.png" alt="User profile image">
                </div>
                <a class="fw-bold mb-3" type="button" @click="editProfile">Edit Profile</a>
                <h5 class="username">{{ fullname }}</h5>
            </div>
            <hr class="border my-0">
        </template>
        <template v-slot:footer>
            <div class="d-flex justify-content-center align-items-center mb-5">
                <button @click="logout" class="btn">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Logout</span>
                </button>
            </div>
        </template>
    </sidebar-menu>
</template>

<script>
export default {
    data() {
        return {
            user: [],
            collapsed: false,
            menu: [
                {
                    href: '/student-dashboard',
                    title: 'Dashboard',
                    icon: 'fa-solid fa-gauge',
                },
                {
                    href: '/student-textbook',
                    title: 'Textbook',
                    icon: 'fa-solid fa-book-open-reader',
                },
                // {
                //     href: '/student-quiz',
                //     title: 'Quiz',
                //     icon: 'fa-solid fa-pen-to-square',
                // },
                {
                    href: '/student-report',
                    title: 'Report',
                    icon: 'fa-solid fa-flag',
                },
                {
                    href: '/contact-us',
                    title: 'Contact Us',
                    icon: 'fa-solid fa-address-book',
                },
            ],
            theme: 'white-theme'
        }
    },
    computed: {
        fullname() {
            return localStorage.getItem('fullname')
        },
    },
    methods: {
        onCollapse(collapsed) {
            if(!collapsed) {
                setTimeout(() => {
                    this.collapsed = collapsed
                }, 100)
            } else {
                this.collapsed = collapsed
            }
            this.$emit('collapse', collapsed)
        },
        onItemClick(event, item) {
            window.localStorage.setItem('menuFlag', item.title.toLowerCase())
        },
        editProfile() {
            this.$router.push('/student/edit-profile')
        },
        async logout(){
            await axios.post('api/logout')
            .then(()=>{
                localStorage.removeItem('fullname')
                this.$router.push({path: '/'})
            })
        }
    },
}
</script>

<style scoped>
.profile-image {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    overflow: hidden;
}

.profile-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.username {
    font-weight: bold !important;
}

.border-black {
    border-color: black !important;
    width: 75% !important;
}

.visibility-visible {
    visibility: visible !important;
}

.visibility-hidden {
    visibility: hidden !important
}
</style>
