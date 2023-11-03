<script>
import adminApi from "../../api/adminAxios";
import router from "../../router";
import Notification from "./notification";
import allRoutes from "../../helper/allRoute.js";
import routeModules from "../../helper/Rule.js";
import { selectedParents } from "../../helper/global.js";
import Swal from "sweetalert2";
/**
 * Topbar component
 */
export default {
  data() {
    return {
      languages: [
        {
          flag: "/assets/images/us.jpg",
          language: "en",
          title: "English",
        },
        {
          flag: "/assets/images/arabic.png",
          language: "ar",
          title: "Arabic",
        },
      ],
        selectedParents:selectedParents,
      current_language: this.$i18n.locale,
      text: null,
      flag: null,
      value: null,
      currentParentId: null,
      relates: {
        archiving: [
          "arch-departments",
          "archiving screen",
          "arch-doc-status",
          "document-fields",
          "gen-arch-doc-types",
        ],
      },
      module: {},
      allRouteModule: [],
    };
  },
  components: {
    Notification,
  },
  mounted() {
    this.value = this.languages.find((x) => x.language === this.$i18n.locale);
    this.text = this.value.title;
    this.flag = this.value.flag;
    this.allRouteModule = allRoutes.value;
  },
  methods: {
    getMenu(item1, item2)  {
        let programFolders = allRoutes.value.find(
            (e) => e.id == item2.id
        );
        if(programFolders.status == 1){
            this.$store.commit("auth/editIsWeb", programFolders.is_web);
            let folder = this.$store.state.auth.type == 'user' ? this.getWorkflows(programFolders.programFolders) : programFolders.programFolders;
            routeModules.value = folder;
            selectedParents.value = [item1.id, item2.id];
            localStorage.setItem("routeModules", JSON.stringify(folder));
            localStorage.setItem("selectedParents", JSON.stringify(selectedParents.value));
        }else {
            Swal.fire({
                icon: "error",
                title: `${this.$t(`general.Error`)}`,
                text: `${this.$t(`general.contactTheAdministration`)}`,
            });
        }
    },
    getWorkflows(folders){
        // start check user menu subMenus
        folders.forEach((child1) => {
            if (child1.subMenus) {
                let menusCheck = []
                child1.subMenus.forEach((child2) => {
                    if (child2.screens) {
                        let subMenusCheck = [];
                        child2.screens.forEach((child3) => {
                            if(this.$store.state.auth.work_flow_trees.includes(child3.name_e))
                                subMenusCheck.push(true);
                            else
                                subMenusCheck.push(false);
                        });
                        if(subMenusCheck.length > 0){
                            child2.isUserMenu = subMenusCheck.some(el => el);
                        }
                    }

                    menusCheck.push(child2.isUserMenu);
                });
                child1.isUserMenu = menusCheck.some(el => el);
            }

        });
        // end check user menu subMenus

        return folders;
    },
    /**
     * Toggle menu
     */
    toggleMenu() {
      this.$parent.toggleMenu();
    },
    /**
     * Full screen
     */
    initFullScreen() {
      document.body.classList.toggle("fullscreen-enable");
      if (
        !document.fullscreenElement &&
        /* alternative standard method */
        !document.mozFullScreenElement &&
        !document.webkitFullscreenElement
      ) {
        // current working methods
        if (document.documentElement.requestFullscreen) {
          document.documentElement.requestFullscreen();
        } else if (document.documentElement.mozRequestFullScreen) {
          document.documentElement.mozRequestFullScreen();
        } else if (document.documentElement.webkitRequestFullscreen) {
          document.documentElement.webkitRequestFullscreen(
            Element.ALLOW_KEYBOARD_INPUT
          );
        }
      } else {
        if (document.cancelFullScreen) {
          document.cancelFullScreen();
        } else if (document.mozCancelFullScreen) {
          document.mozCancelFullScreen();
        } else if (document.webkitCancelFullScreen) {
          document.webkitCancelFullScreen();
        }
      }
    },
    /**
     * Toggle rightbar
     */
    toggleRightSidebar() {
      this.$parent.toggleRightSidebar();
    },
    /**
     * Set languages
     */
    setLanguage(locale, country, flag) {
      this.$i18n.locale = locale;
      this.current_language = locale;
      this.text = country;
      this.flag = flag;
      localStorage.setItem("lang", locale);
      if (locale == "ar") {
        document.body.style.textAlign = "right";
        document.body.classList.add("rtl");
        document.querySelector("html").style.direction = "rtl";
        document.querySelector("html").setAttribute("lang", locale || "ar");
        let style_dashboard = document.getElementById("style_dashboard");
        style_dashboard.setAttribute(
          "href",
          window.location.origin + `/css/custom.css`
        );
      } else {
        document.body.style.textAlign = "left";
        document.body.classList.remove("rtl");
        document.querySelector("html").style.direction = "ltr";
        document.querySelector("html").setAttribute("lang", locale || "ar");
        let style_dashboard = document.getElementById("style_dashboard");
        style_dashboard.setAttribute("href", "");
      }
    },
    /**
     * Horizontal-toggle menu
     */
    horizonalmenu() {
      let element = document.getElementById("topnav-menu-content");
      element.classList.toggle("show");
    },
    /**
     *  Logout Dashboard
     */
    async logout() {
      if (this.$store.state.auth.type == "user") {
        await adminApi
          .post(`/auth/logout`)
          .then((res) => {
            this.$store.commit("auth/logoutToken");
            return this.$router.push({ name: "login" });
          })
          .catch((err) => {
            this.$store.commit("auth/logoutToken");
            return this.$router.push({ name: "login" });
          })
          .finally(() => {});
      } else {
        this.$store.commit("auth/logoutToken");
        return this.$router.push({ name: "login" });
      }
    },
  },
};
</script>

