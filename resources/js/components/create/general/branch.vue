<template>
  <!--  create   -->
  <b-modal
    :id="id"
    :title="type != 'edit'?getCompanyKey('branch_create_form'):getCompanyKey('branch_edit_form')"
    title-class="font-18"
    body-class="p-4 "
    dialog-class="modal-full-width"
    :hide-footer="true"
    @show="resetModal"
    @hidden="resetModalHidden"
  >
    <form>
      <loader size="large" v-if="isCustom && !isPage" />
        <div class="card">
            <div class="card-body">
                <div class="mb-1 d-flex justify-content-end">
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
            </div>
            <b-tabs nav-class="nav-tabs nav-bordered">
                <b-tab :title="$t('general.DataEntry')" active>
                    <div class="row">
                        <div v-if="isVisible('name')" class="col-md-6">
                            <div class="form-group">
                                <label for="field-1" class="control-label">
                                    {{ getCompanyKey("branch_name_ar") }}
                                    <span v-if="isRequired('name')" class="text-danger">*</span>
                                </label>
                                <div dir="rtl">
                                    <input
                                        type="text"
                                        class="form-control arabicInput"
                                        data-create="1"
                                        v-model="$v.create.name.$model"
                                        :class="{
                  'is-invalid': $v.create.name.$error || errors.name,
                  'is-valid': !$v.create.name.$invalid && !errors.name,
                }"
                                        @keyup="arabicValue(create.name)"
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
                                    >{{ errorMessage }}</ErrorMessage
                                    >
                                </template>
                            </div>
                        </div>
                        <div v-if="isVisible('name_e')" class="col-md-6">
                            <div class="form-group">
                                <label for="field-2" class="control-label">
                                    {{ getCompanyKey("branch_name_en") }}
                                    <span v-if="isRequired('name_e')" class="text-danger">*</span>
                                </label>
                                <div dir="ltr">
                                    <input
                                        type="text"
                                        class="form-control englishInput"
                                        data-create="2"
                                        v-model="$v.create.name_e.$model"
                                        :class="{
                  'is-invalid': $v.create.name_e.$error || errors.name_e,
                  'is-valid': !$v.create.name_e.$invalid && !errors.name_e,
                }"
                                        @keyup="englishValue(create.name_e)"
                                        id="field-2"
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
                                    >{{ errorMessage }}</ErrorMessage
                                    >
                                </template>
                            </div>
                        </div>
                        <div class="col-md-6" v-if="isVisible('parent_id')">
                            <div class="form-group">
                                <label>
                                    {{ getCompanyKey("branch_parent") }}
                                </label>
                                <multiselect
                                    v-model="create.parent_id"
                                    :options="parent.map((type) => type.id)"
                                    :custom-label="
                (opt) => parent.find((x) => x.id == opt)?
                  $i18n.locale == 'ar'
                    ? parent.find((x) => x.id == opt).name
                    : parent.find((x) => x.id == opt).name_e: null
              "
                                    :class="{ 'is-invalid': errors.parent_id }"
                                >
                                </multiselect>

                                <template v-if="errors.parent_id">
                                    <ErrorMessage
                                        v-for="(errorMessage, index) in errors.parent_id"
                                        :key="index"
                                    >{{ errorMessage }}
                                    </ErrorMessage>
                                </template>
                            </div>
                        </div>
                        <div class="col-md-6" v-if="isVisible('email')">
                            <div class="form-group">
                                <label class="control-label">
                                    {{ getCompanyKey("branch_email") }}
                                    <span v-if="isRequired('email')" class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    class="form-control"
                                    data-create="9"
                                    v-model="$v.create.email.$model"
                                    :class="{
                          'is-invalid': $v.create.email.$error || errors.email,
                          'is-valid': !$v.create.email.$invalid && !errors.email,
                        }"
                                />
                                <template v-if="errors.email">
                                    <ErrorMessage
                                        v-for="(errorMessage, index) in errors.email"
                                        :key="index"
                                    >
                                        {{ errorMessage }}
                                    </ErrorMessage>
                                </template>
                            </div>
                        </div>
                        <div class="col-md-6" v-if="isVisible('phone')">
                            <div class="form-group">
                                <label class="control-label">
                                    {{ getCompanyKey("branch_phone") }}
                                    <span v-if="isRequired('phone')" class="text-danger">*</span>
                                </label>
                                <VuePhoneNumberInput
                                    v-model="$v.create.phone.$model"
                                    :default-country-code="codeCountry"
                                    valid-color="#28a745"
                                    error-color="#dc3545"
                                    :preferred-countries="['FR', 'EG', 'DE']"
                                    @update="updatePhone"
                                />
                                <div
                                    v-if="$v.create.phone.$error || errors.phone"
                                    class="text-danger"
                                >
                                    {{ $t("general.fieldIsRequired") }}
                                </div>
                                <template v-if="errors.phone">
                                    <ErrorMessage
                                        v-for="(errorMessage, index) in errors.phone"
                                        :key="index"
                                    >{{ errorMessage }}
                                    </ErrorMessage>
                                </template>
                            </div>
                        </div>
                        <div class="col-md-6" v-if="isVisible('second_phone')">
                            <div class="form-group">
                                <label class="control-label">
                                    {{ getCompanyKey("branch_second_phone") }}
                                    <span v-if="isRequired('second_phone')" class="text-danger">*</span>
                                </label>
                                <VuePhoneNumberInput
                                    v-model="$v.create.second_phone.$model"
                                    :default-country-code="codeCountry"
                                    valid-color="#28a745"
                                    error-color="#dc3545"
                                    :preferred-countries="['FR', 'EG', 'DE']"
                                    @update="updateWhatsapp"
                                />
                                <div
                                    v-if="$v.create.second_phone.$error || errors.second_phone"
                                    class="text-danger"
                                >
                                    {{ $t("general.fieldIsRequired") }}
                                </div>
                                <template v-if="errors.second_phone">
                                    <ErrorMessage
                                        v-for="(errorMessage, index) in errors.whatsapp"
                                        :key="index"
                                    >{{ errorMessage }}
                                    </ErrorMessage>
                                </template>
                            </div>
                        </div>
                        <div v-if="isVisible('address')" class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">
                                    {{ getCompanyKey("branch_address") }}
                                    <span v-if="isRequired('address')" class="text-danger">*</span>
                                </label>
                                <div>
                                    <input
                                        type="text"
                                        class="form-control arabicInput"
                                        data-create="1"
                                        v-model="$v.create.address.$model"
                                        :class="{
                              'is-invalid': $v.create.address.$error || errors.address,
                              'is-valid': !$v.create.address.$invalid && !errors.address,
                            }"
                                    />
                                </div>
                                <template v-if="errors.address">
                                    <ErrorMessage
                                        v-for="(errorMessage, index) in errors.address"
                                        :key="index"
                                    >{{ errorMessage }}</ErrorMessage
                                    >
                                </template>
                            </div>
                        </div>
                        <div v-if="isVisible('fax')" class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">
                                    {{ getCompanyKey("branch_fax") }}
                                    <span v-if="isRequired('fax')" class="text-danger">*</span>
                                </label>
                                <div>
                                    <input
                                        type="text"
                                        class="form-control"
                                        v-model="$v.create.fax.$model"
                                        :class="{
                              'is-invalid': $v.create.fax.$error || errors.fax,
                              'is-valid': !$v.create.fax.$invalid && !errors.fax,
                            }"
                                    />
                                </div>
                                <template v-if="errors.fax">
                                    <ErrorMessage
                                        v-for="(errorMessage, index) in errors.fax"
                                        :key="index"
                                    >{{ errorMessage }}</ErrorMessage
                                    >
                                </template>
                            </div>
                        </div>
                        <div v-if="isVisible('p_o_pox')" class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">
                                    {{ getCompanyKey("branch_p_o_pox") }}
                                    <span v-if="isRequired('p_o_pox')" class="text-danger">*</span>
                                </label>
                                <div>
                                    <input
                                        type="text"
                                        class="form-control"
                                        v-model="$v.create.p_o_pox.$model"
                                        :class="{
                              'is-invalid': $v.create.p_o_pox.$error || errors.p_o_pox,
                              'is-valid': !$v.create.p_o_pox.$invalid && !errors.p_o_pox,
                            }"
                                    />
                                </div>
                                <template v-if="errors.p_o_pox">
                                    <ErrorMessage
                                        v-for="(errorMessage, index) in errors.p_o_pox"
                                        :key="index"
                                    >{{ errorMessage }}</ErrorMessage
                                    >
                                </template>
                            </div>
                        </div>
                        <div v-if="isVisible('is_active')" class="col-md-6">
                            <div class="form-group">
                                <label class="mr-2">
                                    {{ getCompanyKey("branch_status") }}
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
                                        :value="1"
                                    >{{ $t("general.Active") }}</b-form-radio
                                    >
                                    <b-form-radio
                                        class="d-inline-block m-1"
                                        v-model="$v.create.is_active.$model"
                                        name="some-radios"
                                        :value="0"
                                    >{{ $t("general.Inactive") }}</b-form-radio
                                    >
                                </b-form-group>
                                <template v-if="errors.is_active">
                                    <ErrorMessage
                                        v-for="(errorMessage, index) in errors.is_active"
                                        :key="index"
                                    >{{ errorMessage }}</ErrorMessage
                                    >
                                </template>
                            </div>
                        </div>
                    </div>
                </b-tab>
                <b-tab :disabled="!branch_id" :title="$t('general.ImageUploads')">
                    <div class="row">
                        <input
                            accept="image/png, image/gif, image/jpeg, image/jpg"
                            type="file"
                            id="uploadImageCreate"
                            @change.prevent="onImageChanged"
                            class="input-file-upload position-absolute"
                            :class="['d-none',]"
                        />
                        <div class="col-md-8 my-1">
                            <!-- file upload -->
                            <div
                                class="row align-content-between"
                                style="width: 100%; height: 100%"
                            >
                                <div class="col-12">
                                    <div class="d-flex flex-wrap">
                                        <div
                                            :class="[
                                    'dropzone-previews col-4 position-relative mb-2',
                                  ]"
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
                                                        <div
                                                            class="col-auto"
                                                            @click="showPhoto = photo.webp"
                                                        >
                                                            <img
                                                                data-dz-thumbnail
                                                                :src="photo.webp"
                                                                class="avatar-sm rounded bg-light"
                                                                @error="src = img"
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
                                            $i18n.locale == 'ar'
                                              ? 'dropzone-close-rtl'
                                              : '',
                                          ]"
                                                            data-dz-remove
                                                            @click.prevent="
                                            deleteImageCreate(photo.id, index)
                                          "
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
                                    @error="src = img"
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
import {
  maxLength,
  minLength,
  requiredIf,
  email
} from "vuelidate/lib/validators";
import img from "../../../assets/images/img-1.png";
import adminApi from "../../../api/adminAxios";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import { arabicValue, englishValue } from "../../../helper/langTransform";
import ErrorMessage from "../../widgets/errorMessage";
import Multiselect from "vue-multiselect";
import loader from "../../general/loader";
import successError from "../../../helper/mixin/success&error";

