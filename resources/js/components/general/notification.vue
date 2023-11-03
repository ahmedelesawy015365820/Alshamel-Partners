<template>
    <b-nav-item-dropdown
        right
        class="notification-list"
        :menu-class="['dropdown-lg']"
    >
        <template slot="button-content" class="nav-link dropdown-toggle">
            <i class="fe-bell noti-icon"></i>
            <span
                class="badge badge-danger rounded-circle noti-icon-badge"
                v-if="count"
            >{{
              count
            }}</span>
        </template>

        <a
            href="#"
            class="dropdown-item noti-title"
        >
            <h5 class="m-0">
              <span class="float-right">
                <a href class="text-dark" @click.prevent="clearAll">
                  <small>{{
                    $t("navbar.dropdown.notification.subtext")
                  }}</small>
                </a>
              </span>
              {{ $t("navbar.dropdown.notification.text") }}
            </h5>
        </a>

        <simplebar style="max-height: 230px" >
            <template v-for="(notification,index) in notifications">
                <router-link
                    :key="index"
                    :to="{name:notification.data.name,params: {id:notification.data.id}}"
                    class="dropdown-item notify-item"
                >
                    <div  @click="clearItem(notification.id,index)">
                        <div class="notify-icon bg-soft-primary text-primary">
                            <i class="mdi mdi-comment-account-outline"></i>
                        </div>
                        <p class="notify-details">
                            {{ notification.data.message }}
                            <small class="text-muted">{{
                                    notification.data.timeDate
                                }}
                            </small>
                        </p>
                    </div>
                </router-link>
            </template>
        </simplebar>

        <router-link
            :to="{name: 'notifications'}"
            class="dropdown-item text-center text-primary notify-item notify-all"
        >
            {{ $t("navbar.dropdown.notification.button") }}
            <i class="fi-arrow-right"></i>
        </router-link>
    </b-nav-item-dropdown>
</template>

<script>
import adminApi from "../../api/adminAxios";

export default {
    name: "notification",
    data(){
        return {
            notifications: [],
            count: 0
        }
    },
    computed: {
        deleteNot () {
            return this.$store.state.auth.notification;
        }
    },
    watch: {
        deleteNot (newDeleteNot, oldDeleteNot) {
            this.notifications = [];
            this.count = 0;
        }
    },
    methods: {
        notificationNotRead(){
            if(localStorage.getItem("user")){
                adminApi.get(`/getNotNotRead`)
                    .then((res) => {
                        this.notifications = res.data.data.notifications;
                        this.count = res.data.data.count;
                    })
                    .catch((err) => {})
            }
        },
        clearItem(id,index){
            if(localStorage.getItem("user")){
                adminApi.post(`/clearItem/${id}`)
                    .then((res) => {
                        this.notifications.splice(index,1);
                        this.count -= 1;
                    })
                    .catch((err) => {})
            }
        },
        clearAll(){
            if(localStorage.getItem("user")){
                adminApi.post(`/getNotNotRead`)
                    .then((res) => {
                        this.notifications = [];
                        this.count = 0;
                    })
                    .catch((err) => {})
            }
        },
        pusherNotification(){
            if(localStorage.getItem("user")){
                Echo.private('App.Models.User.'+JSON.parse(localStorage.getItem("user")).id)
                    .notification((notification) => {
                        this.notifications.unshift(notification);
                        this.count += 1;
                        console.log(notification);
                    });
            }
        }
    },
    mounted(){
        this.notificationNotRead();
        this.pusherNotification();
    }
}
</script>

<style scoped>

.dropdown .dropdown-menu.dropdown-menu-custom {
    padding: 0 !important;
}
</style>
