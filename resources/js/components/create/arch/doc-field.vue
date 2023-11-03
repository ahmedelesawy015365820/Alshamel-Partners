<template>
    <dev>
        <!--  create   -->
        <propertyTree :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getProperties" />
        <b-modal
            id="create-doc-field"
            :title="getCompanyKey('documentFieldCreate')"
            title-class="font-18"
            body-class="p-4"
            :hide-footer="true"
            size="lg"
            @show="resetModal"
            @hidden="resetModalHidden"
        >
            <form>
                <div class="mb-3 d-flex justify-content-end">
                    <!-- Start Add New Record Button -->
                    <b-button
                        variant="success"
                        :disabled="!is_disabled"
                        @click.prevent="resetForm"
                        type="button"
                        :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : '']"
                    >
                        {{ $t("general.AddNewRecord") }}
                    </b-button>
                    <!-- End Add New Record Button -->
                    <!-- Emulate built in modal footer ok and cancel button actions -->
                    <template v-if="!is_disabled">
                        <!-- Start Add Button -->
                        <b-button
                            variant="success"
                            type="submit"
                            class="mx-1"
                            v-if="!isLoader"
                            @click.prevent="AddSubmit"
                        >
                            {{ $t("general.Add") }}
                        </b-button>
                        <!-- End Add Button -->
                        <b-button variant="success" class="mx-1" disabled v-else>
                            <b-spinner small></b-spinner>
                            <span class="sr-only">{{ $t("login.Loading") }}...</span>
                        </b-button>
                    </template>
                    <!-- Start Cancel Button Modal -->
                    <b-button variant="danger" type="button" @click.prevent="resetModalHidden">
                        {{ $t("general.Cancel") }}
                    </b-button>
                    <!-- End Cancel Button Modal -->
                </div>
                <div class="row">
                    <div class="col-md-6 direction" dir="rtl">
                        <div class="form-group">
                            <label for="field-1" class="control-label">
                                {{ getCompanyKey("documentFieldName") }}
                                <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                class="form-control arabicInput"
                                v-model="$v.create.name.$model"
                                :class="{
                                    'is-invalid': $v.create.name.$error || errors.name,
                                    'is-valid': !$v.create.name.$invalid && !errors.name,
                                  }"
                                @keyup="arabicValue(create.name)"
                                id="field-1"
                            />
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
                                <ErrorMessage v-for="(errorMessage, index) in errors.name" :key="index">{{
                                        errorMessage
                                    }}</ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6 direction-ltr" dir="ltr">
                        <div class="form-group">
                            <label for="field-2" class="control-label">
                                {{ getCompanyKey("documentFieldNameE") }}
                                <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                class="form-control englishInput"
                                v-model="$v.create.name_e.$model"
                                :class="{
                                    'is-invalid': $v.create.name_e.$error || errors.name_e,
                                    'is-valid': !$v.create.name_e.$invalid && !errors.name_e,
                                  }"
                                @keyup="englishValue(create.name_e)"
                                id="field-2"
                            />
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
                                <ErrorMessage v-for="(errorMessage, index) in errors.name_e" :key="index">{{
                                        errorMessage
                                    }}</ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>
                                {{ getCompanyKey("document-field-type") }}
                                <span class="text-danger">*</span>
                            </label>
                            <select
                                class="custom-select"
                                @change="lookUpChange"
                                v-model="$v.create.type.$model"
                                :class="{
                'is-invalid': $v.create.type.$error || errors.type,
                'is-valid': !$v.create.type.$invalid && !errors.type,
              }"
                            >
                                <option v-for="type in dataTypes" :key="type.id" :value="type.id">
                                    {{ $i18n.locale == "ar" ? type.name : type.name_e }}...
                                </option>
                            </select>

                            <template v-if="errors.type">
                                <ErrorMessage v-for="(errorMessage, index) in errors.type" :key="index">{{
                                        errorMessage
                                    }}</ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="create.type && create.type != 7">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey("arch_doc_field_character") }}
                                <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                v-model="$v.create.field_characters.$model"
                                :class="{
                'is-invalid':
                  $v.create.field_characters.$error || errors.field_characters,
                'is-valid':
                  !$v.create.field_characters.$invalid && !errors.field_characters,
              }"
                            />
                            <template v-if="errors.field_characters">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.field_characters"
                                    :key="index"
                                >{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div v-if="create.type == 10" class="col-md-6">
                        <div class="form-group">
                            <label>
                                {{ getCompanyKey("property") }}
                                <span class="text-danger">*</span>
                            </label>
                            <select
                                @change="showTreeCreate"
                                class="custom-select"
                                v-model="$v.create.tree_property_id.$model"
                                :class="{
                'is-invalid':
                  $v.create.tree_property_id.$error || errors.tree_property_id,
                'is-valid':
                  !$v.create.tree_property_id.$invalid && !errors.tree_property_id,
              }"
                            >
                                <option
                                    v-for="property in properties"
                                    :key="property.id"
                                    :value="property.id"
                                >
                                    {{ $i18n.locale == "ar" ? property.name : property.name_e }}
                                </option>
                            </select>

                            <template v-if="errors.type">
                                <ErrorMessage v-for="(errorMessage, index) in errors.type" :key="index">{{
                                        errorMessage
                                    }}</ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-show="showInput">
                        <div class="form-group position-relative">
                            <label class="control-label">
                                {{ getCompanyKey("document-field-lookup") }}
                                <span class="text-danger">*</span>
                            </label>
                            <multiselect
                                @input="getColumns(create.lookup_table)"
                                v-model="$v.create.lookup_table.$model"
                                :options="tables"
                                :custom-label="(opt) => opt"
                            >
                            </multiselect>
                            <div
                                v-if="$v.create.lookup_table.$error || errors.lookup_table"
                                class="text-danger"
                            >
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.lookup_table">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.lookup_table"
                                    :key="index"
                                >{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-show="create.lookup_table">
                        <div class="form-group position-relative">
                            <label class="control-label">
                                {{ getCompanyKey("document-field-lookup_column") }}
                                <span class="text-danger">*</span>
                            </label>
                            <multiselect
                                v-model="$v.create.lookup_table_column.$model"
                                :options="columns"
                                :custom-label="(opt) => opt"
                            >
                            </multiselect>
                            <div
                                v-if="$v.create.lookup_table_column.$error || errors.lookup_table_column"
                                class="text-danger"
                            >
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.lookup_table_column">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.lookup_table_column"
                                    :key="index"
                                >{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="mr-2">
                                {{ getCompanyKey("document-field-reference") }}
                                <span class="text-danger">*</span>
                            </label>
                            <b-form-group
                                :class="{
                'is-invalid': $v.create.is_reference.$error || errors.is_reference,
                'is-valid': !$v.create.is_reference.$invalid && !errors.is_reference,
              }"
                            >
                                <b-form-radio
                                    class="d-inline-block"
                                    v-model="$v.create.is_reference.$model"
                                    name="some-radios"
                                    value="1"
                                >{{ $t("general.isReferenceYes") }}</b-form-radio
                                >
                                <b-form-radio
                                    class="d-inline-block m-1"
                                    v-model="$v.create.is_reference.$model"
                                    name="some-radios"
                                    value="0"
                                >{{ $t("general.isReferenceNo") }}</b-form-radio
                                >
                            </b-form-group>
                            <template v-if="errors.is_reference">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.is_reference"
                                    :key="index"
                                >{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                </div>
            </form>
        </b-modal>
        <!--  /create   -->
    </dev>

</template>

<script>
import { maxLength, minLength, required, requiredIf } from "vuelidate/lib/validators";
import adminApi from "../../../api/adminAxios";
import Swal from "sweetalert2";
import Multiselect from "vue-multiselect";
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import propertyTree from "../general/property-tree";
import {arabicValue, englishValue} from "../../../helper/langTransform";

export default {
  name: "doc-field",
  mixins: [transMixinComp],
  props: ["companyKeys", "defaultsKeys"],
  data() {
    return {
      showInput: false,
      dataTypes: [],
      properties: [],
      create: {
        name: "",
        name_e: "",
        type: "",
        is_reference: 0,
        lookup_table: "",
        lookup_table_column: "",
        tree_property_id: null,
        field_characters: null,
      }, //Create form
      tables: [],
      columns: [],
      isLoader: false,
      is_disabled: false,
      errors: {},
    };
  },
  updated() {
    // $(".englishInput").keypress(function (event) {
    //   var ew = event.which;
    //   if (ew == 32) return true;
    //   if (48 <= ew && ew <= 57) return true;
    //   if (65 <= ew && ew <= 90) return true;
    //   if (97 <= ew && ew <= 122) return true;
    //   return false;
    // });
    // $(".arabicInput").keypress(function (event) {
    //   var ew = event.which;
    //   if (ew == 32) return true;
    //   if (48 <= ew && ew <= 57) return false;
    //   if (65 <= ew && ew <= 90) return false;
    //   if (97 <= ew && ew <= 122) return false;
    //   return true;
    // });
  },
  components: {
    Multiselect,
    ErrorMessage,
    loader,
      propertyTree
  },
  validations: {
    create: {
      name: { required, minLength: minLength(3), maxLength: maxLength(255) },
      name_e: {
        required,
        minLength: minLength(3),
        maxLength: maxLength(255),
      },
      type: { required },
      is_reference: { required },
      lookup_table: {},
      lookup_table_column: {},
      tree_property_id: {
        required: requiredIf(function (model) {
          return this.create.type == 10;
        }),
      },
      field_characters: {
        required: requiredIf(function (model) {
          return this.create.type != 7;
        }),
      },
    },
  },
  methods: {
      showTreeCreate(){
          if (this.create.tree_property_id == 0) {
              this.$bvModal.show("property-create");
              this.create.tree_property_id = null;
          }
      },
    async getProperties() {
      await adminApi
        .get(`/tree-properties/root-nodes`)
        .then((res) => {
            let l = res.data;
            l.unshift({ id: 0, name: "اضف خاصية", name_e: "Add property" });
          this.properties = l;
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
    resetModalHidden() {
      this.create = {
        name: "",
        name_e: "",
        type: "",
        is_reference: 0,
        lookup_table: "",
        lookup_table_column: "",
        tree_property_id: null,
        field_characters:null
      };
        this.is_disabled = false;
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
      this.$bvModal.hide(`create-doc-field`);
    },
    /**
     *  hidden Modal (create)
     */
    async resetModal() {
      await this.getTables();
      await this.getDataTypes();
      await this.getProperties();
      this.create = {
        name: "",
        name_e: "",
        type: "",
        is_reference: 0,
        lookup_table: "",
        lookup_table_column: "",
        tree_property_id: null,
        field_characters:null

      };
      this.is_disabled = false;
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.showInput = false;
      this.errors = {};
    },
    /**
     *  create document field
     */
    async resetForm() {
      await this.getTables();
      this.create = {
        name: "",
        name_e: "",
        type: "",
        is_reference: 0,
        lookup_table: "",
        lookup_table_column: "",
        tree_property_id: null,
        field_characters:null

      };
      this.is_disabled = false;
      this.showInput = false;
      this.$nextTick(() => {
        this.$v.$reset();
      });
    },
    /**
     *  add document field
     */
    AddSubmit() {
      if (this.create.name || this.create.name_e) {
        this.create.name = this.create.name ? this.create.name : this.create.name_e;
        this.create.name_e = this.create.name_e ? this.create.name_e : this.create.name;
      }
      this.$v.create.$touch();
      if (this.$v.create.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        this.is_disabled = false;
        adminApi
          .post(`/document-field`, {
            ...this.create,
            data_type_id: this.create.type,
            type: undefined,
            is_reference: this.create.is_reference == "1" ? 1 : 0,
              company_id: this.$store.getters["auth/company_id"]
          })
          .then((res) => {
            this.$emit("create");
            this.is_disabled = true;
            setTimeout(() => {
              Swal.fire({
                icon: "success",
                text: `${this.$t("general.Addedsuccessfully")}`,
                showConfirmButton: false,
                timer: 1500,
              });
            }, 500);
          })
          .catch((err) => {
            if (err.response.data) {
              this.errors = err.response.data.errors;
            } else {
              Swal.fire({
                icon: "error",
                title: `${this.$t("general.Error")}`,
                text: `${this.$t("general.Thereisanerrorinthesystem")}`,
              });
            }
          })
          .finally(() => {
            this.isLoader = false;
          });
      }
    },
    async getTables() {
      this.isLoader = true;

      await adminApi
        .get(`/document-field/tables`)
        .then((res) => {
          let l = res.data.data;
          this.tables = l;
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
    async getColumns(table) {
      this.isLoader = true;

      await adminApi
        .get(`/document-field/columns/${table}`)
        .then((res) => {
          let l = res.data.data;
          this.columns = l;
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
    getDataTypes() {
      this.isLoader = true;
      adminApi
        .get(`/data-types`)
        .then((res) => {
          this.dataTypes = res.data.data;
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
    lookUpChange() {
      if (this.create.type == 11) {
        this.showInput = true;
      } else {
        this.showInput = false;
      }
    },
      arabicValue(txt){
          this.create.name = arabicValue(txt);
      },

      englishValue(txt){
          this.create.name_e = englishValue(txt);
      }
  },
};
</script>

<style scoped></style>
