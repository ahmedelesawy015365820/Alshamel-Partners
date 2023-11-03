<template>
  <div>
    <Department
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      id="department-create-employee"
      :isPage="false"
      type="create"
      :isPermission="isPermission"
      @created="getDepartnent"
    />
    <SalemanType
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getSaleMenType"
      id="sales-man-type-create-employee"
      :isPage="false"
      type="create"
      :isPermission="isPermission"
    />
    <SalemanPlan
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getPlans"
      id="create-salesman-plan-employee"
      :isPage="false"
      type="create"
      :isPermission="isPermission"
    />
    <Job
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getJobs"
      id="job_create_employee"
    />
    <!--  create   -->
    <b-modal
      :id="id"
      :title="
        type != 'edit'
          ? getCompanyKey('employee_create_form')
          : getCompanyKey('employee_edit_form')
      "
      title-class="font-18"
      body-class="p-4 "
      dialog-class="modal-full-width"
      :hide-footer="true"
      @show="resetModal"
      @hidden="resetModalHidden"
    >
      <form>
        <loader size="large" v-if="isCustom && !isPage" />
        <div class="d-flex justify-content-end">
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
              {{ type != "edit" ? $t("general.Add") : $t("general.edit") }}
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
          <div class="col-md-3" v-if="isVisible('name')">
            <div class="form-group">
              <label  class="control-label">
                {{ getCompanyKey("employee_name_ar") }}
                <span v-if="isRequired('name')" class="text-danger">*</span>
              </label>
              <div dir="rtl">
                <input
                  type="text"
                  class="form-control arabicInput"
                  data-create="1"
                  @keyup="arabicValueName(create.name)"
                  v-model="$v.create.name.$model"
                  :class="{
                    'is-invalid': $v.create.name.$error || errors.name,
                    'is-valid': !$v.create.name.$invalid && !errors.name,
                  }"
                />
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
          <div class="col-md-3" v-if="isVisible('name_e')">
            <div class="form-group">
              <label  class="control-label">
                {{ getCompanyKey("employee_name_en") }}
                <span v-if="isRequired('name_e')" class="text-danger">*</span>
              </label>
              <div dir="ltr">
                <input
                  type="text"
                  class="form-control englishInput"
                  data-create="2"
                  @keyup="englishValueName(create.name_e)"
                  v-model="$v.create.name_e.$model"
                  :class="{
                    'is-invalid': $v.create.name_e.$error || errors.name_e,
                    'is-valid': !$v.create.name_e.$invalid && !errors.name_e,
                  }"
                />
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
          <div class="col-md-3" v-if="isVisible('email')">
                <div class="form-group">
                    <label class="control-label">
                        {{ getCompanyKey("employee_email") }}
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
          <div class="col-md-3" v-if="isVisible('mobile')">
                <div class="form-group">
                    <label class="control-label">
                        {{ getCompanyKey("employee_mobile") }}
                        <span v-if="isRequired('phone')" class="text-danger">*</span>
                    </label>
                    <VuePhoneNumberInput
                        v-model="$v.create.mobile.$model"
                        :default-country-code="codeCountry"
                        valid-color="#28a745"
                        error-color="#dc3545"
                        :preferred-countries="['FR', 'EG', 'DE']"
                        @update="updatePhone"
                    />
                    <div
                        v-if="$v.create.mobile.$error || errors.mobile"
                        class="text-danger"
                    >
                        {{ $t("general.fieldIsRequired") }}
                    </div>
                    <template v-if="errors.mobile">
                        <ErrorMessage
                            v-for="(errorMessage, index) in errors.mobile"
                            :key="index"
                        >{{ errorMessage }}
                        </ErrorMessage>
                    </template>
                </div>
            </div>
          <div class="col-md-3" v-if="isVisible('whatsapp')">
                <div class="form-group">
                    <label class="control-label">
                        {{ getCompanyKey("employee_whatsapp") }}
                        <span v-if="isRequired('whatsapp')" class="text-danger">*</span>
                    </label>
                    <VuePhoneNumberInput
                        v-model="$v.create.whatsapp.$model"
                        :default-country-code="codeCountry"
                        valid-color="#28a745"
                        error-color="#dc3545"
                        :preferred-countries="['FR', 'EG', 'DE']"
                        @update="updateWhatsapp"
                    />
                    <div
                        v-if="$v.create.whatsapp.$error || errors.whatsapp"
                        class="text-danger"
                    >
                        {{ $t("general.fieldIsRequired") }}
                    </div>
                    <template v-if="errors.whatsapp">
                        <ErrorMessage
                            v-for="(errorMessage, index) in errors.whatsapp"
                            :key="index"
                        >{{ errorMessage }}
                        </ErrorMessage>
                    </template>
                </div>
          </div>
          <div v-if="isVisible('sms')" class="col-md-3">
                <div class="form-group">
                    <label class="mr-2">
                        {{ getCompanyKey("employee_is_sms") }}
                    </label>
                    <b-form-group>
                        <b-form-radio
                            class="d-inline-block"
                            v-model="isSms"
                            name="manage_others_some-radios3"
                            :value="1"
                        >{{ $t("general.Yes") }}
                        </b-form-radio>
                        <b-form-radio
                            class="d-inline-block m-1"
                            v-model="isSms"
                            name="manage_others_some-radios3"
                            :value="0"
                        >{{ $t("general.No") }}
                        </b-form-radio>
                    </b-form-group>
                </div>
            </div>
          <div class="col-md-3" v-if="isVisible('sms') && !isSms">
                <div class="form-group">
                    <label class="control-label">
                        {{ getCompanyKey("employee_sms") }}
                        <span v-if="isRequired('phone')" class="text-danger">*</span>
                    </label>
                    <VuePhoneNumberInput
                        v-model="$v.create.sms.$model"
                        :default-country-code="codeCountry"
                        valid-color="#28a745"
                        error-color="#dc3545"
                        :preferred-countries="['FR', 'EG', 'DE']"
                        @update="updateSms"
                    />
                    <div
                        v-if="$v.create.sms.$error || errors.sms"
                        class="text-danger"
                    >
                        {{ $t("general.fieldIsRequired") }}
                    </div>
                    <template v-if="errors.sms">
                        <ErrorMessage
                            v-for="(errorMessage, index) in errors.sms"
                            :key="index"
                        >{{ errorMessage }}
                        </ErrorMessage>
                    </template>
                </div>
            </div>
        </div>
        <hr
              style="
                        margin: 10px 0 !important;
                        border-top: 1px solid rgb(141 163 159 / 42%);
                      "
          />
        <div class="row">
            <div class="col-md-3" v-if="isVisible('department_id')">
                <div class="form-group position-relative">
                    <label class="control-label">
                        {{ getCompanyKey("employee_department") }}
                        <span v-if="isRequired('department_id')" class="text-danger"
                        >*</span
                        >
                    </label>
                    <multiselect
                        @input="showDepartmentModal"
                        v-model="create.department_id"
                        :options="departments.map((type) => type.id)"
                        :custom-label="
                  (opt) =>
                    departments.find((x) => x.id == opt)
                      ? $i18n.locale == 'ar'
                        ? departments.find((x) => x.id == opt).name
                        : departments.find((x) => x.id == opt).name_e
                      : null
                "
                    >
                    </multiselect>
                    <div
                        v-if="$v.create.department_id.$error || errors.department_id"
                        class="text-danger"
                    >
                        {{ $t("general.fieldIsRequired") }}
                    </div>
                    <template v-if="errors.department_id">
                        <ErrorMessage
                            v-for="(errorMessage, index) in errors.department_id"
                            :key="index"
                        >{{ errorMessage }}
                        </ErrorMessage>
                    </template>
                </div>
            </div>
            <div class="col-md-3" v-if="isVisible('job_id')">
                <div class="form-group position-relative">
                    <label class="control-label">
                        {{ getCompanyKey("employee_job") }}
                        <span v-if="isRequired('job_id')" class="text-danger">*</span>
                    </label>
                    <multiselect
                        @input="showJobModal"
                        v-model="create.job_id"
                        :options="jobs.map((type) => type.id)"
                        :custom-label="
                  (opt) =>
                    jobs.find((x) => x.id == opt)
                      ? $i18n.locale == 'ar'
                        ? jobs.find((x) => x.id == opt).name
                        : jobs.find((x) => x.id == opt).name_e
                      : null
                "
                    >
                    </multiselect>
                    <div
                        v-if="$v.create.job_id.$error || errors.job_id"
                        class="text-danger"
                    >
                        {{ $t("general.fieldIsRequired") }}
                    </div>
                    <template v-if="errors.job_id">
                        <ErrorMessage
                            v-for="(errorMessage, index) in errors.job_id"
                            :key="index"
                        >{{ errorMessage }}
                        </ErrorMessage>
                    </template>
                </div>
            </div>
            <div class="col-md-3" v-if="isVisible('manager_id')">
                <div class="form-group">
                    <label class="mr-2">
                        {{ getCompanyKey("employee_manager") }}
                    </label>
                    <multiselect
                        v-model="create.manager_id"
                        :internalSearch="false"
                        @search-change="searchManeger"
                        :options="managers.map((type) => type.id)"
                        :custom-label="
                  (opt) =>
                    managers.find((x) => x.id == opt)
                      ? managers.find((x) => x.id == opt).name
                      : null
                "
                    >
                    </multiselect>
                    <div
                        v-if="$v.create.manager_id.$error || errors.manager_id"
                        class="text-danger"
                    >
                        {{ $t("general.fieldIsRequired") }}
                    </div>
                    <template v-if="errors.manager_id">
                        <ErrorMessage
                            v-for="(errorMessage, index) in errors.manager_id"
                            :key="index"
                        >{{ errorMessage }}
                        </ErrorMessage>
                    </template>
                </div>
            </div>
            <div class="col-md-3" v-if="isVisible('manager_id')">
                <div class="form-group">
                    <label class="mr-2">
                        {{ getCompanyKey("employee_managers") }}
                    </label>
                    <multiselect
                        :multiple="true"
                        v-model="create.manager_ids"
                        :internalSearch="false"
                        @search-change="searchManeger"
                        :options="managers.map((type) => type.id)"
                        :custom-label="
                  (opt) =>
                    managers.find((x) => x.id == opt)
                      ? managers.find((x) => x.id == opt).name
                      : null
                "
                    >
                    </multiselect>
                    <div
                        v-if="$v.create.manager_ids.$error || errors.manager_ids"
                        class="text-danger"
                    >
                        {{ $t("general.fieldIsRequired") }}
                    </div>
                    <template v-if="errors.manager_ids">
                        <ErrorMessage
                            v-for="(errorMessage, index) in errors.manager_ids"
                            :key="index"
                        >{{ errorMessage }}
                        </ErrorMessage>
                    </template>
                </div>
            </div>
            <div class="col-md-3" v-if="isVisible('branch_id')">
                <div class="form-group">
                    <label class="mr-2">
                        {{ getCompanyKey("employee_branch") }}
                    </label>
                    <multiselect
                        v-model="create.branch_id"
                        :options="branches.map((type) => type.id)"
                        :custom-label="
                  (opt) =>
                    branches.find((x) => x.id == opt)
                      ? branches.find((x) => x.id == opt).name
                      : null
                "
                    >
                    </multiselect>
                    <div
                        v-if="$v.create.branch_id.$error || errors.branch_id"
                        class="text-danger"
                    >
                        {{ $t("general.fieldIsRequired") }}
                    </div>
                    <template v-if="errors.branch_id">
                        <ErrorMessage
                            v-for="(errorMessage, index) in errors.branch_id"
                            :key="index"
                        >{{ errorMessage }}
                        </ErrorMessage>
                    </template>
                </div>
            </div>
            <div v-if="isVisible('customer_handel')" class="col-md-3">
                <div class="form-group">
                    <label class="mr-2">
                        {{ getCompanyKey("employee_customer_handel") }}
                        <span v-if="isRequired('customer_handel')" class="text-danger"
                        >*</span
                        >
                    </label>

                    <select
                        class="custom-select"
                        v-model="$v.create.customer_handel.$model"
                    >
                        <option value="non_customer">
                            {{ $t("general.non_customer") }}
                        </option>
                        <option value="his_customer">
                            {{ $t("general.his_customer") }}
                        </option>
                        <option value="all_customer">
                            {{ $t("general.all_customer") }}
                        </option>
                    </select>

                    <template v-if="errors.customer_handel">
                        <ErrorMessage
                            v-for="(errorMessage, index) in errors.customer_handel"
                            :key="index"
                        >{{ errorMessage }}
                        </ErrorMessage>
                    </template>
                </div>
            </div>
            <div v-if="isVisible('manage_others')" class="col-md-3">
                <div class="form-group">
                    <label class="mr-2">
                        {{ getCompanyKey("manage_others") }}
                        <span v-if="isRequired('manage_others')" class="text-danger"
                        >*</span
                        >
                    </label>
                    <b-form-group
                        :class="{
                  'is-invalid':
                    $v.create.manage_others.$error || errors.manage_others,
                  'is-valid':
                    !$v.create.manage_others.$invalid && !errors.manage_others,
                }"
                    >
                        <b-form-radio
                            class="d-inline-block"
                            v-model="$v.create.manage_others.$model"
                            name="manage_others_some-radios"
                            :value="1"
                        >{{ $t("general.Yes") }}
                        </b-form-radio>
                        <b-form-radio
                            class="d-inline-block m-1"
                            v-model="$v.create.manage_others.$model"
                            name="manage_others_some-radios"
                            :value="0"
                        >{{ $t("general.No") }}
                        </b-form-radio>
                    </b-form-group>
                    <template v-if="errors.manage_others">
                        <ErrorMessage
                            v-for="(errorMessage, index) in errors.manage_others"
                            :key="index"
                        >{{ errorMessage }}
                        </ErrorMessage>
                    </template>
                </div>
            </div>
            <div v-if="isVisible('is_salesman')" class="col-md-3">
                <div class="form-group">
                    <label class="mr-2">
                        {{ getCompanyKey("employee_is_salesman") }}
                        <span v-if="isRequired('is_salesman')" class="text-danger"
                        >*</span
                        >
                    </label>
                    <b-form-group
                        :class="{
                  'is-invalid':
                    $v.create.is_salesman.$error || errors.is_salesman,
                  'is-valid':
                    !$v.create.is_salesman.$invalid && !errors.is_salesman,
                }"
                    >
                        <b-form-radio
                            class="d-inline-block"
                            v-model="$v.create.is_salesman.$model"
                            name="some-radios"
                            value="active"
                        >{{ $t("general.Yes") }}
                        </b-form-radio>
                        <b-form-radio
                            class="d-inline-block m-1"
                            v-model="$v.create.is_salesman.$model"
                            name="some-radios"
                            value="inactive"
                        >{{ $t("general.No") }}
                        </b-form-radio>
                    </b-form-group>
                    <template v-if="errors.is_salesman">
                        <ErrorMessage
                            v-for="(errorMessage, index) in errors.is_salesman"
                            :key="index"
                        >{{ errorMessage }}
                        </ErrorMessage>
                    </template>
                </div>
            </div>
            <div
                v-if="
              isVisible('salesman_type_id') && create.is_salesman == 'active'
            "
                class="col-md-3"
            >
                <div class="form-group">
                    <label class="mr-2">
                        {{ getCompanyKey("employee_sale_man_type") }}
                        <span v-if="isRequired('salesman_type_id')" class="text-danger"
                        >*</span
                        >
                    </label>
                    <multiselect
                        @input="showSalesManTypeModal"
                        v-model="create.salesman_type_id"
                        :options="salesmenTypes.map((type) => type.id)"
                        :custom-label="
                  (opt) =>
                    salesmenTypes.find((x) => x.id == opt)
                      ? salesmenTypes.find((x) => x.id == opt).name
                      : null
                "
                    >
                    </multiselect>
                    <div
                        v-if="
                  $v.create.salesman_type_id.$error || errors.salesman_type_id
                "
                        class="text-danger"
                    >
                        {{ $t("general.fieldIsRequired") }}
                    </div>
                    <template v-if="errors.salesman_type_id">
                        <ErrorMessage
                            v-for="(errorMessage, index) in errors.salesman_type_id"
                            :key="index"
                        >{{ errorMessage }}
                        </ErrorMessage>
                    </template>
                </div>
            </div>
            <div
                v-if="
              isVisible('salesman_type_id') && create.is_salesman == 'active'
            "
                class="col-md-3"
            >
                <div class="form-group">
                    <label class="mr-2">
                        {{ getCompanyKey("employee_plan") }}
                        <span class="text-danger">*</span>
                    </label>
                    <multiselect
                        @input="showPlanModal"
                        :multiple="true"
                        v-model="create.plan_id"
                        :options="plans.map((type) => type.id)"
                        :custom-label="
                  (opt) =>
                    plans.find((x) => x.id == opt)
                      ? plans.find((x) => x.id == opt).name
                      : null
                "
                    >
                    </multiselect>
                    <div
                        v-if="$v.create.plan_id.$error || errors.plan_id"
                        class="text-danger"
                    >
                        {{ $t("general.fieldIsRequired") }}
                    </div>
                    <template v-if="errors.plan_id">
                        <ErrorMessage
                            v-for="(errorMessage, index) in errors.plan_id"
                            :key="index"
                        >{{ errorMessage }}
                        </ErrorMessage>
                    </template>
                </div>
            </div>
            <div class="col-md-3" v-if="isVisible('att_code')">
                <div class="form-group">
                    <label class="control-label">
                        {{ getCompanyKey("employee_att_code") }}
                        <span v-if="isRequired('att_code')" class="text-danger">*</span>
                    </label>
                    <div dir="rtl">
                        <input
                            type="number"
                            class="form-control"
                            v-model="$v.create.att_code.$model"
                            :class="{
                                'is-invalid': $v.create.att_code.$error || errors.att_code,
                                'is-valid': !$v.create.att_code.$invalid && !errors.att_code,
                              }"
                        />
                    </div>
                    <template v-if="errors.att_code">
                        <ErrorMessage
                            v-for="(errorMessage, index) in errors.att_code"
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
import adminApi from "../../../api/adminAxios";
import {
  required,
  maxLength,
  minLength,
  requiredIf,
  email,
} from "vuelidate/lib/validators";
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import { arabicValue, englishValue } from "../../../helper/langTransform";
import Department from "./departmentTask";
import Multiselect from "vue-multiselect";
import SalemanType from "./salesManType.vue";
import SalemanPlan from "./salesmanPlan.vue";
import Job from "../hr/job.vue";
import successError from "../../../helper/mixin/success&error";
/**
 * Advanced Table component
 */
