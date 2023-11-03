<template>
    <Layout>
        <PageHeader />
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <div class="row justify-content-center align-items-center mt-2 mb-2 px-1">
                               <div class="col-md-7">
                                   <div class="notify position-relative">
                                       <!--       start loader       -->
                                       <loader size="large" v-if="loading" />
                                       <!--       end loader       -->
                                       <a
                                           href="#"
                                           class="notify-item"
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
                                       <simplebar style="max-height: 450px" @scroll="notificationNotReadScroll" ref="scrollHeigthNotify">
                                           <!-- start item-->
                                           <template v-for="(notification,index) in notifications">
                                               <router-link
                                                   :key="index"
                                                   :to="{name:notification.data.name,params: {id:notification.data.id}}"
                                                   :class="['dropdown-item','notify-item',!notification.read_at?'active':'']"
                                               >
                                                   <div  @click.prevent="clearItem(notification.id,index)">
                                                       <div class="notify-icon bg-soft-primary text-primary">
                                                           <i class="mdi mdi-comment-account-outline"></i>
                                                       </div>
                                                       <p class="notify-details">
                                                           {{ notification.data.message }}
                                                           <br /><small class="text-muted">{{
                                                                   notification.data.timeDate
                                                               }}
                                                           </small>
                                                       </p>
                                                   </div>
                                               </router-link>
                                           </template>
                                           <!-- end item-->
                                       </simplebar>
                                   </div>
                               </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import adminApi from "../../../api/adminAxios";
import loader from "../../../components/general/loader";
import permissionGuard from "../../../helper/permission";


export default {
    name: "notification",
    components: {
        Layout,
        PageHeader,
        loader,
    },
    data(){
        return {
            notifications: [],
            count: 0,
            skip: 0,
            loading: false
        }
    },
    updated() {
        this.notificationNotReadScroll();
    },
    beforeRouteEnter(to, from, next) {
    next((vm) => {
      return permissionGuard(vm, "Notification", "all Notification");
    });
  },
    mounted(){
        this.notificationNotRead();
        this.pusherNotification();
    },
    methods: {
        notificationNotReadScroll(){
            let el = this.$refs.scrollHeigthNotify;
            console.log(el);
            // if (
            //     !(this.count == this.notifications.length)
            //     &&
            //     el.scrollHeight == (el.offsetHeight + el.scrollTop)
            // ) {
            //     this.loading = true;
            //     adminApi.get(`/getAllNot?skip=${this.skip}`)
            //         .then((res) => {
            //             res.data.data.notifications.forEach(el => {
            //                 this.notifications.push(el);
            //             });
            //             this.skip += this.skip;
            //         })
            //         .catch((err) => {
            //             console.log(err.response);
            //         })
            //         .finally(() => {
            //             this.loading = false;
            //         });
            // }
        },
        clearItem(id){
            adminApi.post(`/clearItem/${id}`)
                .then((res) => {
                })
                .catch((err) => {
                    console.log(err.response);
                })
        },
        clearAll(){
            this.loading = true;
            adminApi.post(`/getNotNotRead`)
                .then((res) => {
                    this.notificationNotRead();
                    this.$store.commit('auth/editNotification',!this.$store.state.auth.notification);
                })
                .catch((err) => {
                    console.log(err.response);
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        notificationNotRead(){
            this.loading = true;
            if (localStorage.getItem("user")) {
                adminApi.get(`/getAllNot`)
                    .then((res) => {
                        this.notifications = res.data.data.notifications;
                        this.count = res.data.data.count;
                        this.skip = 15;
                    })
                    .catch((err) => {
                        console.log(err.response);
                    })
                    .finally(() => {
                        this.loading = false;
                    });
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
    }
}
</script>

<style scoped>
.notify {
    padding: 5px 10px 10px;
    box-shadow: 0 0 8px 0 rgb(154 161 171 / 30%);
    margin: 0.125rem 0 0;
    font-size: 0.9rem;
    color: #6c757d;
    text-align: left;
    list-style: none;
    background-color: #fff;
    background-clip: padding-box;
    border: 0 solid #e7eef1;
    border-radius: 0.25rem;
}
.notify-item {
    padding: 15px 20px;
    display: block;
    width: 100%;
    clear: both;
    font-weight: 400;
    color: #6c757d;
    text-align: inherit;
    white-space: nowrap;
    background-color: transparent;
    border: 0;
}
.notify-item.active{
    color: #272e37;
    text-decoration: none;
    background-color: #f1f5f7;
}
.notify-item .notify-icon {
    float: left;
    height: 36px;
    width: 36px;
    font-size: 18px;
    line-height: 38px;
    text-align: center;
    margin-right: 10px;
    border-radius: 50%;
    color: #fff;
}
.notify-item .notify-details {
    margin-bottom: 5px;
    overflow: hidden;
    margin-left: 45px;
    text-overflow: ellipsis;
    white-space: nowrap;
    color: #343a40;
}
.notify-item:hover, .notify-item:focus {
    color: #272e37;
    text-decoration: none;
    background-color: #f1f5f7;
}
</style>




