<template>
  <div>
    <!--  create   -->
    <b-modal
      id="arch-doc-type"
      :title="$t('GenArchDocType.AddGenArchDocType')"
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
          <b-button
            variant="danger"
            type="button"
            @click.prevent="$bvModal.hide(`arch-doc-type`)"
          >
            {{ $t("general.Cancel") }}
          </b-button>
          <!-- End Cancel Button Modal -->
        </div>
        <div class="row">
          <div class="col-md-6 direction" dir="rtl">
            <div class="form-group">
              <label for="field-1" class="control-label">
                {{ $t("general.Name") }}
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
                {{ $t("general.Name_en") }}
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
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.name_e"
                  :key="index"
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="inlineFormCustomSelectPref">
                {{ $t("general.parentId") }}
                <span class="text-danger">*</span>
              </label>

              <multiselect
                v-model="create.parent_id"
                :options="parent.map((type) => type.id)"
                :custom-label="
                  (opt) =>
                    $i18n.locale
                      ? parent.find((x) => x.id == opt).name
                      : parent.find((x) => x.id == opt).name_e
                "
              >
              </multiselect>

              <template v-if="errors.parent_id">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.parent_id"
                  :key="index"
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="mr-2">
                {{ $t("general.isValid") }}
                <span class="text-danger">*</span>
              </label>
              <b-form-group
                :class="{
                  'is-invalid': $v.create.is_valid.$error || errors.is_valid,
                  'is-valid': !$v.create.is_valid.$invalid && !errors.is_valid,
                }"
              >
                <b-form-radio
                  class="d-inline-block"
                  v-model="$v.create.is_valid.$model"
                  name="some-radios"
                  value="1"
                  >{{ $t("general.isReferenceYes") }}</b-form-radio
                >
                <b-form-radio
                  class="d-inline-block m-1"
                  v-model="$v.create.is_valid.$model"
                  name="some-radios"
                  value="0"
                  >{{ $t("general.isReferenceNo") }}</b-form-radio
                >
              </b-form-group>
              <template v-if="errors.is_valid">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.is_valid"
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
  </div>
</template>

<script>
import ErrorMessage from "../../widgets/errorMessage";
import Multiselect from "vue-multiselect";
import adminApi from "../../../api/adminAxios";
import Swal from "sweetalert2";
import { maxLength, minLength, required } from "vuelidate/lib/validators";
import loader from "../../general/loader";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import {arabicValue, englishValue} from "../../../helper/langTransform";

export default {
  name: "gen-arch-doc-type",
  mixins: [transMixinComp],
  props: ["companyKeys", "defaultsKeys"],

  components: {
    ErrorMessage,
    Multiselect,
    loader,
  },
  data() {
    return {
      create: {
        name: "",
        name_e: "",
        parent_id: "",
        is_valid: null,
      }, //Create form
      parent: [],
      errors: {},
      is_disabled: false,
      isLoader: false,
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
      is_valid: { required },
    },
  },
  methods: {
    async getParent() {
      await adminApi
        .get(`/gen-arch-doc-type`)
        .then((res) => {
          let l = res.data;
          this.parent = l.data;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        });
    },
    resetModalHidden() {
      this.create = { name: "", name_e: "", is_valid: "", parent_id: "" };
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
      // call parent api
      await this.getParent();
      this.create = { name: "", name_e: "", is_valid: "", parent_id: "" };
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
      this.create = { name: "", name_e: "", is_valid: "", parent_id: "" };
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
          .post(`/gen-arch-doc-type`, {
            ...this.create,
            is_valid: this.create.is_valid == "1" ? 1 : 0,
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
