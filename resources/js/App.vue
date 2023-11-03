<template>
  <div>
    <router-view />
  </div>
</template>
<script>
import Swal from "sweetalert2";
import adminApi from "./api/adminAxios";
export default {
  provide() {
    return {
      isRouteClicked: this.isRouteClicked,
      menuLoadedCounter:0 ,
    };
  },
  data() {
    return {
      isRouteClicked: false,
      company_id: null,
      menuLoaded:false
    };
  },
  methods: {
    companyId(id) {
      this.$store.commit("auth/editCompanyId", id);
      axios
        .get(
          `${process.env.MIX_APP_URL_OUTSIDE}api/everything_about_the_company/${id}`
        )
        .then((res) => {
          let l = res.data.data;
          let name = [];
          l.work_flow_trees.forEach((parent) => {
            if (parent.module_id && parent.is_module_expired) {
              name.push(parent.name_e + "-e");
            }
            name.push(parent.name_e);
            if (parent.child) {
              parent.child.forEach((child1) => {
                name.push(child1.name_e);
                if (child1.child) {
                  child1.child.forEach((child2) => {
                    name.push(child2.name_e);
                    if (child2.child) {
                      child2.child.forEach((child3) => {
                        name.push(child3.name_e);
                      });
                    }
                  });
                }
              });
            }
          });
          this.$store.commit("auth/editWorkFlowTrees", [
            "dictionary",
            "company",
            ...name,
          ]);
          // this.$store.state.auth.work_flow_trees = ["dictionary", "company", ...name];
          // this.$store.state.auth.allWorkFlow = l.work_flow_trees;
          this.$store.commit("auth/allWorkFlow", l.work_flow_trees);
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        });
    },
    profile() {
      adminApi
        .get(`/users/profile`)
        .then(async (res) => {
          let l = res.data.data;
          this.$store.commit("auth/editUser", l.user);
          this.$store.commit("auth/editType", "user");
          await this.workflowUser(l.user.permissions);
        })
        .catch((err) => {})
        .finally(() => {});
    },
    workflowUser(permissions) {
      let workflowTree = [];
      permissions.forEach((el) => {
        if (el.category.length > 0 && el.category) {
          el.category.forEach((e) => {
            if (!workflowTree.includes(e) && e != "") {
              workflowTree.push(e);
            }
          });
        }
        if (!workflowTree.includes(el.workflow)) {
          workflowTree.push(el.workflow);
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
          this.$store.commit("translation/editDefaultsKeys", keys);
          this.$store.commit("translation/editFilterResult", { ...keys });
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
    async getCompanyKeys() {
      this.isLoader = true;
      await adminApi
        .post(`/translation-update`, {
          company_id: this.company_id,
          translations: {},
          get_translation: true,
        })
        .then((res) => {
          this.$store.commit(
            "translation/editCompanyKeys",
            res.data.translations
          );
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
  },
  created(){
    localStorage.setItem("menuLoaded",false);
  },
  mounted() {
    this.company_id = this.$store.getters["auth/company_id"];
    if (this.$store.state.auth.type == "user") {
      this.profile();
      this.getDefaultKeys();
      this.getCompanyKeys();
    } else if (this.$store.state.auth.type == "admin") {
      this.getDefaultKeys();
      this.getCompanyKeys();
      //this.companyId(this.$store.getters["auth/company_id"]);
    } else {
      this.$store.commit("auth/logoutToken");
      return this.$router.push({ name: "login" });
    }
  },
};
</script>
