<script>
import Auth from "../../../layouts/auth";

import { required, email } from "vuelidate/lib/validators";
import adminApi from "../../../../api/adminAxios";
import Swal from "sweetalert2";
import routeModules from "../../../../helper/Rule.js";
import allRoutes from "../../../../helper/allRoute.js";
import { _companies } from "../../../../helper/global.js";

/**
 * Login component
 */
export default {
  page: {
    title: "Login",
    meta: [{ name: "description", content: "login" }],
  },
  data() {
    return {
      email: "",
      password: "",
      submitted: false,
      isSuccess: false,
      isError: false,
      type: "password",
      login_as: "admin",
    };
  },
  components: {
    Auth,
  },
  computed: {},
  validations: {
    email: {
      required,
      email,
    },
    password: {
      required,
    },
  },
  methods: {
    // Try to log the user in with the username
    // and password they provided.
    tryToLogIn() {
      // stop here if form is invalid
      this.$v.$touch();
      if (this.$v.$invalid) {
        return;
      } else {
        this.submitted = true;
        this.isError = false;
        const { email, password } = this;
        if (this.login_as == "admin") {
          axios
            .post(`${process.env.MIX_APP_URL_OUTSIDE}api/partners/login`, {
              email,
              password,
              url: 0,
            })
            .then(async (res) => {
              let l = res.data.data;
              let { id, name, name_e, is_active, email, mobile_no } = l.partner;
              this.$store.commit("auth/editToken", l.token);
              this.$store.commit("auth/editPartner", {
                id,
                name,
                name_e,
                is_active,
                email,
                mobile_no,
              });
              localStorage.setItem(
                "companies",
                JSON.stringify(l.partner.companies)
              );
              _companies.value = l.partner.companies;
              this.$store.commit("auth/editType", "admin");

              if (l.partner.companies.length > 1 && l.partner.companies) {
                this.isSuccess = true;
                this.$router.push({ name: "company" });
              } else if (l.partner.companies.length == 1) {
                if(l.partner.companies[0].is_active == 1){
                    this.isSuccess = true;
                    this.$store.commit(
                        "auth/editCompanyId",
                        l.partner.companies[0].id
                    );
                    allRoutes.value = l.partner.companies[0].Program;
                    localStorage.setItem(
                        "allRoutes",
                        JSON.stringify(l.partner.companies[0].Program)
                    );
                    if (l.partner.companies[0].Program) {
                        let parent = [];
                        l.partner.companies[0].Program.forEach((e) => {
                            if (!parent.find((el) => el.id == e.Parent.id)) {
                                parent.push(e.Parent);
                            }
                        });
                        this.$store.commit("auth/editParentModule", parent);
                    }
                    this.getWorkflows();
                    this.$router.push({ name: "home" });
                    this.getDefaultKeys();
                    this.getCompanyKeys(l.partner.companies[0].id);
                }else {
                    this.isSuccess = false;
                    this.$store.commit("auth/logoutToken");
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t(`general.Error`)}`,
                        text: `${this.$t('general.contactTheAdministration')}`,
                    });
                }
              } else {
                this.submitted = false;
              }
            })
            .catch((err) => {
                if (err.response.status == 422) {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t(`general.Error`)}`,
                        text: `${err.response.data.message}`,
                    });
                } else {
                    this.isError = true;
                }
            })
            .finally(() => {
              this.submitted = false;
            });
        } else {
          adminApi
            .post(`/users/login`, {
              email,
              password,
            })
            .then(async (res) => {
              let l = res.data.data;
              this.$store.commit("auth/editToken", l.token);
              this.$store.commit("auth/editUser", l.user);
              this.$store.commit("auth/editType", "user");
              this.isSuccess = true;
              await this.workflowUser(l.user.permissions);
              await this.getProgCompanyId(l.user.company_id)
              this.$router.push({ name: "home" });
              this.getDefaultKeys();
              this.getCompanyKeys(l.user.company_id);
            })
            .catch((err) => {
              this.isError = true;
            })
            .finally(() => {
              this.submitted = false;
            });
        }
      }
    },
    async getWorkflows() {
      let name = [];
      let modules = ['General'];
      allRoutes.value.forEach((parent) => {
        if (parent.programFolders) {
          parent.programFolders.forEach((child1) => {
            if (child1.screens) {
                if (child1.screens.length > 0) {
                    child1.screens.forEach((child3) => {
                        if(!name.includes(child3.name_e)){
                            name.push(child3.name_e);
                        }
                        if(child3.module_id){
                            let mod = child3.module;
                            if(!modules.includes(mod.name_e)){
                                modules.push(mod.name_e);
                            }
                        }
                    });
                }
            }
            if (child1.subMenus) {
              child1.subMenus.forEach((child2) => {
                if (child2.screens) {
                  child2.screens.forEach((child3) => {
                    if(!name.includes(child3.name_e)){
                        name.push(child3.name_e);
                    }
                      if(child3.module_id){
                          let mod = child3.module;
                          if(!modules.includes(mod.name_e)){
                              modules.push(mod.name_e);
                          }
                      }
                  });
                }
              });
            }
          });
        }
      });
      this.$store.commit("auth/editWorkFlowTrees", [
        "dictionary",
        "home",
        "company",
        ...name,
      ]);
      this.$store.commit("auth/editCustomModules",modules);
    },
    async workflowUser(permissions) {
      let workflowTree = [];
      permissions.forEach((el) => {
        if (!workflowTree.includes(el.crud_name)) {
          workflowTree.push(el.crud_name);
        }
      });
      this.$store.commit("auth/editPermission", permissions);
      this.$store.commit("auth/editWorkFlowTrees", ["home", ...workflowTree]);
    },
    async getDefaultKeys() {
          this.isLoader = true;
          await adminApi
              .post(`/translation-update`, {
                  company_id: 0,
                  translations: {},
                  get_translation: true,
              })
              .then((res) => {
                  let workflows = this.$store.state.auth.work_flow_trees;
                  let keys = {};
                  for (let key in res.data.translations) {
                      // if (this.$store.state.auth.user.type == 'super_admin' || workflows.includes(res.data.translations[key].screen)) {
                      keys[key] = res.data.translations[key];
                      // }
                  }
                  this.$store.commit('translation/editDefaultsKeys',keys);
                  this.$store.commit('translation/editFilterResult',{ ...keys });
              })
              .catch((err) => {
                  Swal.fire({
                      icon: "error",
                      title: `${this.$t("general.Error")}`,
                      text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                  });
              })
              .finally(() => {
                  this.isLoader = false;
              });
      },
    async getCompanyKeys(company_id) {
          this.isLoader = true;
          await adminApi
              .post(`/translation-update`, {
                  company_id: company_id,
                  translations: {},
                  get_translation: true,
              })
              .then((res) => {
                  this.$store.commit('translation/editCompanyKeys',res.data.translations);
              })
              .catch((err) => {
                  Swal.fire({
                      icon: "error",
                      title: `${this.$t("general.Error")}`,
                      text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                  });
              })
              .finally(() => {
                  this.isLoader = false;
              });
      },
    async getProgCompanyId(id) {
          await axios
              .get(
                  `${process.env.MIX_APP_URL_OUTSIDE}api/project-program-modules/find-company-program/${id}`
              )
              .then((res) => {
                  let l = res.data.data;
                  this.$store.commit("auth/editCompanyId", id);
                  allRoutes.value = l.Program;
                  localStorage.setItem("allRoutes", JSON.stringify(l.Program));
                  let parent = [];
                  l.Program.forEach((e) => {
                      if (!parent.find((el) => el.id == e.Parent.id)) {
                          parent.push(e.Parent);
                      }
                  });
                  this.$store.commit("auth/editParentModule", parent);
              })
              .catch((err) => {
                  Swal.fire({
                      icon: "error",
                      title: `${this.$t("general.Error")}`,
                      text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                  });
              });
      },
  },
};
</script>

