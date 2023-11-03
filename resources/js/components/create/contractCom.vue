<template>
    <div>
        <Saleman :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getSalesmen" />
        <customerGeneral :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getCustomers" />
        <Branch :id="'create_branch'" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getBranches" />
        <InstallmentPlan :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getInstallmentPlans" />
        <Building :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getBuildings" />
        <transactionBreak :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :opening="openingBreak" />
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row justify-content-between align-items-center mb-2">
                            <h4 class="header-title">{{ $t("general.contractTable") }}</h4>
                            <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                                <div class="d-inline-block" style="width: 22.2%">
                                    <!-- Basic dropdown -->
                                    <b-dropdown variant="primary" :text="$t('general.searchSetting')" ref="dropdown"
                                        class="btn-block setting-search">
                                        <b-form-checkbox v-model="filterSetting" value="salesman_id" class="mb-1">{{
                                            getCompanyKey("sale_man") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="customer_id" class="mb-1">{{
                                            getCompanyKey("customer") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="branch_id" class="mb-1">{{
                                            getCompanyKey("branch") }}
                                        </b-form-checkbox>
                                    </b-dropdown>
                                    <!-- Basic dropdown -->
                                </div>

                                <div class="d-inline-block position-relative" style="width: 77%">
                                    <span
                                        :class="['search-custom position-absolute', $i18n.locale == 'ar' ? 'search-custom-ar' : '',]">
                                        <i class="fe-search"></i>
                                    </span>
                                    <input class="form-control" style="display: block; width: 93%; padding-top: 3px"
                                        type="text" v-model.trim="search" :placeholder="`${$t('general.Search')}...`" />
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-between align-items-center mb-2 px-1">
                            <div class="col-md-3 d-flex align-items-center mb-1 mb-xl-0">
                                <b-button v-b-modal.create v-if="isPermission('create contract RealState') || isPermission('create contract RP')" variant="primary" class="btn-sm mx-1 font-weight-bold">
                                    {{ $t("general.Create") }}
                                    <i class="fas fa-plus"></i>
                                </b-button>
                                <div class="d-inline-flex">
                                    <button @click="ExportExcel('xlsx')" class="custom-btn-dowonload">
                                        <i class="fas fa-file-download"></i>
                                    </button>
                                    <button v-print="'#printReservation'" class="custom-btn-dowonload">
                                        <i class="fe-printer"></i>
                                    </button>
                                    <button class="custom-btn-dowonload" @click="$bvModal.show(`modal-edit-${checkAll[0]}`)"
                                        v-if="checkAll.length == 1 && (isPermission('update contract RealState') || isPermission('update contract RP'))">
                                        <i class="mdi mdi-square-edit-outline"></i>
                                    </button>
                                    <!-- start mult delete  -->
                                    <button class="custom-btn-dowonload" v-if="checkAll.length > 1 && (isPermission('delete contract RealState') || isPermission('delete contract RP'))"
                                        @click.prevent="deleteScreenButton(checkAll)">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <!-- end mult delete  -->
                                    <!--  start one delete  -->
                                    <button class="custom-btn-dowonload" v-if="checkAll.length == 1 && (isPermission('delete contract RealState') || isPermission('delete contract RP'))"
                                        @click.prevent="deleteScreenButton(checkAll[0])">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <!--  end one delete  -->
                                </div>
                            </div>
                            <div class="col-xs-10 col-md-9 col-lg-7 d-flex align-items-center justify-content-end">
                                <div>
                                    <b-button class="mx-1 custom-btn-background">
                                        {{ $t("general.filter") }}
                                        <i class="fas fa-filter"></i>
                                    </b-button>
                                    <b-button class="mx-1 custom-btn-background">
                                        {{ $t("general.group") }}
                                        <i class="fe-menu"></i>
                                    </b-button>
                                    <!-- Basic dropdown -->
                                    <b-dropdown variant="primary"
                                        :html="`${$t('general.setting')} <i class='fe-settings'></i>`" ref="dropdown"
                                        class="dropdown-custom-ali">
                                        <b-form-checkbox v-model="setting.date" class="mb-1">{{
                                            getCompanyKey("reservation_date") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="setting.customer_id" class="mb-1">{{
                                            getCompanyKey("customer") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="setting.salesman_id" class="mb-1">
                                            {{ getCompanyKey("sale_man") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="setting.branch_id" class="mb-1">
                                            {{ getCompanyKey("branch") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="setting.serial_id" class="mb-1">
                                            {{ getCompanyKey("reservation_serial") }}
                                        </b-form-checkbox>

                                        <div class="d-flex justify-content-end">
                                            <a href="javascript:void(0)" class="btn btn-primary btn-sm">{{
                                                $t("general.Apply")
                                            }}</a>
                                        </div>
                                    </b-dropdown>
                                    <!-- Basic dropdown -->
                                    <!-- start Pagination -->
                                    <div class="d-inline-flex align-items-center pagination-custom">
                                        <div class="d-inline-block" style="font-size: 15px">
                                            {{ contractsPagination.from }}-{{ contractsPagination.to }}
                                            /
                                            {{ contractsPagination.total }}
                                        </div>
                                        <div class="d-inline-block">
                                            <a href="javascript:void(0)" :style="{
                                                'pointer-events':
                                                    contractsPagination.current_page == 1 ? 'none' : '',
                                            }" @click.prevent="getData(contractsPagination.current_page - 1)">
                                                <span>&lt;</span>
                                            </a>
                                            <input type="text" @keyup.enter="getDataCurrentPage()" v-model="current_page"
                                                class="pagination-current-page" />
                                            <a href="javascript:void(0)" :style="{
                                                'pointer-events':
                                                    contractsPagination.last_page ==
                                                        contractsPagination.current_page
                                                        ? 'none'
                                                        : '',
                                            }" @click.prevent="getData(contractsPagination.current_page + 1)">
                                                <span>&gt;</span>
                                            </a>
                                        </div>
                                    </div>
                                    <!-- end Pagination -->
                                </div>
                            </div>
                        </div>

                        <!--  create   -->
                        <b-modal dialog-class="modal-full-width" id="create" :title="getCompanyKey('contract_create_form')"
                            title-class="font-18" body-class="p-4 " :hide-footer="true" @show="resetModal"
                            @hidden="resetModalHidden">
                            <form>
                                <div class="mb-3 d-flex justify-content-end">
                                    <b-button variant="success" :disabled="!is_disabled" @click.prevent="resetForm"
                                        type="button" :class="['font-weight-bold px-2 mx-2']">
                                        {{ $t("general.AddNewRecord") }}
                                    </b-button>

                                    <b-button variant="danger" type="button" @click.prevent="$bvModal.hide(`create`)">
                                        {{ $t("general.Cancel") }}
                                    </b-button>
                                </div>
                                <b-tabs nav-class="nav-tabs nav-bordered">
                                    <b-tab :title="$t('general.DataEntry')" active>
                                        <div class="row">
                                            <div class="col-md-3 position-relative">
                                                <div class="form-group">
                                                    <label>{{ getCompanyKey("branch") }}</label>
                                                    <multiselect @input="showBranchModal" v-model="create.branch_id"
                                                        :options="branches.map((type) => type.id)" :custom-label="
                                                            (opt) =>
                                                                $i18n.locale == 'ar'
                                                                    ? branches.find((x) => x.id == opt).name
                                                                    : branches.find((x) => x.id == opt).name_e
                                                        " :class="{
    'is-invalid':
        $v.create.branch_id.$error || errors.branch_id,
}">
                                                    </multiselect>
                                                    <div v-if="!$v.create.branch_id.required" class="invalid-feedback">
                                                        {{ $t("general.fieldIsRequired") }}
                                                    </div>

                                                    <template v-if="errors.branch_id">
                                                        <ErrorMessage v-for="(errorMessage, index) in errors.branch_id"
                                                            :key="index">{{ errorMessage }}
                                                        </ErrorMessage>
                                                    </template>
                                                </div>
                                            </div>
                                            <div class="col-md-3 position-relative">
                                                <div class="form-group">
                                                    <label class="control-label">
                                                        {{ getCompanyKey("reservation_date") }}
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <date-picker type="date" v-model="date" defaultValue
                                                        @change="v_dateCreate" confirm></date-picker>
                                                    <template v-if="errors.date">
                                                        <ErrorMessage v-for="(errorMessage, index) in errors.date"
                                                            :key="index">{{
                                                                errorMessage }}
                                                        </ErrorMessage>
                                                    </template>
                                                </div>
                                            </div>
                                            <div class="col-md-3 position-relative">
                                                <div class="form-group">
                                                    <label>{{ getCompanyKey("customer") }}</label>
                                                    <multiselect @input="showCustomerModal" v-model="create.customer_id"
                                                        :options="customers.map((type) => type.id)" :custom-label="
                                                            (opt) =>
                                                                $i18n.locale == 'ar'
                                                                    ? customers.find((x) => x.id == opt).name
                                                                    : customers.find((x) => x.id == opt).name_e
                                                        " :class="{
    'is-invalid':
        $v.create.customer_id.$error || errors.customer_id,
}">
                                                    </multiselect>
                                                    <div v-if="!$v.create.customer_id.required" class="invalid-feedback">
                                                        {{ $t("general.fieldIsRequired") }}
                                                    </div>

                                                    <template v-if="errors.customer_id">
                                                        <ErrorMessage v-for="(errorMessage, index) in errors.customer_id"
                                                            :key="index">{{ errorMessage }}
                                                        </ErrorMessage>
                                                    </template>
                                                </div>
                                            </div>
                                            <div class="col-md-3 position-relative">
                                                <div class="form-group">
                                                    <label>{{ getCompanyKey("sale_man") }}</label>
                                                    <multiselect @input="showSaleManModal" v-model="create.salesman_id"
                                                        :options="salesmen.map((type) => type.id)" :custom-label="
                                                            (opt) =>
                                                                $i18n.locale == 'ar'
                                                                    ? salesmen.find((x) => x.id == opt).name
                                                                    : salesmen.find((x) => x.id == opt).name_e
                                                        " :class="{
    'is-invalid':
        $v.create.salesman_id.$error || errors.salesman_id,
}">
                                                    </multiselect>
                                                    <div v-if="!$v.create.salesman_id.required" class="invalid-feedback">
                                                        {{ $t("general.fieldIsRequired") }}
                                                    </div>
                                                    <template v-if="errors.salesman_id">
                                                        <ErrorMessage v-for="(errorMessage, index) in errors.button_id"
                                                            :key="index">{{ errorMessage }}
                                                        </ErrorMessage>
                                                    </template>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">
                                                        {{ getCompanyKey("reservation_start_date") }}
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <date-picker type="date" v-model="start_date" defaultValue
                                                        @change="v_startCreate" confirm></date-picker>
                                                    <div v-if="!$v.create.start_date.beforeEndDate"
                                                        class="invalid-feedback">
                                                        {{ $t("general.startShoudBeforeEndDate") }}
                                                    </div>
                                                    <template v-if="errors.start_date">
                                                        <ErrorMessage v-for="(errorMessage, index) in errors.start_date"
                                                            :key="index">{{
                                                                errorMessage }}
                                                        </ErrorMessage>
                                                    </template>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">
                                                        {{ getCompanyKey("reservation_end_date") }}
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <date-picker type="date" v-model="end_date" defaultValue
                                                        @change="v_endCreate" confirm></date-picker>
                                                    <div v-if="!$v.create.end_date.beforeEndDate" class="invalid-feedback">
                                                        {{ $t("general.startShoudBeforeEndDate") }}
                                                    </div>
                                                    <template v-if="errors.end_date">
                                                        <ErrorMessage v-for="(errorMessage, index) in errors.end_date"
                                                            :key="index">
                                                            {{
                                                                errorMessage }}
                                                        </ErrorMessage>
                                                    </template>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="page-content">
                                                    <div class="px-0">
                                                        <div class="row mt-4">
                                                            <div class="col-12 col-lg-12">
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="text-center text-150">
                                                                            <i style="font-size:20px"
                                                                                class="fa fa-book fa-2x text-success-m2 mr-1"></i>
                                                                            <span class="text-default-d3">{{
                                                                                $t("general.contract") }}</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!-- .row -->
                                                                <hr class="row brc-default-l1 mx-n1 mb-4" />
                                                                <div class="mt-4">
                                                                    <div
                                                                        class="row text-600 text-white bgc-default-tp1 py-25">
                                                                        <div class="col-1">#</div>
                                                                        <div class="col-2">{{ getCompanyKey("building") }}
                                                                        </div>
                                                                        <div class="col-2">{{ getCompanyKey("unit") }}</div>
                                                                        <div class="col-2">{{
                                                                            getCompanyKey("reservation_installment_plan") }}
                                                                        </div>
                                                                        <div class="col-2"> {{ getCompanyKey("unit_value")
                                                                        }}</div>
                                                                        <div class="col-2"> {{
                                                                            getCompanyKey("reservation_value") }}</div>
                                                                        <div class="col-1">{{ $t("general.Action") }}</div>
                                                                    </div>
                                                                    <div v-for="(item, index) in create.details"
                                                                        class="text-95  text-secondary-d3">
                                                                        <Unit :companyKeys="companyKeys"
                                                                            :defaultsKeys="defaultsKeys"
                                                                            :id="`create-unit-${index}`"
                                                                            @created="getUnits(create.details[index].building_id, index)" />

                                                                        <div class="row mb-2 mb-sm-0 py-25"
                                                                            :class="index % 2 == 0 ? 'bgc-default-l4' : ''">
                                                                            <div class="col-1">
                                                                                {{ index + 1 }}
                                                                            </div>

                                                                            <div class="col-2">
                                                                                <multiselect :id="`create-${index}-1`"
                                                                                    @input="getUnits(create.details[index].building_id, index)"
                                                                                    v-model="$v.create.details.$each[index].building_id.$model"
                                                                                    :options="buildings.map((type) => type.id)"
                                                                                    :custom-label="
                                                                                        (opt) =>
                                                                                            $i18n.locale == 'ar'
                                                                                                ? buildings.find((x) => x.id == opt).name
                                                                                                : buildings.find((x) => x.id == opt).name_e
                                                                                    " :class="{
    'is-invalid':
        $v.create.details.$each[index].building_id.$error || errors.building_id,
}">
                                                                                </multiselect>
                                                                                <div v-if="!$v.create.details.$each[index].building_id.required"
                                                                                    class="invalid-feedback">
                                                                                    {{ $t("general.fieldIsRequired") }}
                                                                                </div>
                                                                                <template v-if="errors.building_id">
                                                                                    <ErrorMessage
                                                                                        v-for="(errorMessage, index) in errors.building_id"
                                                                                        :key="index">{{ errorMessage
                                                                                        }}
                                                                                    </ErrorMessage>
                                                                                </template>
                                                                            </div>
                                                                            <div class="col-2">
                                                                                <multiselect :id="`create-${index}-2`"
                                                                                    @input="showUnitModal(index)"
                                                                                    v-model="$v.create.details.$each[index].unit_id.$model"
                                                                                    :options="multUnits[index].units.map((type) => type.id)"
                                                                                    :custom-label="
                                                                                        (opt) =>
                                                                                            $i18n.locale == 'ar'
                                                                                                ? multUnits[index].units.find((x) => x.id == opt).name
                                                                                                : multUnits[index].units.find((x) => x.id == opt).name_e
                                                                                    " :class="{
    'is-invalid':
        $v.create.details.$each[index].unit_id.$error || errors.unit_id,
}">
                                                                                </multiselect>
                                                                                <div v-if="!$v.create.details.$each[index].unit_id.required"
                                                                                    class="invalid-feedback">
                                                                                    {{ $t("general.fieldIsRequired") }}
                                                                                </div>
                                                                                <template v-if="errors.unit_id">
                                                                                    <ErrorMessage
                                                                                        v-for="(errorMessage, index) in errors.unit_id"
                                                                                        :key="index">
                                                                                        {{ errorMessage }}
                                                                                    </ErrorMessage>
                                                                                </template>
                                                                            </div>
                                                                            <div class="col-2">
                                                                                <multiselect :id="`create-${index}-3`"
                                                                                    @input="showInstallmentPlanModal(index)"
                                                                                    v-model="$v.create.details.$each[index].installment_payment_plans_id.$model"
                                                                                    :options="installment_plans.map((type) => type.id)"
                                                                                    :custom-label="
                                                                                        (opt) =>
                                                                                            $i18n.locale == 'ar'
                                                                                                ? installment_plans.find((x) => x.id == opt).name
                                                                                                : installment_plans.find((x) => x.id == opt).name_e
                                                                                    " :class="{
    'is-invalid':
        $v.create.details.$each[index].installment_payment_plans_id.$error || errors.installment_payment_plans_id,
}">
                                                                                </multiselect>
                                                                                <div v-if="!$v.create.details.$each[index].installment_payment_plans_id.required"
                                                                                    class="invalid-feedback">
                                                                                    {{ $t("general.fieldIsRequired") }}
                                                                                </div>
                                                                                <template
                                                                                    v-if="errors.installment_payment_plans_id">
                                                                                    <ErrorMessage
                                                                                        v-for="(errorMessage, index) in errors.installment_payment_plans_id"
                                                                                        :key="index">
                                                                                        {{ errorMessage }}
                                                                                    </ErrorMessage>
                                                                                </template>
                                                                            </div>
                                                                            <div class="col-2">
                                                                                <input
                                                                                    @keyup.enter="moveEnter('create', index, 5)"
                                                                                    :id="`create-${index}-4`"
                                                                                    v-model.number="$v.create.details.$each[index].unit_value.$model"
                                                                                    @input="changeValue(index)"
                                                                                    class="form-control" step=".01"
                                                                                    type="number" :class="{
                                                                                        'is-invalid':
                                                                                            $v.create.details.$each[index].unit_value.$error || errors.unit_value || !vaildUnitReservation[index].vaildNumber,
                                                                                        'is-valid': !$v.create.details.$each[index].unit_value.$invalid && !errors.unit_value && vaildUnitReservation[index].vaildNumber,
                                                                                    }" />
                                                                                <div v-if="!$v.create.details.$each[index].unit_value.required"
                                                                                    class="invalid-feedback">
                                                                                    {{ $t("general.fieldIsRequired") }}
                                                                                </div>
                                                                                <div v-if="!vaildUnitReservation[index].vaildNumber"
                                                                                    class="invalid-feedback">
                                                                                    {{
                                                                                        $t("general.reservationLessThanUnitVal")
                                                                                    }}
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-2">
                                                                                <input
                                                                                    @keyup.enter="moveEnter('create', index, 6)"
                                                                                    :id="`create-${index}-5`"
                                                                                    @input="changeValue(index)" step=".01"
                                                                                    v-model.number="$v.create.details.$each[index].reservation_value.$model"
                                                                                    class="form-control" type="number"
                                                                                    :class="{
                                                                                        'is-invalid':
                                                                                            $v.create.details.$each[index].reservation_value.$error || errors.reservation_value || !vaildUnitReservation[index].vaildNumber,
                                                                                        'is-valid': !$v.create.details.$each[index].reservation_value.$invalid && !errors.reservation_value && vaildUnitReservation[index].vaildNumber,
                                                                                    }" />
                                                                                <div v-if="!$v.create.details.$each[index].reservation_value.required"
                                                                                    class="invalid-feedback">
                                                                                    {{ $t("general.fieldIsRequired") }}
                                                                                </div>
                                                                                <div v-if="!vaildUnitReservation[index].vaildNumber"
                                                                                    class="invalid-feedback">
                                                                                    {{
                                                                                        $t("general.reservationLessThanUnitVal")
                                                                                    }}
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-1">
                                                                                <button v-if="create.details.length > 1"
                                                                                    type="button"
                                                                                    @click.prevent="removeNewField(index)"
                                                                                    class="custom-btn-dowonload">
                                                                                    <i class="fas fa-trash-alt"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row border-b-2 brc-default-l2"></div>
                                                                    <div class="row mt-3">
                                                                        <div
                                                                            class="col-12 col-sm-7 text-grey-d2 text-95 mt-2 mt-lg-0">
                                                                            {{ $t("general.Extra_note") }}
                                                                        </div>

                                                                        <div
                                                                            class="col-12 col-sm-5 text-grey text-90 order-first order-sm-last">
                                                                            <div
                                                                                class="row my-2 align-items-center bgc-primary-l3 p-2">
                                                                                <div class="col-7 text-right">
                                                                                    {{ $t("general.Total_Amount") }}
                                                                                </div>
                                                                                <div class="col-5">
                                                                                    <span
                                                                                        class="text-150 text-success-d3 opacity-2">
                                                                                        {{ total ? total : '0.00' }}
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                    <hr />
                                                                    <div>
                                                                        <span class="text-secondary-d1 text-105">{{
                                                                            $t("general.Thank_you") }}</span>
                                                                        <template v-if="total && create.details.length > 0">
                                                                            <b-button v-if="!isLoader" variant="primary"
                                                                                class="btn btn-info btn-bold px-4 float-right mt-3 mx-2 mt-lg-0"
                                                                                @click.prevent="AddSubmit">
                                                                                {{ $t('general.Break') }}
                                                                            </b-button>
                                                                            <b-button
                                                                                class="btn btn-info btn-bold px-4 float-right mt-3 mx-2 mt-lg-0"
                                                                                variant="success" disabled v-else>
                                                                                <b-spinner small></b-spinner>
                                                                                <span class="sr-only">{{ $t("login.Loading")
                                                                                }}...</span>
                                                                            </b-button>
                                                                        </template>
                                                                        <b-button v-else variant="secondary"
                                                                            class="btn btn-secondary btn-bold px-4 float-right mt-3 mx-2 mt-lg-0">
                                                                            {{ $t('general.Break') }}
                                                                        </b-button>
                                                                        <a @click.prevent="addNewField" href=""
                                                                            class="btn btn-info btn-bold px-4 float-right mt-3 mx-2 mt-lg-0">{{
                                                                                $t("general.Add") }}</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </b-tab>
                                    <b-tab :disabled="!reservation_id" :title="$t('general.ImageUploads')">
                                        <div class="row">
                                            <input accept="image/png, image/gif, image/jpeg, image/jpg" type="file"
                                                id="uploadImageCreate" @change.prevent="onImageChanged"
                                                class="input-file-upload position-absolute" :class="[
                                                    'd-none',
                                                    {
                                                        'is-invalid': $v.edit.media.$error || errors.media,
                                                        'is-valid':
                                                            !$v.edit.media.$invalid && !errors.media,
                                                    },
                                                ]" />
                                            <div class="col-md-8 my-1">
                                                <!-- file upload -->
                                                <div class="row align-content-between" style="width: 100%; height: 100%">
                                                    <div class="col-12">
                                                        <div class="d-flex flex-wrap">
                                                            <div class="dropzone-previews col-4 position-relative mb-2"
                                                                v-for="(photo, index) in images">
                                                                <div :class="[
                                                                    'card mb-0 shadow-none border',
                                                                    images.length - 1 == index
                                                                        ? 'bg-primary'
                                                                        : '',
                                                                ]">
                                                                    <div class="p-2">
                                                                        <div class="row align-items-center">
                                                                            <div class="col-auto"
                                                                                @click="showPhoto = photo.webp">
                                                                                <img data-dz-thumbnail :src="photo.webp"
                                                                                    class="avatar-sm rounded bg-light"
                                                                                    @error="src = './images/img-1.png'" />
                                                                            </div>
                                                                            <div class="col pl-0">
                                                                                <a href="javascript:void(0);" :class="[
                                                                                    'font-weight-bold',
                                                                                    images.length - 1 == index
                                                                                        ? 'text-white'
                                                                                        : 'text-muted',
                                                                                ]" data-dz-name>
                                                                                    {{ photo.name }}
                                                                                </a>
                                                                            </div>
                                                                            <!-- Button -->
                                                                            <a href="javascript:void(0);" :class="[
                                                                                'btn-danger text-muted dropzone-close',
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
                                                        <b-button @click="changePhotoEdit" variant="success" type="button"
                                                            class="mx-1 font-weight-bold px-3" v-if="!isLoader">
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
                                                    <img :src="showPhoto" class="img-thumbnail"
                                                        @error="src = './images/img-1.png'" />
                                                </div>
                                            </div>
                                        </div>
                                    </b-tab>
                                </b-tabs>
                            </form>
                        </b-modal>
                        <!--  /create   -->

                        <!-- start .table-responsive-->
                        <div class="table-responsive mb-3 custom-table-theme position-relative">
                            <!--       start loader       -->
                            <loader size="large" v-if="isLoader" />
                            <!--       end loader       -->

                            <table class="table table-borderless table-hover table-centered m-0" ref="exportable_table"
                                id="printReservation">
                                <thead>
                                    <tr>
                                        <th scope="col" style="width: 0" v-if="enabled3" class="do-not-print">
                                            <div class="form-check custom-control">
                                                <input class="form-check-input" type="checkbox" v-model="isCheckAll"
                                                    style="width: 17px; height: 17px" />
                                            </div>
                                        </th>
                                        <th v-if="setting.date">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey("reservation_date") }}</span>
                                                <div class="arrow-sort">
                                                    <i class="fas fa-arrow-up" @click="
                                                        reservations.sort(
                                                            sortString(
                                                                $i18n.locale == 'ar' ? 'field_title' : 'field_title_e'
                                                            )
                                                        )
                                                    "></i>
                                                    <i class="fas fa-arrow-down" @click="
                                                        reservations.sort(
                                                            sortString($i18n.locale == 'ar' ? '-name' : '-name_e')
                                                        )
                                                    "></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th v-if="setting.customer_id">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey("customer") }}</span>
                                                <div class="arrow-sort">
                                                    <i class="fas fa-arrow-up" @click="
                                                        reservations.sort(
                                                            sortString($i18n.locale == 'ar' ? 'name' : 'name_e')
                                                        )
                                                    "></i>
                                                    <i class="fas fa-arrow-down" @click="
                                                        reservations.sort(
                                                            sortString($i18n.locale == 'ar' ? '-name' : '-name_e')
                                                        )
                                                    "></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th v-if="setting.salesman_id">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey("sale_man") }}</span>
                                                <div class="arrow-sort">
                                                    <i class="fas fa-arrow-up" @click="
                                                        reservations.sort(
                                                            sortString($i18n.locale == 'ar' ? 'name' : 'name_e')
                                                        )
                                                    "></i>
                                                    <i class="fas fa-arrow-down" @click="
                                                        reservations.sort(
                                                            sortString($i18n.locale == 'ar' ? '-name' : '-name_e')
                                                        )
                                                    "></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th v-if="setting.branch_id">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey("branch") }}</span>
                                                <div class="arrow-sort">
                                                    <i class="fas fa-arrow-up" @click="
                                                        reservations.sort(
                                                            sortString($i18n.locale == 'ar' ? 'name' : 'name_e')
                                                        )
                                                    "></i>
                                                    <i class="fas fa-arrow-down" @click="
                                                        reservations.sort(
                                                            sortString($i18n.locale == 'ar' ? '-name' : '-name_e')
                                                        )
                                                    "></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th v-if="setting.serial_id">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey("reservation_serial") }}</span>
                                                <div class="arrow-sort">
                                                    <i class="fas fa-arrow-up" @click="
                                                        reservations.sort(
                                                            sortString($i18n.locale == 'ar' ? 'name' : 'name_e')
                                                        )
                                                    "></i>
                                                    <i class="fas fa-arrow-down" @click="
                                                        reservations.sort(
                                                            sortString($i18n.locale == 'ar' ? '-name' : '-name_e')
                                                        )
                                                    "></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th v-if="enabled3" class="do-not-print">
                                            {{ $t("general.Action") }}
                                        </th>
                                        <th v-if="enabled3" class="do-not-print"><i class="fas fa-ellipsis-v"></i></th>
                                    </tr>
                                </thead>
                                <tbody v-if="contracts.length > 0">
                                    <tr @click.capture="checkRow(data.id)"
                                        @dblclick.prevent="(isPermission('update contract RealState') || isPermission('update contract RP'))?
                                        $bvModal.show(`modal-edit-${data.id}`):false"
                                        v-for="(data, index) in contracts" :key="data.id" class="body-tr-custom">
                                        <td v-if="enabled3" class="do-not-print">
                                            <div class="form-check custom-control" style="min-height: 1.9em">
                                                <input style="width: 17px; height: 17px" class="form-check-input"
                                                    type="checkbox" :value="data.id" v-model="checkAll" />
                                            </div>
                                        </td>
                                        <td v-if="setting.date">
                                            <h5 class="m-0 font-weight-normal">
                                                {{ data.date }}
                                            </h5>
                                        </td>
                                        <td v-if="setting.customer_id">
                                            <h5 class="m-0 font-weight-normal">
                                                {{
                                                    $i18n.locale == "ar" ? data.customer.name : data.customer.name_e
                                                }}
                                            </h5>
                                        </td>
                                        <td v-if="setting.salesman_id">
                                            <h5 class="m-0 font-weight-normal">
                                                {{
                                                    $i18n.locale == "ar" ? data.salesman.name : data.salesman.name_e
                                                }}
                                            </h5>
                                        </td>
                                        <td v-if="setting.branch_id">
                                            {{
                                                data.branch ? $i18n.locale == "ar" ? data.branch.name : data.branch.name_e : ''
                                            }}
                                        </td>
                                        <td v-if="setting.serial_id">
                                            {{ data.prefix }}
                                        </td>
                                        <td v-if="enabled3" class="do-not-print">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm dropdown-toggle dropdown-coustom"
                                                    data-toggle="dropdown" aria-expanded="false">
                                                    {{ $t("general.commands") }}
                                                    <i class="fas fa-angle-down"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-custom">
                                                    <a class="dropdown-item" href="#"
                                                       v-if="isPermission('update contract RealState') || isPermission('update contract RP')"
                                                        @click="$bvModal.show(`modal-edit-${data.id}`)">
                                                        <div
                                                            class="d-flex justify-content-between align-items-center text-black">
                                                            <span>{{ $t("general.edit") }}</span>
                                                            <i class="mdi mdi-square-edit-outline text-info"></i>
                                                        </div>
                                                    </a>
                                                    <a class="dropdown-item text-black" href="#"
                                                       v-if="isPermission('delete contract RealState') || isPermission('delete contract RP')"
                                                        @click.prevent="deleteScreenButton(data.id)">
                                                        <div
                                                            class="d-flex justify-content-between align-items-center text-black">
                                                            <span>{{ $t("general.delete") }}</span>
                                                            <i class="fas fa-times text-danger"></i>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                            <!--  edit   -->
                                            <b-modal dialog-class="modal-full-width" :id="`modal-edit-${data.id}`"
                                                :title="getCompanyKey('contract_edit_form')" title-class="font-18"
                                                body-class="p-4" :ref="`edit-${data.id}`" :hide-footer="true"
                                                @show="resetModalEdit(data.id)" @hidden="resetModalHiddenEdit(data.id)">
                                                <form>
                                                    <div class="mb-3 d-flex justify-content-end">

                                                        <b-button variant="danger" type="button"
                                                            @click.prevent="$bvModal.hide(`modal-edit-${data.id}`)">
                                                            {{ $t("general.Cancel") }}
                                                        </b-button>
                                                    </div>
                                                    <b-tabs nav-class="nav-tabs nav-bordered">
                                                        <b-tab :title="$t('general.DataEntry')" active>
                                                            <div class="row">
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label>{{ getCompanyKey("reservation_serial") }}</label>
                                                                        <input
                                                                            v-model="$v.edit.serial_number.$model"
                                                                            class="form-control"
                                                                            type="text"
                                                                            :class="{
                                                                                'is-invalid':
                                                                                    $v.edit.serial_number.$error || errors.serial_number,
                                                                                'is-valid': !$v.edit.serial_number.$invalid && !errors.serial_number,
                                                                            }" />
                                                                        <template v-if="errors.serial_number">
                                                                            <ErrorMessage
                                                                                v-for="(errorMessage, index) in errors.serial_number"
                                                                                :key="index">{{ errorMessage }}
                                                                            </ErrorMessage>
                                                                        </template>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 position-relative">
                                                                    <div class="form-group">
                                                                        <label>{{ getCompanyKey("branch") }}</label>
                                                                        <multiselect @input="showBranchModalEdit"
                                                                            v-model="edit.branch_id"
                                                                            :options="branches.map((type) => type.id)"
                                                                            :custom-label="
                                                                                (opt) =>
                                                                                    $i18n.locale == 'ar'
                                                                                        ? branches.find((x) => x.id == opt).name
                                                                                        : branches.find((x) => x.id == opt).name_e
                                                                            " :class="{
    'is-invalid':
        $v.edit.branch_id.$error || errors.branch_id,
}">
                                                                        </multiselect>
                                                                        <div v-if="!$v.edit.branch_id.required"
                                                                            class="invalid-feedback">
                                                                            {{ $t("general.fieldIsRequired") }}
                                                                        </div>

                                                                        <template v-if="errors.branch_id">
                                                                            <ErrorMessage
                                                                                v-for="(errorMessage, index) in errors.branch_id"
                                                                                :key="index">{{ errorMessage }}
                                                                            </ErrorMessage>
                                                                        </template>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 position-relative">
                                                                    <div class="form-group">
                                                                        <label class="control-label">
                                                                            {{ getCompanyKey("reservation_date") }}
                                                                            <span class="text-danger">*</span>
                                                                        </label>
                                                                        <date-picker type="date" v-model="date" defaultValue
                                                                            @change="v_dateEdit" confirm></date-picker>
                                                                        <template v-if="errors.date">
                                                                            <ErrorMessage
                                                                                v-for="(errorMessage, index) in errors.date"
                                                                                :key="index">{{
                                                                                    errorMessage }}
                                                                            </ErrorMessage>
                                                                        </template>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 position-relative">
                                                                    <div class="form-group">
                                                                        <label>{{ getCompanyKey("customer") }}</label>
                                                                        <multiselect @input="showCustomerModalEdit"
                                                                            v-model="edit.customer_id"
                                                                            :options="customers.map((type) => type.id)"
                                                                            :custom-label="
                                                                                (opt) =>
                                                                                    $i18n.locale == 'ar'
                                                                                        ? customers.find((x) => x.id == opt).name
                                                                                        : customers.find((x) => x.id == opt).name_e
                                                                            " :class="{
    'is-invalid':
        $v.edit.customer_id.$error || errors.customer_id,
}">
                                                                        </multiselect>
                                                                        <div v-if="!$v.edit.customer_id.required"
                                                                            class="invalid-feedback">
                                                                            {{ $t("general.fieldIsRequired") }}
                                                                        </div>

                                                                        <template v-if="errors.customer_id">
                                                                            <ErrorMessage
                                                                                v-for="(errorMessage, index) in errors.customer_id"
                                                                                :key="index">{{ errorMessage }}
                                                                            </ErrorMessage>
                                                                        </template>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 position-relative">
                                                                    <div class="form-group">
                                                                        <label>{{ getCompanyKey("sale_man") }}</label>
                                                                        <multiselect @input="showSaleManModalEdit"
                                                                            v-model="edit.salesman_id"
                                                                            :options="salesmen.map((type) => type.id)"
                                                                            :custom-label="
                                                                                (opt) =>
                                                                                    $i18n.locale == 'ar'
                                                                                        ? salesmen.find((x) => x.id == opt).name
                                                                                        : salesmen.find((x) => x.id == opt).name_e
                                                                            " :class="{
    'is-invalid':
        $v.edit.salesman_id.$error || errors.salesman_id,
}">
                                                                        </multiselect>
                                                                        <div v-if="!$v.edit.salesman_id.required"
                                                                            class="invalid-feedback">
                                                                            {{ $t("general.fieldIsRequired") }}
                                                                        </div>
                                                                        <template v-if="errors.salesman_id">
                                                                            <ErrorMessage
                                                                                v-for="(errorMessage, index) in errors.button_id"
                                                                                :key="index">{{ errorMessage }}
                                                                            </ErrorMessage>
                                                                        </template>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label class="control-label">
                                                                            {{ getCompanyKey("reservation_start_date") }}
                                                                            <span class="text-danger">*</span>
                                                                        </label>
                                                                        <date-picker type="date" v-model="start_date"
                                                                            defaultValue @change="v_startEdit"
                                                                            confirm></date-picker>
                                                                        <div v-if="!$v.edit.start_date.beforeEndDate"
                                                                            class="invalid-feedback">
                                                                            {{ $t("general.startShoudBeforeEndDate") }}
                                                                        </div>
                                                                        <template v-if="errors.start_date">
                                                                            <ErrorMessage
                                                                                v-for="(errorMessage, index) in errors.start_date"
                                                                                :key="index">{{
                                                                                    errorMessage }}
                                                                            </ErrorMessage>
                                                                        </template>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label class="control-label">
                                                                            {{ getCompanyKey("reservation_end_date") }}
                                                                            <span class="text-danger">*</span>
                                                                        </label>
                                                                        <date-picker type="date" v-model="end_date"
                                                                            defaultValue @change="v_endEdit"
                                                                            confirm></date-picker>
                                                                        <div v-if="!$v.edit.end_date.beforeEndDate"
                                                                            class="invalid-feedback">
                                                                            {{ $t("general.startShoudBeforeEndDate") }}
                                                                        </div>
                                                                        <template v-if="errors.end_date">
                                                                            <ErrorMessage
                                                                                v-for="(errorMessage, index) in errors.end_date"
                                                                                :key="index">
                                                                                {{
                                                                                    errorMessage }}
                                                                            </ErrorMessage>
                                                                        </template>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div class="page-content">
                                                                        <div class="px-0">
                                                                            <div class="row mt-4">
                                                                                <div class="col-12 col-lg-12">
                                                                                    <div class="row">
                                                                                        <div class="col-12">
                                                                                            <div
                                                                                                class="text-center text-150">
                                                                                                <i style="font-size:20px"
                                                                                                    class="fa fa-book fa-2x text-success-m2 mr-1"></i>
                                                                                                <span
                                                                                                    class="text-default-d3">{{
                                                                                                        $t("general.contract")
                                                                                                    }}</span>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <!-- .row -->
                                                                                    <hr
                                                                                        class="row brc-default-l1 mx-n1 mb-4" />
                                                                                    <div class="mt-4">
                                                                                        <div
                                                                                            class="row text-600 text-white bgc-default-tp1 py-25">
                                                                                            <div class="col-1">#</div>
                                                                                            <div class="col-2">{{
                                                                                                getCompanyKey("building") }}
                                                                                            </div>
                                                                                            <div class="col-2">{{
                                                                                                getCompanyKey("unit") }}
                                                                                            </div>
                                                                                            <div class="col-2">{{
                                                                                                getCompanyKey("reservation_installment_plan")
                                                                                            }}
                                                                                            </div>
                                                                                            <div class="col-2"> {{
                                                                                                getCompanyKey("unit_value")
                                                                                            }}</div>
                                                                                            <div class="col-2"> {{
                                                                                                getCompanyKey("reservation_value")
                                                                                            }}</div>
                                                                                            <div class="col-1">{{
                                                                                                $t("general.Action") }}
                                                                                            </div>
                                                                                        </div>
                                                                                        <div v-for="(item, index) in edit.details"
                                                                                            class="text-95  text-secondary-d3">
                                                                                            <Unit :companyKeys="companyKeys"
                                                                                                :defaultsKeys="defaultsKeys"
                                                                                                :id="`edit-unit-${index}`"
                                                                                                @created="getUnitsEdit(edit.details[index].building_id, index)" />

                                                                                            <div class="row mb-2 mb-sm-0 py-25"
                                                                                                :class="index % 2 == 0 ? 'bgc-default-l4' : ''">
                                                                                                <div class="col-1">
                                                                                                    {{ index + 1 }}
                                                                                                </div>

                                                                                                <div class="col-2">
                                                                                                    <multiselect
                                                                                                        :id="`edit-${index}-1`"
                                                                                                        @input="getUnitsEdit(edit.details[index].building_id, index,true)"
                                                                                                        v-model="$v.edit.details.$each[index].building_id.$model"
                                                                                                        :options="buildings.map((type) => type.id)"
                                                                                                        :custom-label="
                                                                                                            (opt) =>
                                                                                                                $i18n.locale == 'ar'
                                                                                                                    ? buildings.find((x) => x.id == opt).name
                                                                                                                    : buildings.find((x) => x.id == opt).name_e
                                                                                                        " :class="{
    'is-invalid':
        $v.edit.details.$each[index].building_id.$error || errors.building_id,
}">
                                                                                                    </multiselect>
                                                                                                    <div v-if="!$v.edit.details.$each[index].building_id.required"
                                                                                                        class="invalid-feedback">
                                                                                                        {{
                                                                                                            $t("general.fieldIsRequired")
                                                                                                        }}
                                                                                                    </div>
                                                                                                    <template
                                                                                                        v-if="errors.building_id">
                                                                                                        <ErrorMessage
                                                                                                            v-for="(errorMessage, index) in errors.building_id"
                                                                                                            :key="index">{{
                                                                                                                errorMessage
                                                                                                            }}
                                                                                                        </ErrorMessage>
                                                                                                    </template>
                                                                                                </div>
                                                                                                <div class="col-2">
                                                                                                    <multiselect
                                                                                                        :id="`edit-${index}-2`"
                                                                                                        @input="showUnitModalEdit(index)"
                                                                                                        v-model="$v.edit.details.$each[index].unit_id.$model"
                                                                                                        :options="multUnitsEdit[index].units.map((type) => type.id)"
                                                                                                        :custom-label="
                                                                                                            (opt) =>
                                                                                                                $i18n.locale == 'ar'
                                                                                                                    ? multUnitsEdit[index].units.find((x) => x.id == opt).name
                                                                                                                    : multUnitsEdit[index].units.find((x) => x.id == opt).name_e
                                                                                                        " :class="{
    'is-invalid':
        $v.edit.details.$each[index].unit_id.$error || errors.unit_id,
}">
                                                                                                    </multiselect>
                                                                                                    <div v-if="!$v.edit.details.$each[index].unit_id.required"
                                                                                                        class="invalid-feedback">
                                                                                                        {{
                                                                                                            $t("general.fieldIsRequired")
                                                                                                        }}
                                                                                                    </div>
                                                                                                    <template
                                                                                                        v-if="errors.unit_id">
                                                                                                        <ErrorMessage
                                                                                                            v-for="(errorMessage, index) in errors.unit_id"
                                                                                                            :key="index">
                                                                                                            {{ errorMessage
                                                                                                            }}
                                                                                                        </ErrorMessage>
                                                                                                    </template>
                                                                                                </div>
                                                                                                <div class="col-2">
                                                                                                    <multiselect
                                                                                                        :id="`edit-${index}-3`"
                                                                                                        @input="showInstallmentPlanModalEdit(index)"
                                                                                                        v-model="$v.edit.details.$each[index].installment_payment_plans_id.$model"
                                                                                                        :options="installment_plans.map((type) => type.id)"
                                                                                                        :custom-label="
                                                                                                            (opt) =>
                                                                                                                $i18n.locale == 'ar'
                                                                                                                    ? installment_plans.find((x) => x.id == opt).name
                                                                                                                    : installment_plans.find((x) => x.id == opt).name_e
                                                                                                        " :class="{
    'is-invalid':
        $v.edit.details.$each[index].installment_payment_plans_id.$error || errors.installment_payment_plans_id,
}">
                                                                                                    </multiselect>
                                                                                                    <div v-if="!$v.edit.details.$each[index].installment_payment_plans_id.required"
                                                                                                        class="invalid-feedback">
                                                                                                        {{
                                                                                                            $t("general.fieldIsRequired")
                                                                                                        }}
                                                                                                    </div>
                                                                                                    <template
                                                                                                        v-if="errors.installment_payment_plans_id">
                                                                                                        <ErrorMessage
                                                                                                            v-for="(errorMessage, index) in errors.installment_payment_plans_id"
                                                                                                            :key="index">
                                                                                                            {{ errorMessage
                                                                                                            }}
                                                                                                        </ErrorMessage>
                                                                                                    </template>
                                                                                                </div>
                                                                                                <div class="col-2">
                                                                                                    <input
                                                                                                        :id="`edit-${index}-4`"
                                                                                                        @keyup.enter="moveEnter('edit', index, 5)"
                                                                                                        v-model.number="$v.edit.details.$each[index].unit_value.$model"
                                                                                                        @input="changeValueEdit(index)"
                                                                                                        class="form-control"
                                                                                                        step=".01"
                                                                                                        type="number"
                                                                                                        :class="{
                                                                                                            'is-invalid':
                                                                                                                $v.edit.details.$each[index].unit_value.$error || errors.unit_value || !vaildUnitReservationEdit[index].vaildNumber,
                                                                                                            'is-valid': !$v.edit.details.$each[index].unit_value.$invalid && !errors.unit_value && vaildUnitReservationEdit[index].vaildNumber,
                                                                                                        }" />
                                                                                                    <div v-if="!$v.edit.details.$each[index].unit_value.required"
                                                                                                        class="invalid-feedback">
                                                                                                        {{
                                                                                                            $t("general.fieldIsRequired")
                                                                                                        }}
                                                                                                    </div>
                                                                                                    <div v-if="!vaildUnitReservationEdit[index].vaildNumber"
                                                                                                        class="invalid-feedback">
                                                                                                        {{
                                                                                                            $t("general.reservationLessThanUnitVal")
                                                                                                        }}
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="col-2">
                                                                                                    <input
                                                                                                        :id="`edit-${index}-5`"
                                                                                                        @keyup.enter="moveEnter('edit', index, 6)"
                                                                                                        @input="changeValueEdit(index)"
                                                                                                        step=".01"
                                                                                                        v-model.number="$v.edit.details.$each[index].reservation_value.$model"
                                                                                                        class="form-control"
                                                                                                        type="number"
                                                                                                        :class="{
                                                                                                            'is-invalid':
                                                                                                                $v.edit.details.$each[index].reservation_value.$error || errors.reservation_value || !vaildUnitReservationEdit[index].vaildNumber,
                                                                                                            'is-valid': !$v.edit.details.$each[index].reservation_value.$invalid && !errors.reservation_value && vaildUnitReservationEdit[index].vaildNumber,
                                                                                                        }" />
                                                                                                    <div v-if="!$v.edit.details.$each[index].reservation_value.required"
                                                                                                        class="invalid-feedback">
                                                                                                        {{
                                                                                                            $t("general.fieldIsRequired")
                                                                                                        }}
                                                                                                    </div>
                                                                                                    <div v-if="!vaildUnitReservationEdit[index].vaildNumber"
                                                                                                        class="invalid-feedback">
                                                                                                        {{
                                                                                                            $t("general.reservationLessThanUnitVal")
                                                                                                        }}
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="col-1">
                                                                                                    <button
                                                                                                        v-if="edit.details.length > 1"
                                                                                                        type="button"
                                                                                                        @click.prevent="removeNewFieldEdit(index)"
                                                                                                        class="custom-btn-dowonload">
                                                                                                        <i
                                                                                                            class="fas fa-trash-alt"></i>
                                                                                                    </button>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div
                                                                                            class="row border-b-2 brc-default-l2">
                                                                                        </div>
                                                                                        <div class="row mt-3">
                                                                                            <div
                                                                                                class="col-12 col-sm-7 text-grey-d2 text-95 mt-2 mt-lg-0">
                                                                                                {{ $t("general.Extra_note")
                                                                                                }}
                                                                                            </div>

                                                                                            <div
                                                                                                class="col-12 col-sm-5 text-grey text-90 order-first order-sm-last">
                                                                                                <div
                                                                                                    class="row my-2 align-items-center bgc-primary-l3 p-2">
                                                                                                    <div
                                                                                                        class="col-7 text-right">
                                                                                                        {{
                                                                                                            $t("general.Total_Amount")
                                                                                                        }}
                                                                                                    </div>
                                                                                                    <div class="col-5">
                                                                                                        <span
                                                                                                            class="text-150 text-success-d3 opacity-2">
                                                                                                            {{ total ? total
                                                                                                                : '0.00' }}
                                                                                                        </span>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>

                                                                                        </div>
                                                                                        <hr />
                                                                                        <div>
                                                                                            <span
                                                                                                class="text-secondary-d1 text-105">{{
                                                                                                    $t("general.Thank_you")
                                                                                                }}</span>
                                                                                            <template
                                                                                                v-if="total && edit.details.length > 0 && reservation_id">
                                                                                                <b-button v-if="!isLoader"
                                                                                                    variant="primary"
                                                                                                    class="btn btn-info btn-bold px-4 float-right mt-3 mx-2 mt-lg-0"
                                                                                                    @click.prevent="editSubmit">
                                                                                                    {{ $t('general.Break')
                                                                                                    }}
                                                                                                </b-button>
                                                                                                <b-button variant="success"
                                                                                                    class="btn btn-info btn-bold px-4 float-right mt-3 mx-2 mt-lg-0"
                                                                                                    disabled v-else>
                                                                                                    <b-spinner
                                                                                                        small></b-spinner>
                                                                                                    <span class="sr-only">{{
                                                                                                        $t("login.Loading")
                                                                                                    }}...</span>
                                                                                                </b-button>
                                                                                            </template>
                                                                                            <b-button v-else
                                                                                                variant="secondary"
                                                                                                class="btn-secondary btn-bold px-4 float-right mt-3 mx-2 mt-lg-0">
                                                                                                {{ $t('general.Break') }}
                                                                                            </b-button>

                                                                                            <a @click.prevent="addNewFieldEdit"
                                                                                                href=""
                                                                                                class="btn btn-info btn-bold px-4 float-right mt-3 mx-2 mt-lg-0">{{
                                                                                                    $t("general.Add") }}</a>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </b-tab>
                                                        <b-tab :title="$t('general.ImageUploads')">
                                                            <div class="row">
                                                                <input accept="image/png, image/gif, image/jpeg, image/jpg"
                                                                    type="file" id="uploadImageEdit"
                                                                    @change.prevent="onImageChanged"
                                                                    class="input-file-upload position-absolute" :class="[
                                                                        'd-none',
                                                                        {
                                                                            'is-invalid': $v.edit.media.$error || errors.media,
                                                                            'is-valid':
                                                                                !$v.edit.media.$invalid && !errors.media,
                                                                        },
                                                                    ]" />
                                                                <div class="col-md-8 my-1">
                                                                    <!-- file upload -->
                                                                    <div class="row align-content-between"
                                                                        style="width: 100%; height: 100%">
                                                                        <div class="col-12">
                                                                            <div class="d-flex flex-wrap">
                                                                                <div class="dropzone-previews col-4 position-relative mb-2"
                                                                                    v-for="(photo, index) in images">
                                                                                    <div :class="[
                                                                                        'card mb-0 shadow-none border',
                                                                                        images.length - 1 == index
                                                                                            ? 'bg-primary'
                                                                                            : '',
                                                                                    ]">
                                                                                        <div class="p-2">
                                                                                            <div
                                                                                                class="row align-items-center">
                                                                                                <div class="col-auto"
                                                                                                    @click="showPhoto = photo.webp">
                                                                                                    <img data-dz-thumbnail
                                                                                                        :src="photo.webp"
                                                                                                        class="avatar-sm rounded bg-light"
                                                                                                        @error="src = './images/img-1.png'" />
                                                                                                </div>
                                                                                                <div class="col pl-0">
                                                                                                    <a href="javascript:void(0);"
                                                                                                        :class="[
                                                                                                            'font-weight-bold',
                                                                                                            images.length - 1 == index
                                                                                                                ? 'text-white'
                                                                                                                : 'text-muted',
                                                                                                        ]"
                                                                                                        data-dz-name>
                                                                                                        {{ photo.name }}
                                                                                                    </a>
                                                                                                </div>
                                                                                                <!-- Button -->
                                                                                                <a href="javascript:void(0);"
                                                                                                    :class="[
                                                                                                        'btn-danger text-muted dropzone-close',
                                                                                                        $i18n.locale == 'ar'
                                                                                                            ? 'dropzone-close-rtl'
                                                                                                            : '',
                                                                                                    ]" data-dz-remove
                                                                                                    @click.prevent="
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
                                                                            <b-button @click="changePhotoEdit"
                                                                                variant="success" type="button"
                                                                                class="mx-1 font-weight-bold px-3"
                                                                                v-if="!isLoader">
                                                                                {{ $t("general.Add") }}
                                                                            </b-button>
                                                                            <b-button variant="success" class="mx-1"
                                                                                disabled v-else>
                                                                                <b-spinner small></b-spinner>
                                                                                <span class="sr-only">{{ $t("login.Loading")
                                                                                }}...</span>
                                                                            </b-button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="show-dropzone">
                                                                        <img :src="showPhoto" class="img-thumbnail"
                                                                            @error="src = './images/img-1.png'" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </b-tab>
                                                    </b-tabs>
                                                </form>
                                            </b-modal>
                                            <!--  /edit   -->
                                        </td>
                                        <td v-if="enabled3" class="do-not-print">
                                            <button @mousemove="log(data.id)" @mouseover="log(data.id)" type="button"
                                                class="btn" :id="`tooltip-${data.id}`"
                                                :data-placement="$i18n.locale == 'en' ? 'left' : 'right'" :title="Tooltip">
                                                <i class="fe-info" style="font-size: 22px"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <th class="text-center" colspan="7">
                                            {{ $t("general.notDataFound") }}
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- end .table-responsive-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import adminApi from "../../api/adminAxios";
import { minValue, required } from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../widgets/errorMessage";
import loader from "../general/loader";
import { dynamicSortString } from "../../helper/tableSort";
import Multiselect from "vue-multiselect";
import { formatDateOnly } from "../../helper/startDate";
import translation from "../../helper/mixin/translation-mixin";
import Saleman from "./general/saleman.vue";
import customerGeneral from "./general/customerGeneral";
import Branch from "./general/branch";
import InstallmentPlan from "./receivablePayment/installmentPlan.vue";
import Building from "./realEstate/building";
import Unit from "./realEstate/unit";
import DatePicker from "vue2-datepicker";
import TransactionBreak from "./receivablePayment/transactionBreak/transactionBreak";