export default {
  components: {
    SalemanPlan,
    ErrorMessage,
    loader,
    Job,
    SalemanType,
    Multiselect,
    Department,
  },
  mixins: [transMixinComp, successError],
  props: {
    id: { default: "create" },
    companyKeys: { default: [] },
    defaultsKeys: { default: [] },
    isPage: { default: true },
    isVisiblePage: { default: null },
    isRequiredPage: { default: null },
    type: { default: "create" },
    idObjEdit: { default: null },
    isPermission: {},
    url: { default: "/employees" },
  },
  data() {
    return {
      fields: [],
      idEmployee: null,
      codeCountry: "",
      departments: [],
      isLoader: false,
      company_id: null,
      salesmenTypes: [],
      managers: [],
      plans: [],
      hasSun: true,
        isSms: 0,
      jobs: [],
      branches: [],
      create: {
        branch_id: null,
        job_id: null,
        name: "",
        name_e: "",
        is_salesman: "inactive",
        customer_handel: "non_customer",
        department_id: null,
        manager_id: null,
        salesman_type_id: null,
        manager_ids: [],
        plan_id: [],
          att_code: '',
          sms: '',
        manage_others: 0,
        mobile: "",
        email: "",
        whatsapp: "",
        code_country: "",
      },
      errors: {},
      current_page: 1,
      isCustom: false,
      is_disabled: false,
    };
  },
  validations: {
    create: {
      name: {
        required: requiredIf(function (model) {
          return this.isRequired("name");
        }),
        maxLength: maxLength(100),
      },
      name_e: {
        required: requiredIf(function (model) {
          return this.isRequired("name_e");
        }),
        maxLength: maxLength(100),
      },
      department_id: {
        required: requiredIf(function (model) {
          return this.isRequired("department_id");
        }),
      },
      branch_id: {
        required: requiredIf(function (model) {
          return this.isRequired("branch_id");
        }),
      },
      is_salesman: {
        required: requiredIf(function (model) {
          return this.isRequired("is_salesman");
        }),
      },
      customer_handel: {
        required: requiredIf(function (model) {
          return this.isRequired("customer_handel");
        }),
      },
      sms: {
            required: requiredIf(function (model) {
                return this.isRequired("sms");
            }),
        },
        att_code: {
            required: requiredIf(function (model) {
                return this.isRequired("att_code");
            }),
        },
      manager_id: {required: requiredIf(function (model) {
              return this.isRequired("manager_id")})
      ,},
      manager_ids: {required: requiredIf(function (model) {
              return this.isRequired("manager_id")})
          ,},
      manage_others: {required: requiredIf(function (model) {
              return this.isRequired("manage_others");
          }),},
      salesman_type_id: {
        required: requiredIf(function (model) {
          return (
            this.isRequired("salesman_type_id") &&
            this.create.is_salesman == "active"
          );
        }),
      },
      plan_id: {
        required: requiredIf(function (model) {
          return this.create.is_salesman == "active";
        }),
      },
      job_id: {
        required: requiredIf(function (model) {
          return this.isRequired("job_id");
        }),
      },
      mobile: {
        required: requiredIf(function (model) {
          return this.isRequired("mobile");
        }),
      },
      email: {
        required: requiredIf(function (model) {
          return this.isRequired("email");
        }),
        email,
      },
      whatsapp: {
        required: requiredIf(function (model) {
          return this.isRequired("whatsapp");
        }),
        maxLength: maxLength(100),
      },
    },
  },
  mounted() {
    this.company_id = this.$store.getters["auth/company_id"];
    this.$store.dispatch('locationIp/getIp');
  },
  methods: {
    getEmployeeChildren(id) {
      this.isLoader = true;
      adminApi
        .get(`employees/get-drop-down?manager_id=${id}`)
        .then((res) => {
          let l = res.data.data;
          this.hasSun = l.length ? true : false;
        })
        .catch((err) => {
          this.errorFun("Error", "Thereisanerrorinthesystem");
        })
        .finally(() => {
          this.isLoader = false;
        });
    },
    getSaleMenType() {
      this.isLoader = true;
      adminApi
        .get(`/salesmen-types/get-drop-down?is_empolyee=1`)
        .then((res) => {
          let l = res.data.data;
          if (this.isPermission("create Sales Man Type")) {
            l.unshift({
              id: 0,
              name: "اضف نوع رجل المبيعات",
              name_e: "Add saleman type",
            });
          }
          this.salesmenTypes = l;
        })
        .catch((err) => {
          this.errorFun("Error", "Thereisanerrorinthesystem");
        })
        .finally(() => {
          this.isLoader = false;
        });
    },
    async getCustomTableFields() {
      this.isCustom = false;
      await adminApi
        .get(`/customTable/table-columns/general_employees`)
        .then((res) => {
          this.fields = res.data;
        })
        .catch((err) => {
          this.errorFun("Error", "Thereisanerrorinthesystem");
        })
        .finally(() => {
          this.isCustom = false;
        });
    },
    isVisible(fieldName) {
      if (!this.isPage) {
        let res = this.fields.filter((field) => {
          return field.column_name == fieldName;
        });
        return res.length > 0 && res[0].is_visible == 1 ? true : false;
      } else {
        return this.isVisiblePage(fieldName);
      }
    },
    isRequired(fieldName) {
      if (!this.isPage) {
        let res = this.fields.filter((field) => {
          return field.column_name == fieldName;
        });
        return res.length > 0 && res[0].is_required == 1 ? true : false;
      } else {
        return this.isRequiredPage(fieldName);
      }
    },
    getDepartnent() {
      this.isLoader = true;

      adminApi
        .get(`/depertments/get-drop-down`)
        .then((res) => {
          let l = res.data.data;
          if (this.isPermission("create Department")) {
            l.unshift({ id: 0, name: "اضف قسم", name_e: "Add Department" });
          }
          this.departments = l;
        })
        .catch((err) => {
          this.errorFun("Error", "Thereisanerrorinthesystem");
        })
        .finally(() => {
          this.isLoader = false;
        });
    },
    getJobs() {
      this.isLoader = true;

      adminApi
        .get(`hr/job-title`)
        .then((res) => {
          let l = res.data.data;
          l.unshift({ id: 0, name: "اضف وظيفة", name_e: "Add Job" });
          this.jobs = l;
        })
        .catch((err) => {
          this.errorFun("Error", "Thereisanerrorinthesystem");
        })
        .finally(() => {
          this.isLoader = false;
        });
    },
    getBranches() {
      this.isLoader = true;

      adminApi
        .get(`/branches/get-drop-down`)
        .then((res) => {
          let l = res.data.data;
          this.branches = l;
        })
        .catch((err) => {
          this.errorFun("Error", "Thereisanerrorinthesystem");
        })
        .finally(() => {
          this.isLoader = false;
        });
    },
    getPlans() {
      this.isLoader = true;

      adminApi
        .get(`/salesmen-plans/get-drop-down`)
        .then((res) => {
          let l = res.data.data;
          if (this.isPermission("create Plan")) {
            l.unshift({ id: 0, name: "اضف خطة", name_e: "Add plan" });
          }
          this.plans = l;
        })
        .catch((err) => {
          this.errorFun("Error", "Thereisanerrorinthesystem");
        })
        .finally(() => {
          this.isLoader = false;
        });
    },
    async searchManeger(e) {
      let search = e ?? "";
      clearTimeout(this.debounce);
      this.debounce = setTimeout(() => {
        this.getManagers(search);
      }, 500);
    },
    getManagers(search = "") {
      this.isLoader = true;
      adminApi
        .get(
          `/employees/get-drop-down?limet=10&company_id=${
            this.company_id
          }&manage=true&employee_id=${
            this.idEmployee ?? ""
          }&search=${search}&columns[0]=name&columns[1]=name_e&columns[2]=id&department_id=${this.create.department_id}`
        )
        .then((res) => {
          let l = res.data.data;
          this.managers = l;
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
    showDepartmentModal() {
      if (this.create.department_id == 0) {
        this.$bvModal.show("department-create-employee");
        this.create.department_id = null;
      }else {
          if (this.isVisible("manager_id")) this.getManagers();
      }
    },
    showSalesManTypeModal() {
      if (this.create.salesman_type_id == 0) {
        this.$bvModal.show("sales-man-type-create-employee");
        this.create.salesman_type_id = null;
      }
    },
    showPlanModal() {
      let index = this.create.plan_id.findIndex((id) => id == 0);
      if (index > -1) {
        this.$bvModal.show("create-salesman-plan-employee");
        this.create.plan_id.splice(index, 1);
      }
    },
    showJobModal() {
      if (this.create.job_id == 0) {
        this.$bvModal.show("job_create_employee");
        this.create.job_id = null;
      }
    },
    defaultData(edit = null) {
      this.hasSun = true;
      this.idEmployee = null;
      this.create = {
          branch_id: null,
          job_id: null,
          name: "",
          name_e: "",
          is_salesman: "inactive",
          customer_handel: "non_customer",
          department_id: null,
          manager_id: null,
          salesman_type_id: null,
          manager_ids: [],
          plan_id: [],
          att_code: '',
          sms: '',
          manage_others: 0,
          mobile: "",
          email: "",
          whatsapp: "",
          code_country: "",
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.isSms = 0;
      this.errors = {};
      this.is_disabled = false;
    },
    resetModalHidden() {
      this.defaultData();
      this.$bvModal.hide(this.id);
    },
    resetModal() {
      this.defaultData();
      setTimeout(async () => {
        if (this.type != "edit") {
          if (!this.isPage) await this.getCustomTableFields();
          this.codeCountry = this.$store.getters["locationIp/countryCode"];
          if (this.isVisible("department_id")) this.getDepartnent();
          if (this.isVisible("job_id")) this.getJobs();
          if (this.isVisible("branch_id")) this.getBranches();
          this.getPlans();
          if (this.isVisible("salesman_type_id")) this.getSaleMenType();
        } else {
          if (this.idObjEdit.dataObj) {
            let employee = this.idObjEdit.dataObj;
            this.errors = {};
            this.idEmployee = employee.id;
            this.create.name = employee.name;
            this.create.name_e = employee.name_e;
            this.create.customer_handel = employee.customer_handel;
            this.create.sms= employee.sms;
            this.create.att_code = employee.att_code;
            this.getDepartnent();
            this.create.department_id = employee.department? employee.department.id: null;
            this.getJobs();
            this.create.job_id = employee.job_title
              ? employee.job_title.id
              : null;
            this.getBranches();
            this.create.branch_id = employee.branch_id;
            this.getSaleMenType();
            this.create.salesman_type_id = employee.salesman_type
              ? employee.salesman_type.id
              : null;
            this.create.is_salesman =
              employee.is_salesman == "true" ? "active" : "inactive";
            this.getPlans();
            this.create.plan_id =  employee.plans.length  > 0? employee.plans.map((plan) => plan.id):null;
            employee.department?this.getManagers():null;
            this.managers.push(employee.manager);
            this.create.manager_id = employee.manager
              ? employee.manager.id
              : null;
            if(employee.managers.length > 0){
                employee.managers.forEach(el => this.create.manager_ids.push(el.id));
            }
            this.create.manage_others = employee.manage_others;
            this.getEmployeeChildren(employee.id);
            this.create.mobile = employee.mobile;
            this.create.email = employee.email;
            this.create.whatsapp = employee.whatsapp;
            this.codeCountry = employee.code_country;
          }
        }
      }, 50);
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

        if (this.isSms == 1) {
            this.create.sms = this.create.whatsapp;
        }

        this.$v.create.$touch();

      if (this.$v.create.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        if (this.type != "edit") {
          adminApi
            .post(this.url, {
              ...this.create,
              plans: this.create.plan_id,
              is_salesman:
                this.create.is_salesman == "active" ? "true" : "false",
              company_id: this.$store.getters["auth/company_id"],
            })
            .then((res) => {
              this.is_disabled = true;
              if (!this.isPage) this.$emit("created");
              else this.$emit("getDataTable");

              setTimeout(() => {
                this.successFun("Addedsuccessfully");
              }, 500);
            })
            .catch((err) => {
              if (err.response.data) {
                this.errors = err.response.data.errors;
              } else {
                this.errorFun("Error", "Thereisanerrorinthesystem");
              }
            })
            .finally(() => {
              this.isLoader = false;
            });
        } else {
          adminApi
            .put(`${this.url}/${this.idObjEdit.idEdit}`, {
              ...this.create,
              plans: this.create.plan_id,
              is_salesman:
                this.create.is_salesman == "active" ? "true" : "false",
              code_country: this.codeCountry,
            })
            .then((res) => {
              this.$bvModal.hide(this.id);
              this.$emit("getDataTable");
              setTimeout(() => {
                this.successFun("Editsuccessfully");
              }, 500);
            })
            .catch((err) => {
              if (err.response.data) {
                this.errors = err.response.data.errors;
              } else {
                this.errorFun("Error", "Thereisanerrorinthesystem");
              }
            })
            .finally(() => {
              this.isLoader = false;
            });
        }
      }
    },
    arabicValueName(txt) {
      this.create.name = arabicValue(txt);
    },
    englishValueName(txt) {
      this.create.name_e = englishValue(txt);
    },
    updatePhone(e) {
      this.create.phone = e.phoneNumber;
    },
    updateSms(e) {
      this.create.phone = e.phoneNumber;
    },
    updateWhatsapp(e) {
      this.create.whatsapp = e.phoneNumber;
    },
  },
};
</script>

<style scoped>
form {
  position: relative;
}
</style>
