<script>
import Layout from "../../../layouts/main";
import PageHeader from "../../../../components/general/Page-header";
import adminApi from "../../../../api/adminAxios";
import loader from "../../../../components/general/loader";
import translation from "../../../../helper/mixin/translation-mixin";
import Swal from "sweetalert2";
import { required, minLength, maxLength, integer } from "vuelidate/lib/validators";
import permissionGuard from "../../../../helper/permission";
/**
 * Advanced Table component
 */
export default {
  page: {
    title: "Dictionary",
    meta: [{ name: "description", content: "Dictionary" }],
  },
  mixins: [translation],
  components: {
    Layout,
    PageHeader,
    loader,
  },
  data() {
    return {
      isLoader: false,
      currentKey: "",
      newText: "",
      text_ar: "",
      text_en: "",
      company_id: null,
      search: "",
    };
  },
  validations: {
    text_ar: { required, minLength: minLength(2), maxLength: maxLength(100) },
    text_en: { required, minLength: minLength(2), maxLength: maxLength(100) },
  },
  updated() {
    $(function () {
      $(".englishInput").keypress(function (event) {
        var ew = event.which;
        if (ew == 32) return true;
        if (48 <= ew && ew <= 57) return true;
        if (65 <= ew && ew <= 90) return true;
        if (97 <= ew && ew <= 122) return true;
        return false;
      });
      $(".arabicInput").keypress(function (event) {
        var ew = event.which;
        if (ew == 32) return true;
        if (48 <= ew && ew <= 57) return false;
        if (65 <= ew && ew <= 90) return false;
        if (97 <= ew && ew <= 122) return false;
        return true;
      });
    });
  },
  mounted() {
    this.company_id = this.$store.getters["auth/company_id"];
  },
  
beforeRouteEnter(to, from, next) {
    next((vm) => {
      return permissionGuard(vm, "Dictionary", "all Dictionary");
    });
  },
  methods: {
    moveInput(tag, c, index) {
      document.querySelector(`${tag}[data-${c}='${index}']`).focus();
    },
    cancelUpdate() {
      this.currentKey = "";
      this.newText = "";
    },
    setCurrentKey(propertyName) {
      this.$bvModal.show(`create`);
      this.currentKey = propertyName;
      this.text_ar = this.getCompanyKeyLang(this.currentKey, "ar");
      this.text_en = this.getCompanyKeyLang(this.currentKey, "en");
    },
    filteringResult() {
      let filterResult = {};
      for (let key in this.defaultsKeys) {
        if (key.includes(this.search)) {
          filterResult[key] = this.defaultsKeys[key];
        }
      }
      this.filterResult = filterResult;
    },
    updateChange() {
      if (!this.text_ar) {
        this.text_ar = this.text_en;
      }
      if (!this.text_en) {
        this.text_en = this.text_ar;
      }
      this.$v.$touch();
      if (this.$v.$invalid) {
        return;
      }
      this.isLoader = true;
      let currentKey = this.currentKey;
      let formValue = {
        company_id: this.company_id,
        get_translation: false,
        translations: {},
      };
      formValue.translations[currentKey] = {
        default_en: "",
        default_ar: "",
        new_ar: this.text_ar,
        new_en: this.text_en,
      };
      adminApi
        .post("/translation-update", formValue)
        .then(() => {
          this.$bvModal.hide("create");
          this.getCompanyKeys();
          setTimeout(() => {
            Swal.fire({
              icon: "success",
              text: `${this.$t("general.Editsuccessfully")}`,
              showConfirmButton: false,
              timer: 1500,
            });
          }, 500);
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
    reset(propertyName) {
      let keyInfo = this.getKeyInfo(propertyName);
      if (!keyInfo) {
        Swal.fire({
          icon: "error",
          text: `${this.$t("general.CompanyNotHaveTranslation")}`,
          showConfirmButton: false,
        });
      } else {
        this.isLoader = true;
        adminApi
          .post("/translation-delete", {
            get_translation: false,
            ids: [keyInfo.id],
          })
          .then(() => {
            this.getCompanyKeys();
            setTimeout(() => {
              Swal.fire({
                icon: "success",
                text: `${this.$t("general.Editsuccessfully")}`,
                showConfirmButton: false,
                timer: 1500,
              });
            }, 500);
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
      }
    },
  },
};
</script>

<template>
  <Layout>
    <PageHeader />
    <!--  create   -->
    <b-modal
      id="create"
      :title="$t('general.translation')"
      title-class="font-18"
      body-class="p-4 "
      :hide-footer="true"
    >
      <form>
        <div class="mb-3 d-flex justify-content-end">
          <b-button
            variant="success"
            type="button"
            class="mx-1"
            v-if="!isLoader"
            @click.prevent="updateChange"
          >
            {{ $t("general.Edit") }}
          </b-button>

          <b-button variant="success" class="mx-1" disabled v-else>
            <b-spinner small></b-spinner>
            <span class="sr-only">{{ $t("login.Loading") }}...</span>
          </b-button>
          <!-- Emulate built in modal footer ok and cancel button actions -->
          <b-button
            variant="danger"
            type="button"
            @click.prevent="$bvModal.hide('create')"
          >
            {{ $t("general.Cancel") }}
          </b-button>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="field-1" class="control-label">
                {{ $t("general.text_ar") }}
                <span class="text-danger">*</span>
              </label>
              <div dir="rtl">
                <input
                  type="text"
                  class="form-control arabicInput"
                  data-create="1"
                  @keypress.enter="moveInput('input', 'create', 2)"
                  v-model="$v.text_ar.$model"
                  :class="{
                    'is-invalid': $v.text_ar.$error,
                    'is-valid': !$v.text_ar.$invalid,
                  }"
                  id="field-1"
                />
              </div>
              <div v-if="!$v.text_ar.minLength" class="invalid-feedback">
                {{ $t("general.Itmustbeatleast") }}
                {{ $v.text_ar.$params.minLength.min }}
                {{ $t("general.letters") }}
              </div>
              <div v-if="!$v.text_ar.maxLength" class="invalid-feedback">
                {{ $t("general.Itmustbeatmost") }}
                {{ $v.text_ar.$params.maxLength.max }}
                {{ $t("general.letters") }}
              </div>
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group">
              <label for="field-2" class="control-label">
                {{ $t("general.text_en") }}
                <span class="text-danger">*</span>
              </label>
              <div dir="ltr">
                <input
                  type="text"
                  class="form-control englishInput"
                  data-create="2"
                  @keypress.enter="moveInput('select', 'create', 3)"
                  v-model="$v.text_en.$model"
                  :class="{
                    'is-invalid': $v.text_en.$error,
                    'is-valid': !$v.text_en.$invalid,
                  }"
                  id="field-2"
                />
              </div>
              <div v-if="!$v.text_en.minLength" class="invalid-feedback">
                {{ $t("general.Itmustbeatleast") }}
                {{ $v.text_en.$params.minLength.min }}
                {{ $t("general.letters") }}
              </div>
              <div v-if="!$v.text_en.maxLength" class="invalid-feedback">
                {{ $t("general.Itmustbeatmost") }}
                {{ $v.text_en.$params.maxLength.max }}
                {{ $t("general.letters") }}
              </div>
            </div>
          </div>
        </div>
      </form>
    </b-modal>
    <!--  /create   -->
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <!-- start search -->
            <div class="row justify-content-between align-items-center mb-2">
              <h4 class="header-title mb-4">{{ $t("general.dictionary") }}</h4>
              <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                <span
                  :class="[
                    'search-custom position-absolute',
                    $i18n.locale == 'ar' ? 'search-custom-ar' : '',
                  ]"
                >
                  <i class="fe-search"></i>
                </span>
                <input
                  class="form-control"
                  style="display: block; width: 93%; padding-top: 3px"
                  type="text"
                  v-model.trim="search"
                  @input="filteringResult"
                  :placeholder="`${$t('general.search_source_text')}`"
                />
              </div>
            </div>
            <div class="table-responsive mb-3 custom-table-theme position-relative">
              <loader size="large" v-if="isLoader" />
              <table class="table table-borderless table-hover table-centered m-0">
                <thead>
                  <tr>
                    <th>
                      <div class="d-flex justify-content-center">
                        <span>{{ $t("general.source_text") }}</span>
                      </div>
                    </th>
                    <th>
                      <div class="d-flex justify-content-center">
                        <span>{{ $t("general.text_ar") }}</span>
                      </div>
                    </th>
                    <th>
                      <div class="d-flex justify-content-center">
                        <span>{{ $t("general.text_en") }}</span>
                      </div>
                    </th>
                    <th>
                      {{ $t("general.Action") }}
                    </th>
                  </tr>
                </thead>
                <tbody v-if="Object.keys(filterResult).length > 0">
                  <tr
                    v-for="(value, propertyName) in filterResult"
                    :key="propertyName"
                    @dblclick="setCurrentKey(propertyName)"
                  >
                    <td>
                      <h5 class="m-0 font-weight-normal">{{ propertyName }}</h5>
                    </td>
                    <td>
                      <h5 class="m-0 font-weight-normal">
                        {{ getCompanyKeyLang(propertyName, "ar") }}
                      </h5>
                    </td>
                    <td>
                      <h5 class="m-0 font-weight-normal">
                        {{ getCompanyKeyLang(propertyName, "en") }}
                      </h5>
                    </td>
                    <td>
                      <div class="btn-group">
                        <div class="btn-group">
                          <button
                            type="button"
                            class="btn btn-sm dropdown-toggle dropdown-coustom"
                            data-toggle="dropdown"
                            aria-expanded="false"
                          >
                            {{ $t("general.commands") }}
                            <i class="fas fa-angle-down"></i>
                          </button>
                          <div class="dropdown-menu dropdown-menu-custom">
                            <a
                              class="dropdown-item"
                              href="#"
                              @click="setCurrentKey(propertyName)"
                            >
                              <div
                                class="d-flex justify-content-between align-items-center text-black"
                              >
                                <span>{{ $t("general.edit") }}</span>
                                <i class="mdi mdi-square-edit-outline text-info"></i>
                              </div>
                            </a>
                            <a
                              class="dropdown-item text-black"
                              href="#"
                              @click.prevent="reset(propertyName)"
                            >
                              <div
                                class="d-flex justify-content-between align-items-center text-black"
                              >
                                <span>{{ $t("general.reset") }}</span>
                                <i class="fas fa-times text-danger"></i>
                              </div>
                            </a>
                          </div>
                        </div>
                      </div>
                    </td>
                  </tr>
                </tbody>
                <tbody v-else>
                  <tr>
                    <th class="text-center" colspan="4">
                      {{ $t("general.notDataFound") }}
                    </th>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>
<style scoped>
thead {
  background-color: #3bafda;
  color: #fff;
}
table td {
  padding: 0.13rem !important;
}
</style>
