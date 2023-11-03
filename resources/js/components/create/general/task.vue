<template>
    <div>
        <employee
            :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :id="'employee-create-task'"
            :isPage="false" type="create" :isPermission="isPermission" @created="getEmployees"
        />
        <Department
            :id="'department-create-task'" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
            :isPage="false" type="create" :isPermission="isPermission" @created="getDepartment"
        />
        <customerGeneral
            :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :id="'customer-general-create-task'"
            :isPage="false" type="create" :isPermission="isPermission" @created="getCustomers"
        />
        <TaskDepartment
            :id="'department-task-create-task'"
            :department_id="department_id" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
            :isPage="false" type="create" :isPermission="isPermission" @created="getDepartmentTask()"
        />
        <!--  create   -->
        <b-modal
            :id="id"
            :title="type != 'edit'?getCompanyKey('boardRent_task_create_form'):getCompanyKey('boardRent_task_edit_form')"
            title-class="font-18"
            dialog-class="modal-full-width"
            body-class="modal-body"
            :hide-footer="true"
            @show="resetModal"
            @hidden="resetModalHidden"
        >
            <form>
                <loader size="large" v-if="isCustom && !isPage" />
                <div :class="['d-flex justify-content-end position-absolute',$i18n.locale == 'en' ? 'button-left' : 'button-right']">
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
                        <div class="row">
                            <div v-if="isVisible('type')" class="col-md-4">
                                <div class="form-group">
                                    <label>
                                        {{ getCompanyKey('boardRent_task_type') }}
                                        <span v-if="isRequired('type')" class="text-danger">*</span>
                                    </label>
                                    <multiselect
                                        @input="showTypeModal('create')"
                                        v-model="create.type"
                                        :options="types.map((type) => type)"
                                        :class="{'is-invalid': $v.create.type.$error || errors.type,'is-valid': !$v.create.type.$invalid && !errors.type,}"
                                    >
                                    </multiselect>
                                    <div v-if="!$v.create.type.required" class="invalid-feedback">
                                        {{ $t("general.fieldIsRequired") }}
                                    </div>

                                    <template v-if="errors.type">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.type"
                                                      :key="index">{{ errorMessage }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div v-if="isVisible('employee_id')" class="col-md-4">
                                <div class="form-group">
                                    <label>
                                        {{ getCompanyKey('employee') }}
                                        <span v-if="isRequired('employee_id')" class="text-danger">*</span>
                                    </label>
                                    <multiselect
                                        @input="showEmployeeModal"
                                        v-model="create.employee_id"
                                        :options="employeeDepartments.map((type) => type.id)"
                                        :custom-label=" (opt) => employeeDepartments.find((x) => x.id == opt)?
                                            $i18n.locale == 'ar' ? employeeDepartments.find((x) => x.id == opt).name : employeeDepartments.find((x) => x.id == opt).name_e
                                            : null
                                        "
                                        :class="{'is-invalid': $v.create.employee_id.$error || errors.employee_id,'is-valid': !$v.create.employee_id.$invalid && !errors.employee_id,}"
                                    >
                                    </multiselect>
                                    <div v-if="!$v.create.employee_id.required" class="invalid-feedback">
                                        {{ $t("general.fieldIsRequired") }}
                                    </div>

                                    <template v-if="errors.employee_id">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.employee_id"
                                                      :key="index">{{ errorMessage }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div v-if="isVisible('department_id')" class="col-md-4">
                                <div class="form-group position-relative">
                                    <label class="control-label">
                                        {{ getCompanyKey("boardRent_task_department") }}
                                        <span v-if="isRequired('department_id')" class="text-danger">*</span>
                                    </label>
                                    <multiselect
                                        @input="showDepartmentModal" v-model="create.department_id"
                                        :options="departments.map((type) => type.id)"
                                        :custom-label=" (opt) => departments.find((x) => x.id == opt)?
                                            $i18n.locale == 'ar' ? departments.find((x) => x.id == opt).name : departments.find((x) => x.id == opt).name_e
                                            : null
                                        "
                                        :class="{'is-invalid': $v.create.department_id.$error || errors.department_id,'is-valid': !$v.create.department_id.$invalid && !errors.department_id,}"
                                    >
                                    </multiselect>
                                    <div v-if="$v.create.department_id.$error || errors.department_id" class="text-danger">
                                        {{ $t("general.fieldIsRequired") }}
                                    </div>
                                    <template v-if="errors.department_id">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.department_id" :key="index">{{ errorMessage }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <hr style="margin: 10px 0 !important;  border-top: 1px solid rgb(141 163 159 / 42%)" />
                            </div>
                            <div  v-if="create.type == 'customer' && isVisible('customer_id')" class="col-md-4">
                                <div class="form-group position-relative">
                                    <label
                                        class="control-label">{{ getCompanyKey("customer") }}
                                        <span v-if="isRequired('customer_id')" class="text-danger">*</span>
                                    </label>
                                    <multiselect
                                        @input="showCustomerModal"
                                        :internalSearch="false"
                                        @search-change="searchCustomer"
                                        v-model="$v.create.customer_id.$model"
                                        :options="customers.map((type) => type.id)"
                                        :custom-label=" (opt) => customers.find((x) => x.id == opt)?
                                            $i18n.locale == 'ar' ? customers.find((x) => x.id == opt).name : customers.find((x) => x.id == opt).name_e
                                            : null
                                        "
                                        :class="{'is-invalid': $v.create.customer_id.$error || errors.customer_id,'is-valid': !$v.create.customer_id.$invalid && !errors.customer_id,}"
                                    >
                                    </multiselect>

                                    <template v-if="errors.customer_id">
                                        <ErrorMessage
                                            v-for="(errorMessage, index) in errors.customer_id"
                                            :key="index"
                                        >{{ errorMessage }}
                                        </ErrorMessage
                                        >
                                    </template>
                                </div>
                            </div>
                            <div v-if="create.type == 'customer' && isVisible('contact_person')" class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey('general_customer_contact_person') }}
                                        <span v-if="isRequired('contact_person')" class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        data-create="9"
                                        v-model="$v.create.contact_person.$model"
                                        :class="{
                                            'is-invalid':$v.create.contact_person.$error || errors.contact_person,
                                            'is-valid':!$v.create.contact_person.$invalid && !errors.contact_person
                                        }"
                                    />
                                    <template v-if="errors.contact_person">
                                        <ErrorMessage v-for="(errorMessage,index) in errors.contact_person" :key="index">{{ errorMessage }}</ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div v-if="create.type == 'customer' && isVisible('contact_phone')" class="col-md-4">
                                <div class="form-group">
                                    <label  class="control-label">
                                        {{ getCompanyKey('general_customer_contact_phones') }}
                                        <span v-if="isRequired('contact_phone')" class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        data-create="9"
                                        v-model="$v.create.contact_phone.$model"
                                        :class="{
                                            'is-invalid':$v.create.contact_phone.$error || errors.contact_phone,
                                            'is-valid':!$v.create.contact_phone.$invalid && !errors.contact_phone
                                        }"
                                    />
                                    <template v-if="errors.contact_phone">
                                        <ErrorMessage v-for="(errorMessage,index) in errors.contact_phone" :key="index">{{ errorMessage }}</ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div v-if="create.type == 'equipment' && isVisible('location_id')" class="col-md-4">
                                <div class="form-group position-relative">
                                    <label
                                        class="control-label">{{ getCompanyKey("boardRent_task_location") }}
                                        <span v-if="isRequired('location_id')" class="text-danger">*</span>
                                    </label>
                                    <multiselect
                                        @input="showLocationModal"
                                        v-model="$v.create.location_id.$model"
                                        :options="locations.map((type) => type.id)"
                                        :custom-label=" (opt) => locations.find((x) => x.id == opt)?
                                            $i18n.locale == 'ar' ? locations.find((x) => x.id == opt).name : locations.find((x) => x.id == opt).name_e
                                            : null
                                        "
                                        :class="{'is-invalid': $v.create.location_id.$error || errors.location_id,'is-valid': !$v.create.location_id.$invalid && !errors.location_id,}"
                                    >
                                    </multiselect>

                                    <template v-if="errors.location_id">
                                        <ErrorMessage
                                            v-for="(errorMessage, index) in errors.location_id"
                                            :key="index"
                                        >{{ errorMessage }}
                                        </ErrorMessage
                                        >
                                    </template>
                                </div>
                            </div>
                            <div v-if="create.type == 'equipment' && isVisible('equipment_id')" class="col-md-4">
                                <div class="form-group position-relative">
                                    <label
                                        class="control-label">{{ getCompanyKey("equipment") }}
                                        <span  v-if="isRequired('equipment_id')" class="text-danger">*</span>
                                    </label>
                                    <multiselect
                                        @input="showEquipmentModal"
                                        v-model="equipment_id"
                                        :options="equipments.map((type) => type.id)"
                                        :custom-label=" (opt) => equipments.find((x) => x.id == opt)?
                                            $i18n.locale == 'ar' ? equipments.find((x) => x.id == opt).name : equipments.find((x) => x.id == opt).name_e
                                            : null
                                        "                                      >
                                    </multiselect>
                                </div>
                            </div>
                            <div v-if="create.type == 'equipment' && isVisible('equipment_id')" class="col-md-4">
                                <div class="form-group position-relative">
                                    <label
                                        class="control-label">{{ getCompanyKey("boardRent_task_equipment") }}
                                        <span v-if="isRequired('equipment_id')" class="text-danger">*</span>
                                    </label>
                                    <multiselect
                                        v-model="$v.create.equipment_id.$model"
                                        :options="equipment_childs.map((type) => type.id)"
                                        :custom-label=" (opt) => equipment_childs.find((x) => x.id == opt)?
                                            $i18n.locale == 'ar' ? equipment_childs.find((x) => x.id == opt).name : equipment_childs.find((x) => x.id == opt).name_e
                                            : null
                                        "
                                        :class="{'is-invalid': $v.create.equipment_id.$error || errors.equipment_id,'is-valid': !$v.create.equipment_id.$invalid && !errors.equipment_id,}"
                                    >
                                    </multiselect>

                                    <template v-if="errors.equipment_id">
                                        <ErrorMessage
                                            v-for="(errorMessage, index) in errors.equipment_id"
                                            :key="index"
                                        >{{ errorMessage }}
                                        </ErrorMessage
                                        >
                                    </template>
                                </div>
                            </div>
                            <div v-if="create.type == 'general' && isVisible('task_requirement')" class="col-md-6">
                                <div class="form-group position-relative">
                                    <label
                                        class="control-label">{{ getCompanyKey("boardRent_task_task_requirement") }}
                                        <span v-if="isRequired('task_requirement')"  class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        v-model="$v.create.task_requirement.$model"
                                        :class="{
                                            'is-invalid':$v.create.task_requirement.$error || errors.task_requirement,
                                            'is-valid':!$v.create.task_requirement.$invalid && !errors.task_requirement
                                        }"
                                    />
                                    <template v-if="errors.task_requirement">
                                        <ErrorMessage
                                            v-for="(errorMessage, index) in errors.task_requirement"
                                            :key="index"
                                        >{{ errorMessage }}
                                        </ErrorMessage
                                        >
                                    </template>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <hr style="margin: 10px 0 !important;  border-top: 1px solid rgb(141 163 159 / 42%)" />
                            </div>
                            <div v-if="isVisible('task_title')" class="col-md-4">
                                <div class="form-group">
                                    <label  class="control-label">
                                        {{ getCompanyKey('task_title') }}
                                        <span v-if="isRequired('task_title')" class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        data-create="9"
                                        v-model="$v.create.task_title.$model"
                                        :class="{
                                            'is-invalid':$v.create.task_title.$error || errors.task_title,
                                            'is-valid':!$v.create.task_title.$invalid && !errors.task_title
                                        }"
                                    />
                                    <template v-if="errors.task_title">
                                        <ErrorMessage v-for="(errorMessage,index) in errors.task_title" :key="index">{{ errorMessage }}</ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div v-if="isVisible('department_task_id')" class="col-md-4">
                                <div class="form-group position-relative">
                                    <label class="control-label">
                                        {{ getCompanyKey("task_type") }}
                                        <span v-if="isRequired('department_task_id')" class="text-danger">*</span>
                                    </label>
                                    <multiselect
                                        @input="showDepartmentTaskModal" v-model="create.department_task_id"
                                        :options="departmentTasks.map((type) => type.id)"
                                        :custom-label=" (opt) => departmentTasks.find((x) => x.id == opt)?
                                            $i18n.locale == 'ar' ? departmentTasks.find((x) => x.id == opt).name : departmentTasks.find((x) => x.id == opt).name_e
                                            : null
                                        "
                                        :class="{'is-invalid': $v.create.department_task_id.$error || errors.department_task_id,'is-valid': !$v.create.department_task_id.$invalid && !errors.department_task_id,}"
                                    >
                                    </multiselect>
                                    <div v-if="$v.create.department_task_id.$error || errors.department_task_id" class="text-danger">
                                        {{ $t("general.fieldIsRequired") }}
                                    </div>
                                    <template v-if="errors.department_task_id">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.department_task_id" :key="index">{{ errorMessage }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div v-if="isVisible('status_id')" class="col-md-4">
                                <div class="form-group">
                                    <label>
                                        {{ getCompanyKey('task_status') }}
                                        <span v-if="isRequired('status_id')" class="text-danger">*</span>
                                    </label>
                                    <multiselect
                                        v-model="create.status_id"
                                        :options="statuses.map((type) => type.id)"
                                        :custom-label=" (opt) => statuses.find((x) => x.id == opt)?
                                            $i18n.locale == 'ar' ? statuses.find((x) => x.id == opt).name : statuses.find((x) => x.id == opt).name_e
                                            : null
                                        "
                                        :class="{'is-invalid': $v.create.status_id.$error || errors.status_id,'is-valid': !$v.create.status_id.$invalid && !errors.status_id,}"
                                    >
                                    </multiselect>
                                    <div v-if="!$v.create.status_id.required" class="invalid-feedback">
                                        {{ $t("general.fieldIsRequired") }}
                                    </div>

                                    <template v-if="errors.status_id">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.status_id"
                                                      :key="index">{{ errorMessage }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div  class="col-md-4">
                                <div class="form-group">
                                    <label>
                                        {{ getCompanyKey('task_owners') }}
                                        <span  class="text-danger">*</span>
                                    </label>
                                    <multiselect
                                        :disabled="!(create.type == 'general')"
                                        :multiple="true"
                                        @input="checkIncloudIdOwners"
                                        v-model="create.owners"
                                        :options="employees.map((type) => type.id)"
                                        :custom-label=" (opt) => employees.find((x) => x.id == opt)?
                                            $i18n.locale == 'ar' ? employees.find((x) => x.id == opt).name : employees.find((x) => x.id == opt).name_e
                                            : null
                                        "
                                        :class="{'is-invalid': $v.create.owners.$error || errors.owners,'is-valid': !$v.create.owners.$invalid && !errors.owners,}"
                                    >
                                    </multiselect>
                                    <div v-if="!$v.create.owners.required" class="invalid-feedback">
                                        {{ $t("general.fieldIsRequired") }}
                                    </div>

                                    <template v-if="errors.owners">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.owners"
                                                      :key="index">{{ errorMessage }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div  class="col-md-4">
                                <div class="form-group">
                                    <label>{{ getCompanyKey('task_supervisors') }}<span class="text-danger">*</span></label>
                                    <multiselect
                                        :multiple="true"
                                        @input="checkIncloudIdSupervisors"
                                        v-model="create.supervisors"
                                        :options="employees.map((type) => type.id)"
                                        :custom-label=" (opt) => employees.find((x) => x.id == opt)?
                                            $i18n.locale == 'ar' ? employees.find((x) => x.id == opt).name : employees.find((x) => x.id == opt).name_e
                                            : null
                                        "
                                        :class="{'is-invalid': $v.create.supervisors.$error || errors.supervisors,'is-valid': !$v.create.supervisors.$invalid && !errors.supervisors,}"
                                    >
                                    </multiselect>
                                    <div v-if="!$v.create.supervisors.required" class="invalid-feedback">
                                        {{ $t("general.fieldIsRequired") }}
                                    </div>

                                    <template v-if="errors.supervisors">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.supervisors"
                                                      :key="index">{{ errorMessage }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div  class="col-md-4">
                                <div class="form-group">
                                    <label>{{ getCompanyKey('task_notifications') }}<span class="text-danger">*</span></label>
                                    <multiselect
                                        :multiple="true"
                                        @input="checkIncloudIdNotifications"
                                        v-model="create.notifications"
                                        :options="employees.map((type) => type.id)"
                                        :custom-label=" (opt) => employees.find((x) => x.id == opt)?
                                            $i18n.locale == 'ar' ? employees.find((x) => x.id == opt).name : employees.find((x) => x.id == opt).name_e
                                            : null
                                        "
                                        :class="{'is-invalid': $v.create.notifications.$error || errors.notifications,'is-valid': !$v.create.notifications.$invalid && !errors.notifications,}"
                                    >
                                    </multiselect>
                                    <div v-if="!$v.create.notifications.required" class="invalid-feedback">
                                        {{ $t("general.fieldIsRequired") }}
                                    </div>

                                    <template v-if="errors.notifications">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.notifications"
                                                      :key="index">{{ errorMessage }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <hr style="margin: 10px 0 !important;  border-top: 1px solid rgb(141 163 159 / 42%)" />
                            </div>
                            <div v-if="isVisible('execution_date')" class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey("execution_date") }}
                                        <span v-if="isRequired('execution_date')" class="text-danger">*</span>
                                    </label>
                                    <date-picker
                                        @input="calcDurationCreate"
                                        type="date"
                                        v-model="$v.create.execution_date.$model"
                                        format="YYYY-MM-DD"
                                        valueType="format"
                                        :confirm="false"
                                        :class="{
                                            'is-invalid': $v.create.execution_date.$error || errors.execution_date,
                                            'is-valid': !$v.create.execution_date.$invalid && !errors.execution_date,
                                        }">
                                    </date-picker>
                                    <template v-if="errors.execution_date">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.execution_date" :key="index">
                                            {{
                                                errorMessage
                                            }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div v-if="isVisible('start_time')" class="col-md-2">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey("task_start_time") }}
                                        <span v-if="isRequired('start_time')" class="text-danger">*</span>
                                    </label>
                                    <date-picker
                                        @input="calcDurationCreate"
                                        type="time"
                                        v-model="$v.create.start_time.$model"
                                        format="HH:mm:ss"
                                        valueType="format"
                                        :confirm="false"
                                        :class="{
                                                                'is-invalid': $v.create.start_time.$error || errors.start_time,
                                                                'is-valid': !$v.create.start_time.$invalid && !errors.start_time,
                                                            }">
                                    </date-picker>
                                    <template v-if="errors.start_time">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.start_time" :key="index">
                                            {{
                                                errorMessage
                                            }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div v-if="isVisible('execution_end_date')" class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey("execution_end_date") }}
                                        <span v-if="isRequired('execution_end_date')" class="text-danger">*</span>
                                    </label>
                                    <date-picker
                                        @input="calcDurationCreate"
                                        type="date"
                                        v-model="$v.create.execution_end_date.$model"
                                        format="YYYY-MM-DD"
                                        valueType="format"
                                        :confirm="false"
                                        :class="{
                                                                'is-invalid': $v.create.execution_end_date.$error || errors.execution_end_date,
                                                                'is-valid': !$v.create.execution_end_date.$invalid && !errors.execution_end_date,
                                                            }">
                                    </date-picker>
                                    <template v-if="errors.execution_end_date">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.execution_end_date" :key="index">
                                            {{
                                                errorMessage
                                            }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div v-if="isVisible('end_time')" class="col-md-2">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey("task_end_time") }}
                                        <span v-if="isRequired('end_time')" class="text-danger">*</span>
                                    </label>
                                    <date-picker
                                        @input="calcDurationCreate"
                                        type="time"
                                        v-model="$v.create.end_time.$model"
                                        format="HH:mm:ss"
                                        valueType="format"
                                        :confirm="false"
                                        :class="{
                                                                'is-invalid': $v.create.end_time.$error || errors.end_time,
                                                                'is-valid': !$v.create.end_time.$invalid && !errors.end_time,
                                                            }">
                                    </date-picker>
                                    <template v-if="errors.end_time">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.end_time" :key="index">
                                            {{
                                                errorMessage
                                            }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div v-if="isVisible('execution_duration')" class="col-md-2">
                                <div class="form-group">
                                    <label  class="control-label">
                                        {{ getCompanyKey('execution_duration') }}
                                        <span v-if="isRequired('execution_duration')" class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        :disabled="true"
                                        class="form-control"
                                        data-create="9"
                                        v-model="$v.create.execution_duration.$model"
                                        :class="{
                                                                'is-invalid':$v.create.execution_duration.$error || errors.execution_duration,
                                                                'is-valid':!$v.create.execution_duration.$invalid && !errors.execution_duration
                                                            }"
                                    />
                                    <template v-if="errors.execution_duration">
                                        <ErrorMessage v-for="(errorMessage,index) in errors.execution_duration" :key="index">{{ errorMessage }}</ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div v-if="isVisible('notification_date')" class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey("notification_date") }}
                                        <span v-if="isRequired('notification_date')" class="text-danger">*</span>
                                    </label>
                                    <date-picker
                                        type="date"
                                        v-model="$v.create.notification_date.$model"
                                        format="YYYY-MM-DD"
                                        valueType="format"
                                        :confirm="false"
                                        :class="{
                                                                'is-invalid': $v.create.notification_date.$error || errors.notification_date,
                                                                'is-valid': !$v.create.notification_date.$invalid && !errors.notification_date,
                                                            }">
                                    </date-picker>
                                    <template v-if="errors.notification_date">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.notification_date" :key="index">
                                            {{
                                                errorMessage
                                            }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey("task_priority") }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <multiselect
                                        v-model="$v.create.priority_id.$model"
                                        :options="priorities.map((type) => type.id)"
                                        :custom-label="(opt) => $i18n.locale == 'ar' ? priorities.find((x) => x.id == opt).name : priorities.find((x) => x.id == opt).name_e"
                                        :class="{'is-invalid': $v.create.priority_id.$error || errors.priority_id,'is-valid': !$v.create.priority_id.$invalid && !errors.priority_id,}"
                                    >
                                    </multiselect>
                                    <template v-if="errors.priority_id">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.priority_id" :key="index">
                                            {{
                                                errorMessage
                                            }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div v-if="isVisible('is_closed')" class="col-md-2" >
                                <div class="form-group">
                                    <label class="my-1 mr-2">
                                        {{ getCompanyKey("boardRent_task_is_closed") }}
                                        <span v-if="isRequired('is_closed')" class="text-danger">*</span>
                                    </label>
                                    <b-form-group>
                                        <b-form-radio
                                            class="d-inline-block"
                                            v-model="$v.create.is_closed.$model"
                                            name="some-radios-create"
                                            value="1"
                                        >{{ $t("general.Yes") }}</b-form-radio
                                        >
                                        <b-form-radio
                                            class="d-inline-block m-1"
                                            v-model="$v.create.is_closed.$model"
                                            name="some-radios-create"
                                            value="0"
                                        >{{ $t("general.No") }}</b-form-radio
                                        >
                                    </b-form-group>
                                    <template v-if="errors.is_closed">
                                        <ErrorMessage
                                            v-for="(errorMessage, index) in errors.is_closed"
                                            :key="index"
                                        >{{ errorMessage }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div v-if="isVisible('note')" class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey("boardRent_panel_note") }}
                                        <span v-if="isRequired('note')" class="text-danger">*</span>
                                    </label>
                                    <textarea v-model="$v.create.note.$model" class="form-control" :maxlength="1000" rows="4"></textarea>
                                    <template v-if="errors.note">
                                        <ErrorMessage
                                            v-for="(errorMessage, index) in errors.note"
                                            :key="index"
                                        >{{ errorMessage }}</ErrorMessage
                                        >
                                    </template>
                                </div>
                            </div>
                            <div v-if="isVisible('admin_note')" class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey("boardRent_panel_admin_note") }}
                                        <span v-if="isRequired('admin_note')" class="text-danger">*</span>
                                    </label>
                                    <textarea v-model="$v.create.admin_note.$model" class="form-control" :maxlength="1000" rows="4"></textarea>
                                    <template v-if="errors.admin_note">
                                        <ErrorMessage
                                            v-for="(errorMessage, index) in errors.admin_note"
                                            :key="index"
                                        >{{ errorMessage }}</ErrorMessage
                                        >
                                    </template>
                                </div>
                            </div>
                        </div>
                    </b-tab>
                    <b-tab :disabled="!task_id" :title="$t('general.attachment')">
                        <div class="row">
                            <b-modal
                                id="uploadPhotoTitleCreate"
                                :title="$t('general.ImageUploads')"
                                title-class="font-18"
                                body-class="p-4 "
                                :hide-footer="true"
                                @show="uploadPhotoTitle"
                                @hidden="uploadPhotoTitleHidden"
                            >
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ $t("general.titleFile") }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div dir="rtl">
                                        <input
                                            type="text"
                                            class="form-control"
                                            data-create="1"
                                            v-model="$v.titleFile.$model"
                                            :class="{
                                                                        'is-invalid':$v.titleFile.$error || errors.title,
                                                                        'is-valid':!$v.titleFile.$invalid && !errors.title
                                                                    }"
                                        />
                                    </div>
                                    <div v-if="!$v.titleFile.minLength" class="invalid-feedback">{{ $t('general.Itmustbeatleast') }} {{ $v.titleFile.$params.minLength.min }} {{ $t('general.letters') }}</div>
                                    <div v-if="!$v.titleFile.maxLength" class="invalid-feedback">{{ $t('general.Itmustbeatmost') }}  {{ $v.titleFile.$params.maxLength.max }} {{ $t('general.letters') }}</div>
                                    <template v-if="errors.title">
                                        <ErrorMessage v-for="(errorMessage,index) in errors.title" :key="index">{{ errorMessage }}</ErrorMessage>
                                    </template>
                                </div>

                                <input accept="image/png, image/gif, image/jpeg, image/jpg" type="file" id="uploadImageCreate"
                                       @change.prevent="onImageChanged" class="input-file-upload position-absolute" :class="[
                                                        'd-none',
                                                        {
                                                          'is-invalid': $v.create.media.$error || errors.media,
                                                          'is-valid': !$v.create.media.$invalid && !errors.media,
                                                        },
                                                      ]" />

                                <b-button :disabled="!titleFile && $v.titleFile.$error" @click="changePhoto" variant="success" type="button"
                                          class="mx-1 font-weight-bold px-3" v-if="!isLoader">
                                    {{ $t("general.Add") }}
                                </b-button>
                                <b-button variant="success" class="mx-1" disabled v-else>
                                    <b-spinner small></b-spinner>
                                    <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                </b-button>

                            </b-modal>
                            <div class="col-md-8 my-1">
                                <!-- file upload -->
                                <div class="row align-content-between" style="width: 100%; height: 100%">
                                    <div class="col-12">
                                        <div class="d-flex flex-wrap">
                                            <div :class="[
                                                          'dropzone-previews col-4 position-relative mb-2',
                                                        ]" v-for="(photo, index) in images" :key="photo.id">
                                                <div :class="[
                                                            'card mb-0 shadow-none border',
                                                            images.length - 1 == index ? 'bg-primary' : '',
                                                          ]">
                                                    <div class="p-2">
                                                        <div class="row align-items-center">
                                                            <div class="col-auto" @click="showPhoto = photo.webp">
                                                                <img data-dz-thumbnail :src="photo.webp" class="avatar-sm rounded bg-light"
                                                                     @error="src = img" />
                                                            </div>
                                                            <div class="col pl-0">
                                                                <a href="javascript:void(0);" :class="[
                                                                    'font-weight-bold',
                                                                    images.length - 1 == index
                                                                      ? 'text-white'
                                                                      : 'text-muted',
                                                                  ]" data-dz-name>
                                                                    {{ photo.title }}
                                                                </a>
                                                            </div>
                                                            <!-- Button -->
                                                            <a href="javascript:void(0);" :class="[
                                                                  'btn-danger dropzone-close',
                                                                  $i18n.locale == 'ar'
                                                                    ? 'dropzone-close-rtl'
                                                                    : '',
                                                                ]" data-dz-remove @click.prevent="
                                                                    deleteImageCreate(photo.id, index)
                                                                    ">
                                                                <i class="fe-x"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="footer-image col-12">
                                        <b-button @click="uploadPhotoTitle" variant="success" type="button"
                                                  class="mx-1 font-weight-bold px-3" v-if="!isLoader">
                                            {{ $t("general.Add") }}
                                        </b-button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="show-dropzone">
                                    <img :src="showPhoto" class="img-thumbnail" @error="src = img" />
                                </div>
                            </div>
                        </div>
                    </b-tab>
                </b-tabs>
            </form>
        </b-modal>
        <!--  /create   -->
    </div>
