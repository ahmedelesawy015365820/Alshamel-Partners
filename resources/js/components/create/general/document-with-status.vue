<template>
  <div>
    <!--  create   -->
    <b-modal
          :id="id"
          :title="type != 'edit'?getCompanyKey('link_documents_with_status_create_form'): getCompanyKey('link_documents_with_status_edit_form')"
          title-class="font-18"
          size="lg"
          body-class="p-4 "
          :hide-footer="true"
          @show="resetModal"
          @hidden="resetModalHidden"
      >
          <form>
              <loader size="large" v-if="isCustom && !isPage" />
              <div class="mb-3 d-flex justify-content-end">
                  <b-button
                      v-if="type != 'edit'"
                      variant="success"
                      :disabled="!is_disabled"
                      @click.prevent="resetForm"
                      type="button"
                      :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : '']"
                  >
                      {{ $t("general.AddNewRecord") }}
                  </b-button>
                  <template v-if="!is_disabled">
                      <b-button
                          variant="success"
                          type="submit"
                          class="mx-1"
                          v-if="!isLoader"
                          @click.prevent="AddSubmit"
                      >
                          {{ type != 'edit'? $t("general.Add"): $t("general.edit") }}
                      </b-button>

                      <b-button variant="success" class="mx-1" disabled v-else>
                          <b-spinner small></b-spinner>
                          <span class="sr-only">{{ $t("login.Loading") }}...</span>
                      </b-button>
                  </template>
                  <b-button
                      @click.prevent="resetModalHidden"
                      variant="danger"
                      type="button"
                  >
                      {{ $t("general.Cancel") }}
                  </b-button>
              </div>
              <div class="row">
                  <div class="col-md-6" v-if="isVisible('document_id')">
                      <div class="form-group position-relative">
                          <label class="control-label">
                              {{ getCompanyKey("link_documents_with_status_document") }}
                              <span v-if="isRequired('document_id')" class="text-danger">*</span>
                          </label>
                          <multiselect
                              v-model="create.document_id"
                              :options="documents.map((type) => type.id)"
                              :custom-label="
                                  (opt) => documents.find((x) => x.id == opt)?
                                     $i18n.locale == 'ar'  ?documents.find((x) => x.id == opt).name:documents.find((x) => x.id == opt).name_e
                                    : null
                                "
                          >
                          </multiselect>
                          <div
                              v-if="$v.create.document_id.$error || errors.document_id"
                              class="text-danger"
                          >
                              {{ $t("general.fieldIsRequired") }}
                          </div>
                          <template v-if="errors.document_id">
                              <ErrorMessage
                                  v-for="(errorMessage, index) in errors.document_id"
                                  :key="index"
                              >{{ errorMessage }}</ErrorMessage
                              >
                          </template>
                      </div>
                  </div>
                  <div class="col-md-6" v-if="isVisible('document_module_type_id')">
                      <div class="form-group position-relative">
                          <label class="control-label">
                              {{ getCompanyKey("link_documents_with_status_document_module_type") }}
                              <span v-if="isRequired('document_module_type_id')" class="text-danger">*</span>
                          </label>
                          <multiselect
                              v-model="create.document_module_type_id"
                              :options="moduleTypes.map((type) => type.id)"
                              :custom-label="
                                  (opt) => moduleTypes.find((x) => x.id == opt)?
                                     $i18n.locale == 'ar'  ?moduleTypes.find((x) => x.id == opt).name:moduleTypes.find((x) => x.id == opt).name_e
                                    : null
                                "
                          >
                          </multiselect>
                          <div
                              v-if="$v.create.document_module_type_id.$error || errors.document_module_type_id"
                              class="text-danger"
                          >
                              {{ $t("general.fieldIsRequired") }}
                          </div>
                          <template v-if="errors.document_module_type_id">
                              <ErrorMessage
                                  v-for="(errorMessage, index) in errors.document_module_type_id"
                                  :key="index"
                              >{{ errorMessage }}</ErrorMessage
                              >
                          </template>
                      </div>
                  </div>
                  <div class="col-md-6" v-if="isVisible('status_id')">
                      <div class="form-group position-relative">
                          <label class="control-label">
                              {{ getCompanyKey("link_documents_with_status_status") }}
                              <span v-if="isRequired('status_id')" class="text-danger">*</span>
                          </label>
                          <multiselect
                              v-model="create.status_id"
                              :options="statuses.map((type) => type.id)"
                              :custom-label="
                                  (opt) => statuses.find((x) => x.id == opt)?
                                     $i18n.locale == 'ar'  ?statuses.find((x) => x.id == opt).name:statuses.find((x) => x.id == opt).name_e
                                    : null
                                "
                          >
                          </multiselect>
                          <div
                              v-if="$v.create.status_id.$error || errors.status_id"
                              class="text-danger"
                          >
                              {{ $t("general.fieldIsRequired") }}
                          </div>
                          <template v-if="errors.status_id">
                              <ErrorMessage
                                  v-for="(errorMessage, index) in errors.status_id"
                                  :key="index"
                              >{{ errorMessage }}</ErrorMessage
                              >
                          </template>
                      </div>
                  </div>
              </div>
          </form>
      </b-modal>
    <!--  /create   -->
  </div>
</template>

<script>
import adminApi from "../../../api/adminAxios";
import {required, minLength, maxLength, integer, alpha, requiredIf} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import Multiselect from "vue-multiselect";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import successError from "../../../helper/mixin/success&error";

