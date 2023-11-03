<script>
import adminApi from "../../../api/adminAxios";
import { required, minLength, maxLength } from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../general/loader";
import Multiselect from "vue-multiselect";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import {arabicValue, englishValue} from "../../../helper/langTransform";

/**
 * Advanced Table component
 */
export default {
  components: {
    ErrorMessage,
    loader,
    Multiselect,
  },
  mixins: [transMixinComp],
 props: ["companyKeys", "defaultsKeys"],
  data() {
    return {
      parent: [],
      enabled3: false,
      isLoader: false,
            errors: {},

      create: {
        name: "",
        name_e: "",
      }, //Create form
      edit: {
        name: "",
        name_e: "",
      }, //Edit form
      isCheckAll: false,
      checkAll: [],
      is_disabled: false,
    };
  },
  validations: {
    create: {
      name: { required, minLength: minLength(3), maxLength: maxLength(255) },
      name_e: {
        required,
        minLength: minLength(3),
        maxLength: maxLength(255),
      },
    },
    edit: {
      name: { required, minLength: minLength(3), maxLength: maxLength(255) },
      name_e: {
        required,
        minLength: minLength(3),
        maxLength: maxLength(255),
      },
    },
  },
  methods: {
    /**
     *  reset Modal (create)
     */
    resetModalHidden() {
      this.create = { name: "", name_e: "" };
      this.$nextTick(() => {
        this.$v.$reset();
      });
        this.is_disabled = false;
      this.errors = {};
    },
    /**
     *  hidden Modal (create)
     */
    async resetModal() {
      this.create = { name: "", name_e: "" };
      this.is_disabled = false;
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
    },
    /**
     *  create document field
     */
    resetForm() {
      this.create = { name: "", name_e: "" };
      this.is_disabled = false;
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
          .post(`/arch-doc-status`, {
            ...this.create,
          })
          .then((res) => {
            this.is_disabled = true;
            this.$emit("created");
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
      arabicValue(txt){
          this.create.name = arabicValue(txt);
      },

      englishValue(txt){
          this.create.name_e = englishValue(txt);
      }
  },
};
</script>

<template>
  <!--  create   -->
  <b-modal
    id="arch-status-create"
    :title="getCompanyKey('arch_document_status_create_form')"
    title-class="font-18"
    body-class="p-4 "
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
        <b-button variant="danger" type="button" @click.prevent="$bvModal.hide(`arch-status-create`)">
          {{ $t("general.Cancel") }}
        </b-button>
        <!-- End Cancel Button Modal -->
      </div>
      <div class="row">
        <div class="col-md-6 direction" dir="rtl">
          <div class="form-group">
            <label for="field-1" class="control-label">
              {{ getCompanyKey("arch_document_status_name_ar") }}
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
              {{ getCompanyKey("arch_document_status_name_en") }}
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
      </div>
    </form>
  </b-modal>
  <!--  /create   -->
</template>
