<script>
import { mapState } from "vuex";
import allRoutes from "../../helper/allRoute.js";
import { selectedParents } from "../../helper/global.js";
import routeModules from "../../helper/Rule.js";

export default {
  inject: ["isRouteClicked", "menuLoadedCounter"],
  data() {
    return {
      menuItems: [],
      workFlowTree: [],
      allWorkFlowTree: [],
    };
  },
  props: {
    type: {
      type: String,
      required: true,
    },
    width: {
      type: String,
      required: true,
    },
    size: {
      type: String,
      required: true,
    },
    menu: {
      type: String,
      required: true,
    },
    topbar: {
      type: String,
      required: true,
    },
  },
  computed: {
    ...mapState(["layout"]),
    routeMod: function () {
      return routeModules.value;
    },
  },
  watch: {
    type: {
      immediate: true,
      handler(newVal, oldVal) {
        if (newVal !== oldVal) {
          switch (newVal) {
            case "dark":
              document.body.setAttribute("data-sidebar-color", "dark");
              break;
            case "light":
              document.body.setAttribute("data-sidebar-color", "light");
              break;
            case "brand":
              document.body.setAttribute("data-sidebar-color", "brand");
              break;
            case "gradient":
              document.body.setAttribute("data-sidebar-color", "gradient");
              break;
            default:
              document.body.setAttribute("data-sidebar-color", "light");
              break;
          }
        }
      },
    },
    width: {
      immediate: true,
      handler(newVal, oldVal) {
        if (newVal !== oldVal) {
          switch (newVal) {
            case "boxed":
              document.body.setAttribute("data-layout-width", "boxed");
              document.body.setAttribute("data-sidebar-size", "condensed");
              break;
            case "fluid":
              document.body.setAttribute("data-layout-width", "fluid");
              document.body.removeAttribute("data-sidebar-size");
              document.body.removeAttribute("data-layout-width");
              break;
            default:
              document.body.setAttribute("data-layout-mode", "fluid");
              break;
          }
        }
      },
    },
    size: {
      immediate: true,
      handler(newVal, oldVal) {
        if (newVal !== oldVal) {
          switch (newVal) {
            case "default":
              document.body.setAttribute("data-sidebar-size", "default");
              break;
            case "condensed":
              document.body.setAttribute("data-sidebar-size", "condensed");
              break;
            case "compact":
              document.body.setAttribute("data-sidebar-size", "compact");
              break;
            default:
              document.body.setAttribute("data-sidebar-size", "default");
              break;
          }
        }
      },
    },
    routeMod: {
      handler(newV, old) {
        this.menuItems = routeModules.value;
        this.orderMenu();
      },
    },
    menu: {
      immediate: true,
      handler(newVal, oldVal) {
        if (newVal !== oldVal) {
          switch (newVal) {
            case "fixed":
              document.body.setAttribute("data-layout-menu-position", "fixed");
              break;
            case "scrollable":
              document.body.setAttribute(
                "data-layout-menu-position",
                "scrollable"
              );
              break;
            default:
              document.body.setAttribute("data-layout-menu-position", "fixed");
              break;
          }
        }
      },
    },
    topbar: {
      immediate: true,
      handler(newVal, oldVal) {
        if (newVal !== oldVal) {
          switch (newVal) {
            case "dark":
              document.body.setAttribute("data-topbar-color", "dark");
              break;
            case "light":
              document.body.setAttribute("data-topbar-color", "light");
              break;
            default:
              document.body.setAttribute("data-topbar-color", "dark");
              break;
          }
        }
      },
    },
  },
  mounted() {
    this.workFlowTree = this.$store.state.auth.work_flow_trees;
    this.allWorkFlowTree = this.$store.state.auth.allWorkFlow;
    if (selectedParents.value.length) {
      let is_web = allRoutes.value.find((e) => e.id == selectedParents.value[1]).is_web;
      this.$store.commit("auth/editIsWeb", is_web);
      this.menuItems = routeModules.value;
    } else {
      this.menuItems = [];
    }
    if (!localStorage.getItem("menuLoaded") ?? false) {
      this.orderMenu();
    }
    localStorage.setItem("menuLoaded", true);
    this._activateMenuDropdown();
    this.$router.afterEach((routeTo, routeFrom) => {
      this._activateMenuDropdown();
    });
  },
  methods: {
    orderMenu() {
      let orderedMenus = [...this.menuItems];

      orderedMenus.sort((a, b) =>
        parseInt(a.menu_folder.sort) > parseInt(b.menu_folder.sort) ? 1 : -1
      );

      orderedMenus.forEach((pf) => {
        pf.subMenus.sort((a, b) =>
          parseInt(a.sort) > parseInt(b.sort) ? 1 : -1
        );
      });
      orderedMenus.forEach((pf) => {
        pf.subMenus.forEach((sm) => {
          sm.screens.sort((a, b) =>
            parseInt(a.sort) > parseInt(b.sort) ? 1 : -1
          );
        });
      });
      this.menuItems = orderedMenus;
    },
    // showScreen(module, screen) {
    //   return true;
    //   // let filterRes = this.$store.state.auth.allWorkFlow.filter(
    //   //   (workflow) => workflow.name_e == module.name
    //   // );
    //   // let _module = filterRes.length ? filterRes[0] : null;
    //   // if (!_module) return false;
    //   // return _module.screen ? _module.screen.name_e == screen.name : true;
    // },
    /**
     * Returns true or false if given menu item has child or not
     * @param item menuItem
     */
    hasItems(item) {
      return item.subItems !== undefined ? item.subItems.length > 0 : false;
    },

    _activateMenuDropdown() {
      const resetParent = (el) => {
        el.classList.remove("active");
        var parent = el.parentElement;
        if (parent) {
          parent.classList.remove("menuitem-active");
          const parent2 = parent.parentElement;
          if (parent2) {
            const parent3 = parent2.parentElement;
            if (parent3) {
              parent3.classList.remove("show");
              const parent4 = parent3.parentElement;
              if (parent4) {
                parent4.classList.remove("menuitem-active");
              }
            }
          }
        }
      };
      var links = document.getElementsByClassName("side-nav-link-ref");
      var matchingMenuItem = null;
      const paths = [];
      for (let i = 0; i < links.length; i++) {
        // reset menu
        resetParent(links[i]);
      }
      for (var i = 0; i < links.length; i++) {
        paths.push(links[i]["pathname"]);
      }
      var itemIndex = paths.indexOf(window.location.pathname);
      if (itemIndex === -1) {
        const strIndex = window.location.pathname.lastIndexOf("/");
        const item = window.location.pathname.substr(0, strIndex).toString();
        matchingMenuItem = links[paths.indexOf(item)];
      } else {
        matchingMenuItem = links[itemIndex];
      }

      if (matchingMenuItem) {
        matchingMenuItem.classList.add("active");
        var parent = matchingMenuItem.parentElement;

        /**
         * TODO: This is hard coded way of expading/activating parent menu dropdown and working till level 3.
         * We should come up with non hard coded approach
         */
        if (parent) {
          parent.classList.add("menuitem-active");
          const parent2 = parent.parentElement;
          if (parent2) {
            const parent3 = parent2.parentElement;
            if (parent3) {
              parent3.classList.add("show");
              const parent4 = parent3.parentElement;
              if (parent4) {
                parent4.classList.add("menuitem-active");
              }
            }
          }
        }
      }
    },
    checkUserOrAdmin(item = null) {
      if (this.$store.state.auth.type == "user") {
        return this.$store.state.auth.work_flow_trees.includes(item);
      }
      return true;
    },
    checkUserOrAdminPermission(isUserMenu) {
      if (this.$store.state.auth.type == "user") {
        return isUserMenu;
      }
      return true;
    },
    checkPermission(name){
        return this.$store.state.auth.work_flow_trees.includes(name);
    }
  },
};
</script>