export default {
    name: "contractCom",
    mixins: [translation],
    components: {
        Unit,
        Building,
        InstallmentPlan,
        Branch,
        Saleman,
        ErrorMessage,
        loader,
        DatePicker,
        Multiselect,
        customerGeneral,
        TransactionBreak
    },
    data() {
        return {
            per_page: 50,
            search: "",
            debounce: {},
            contractsPagination: {},
            contracts: [],
            customers: [],
            salesmen: [],
            total: 0,
            paymentPlans: [],
            serial: null,
            enabled3: true,
            isLoader: false,
            reservation_id: null,
            create: {
                media: [],
                branch_id: null,
                date: this.formatDate(new Date()),
                start_date: this.formatDate(new Date()),
                end_date: this.formatDate(new Date()),
                salesman_id: null,
                customer_id: null,
                details: [{
                    building_id: null,
                    unit_id: null,
                    plan_installments_id: null,
                    unit_value: 0,
                    reservation_value: 0
                }]
            },
            edit: {
                old_media: [],
                media: [],
                branch_id: null,
                date: this.formatDate(new Date()),
                start_date: this.formatDate(new Date()),
                end_date: this.formatDate(new Date()),
                salesman_id: null,
                customer_id: null,
                details: [],
                serial_number: ''
            },
            openingBreak: [],
            images: [],
            media: {},
            saveImageName: [],
            showPhoto: "./images/img-1.png",
            vaildUnitReservation: [{ vaildNumber: true }],
            setting: {
                date: true,
                customer_id: true,
                salesman_id: true,
                branch_id: true,
                serial_id: true,
            },
            filterSetting: [
                this.$i18n.locale == 'ar' ? 'salesman.name' : 'salesman.name_e',
                this.$i18n.locale == 'ar' ? 'customer.name' : 'customer.name_e',
                this.$i18n.locale == 'ar' ? 'branch.name' : 'branch.name_e',
            ],
            errors: {},
            branches: [],
            buildings: [],
            date: new Date(),
            start_date: new Date(),
            end_date: new Date(),
            installment_plans: [],
            units: [],
            isCheckAll: false,
            checkAll: [],
            is_disabled: false,
            current_page: 1,
            company_id: 48,
            Tooltip: "",
            mouseEnter: null,
            printLoading: true,
            printObj: {
                id: "printReservation",
            },
            vaildUnitReservationEdit: [],
            multUnits: [{ units: [] }],
            multUnitsEdit: [],
        };
    },
    validations: {
        create: {
            media: {},
            date: { required },
            customer_id: { required },
            branch_id: { required },
            start_date: {
                required, beforeEndDate: function (value) {
                    return new Date(this.create.start_date) < new Date(this.create.end_date);
                }
            },
            end_date: {
                required, beforeEndDate: function (value) {
                    return new Date(this.create.start_date) < new Date(this.create.end_date);
                }
            },
            salesman_id: { required },
            details: {
                required,
                $each: {
                    building_id: { required },
                    unit_id: { required },
                    unit_value: { required, minValue: minValue(0) },
                    installment_payment_plans_id: { required },
                    reservation_value: { required, minValue: minValue(0) },
                }
            }
        },
        edit: {
            media: {},
            date: { required },
            customer_id: { required },
            branch_id: { required },
            start_date: {
                required, beforeEndDate: function (value) {
                    return new Date(this.edit.start_date) < new Date(this.edit.end_date);
                }
            },
            end_date: {
                required, beforeEndDate: function (value) {
                    return new Date(this.edit.start_date) < new Date(this.edit.end_date);
                }
            },
            salesman_id: { required },
            details: {
                required,
                $each: {
                    building_id: { required },
                    unit_id: { required },
                    unit_value: {
                        required, minValue: minValue(0)
                    },
                    installment_payment_plans_id: { required },
                    reservation_value: {
                        required, minValue: minValue(0)
                    },
                }
            },
            serial_number: {
                required
            }
        },
    },
    watch: {
        /**
         * watch per_page
         */
        per_page(after, befour) {
            this.getData();
        },
        /**
         * watch search
         */
        search(after, befour) {
            clearTimeout(this.debounce);
            this.debounce = setTimeout(() => {
                this.getData();
            }, 400);
        },
        /**
         * watch check All table
         */
        isCheckAll(after, befour) {
            if (after) {
                this.contracts.forEach((el) => {
                    if (!this.checkAll.includes(el.id)) {
                        this.checkAll.push(el.id);
                    }
                });
            } else {
                this.checkAll = [];
            }
        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
        this.getData();
    },
    methods: {
        isPermission(item) {
            if (this.$store.state.auth.type == 'user'){
                return this.$store.state.auth.permissions.includes(item)
            }
            return true;
        },
        moveEnter(action, index, nextNumberInput) {
            if (nextNumberInput == 6 && action == "create") {
                if (this.create.details.length == (index + 1)) {
                    this.addNewField();
                }
                setTimeout(() => {
                    document.getElementById(`${action}-${index + 1}-1`).focus();
                }, 500)
            }
            else if (nextNumberInput == 6 && action == "edit") {
                if (this.edit.details.length == (index + 1)) {
                    this.addNewFieldEdit();
                }
                setTimeout(() => {
                    document.getElementById(`${action}-${index + 1}-1`).focus();
                }, 500)
            }
            else if (nextNumberInput < 6) {
                document.getElementById(`${action}-${index}-${nextNumberInput}`).focus();
            }
        },
        showBreakCreate() {
            if (this.reservation_id) {
                this.openingBreak = {
                    customer_id: this.create.customer_id,
                    currency_id: 1,
                    document_id: 3,
                    debit: this.total,
                    credit: 0,
                    date: this.create.date,
                    rate: 1,
                    id: this.reservation_id,
                    break_type: 'contract'
                };
                this.$bvModal.show("opening-balance-break-create");
            }
        },
        showBreakEdit() {
            if (this.reservation_id) {
                this.openingBreak = {
                    customer_id: this.edit.customer_id,
                    currency_id: 1,
                    document_id: 3,
                    debit: this.total,
                    credit: 0,
                    data: this.edit.data,
                    rate: 1,
                    id: this.reservation_id,
                    break_type: 'contract'
                };
                this.openingBreak.is_update = true;
                this.$bvModal.show("opening-balance-break-create");
            }
        },
        v_startCreate(e) {
            if (e) {
                this.create.start_date = formatDateOnly(e);
            } else {
                this.create.start_date = null;
            }
        },
        v_startEdit(e) {
            if (e) {
                this.edit.start_date = formatDateOnly(e);
            } else {
                this.edit.start_date = null;
            }
        },
        v_endCreate(e) {
            if (e) {
                this.create.end_date = formatDateOnly(e);
            } else {
                this.create.end_date = null;
            }
        },
        v_endEdit(e) {
            if (e) {
                this.edit.end_date = formatDateOnly(e);
            } else {
                this.edit.end_date = null;
            }
        },
        v_dateCreate(e) {
            if (e) {
                this.create.date = formatDateOnly(e);
            } else {
                this.create.date = null;
            }
        },
        v_dateEdit(e) {
            if (e) {
                this.edit.date = formatDateOnly(e);
            } else {
                this.edit.date = null;
            }
        },
        changeValue(index) {
            this.vaildUnitReservation[index].vaildNumber = this.create.details[index].unit_value >
                this.create.details[index].reservation_value;
            let sum = 0;
            this.create.details.forEach(e => e.unit_value ? sum += parseFloat(e.unit_value) : null);
            this.total = sum;
        },
        changeValueEdit(index) {
            this.vaildUnitReservationEdit[index].vaildNumber = this.edit.details[index].unit_value >
                this.edit.details[index].reservation_value;
            let sum = 0;
            this.edit.details.forEach(e => e.unit_value ? sum += parseFloat(e.unit_value) : null);
            this.total = sum;
        },
        addNewField() {
            this.multUnits.push({ units: [] });
            this.create.details.push({
                building_id: null,
                unit_id: null,
                installment_payment_plans_id: null,
                unit_value: 0,
                reservation_value: 0
            });
            this.vaildUnitReservation.push({ vaildNumber: true });
        },
        removeNewField(index) {
            if (this.create.details.length > 1) {
                this.create.details.splice(index, 1);
                this.vaildUnitReservation.splice(index, 1);
                this.multUnits.splice(index, 1);
            }
        },
        addNewFieldEdit() {
            this.multUnitsEdit.push({ units: [] });
            this.edit.details.push({
                building_id: null,
                unit_id: null,
                installment_payment_plans_id: null,
                unit_value: 0,
                reservation_value: 0
            });
            this.vaildUnitReservationEdit.push({ vaildNumber: true });
        },
        removeNewFieldEdit(index) {
            if (this.edit.details.length > 1) {
                this.edit.details.splice(index, 1);
                this.vaildUnitReservationEdit.splice(index, 1);
                this.multUnitsEdit.splice(index, 1);
            }
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

                            adminApi
                                .put(`real-estate/reservations/${this.reservation_id}`, { old_media, media: new_media })
                                .then((res) => {
                                    this.images = res.data.data.media ?? [];
                                    if (this.images && this.images.length > 0) {
                                        this.showPhoto = this.images[this.images.length - 1].webp;
                                    } else {
                                        this.showPhoto = "./images/img-1.png";
                                    }
                                    this.getData();
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
                                        .put(`real-estate/reservations/${this.reservation_id}`, { old_media, media: new_media })
                                        .then((res) => {
                                            this.images = res.data.data.media ?? [];
                                            if (this.images && this.images.length > 0) {
                                                this.showPhoto = this.images[this.images.length - 1].webp;
                                            } else {
                                                this.showPhoto = "./images/img-1.png";
                                            }
                                            this.getData();
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
                .put(`real-estate/reservations/${this.reservation_id}`, { old_media })
                .then((res) => {
                    this.reservations[index] = res.data.data;
                    this.images = res.data.data.media ?? [];
                    if (this.images && this.images.length > 0) {
                        this.showPhoto = this.images[this.images.length - 1].webp;
                    } else {
                        this.showPhoto = "./images/img-1.png";
                    }
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        showSaleManModal() {
            if (this.create.salesman_id == 0) {
                this.$bvModal.show("saleman-create");
                this.create.salesman_id = null;
            }
        },
        showSaleManModalEdit() {
            if (this.edit.salesman_id == 0) {
                this.$bvModal.show("saleman-create");
                this.edit.salesman_id = null;
            }
        },
        showCustomerModal() {
            if (this.create.customer_id == 0) {
                this.$bvModal.show("customer-general-create");
                this.create.customer_id = null;
            }
        },
        showCustomerModalEdit() {
            if (this.edit.customer_id == 0) {
                this.$bvModal.show("customer-general-create");
                this.edit.customer_id = null;
            }
        },
        showBranchModal() {
            if (this.create.branch_id == 0) {
                this.$bvModal.show("create_branch");
                this.create.branch_id = null;
            } else {
                // this.getBranchSerial(this.create.branch_id);
            }
        },
        showBranchModalEdit() {
            if (this.edit.branch_id == 0) {
                this.$bvModal.show("create_branch");
                this.edit.branch_id = null;
            } else {
                // this.getBranchSerial(this.edit.branch_id);
            }
        },
        showUnitModal(index) {
            if (this.create.details[index].unit_id == 0) {
                this.$bvModal.show(`create-unit-${index}`);
                this.create.details[index].unit_id = null;
                return;
            }
            this.moveEnter('create', index, 3);
        },
        showUnitModalEdit(index) {
            if (this.edit.details[index].unit_id == 0) {
                this.$bvModal.show(`edit-unit-${index}`);
                this.edit.details[index].unit_id = null;
                return;
            }
            this.moveEnter('edit', index, 3);
        },
        showInstallmentPlanModal(index) {
            if (this.create.details[index].installment_payment_plans_id == 0) {
                this.$bvModal.show("installment-payment-plan-create");
                this.create.details[index].installment_payment_plans_id = null;
                return;
            }
            this.moveEnter('create', index, 4);
        },
        showInstallmentPlanModalEdit(index) {
            if (this.edit.details[index].installment_payment_plans_id == 0) {
                this.$bvModal.show("installment-payment-plan-create");
                this.edit.details[index].installment_payment_plans_id = null;
                return;
            }
            this.moveEnter('edit', index, 4);

        },

        /**
         *  get Data contracts
         */
        getData(page = 1) {
            this.isLoader = true;
            let _filterSetting = [...this.filterSetting];
            let index = this.filterSetting.indexOf("customer_id");
            if (index > -1) {
                _filterSetting[index] =
                    this.$i18n.locale == "ar" ? "customer.name" : "customer.name_e";
            }
            index = this.filterSetting.indexOf("salesman_id");
            if (index > -1) {
                _filterSetting[index] =
                    this.$i18n.locale == "ar" ? "salesman.name" : "salesman.name_e";
            }
            index = this.filterSetting.indexOf("branch_id");
            if (index > -1) {
                _filterSetting[index] =
                    this.$i18n.locale == "ar" ? "branch.name" : "branch.name_e";
            }
            let filter = "";
            for (let i = 0; i < _filterSetting.length; ++i) {
                filter += `columns[${i}]=${_filterSetting[i]}&`;
            }
            adminApi
                .get(
                    `real-estate/contracts?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
                )
                .then((res) => {
                    let l = res.data;
                    this.contracts = l.data;
                    this.contractsPagination = l.pagination;
                    this.current_page = l.pagination.current_page;
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
        getDataCurrentPage() {
            if (
                this.current_page <= this.contractsPagination.last_page &&
                this.current_page != this.contractsPagination.current_page &&
                this.current_page
            ) {
                this.isLoader = true;
                let _filterSetting = [...this.filterSetting];
                let index = this.filterSetting.indexOf("customer_id");
                if (index > -1) {
                    _filterSetting[index] =
                        this.$i18n.locale == "ar" ? "customer.name" : "customer.name_e";
                }
                index = this.filterSetting.indexOf("salesman_id");
                if (index > -1) {
                    _filterSetting[index] =
                        this.$i18n.locale == "ar" ? "salesman.name" : "salesman.name_e";
                }
                index = this.filterSetting.indexOf("branch_id");
                if (index > -1) {
                    _filterSetting[index] =
                        this.$i18n.locale == "ar" ? "branch.name" : "branch.name_e";
                }
                let filter = "";
                for (let i = 0; i < _filterSetting.length; ++i) {
                    filter += `columns[${i}]=${_filterSetting[i]}&`;
                }

                adminApi
                    .get(
                        `real-estate/contracts?page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&${filter}`
                    )
                    .then((res) => {
                        let l = res.data;
                        this.contracts = l.data;
                        this.contractsPagination = l.pagination;
                        this.current_page = l.pagination.current_page;
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
        },
        /**
         *  delete screen button
         */
        deleteScreenButton(id, index) {
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
                            .post(`real-estate/contracts/bulk-delete`, { ids: id })
                            .then((res) => {
                                this.checkAll = [];
                                this.getData();
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
                                    Swal.fire({
                                        icon: "error",
                                        title: `${this.$t("general.Error")}`,
                                        text: `${this.$t("general.CantDeleteRelation")}`,
                                    });
                                    this.getData();
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
                            .delete(`real-estate/contracts/${id}`)
                            .then((res) => {
                                this.checkAll = [];
                                this.getData();
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
                                    Swal.fire({
                                        icon: "error",
                                        title: `${this.$t("general.Error")}`,
                                        text: `${this.$t("general.CantDeleteRelation")}`,
                                    });
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
        },
        /**
         *  reset Modal (create)
         */
        resetModalHidden() {
            this.create = {
                media: [],
                branch_id: null,
                date: this.formatDate(new Date()),
                start_date: this.formatDate(new Date()),
                end_date: this.formatDate(new Date()),
                salesman_id: null,
                customer_id: null,
                details: [{
                    building_id: null,
                    unit_id: null,
                    plan_installments_id: null,
                    unit_value: 0,
                    reservation_value: 0
                }]
            };
            this.reservation_id = null;
            this.serial = null;
            this.total = 0;
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
            this.images = [];
        },
        /**
         *  hidden Modal (create)
         */
        async resetModal() {
            await this.getCustomers();
            await this.getBranches();
            await this.getBuildings();
            await this.getInstallmentPlans();
            await this.getSalesmen();
            this.date = new Date();
            this.start_date = new Date();
            this.end_date = new Date();
            this.multUnits = [{ units: [] }];
            this.create = {
                media: [],
                branch_id: null,
                date: this.formatDate(new Date()),
                start_date: this.formatDate(new Date()),
                end_date: this.formatDate(new Date()),
                salesman_id: null,
                customer_id: null,
                details: [{
                    building_id: null,
                    unit_id: null,
                    installment_payment_plans_id: null,
                    unit_value: 0,
                    reservation_value: 0
                }]
            };
            this.showPhoto = "./images/img-1.png";
            this.vaildUnitReservation = [{ vaildNumber: true }];
            this.reservation_id = null;

            this.is_disabled = false;
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
            this.media = {};
            this.images = [];
        },
        /**
         *  create screen
         */
        resetForm() {
            this.reservation_id = null;
            this.multUnits = [{ units: [] }];
            this.serial = null;
            this.create = {
                media: [],
                branch_id: null,
                date: this.formatDate(new Date()),
                start_date: this.formatDate(new Date()),
                end_date: this.formatDate(new Date()),
                salesman_id: null,
                customer_id: null,
                details: [{
                    building_id: null,
                    unit_id: null,
                    installment_payment_plans_id: null,
                    unit_value: 0,
                    reservation_value: 0
                }]
            };
            this.date = new Date();
            this.total = 0;
            this.start_date = new Date();
            this.end_date = new Date();
            this.vaildUnitReservation = [{ vaildNumber: true }];
            this.is_disabled = false;
            this.media = {};
            this.images = [];

            this.$nextTick(() => {
                this.$v.$reset();
            });
        },
        AddSubmit() {
            this.vaildUnitReservation.forEach((el, index) => {
                this.vaildUnitReservation[index].vaildNumber = this.create.details[index].unit_value > this.create.details[index].reservation_value;
            });
            let vaild = this.vaildUnitReservation.every(el => el.vaildNumber);

            if (this.$v.create.$invalid || !vaild) {
                this.$v.create.$touch();
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                this.is_disabled = false;
                adminApi
                    .post(`real-estate/contracts`, {
                        ...this.create,
                        document_id: 3,
                    })
                    .then((res) => {
                        this.reservation_id = res.data.data.id;
                        this.showBreakCreate();
                        this.getData();
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
        /**
         *  edit screen
         */
        editSubmit(id) {
            this.vaildUnitReservationEdit.forEach((el, index) => {
                this.vaildUnitReservationEdit[index].vaildNumber = this.edit.details[index].unit_value > this.edit.details[index].reservation_value;
            });
            let vaild = this.vaildUnitReservationEdit.every(el => el.vaildNumber);

            this.$v.edit.$touch();
            this.images.forEach((e) => {
                this.edit.old_media.push(e.id);
            });

            if (this.$v.edit.$invalid || !vaild) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                adminApi
                    .put(`real-estate/contracts/${this.reservation_id}`, {
                        ...this.edit,
                        document_id: 3
                    })
                    .then((res) => {
                        this.showBreakEdit();
                        this.getData();
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
         *  get workflows
         */
        async getCustomers() {
            this.isLoader = true;
            await adminApi
                .get(`/general-customer`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    if(this.isPermission('create Customer')){
                        l.unshift({ id: 0, name: "اضافة زبون", name_e: "Add customer" });
                    }
                    this.customers = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        async getBranches() {
            this.isLoader = true;
            await adminApi
                .get(`/branches?document_id=3`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    if(this.isPermission('create Branch')){
                        l.unshift({ id: 0, name: "اضف فرع", name_e: "Add branch" });
                    }
                    this.branches = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        async getInstallmentPlans() {
            this.isLoader = true;
            await adminApi
                .get(`recievable-payable/rp_installment_p_plan`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    if(this.isPermission('create paymentPlan RP')){
                        l.unshift({ id: 0, name: "اضف خطة تقسيط", name_e: "Add installment plan" });
                    }
                    this.installment_plans = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        async getBuildings() {
            this.isLoader = true;
            await adminApi
                .get(`real-estate/buildings`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    if(this.isPermission('create building RealState')){
                        l.unshift({ id: 0, name: "اضف مبنى", name_e: "Add building" });
                    }
                    this.buildings = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        async getUnits(buildingId = 0, index = null) {
            if (buildingId == 0) {

                this.$bvModal.show("building-create");
                if (this.create.details[index]) {
                    this.create.details[index].building_id = null;
                }
                return;
            }
            this.moveEnter('create', index, 2);
            this.isLoader = true;
            await adminApi
                .get(`real-estate/units?building_id=${this.create.details[index].building_id}`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    if(this.isPermission('create units RealState')){
                        l.unshift({ id: 0, name: "اضف وحدة جديدة", name_e: "Add new unit" });
                    }
                    this.multUnits[index].units = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        async getUnitsEdit(buildingId, index = null,moveEnter=false) {
            if (buildingId == 0) {
                this.$bvModal.show("building-create");
                if (this.edit.details[index]) {
                    this.edit.details[index].building_id = null;
                }
                return;
            }
            if(moveEnter){
                this.moveEnter('edit', index, 2);
            }
            this.isLoader = true;
            await adminApi
                .get(`real-estate/units?building_id=${buildingId}`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    if(this.isPermission('create units RealState')){
                        l.unshift({ id: 0, name: "اضف وحدة جديدة", name_e: "Add new unit" });
                    }
                    this.multUnitsEdit[index].units = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        async getSalesmen() {
            this.isLoader = true;
            await adminApi
                .get(`/salesmen`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    if(this.isPermission('create Sales Man')){
                        l.unshift({ id: 0, name: "اضافة رجل مبيعات", name_e: "Add sale man" });
                    }
                    this.salesmen = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        async getPaymentPlans() {
            this.isLoader = true;
            await adminApi
                .get(`/real-estate/paymentPlans`)
                .then((res) => {
                    this.isLoader = false;
                    this.paymentPlans = res.data.data;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        async getBranchSerial(id) {
            await adminApi
                .get(`/serial/branch?branch_id=${id}`)
                .then((res) => {
                    this.serial = res.data.data ? res.data.data.serial_number : '';
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
         *   show Modal (edit)
         */
        async resetModalEdit(id) {
            let reservation = this.contracts.find((e) => id == e.id);
            this.vaildUnitReservationEdit = [];
            this.date = new Date(reservation.date);
            this.start_date = new Date(reservation.start_date);
            this.end_date = new Date(reservation.end_date);
            this.edit.date = reservation.date;
            this.edit.serial_number = reservation.serial_number;
            this.edit.start_date = reservation.start_date;
            this.edit.end_date = reservation.end_date;
            await this.getCustomers();
            this.edit.customer_id = reservation.customer.id;
            // await this.getPaymentPlans();
            await this.getSalesmen();
            this.edit.salesman_id = reservation.salesman.id;
            await this.getBranches();
            this.edit.branch_id = reservation.branch.id;
            await this.getBuildings();
            await this.getInstallmentPlans();
            this.images = reservation.media ?? [];;
            reservation.details.forEach(async (e, index) => {
                this.multUnitsEdit.push({ units: [] });
                await this.getUnitsEdit(e.building_id, index);
                this.vaildUnitReservationEdit.push({ vaildNumber: true });
                this.edit.details.push({
                    unit_value: e.unit_value,
                    reservation_value: e.reservation_value,
                    building_id: e.building_id,
                    unit_id: e.unit_id,
                    installment_payment_plans_id: e.installment_payment_plans_id,
                    id: e.id
                });
                this.total += parseFloat(e.unit_value);
            });
            this.reservation_id = reservation.id;
            if (this.images && this.images.length > 0) {
                this.showPhoto = this.images[this.images.length - 1].webp;
            } else {
                this.showPhoto = "./images/img-1.png";
            }
            this.errors = {};
        },
        /**
         *  hidden Modal (edit)
         */
        resetModalHiddenEdit(id) {
            this.errors = {};
            this.edit = {
                date: this.formatDate(new Date()),
                start_date: this.formatDate(new Date()),
                end_date: this.formatDate(new Date()),
                plan_installments_id: null,
                salesman_id: null,
                old_media: [],
                customer_id: null,
                details: [],
                serial_number: '',
                branch_id: null
            };
            this.reservation_id = null;
            this.images = [];
            this.is_disabled = false;
            this.total = 0;
        },

        /**
         *  start  dynamicSortString
         */
        sortString(value) {
            return dynamicSortString(value);
        },
        checkRow(id) {
            if (!this.checkAll.includes(id)) {
                this.checkAll.push(id);
            } else {
                let index = this.checkAll.indexOf(id);
                this.checkAll.splice(index, 1);
            }
        },
        formatDate(value) {
            return formatDateOnly(value);
        },
        log(id) {
            if (this.mouseEnter != id) {
                this.Tooltip = "";
                this.mouseEnter = id;
                adminApi
                    .get(`real-estate/contracts/logs/${id}`)
                    .then((res) => {
                        let l = res.data.data;
                        l.forEach((e) => {
                            this.Tooltip += `Created By: ${e.causer_type}; Event: ${e.event
                                }; Description: ${e.description} ;Created At: ${this.formatDate(
                                    e.created_at
                                )} \n`;
                        });
                        $(`#tooltip-${id}`).tooltip();
                    })
                    .catch((err) => {
                        Swal.fire({
                            icon: "error",
                            title: `${this.$t("general.Error")}`,
                            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                        });
                    });
            }
        },
        ExportExcel(type, fn, dl) {
            this.enabled3 = false;
            setTimeout(() => {
                let elt = this.$refs.exportable_table;
                let wb = XLSX.utils.table_to_book(elt, { sheet: "Sheet JS" });
                if (dl) {
                    XLSX.write(wb, { bookType: type, bookSST: true, type: 'base64' });
                } else {
                    XLSX.writeFile(wb, fn || (('Reservation' + '.' || 'SheetJSTableExport.') + (type || 'xlsx')));
                }
                this.enabled3 = true;
            }, 100);
        }
    },
}
</script>

<style scoped>
.page-content {
    width: 100%;
}

.total {
    color: #343a40 !important;
    font-weight: bold;
}

.text-secondary-d1 {
    color: #728299 !important;
}

.page-header {
    margin: 0 0 1rem;
    padding-bottom: 1rem;
    padding-top: .5rem;
    border-bottom: 1px dotted #e2e2e2;
    display: -ms-flexbox;
    display: flex;
    -ms-flex-pack: justify;
    justify-content: space-between;
    -ms-flex-align: center;
    align-items: center;
}

.page-title {
    padding: 0;
    margin: 0;
    font-size: 1.75rem;
    font-weight: 300;
}

.brc-default-l1 {
    border-color: #dce9f0 !important;
}

.ml-n1,
.mx-n1 {
    margin-left: -.25rem !important;
}

.mr-n1,
.mx-n1 {
    margin-right: -.25rem !important;
}

.mb-4,
.my-4 {
    margin-bottom: 1.5rem !important;
}

hr {
    margin-top: 1rem;
    margin-bottom: 1rem;
    border: 0;
    border-top: 1px solid rgba(0, 0, 0, .1);
}

.text-grey-m2 {
    color: #888a8d !important;
}

.text-success-m2 {
    color: #86bd68 !important;
}

.font-bolder,
.text-600 {
    font-weight: 600 !important;
}

.text-110 {
    font-size: 110% !important;
}

.text-blue {
    color: #478fcc !important;
}

.pb-25,
.py-25 {
    padding-bottom: .75rem !important;
}

.pt-25,
.py-25 {
    padding-top: .75rem !important;
}

.bgc-default-tp1 {
    background-color: rgba(121, 169, 197, .92) !important;
}

.bgc-default-l4,
.bgc-h-default-l4:hover {
    background-color: #f3f8fa !important;
}

.page-header .page-tools {
    -ms-flex-item-align: end;
    align-self: flex-end;
}

.btn-light {
    color: #757984;
    background-color: #f5f6f9;
    border-color: #dddfe4;
}

.w-2 {
    width: 1rem;
}

.text-120 {
    font-size: 120% !important;
}

.text-primary-m1 {
    color: #4087d4 !important;
}

.text-danger-m1 {
    color: #dd4949 !important;
}

.text-blue-m2 {
    color: #68a3d5 !important;
}

.text-150 {
    font-size: 150% !important;
}

.text-60 {
    font-size: 60% !important;
}

.text-grey-m1 {
    color: #7b7d81 !important;
}

.align-bottom {
    vertical-align: bottom !important;
}
</style>
