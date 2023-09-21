<template>
    <sidebar-menu :menu="menu" :theme="theme" :collapsed="collapsed" @update:collapsed="onCollapse" @item-click="onItemClick">
        <template v-slot:header>
            <div :class="collapsed ? 'd-none' : 'visibility-visible'" class="position-relative d-flex align-items-center justify-content-center flex-column" style="height: 210px;">
                <button data-bs-toggle="offcanvas" data-bs-target="#notificationPanel" aria-controls="notificationPanel" style="right: 20px" type="button" class="icon-button mt-3 position-absolute top-0">
                    <i class="far fa-bell fa-lg"></i>
                    <span v-if="notificationCount" class="icon-button__badge">{{ notificationCount ?? '' }}</span>
                </button>
                <div class="profile-image" :class="{ 'mb-3' : !collapsed }">
                    <img src="/images/user-icon.png" alt="User profile image">
                </div>
                <a class="fw-bold mb-3" type="button" @click="editProfile">Edit Profile</a>
                <h5 class="username">{{ fullname }}</h5>
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
                    <hr>
                    <template v-if="notifications.length">
                        <div v-for="item in notifications" :key="item.id">
                            <div class="p-2" :class="{ 'unread-background': item.status === 'unread' }">
                                <a class="text-dark notification-link" href="javascript:;" @click="updateNotification(item)">
                                    <i v-if="item.status === 'read'" class="fas fa-envelope-open-text me-1 fa-lg text-success"></i>
                                    <i v-else class="fas fa-circle me-1 fa-sm text-danger"></i>
                                    <span :class="{ 'fw-bold': item.status === 'unread' }">{{ item.notification_content }}</span>
                                </a>
                            </div>
                            <hr>
                        </div>
                    </template>
                    <template v-else>
                        <p class="text-center">No records found</p>
                    </template>
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
                    href: '/student-certificates',
                    title: 'Certificates',
                    icon: 'fa-solid fa-certificate',
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
        notificationCount() {
            const unreadNotification = this.notifications.filter($data => {
                return $data.status === 'unread'
            })
            return unreadNotification.length
        }
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
        editProfile() {
            this.$router.push('/student/edit-profile')
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
                const response = await axios.get('/api/notifications/get?user_flag=student')
                this.notifications = response.data
            } catch (error) {
                console.log(error)
            }
        },
        async updateNotification(data) {
            try {
                if (data.status === 'unread')
                    await axios.put(`/api/notification/update/${data.id}`)

                this.$router.push(data.url)
            } catch (error) {
                console.log(error)
            }
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

.notification-link {
    text-decoration: none;
}

.notification-link:hover {
    color: blue !important;
}

</style>
