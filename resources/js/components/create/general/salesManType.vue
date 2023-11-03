<template>
  <!--  create   -->
  <b-modal
        :id="id"
        :title="type != 'edit'?getCompanyKey('sale_man_type_create_form'):getCompanyKey('sale_man_type_edit_form')"
        title-class="font-18"
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
                    :class="[
                      'font-weight-bold px-2',
                      is_disabled ? 'mx-2' : '',
                    ]"
                >
                    {{ $t("general.AddNewRecord") }}
                </b-button>
                <template v-if="!is_disabled">
                    <b-button
                        variant="success"
                        type="button"
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
                <!-- Emulate built in modal footer ok and cancel button actions -->

                <b-button
                    variant="danger"
                    type="button"
                    @click.prevent="resetModalHidden"
                >
                    {{ $t("general.Cancel") }}
                </b-button>
            </div>
            <div class="row">
                <div class="col-md-12" v-if="isVisible('name')">
                    <div class="form-group">
                        <label class="control-label">
                            {{ getCompanyKey("sale_man_type_name_ar") }}
                            <span v-if="isRequired('name')" class="text-danger">*</span>
                        </label>
                        <div dir="rtl">
                            <input
                                type="text"
                                class="form-control arabicInput"
                                data-create="1"
                                @keypress.enter="moveInput('input', 'create', 2)"
                                v-model="$v.create.name.$model"
                                :class="{
                            'is-invalid': $v.create.name.$error || errors.name,
                            'is-valid': !$v.create.name.$invalid && !errors.name,
                          }"
                                @keyup="arabicValue(create.name)"
                            />
                        </div>
                        <div v-if="!$v.create.name.minLength" class="invalid-feedback">
                            {{ $t("general.Itmustbeatleast") }}
                            {{ $v.create.name.$params.minLength.min }}
                            {{ $t("general.letters") }}
                        </div>
                        <div v-if="!$v.create.name.maxLength" class="invalid-feedback">
                            {{ $t("general.Itmustbeatmost") }}
                            {{ $v.create.name.$params.maxLength.max }}
                            {{ $t("general.letters") }}
                        </div>
                        <template v-if="errors.name">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.name"
                                :key="index"
                            >
                                {{ errorMessage }}
                            </ErrorMessage>
                        </template>
                    </div>
                </div>
                <div class="col-md-12"  v-if="isVisible('name_e')">
                    <div class="form-group">
                        <label class="control-label">
                            {{ getCompanyKey("sale_man_type_name_en") }}
                            <span  v-if="isRequired('name_e')" class="text-danger">*</span>
                        </label>
                        <div dir="ltr">
                            <input
                                type="text"
                                class="form-control"
                                data-create="2"
                                @keypress.enter="moveInput('select', 'create', 3)"
                                v-model="$v.create.name_e.$model"
                                :class="{
                            'is-invalid': $v.create.name_e.$error || errors.name_e,
                            'is-valid': !$v.create.name_e.$invalid && !errors.name_e,
                          }"
                                @keyup="englishValue(create.name_e)"
                            />
                        </div>
                        <div v-if="!$v.create.name_e.minLength" class="invalid-feedback">
                            {{ $t("general.Itmustbeatleast") }}
                            {{ $v.create.name_e.$params.minLength.min }}
                            {{ $t("general.letters") }}
                        </div>
                        <div v-if="!$v.create.name_e.maxLength" class="invalid-feedback">
                            {{ $t("general.Itmustbeatmost") }}
                            {{ $v.create.name_e.$params.maxLength.max }}
                            {{ $t("general.letters") }}
                        </div>
                        <template v-if="errors.name_e">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.name_e"
                                :key="index"
                            >{{ errorMessage }}
                            </ErrorMessage>
                        </template>
                    </div>
                </div>
                <div class="col-md-12" v-if="isVisible('is_employee')" >
                    <div class="form-group">
                        <label class="mr-2">
                            {{ getCompanyKey("is_employee") }}
                            <span v-if="isRequired('is_employee')"  class="text-danger">*</span>
                        </label>
                        <b-form-group :class="{
                                      'is-invalid':
                                        $v.create.is_employee.$error || errors.is_employee,
                                      'is-valid':
                                        !$v.create.is_employee.$invalid && !errors.is_employee,
                                    }"
                        >
                            <b-form-radio
                                class="d-inline-block"
                                v-model="$v.create.is_employee.$model"
                                name="some-radios"
                                value="1"
                            >{{ $t("general.Yes") }}</b-form-radio
                            >
                            <b-form-radio
                                class="d-inline-block m-1"
                                v-model="$v.create.is_employee.$model"
                                name="some-radios"
                                value="0"
                            >{{ $t("general.No") }}</b-form-radio
                            >
                        </b-form-group>
                        <template v-if="errors.is_employee">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.is_employee"
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
</template>

