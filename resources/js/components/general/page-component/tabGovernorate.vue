<template>
  <div>
    <!--  create   -->
    <b-modal
      :id="idCreate"
      :title="getCompanyKey('governorate_create_form')"
      title-class="font-18"
      size="lg"
      body-class="p-4 "
      :hide-footer="true"
      @show="resetModal"
      @hidden="resetModalHidden"
    >
      <form>
        <div class="mb-3 d-flex justify-content-end">
          <b-button
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
              {{ $t("general.Add") }}
            </b-button>

            <b-button variant="success" class="mx-1" disabled v-else>
              <b-spinner small></b-spinner>
              <span class="sr-only">{{ $t("login.Loading") }}...</span>
            </b-button>
          </template>
          <b-button @click.prevent="resetModalHidden" variant="danger" type="button">
            {{ $t("general.Cancel") }}
          </b-button>
        </div>
        <div class="row">
          <div class="col-md-6 direction" v-if="isVisible('name')" dir="rtl">
            <div class="form-group">
              <label for="field-1" class="control-label">
                {{ getCompanyKey("governorate_name_ar") }}
                <span v-if="isRequired('name')" class="text-danger">*</span>
              </label>
              <input
                @keyup="arabicValue(create.name)"
                type="text"
                class="form-control"
                data-create="1"
                v-model="$v.create.name.$model"
                :class="{
                  'is-invalid': $v.create.name.$error || errors.name,
                  'is-valid': !$v.create.name.$invalid && !errors.name,
                }"
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
                <ErrorMessage v-for="(errorMessage, index) in errors.name" :key="index"
                  >{{ errorMessage }}
                </ErrorMessage>
              </template>
            </div>
          </div>
          <div class="col-md-6 direction-ltr" v-if="isVisible('name_e')" dir="ltr">
            <div class="form-group">
              <label for="field-2" class="control-label">
                {{ getCompanyKey("governorate_name_en") }}
                <span v-if="isRequired('name_e')" class="text-danger">*</span>
              </label>
              <input
                @keyup="englishValue(create.name_e)"
                type="text"
                class="form-control"
                data-create="2"
                v-model="$v.create.name_e.$model"
                :class="{
                  'is-invalid': $v.create.name_e.$error || errors.name_e,
                  'is-valid': !$v.create.name_e.$invalid && !errors.name_e,
                }"
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
                <ErrorMessage v-for="(errorMessage, index) in errors.name_e" :key="index"
                  >{{ errorMessage }}
                </ErrorMessage>
              </template>
            </div>
          </div>
          <div class="col-md-6" v-if="isVisible('phone_key')">
                <div class="form-group">
                    <label for="field-5" class="control-label">
                        {{ getCompanyKey("governorate_phone_key") }}
                        <span v-if="isRequired('phone_key')" class="text-danger">*</span>
                    </label>
                    <input
                        type="number"
                        class="form-control"
                        data-create="4"
                        v-model="$v.create.phone_key.$model"
                        :class="{
                  'is-invalid': $v.create.phone_key.$error || errors.phone_key,
                  'is-valid': !$v.create.phone_key.$invalid && !errors.phone_key,
                }"
                        id="field-5"
                    />
                    <div v-if="!$v.create.phone_key.minLength" class="invalid-feedback">
                        {{ $t("general.Itmustbeatleast") }}
                        {{ $v.create.phone_key.$params.minLength.min }}
                        {{ $t("general.letters") }}
                    </div>
                    <div v-if="!$v.create.phone_key.maxLength" class="invalid-feedback">
                        {{ $t("general.Itmustbeatmost") }}
                        {{ $v.create.phone_key.$params.maxLength.max }}
                        {{ $t("general.letters") }}
                    </div>
                    <template v-if="errors.phone_key">
                        <ErrorMessage
                            v-for="(errorMessage, index) in errors.phone_key"
                            :key="index"
                        >{{ errorMessage }}
                        </ErrorMessage>
                    </template>
                </div>
            </div>
          <div class="col-md-6" v-if="isVisible('is_default')">
            <div class="form-group">
              <label class="mr-2" for="field-11">
                {{ getCompanyKey("governorate_default") }}
                <span v-if="isRequired('is_default')" class="text-danger">*</span>
              </label>
              <select
                class="custom-select mr-sm-2"
                id="field-11"
                data-create="5"
                v-model="$v.create.is_default.$model"
                :class="{
                  'is-invalid': $v.create.is_default.$error || errors.is_default,
                  'is-valid': !$v.create.is_default.$invalid && !errors.is_default,
                }"
              >
                <option value="" selected>{{ $t("general.Choose") }}...</option>
                <option value="1">{{ $t("general.Active") }}</option>
                <option value="0">{{ $t("general.Inactive") }}</option>
              </select>
              <template v-if="errors.is_default">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.is_default"
                  :key="index"
                  >{{ errorMessage }}
                </ErrorMessage>
              </template>
            </div>
          </div>
          <div class="col-md-6" v-if="isVisible('is_active')">
            <div class="form-group">
              <label class="mr-2">
                {{ getCompanyKey("governorate_status") }}
                <span v-if="isRequired('is_active')" class="text-danger">*</span>
              </label>
              <b-form-group
                :class="{
                  'is-invalid': $v.create.is_active.$error || errors.is_active,
                  'is-valid': !$v.create.is_active.$invalid && !errors.is_active,
                }"
              >
                <b-form-radio
                  class="d-inline-block"
                  v-model="$v.create.is_active.$model"
                  name="some-radios"
                  value="active"
                  >{{ $t("general.Active") }}</b-form-radio
                >
                <b-form-radio
                  class="d-inline-block m-1"
                  v-model="$v.create.is_active.$model"
                  name="some-radios"
                  value="inactive"
                  >{{ $t("general.Inactive") }}</b-form-radio
                >
              </b-form-group>
              <template v-if="errors.is_active">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.is_active"
                  :key="index"
                  >{{ errorMessage }}
                </ErrorMessage>
              </template>
            </div>
          </div>
        </div>
      </form>
    </b-modal>
    <!--  /edit   -->
    <b-modal
      id="tab-governorate-edit"
      :title="getCompanyKey('governorate_edit_form')"
      title-class="font-18"
      size="lg"
      body-class="p-4"
      :ref="`edit-${edit}`"
      :hide-footer="true"
      @hidden="resetModalHiddenEdit()"
    >
      <form>
        <div class="mb-3 d-flex justify-content-end">
          <!-- Emulate built in modal footer ok and cancel button actions -->
          <b-button
            variant="success"
            type="submit"
            class="mx-1"
            v-if="!isLoader"
            @click.prevent="editSubmit()"
          >
            {{ $t("general.Edit") }}
          </b-button>

          <b-button variant="success" class="mx-1" disabled v-else>
            <b-spinner small></b-spinner>
            <span class="sr-only">{{ $t("login.Loading") }}...</span>
          </b-button>

          <b-button
            variant="danger"
            type="button"
            @click.prevent="$bvModal.hide(`tab-governorate-edit`)"
          >
            {{ $t("general.Cancel") }}
          </b-button>
        </div>
        <div class="row">
          <div class="col-md-6 direction" v-if="isVisible('name')" dir="rtl">
            <div class="form-group">
              <label for="edit-1" class="control-label">
                {{ getCompanyKey("governorate_name_ar") }}
                <span v-if="isRequired('name')" class="text-danger">*</span>
              </label>
              <input
                @keyup="arabicValue(edit.name)"
                type="text"
                class="form-control"
                data-edit="1"
                v-model="$v.edit.name.$model"
                :class="{
                  'is-invalid': $v.edit.name.$error || errors.name,
                  'is-valid': !$v.edit.name.$invalid && !errors.name,
                }"
                id="edit-1"
              />
              <div v-if="!$v.edit.name.minLength" class="invalid-feedback">
                {{ $t("general.Itmustbeatleast") }}
                {{ $v.edit.name.$params.minLength.min }}
                {{ $t("general.letters") }}
              </div>
              <div v-if="!$v.edit.name.maxLength" class="invalid-feedback">
                {{ $t("general.Itmustbeatmost") }}
                {{ $v.edit.name.$params.maxLength.max }}
                {{ $t("general.letters") }}
              </div>
              <template v-if="errors.name">
                <ErrorMessage v-for="(errorMessage, index) in errors.name" :key="index"
                  >{{ errorMessage }}
                </ErrorMessage>
              </template>
            </div>
          </div>
          <div class="col-md-6 direction-ltr" v-if="isVisible('name_e')" dir="ltr">
            <div class="form-group">
              <label for="edit-2" class="control-label">
                {{ getCompanyKey("governorate_name_en") }}
                <span v-if="isRequired('name_e')" class="text-danger">*</span>
              </label>
              <input
                @keyup="englishValue(edit.name_e)"
                type="text"
                class="form-control"
                v-model="$v.edit.name_e.$model"
                :class="{
                  'is-invalid': $v.edit.name_e.$error || errors.name_e,
                  'is-valid': !$v.edit.name_e.$invalid && !errors.name_e,
                }"
                id="edit-2"
              />
              <div v-if="!$v.edit.name_e.minLength" class="invalid-feedback">
                {{ $t("general.Itmustbeatleast") }}
                {{ $v.edit.name_e.$params.minLength.min }}
                {{ $t("general.letters") }}
              </div>
              <div v-if="!$v.edit.name_e.maxLength" class="invalid-feedback">
                {{ $t("general.Itmustbeatmost") }}
                {{ $v.edit.name_e.$params.maxLength.max }}
                {{ $t("general.letters") }}
              </div>
              <template v-if="errors.name_e">
                <ErrorMessage v-for="(errorMessage, index) in errors.name_e" :key="index"
                  >{{ errorMessage }}
                </ErrorMessage>
              </template>
            </div>
          </div>
          <div class="col-md-6" v-if="isVisible('phone_key')">
                <div class="form-group">
                    <label for="edit-4" class="control-label">
                        {{ getCompanyKey("governorate_phone_key") }}
                        <span v-if="isRequired('phone_key')" class="text-danger">*</span>
                    </label>
                    <input
                        type="number"
                        class="form-control"
                        v-model="$v.edit.phone_key.$model"
                        :class="{
                  'is-invalid': $v.edit.phone_key.$error || errors.phone_key,
                  'is-valid': !$v.edit.phone_key.$invalid && !errors.phone_key,
                }"
                        id="edit-4"
                    />
                    <div v-if="!$v.edit.phone_key.minLength" class="invalid-feedback">
                        {{ $t("general.Itmustbeatleast") }}
                        {{ $v.edit.phone_key.$params.minLength.min }}
                        {{ $t("general.letters") }}
                    </div>
                    <div v-if="!$v.edit.phone_key.maxLength" class="invalid-feedback">
                        {{ $t("general.Itmustbeatmost") }}
                        {{ $v.edit.phone_key.$params.maxLength.max }}
                        {{ $t("general.letters") }}
                    </div>
                    <template v-if="errors.phone_key">
                        <ErrorMessage
                            v-for="(errorMessage, index) in errors.phone_key"
                            :key="index"
                        >{{ errorMessage }}
                        </ErrorMessage>
                    </template>
                </div>
            </div>
          <div class="col-md-6" v-if="isVisible('is_default')">
            <div class="form-group">
              <label class="mr-2" for="edit-11">
                {{ getCompanyKey("governorate_default") }}
                <span v-if="isRequired('is_default')" class="text-danger">*</span>
              </label>
              <select
                class="custom-select mr-sm-2"
                id="edit-11"
                v-model="$v.edit.is_default.$model"
                :class="{
                  'is-invalid': $v.edit.is_default.$error || errors.is_default,
                  'is-valid': !$v.edit.is_default.$invalid && !errors.is_default,
                }"
              >
                <option value="" selected>{{ $t("general.Choose") }}...</option>
                <option value="1">{{ $t("general.Active") }}</option>
                <option value="0">{{ $t("general.Inactive") }}</option>
              </select>
              <template v-if="errors.is_default">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.is_default"
                  :key="index"
                  >{{ errorMessage }}
                </ErrorMessage>
              </template>
            </div>
          </div>
          <div class="col-md-6" v-if="isVisible('is_active')">
            <div class="form-group">
              <label class="mr-2">
                {{ getCompanyKey("governorate_status") }}
                <span v-if="isRequired('is_active')" class="text-danger">*</span>
              </label>
              <b-form-group
                :class="{
                  'is-invalid': $v.edit.is_active.$error || errors.is_active,
                  'is-valid': !$v.edit.is_active.$invalid && !errors.is_active,
                }"
              >
                <b-form-radio
                  class="d-inline-block"
                  v-model="$v.edit.is_active.$model"
                  name="some-radios"
                  value="active"
                  >{{ $t("general.Active") }}</b-form-radio
                >
                <b-form-radio
                  class="d-inline-block m-1"
                  v-model="$v.edit.is_active.$model"
                  name="some-radios"
                  value="inactive"
                  >{{ $t("general.Inactive") }}</b-form-radio
                >
              </b-form-group>
              <template v-if="errors.is_active">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.is_active"
                  :key="index"
                  >{{ errorMessage }}
                </ErrorMessage>
              </template>
            </div>
          </div>

        </div>
      </form>
    </b-modal>
  </div>