export default {
  name: "branch",
  mixins: [transMixinComp,successError],
  props: {
      id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
      isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
      type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/branches'}
  },
  mounted() {
      this.company_id = this.$store.getters["auth/company_id"];
      this.$store.dispatch('locationIp/getIp');
  },
  components: {
    ErrorMessage,
    Multiselect,
    loader,
  },
  data() {
    return {
      fields: [],
      parent: [],
      errors: {},
      isCustom: false,
      is_disabled: false,
      codeCountry: "",
      isLoader: false,
      create: {
        name: "",
        name_e: "",
        is_active: 1,
        parent_id: null,
        phone: '',
        second_phone: '',
        fax: '',
          p_o_pox: '',
        Address: '',
        email: '',
        code_country: "",
          media: []
      },
      showPhoto: img,
      idDelete: null,
      images: [],
      media: {},
      branch_id: null
    };
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
      is_active: {
        required: requiredIf(function (model) {
          return this.isRequired("is_active");
        }),
      },
      phone: {required: requiredIf(function (model) {
              return this.isRequired("phone");
          }),},
      second_phone: {required: requiredIf(function (model) {
              return this.isRequired("second_phone");
          }),},
      fax: {required: requiredIf(function (model) {
              return this.isRequired("fax");
          }),},
        p_o_pox: {required: requiredIf(function (model) {
              return this.isRequired("p_o_pox");
          }),},
        address: {required: requiredIf(function (model) {
              return this.isRequired("address");
          }),},
      email: {required: requiredIf(function (model) {
              return this.isRequired("email");
          }),email}
    },
  },
  methods: {
    getBranch() {
      this.isLoader = true;

       adminApi
        .get(`/branches/get-drop-down?company_id=${this.company_id}`)
        .then((res) => {
          let l = res.data.data;
          this.parent = l;
        })
        .catch((err) => {
            this.errorFun('Error','Thereisanerrorinthesystem');
        })
        .finally(() => {
          this.isLoader = false;
        });
    },
    async getCustomTableFields() {
        this.isCustom = true;
        await adminApi
        .get(`/customTable/table-columns/general_branches`)
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
              name: "",
              name_e: "",
              is_active: 1,
              parent_id: null,
              phone: '',
              second_phone: '',
              fax: '',
              p_o_pox: '',
              address: '',
              email: '',
              code_country: "",
          };
          this.errors = {};
          this.is_disabled = false;
          this.$nextTick(() => {
              this.$v.$reset();
          });
          this.images = [];
          this.branch_id = null;
          this.showPhoto = img;
          this.media = {};
      },
    resetModalHidden() {
      this.defaultData();
      this.$bvModal.hide(this.id);
    },
    resetModal() {
      this.defaultData();
      setTimeout(async () => {
            if(this.type != 'edit'){
                if(!this.isPage) await this.getCustomTableFields();
                this.codeCountry = this.$store.getters["locationIp/countryCode"];
                this.$nextTick(() => {this.$v.$reset();});
                if (this.isVisible("parent_id")) this.getBranch();
            }else {
                if(this.idObjEdit.dataObj){
                    let branch = this.idObjEdit.dataObj;
                    this.branch_id = branch.id;
                    this.errors = {};
                    this.create.name = branch.name;
                    this.create.name_e = branch.name_e;
                    this.create.is_active = branch.is_active;
                    this.create.phone = branch.phone;
                    this.create.second_phone = branch.second_phone;
                    this.create.fax = branch.fax;
                    this.create.address = branch.address;
                    this.create.p_o_pox = branch.p_o_pox;
                    this.create.email = branch.email;

                    if (this.isVisible("parent_id")) this.getBranch();
                    this.create.parent_id = branch.parent_id;
                    this.codeCountry = branch.code_country;
                    this.images = branch.media ?? [];
                    if (this.images && this.images.length > 0) {
                        this.showPhoto = this.images[this.images.length - 1].webp;
                    } else {
                        this.showPhoto = img;
                    }
                }
            }
        },50);
    },
    resetForm() {
        this.defaultData();
        this.codeCountry = this.$store.getters["locationIp/countryCode"];
    },
    AddSubmit() {
      this.create.code_country = this.codeCountry;
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
        this.is_disabled = false;
        if(this.type != 'edit'){
              adminApi
                  .post(this.url, {
                      ...this.create,
                      company_id: this.$store.getters["auth/company_id"],
                  })
                  .then((res) => {
                      this.is_disabled = true;
                      this.branch_id = res.data.data.id;
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
            this.images.forEach((e) => {
                this.create.old_media.push(e.id);
            });
            adminApi
                .put(`${this.url}/${this.idObjEdit.idEdit}`, {
                    ...this.create,
                    company_id: this.$store.getters["auth/company_id"],
                    media: []
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
    arabicValue(txt) {
      this.create.name = arabicValue(txt);
    },
    englishValue(txt) {
      this.create.name_e = englishValue(txt);
    },
    updatePhone(e) {
        this.create.phone = e.phoneNumber;
    },
    updateWhatsapp(e) {
          this.create.second_phone = e.phoneNumber;
      },
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
                          console.log(old_media)
                          adminApi
                              .put(`/branches/${this.branch_id}`, {
                                  old_media: old_media,
                                  media: new_media,
                              })
                              .then((res) => {
                                  this.images = res.data.data.media ?? [];
                                  if (this.images && this.images.length > 0) {
                                      this.showPhoto = this.images[this.images.length - 1].webp;
                                  } else {
                                      this.showPhoto = img;
                                  }
                              })
                              .catch((err) => {
                                  this.errorFun('Error','Thereisanerrorinthesystem');
                              });
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
                                  console.log(old_media);
                                  adminApi
                                      .put(`/branches/${this.branch_id}`, {
                                          old_media:old_media,
                                          media: new_media,
                                      })
                                      .then((res) => {
                                          this.images = res.data.data.media ?? [];
                                          if (this.images && this.images.length > 0) {
                                              this.showPhoto = this.images[this.images.length - 1].webp;
                                          } else {
                                              this.showPhoto = img;
                                          }
                                      })
                                      .catch((err) => {
                                          this.errorFun('Error','Thereisanerrorinthesystem');
                                      });
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
              .put(`/branches/${this.branch_id}`, { old_media })
              .then((res) => {
                  this.bankAccounts[index] = res.data.data;
                  this.images = res.data.data.media ?? [];
                  if (this.images && this.images.length > 0) {
                      this.showPhoto = this.images[this.images.length - 1].webp;
                  } else {
                      this.showPhoto = img;
                  }
              })
              .catch((err) => {
                  this.errorFun('Error','Thereisanerrorinthesystem');
              });
      }
  },
};
</script>

<style>
form {
    position: relative;
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
