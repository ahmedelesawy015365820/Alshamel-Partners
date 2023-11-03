<template>
    <div>
        <!--  create   -->
        <b-modal
            :id="id"
            :title="type != 'edit'?getCompanyKey('department_create_form'):getCompanyKey('department_edit_form')"
            title-class="font-18"
            body-class="p-4"
            size="lg"
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
                <b-tabs nav-class="nav-tabs nav-bordered">
                    <b-tab :title="$t('general.DataEntry')" active>
                        <div class="row justify-content-center">
                            <div class="col-md-6" v-if="isVisible('name')">
                                <div class="form-group">
                                    <label
                                        class="control-label"
                                    >
                                        {{ getCompanyKey("department_name_ar") }}
                                        <span
                                            v-if="isRequired('name')"
                                            class="text-danger"
                                        >*</span
                                        >
                                    </label>
                                    <div dir="rtl">
                                        <input
                                            type="text"
                                            class="form-control"
                                            v-model="$v.create.name.$model"
                                            @keyup="arabicValueName(create.name)"
                                            :class="{
                                          'is-invalid':
                                            $v.create.name.$error || errors.name,
                                          'is-valid':
                                            !$v.create.name.$invalid &&
                                            !errors.name,
                                        }"
                                        />
                                    </div>
                                    <div
                                        v-if="!$v.create.name.minLength"
                                        class="invalid-feedback"
                                    >
                                        {{ $t("general.Itmustbeatleast") }}
                                        {{ $v.create.name.$params.minLength.min }}
                                        {{ $t("general.letters") }}
                                    </div>
                                    <div
                                        v-if="!$v.create.name.maxLength"
                                        class="invalid-feedback"
                                    >
                                        {{ $t("general.Itmustbeatmost") }}
                                        {{ $v.create.name.$params.maxLength.max }}
                                        {{ $t("general.letters") }}
                                    </div>
                                    <template v-if="errors.name">
                                        <ErrorMessage
                                            v-for="(
                                          errorMessage, index
                                        ) in errors.name"
                                            :key="index"
                                        >{{ errorMessage }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div
                                class="col-md-6"
                                v-if="isVisible('name_e')"
                            >
                                <div class="form-group">
                                    <label
                                        class="control-label"
                                    >
                                        {{ getCompanyKey("department_name_en") }}
                                        <span
                                            v-if="isRequired('name_e')"
                                            class="text-danger"
                                        >*</span
                                        >
                                    </label>
                                    <div dir="ltr">
                                        <input
                                            type="text"
                                            class="form-control"
                                            @keyup="englishValueName(create.name_e)"
                                            v-model="$v.create.name_e.$model"
                                            :class="{
                                          'is-invalid':
                                            $v.create.name_e.$error ||
                                            errors.name_e,
                                          'is-valid':
                                            !$v.create.name_e.$invalid &&
                                            !errors.name_e,
                                        }"
                                        />
                                    </div>
                                    <div
                                        v-if="!$v.create.name_e.minLength"
                                        class="invalid-feedback"
                                    >
                                        {{ $t("general.Itmustbeatleast") }}
                                        {{ $v.create.name_e.$params.minLength.min }}
                                        {{ $t("general.letters") }}
                                    </div>
                                    <div
                                        v-if="!$v.create.name_e.maxLength"
                                        class="invalid-feedback"
                                    >
                                        {{ $t("general.Itmustbeatmost") }}
                                        {{ $v.create.name_e.$params.maxLength.max }}
                                        {{ $t("general.letters") }}
                                    </div>
                                    <template v-if="errors.name_e">
                                        <ErrorMessage
                                            v-for="(
                                          errorMessage, index
                                        ) in errors.name_e"
                                            :key="index"
                                        >{{ errorMessage }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div
                                class="col-md-6"
                                v-if="isVisible('supervisors')"
                            >
                                <div class="form-group">
                                    <label>
                                        {{
                                            getCompanyKey(
                                                "department_task_supervisors"
                                            )
                                        }}
                                        <span
                                            v-if="isRequired('supervisors')"
                                            class="text-danger"
                                        >*</span
                                        >
                                    </label>
                                    <multiselect
                                        :multiple="true"
                                        v-model="$v.create.supervisors.$model"
                                        :options="
                                        employees.map((type) => type.id)
                                      "
                                        :custom-label="
                                        (opt) =>
                                          employees.find((x) => x.id == opt) ?$i18n.locale == 'ar'
                                            ? employees.find((x) => x.id == opt)
                                                .name
                                            : employees.find((x) => x.id == opt)
                                                .name_e: null
                                      "
                                    >
                                    </multiselect>
                                    <div
                                        v-if="!$v.create.supervisors.required"
                                        class="invalid-feedback"
                                    >
                                        {{ $t("general.fieldIsRequired") }}
                                    </div>

                                    <template v-if="errors.supervisors">
                                        <ErrorMessage
                                            v-for="(
                                          errorMessage, index
                                        ) in errors.supervisors"
                                            :key="index"
                                        >{{ errorMessage }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div
                                class="col-md-6"
                                v-if="isVisible('attentions')"
                            >
                                <div class="form-group">
                                    <label>
                                        {{
                                            getCompanyKey("department_attentions")
                                        }}
                                        <span
                                            v-if="isRequired('attentions')"
                                            class="text-danger"
                                        >*</span
                                        >
                                    </label>
                                    <multiselect
                                        :multiple="true"
                                        v-model="$v.create.attentions.$model"
                                        :options="
                                        employees.map((type) => type.id)
                                      "
                                        :custom-label="
                                        (opt) =>
                                          employees.find((x) => x.id == opt)  ? $i18n.locale == 'ar'
                                            ? employees.find((x) => x.id == opt)
                                                .name
                                            : employees.find((x) => x.id == opt)
                                                .name_e: null
                                      "
                                    >
                                    </multiselect>
                                    <div
                                        v-if="!$v.create.attentions.required"
                                        class="invalid-feedback"
                                    >
                                        {{ $t("general.fieldIsRequired") }}
                                    </div>

                                    <template v-if="errors.supervisors">
                                        <ErrorMessage
                                            v-for="(
                                          errorMessage, index
                                        ) in errors.supervisors"
                                            :key="index"
                                        >{{ errorMessage }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </b-tab>
                    <b-tab :disabled="!department_id?true:false" :title="$t('general.tasks')">
                        <div
                            class="row justify-content-between align-items-center mt-1"
                        >
                            <h4 class="col-6 header-title">
                                {{ $t("general.tasks") }}
                            </h4>
                            <!-- start create and printer -->
                            <div class="col-2 header-title">
                                <b-button
                                    type="button"
                                    @click.prevent="changeDepartmentTask(true)"
                                    variant="primary"
                                    class="btn-sm mx-1 font-weight-bold"
                                >
                                    {{ $t("general.Create") }}
                                    <i class="fas fa-plus"></i>
                                </b-button>
                                <!-- end create and printer -->
                            </div>
                        </div>

                        <!-- start .table-responsive-->
                        <div
                            class="table-responsive mb-3 custom-table-theme position-relative"
                        >
                            <!--       start loader       -->
                            <loader size="large" v-if="isLoader" />
                            <!--       end loader       -->
                            <table
                                class="table table-borderless table-hover table-centered m-0"
                            >
                                <thead>
                                <tr>
                                    <th>
                                        <div
                                            class="d-flex justify-content-center"
                                        >
                                          <span>{{
                                                  getCompanyKey(
                                                      "department_task_name_ar"
                                                  )
                                              }}</span>
                                        </div>
                                    </th>
                                    <th>
                                        <div
                                            class="d-flex justify-content-center"
                                        >
                                          <span>{{
                                                  getCompanyKey(
                                                      "department_task_name_en"
                                                  )
                                              }}</span>
                                        </div>
                                    </th>
                                    <th class="do-not-print">
                                        {{ $t("general.Action") }}
                                    </th>
                                </tr>
                                </thead>
                                <tbody v-if="tasks.length > 0">
                                <tr
                                    @dblclick.prevent="changeDepartmentTask(false,data2.id)"
                                    v-for="(data2, index) in tasks"
                                    :key="data2.id"
                                    class="body-tr-custom"
                                >
                                    <td>
                                        <h5 class="m-0 font-weight-normal">
                                            {{ data2.name }}
                                        </h5>
                                    </td>
                                    <td>
                                        <h5 class="m-0 font-weight-normal">
                                            {{ data2.name_e }}
                                        </h5>
                                    </td>
                                    <td class="do-not-print">
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
                                            <div
                                                class="dropdown-menu dropdown-menu-custom"
                                            >
                                                <a
                                                    class="dropdown-item"
                                                    href="#"
                                                    @click="changeDepartmentTask(false,data2.id)"
                                                >
                                                    <div
                                                        class="d-flex justify-content-between align-items-center text-black"
                                                    >
                                                <span>{{
                                                        $t("general.edit")
                                                    }}</span>
                                                        <i
                                                            class="mdi mdi-square-edit-outline text-info"
                                                        ></i>
                                                    </div>
                                                </a>
                                                <a
                                                    class="dropdown-item text-black"
                                                    href="#"
                                                    @click.prevent="
                                                deleteTasks(data2.id)
                                              "
                                                >
                                                    <div
                                                        class="d-flex justify-content-between align-items-center text-black"
                                                    >
                                                <span>{{
                                                        $t("general.delete")
                                                    }}</span>
                                                        <i
                                                            class="fas fa-times text-danger"
                                                        ></i>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                                <tbody v-else>
                                <tr>
                                    <th class="text-center" colspan="11">
                                        {{ $t("general.notDataFound") }}
                                    </th>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- end .table-responsive-->
                    </b-tab>
                </b-tabs>
            </form>
        </b-modal>
        <!--  /create   -->
        <!--  create department task  -->
        <b-modal
            id="create-task-department"
            :title="typeTask ? getCompanyKey('department_task_create_form'):getCompanyKey('department_task_edit_form')"
            title-class="font-18"
            body-class="p-4 "
            size="lg"
            :hide-footer="true"
            @show="resetModalTask"
            @hidden="resetModalHiddenTask"
        >
            <form>
                <div class="mb-3 d-flex justify-content-end">
                    <b-button
                        variant="success"
                        v-if="typeTask"
                        :disabled="!is_disabledTask"
                        @click.prevent="resetFormTask"
                        type="button"
                        :class="[
                          'font-weight-bold px-2',
                          is_disabledTask ? 'mx-2' : '',
                        ]"
                    >
                        {{ $t("general.AddNewRecord") }}
                    </b-button>
                    <template v-if="!is_disabledTask">
                        <b-button
                            variant="success"
                            type="button"
                            class="mx-1"
                            v-if="!isLoader"
                            @click.prevent="AddSubmitTask"
                        >
                            {{ typeTask? $t("general.Add"): $t("general.edit") }}
                        </b-button>

                        <b-button
                            variant="success"
                            class="mx-1"
                            disabled
                            v-else
                        >
                            <b-spinner small></b-spinner>
                            <span class="sr-only"
                            >{{ $t("login.Loading") }}...</span
                            >
                        </b-button>
                    </template>
                    <!-- Emulate built in modal footer ok and cancel button actions -->

                    <b-button
                        variant="danger"
                        type="button"
                        @click.prevent="resetModalHiddenTask"
                    >
                        {{ $t("general.Cancel") }}
                    </b-button>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey("department_task_name_ar") }}
                                <span class="text-danger">*</span>
                            </label>
                            <div dir="rtl">
                                <input
                                    @keyup="arabicValueTask(createTask.name)"
                                    type="text"
                                    class="form-control"
                                    data-create="1"
                                    v-model="$v.createTask.name.$model"
                                    :class="{
                                'is-invalid':
                                  $v.create.name.$error || errors.name,
                                'is-valid':
                                  !$v.create.name.$invalid && !errors.name,
                              }"
                                />
                            </div>
                            <div
                                v-if="!$v.createTask.name.minLength"
                                class="invalid-feedback"
                            >
                                {{ $t("general.Itmustbeatleast") }}
                                {{ $v.createTask.name.$params.minLength.min }}
                                {{ $t("general.letters") }}
                            </div>
                            <div
                                v-if="!$v.createTask.name.maxLength"
                                class="invalid-feedback"
                            >
                                {{ $t("general.Itmustbeatmost") }}
                                {{ $v.createTask.name.$params.maxLength.max }}
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
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey("department_task_name_en") }}
                                <span class="text-danger">*</span>
                            </label>
                            <div dir="ltr">
                                <input
                                    @keyup="englishValueTask(createTask.name_e)"
                                    type="text"
                                    class="form-control englishInput"
                                    data-create="2"
                                    v-model="$v.createTask.name_e.$model"
                                    :class="{
                                'is-invalid':
                                  $v.createTask.name_e.$error || errors.name_e,
                                'is-valid':
                                  !$v.createTask.name_e.$invalid &&
                                  !errors.name_e,
                              }"
                                />
                            </div>
                            <div
                                v-if="!$v.createTask.name_e.minLength"
                                class="invalid-feedback"
                            >
                                {{ $t("general.Itmustbeatleast") }}
                                {{ $v.createTask.name_e.$params.minLength.min }}
                                {{ $t("general.letters") }}
                            </div>
                            <div
                                v-if="!$v.createTask.name_e.maxLength"
                                class="invalid-feedback"
                            >
                                {{ $t("general.Itmustbeatmost") }}
                                {{ $v.createTask.name_e.$params.maxLength.max }}
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
                        <div class="row">
                            <div class="col-6">
                                <label class="mr-2">
                                    {{ getCompanyKey("department_estimated_day") }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="number"
                                    class="form-control"
                                    v-model="createTask.estimate_task_duration.day"
                                />
                            </div>
                            <div class="col-6">
                                <label class="mr-2">
                                    {{ getCompanyKey("department_estimated_time") }}
                                    <span class="text-danger">*</span>
                                </label>
                                <date-picker
                                    type="time"
                                    v-model="createTask.estimate_task_duration.time"
                                    format="HH:mm:ss"
                                    valueType="format"
                                    :confirm="false"
                                >
                                </date-picker>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6"></div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="mr-2">
                                {{
                                    getCompanyKey("department_task_description_ar")
                                }}
                                <span class="text-danger">*</span>
                            </label>
                            <textarea
                                @input="
                              arabicValueDescriptionTask(createTask.description)
                            "
                                v-model="$v.createTask.description.$model"
                                class="form-control"
                                :maxlength="1000"
                                rows="5"
                            ></textarea>
                            <template v-if="errors.description">
                                <ErrorMessage
                                    v-for="(
                                errorMessage, index
                              ) in errors.description"
                                    :key="index"
                                >{{ errorMessage }}</ErrorMessage
                                >
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="mr-2">
                                {{
                                    getCompanyKey("department_task_description_en")
                                }}
                                <span class="text-danger">*</span>
                            </label>
                            <textarea
                                @input="
                              englishValueDescriptionTask(
                                createTask.description_e
                              )
                            "
                                v-model="$v.createTask.description_e.$model"
                                class="form-control"
                                :maxlength="1000"
                                rows="5"
                            ></textarea>
                            <template v-if="errors.description_e">
                                <ErrorMessage
                                    v-for="(
                                errorMessage, index
                              ) in errors.description_e"
                                    :key="index"
                                >{{ errorMessage }}</ErrorMessage
                                >
                            </template>
                        </div>
                    </div>
                </div>
            </form>
        </b-modal>
        <!--  create department task   -->
    </div>