</template>

<script>
import adminApi from "../../../api/adminAxios";
import {
  required,
  minLength,
  maxLength,
  integer,
  alpha,
  requiredIf,
} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import Switches from "vue-switches";
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../loader";
import Multiselect from "vue-multiselect";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import { arabicValue, englishValue } from "../../../helper/langTransform";

export default {
  components: {
    Switches,
    ErrorMessage,
    loader,
    Multiselect,
  },
  mixins: [transMixinComp],
    props: {
        idCreate: {
            default: "tab-governorate-create",
        },
        companyKeys: {
            default: [],
        },
        defaultsKeys: {
            default: [],
        },
        country_id: {
            default: null
        },
        edit: {
            default: null
        }
    },
  mounted() {
    this.getCustomTableFields();
    this.company_id = this.$store.getters["auth/company_id"];
  },
  updated() {
    // $(function () {
    //   $(".englishInput").keypress(function (event) {
    //     var ew = event.which;
    //     if (ew == 32) return true;
    //     if (48 <= ew && ew <= 57) return true;
    //     if (65 <= ew && ew <= 90) return true;
    //     if (97 <= ew && ew <= 122) return true;
    //     return false;
    //   });
    //   $(".arabicInput").keypress(function (event) {
    //     var ew = event.which;
    //     if (ew == 32) return true;
    //     if (48 <= ew && ew <= 57) return false;
    //     if (65 <= ew && ew <= 90) return false;
    //     if (97 <= ew && ew <= 122) return false;
    //     return true;
    //   });
    // });
  },
  validations: {
    create: {
      name: {
        required: requiredIf(function (model) {
          return this.isRequired("name");
        }),
        minLength: minLength(2),
        maxLength: maxLength(100),
      },
      name_e: {
        required: requiredIf(function (model) {
          return this.isRequired("name_e");
        }),
        minLength: minLength(2),
        maxLength: maxLength(100),
      },
      phone_key: {
        required: requiredIf(function (model) {
          return this.isRequired("phone_key");
        }),
        integer,
        minLength: minLength(1),
        maxLength: maxLength(10),
      },
      is_default: {
        required: requiredIf(function (model) {
          return this.isRequired("is_default");
        }),
        integer,
      },
      is_active: {
        required: requiredIf(function (model) {
          return this.isRequired("is_active");
        }),
      },
    },
    edit: {
      name: {
        required: requiredIf(function (model) {
          return this.isRequired("name");
        }),
        minLength: minLength(2),
        maxLength: maxLength(100),
      },
      name_e: {
        required: requiredIf(function (model) {
          return this.isRequired("name_e");
        }),
        minLength: minLength(2),
        maxLength: maxLength(100),
      },
      phone_key: {
        required: requiredIf(function (model) {
          return this.isRequired("phone_key");
        }),
        integer,
        minLength: minLength(1),
        maxLength: maxLength(10),
      },
      is_default: {
        required: requiredIf(function (model) {
          return this.isRequired("is_default");
        }),
        integer,
      },
      is_active: {
        required: requiredIf(function (model) {
          return this.isRequired("is_active");
        }),
      },
    },
  },
  data() {
    return {
      fields: [],
      isLoader: false,
      is_disabled: false,
      create: {
        name: "",
        name_e: "",
        phone_key: "",
        is_default: 0,
        is_active: "active",
      },
      errors: {},
    };
  },
  methods: {
    editSubmit() {
      if (this.edit.name || this.edit.name_e) {
        this.edit.name = this.edit.name ? this.edit.name : this.edit.name_e;
        this.edit.name_e = this.edit.name_e ? this.edit.name_e : this.edit.name;
      }

      this.$v.edit.$touch();

      if (this.$v.edit.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        adminApi
          .put(`/governorates/${this.edit.id}`, this.edit)
          .then((res) => {
            this.$bvModal.hide(`tab-governorate-edit`);
            this.$emit("updated");
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
    /**
     *   show Modal (edit)
     */
    /**
     *  hidden Modal (edit)
     */
    resetModalHiddenEdit() {
      this.edit = {
        name: "",
        name_e: "",
        phone_key: "",
        is_default: 0,
        is_active: "active",
      };
    },
    getCustomTableFields() {
      adminApi
        .get(`/customTable/table-columns/general_governorates`)
        .then((res) => {
          this.fields = res.data;
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
    isVisible(fieldName) {
      let res = this.fields.filter((field) => {
        return field.column_name == fieldName;
      });
      return res.length > 0 && res[0].is_visible == 1 ? true : false;
    },
    isRequired(fieldName) {
      let res = this.fields.filter((field) => {
        return field.column_name == fieldName;
      });
      return res.length > 0 && res[0].is_required == 1 ? true : false;
    },
    resetForm() {
      this.create = {
        name: "",
        name_e: "",
        phone_key: "",
        is_default: 0,
        is_active: "active",
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.is_disabled = false;
    },
    /**
     *  end  ckeckRow
     */
    resetModal() {
      this.create = {
        name: "",
        name_e: "",
        phone_key: "",
        is_default: 0,
        is_active: "active",
      };
      this.is_disabled = false;
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
    },
    /**
     *  create countrie
     */
    resetModalHidden() {
      this.create = {
        name: "",
        name_e: "",
        phone_key: "",
        is_default: 0,
        is_active: "active",
      };
      this.$bvModal.hide(`${this.idCreate}`);
    },
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
          .post(`/governorates`, {
            ...this.create,
            country_id: this.country_id,
          })
          .then((res) => {
            this.is_disabled = true;
            this.$emit("created", res.data.id);
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
    arabicValue(txt) {
      this.create.name = arabicValue(txt);
    },
    englishValue(txt) {
      this.create.name_e = englishValue(txt);
    },
  },
};
</script>