<template>
  <!-- Topbar Start -->
  <div class="navbar-custom">
    <div class="container-fluid">
      <ul class="list-unstyled topnav-menu float-right mb-0">
        <li class="dropdown d-lg-inline-block">
          <a
            class="nav-link dropdown-toggle arrow-none waves-effect waves-light"
            @click="initFullScreen"
            data-toggle="fullscreen"
            href="#"
          >
            <i class="fe-maximize noti-icon"></i>
          </a>
        </li>

        <b-nav-item-dropdown
          variant="white"
          class="d-lg-inline-block"
          right
          toggle-class="header-item"
        >
          <template v-slot:button-content>
            <img class :src="flag" alt="Header Language" height="16" />
            {{ text }}
          </template>
          <b-dropdown-item
            class="notify-item"
            v-for="(entry, i) in languages"
            :key="`Lang${i}`"
            :value="entry"
            @click="setLanguage(entry.language, entry.title, entry.flag)"
            :link-class="{ active: entry.language === current_language }"
          >
            <img
              :src="`${entry.flag}`"
              alt="user-image"
              class="mr-1"
              height="12"
            />
            <span class="align-middle">{{ entry.title }}</span>
          </b-dropdown-item>
        </b-nav-item-dropdown>

        <Notification />

        <b-nav-item-dropdown
          right
          class="notification-list topbar-dropdown"
          menu-class="profile-dropdown"
          toggle-class="p-0"
        >
          <template slot="button-content" class="nav-link dropdown-toggle">
            <div class="nav-user mr-0">
              <img
                src="../../assets/images/users/avatar-1.jpg"
                alt="user-image"
                class="rounded-circle"
              />
              <span
                class="pro-user-name ml-1"
                v-if="$store.state.auth.type == 'admin'"
              >
                {{
                  $i18n.locale
                    ? $store.getters["auth/partner"].name
                    : $store.getters["auth/partner"].name_e
                }}
                <i class="mdi mdi-chevron-down"></i>
              </span>
              <span class="pro-user-name ml-1" v-else>
                {{
                  $i18n.locale
                    ? $store.state.auth.user.name
                    : $store.state.auth.user.name_e
                }}
                <i class="mdi mdi-chevron-down"></i>
              </span>
            </div>
          </template>

          <b-dropdown-header>
            <h6 class="text-overflow m-0 py-2">
              {{ $t("navbar.dropdown.name.list.greet") }}
            </h6>
          </b-dropdown-header>

          <b-dropdown-item href="#">
            <i class="remixicon-account-circle-line"></i>
            <span>{{ $t("navbar.dropdown.name.list.account") }}</span>
          </b-dropdown-item>

          <b-dropdown-item href="#">
            <i class="remixicon-settings-3-line"></i>
            <span>{{ $t("navbar.dropdown.name.list.settings") }}</span>
          </b-dropdown-item>

          <b-dropdown-item href="#">
            <i class="remixicon-lock-line"></i>
            <span>{{ $t("navbar.dropdown.name.list.lockscreen") }}</span>
          </b-dropdown-item>

          <b-dropdown-divider></b-dropdown-divider>
          <a
            class="dropdown-item"
            href="javascript:void(0)"
            @click.prevent="logout"
          >
            <i class="fe-log-out mr-1"></i>
            <span>{{ $t("navbar.dropdown.name.list.logout") }}</span>
          </a>
        </b-nav-item-dropdown>
      </ul>

      <!-- LOGO -->
      <div class="logo-box">
        <router-link to="/" class="logo logo-dark text-center">
          <span class="logo-sm">
            <img src="../../assets/images/logo-sm-dark.png" alt height="24" />
            <!-- <span class="logo-lg-text-light">Minton</span> -->
          </span>
          <span class="logo-lg">
            <img src="../../assets/images/logo-dark.png" alt height="20" />
            <!-- <span class="logo-lg-text-light">M</span> -->
          </span>
        </router-link>

        <router-link to="/" class="logo logo-light text-center">
          <span class="logo-sm">
            <img src="../../assets/images/logo-sm.png" alt height="24" />
          </span>
          <span class="logo-lg">
            <img src="../../assets/images/logo-light.png" alt height="20" />
          </span>
        </router-link>
      </div>

      <ul class="list-unstyled topnav-menu topnav-menu-left m-0">
        <li>
          <button
            class="button-menu-mobile waves-effect waves-light"
            @click="toggleMenu"
          >
            <i class="fe-menu"></i>
          </button>
        </li>

        <li>
          <!-- Mobile menu toggle (Horizontal Layout)-->
          <a
            class="navbar-toggle nav-link"
            data-toggle="collapse"
            @click="horizonalmenu()"
            data-target="#topnav-menu-content"
          >
            <div class="lines">
              <span></span>
              <span></span>
              <span></span>
            </div>
          </a>
          <!-- End mobile menu toggle-->
        </li>

        <template>
          <li
            class="nav-route-related d-none d-md-block"
            v-if="$store.state.auth.parentModule.length > 0"
          >
            <div>
              <ul class="list-unstyled">
                <li
                  :key="index"
                  :class="['d-inline']"
                  style="margin: 0 0.5rem"
                  v-for="(item, index) in $store.state.auth.parentModule"
                >
                  <!-- Basic dropdown getMenu(item.id) -->
                  <b-dropdown
                    :variant="selectedParents.value.length&&selectedParents.value[0]==item.id?'secondary':'primary'"
                    :text="$i18n.locale == 'ar' ? item.name : item.name_e"
                    ref="dropdown"
                  >
                    <template
                      v-for="(item2, index) in allRouteModule.filter(
                        (e) => e.Parent.id == item.id
                      )"
                      :index="item2.id"
                    >
                      <b-dropdown-item
                      :class="selectedParents.value.length&&selectedParents.value[1]==item2.id?'selected-program':''"

                        href="#"
                        @click.prevent="getMenu(item, item2)"
                      >
                        {{ $i18n.locale == "ar" ? item2.name : item2.name_e }}
                      </b-dropdown-item>
                    </template>
                  </b-dropdown>
                  <!-- Basic dropdown -->
                </li>
              </ul>
            </div>
          </li>
        </template>
      </ul>
      <div class="clearfix"></div>
    </div>
  </div>
  <!-- end Topbar -->
</template>

<style>
.selected-program .dropdown-item {
    background: #f1f5f7 !important;
}
</style>

