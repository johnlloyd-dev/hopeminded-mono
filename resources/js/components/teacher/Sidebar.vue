<template>
    <sidebar-menu :menu="menu" :theme="theme" :collapsed="collapsed" @update:collapsed="onCollapse" @item-click="onItemClick">
        <template v-slot:header>
            <div class="d-flex align-items-center justify-content-center flex-column pt-3 position-relative" style="height: 110px;">
                <button data-bs-toggle="offcanvas" data-bs-target="#notificationPanel" aria-controls="notificationPanel" style="right: 20px" type="button" class="icon-button mt-3 position-absolute top-0">
                    <i class="far fa-bell fa-lg"></i>
                    <span v-if="notifications.length" class="icon-button__badge">{{ notifications.length ?? '' }}</span>
                </button>
                <div class="profile-image" :class="{ 'mb-3' : !collapsed }">
                    <img src="/images/user-icon.png" alt="User profile image">
                </div>
                <h5 :class="collapsed ? 'd-none' : 'visibility-visible'" class="username">{{ fullname }}</h5>
            </div>
            <hr class="border my-0">
            <!-- Notifications Panel-->
            <div class="offcanvas offcanvas-start" tabindex="-1" id="notificationPanel" aria-labelledby="notificationPanelLabel">
                <div class="offcanvas-header">
                    <h5 class="fw-bold" id="notificationPanelLabel">Notifications</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <h5 class="fw-bold">ALL</h5>
                    <ul class="list-group rounded-0">
                        <li v-for="item in notifications" :key="item.id" class="list-group-item">
                            <a class="text-dark notification-link" href="javascript:;" @click="$router.push(item.url)">
                                <i v-if="item.status === 'read'" class="fas fa-envelope-open-text me-1 fa-lg text-success"></i>
                                <i v-else class="fas fa-envelope me-1 fa-lg text-danger"></i>
                                <span :class="{ 'fw-bold': item.status === 'unread' }">{{ item.notification_content }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
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
            notifications: [],
            collapsed: false,
            menu: [
                {
                    href: '/student-management',
                    title: 'Student Management',
                    icon: 'fa-solid fa-users',
                },
                {
                    href: '/textbook-management',
                    title: 'Textbook Management',
                    icon: 'fa-solid fa-book-open-reader',
                },
                {
                    href: '/quiz-management',
                    title: 'Quiz Management',
                    icon: 'fa-solid fa-pen-to-square',
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
    mounted() {
        this.getNotifications()
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
        async logout(){
            await axios.post('api/logout')
            .then(()=>{
                localStorage.removeItem('fullname')
                this.$router.push({path: '/'})
            })
        },
        async getNotifications() {
            try {
                const response = await axios.get('/api/notifications/get?user_flag=teacher')
                this.notifications = response.data
            } catch (error) {
                console.log(error)
            }
        }
    },
}
</script>

<style scoped>
.profile-image {
    width: 50px;
    height: 50px;
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

.notification-link {
    text-decoration: none;
}

.notification-link:hover {
    text-decoration: underline;
}
</style>