</template>

<script>
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import {maxLength, minLength, requiredIf,required} from "vuelidate/lib/validators";
import adminApi from "../../../api/adminAxios";
import Swal from "sweetalert2";
import {arabicValue, englishValue} from "../../../helper/langTransform";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import Multiselect from "vue-multiselect";
import successError from "../../../helper/mixin/success&error";
import DatePicker from "vue2-datepicker";

export default {
    name: "departmentTask",
    data() {
        return {
            fields: [],
            per_page: 50,
            isLoader: false,
            create: {
                name: "",
                name_e: "",
                supervisors: [],
                attentions: []
            },
            company_id: null,
            createTask: {
                name: "",
                name_e: "",
                description: "",
                description_e: "",
                estimate_task_duration: {
                    day: null,
                    time: ''
                }
            },
            department_id: null,
            errors: {},
            tasks: [],
            employees: [],
            isCustom:false,
            is_disabled: false,
            is_disabledTask: false,
            typeTask: true,
            task_id: null
        };
    },
    validations: {
        create: {
            name: { required: requiredIf(function (model) {
                    return this.isRequired("name");
                }) , minLength: minLength(3), maxLength: maxLength(100) },
            name_e: { required: requiredIf(function (model) {
                    return this.isRequired("name_e");
                }), minLength: minLength(3), maxLength: maxLength(100) },
            attentions: { required: requiredIf(function (model) {
                    return this.isRequired("attentions");
                }) },
            supervisors: { required: requiredIf(function (model) {
                    return this.isRequired("supervisors");
                })}
        },
        createTask: {
            name: { required, minLength: minLength(2), maxLength: maxLength(255) },
            name_e: { required, minLength: minLength(2), maxLength: maxLength(255) },
            description: {required},
            description_e: {required},
        },
    },
    mixins: [transMixinComp,successError],
    props: {
        id: {default: "department-create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/depertments'}
    },
    components: {
        ErrorMessage,
        loader,
        Multiselect,
        DatePicker
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
    },
    methods: {
        async getCustomTableFields() {
            this.isCustom = true;
             await adminApi
                .get(`/customTable/table-columns/general_departments`)
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
            this.create = { name: "", name_e: "",supervisors: [], attentions: []};
            this.createTask = {
                name: "",
                name_e: "",
                description: "",
                description_e: "",
                estimate_task_duration: {
                    day: null,
                    time: ''
                }
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.department_id = null;
            this.is_disabled = false;
            this.is_disabledTask = false;
            this.errors = {};
        },
        arabicValueTask(txt) {
            this.createTask.name = arabicValue(txt);
        },
        englishValueTask(txt) {
            this.createTask.name_e = englishValue(txt);
        },
        arabicValueDescriptionTask(txt){
            this.createTask.description = arabicValue(txt);
        },
        englishValueDescriptionTask(txt){
            this.createTask.description_e = englishValue(txt);
        },
        resetModalHidden() {
            this.defaultData();
            this.$bvModal.hide(this.id);
        },
        arabicValueName(txt){
            this.create.name = arabicValue(txt);
        },
        englishValueName(txt){
            this.create.name_e = englishValue(txt);
        },
        resetModal() {
            this.defaultData();
            setTimeout(async () => {
                if(this.type != 'edit'){
                    if(!this.isPage)  await this.getCustomTableFields();
                    if(this.isVisible('supervisors') || this.isVisible('attentions')) this.getEmployees();
                }else {
                    if(this.idObjEdit.dataObj){
                        let module = this.idObjEdit.dataObj;
                        this.errors = {};
                        this.department_id = module.id;
                        this.create.name = module.name;
                        this.create.name_e = module.name_e;
                        if(this.isVisible('supervisors') || this.isVisible('attentions'))  this.getEmployees();
                        this.create.attentions = module.attentions;
                        this.create.supervisors = module.supervisors;
                        this.getDataTask();
                    }
                }
            },50);
        },
        resetForm() {
            this.defaultData();
        },
        defaultDataTask(edit = null){
            this.createTask = {
                name: "",
                name_e: "",
                description: "",
                description_e: "",
                estimate_task_duration: {
                    day: null,
                    time: ''
                }
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
            this.is_disabledTask = false;
        },
        resetFormTask(){
            this.defaultDataTask();
        },
        resetModalHiddenTask() {
            this.defaultDataTask();
            this.$bvModal.hide(`create-task-department`);
        },
        resetModalTask() {
            if(this.typeTask){
                this.defaultDataTask();
            }else {
                let tasks = this.tasks.find((e) => this.task_id == e.id);
                this.errors = {};
                this.createTask.name = tasks.name;
                this.createTask.name_e = tasks.name_e;
                this.createTask.description = tasks.description;
                this.createTask.description_e = tasks.description_e;
                if (tasks.estimate_task_duration) {
                    this.createTask.estimate_task_duration.day =
                        tasks.estimate_task_duration.day;
                    this.createTask.estimate_task_duration.time =
                        tasks.estimate_task_duration.time;
                }
            }
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

                if(this.type != 'edit') {
                    adminApi
                        .post(this.url, {...this.create, company_id: this.company_id})
                        .then((res) => {
                            this.department_id = res.data.data.id;
                            this.is_disabled = true;
                            if (!this.isPage)
                                this.$emit("created");
                            else
                                this.$emit("getDataTable");

                            setTimeout(() => {
                                this.successFun('Addedsuccessfully');
                            }, 1000);
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
                            this.$emit("getDataTable");
                            setTimeout(() => {
                                this.successFun('Editsuccessfully');
                            }, 1000);
                        })
                        .catch((err) => {
                            if (err.response.data) {
                                this.errors = err.response.data.errors;
                            } else {
                                this.errorFun('Error','Thereisanerrorinthesystem');
                            }
                        })
                        .finally(() => {
                            this.isLoader = true;
                        });
                }
            }
        },
        changeDepartmentTask(isType,id= null){
            this.task_id = id;
            this.typeTask = isType;
            this.$bvModal.show(`create-task-department`);
        },
        AddSubmitTask(id = null) {
            if (!this.createTask.name) {this.createTask.name = this.createTask.name_e;}
            if (!this.createTask.name_e) {this.createTask.name_e = this.createTask.name;}
            if(!this.createTask.description){ this.createTask.description = this.createTask.description_e}
            if(!this.createTask.description_e){ this.createTask.description_e = this.createTask.description}
            this.$v.createTask.$touch();

            if (this.$v.createTask.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};

                if(this.typeTask){
                    adminApi
                        .post(`/department-tasks`, {...this.createTask,department_id: this.department_id ,company_id: this.company_id})
                        .then((res) => {
                            this.is_disabledTask = true;
                            setTimeout(() => {
                                this.successFun('Addedsuccessfully');
                            }, 1000);
                            this.getDataTask();
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
                        .put(`/department-tasks/${this.task_id}`, {
                            ...this.createTask,
                            department_id: this.department_id,
                        })
                        .then((res) => {
                            this.getDataTask();
                            setTimeout(() => {
                                this.successFun('Editsuccessfully');
                            }, 1000);
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
        deleteTasks(id, index) {
            if (Array.isArray(id)) {
                Swal.fire({
                    title: `${this.$t("general.Areyousure")}`,
                    text: `${this.$t("general.Youwontbeabletoreverthis")}`,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: `${this.$t("general.Yesdeleteit")}`,
                    cancelButtonText: `${this.$t("general.Nocancel")}`,
                    confirmButtonClass: "btn btn-success mt-2",
                    cancelButtonClass: "btn btn-danger ml-2 mt-2",
                    buttonsStyling: false,
                }).then((result) => {
                    if (result.value) {
                        this.isLoader = true;
                        adminApi
                            .post(`/department-tasks/bulk-delete`, { ids: id })
                            .then((res) => {
                                this.checkAll = [];
                                this.getDataTask();
                                Swal.fire({
                                    icon: "success",
                                    title: `${this.$t("general.Deleted")}`,
                                    text: `${this.$t("general.Yourrowhasbeendeleted")}`,
                                    showConfirmButton: false,
                                    timer: 1500,
                                });
                            })
                            .catch((err) => {
                                if (err.response.status == 400) {
                                    this.errorFun('Error','CantDeleteRelation');
                                    this.getData();
                                } else {
                                    this.errorFun('Error','Thereisanerrorinthesystem');
                                }
                            })
                            .finally(() => {
                                this.isLoader = false;
                            });
                    }
                });
            } else {
                Swal.fire({
                    title: `${this.$t("general.Areyousure")}`,
                    text: `${this.$t("general.Youwontbeabletoreverthis")}`,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: `${this.$t("general.Yesdeleteit")}`,
                    cancelButtonText: `${this.$t("general.Nocancel")}`,
                    confirmButtonClass: "btn btn-success mt-2",
                    cancelButtonClass: "btn btn-danger ml-2 mt-2",
                    buttonsStyling: false,
                }).then((result) => {
                    if (result.value) {
                        this.isLoader = true;

                        adminApi
                            .delete(`/department-tasks/${id}`)
                            .then((res) => {
                                this.getDataTask();
                                Swal.fire({
                                    icon: "success",
                                    title: `${this.$t("general.Deleted")}`,
                                    text: `${this.$t("general.Yourrowhasbeendeleted")}`,
                                    showConfirmButton: false,
                                    timer: 1500,
                                });
                            })

                            .catch((err) => {
                                if (err.response.status == 400) {
                                    this.errorFun('Error','CantDeleteRelation');
                                    this.getData();
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
        },
        getEmployees() {
             adminApi
                .get(`/employees/get-drop-down`)
                .then((res) => {
                    let l = res.data.data;
                    this.employees = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        getDataTask(page = 1) {
            this.isLoader = true;

            adminApi
                .get(
                    `/department-tasks?page=${page}&per_page=${this.per_page}&department_id=${this.department_id}`
                )
                .then((res) => {
                    let l = res.data;
                    this.tasks = l.data;
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
    }
}
</script>

<style scoped>
form {
    position: relative;
}
</style>