<template>
  <!-- ========== Left Sidebar Start ========== -->
  <div class="left-side-menu">
    <!-- LOGO -->
    <div class="logo-box">
      <router-link to="/" class="logo logo-dark text-center">
        <span class="logo-sm">
          <img src="/images/shamel-logo-006.png" alt height="24" />
          <!-- <span class="logo-lg-text-light">Minton</span> -->
        </span>
        <span class="logo-lg">
          <img src="/images/shamel-logo-006.png" alt height="20" />
          <!-- <span class="logo-lg-text-light">M</span> -->
        </span>
      </router-link>

      <router-link to="/" class="logo logo-light text-center">
        <span class="logo-sm">
          <img src="/images/shamel-logo-006.png" alt height="24" />
        </span>
        <span class="logo-lg">
          <img src="/images/shamel-logo-006.png" alt height="20" />
        </span>
      </router-link>
    </div>

    <simplebar class="h-100" data-simplebar>
      <!-- User box -->
      <div class="user-box text-center">
        <img
          src="../../assets/images/users/avatar-1.jpg"
          alt="user-img"
          title="Mat Helme"
          class="rounded-circle avatar-md"
        />
        <div class="dropdown">
          <a
            href="javascript: void(0);"
            class="text-reset dropdown-toggle h5 mt-2 mb-1 d-block"
            data-toggle="dropdown"
            >Nik Patel</a
          >
          <div class="dropdown-menu user-pro-dropdown">
            <!-- item-->
            <a href="javascript:void(0);" class="dropdown-item notify-item">
              <i class="fe-user mr-1"></i>
              <span>My Account</span>
            </a>

            <!-- item-->
            <a href="javascript:void(0);" class="dropdown-item notify-item">
              <i class="fe-settings mr-1"></i>
              <span>Settings</span>
            </a>

            <!-- item-->
            <a href="javascript:void(0);" class="dropdown-item notify-item">
              <i class="fe-lock mr-1"></i>
              <span>Lock Screen</span>
            </a>

            <!-- item-->
            <a href="javascript:void(0);" class="dropdown-item notify-item">
              <i class="fe-log-out mr-1"></i>
              <span>Logout</span>
            </a>
          </div>
        </div>
        <p class="text-reset">Admin Head</p>
      </div>

      <!--- Sidemenu -->
      <div id="sidebar-menu">
        <!-- Left Menu Start -->
        <ul class="list-unstyled" id="side-menu">
          <li v-if="$store.state.auth.type == 'admin'">
            <router-link to="/dashboard/company" class="side-nav-link-ref">
              <i class="fas fa-city"></i>
              <span>{{ $t("menuitems.company.text") }}</span>
            </router-link>
          </li>
          <template v-for="(item, i) in menuItems">
            <li :key="i" v-if="checkUserOrAdminPermission(item.isUserMenu)">
              <a
                href="javascript:void(0);"
                @click="
                  item.menu_folder.is_menu_collapsed =
                    item.menu_folder.is_menu_collapsed === '0' ||
                    item.menu_folder.is_menu_collapsed == false
                      ? true
                      : false
                "
                :class="['has-arrow', 'has-dropdown']"
              >
                <i
                  :class="`${item.menu_folder.icon}`"
                  v-if="item.menu_folder.icon"
                ></i>
                <span>{{
                  $i18n.locale == "ar"
                    ? item.menu_folder.name
                    : item.menu_folder.name_e
                }}</span>
                <span class="menu-arrow"></span>
              </a>
              <div
                class="collapse"
                :class="{ show: item.menu_folder.is_menu_collapsed === true }"
                id="sidebarTasks"
              >
                <ul
                  class="sub-menu nav-second-level"
                  aria-expanded="false"
                >
                  <template v-for="(subItem, index) of item.subMenus">
                    <li :key="index" v-if="checkUserOrAdminPermission(subItem.isUserMenu)">
                      <a
                        @click="
                          subItem.is_menu_collapsed =
                            subItem.is_menu_collapsed === '0' ||
                            subItem.is_menu_collapsed == false
                              ? true
                              : false
                        "
                        class="side-nav-link-a-ref has-arrow"
                        href="javascript:void(0);"
                      >
                        {{
                          subItem
                            ? $i18n.locale == "ar"
                              ? subItem.name
                              : subItem.name_e
                            : ""
                        }}
                        <span class="menu-arrow"></span>
                      </a>
                      <div
                        class="collapse"
                        :class="{ show: subItem.is_menu_collapsed === true }"
                      >
                        <ul
                          class="sub-menu"
                          aria-expanded="false"
                        >
                            <template v-for="(subSubItem, index) of subItem.screens">
                                <li
                                    :key="index"
                                    v-if="checkPermission(subSubItem.name_e)"
                                >
                                      <router-link
                                          @click.prevent="isRouteClicked = true"
                                          :to="`/dashboard/${subSubItem.url}`"
                                          class="side-nav-link-ref"
                                       >
                                              {{
                                              $i18n.locale == "ar"
                                              ? subSubItem.title
                                              : subSubItem.title_e
                                              }}
                                      </router-link>
                                </li>
                           </template>
                        </ul>
                      </div>
                    </li>
                  </template>
                  <template v-for="(subSubItem, sub) of item.screens">
                        <li
                            :key="subSubItem.id"
                            v-if="checkPermission(subSubItem.name_e)"
                        >
                            <router-link
                                @click.prevent="isRouteClicked = true"
                                :to="`/dashboard/${subSubItem.url}`"
                                class="side-nav-link-ref"
                            >
                                {{
                                $i18n.locale == "ar"
                                ? subSubItem.title
                                : subSubItem.title_e
                                }}
                            </router-link>
                        </li>
                    </template>
                </ul>
              </div>
            </li>
          </template>
          <!--          <template v-for="(item, i) in menuItems">-->
          <!--            <li :key="i">-->
          <!--              <a-->
          <!--                href="javascript:void(0);"-->
          <!--                @click="item.is_menu_collapsed = !item.is_menu_collapsed"-->
          <!--                :class="['has-arrow', 'has-dropdown']"-->
          <!--              >-->
          <!--                <i :class="`${item.icon}`" v-if="item.icon"></i>-->
          <!--                <span>{{-->
          <!--                  $i18n.locale == "ar" ? item.name : item.name_e-->
          <!--                }}</span>-->
          <!--                <span class="menu-arrow"></span>-->
          <!--              </a>-->

          <!--              <div-->
          <!--                class="collapse"-->
          <!--                :class="{ show: item.is_menu_collapsed }"-->
          <!--                id="sidebarTasks"-->
          <!--              >-->
          <!--                <ul-->
          <!--                  v-if="checkUserOrAdmin(item.name_e)"-->
          <!--                  class="sub-menu nav-second-level"-->
          <!--                  aria-expanded="false"-->
          <!--                >-->
          <!--                  <template v-for="(subItem, index) of item.programFolders">-->
          <!--                    <li :key="index">-->
          <!--                      <a-->
          <!--                        v-if="checkUserOrAdmin(subItem.name)"-->
          <!--                        @click="-->
          <!--                          subItem.is_menu_collapsed =-->
          <!--                            !subItem.menu_folder.is_menu_collapsed-->
          <!--                        "-->
          <!--                        class="side-nav-link-a-ref has-arrow"-->
          <!--                        href="javascript:void(0);"-->
          <!--                      >-->
          <!--                        {{-->
          <!--                          subItem.menu_folder-->
          <!--                            ? $i18n.locale == "ar"-->
          <!--                              ? subItem.menu_folder.name-->
          <!--                              : subItem.menu_folder.name_e-->
          <!--                            : ""-->
          <!--                        }}-->
          <!--                        <span class="menu-arrow"></span>-->
          <!--                      </a>-->

          <!--                      <div-->
          <!--                        class="collapse"-->
          <!--                        :class="{ show: subItem.menu_folder.is_menu_collapsed }"-->
          <!--                      >-->
          <!--                        <ul-->
          <!--                          v-if="checkUserOrAdmin(subItem.name)"-->
          <!--                          class="sub-menu"-->
          <!--                          aria-expanded="false"-->
          <!--                        >-->
          <!--                          <li-->
          <!--                            v-for="(subSubItem, index) of subItem.subMenus"-->
          <!--                            :key="index"-->
          <!--                          >-->
          <!--                            <a-->
          <!--                              @click="-->
          <!--                                subSubItem.is_menu_collapsed =-->
          <!--                                  !subSubItem.is_menu_collapsed-->
          <!--                              "-->
          <!--                              v-if="checkUserOrAdmin(subSubItem.name)"-->
          <!--                              class="side-nav-link-a-ref has-arrow"-->
          <!--                              href="javascript:void(0);"-->
          <!--                              >{{-->
          <!--                                $i18n.locale == "ar"-->
          <!--                                  ? subSubItem.name-->
          <!--                                  : subSubItem.name_e-->
          <!--                              }}-->
          <!--                              <span class="menu-arrow"></span>-->
          <!--                            </a>-->
          <!--                            <div-->
          <!--                              class="collapse"-->
          <!--                              :class="{ show: subSubItem.is_menu_collapsed }"-->
          <!--                            >-->
          <!--                              <ul-->
          <!--                                v-if="checkUserOrAdmin(subSubItem.name)"-->
          <!--                                class="sub-menu"-->
          <!--                                aria-expanded="false"-->
          <!--                              >-->
          <!--                                <li-->
          <!--                                  v-for="(-->
          <!--                                    subSubSubItem, index-->
          <!--                                  ) of subSubItem.screens"-->
          <!--                                  :key="index"-->
          <!--                                >-->
          <!--                                  <router-link-->
          <!--                                    v-if="-->
          <!--                                      checkUserOrAdminPermission(-->
          <!--                                        subSubSubItem.name-->
          <!--                                      )-->
          <!--                                    "-->
          <!--                                    :to="`dashboard/${subSubSubItem.url}`"-->
          <!--                                    class="side-nav-link-ref"-->
          <!--                                  >-->
          <!--                                    {{-->
          <!--                                      $i18n.locale == "ar"-->
          <!--                                        ? subSubSubItem.name-->
          <!--                                        : subSubSubItem.name_e-->
          <!--                                    }}-->
          <!--                                  </router-link>-->
          <!--                                </li>-->
          <!--                              </ul>-->
          <!--                            </div>-->
          <!--                          </li>-->
          <!--                        </ul>-->
          <!--                      </div>-->
          <!--                    </li>-->
          <!--                  </template>-->
          <!--                </ul>-->
          <!--              </div>-->
          <!--            </li>-->
          <!--          </template>-->
          <!--          <li v-if="$store.state.auth.user">-->
          <!--            <router-link-->
          <!--              v-if="$store.state.auth.user.type == 'super_admin'"-->
          <!--              to="/dashboard/dictionary"-->
          <!--              class="side-nav-link-ref"-->
          <!--            >-->
          <!--              <i class="fas fa-spell-check"></i>-->
          <!--              <span>{{ $t("general.dictionary") }}</span>-->
          <!--            </router-link>-->
          <!--          </li>-->
        </ul>
      </div>
      <!-- End Sidebar -->

      <div class="clearfix"></div>
    </simplebar>
    <!-- Sidebar -left -->
  </div>
  <!-- Left Sidebar End -->
</template>
<style scoped>
.logo-lg img {
  width: 70px;
  height: 45px;
}
.logo-sm img {
  width: 70px;
  height: 45px;
}
</style>