</template>

<script>
import adminApi from "../../../api/adminAxios";
import Swal from "sweetalert2";
import {formatDateOnly} from "../../../helper/startDate";
import {maxLength, minLength, required, requiredIf} from "vuelidate/lib/validators";
import loader from "../../general/loader";
import Multiselect from "vue-multiselect";
import TaskDepartment from "./taskDepartment";
import employee from "./employee";
import Department from "./departmentTask";
import customerGeneral from "./customerGeneral";
import Status from "./status";
import DatePicker from "vue2-datepicker";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import successError from "../../../helper/mixin/success&error";
import ErrorMessage from "../../widgets/errorMessage";
import img from "../../../assets/images/img-1.png";

export default {
    name: "task",
    components: {
        ErrorMessage, loader, Multiselect, employee,
        Department, customerGeneral, TaskDepartment,
        Status, DatePicker
    },
    mixins: [transMixinComp,successError],
    props: {
        id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/tasks'}
    },
    data()  {
        return {
            types: ['customer','general','equipment'],
            fields: [],
            isCustom: false,
            debounce: {},
            company_id: null,
            employees: [],
            employeeDepartments: [],
            departments: [],
            customers: [],
            departmentTasks: [],
            statuses: [],
            isLoader: false,
            isVaildPhone: false,
            create: {
                employee_id: null,
                department_id: null,
                customer_id:null,
                contact_person: '',
                contact_phone:'',
                task_title:'',
                department_task_id:null,
                owners:[],
                supervisors:[],
                notifications:[],
                status_id:null,
                execution_date:this.formatDate(new Date()),
                start_time:'',
                end_time:'',
                execution_duration:'0 Day 0 Minutes',
                notification_date:this.formatDate(new Date()),
                execution_end_date:this.formatDate(new Date()),
                note:'',
                media: [],
                type: 'general',
                equipment_id: null,
                location_id: null,
                priority_id: null,
                admin_note: '',
                is_closed: 0,
                task_requirement: ''
            },
            task_id: null,
            department_id: null,
            errors: {},
            equipments: [],
            locations: [],
            images: [],
            media: {},
            titleFile:"",
            saveImageName: [],
            img,
            showPhoto: img,
            image: '',
            is_disabled: false,
            equipment_childs: [],
            priorities: [],
            equipment_id: null,
            idDelete: null,
        }
    },
    validations: {
        create: {
            employee_id: {required: requiredIf(function (model) {
                    return this.isRequired("employee_id");
                })},
            department_id: {required: requiredIf(function (model) {
                    return this.isRequired("department_id");
                })},
            task_title: {required: requiredIf(function (model) {
                    return this.isRequired("task_title");
                })},
            department_task_id: {required: requiredIf(function (model) {
                    return this.isRequired("department_task_id");
                })},
            owners: {},
            supervisors: {},
            notifications: {},
            status_id: {required: requiredIf(function (model) {
                    return this.isRequired("status_id");
                })},
            execution_date: {required: requiredIf(function (model) {
                    return this.isRequired("execution_date");
                })},
            start_time:{required: requiredIf(function (model) {
                    return this.isRequired("start_time");
                })},
            end_time:{required: requiredIf(function (model) {
                    return this.isRequired("end_time");
                })},
            execution_duration: {required: requiredIf(function (model) {
                    return this.isRequired("execution_duration");
                })},
            notification_date: {required: requiredIf(function (model) {
                    return this.isRequired("notification_date");
                })},
            execution_end_date: {required: requiredIf(function (model) {
                    return this.isRequired("execution_end_date");
                })},
            note: {required: requiredIf(function (model) {
                    return this.isRequired("note");
                })},
            admin_note: {required: requiredIf(function (model) {
                    return this.isRequired("admin_note");
                })},
            media: {},
            type: {required: requiredIf(function (model) {
                    return this.isRequired("type");
                })},
            customer_id: {requiredIf: requiredIf(function (model) {
                    return this.create.type == "customer" && this.isRequired("customer_id");
                })},
            contact_person: {requiredIf: requiredIf(function (model) {
                    return this.create.type == "customer" && this.isRequired("contact_person");
                })},
            contact_phone: {requiredIf: requiredIf(function (model) {
                    return this.create.type == "customer" && this.isRequired("contact_phone");
                })},
            equipment_id: {requiredIf: requiredIf(function (model) {
                    return this.create.type == "equipment" && this.isRequired("equipment_id");
                })},
            location_id: {requiredIf: requiredIf(function (model) {
                    return this.create.type == "equipment" && this.isRequired("location_id");
                })},
            task_requirement: {requiredIf: requiredIf(function (model) {
                    return this.create.type == "general" && this.isRequired("task_requirement");
                })},
            is_closed: {required: requiredIf(function (model) {
                    return this.isRequired("is_closed");
                })},
            priority_id: {required: requiredIf(function (model) {
                    return this.isRequired("priority_id");
                })}
        },
        titleFile:{required,minLength: minLength(2),maxLength: maxLength(100),}
    },
    mounted(){
        this.company_id = JSON.parse(localStorage.getItem("company_id"));
    },
    methods: {
        async getCustomTableFields() {
            this.isCustom = true;
            await adminApi
                .get(`/customTable/table-columns/general_tasks`)
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
        dataCreate() {
            this.create = {
                employee_id: null,
                department_id: null,
                customer_id: null,
                contact_person: '',
                contact_phone:'',
                task_title:'',
                department_task_id:null,
                owners:[],
                supervisors:[],
                notifications:[],
                status_id: 1,
                execution_date:this.formatDate(new Date()),
                start_time:'',
                is_closed: 0,
                task_requirement: '',
                end_time:'',
                execution_duration:'0 Day 0 Minutes',
                notification_date:this.formatDate(new Date()),
                execution_end_date:this.formatDate(new Date()),
                note:'',
                type: 'general',
                equipment_id: null,
                location_id: null,
                priority_id: null,
                admin_note: ''
            };
            this.$nextTick(() => { this.$v.$reset() });
            this.department_id = null;
            this.equipment_id = null;
            this.task_id = null;
            this.$nextTick(() => { this.$v.$reset() });
            this.errors = {};
            this.showPhoto = img;
            this.media = {};
            this.images = [];
            this.is_disabled = false;
        },
        resetModalHidden(){
            this.dataCreate();
            this.$bvModal.hide(this.id);
        },
        resetModal(){
             this.dataCreate();
             setTimeout( async () => {
                if(this.type != 'edit'){
                    if(!this.isPage)  await this.getCustomTableFields();
                    this.getEmployees();
                    this.getEmployeeDepartments();
                    if (this.isVisible("department_id"))  this.getDepartment();
                    if (this.isVisible("status_id"))  this.getStatus();
                    if (this.isVisible("priority_id"))  this.getPriority();
                }else {
                    if(this.idObjEdit.dataObj){
                        let build = this.idObjEdit.dataObj;
                        this.create.type = build.type;
                        this.getEmployees();
                        this.getEmployeeDepartments();
                        if (this.isVisible("department_id"))  this.getDepartment();
                        let owners = [];
                        build.owners.forEach((el)=>{
                            owners.push(el.id);
                        });
                        let supervisors = [];
                        build.supervisors.forEach((el)=>{
                            supervisors.push(el.id);
                        });
                        let notifications = [];
                        build.notifications.forEach((el)=>{
                            notifications.push(el.id);
                        });
                        this.department_id = build.department_id;
                        this.task_id = build.id;
                        this.getDepartmentTask();
                        this.create.employee_id = build.employee_id;
                        this.create.department_id = build.department_id;
                        this.create.task_title = build.task_title;
                        this.create.department_task_id = build.department_task_id;
                        this.create.owners = owners;
                        this.create.supervisors = supervisors;
                        this.create.notifications = notifications;
                        this.create.execution_date = build.execution_date;
                        this.create.start_time = build.start_time;
                        this.create.end_time = build.end_time;
                        this.create.execution_duration = build.execution_duration;
                        this.create.notification_date = build.notification_date;
                        this.create.execution_end_date = build.execution_end_date;
                        this.create.admin_note = build.admin_note;
                        this.create.is_closed = build.is_closed ;
                        this.create.note = build.note;
                        if( this.create.type == 'customer'){
                            this.getCustomers();
                            this.create.customer_id = build.customer_id;
                            this.create.contact_person = build.contact_person;
                            this.create.contact_phone = build.contact_phone;
                        }else if (this.create.type == 'equipment') {
                            this.getLocation();
                            this.create.location_id = build.location_id;
                            this.getEquipment(build.location_id);
                            this.equipment_id = build.equipment.parent_id;
                            this.getEquipmentChild(this.equipment_id);
                            this.create.equipment_id = build.equipment.id;
                        }else{
                            this.create.task_requirement = build.task_requirement;
                        }
                        if (this.isVisible("status_id"))  this.getStatus();
                        this.create.status_id = build.status_id;
                        if (this.isVisible("priority_id")) this.getPriority();
                        this.create.priority_id = build.priority_id;

                        this.images = build.media ?? [];
                        if (this.images && this.images.length > 0) {
                            this.showPhoto = this.images[this.images.length - 1].webp;
                        } else {
                            this.showPhoto = img;
                        }
                        this.errors = {};
                    }
                }
            },50);
        },
        resetForm(){
            this.dataCreate();
        },
        AddSubmit(){
            this.$v.create.$touch();
            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};

                if(this.type != 'edit' && !this.task_id){
                    adminApi
                        .post(this.url, {
                            ...this.create,
                            execution_date: this.create.execution_date.slice(0,10)  + (this.create.start_time ? ` ${this.create.start_time}` : ' 00:00:00'),
                            execution_end_date: this.create.execution_end_date.slice(0,10)  + (this.create.end_time ? ` ${this.create.end_time}` : ' 00:00:00'),
                            company_id: this.$store.getters["auth/company_id"]
                        })
                        .then((res) => {
                            this.is_disabled = true;
                            this.task_id = res.data.data.id;
                            if(!this.isPage)
                                this.$emit("created");
                            else
                                this.$emit("getDataTable");
                            setTimeout(() => {
                                this.successFun('Addedsuccessfully');
                            }, 200);

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
                        .put(`${this.url}/${this.task_id}`, {
                            ...this.create,
                            execution_date: this.create.execution_date.slice(0,10) +  (this.create.start_time ? ` ${this.create.start_time}` : ' 00:00:00'),
                            execution_end_date: this.create.execution_end_date.slice(0,10)  +  (this.create.end_time ? ` ${this.create.end_time}`  : ' 00:00:00')
                        })
                        .then((res) => {
                            this.$bvModal.hide(this.id);
                            this.$emit("getDataTable");
                            this.successFun('Editsuccessfully');
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
        uploadPhotoTitle () {
            this.titleFile="";
            this.$bvModal.show(`uploadPhotoTitleCreate`);
            this.errors = {};
        },
        uploadPhotoTitleHidden(){
            this.titleFile="";
            this.$bvModal.hide(`uploadPhotoTitleCreate`);
            this.errors = {};
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
                    formDate.append("media[][media]", this.media);
                    formDate.append("media[][title]", this.titleFile);
                    adminApi
                        .post(`/media-name`, formDate)
                        .then((res) => {
                            let old_media = [];
                            this.images.forEach((e) => old_media.push(e.id));
                            let new_media = [];
                            res.data.data.forEach((e) => new_media.push(e.id));

                            adminApi
                                .put(`${this.url}/${this.task_id}`, { old_media, media: new_media })
                                .then((res) => {
                                    this.images = res.data.data.media ?? [];
                                    if (this.images && this.images.length > 0) {
                                        this.showPhoto = this.images[this.images.length - 1].webp;
                                    } else {
                                        this.showPhoto = img;
                                    }
                                    this.getData();
                                    this.uploadPhotoTitleHidden();
                                })
                                .catch((err) => {
                                    this.errorFun("Error", "Thereisanerrorinthesystem");
                                });
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
                                        .put(`${this.url}/${this.country_id}`, { old_media, media: new_media })
                                        .then((res) => {
                                            this.images = res.data.data.media ?? [];
                                            if (this.images && this.images.length > 0) {
                                                this.showPhoto = this.images[this.images.length - 1].webp;
                                            } else {
                                                this.showPhoto = img;
                                            }
                                            this.getData();
                                        })
                                        .catch((err) => {
                                            this.errorFun("Error", "Thereisanerrorinthesystem");
                                        });
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
                .put(`${this.url}/${this.task_id}`, { old_media })
                .then((res) => {
                    this.tasks[index] = res.data.data;
                    this.images = res.data.data.media ?? [];
                    if (this.images && this.images.length > 0) {
                        this.showPhoto = this.images[this.images.length - 1].webp;
                    } else {
                        this.showPhoto = img;
                    }
                })
                .catch((err) => {
                    this.errorFun("Error", "Thereisanerrorinthesystem");
                });
        },
        formatDate(value) {
            return formatDateOnly(value);
        },
        getStatus(){
            this.isLoader = true;
             adminApi
                .get(`/statuses/get-drop-down?module_type=bordRent`)
                .then((res) => {
                    let l = res.data.data;
                    if(this.isPermission('create Status')){
                        l.unshift({ id: 0, name: "اضف حاله", name_e: "Add Status" });
                    }
                    this.statuses = l;
                })
                .catch((err) => {
                    this.errorFun("Error", "Thereisanerrorinthesystem");
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        getEmployeeDepartments() {
             adminApi
                .get(`/employees/get-drop-down?depertment=1`)
                .then((res) => {
                    let l = res.data.data;
                    if(this.isPermission('create Employee')){
                        l.unshift({ id: 0, name: "اضف موظف", name_e: "Add Employee" });
                    }
                    this.employeeDepartments = l;
                })
                .catch((err) => {
                    this.errorFun("Error", "Thereisanerrorinthesystem");
                });
        },
        getEmployees() {
             adminApi
                .get(`/employees/get-drop-down`)
                .then((res) => {
                    let l = res.data.data;
                    this.employees = l;
                })
                .catch((err) => {
                    this.errorFun("Error", "Thereisanerrorinthesystem");
                });
        },
        showEmployeeModal() {
            if (this.create.employee_id == 0) {
                this.$bvModal.show("employee-create-task");
                this.create.employee_id = null;
            }else {
                let customer = this.customerDepartment(this.create.employee_id);
                if (customer)
                {
                    this.create.owners = [];
                    this.create.department_id = customer.department_id;
                    let department = this.departments.find((el) => el.id ==  this.create.department_id)
                    this.create.supervisors = department.supervisors ? department.supervisors : [];
                    this.department_id = customer.department_id;
                    this.create.owners.push(this.create.employee_id);
                    this.getDepartmentTask();
                }
            }
        },
        customerDepartment(id) {
            if (this.employees && id)
            {
                return this.employees.find(e => id == e.id);
            }
        },
        getDepartment() {
            this.isLoader = true;
            adminApi
                .get(`/depertments/get-drop-down?employees=1`)
                .then((res) => {
                    let l = res.data.data;
                    if(this.isPermission('create Department')){
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
        showDepartmentModal() {
            if (this.create.department_id == 0) {
                this.$bvModal.show("department-create-task");
                this.create.department_id = null;
            }else {
                let department = this.departments.find((el) => el.id ==  this.create.department_id)
                this.department_id = this.create.department_id;
                this.create.supervisors = department.supervisors ? department.supervisors : [];
                this.getDepartmentTask();
            }
        },
        getCustomers(search='') {
            this.isLoader = true;
            adminApi
                .get(`/general-customer/get-drop-down?limet=10&company_id=${this.company_id}${search? '&search=${search}&columns[0]=name&columns[1]=name_e' : ''}`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({id: 0, name: "اضافة زبون", name_e: "Add customer"});
                    this.customers = l;
                    this.isLoader = false;
                })
                .catch((err) => {
                    this.errorFun("Error", "Thereisanerrorinthesystem");
                });
        },
        showCustomerModal() {
            if (this.create.customer_id == 0) {
                this.$bvModal.show("customer-general-create-task");
                this.create.customer_id = null;
            }else {
                let customer = this.getCustomerData(this.create.customer_id)
                if (customer)
                {
                    this.create.contact_person = customer.contact_person;
                    this.create.contact_phone = customer.contact_phone;
                }
            }
        },
        getCustomerData (id) {
            if (this.customers && id)
            {
                return this.customers.find(e => id == e.id);
            }
        },
        searchCustomer(e) {
            let search = e??'';
            clearTimeout(this.debounce);
            this.debounce = setTimeout(() => {
                this.getCustomers(search);
            }, 500);

        },
        getDepartmentTask() {
            this.isLoader = true;
            adminApi
                .get(`/department-tasks/get-drop-down?department_id=${this.department_id}`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({id: 0, name: "اضافة مهمة", name_e: "Add Task"});
                    this.departmentTasks = l;
                    this.isLoader = false;
                })
                .catch((err) => {
                    this.errorFun("Error", "Thereisanerrorinthesystem");
                });
        },
        showDepartmentTaskModal() {
            if (this.create.department_task_id == 0) {
                this.$bvModal.show("department-task-create-task");
                this.create.department_task_id = null;
            }
        },
        getEquipment(location){
            this.isLoader = true;
             adminApi
                .get(`/equipments/get-drop-down?location_id=${location}`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف معده", name_e: "Add Equipment" });
                    this.equipments = l;
                })
                .catch((err) => {
                    this.errorFun("Error", "Thereisanerrorinthesystem");
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        getEquipmentChild(child){
            this.isLoader = true;
             adminApi
                .get(`/equipments/get-drop-down?equipment_id=${child}`)
                .then((res) => {
                    let l = res.data.data;
                    this.equipment_childs = l;
                })
                .catch((err) => {
                    this.errorFun("Error", "Thereisanerrorinthesystem");
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        showEquipmentModal() {
            if (this.equipment_id == 0) {
                this.$bvModal.show("equipment-create");
            }else {
                 this.getEquipmentChild(this.equipment_id)
            }
        },
        getLocation(){
            this.isLoader = true;
             adminApi
                .get(`/locations/get-drop-down`)
                .then((res) => {
                    let l = res.data.data;
                    this.locations = l;
                })
                .catch((err) => {
                    this.errorFun("Error", "Thereisanerrorinthesystem");
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        showLocationModal() {
            if (this.create.location_id == 0) {
                this.$bvModal.show("location-create");
                this.create.location_id = null;
            }else {
                 this.getEquipment(this.create.location_id);
            }
        },
        checkIncloudIdOwners(e) {
            let employee_id = e[e.length-1];
            if (employee_id)
            {
                if (this.create.supervisors.includes(employee_id))
                {
                    this.create.supervisors.splice(this.create.supervisors.indexOf(employee_id),1)
                }
                if (this.create.notifications.includes(employee_id))
                {
                    this.create.notifications.splice(this.create.notifications.indexOf(employee_id),1)
                }
            }
        },
        checkIncloudIdSupervisors(e) {
            let employee_id = e[e.length-1];
            if (employee_id)
            {
                if (this.create.owners.includes(employee_id))
                {
                    this.create.owners.splice(this.create.owners.indexOf(employee_id),1)
                }
                if (this.create.notifications.includes(employee_id))
                {
                    this.create.notifications.splice(this.create.notifications.indexOf(employee_id),1)
                }
            }
        },
        checkIncloudIdNotifications(e) {
            let employee_id = e[e.length-1];
            if (employee_id)
            {
                if (this.create.owners.includes(employee_id))
                {
                    this.create.owners.splice(this.create.owners.indexOf(employee_id),1)
                }
                if (this.create.supervisors.includes(employee_id))
                {
                    this.create.supervisors.splice(this.create.supervisors.indexOf(employee_id),1)
                }
            }
        },
        calcDurationCreate() {
            let TotalDays = 0 ;
            let TotalTime = '0 Minutes';
            let execution_date = new Date(this.create.execution_date).getTime();
            let execution_end_date = new Date(this.create.execution_end_date).getTime();
            if (execution_date < execution_end_date)
            {
                let difference = execution_end_date - execution_date;
                TotalDays = Math.ceil(difference / (1000 * 3600 * 24));
            }

            // --------- calc Time --------------
            var today = new Date().toJSON().slice(0, 10).replace(/-/g, '/');

            let startTime = new Date(today + " " +this.create.start_time);
            let endTime = new Date(today + " " +this.create.end_time);
            if (endTime > startTime){
                var difference = endTime.getTime() - startTime.getTime();

                TotalTime = Math.round(difference / 60000) + " Minutes";
            }

            this.create.execution_duration = `${TotalDays} Days, ${TotalTime}`

        },
        showTypeModal(){
                if(this.create.type == "equipment") {
                    this.create.equipment_id = null;
                    this.create.location_id = null;
                    this.create.priority_id = null;
                    this.equipments = [];
                    this.equipment_childs = [];
                    if(!(this.locations.length > 0))  this.getLocation();
                }else if(this.create.type == "customer") {
                    this.create.customer_id = null;
                    this.create.priority_id = null;
                    this.create.contact_person = '';
                    this.create.contact_phone = '';
                    if(this.customers.length == 0)  this.getCustomers();
                }else {
                    this.create.task_requirement = '';
                }
        },
        getPriority() {
            this.isLoader = true;

            adminApi
                .get(`/priorities/get-drop-down`)
                .then((res) => {
                    let l = res.data.data;
                    this.priorities = l;
                })
                .catch((err) => {
                    this.errorFun("Error", "Thereisanerrorinthesystem");
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
    }
}
</script>

<style scoped>
form {
    position: relative;
}
.dropdown-menu-custom-company.dropdown .dropdown-menu {
    padding: 5px 10px !important;
    overflow-y: scroll;
    height: 300px;
}
.button-left {
    right: 20px;
    left: unset;
}
.button-right {
    right: unset;
    left: 20px;
}

.modal-body {
    padding: 0 !important;
}

.modal-body .tab-content {
    padding: 10px 60px 30px !important;
}
</style>
