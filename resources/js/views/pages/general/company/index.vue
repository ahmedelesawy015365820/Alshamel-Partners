<template>
  <div class="container">
    <div class="company d-flex align-items-center">
      <div class="col-12">
        <div class="mt-3 row justify-content-center align-items-center">
          <div class="col-md-4" v-for="company in companies">
            <div
              class="text-center company-item"
              @click="changeCompany(company.id)"
            >
              <img
                v-if="company.media"
                class="img-thumbnail"
                :src="company.media[0].webp"
                @error="src = '/images/img-1.png'"
              />
              <img
                v-else
                class="img-thumbnail"
                src="/images/img-1.png"
                @error="src = '/images/img-1.png'"
              />
              <h4 class="mt-3">
                {{ $i18n.locale == "ar" ? company.name : company.name_e }}
              </h4>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import permissionGuard from "../../../../helper/permission";

import Swal from "sweetalert2";
import adminApi from "../../../../api/adminAxios";
import routeModules from "../../../../helper/Rule.js";
import allRoutes from "../../../../helper/allRoute.js";
import { selectedParents, _companies } from "../../../../helper/global.js";
export default {
  name: "index",

beforeRouteEnter(to, from, next) {
    next((vm) => {
      return permissionGuard(vm, "Company", "all Company");
    });
  },
  data() {
    return {
      companies: [],
      company_id: null,
    };
  },
  // computed: {
  //   companies() {
  //     return this.$store.getters["auth/companies"];
  //   },
  // },
  async mounted() {
    this.company_id = this.$store.getters["auth/company_id"];
    this.companies = _companies.value;
  },
  methods: {
    changeCompany(id) {
      let companies = _companies.value.find((el) => el.id == id);
      if(companies.is_active == 1){
          localStorage.removeItem("selectedParents");
          selectedParents.value=[];
          this.$store.commit("auth/editCompanyId", id);
          allRoutes.value = companies.Program;
          localStorage.setItem("allRoutes", JSON.stringify(companies.Program));
          if (companies.Program) {
              let parent = [];
              companies.Program.forEach((e) => {
                  if (!parent.find((el) => el.id == e.Parent.id)) {
                      parent.push(e.Parent);
                  }
              });
              this.$store.commit("auth/editParentModule", parent);
              this.getWorkflows();
          }
          this.getDefaultKeys();
          this.getCompanyKeys(companies.id);
          return this.$router.push({ name: "home" });
      }else {
          Swal.fire({
              icon: "error",
              title: `${this.$t(`general.Error`)}`,
              text: `${this.$t(`general.contactTheAdministration`)}`,
          });
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
    /*async companyId(id) {*/
    /*  this.$store.commit("auth/editCompanyId", id);*/
    /*  await axios*/
    /*    .get(*/
    /*      `${process.env.MIX_APP_URL_OUTSIDE}api/everything_about_the_company/${id}`*/
    /*    )*/
    /*    .then((res) => {*/
    /*      let l = res.data.data;*/
    /*      let name = [];*/
    /*      l.work_flow_trees.forEach((parent) => {*/
    /*        if (parent.module_id && parent.is_module_expired) {*/
    /*          name.push(parent.name_e + "-e");*/
    /*        }*/
    /*        name.push(parent.name_e);*/
    /*        if (parent.child) {*/
    /*          parent.child.forEach((child1) => {*/
    /*            name.push(child1.name_e);*/
    /*            if (child1.child) {*/
    /*              child1.child.forEach((child2) => {*/
    /*                name.push(child2.name_e);*/
    /*                if (child2.child) {*/
    /*                  child2.child.forEach((child3) => {*/
    /*                    name.push(child3.name_e);*/
    /*                  });*/
    /*                }*/
    /*              });*/
    /*            }*/
    /*          });*/
    /*        }*/
    /*      });*/
    /*      this.$store.commit("auth/editWorkFlowTrees", [*/
    /*        "dictionary",*/
    /*        "company",*/
    /*        ...name,*/
    /*      ]);*/
    /*      if (l.document_company_module.length > 0) {*/
    /*        let documents = [];*/
    /*        l.document_company_module.forEach(e => {*/
    /*          if (e.document_types.length > 0) {*/
    /*            e.document_types.forEach(w => {*/
    /*              documents.push({*/
    /*                id: w.id,*/
    /*                name: w.name,*/
    /*                name_e: w.name_e,*/
    /*                is_admin: w.is_admin,*/
    /*                is_default: 0,*/
    /*                company_id: id,*/
    /*                document_relateds: w.document_relateds.map(el => el.id)*/
    /*              });*/
    /*            });*/
    /*          }*/
    /*        });*/
    /*        if (documents.length > 0) {*/
    /*          documents.forEach(e => e.is_admin = 1);*/
    /*          adminApi*/
    /*            .post(`/document/from_admin`, { documents })*/
    /*            .then((res) => { })*/
    /*            .catch((err) => {*/
    /*              Swal.fire({*/
    /*                icon: "error",*/
    /*                title: `${this.$t("general.Error")}`,*/
    /*                text: `${this.$t("general.Thereisanerrorinthesystem")}`,*/
    /*              });*/
    /*            })*/
    /*        }*/
    //       }
    //       this.$store.commit("auth/allWorkFlow", l.work_flow_trees);
    //       return this.$router.push({ name: "home" });
    //     })
    //     .catch((err) => {
    //       Swal.fire({
    //         icon: "error",
    //         title: `${this.$t("general.Error")}`,
    //         text: `${this.$t("general.Thereisanerrorinthesystem")}`,
    //       });
    //     });
    // },
  },
};
</script>

<style scoped>
.company {
  background-color: #dff0fe;
  min-height: 100vh;
  width: 100%;
  padding: 40px 0;
}

.company-content {
  height: 100%;
}

img {
  max-height: 150px;
  max-width: 250px;
}

.company-item {
  cursor: pointer;
  background: #fff;
  padding: 40px 20px;
  border-radius: 14px;
  height: 260px;
  margin: 7px 0;
}

.container {
  max-width: 1100px !important;
}
</style>