export default {
    name: "LinkDocumentWithStatus",
  components: {
    Multiselect,
    ErrorMessage,
    loader,
  },
  mixins: [transMixinComp,successError],
  props: {
        id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/cities'}
    },
  mounted() {
    this.company_id = this.$store.getters["auth/company_id"];
  },
  validations: {
      create: {
          document_id: { required: requiredIf(function (model) {
                  return this.isRequired("document_id");
              }) },
          document_module_type_id: { required: requiredIf(function (model) {
                  return this.isRequired("document_module_type_id");
              }) },
          status_id: { required: requiredIf(function (model) {
                  return this.isRequired("status_id");
              }) },
      }
  },
  data() {
    return {
      isCustom: false,
      fields: [],
      isLoader: false,
      is_disabled: false,
      errors: {},
      create: {
          document_id: null,
          document_module_type_id: null,
          status_id: null,
      },
        documents: [],
        moduleTypes: [],
        statuses: [],
    };
  },
  methods: {
    async getCustomTableFields() {
          this.isCustom = true;
           await adminApi
              .get(`/customTable/table-columns/document_company_module_status`)
              .then((res) => {
                  this.fields = res.data;
              })
              .catch((err) => {
                  this.errorFun('Error','Thereisanerrorinthesystem');
              })
              .finally(() => {
                  this.isCustom = false;
              });
      },
    isVisible(fieldName) {
          if(!this.isPage){
              let res = this.fields.filter((field) => {
                  return field.column_name == fieldName;
              });
              return res.length > 0 && res[0].is_visible == 1 ? true : false;
          }else {
              return this.isVisiblePage(fieldName);
          }
      },
    isRequired(fieldName) {
          if(!this.isPage) {
              let res = this.fields.filter((field) => {
                  return field.column_name == fieldName;
              });
              return res.length > 0 && res[0].is_required == 1 ? true : false;
          }else {
              return this.isRequiredPage(fieldName);
          }
      },
    defaultData(edit = null){
        this.create = {
            document_id: null,
            document_module_type_id: null,
            status_id: null,
        };
        this.is_disabled = false;

        this.$nextTick(() => {
            this.$v.$reset();
        });
      },
    resetForm() {
      this.defaultData();
    },
    resetModal() {
      this.defaultData();
      setTimeout( async () => {
            if(this.type != 'edit'){
                if(!this.isPage) await this.getCustomTableFields();
                if (this.isVisible("document_id")) this.getDocument();
                if (this.isVisible("document_module_type_id")) this.getModuleTypes();
                if (this.isVisible("status_id")) this.getStatuses();
            }else {
                if(this.idObjEdit.dataObj){
                    let dataDocument = this.idObjEdit.dataObj;
                    this.errors = {};
                    if (this.isVisible("document_id")) this.getDocument();
                    if (this.isVisible("document_module_type_id")) this.getModuleTypes();
                    if (this.isVisible("status_id")) this.getStatuses();
                    if (this.isVisible("document_id")) this.create.document_id = dataDocument.document_id;
                    if (this.isVisible("document_module_type_id")) this.create.document_module_type_id = dataDocument.document_module_type_id;
                    if (this.isVisible("status_id")) this.create.status_id = dataDocument.status_id;
                }
            }
        },50);
    },
      getDocument() {
       adminApi.get(`/document/get-drop-down`)
            .then((res) => {
              let l = res.data.data;
              this.documents = l;
            })
            .catch((err) => {
                this.errorFun('Error','Thereisanerrorinthesystem');
            });
    },
      getModuleTypes() {
       adminApi.get(`/document-module-type/get-drop-down`)
            .then((res) => {
              let l = res.data.data;
              this.moduleTypes = l;
            })
            .catch((err) => {
                this.errorFun('Error','Thereisanerrorinthesystem');
            });
      },
      getStatuses() {
       adminApi.get(`/statuses/get-drop-down`)
            .then((res) => {
              let l = res.data.data;
              this.statuses = l;
            })
            .catch((err) => {
                this.errorFun('Error','Thereisanerrorinthesystem');
            });
      },

    resetModalHidden() {
      this.defaultData();
      this.$bvModal.hide(this.id);
    },
    AddSubmit() {
      this.$v.create.$touch();

      if (this.$v.create.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        this.is_disabled = false;

        if(this.type != 'edit'){
              adminApi
                  .post(this.url, { ...this.create, company_id: this.company_id })
                  .then((res) => {
                      this.is_disabled = true;
                      if(!this.isPage)
                          this.$emit("created");
                      else
                          this.$emit("getDataTable");

                      setTimeout(() => {
                          this.successFun('Addedsuccessfully');
                      }, 500);
                  })
                  .catch((err) => {
                      if (err.response.data) {
                          this.errors = err.response.data.errors;
                      } else {
                          this.errorFun('Error','Thereisanerrorinthesystem');
                      }
                  })
                  .finally(() => {
                      this.isLoader = false;
                  });
          }else {
              adminApi
                  .put(`${this.url}/${this.idObjEdit.idEdit}`, {
                      ...this.create,
                      company_id: this.$store.getters["auth/company_id"],
                  })
                  .then((res) => {
                      this.$bvModal.hide(this.id);
                      this.$emit("getDataTable");
                      setTimeout(() => {
                          this.successFun('Editsuccessfully');
                      }, 500);
                  })
                  .catch((err) => {
                      if (err.response.data) {
                          this.errors = err.response.data.errors;
                      } else {
                          this.errorFun('Error','Thereisanerrorinthesystem');
                      }
                  })
                  .finally(() => {
                      this.isLoader = false;
                  });
          }
      }
    },
  },
};
</script>

<style>
form {
   position: relative;
}
</style>