<template>
  <Auth>
    <div class="row justify-content-center">
      <div class="col-md-8 col-lg-6 col-xl-5">
        <div class="card">
          <div class="card-body p-4">
            <div class="text-center w-75 m-auto">
              <div class="auth-logo">
                <router-link to="/" class="logo logo-dark text-center">
                  <span class="logo-lg">
                    <img src="/images/shamel-logo-006.png" alt height="20" />
                  </span>
                </router-link>
                <router-link to="/" class="logo logo-light text-center">
                  <span class="logo-lg">
                    <img src="/images/shamel-logo-006.png" alt height="20" />
                  </span>
                </router-link>
              </div>
              <p class="text-muted mb-4 mt-3">
                {{
                  $t("login.Enteryouremailaddressandpasswordtoaccessadminpanel")
                }}
              </p>
            </div>

            <form action="#" @submit.prevent="tryToLogIn">
              <b-alert
                variant="success"
                class="mt-3 text-center"
                v-if="isSuccess"
                :show="5"
                dismissible
                >{{ $t("login.success") }}
              </b-alert>

              <b-alert
                variant="danger"
                class="mt-3 text-center"
                v-if="isError"
                :show="5"
                dismissible
                >{{ $t("login.danger") }}
              </b-alert>
              <div class="form-group mb-3">
                <label for="emailaddress">{{ $t("login.Emailaddress") }}</label>
                <input
                  class="form-control"
                  v-model="email"
                  type="email"
                  id="emailaddress"
                  :placeholder="$t('login.Enteryouremail')"
                  :class="{ 'is-invalid': $v.email.$error }"
                />
                <div v-if="$v.email.$error" class="invalid-feedback">
                  <span v-if="!$v.email.required">{{
                    $t("general.fieldIsRequired")
                  }}</span>
                  <span v-if="!$v.email.email">{{
                    $t("general.PleaseEnterValidEmail")
                  }}</span>
                </div>
              </div>

              <div class="form-group mb-3">
                <label for="password">{{ $t("login.Password") }}</label>
                <div class="input-group input-group-merge">
                  <input
                    v-model="password"
                    :type="type"
                    id="password"
                    class="form-control"
                    :placeholder="$t('login.Enteryourpassword')"
                    :class="{ 'is-invalid': $v.password.$error }"
                  />

                  <div
                    class="input-group-append"
                    data-password="false"
                    @click="
                      type == 'password' && password
                        ? (type = 'text')
                        : (type = 'password')
                    "
                  >
                    <div
                      :class="[
                        'input-group-text',
                        type == 'text' ? 'show' : '',
                      ]"
                    >
                      <span
                        :class="['password-eye', type == 'text' ? 'show' : '']"
                      ></span>
                    </div>
                  </div>

                  <div v-if="!$v.password.required" class="invalid-feedback">
                    {{ $t("general.fieldIsRequired") }}
                  </div>
                </div>
              </div>
              <div class="form-group mb-3">
                <label>
                  {{ $t("general.loginAs") }}
                </label>
                <select
                  class="custom-select mr-sm-2"
                  data-create="4"
                  v-model="login_as"
                >
                  <option value="admin">{{ $t("general.Admin") }}</option>
                  <option value="employee">{{ $t("general.Employee") }}</option>
                </select>
              </div>
              <!-- <div class="form-group mb-3">
                                              <div class="custom-control custom-checkbox">
                                                <input
                                                  type="checkbox"
                                                  class="custom-control-input"
                                                  id="checkbox-signin"
                                                  checked
                                                />
                                                <label class="custom-control-label" for="checkbox-signin">{{
                                                  $t("login.Rememberme")
                                                }}</label>
                                              </div>
                                            </div> -->

              <div class="form-group mb-0 text-center">
                <button
                  class="btn btn-primary btn-block"
                  type="submit"
                  v-if="!submitted"
                >
                  {{ $t("login.loginIn") }}
                </button>
                <b-button class="btn btn-primary btn-block" disabled v-else>
                  <b-spinner small></b-spinner>
                  <span class="sr-only">{{ $t("login.Loading") }}...</span>
                </b-button>
              </div>
            </form>
          </div>
          <!-- end card-body -->
        </div>
        <!-- end card -->

        <!-- end row -->
      </div>
      <!-- end col -->
    </div>
    <!-- end row -->
  </Auth>
</template>

<style>
.input-group-text {
  cursor: pointer;
}

.input-group-text.show {
  background-color: #3bafda;
}

.input-group-text .password-eye.show {
  color: #fff;
}

.custom-checkbox .custom-control-input:checked ~ .custom-control-label::after {
  background-color: #3bafda;
}

.logo-lg img {
  width: 70px;
  height: 45px;
}
</style>