<script>
import {maxLength, minLength, required, requiredIf} from "vuelidate/lib/validators";
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import adminApi from "../../../api/adminAxios";
import Swal from "sweetalert2";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import {arabicValue,englishValue} from "../../../helper/langTransform";
import successError from "../../../helper/mixin/success&error";
export default {
  name: "salesManType",
  mixins: [transMixinComp,successError],
  components: {
    ErrorMessage,
    loader,
  },
  mounted() {
    this.company_id = this.$store.getters["auth/company_id"];
  },
  props: {
        id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/salesmen-types'}
    },
  data() {
    return {
        fields: [],
        isCustom: false,
      create: {
        name: "",
        name_e: "",
        is_employee: 1,
      },
      is_disabled: false,
      isLoader: false,
      errors: {},
    };
  },
  validations: {
    create: {
        name: { required: requiredIf(function (model) {
                return this.isRequired("name");
            }), minLength: minLength(3), maxLength: maxLength(100) },
        name_e: { required: requiredIf(function (model) {
                return this.isRequired("name_e");
            }), minLength: minLength(3), maxLength: maxLength(100) },
        is_employee: { required: requiredIf(function (model) {
                return this.isRequired("is_employee");
            }) },
    },
  },
  methods: {
      getCustomTableFields() {
          this.isCustom = true;
           adminApi
              .get(`/customTable/table-columns/general_salesmen_types`)
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
          this.create = { name: "", name_e: "", is_employee: 1 };
          this.$nextTick(() => {
              this.$v.$reset();
          });
          this.errors = {};
          this.is_disabled = false;
      },
      resetModalHidden() {
         this.defaultData();
         this.$bvModal.hide(this.id);
      },
      resetModal() {
          this.defaultData();
          setTimeout( () => {
              if(this.type != 'edit'){
                  if(!this.isPage)  this.getCustomTableFields();
              }else {
                  if(this.idObjEdit.dataObj){
                      let module = this.idObjEdit.dataObj;
                      this.errors = {};
                      this.create.name = module.name;
                      this.create.name_e = module.name_e;
                      this.create.is_employee = module.is_employee == true ? 1 : 0;
                  }
              }
          },50);
      },
      resetForm() {
          this.defaultData();
      },
      AddSubmit() {
          if (!this.create.name) {
            this.create.name = this.create.name_e;
          }
          if (!this.create.name_e) {
            this.create.name_e = this.create.name;
          }
          this.$v.create.$touch();

          if (this.$v.create.$invalid) {
            return;
          } else {
            this.isLoader = true;
            this.errors = {};
              if (this.$v.create.$invalid) {
                  return;
              } else {
                  this.isLoader = true;
                  this.errors = {};
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
          }
      },
      arabicValue(txt){
          this.create.name = arabicValue(txt);
      } ,
      englishValue(txt){
          this.create.name_e = englishValue(txt);
      }
  },
};
</script>

<style scoped>
form {
    position: relative;
}
</style>
