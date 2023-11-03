<template>
  <!--  create   -->
  <b-modal
    :id="id"
    :title="getCompanyKey('country_create_form')"
    title-class="font-18"
    dialog-class="modal-full-width"
    :hide-footer="true"
    body-class="country"
    @show="resetModal"
    @hidden="resetModalHidden"
  >
    <form>
      <loader size="large" v-if="isCustom" />
      <div class="card">
        <div class="card-body">
          <div class="mt-1 d-flex justify-content-end">
            <!-- Emulate built in modal footer ok and cancel button actions -->
            <b-button
              variant="success"
              :disabled="!country_id"
              @click.prevent="resetForm"
              type="button"
              :class="['font-weight-bold px-2', country_id ? 'mx-2' : '']"
            >
              {{ $t("general.AddNewRecord") }}
            </b-button>

            <template v-if="!country_id">
              <b-button
                variant="success"
                type="button"
                class="mx-1 font-weight-bold px-3"
                v-if="!isLoader"
                @click.prevent="AddSubmit"
              >
                {{ $t("general.Save") }}
              </b-button>

              <b-button variant="success" class="mx-1" disabled v-else>
                <b-spinner small></b-spinner>
                <span class="sr-only">{{ $t("login.Loading") }}...</span>
              </b-button>
            </template>

            <b-button
              variant="danger"
              class="font-weight-bold"
              type="button"
              @click.prevent="resetModalHidden"
            >
              {{ $t("general.Cancel") }}
            </b-button>
          </div>
        </div>

        <b-tabs nav-class="nav-tabs nav-bordered">
          <b-tab :title="$t('general.DataEntry')" active>
            <div class="row">
              <div class="col-md-7">
                <div class="row">
                  <div class="col-md-6" v-if="isVisible('name_e')">
                              <div class="form-group">
                                <label for="field-1" class="control-label">
                                  {{ getCompanyKey("country_name_en") }}
                                  <span v-if="isRequired('name_e')" class="text-danger">*</span>
                                </label>
                                <multiselect v-model="$v.create.name_e.$model" @input="setCreateForm"
                                  :options="seederCountries.map((type) => type.nicename)" :custom-label="(opt) => opt">
                                </multiselect>
                                <div v-if="$v.create.name_e.$error" class="text-danger">
                                  {{ $t("general.fieldIsRequired") }}
                                </div>
                              </div>
                            </div>
                  <div class="col-md-6" v-if="isVisible('long_name_e')">
                    <div class="form-group">
                      <label for="field-4" class="control-label">
                        {{ getCompanyKey("country_long_name_en") }}
                        <span v-if="isRequired('long_name_e')" class="text-danger"
                          >*</span
                        >
                      </label>
                      <div dir="ltr">
                        <input
                        readonly
                          @keyup="englishValueLong(create.long_name_e)"
                          type="text"
                          class="form-control"
                          data-create="4"
                          @keypress.enter="moveInput('input', 'create', 5)"
                          v-model="$v.create.long_name_e.$model"
                          :class="{
                            'is-invalid':
                              $v.create.long_name_e.$error || errors.long_name_e,
                            'is-valid':
                              !$v.create.long_name_e.$invalid && !errors.long_name_e,
                          }"
                          id="field-4"
                        />
                      </div>
                      <div
                        v-if="!$v.create.long_name_e.minLength"
                        class="invalid-feedback"
                      >
                        {{ $t("general.Itmustbeatleast") }}
                        {{ $v.create.long_name_e.$params.minLength.min }}
                        {{ $t("general.letters") }}
                      </div>
                      <div
                        v-if="!$v.create.long_name_e.maxLength"
                        class="invalid-feedback"
                      >
                        {{ $t("general.Itmustbeatmost") }}
                        {{ $v.create.long_name_e.$params.maxLength.max }}
                        {{ $t("general.letters") }}
                      </div>
                      <template v-if="errors.long_name_e">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.long_name_e"
                          :key="index"
                          >{{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
                  <div class="col-md-6" v-if="isVisible('name')">
                    <div class="form-group">
                      <label for="field-1" class="control-label">
                        {{ getCompanyKey("country_name_ar") }}
                        <span v-if="isRequired('name')" class="text-danger">*</span>
                      </label>
                      <div dir="rtl">
                        <input
                          @keyup="arabicValue(create.name)"
                          type="text"
                          class="form-control"
                          data-create="1"
                          @keypress.enter="moveInput('input', 'create', 2)"
                          v-model="$v.create.name.$model"
                          :class="{
                            'is-invalid': $v.create.name.$error || errors.name,
                            'is-valid': !$v.create.name.$invalid && !errors.name,
                          }"
                          id="field-1"
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
                          >{{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
                  <div class="col-md-6" v-if="isVisible('long_name')">
                    <div class="form-group">
                      <label for="field-3" class="control-label">
                        {{ getCompanyKey("country_long_name_ar") }}
                        <span v-if="isRequired('long_name')" class="text-danger">*</span>
                      </label>
                      <div dir="rtl">
                        <input
                          @keyup="longArabicValue(create.long_name)"
                          type="text"
                          class="form-control"
                          data-create="3"
                          @keypress.enter="moveInput('input', 'create', 4)"
                          v-model="$v.create.long_name.$model"
                          :class="{
                            'is-invalid': $v.create.long_name.$error || errors.long_name,
                            'is-valid':
                              !$v.create.long_name.$invalid && !errors.long_name,
                          }"
                          id="field-3"
                        />
                      </div>
                      <div v-if="!$v.create.long_name.minLength" class="invalid-feedback">
                        {{ $t("general.Itmustbeatleast") }}
                        {{ $v.create.long_name.$params.minLength.min }}
                        {{ $t("general.letters") }}
                      </div>
                      <div v-if="!$v.create.long_name.maxLength" class="invalid-feedback">
                        {{ $t("general.Itmustbeatmost") }}
                        {{ $v.create.long_name.$params.maxLength.max }}
                        {{ $t("general.letters") }}
                      </div>
                      <template v-if="errors.long_name">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.long_name"
                          :key="index"
                          >{{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="row">
                  <div class="col-md-6" v-if="isVisible('national_id_length')">
                    <div class="form-group">
                      <label for="create-20" class="control-label">
                        {{ getCompanyKey("country_national_id") }}
                        <span v-if="isRequired('national_id_length')" class="text-danger"
                          >*</span
                        >
                      </label>
                      <input
                        type="number"
                        class="form-control input-Sender"
                        v-model.trim="$v.create.national_id_length.$model"
                        data-create="5"
                        @keypress.enter="moveInput('input', 'create', 6)"
                        :class="{
                          'is-invalid':
                            $v.create.national_id_length.$error ||
                            errors.national_id_length,
                          'is-valid':
                            !$v.create.national_id_length.$invalid &&
                            !errors.national_id_length,
                        }"
                        id="create-20"
                      />
                      <div
                        v-if="!$v.create.national_id_length.minLength"
                        class="invalid-feedback"
                      >
                        {{ $t("general.Itmustbeatleast") }}
                        {{ $v.create.national_id_length.$params.minLength.min }}
                        {{ $t("general.letters") }}
                      </div>
                      <div
                        v-if="!$v.create.national_id_length.maxLength"
                        class="invalid-feedback"
                      >
                        {{ $t("general.Itmustbeatmost") }}
                        {{ $v.create.national_id_length.$params.maxLength.max }}
                        {{ $t("general.letters") }}
                      </div>
                      <template v-if="errors.national_id_length">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.national_id_length"
                          :key="index"
                          >{{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
                  <div class="col-md-6" v-if="isVisible('short_code')">
                    <div class="form-group">
                      <label for="field-4" class="control-label">
                        {{ getCompanyKey("country_short_code") }}
                        <span v-if="isRequired('short_code')" class="text-danger">*</span>
                      </label>
                      <input
                      readonly
                        type="text"
                        class="form-control"
                        data-create="7"
                        @keypress.enter="moveInput('select', 'create', 8)"
                        v-model="$v.create.short_code.$model"
                        :class="{
                          'is-invalid': $v.create.short_code.$error || errors.short_code,
                          'is-valid':
                            !$v.create.short_code.$invalid && !errors.short_code,
                        }"
                        id="field-6"
                      />
                      <div
                        v-if="!$v.create.short_code.minLength"
                        class="invalid-feedback"
                      >
                        {{ $t("general.Itmustbeatleast") }}
                        {{ $v.create.short_code.$params.minLength.min }}
                        {{ $t("general.letters") }}
                      </div>
                      <div
                        v-if="!$v.create.short_code.maxLength"
                        class="invalid-feedback"
                      >
                        {{ $t("general.Itmustbeatmost") }}
                        {{ $v.create.short_code.$params.maxLength.max }}
                        {{ $t("general.letters") }}
                      </div>
                      <template v-if="errors.short_code">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.short_code"
                          :key="index"
                          >{{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
                  <div class="col-md-6" v-if="isVisible('phone_key')">
                    <div class="form-group">
                      <label for="field-4" class="control-label">
                        {{ getCompanyKey("country_phone_key") }}
                        <span v-if="isRequired('phone_key')" class="text-danger">*</span>
                      </label>
                      <input
                      readonly
                        type="number"
                        class="form-control"
                        data-create="6"
                        @keypress.enter="moveInput('input', 'create', 7)"
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
                        {{ getCompanyKey("country_default") }}
                        <span v-if="isRequired('is_default')" class="text-danger">*</span>
                      </label>
                      <select
                        class="custom-select mr-sm-2"
                        id="field-11"
                        data-create="8"
                        @keypress.enter.prevent="moveInput('select', 'create', 9)"
                        v-model="$v.create.is_default.$model"
                        :class="{
                          'is-invalid': $v.create.is_default.$error || errors.is_default,
                          'is-valid':
                            !$v.create.is_default.$invalid && !errors.is_default,
                        }"
                      >
                        <option value="" selected>{{ $t("general.Choose") }}...</option>
                        <option value="1">{{ $t("general.Yes") }}</option>
                        <option value="0">{{ $t("general.No") }}</option>
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
                  <div class="col-md-12" v-if="isVisible('is_active')">
                    <div class="form-group">
                      <label class="mr-2">
                        {{ getCompanyKey("country_status") }}
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
              </div>
            </div>
          </b-tab>
          <b-tab :disabled="!country_id" :title="$t('general.ImageUploads')">
            <div class="row">
              <input
                accept="image/png, image/gif, image/jpeg, image/jpg"
                type="file"
                id="uploadImageCreate"
                @change.prevent="onImageChanged"
                class="input-file-upload position-absolute"
                :class="[
                  'd-none',
                  {
                    'is-invalid': $v.create.media.$error || errors.media,
                    'is-valid': !$v.create.media.$invalid && !errors.media,
                  },
                ]"
              />
              <div class="col-md-8 my-1">
                <!-- file upload -->
                <div class="row align-content-between" style="width: 100%; height: 100%">
                  <div class="col-12">
                    <div class="d-flex flex-wrap">
                      <div
                        :class="['dropzone-previews col-4 position-relative mb-2']"
                        v-for="(photo, index) in images"
                        :key="photo.id"
                      >
                        <div
                          :class="[
                            'card mb-0 shadow-none border',
                            images.length - 1 == index ? 'bg-primary' : '',
                          ]"
                        >
                          <div class="p-2">
                            <div class="row align-items-center">
                              <div class="col-auto" @click="showPhoto = photo.webp">
                                <img
                                  data-dz-thumbnail
                                  :src="photo.webp"
                                  class="avatar-sm rounded bg-light"
                                  @error="src = './images/img-1.png'"
                                />
                              </div>
                              <div class="col pl-0">
                                <a
                                  href="javascript:void(0);"
                                  :class="[
                                    'font-weight-bold',
                                    images.length - 1 == index
                                      ? 'text-white'
                                      : 'text-muted',
                                  ]"
                                  data-dz-name
                                >
                                  {{ photo.name }}
                                </a>
                              </div>
                              <!-- Button -->
                              <a
                                href="javascript:void(0);"
                                :class="[
                                  'btn-danger dropzone-close',
                                  $i18n.locale == 'ar' ? 'dropzone-close-rtl' : '',
                                ]"
                                data-dz-remove
                                @click.prevent="deleteImageCreate(photo.id, index)"
                              >
                                <i class="fe-x"></i>
                              </a>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="footer-image col-12">
                    <b-button
                      @click="changePhoto"
                      variant="success"
                      type="button"
                      class="mx-1 font-weight-bold px-3"
                      v-if="!isLoader"
                    >
                      {{ $t("general.Add") }}
                    </b-button>
                    <b-button variant="success" class="mx-1" disabled v-else>
                      <b-spinner small></b-spinner>
                      <span class="sr-only">{{ $t("login.Loading") }}...</span>
                    </b-button>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="show-dropzone">
                  <img
                    :src="showPhoto"
                    class="img-thumbnail"
                    @error="src = './images/img-1.png'"
                  />
                </div>
              </div>
            </div>
          </b-tab>
        </b-tabs>
      </div>
    </form>
  </b-modal>
  <!--  /create   -->
</template>

<script>
import adminApi from "../../../api/adminAxios";
import Multiselect from "vue-multiselect";

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
import loader from "../../general/loader";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import { arabicValue, englishValue } from "../../../helper/langTransform";

export default {
  components: {
    Switches,
    ErrorMessage,
    loader,
    Multiselect
  },
  mixins: [transMixinComp],
  props: {
    id: {
      default: "country-create",
    },
    companyKeys: {
      default: [],
    },
    defaultsKeys: {
      default: [],
    },
  },
  mounted() {
    this.company_id = this.$store.getters["auth/company_id"];
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
      long_name: {
        required: requiredIf(function (model) {
          return this.isRequired("long_name");
        }),
        minLength: minLength(2),
        maxLength: maxLength(100),
      },
      long_name_e: {
        required: requiredIf(function (model) {
          return this.isRequired("long_name_e");
        }),
        minLength: minLength(2),
        maxLength: maxLength(100),
      },
      short_code: {
        required: requiredIf(function (model) {
          return this.isRequired("short_code");
        }),
        alpha,
        minLength: minLength(1),
        maxLength: maxLength(10),
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
      national_id_length: {
        required: requiredIf(function (model) {
          return this.isRequired("national_id_length");
        }),
        integer,
        minLength: minLength(1),
        maxLength: maxLength(2),
      },
      is_active: {
        required: requiredIf(function (model) {
          return this.isRequired("is_active");
        }),
      },
      media: {},
    },
  },
  data() {
    return {
      fields: [],
      country_id: null,
      isLoader: false,
      errors: {},
      images: [],
      media: {},
      saveImageName: [],
      showPhoto: "./images/img-1.png",
      changeImage: false,
      idDelete: null,
      seederCountries: [],
        isCustom: false,
      create: {
        name: "",
        name_e: "",
        long_name: "",
        long_name_e: "",
        short_code: "",
        phone_key: "",
        national_id_length: null,
        is_default: 0,
        media: [],
        is_active: "active",
      },
    };
  },
  methods: {
    setCreateForm(nicename) {
      let country = this.seederCountries.filter(el => el.nicename == nicename)[0];
      this.create.long_name_e = country.name;
      this.create.short_code = country.iso;
      this.create.phone_key = country.phonecode;
    },
    getCountrySeeder() {
      adminApi
        .get(`/countries/seeder`)
        .then((res) => {
          this.seederCountries = res.data;
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
    async getCustomTableFields() {
      this.isCustom = true;
      await adminApi
        .get(`/customTable/table-columns/general_countries`)
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
          this.isCustom = false;
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
    /**
     *  reset Modal (create)
     */
    resetModalHidden() {
      this.create = {
        name: "",
        name_e: "",
        long_name: "",
        long_name_e: "",
        short_code: "",
        phone_key: "",
        national_id_length: null,
        is_default: 0,
        is_active: "active",
        media: null,
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.images = [];
      this.country_id = null;
      this.errors = {};
      this.$bvModal.hide(this.id);
    },
    /**
     *  hidden Modal (create)
     */
    async resetModal() {
      await this.getCustomTableFields();
      this.getCountrySeeder();
      this.create = {
        name: "",
        name_e: "",
        long_name: "",
        long_name_e: "",
        short_code: "",
        phone_key: "",
        national_id_length: null,
        is_default: 0,
        is_active: "active",
      };
      this.showPhoto = "./images/img-1.png";
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.country_id = null;
      this.media = {};
      this.images = [];
      this.errors = {};
    },
    /**
     *  create countrie
     */
    resetForm() {
      this.create = {
        name: "",
        name_e: "",
        long_name: "",
        long_name_e: "",
        short_code: "",
        phone_key: "",
        national_id_length: null,
        is_default: 0,
        is_active: "active",
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.country_id = null;
      this.media = {};
      this.images = [];
    },
    AddSubmit() {
      if (!this.create.name) {
        this.create.name = this.create.name_e;
      }
      if (!this.create.name_e) {
        this.create.name_e = this.create.name;
      }
      if (!this.create.long_name) {
        this.create.long_name = this.create.long_name_e;
      }
      if (!this.create.long_name_e) {
        this.create.long_name_e = this.create.long_name;
      }

      this.$v.create.$touch();

      if (this.$v.create.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};

        adminApi
          .post(`/countries`, { ...this.create, company_id: this.company_id })
          .then((res) => {
            this.country_id = res.data.data.id;
            this.$emit("created");
            setTimeout(() => {
              Swal.fire({
                icon: "success",
                text: `${this.$t("general.Addedsuccessfully")}`,
                showConfirmButton: false,
                timer: 1500,
              });
            }, 200);
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
     *  end  ckeckRow
     */
    moveInput(tag, c, index) {
      document.querySelector(`${tag}[data-${c}='${index}']`).focus();
    },
    /**
     *  start Image ceate
     */
    changePhoto() {
      document.getElementById("uploadImageCreate").click();
    },
    changePhotoEdit() {
      document.getElementById("uploadImageEdit").click();
    },
    onImageChanged(e) {
      const file = e.target.files[0];
      this.addImage(file);
    },
    addImage(file) {
      this.media = file; //upload
      if (file) {
        this.idDelete = null;
        let is_media = this.images.find(
          (e) => e.name == file.name.slice(0, file.name.indexOf("."))
        );
        this.idDelete = is_media ? is_media.id : null;
        if (!this.idDelete) {
          this.isLoader = true;
          let formDate = new FormData();
          formDate.append("media[0]", this.media);
          adminApi
            .post(`/media`, formDate)
            .then((res) => {
              let old_media = [];
              this.images.forEach((e) => old_media.push(e.id));
              let new_media = [];
              res.data.data.forEach((e) => new_media.push(e.id));

              adminApi
                .put(`/countries/${this.country_id}`, { old_media, media: new_media })
                .then((res) => {
                  this.images = res.data.data.media;
                  this.showPhoto = this.images[this.images.length - 1].webp;
                })
                .catch((err) => {
                  Swal.fire({
                    icon: "error",
                    title: `${this.$t("general.Error")}`,
                    text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                  });
                });
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
        } else {
          Swal.fire({
            title: `${this.$t("general.Thisfilehasalreadybeenuploaded")}`,
            type: "warning",
            showCancelButton: true,
            confirmButtonText: `${this.$t("general.Replace")}`,
            cancelButtonText: `${this.$t("general.Nocancel")}`,
            confirmButtonClass: "btn btn-success mt-2",
            cancelButtonClass: "btn btn-danger ml-2 mt-2",
            buttonsStyling: false,
          }).then((result) => {
            if (result.value) {
              this.isLoader = true;
              let formDate = new FormData();
              formDate.append("media[0]", this.media);
              adminApi
                .post(`/media`, formDate)
                .then((res) => {
                  let old_media = [];
                  this.images.forEach((e) => old_media.push(e.id));
                  old_media.splice(old_media.indexOf(this.idDelete), 1);
                  let new_media = [];
                  res.data.data.forEach((e) => new_media.push(e.id));

                  adminApi
                    .put(`/countries/${this.country_id}`, { old_media, media: new_media })
                    .then((res) => {
                      this.images = res.data.data.media;
                      this.showPhoto = this.images[this.images.length - 1].webp;
                    })
                    .catch((err) => {
                      Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                      });
                    });
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
          });
        }
      }
    },
    deleteImageCreate(id, index) {
      let old_media = [];
      this.images.forEach((e) => {
        if (e.id != id) {
          old_media.push(e.id);
        }
      });
      adminApi
        .put(`/countries/${this.country_id}`, { old_media })
        .then((res) => {
          this.images = res.data.data.media;
          this.showPhoto = this.images[this.images.length - 1].webp;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        });
    },
    /**
     *  end Image ceate
     */

    arabicValue(txt) {
      this.create.name = arabicValue(txt);
    },
    arabicValueLong(txt) {
      this.create.long_name = arabicValue(txt);
    },

    englishValue(txt) {
      this.create.name_e = englishValue(txt);
    },
    englishValueLong(txt) {
      this.create.long_name_e = englishValue(txt);
    },
  },
};
</script>

<style>
form {
    position: relative;
}

.modal-dialog .card {
  margin: 0 !important;
}
.country.modal-body {
  padding: 0 !important;
}
.modal-dialog .card-body {
  padding: 1.5rem 1.5rem 0 1.5rem !important;
}
.nav-bordered {
  border: unset !important;
}
.nav {
  background-color: #dff0fe;
}
.tab-content {
  padding: 70px 60px 40px;
  min-height: 300px;
  background-color: #f5f5f5;
  position: relative;
}
.nav-tabs .nav-link {
  border: 1px solid #b7b7b7 !important;
  background-color: #d7e5f2;
  border-bottom: 0 !important;
  margin-bottom: 1px;
}

.nav-tabs .nav-link.active,
.nav-tabs .nav-item.show .nav-link {
  color: #000;
  background-color: hsl(0deg 0% 96%);
  border-bottom: 0 !important;
}

.img-thumbnail {
  max-height: 400px !important;
}
</style>
