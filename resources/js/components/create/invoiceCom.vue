<template>
    <div>
        <Saleman :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getSalesmen" />
        <Item :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getItems" />
        <customerGeneral :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getCustomers" />
        <Branch :id="'create_branch'" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getBranches" />
        <transactionBreak :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :opening="openingBreak" />
        <div class="invoice-container row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row justify-content-between align-items-center mb-2">
                            <h4 class="header-title">{{ $t("general.InvoiceTable") }}</h4>
                            <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                                <div class="d-inline-block" style="width: 22.2%">
                                    <!-- Basic dropdown -->
                                    <b-dropdown variant="primary" :text="$t('general.searchSetting')" ref="dropdown"
                                                class="btn-block setting-search">
                                        <b-form-checkbox v-model="filterSetting"
                                                         :value="$i18n.locale == 'ar' ? 'salesman.name' : 'salesman.name_e'"
                                                         class="mb-1">{{ getCompanyKey("sale_man") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting"
                                                         :value="$i18n.locale == 'ar' ? 'customer.name' : 'customer.name_e'"
                                                         class="mb-1">{{ getCompanyKey("customer") }}
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
                                <b-button v-b-modal.create v-if="isPermission('create invoice RealState')" variant="primary" class="btn-sm mx-1 font-weight-bold">
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
                                            v-if="checkAll.length == 1 && isPermission('update invoice RealState')">
                                        <i class="mdi mdi-square-edit-outline"></i>
                                    </button>
                                    <!-- start mult delete  -->
                                    <button class="custom-btn-dowonload" v-if="checkAll.length > 1 && isPermission('delete invoice RealState')"
                                            @click.prevent="deleteScreenButton(checkAll)">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <!-- end mult delete  -->
                                    <!--  start one delete  -->
                                    <button class="custom-btn-dowonload" v-if="checkAll.length == 1 && isPermission('delete invoice RealState')"
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
                                                getCompanyKey("invoice_date") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="setting.customer_id" class="mb-1">{{
                                                getCompanyKey("customer") }}
                                        </b-form-checkbox>

                                        <b-form-checkbox v-model="setting.salesman_id" class="mb-1">
                                            {{ getCompanyKey("sale_man") }}
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
                                            {{ invoicesPagination.from }}-{{ invoicesPagination.to }}
                                            /
                                            {{ invoicesPagination.total }}
                                        </div>
                                        <div class="d-inline-block">
                                            <a href="javascript:void(0)" :style="{
                                                'pointer-events':
                                                    invoicesPagination.current_page == 1 ? 'none' : '',
                                            }" @click.prevent="getData(invoicesPagination.current_page - 1)">
                                                <span>&lt;</span>
                                            </a>
                                            <input type="text" @keyup.enter="getDataCurrentPage()" v-model="current_page"
                                                   class="pagination-current-page" />
                                            <a href="javascript:void(0)" :style="{
                                                'pointer-events':
                                                    invoicesPagination.last_page ==
                                                        invoicesPagination.current_page
                                                        ? 'none'
                                                        : '',
                                            }" @click.prevent="getData(invoicesPagination.current_page + 1)">
                                                <span>&gt;</span>
                                            </a>
                                        </div>
                                    </div>
                                    <!-- end Pagination -->
                                </div>
                            </div>
                        </div>

                        <!--  create   -->
                        <b-modal dialog-class="modal-full-width" id="create" :title="getCompanyKey('invoice_create_form')"
                                 title-class="font-18" body-class="p-4 " :hide-footer="true" @show="resetModal"
                                 @hidden="resetModalHidden">
                            <form>
                                <div class="mb-3 d-flex justify-content-end">
                                    <b-button variant="success" :disabled="!is_disabled" @click.prevent="resetForm"
                                              type="button" :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : '']">
                                        {{ $t("general.AddNewRecord") }}
                                    </b-button>
                                    <template v-if="!is_disabled">
                                        <b-button variant="success" type="button" class="mx-1" v-if="!isLoader"
                                                  @click.prevent="AddSubmit">
                                            {{ $t("general.Add") }}
                                        </b-button>

                                        <b-button variant="success" class="mx-1" disabled v-else>
                                            <b-spinner small></b-spinner>
                                            <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                        </b-button>
                                    </template>

                                    <b-button variant="danger" type="button" @click.prevent="$bvModal.hide(`create`)">
                                        {{ $t("general.Cancel") }}
                                    </b-button>
                                </div>
                                <!-- <b-tabs nav-class="nav-tabs nav-bordered"> -->
                                <!-- <b-tab :title="$t('general.DataEntry')" active> -->
                                <div class="row">
                                    <!-- <div class="text-95 col-sm-6 align-self-start d-sm-flex">
                                        <hr class="d-sm-none" />
                                        <div class="text-grey-m2">
                                            <div
                                                class="mt-1 mb-2 text-secondary-m1 text-600 text-125">
                                                {{ $t("general.Invoice") }}
                                            </div>

                                            <div class="my-2"><i
                                                class="fa fa-circle text-blue-m2 text-xs mr-1"></i>
                                                <span class="text-600 text-90">{{
                                                        $t("general.serial") }} : </span> {{
                                                    serial_number }}
                                            </div>
                                        </div>
                                    </div> -->
                                </div>
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
                                                {{ getCompanyKey("invoice_date") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" class="form-control" data-create="9"
                                                   v-model="$v.create.date.$model" :class="{
                                                    'is-invalid': $v.create.date.$error || errors.date,
                                                    'is-valid': !$v.create.date.$invalid && !errors.date,
                                                }" />
                                            <template v-if="errors.date">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.date" :key="index">{{
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
                                                                            $t("general.invoice_details") }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- .row -->
                                                        <hr class="row brc-default-l1 mx-n1 mb-4" />
                                                        <div class="mt-4">
                                                            <div class="row text-600 text-white bgc-default-tp1 py-25">
                                                                <div class="col-1">#</div>
                                                                <div class="col-2">{{ $t("general.code_number") }}</div>
                                                                <div class="col-3">{{ $t("general.Item") }}</div>
                                                                <div class="col-2">{{
                                                                        $t("general.amount") }}</div>
                                                                <div class="col-2">{{
                                                                        $t("general.quantity") }}</div>
                                                                <div class="col-1">{{ $t("general.total") }}</div>
                                                                <div class="col-1">{{ $t("general.Action") }}</div>
                                                            </div>
                                                            <div v-for="(item, index) in create.invoice_details"
                                                                 class="text-95  text-secondary-d3">
                                                                <div class="row mb-2 mb-sm-0 py-25"
                                                                     :class="index % 2 == 0 ? 'bgc-default-l4' : ''">
                                                                    <div class="col-1 colStyle">{{ index + 1 }}</div>
                                                                    <div class="col-2 colStyle">{{ item.code_number }}</div>
                                                                    <div class="col-3">
                                                                        <multiselect :id="`create-${index}-1`"
                                                                                     @input="showItemModal($event, index)"
                                                                                     v-model="$v.create.invoice_details.$each[index].item_id.$model"
                                                                                     :options="items.map((type) => type.id)"
                                                                                     :custom-label="
                                                                                (opt) =>
                                                                                    $i18n.locale == 'ar'
                                                                                        ? items.find((x) => x.id == opt).name
                                                                                        : items.find((x) => x.id == opt).name_e
                                                                            " :class="{
                                                                                    'is-invalid':
                                                                                        $v.create.invoice_details.$each[index].item_id.$error || errors.item_id,
                                                                                }">
                                                                        </multiselect>
                                                                        <div v-if="!$v.create.invoice_details.$each[index].item_id.required"
                                                                             class="invalid-feedback">
                                                                            {{ $t("general.fieldIsRequired") }}
                                                                        </div>
                                                                        <template v-if="errors.item_id">
                                                                            <ErrorMessage
                                                                                v-for="(errorMessage, index) in errors.item_id"
                                                                                :key="index">{{ errorMessage }}
                                                                            </ErrorMessage>
                                                                        </template>
                                                                    </div>
                                                                    <div class="col-2 colStyle">
                                                                        <input @keyup.enter="moveEnter('create', index, 3)"
                                                                               :id="`create-${index}-2`"
                                                                               v-model="$v.create.invoice_details.$each[index].amount.$model"
                                                                               class="form-control" type="number" :class="{
                                                                                'is-invalid':
                                                                                    $v.create.invoice_details.$each[index].amount.$error || errors.amount,
                                                                                'is-valid': !$v.create.invoice_details.$each[index].amount.$invalid && !errors.amount,
                                                                            }" />
                                                                        <div v-if="!$v.create.invoice_details.$each[index].amount.required"
                                                                             class="invalid-feedback">
                                                                            {{ $t("general.fieldIsRequired") }}
                                                                        </div>

                                                                        <template v-if="errors.amount">
                                                                            <ErrorMessage
                                                                                v-for="(errorMessage, index) in errors.amount"
                                                                                :key="index">{{ errorMessage }}
                                                                            </ErrorMessage>
                                                                        </template>
                                                                    </div>
                                                                    <div class="col-2 colStyle">
                                                                        <input @keyup.enter="moveEnter('create', index, 4)"
                                                                               :id="`create-${index}-3`"
                                                                               v-model="$v.create.invoice_details.$each[index].quantity.$model"
                                                                               class="form-control" type="number" :class="{
                                                                                'is-invalid':
                                                                                    $v.create.invoice_details.$each[index].quantity.$error || errors.quantity,
                                                                                'is-valid': !$v.create.invoice_details.$each[index].quantity.$invalid && !errors.quantity,
                                                                            }" />
                                                                        <div v-if="!$v.create.invoice_details.$each[index].quantity.required"
                                                                             class="invalid-feedback">
                                                                            {{ $t("general.fieldIsRequired") }}
                                                                        </div>
                                                                        <template v-if="errors.quantity">
                                                                            <ErrorMessage
                                                                                v-for="(errorMessage, index) in errors.quantity"
                                                                                :key="index">{{ errorMessage }}
                                                                            </ErrorMessage>
                                                                        </template>
                                                                    </div>
                                                                    <div class="col-1">{{ parseFloat(item.quantity * item.amount).toFixed(2) }}
                                                                    </div>
                                                                    <div class="col-1 colStyle">
                                                                        <button v-if="create.invoice_details.length > 1"
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
                                                                                class="text-150 text-success-d3 opacity-2">{{
                                                                                    getTotal(create.invoice_details) }}
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                            <hr />
                                                            <div>
                                                                <span class="text-secondary-d1 text-105"></span>
                                                                <b-button
                                                                    v-if="!invoice_id && create.customer_id && getTotal(create.invoice_details) > 0"
                                                                    variant="primary"
                                                                    class="btn btn-info btn-bold px-4 float-right mt-3 mt-lg-0"
                                                                    @click="saveOpenAndBreak()">
                                                                    {{ $t('general.Break') }}
                                                                </b-button>
                                                                <b-button v-else variant="secondary"
                                                                          class="btn btn-secondary btn-bold px-4 float-right mt-3 mt-lg-0">
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
                                <!-- </b-tab> -->
                                <!-- <b-tab :disabled="!invoice_id" :title="$t('general.ImageUploads')">
                                    <div class="row">
                                        <input accept="image/png, image/gif, image/jpeg, image/jpg" type="file"
                                               id="uploadImageEdit" @change.prevent="onImageChanged"
                                               class="input-file-upload position-absolute" :class="[
                                                    'd-none',
                                                    {
                                                        'is-invalid': $v.edit.media.$error || errors.media,
                                                        'is-valid':
                                                            !$v.edit.media.$invalid && !errors.media,
                                                    },
                                                ]" />
                                        <div class="col-md-8 my-1">
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
                                </b-tab> -->
                                <!-- </b-tabs> -->
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
                                            <span>{{ getCompanyKey("invoice_date") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="
                                                        invoices.sort(
                                                            sortString(
                                                                $i18n.locale == 'ar' ? 'field_title' : 'field_title_e'
                                                            )
                                                        )
                                                    "></i>
                                                <i class="fas fa-arrow-down" @click="
                                                        invoices.sort(
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
                                                        invoices.sort(
                                                            sortString($i18n.locale == 'ar' ? 'name' : 'name_e')
                                                        )
                                                    "></i>
                                                <i class="fas fa-arrow-down" @click="
                                                        invoices.sort(
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
                                                        invoices.sort(
                                                            sortString($i18n.locale == 'ar' ? 'name' : 'name_e')
                                                        )
                                                    "></i>
                                                <i class="fas fa-arrow-down" @click="
                                                        invoices.sort(
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
                                <tbody v-if="invoices.length > 0">
                                <tr @click.capture="checkRow(data.id)"
                                    @dblclick.prevent="$bvModal.show(`modal-edit-${data.id}`)"
                                    v-for="(data, index) in invoices" :key="data.id" class="body-tr-custom">
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
                                                <a class="dropdown-item" href="#" v-if="isPermission('update invoice RealState')"
                                                   @click="$bvModal.show(`modal-edit-${data.id}`)">
                                                    <div
                                                        class="d-flex justify-content-between align-items-center text-black">
                                                        <span>{{ $t("general.edit") }}</span>
                                                        <i class="mdi mdi-square-edit-outline text-info"></i>
                                                    </div>
                                                </a>
                                                <a class="dropdown-item text-black" href="#" v-if="isPermission('delete invoice RealState')"
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
                                                 :title="getCompanyKey('invoice_edit_form')" title-class="font-18"
                                                 body-class="p-4" :ref="`edit-${data.id}`" :hide-footer="true"
                                                 @show="resetModalEdit(data.id)" @hidden="resetModalHiddenEdit(data.id)">
                                            <form>
                                                <div class="mb-3 d-flex justify-content-end">
                                                    <!-- Emulate built in modal footer ok and cancel button actions -->
                                                    <b-button variant="success" type="button" class="mx-1"
                                                              v-if="!isLoader" @click.prevent="editSubmit(data.id)">
                                                        {{ $t("general.Edit") }}
                                                    </b-button>

                                                    <b-button variant="success" class="mx-1" disabled v-else>
                                                        <b-spinner small></b-spinner>
                                                        <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                                    </b-button>

                                                    <b-button variant="danger" type="button"
                                                              @click.prevent="$bvModal.hide(`modal-edit-${data.id}`)">
                                                        {{ $t("general.Cancel") }}
                                                    </b-button>
                                                </div>
                                                <!-- <b-tabs nav-class="nav-tabs nav-bordered"> -->
                                                <!-- <b-tab :title="$t('general.DataEntry')" active> -->
                                                <div class="row">
                                                    <!-- <div
                                                        class="text-95 col-sm-6 align-self-start d-sm-flex">
                                                        <hr class="d-sm-none" />
                                                        <div class="text-grey-m2">
                                                            <div
                                                                class="mt-1 mb-2 text-secondary-m1 text-600 text-125">
                                                                {{ $t("general.Invoice") }}
                                                            </div>

                                                            <div class="my-2"><i
                                                                class="fa fa-circle text-blue-m2 text-xs mr-1"></i>
                                                                <span
                                                                    class="text-600 text-90">{{
                                                                        $t("general.serial") }}
                                                                                            : </span>
                                                                {{ serial_number }}
                                                            </div>
                                                        </div>
                                                    </div> -->
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-3 position-relative">
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                {{ getCompanyKey("serial") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input type="text" class="form-control" data-create="9"
                                                                   v-model="$v.edit.serial_number.$model" :class="{
                                                                        'is-invalid': $v.edit.serial_number.$error || errors.serial_number,
                                                                        'is-valid': !$v.edit.serial_number.$invalid && !errors.serial_number,
                                                                    }" />
                                                            <template v-if="errors.date">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.serial"
                                                                    :key="index">{{
                                                                        errorMessage }}
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
                                                                {{ getCompanyKey("invoice_date") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input type="date" class="form-control" data-create="9"
                                                                   v-model="$v.edit.date.$model" :class="{
                                                                        'is-invalid': $v.edit.date.$error || errors.date,
                                                                        'is-valid': !$v.edit.date.$invalid && !errors.date,
                                                                    }" />
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
                                                                         v-model="$v.edit.customer_id.$model"
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
                                                                                            $t("general.invoice_details")
                                                                                        }}</span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <!-- .row -->
                                                                        <hr class="row brc-default-l1 mx-n1 mb-4" />
                                                                        <div class="mt-4">
                                                                            <div
                                                                                class="row text-600 text-white bgc-default-tp1 py-25">
                                                                                <div class="col-1">#</div>
                                                                                <div class="col-2">{{
                                                                                        $t("general.code_number") }}</div>
                                                                                <div class="col-3">{{ $t("general.Item")
                                                                                    }}</div>
                                                                                <div class="col-2">{{
                                                                                        $t("general.amount") }}</div>
                                                                                <div class="col-2">{{
                                                                                        $t("general.quantity") }}</div>
                                                                                <div class="col-1">{{
                                                                                        $t("general.total") }}</div>
                                                                                <div class="col-1">{{
                                                                                        $t("general.Action") }}</div>
                                                                            </div>
                                                                            <div v-for="(item, index) in edit.invoice_details"
                                                                                 class="text-95 text-secondary-d3">
                                                                                <div class="row mb-2 mb-sm-0 py-25"
                                                                                     :class="index % 2 == 0 ? 'bgc-default-l4' : ''">
                                                                                    <div class="col-1 colStyle">{{ index
                                                                                    + 1 }}
                                                                                    </div>
                                                                                    <div class="col-2 colStyle">{{
                                                                                            item.code_number }}</div>
                                                                                    <div class="col-3">
                                                                                        <multiselect
                                                                                            :id="`edit-${index}-1`"
                                                                                            @input="showItemModal($event, index)"
                                                                                            v-model="$v.edit.invoice_details.$each[index].item_id.$model"
                                                                                            :options="items.map((type) => type.id)"
                                                                                            :custom-label="
                                                                                                    (opt) =>
                                                                                                        $i18n.locale == 'ar'
                                                                                                            ? items.find((x) => x.id == opt).name
                                                                                                            : items.find((x) => x.id == opt).name_e
                                                                                                " :class="{
                                                                                                        'is-invalid':
                                                                                                            $v.edit.invoice_details.$each[index].item_id.$error || errors.item_id,
                                                                                                    }">
                                                                                        </multiselect>
                                                                                        <div v-if="!$v.edit.invoice_details.$each[index].item_id.required"
                                                                                             class="invalid-feedback">
                                                                                            {{
                                                                                                $t("general.fieldIsRequired")
                                                                                            }}
                                                                                        </div>
                                                                                        <template v-if="errors.item_id">
                                                                                            <ErrorMessage
                                                                                                v-for="(errorMessage, index) in errors.item_id"
                                                                                                :key="index">{{
                                                                                                    errorMessage }}
                                                                                            </ErrorMessage>
                                                                                        </template>
                                                                                    </div>
                                                                                    <div class="col-2 colStyle">
                                                                                        <input
                                                                                            @keyup.enter="moveEnter('edit', index, 3)"
                                                                                            :id="`edit-${index}-2`"
                                                                                            v-model="$v.edit.invoice_details.$each[index].amount.$model"
                                                                                            class="form-control"
                                                                                            type="number" :class="{
                                                                                                    'is-invalid':
                                                                                                        $v.edit.invoice_details.$each[index].amount.$error || errors.amount,
                                                                                                    'is-valid': !$v.edit.invoice_details.$each[index].amount.$invalid && !errors.amount,
                                                                                                }" />
                                                                                        <div v-if="!$v.edit.invoice_details.$each[index].amount.required"
                                                                                             class="invalid-feedback">
                                                                                            {{
                                                                                                $t("general.fieldIsRequired")
                                                                                            }}
                                                                                        </div>

                                                                                        <template v-if="errors.amount">
                                                                                            <ErrorMessage
                                                                                                v-for="(errorMessage, index) in errors.amount"
                                                                                                :key="index">{{
                                                                                                    errorMessage }}
                                                                                            </ErrorMessage>
                                                                                        </template>
                                                                                    </div>
                                                                                    <div class="col-2 colStyle">
                                                                                        <input
                                                                                            @keyup.enter="moveEnter('edit', index, 4)"
                                                                                            :id="`edit-${index}-3`"
                                                                                            v-model="$v.edit.invoice_details.$each[index].quantity.$model"
                                                                                            class="form-control"
                                                                                            type="number" :class="{
                                                                                                    'is-invalid':
                                                                                                        $v.edit.invoice_details.$each[index].quantity.$error || errors.quantity,
                                                                                                    'is-valid': !$v.edit.invoice_details.$each[index].quantity.$invalid && !errors.quantity,
                                                                                                }" />
                                                                                        <div v-if="!$v.edit.invoice_details.$each[index].quantity.required"
                                                                                             class="invalid-feedback">
                                                                                            {{
                                                                                                $t("general.fieldIsRequired")
                                                                                            }}
                                                                                        </div>
                                                                                        <template
                                                                                            v-if="errors.quantity">
                                                                                            <ErrorMessage
                                                                                                v-for="(errorMessage, index) in errors.quantity"
                                                                                                :key="index">{{
                                                                                                    errorMessage }}
                                                                                            </ErrorMessage>
                                                                                        </template>
                                                                                    </div>
                                                                                    <div class="col-1 colStyle">{{ parseFloat(item.quantity * item.amount).toFixed(2) }}
                                                                                    </div>
                                                                                    <div class="col-1 colStyle">
                                                                                        <button
                                                                                            v-if="edit.invoice_details.length > 1"
                                                                                            type="button"
                                                                                            @click.prevent="removeNewFieldEdit(index)"
                                                                                            class="custom-btn-dowonload">
                                                                                            <i
                                                                                                class="fas fa-trash-alt"></i>
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="row border-b-2 brc-default-l2">
                                                                            </div>
                                                                            <div class="row mt-3">
                                                                                <div
                                                                                    class="col-12 col-sm-7 text-grey-d2 text-95 mt-2 mt-lg-0">
                                                                                </div>

                                                                                <div
                                                                                    class="col-12 col-sm-5 text-grey text-90 order-first order-sm-last">
                                                                                    <div
                                                                                        class="row my-2 align-items-center bgc-primary-l3 p-2">
                                                                                        <div class="col-7 text-right">
                                                                                            {{
                                                                                                $t("general.Total_Amount")
                                                                                            }}
                                                                                        </div>
                                                                                        <div class="col-5">
                                                                                                <span
                                                                                                    class="text-150 text-success-d3 opacity-2">{{
                                                                                                        getTotal(edit.invoice_details)
                                                                                                    }}</span>

                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <hr />
                                                                            <div>
                                                                                    <span
                                                                                        class="text-secondary-d1 text-105"></span>
                                                                                <b-button
                                                                                    v-if="edit.customer_id && getTotal(edit.invoice_details) > 0"
                                                                                    variant="primary"
                                                                                    class="btn btn-info btn-bold px-4 float-right mt-3 mt-lg-0"
                                                                                    @click.prevent="editSubmit(invoice_id, true)">
                                                                                    {{
                                                                                        $t('general.Break')
                                                                                    }}
                                                                                </b-button>
                                                                                <b-button v-else variant="secondary"
                                                                                          class="btn btn-secondary btn-bold px-4 float-right mt-3 mt-lg-0 ">
                                                                                    {{
                                                                                        $t('general.Break')
                                                                                    }}
                                                                                </b-button>
                                                                                <a @click.prevent="addNewFieldEdit"
                                                                                   href=""
                                                                                   class="btn btn-info btn-bold px-4 float-right mt-3 mt-lg-0 mx-2">{{
                                                                                        $t("general.Add") }}</a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                                <!-- </b-tab> -->
                                                <!-- <b-tab :title="$t('general.ImageUploads')">
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
                                                                                    <div class="row align-items-center">
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
                                                                    <b-button @click="changePhotoEdit" variant="success"
                                                                        type="button" class="mx-1 font-weight-bold px-3"
                                                                        v-if="!isLoader">
                                                                        {{ $t("general.Add") }}
                                                                    </b-button>
                                                                    <b-button variant="success" class="mx-1" disabled
                                                                        v-else>
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
                                                </b-tab> -->
                                                <!-- </b-tabs> -->
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
import Branch from "./general/branch"
import Item from "../create/realEstate/item.vue";
import transactionBreak from "../create/receivablePayment/transactionBreak/transactionBreak";

export default {
    name: "reservationCom",
    mixins: [translation],
    components: {
        Branch,
        Saleman,
        ErrorMessage,
        loader,
        Multiselect,
        customerGeneral,
        Item,
        transactionBreak
    },
    data() {
        return {
            per_page: 50,
            search: "",
            debounce: {},
            invoicesPagination: {},
            invoices: [],
            customers: [],
            salesmen: [],
            enabled3: true,
            isLoader: false,
            invoice_id: null,
            items: [],
            serial_number: "",
            openingBreak: '',
            breakCreate: {
                currency_id: 1,
                rate: 1,
                debit: 0,
                credit: 0,
                local_debit: 0,
                local_credit: 0,
            },
            currencies: [],
            create: {
                // media: [],
                branch_id: null,
                date: this.formatDate(new Date()),
                salesman_id: null,
                customer_id: null,
                invoice_details: [{
                    item_id: null,
                    quantity: 0,
                    amount: (0).toFixed(2),
                }]
            },
            edit: {
                branch_id: null,
                date: this.formatDate(new Date()),
                salesman_id: null,
                serial_number: "",
                // old_media: [],
                customer_id: null,
                invoice_details: [{
                    item_id: null,
                    quantity: 0,
                    amount: (0).toFixed(2),

                }]
            },
            images: [],
            media: {},
            saveImageName: [],
            showPhoto: "./images/img-1.png",
            setting: {
                date: true,
                customer_id: true,
                salesman_id: true,
                serial_id: true,
            },
            filterSetting: [
                this.$i18n.locale == 'ar' ? 'salesman.name' : 'salesman.name_e',
                this.$i18n.locale == 'ar' ? 'customer.name' : 'customer.name_e',
            ],
            errors: {},
            branches: [],
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
            }
        };
    },
    validations: {
        create: {
            // media: {},
            date: { required },
            customer_id: { required },
            branch_id: { required },
            salesman_id: { required },
            invoice_details: {
                required,
                $each: {
                    item_id: { required },
                    quantity: { required, minValue: minValue(0) },
                    amount: { required, minValue: minValue(0) },
                }
            }
        },
        breakCreate: {
            currency_id: { required },
            rate: { required },
            date: { required },
            debit: {},
            credit: {},
            local_debit: {},
            local_credit: {},
        },
        edit: {
            // media: {},
            date: { required },
            customer_id: { required },
            branch_id: { required },
            salesman_id: { required },
            serial_number: {},
            invoice_details: {
                required,
                $each: {
                    item_id: { required },
                    quantity: { required, minValue: minValue(0) },
                    amount: { required, minValue: minValue(0) },
                }
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
                this.invoices.forEach((el) => {
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
        showBreakEdit() {
            if (this.invoice_id) {
                this.openingBreak = {
                    date: this.edit.date,
                    customer_id: this.edit.customer_id,
                    currency_id: 1,
                    rate: 1,
                    debit: parseFloat(this.getTotal(this.edit.invoice_details)),
                    credit: 0,
                    id: this.invoice_id,
                    break_type: "invoice",
                    document_id: 4,
                    is_update: true
                };
                this.$bvModal.show("opening-balance-break-create");
            }
        },
        isPermission(item) {
            if (this.$store.state.auth.type == 'user'){
                return this.$store.state.auth.permissions.includes(item)
            }
            return true;
        },
        async getCurrencies() {
            this.isLoader = true;

            await adminApi
                .get(
                    `/currencies`
                )
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف عمله جديده  ", name_e: "Add New Currency" });
                    this.currencies = l;
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
        async saveOpenAndBreak() {
            this.AddSubmit(true);
        },
        accountRateCreate() {
            if (this.breakCreate.local_credit && this.breakCreate.local_credit > 0) {
                this.accountLocalCreditForCreate();
            }
            if (this.breakCreate.local_debit && this.breakCreate.local_debit > 0) {
                this.accountLocalDebitForCreate();
            }
        },
        accountLocalCreditForCreate() {
            this.breakCreate.credit = this.breakCreate.local_credit * this.breakCreate.rate;
        },
        accountLocalDebitForCreate() {
            this.breakCreate.debit = this.breakCreate.local_debit * this.breakCreate.rate;
        },
        showBreakCreate() {
            if (this.invoice_id) {
                this.openingBreak = {
                    date: this.create.date,
                    customer_id: this.create.customer_id,
                    currency_id: 1,
                    rate: 1,
                    debit: parseFloat(this.getTotal(this.create.invoice_details)),
                    credit: 0,
                    id: this.invoice_id,
                    break_type: "invoice",
                    document_id: 4
                };
                this.$bvModal.show("opening-balance-break-create");
            }
        },
        moveEnter(action, index, nextNumberInput) {
            if (nextNumberInput == 4 && action == "create") {
                if (this.create.invoice_details.length == (index + 1)) {
                    this.addNewField();
                }
                setTimeout(() => {
                    document.getElementById(`${action}-${index + 1}-1`).focus();
                }, 500)
            }
            else if (nextNumberInput == 4 && action == "edit") {
                if (this.edit.invoice_details.length == (index + 1)) {
                    this.addNewFieldEdit();
                }
                setTimeout(() => {
                    document.getElementById(`${action}-${index + 1}-1`).focus();
                }, 500)
            }
            else if (nextNumberInput < 4) {
                document.getElementById(`${action}-${index}-${nextNumberInput}`).focus();
            }
        },
        getCodeNumber(itemId) {
            let rest = this.items.filter(item => item.id == itemId);
            return rest.length ? rest[0].code_number : 0;
        },
        getTotal(invoice_details) {
            let total = 0;
            if (invoice_details) {
                invoice_details.forEach(detail => {
                    total += detail.quantity * detail.amount;
                });
            }
            return parseFloat(total).toFixed(2);

        },
        addNewField() {
            this.create.invoice_details.push({
                item_id: null,
                quantity: 0,
                amount: (0).toFixed(2),
            });
        },
        removeNewField(index) {
            if (this.create.invoice_details.length > 1) {
                this.create.invoice_details.splice(index, 1);
            }
        },
        addNewFieldEdit() {
            this.edit.invoice_details.push({
                item_id: null,
                quantity: 0,
                amount: (0).toFixed(2),
            });
        },
        removeNewFieldEdit(index) {
            if (this.edit.invoice_details.length > 1) {
                this.edit.invoice_details.splice(index, 1);
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
                                .put(`real-estate/invoices/${this.invoice_id}`, { old_media, media: new_media })
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
                                        .put(`real-estate/invoices/${this.invoice_id}`, { old_media, media: new_media })
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
                .put(`real-estate/invoices/${this.invoice_id}`, { old_media })
                .then((res) => {
                    this.invoices[index] = res.data.data;
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
                return;
            }
            else {
                // this.getSerialNumber(this.create.branch_id);
            }
        },
        showBranchModalEdit() {
            if (this.edit.branch_id == 0) {
                this.$bvModal.show("create_branch");
                this.edit.branch_id = null;
                return;
            }
            else {
                // this.getSerialNumber(this.edit.branch_id);
            }
        },

        showItemModal(id, index) {
            if (id == 0) {
                this.$bvModal.show("item-create");
                this.create.invoice_details[index].item_id = null;
                return;
            }
            this.moveEnter("create", index, 2);
            let rest = this.items.filter(item => item.id == this.create.invoice_details[index].item_id);
            this.create.invoice_details[index].code_number = rest.length ? rest[0].code_number : 0;
        },
        showItemModalEdit(id, index) {
            if (id == 0) {
                this.$bvModal.show("item-create");
                this.edit.invoice_details[index].item_id = null;
                return;
            }
            this.moveEnter("edit", index, 2);
            let rest = this.items.filter(item => item.id == this.edit.invoice_details[index].item_id);
            this.edit.invoice_details[index].code_number = rest.length ? rest[0].code_number : 0;
        },
        /**
         *  get Data invoices
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
            let filter = "";
            for (let i = 0; i < _filterSetting.length; ++i) {
                filter += `columns[${i}]=${_filterSetting[i]}&`;
            }
            adminApi
                .get(
                    `real-estate/invoices?invoice=true&page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
                )
                .then((res) => {
                    let l = res.data;
                    this.invoices = l.data;
                    this.invoicesPagination = l.pagination;
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
                this.current_page <= this.invoicesPagination.last_page &&
                this.current_page != this.invoicesPagination.current_page &&
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
                let filter = "";
                for (let i = 0; i < _filterSetting.length; ++i) {
                    filter += `columns[${i}]=${_filterSetting[i]}&`;
                }

                adminApi
                    .get(
                        `real-estate/invoices?invoice=true&page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&${filter}`
                    )
                    .then((res) => {
                        let l = res.data;
                        this.invoices = l.data;
                        this.invoicesPagination = l.pagination;
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
                            .post(`real-estate/invoices/bulk-delete`, { ids: id })
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
                            .delete(`real-estate/invoices/${id}`)
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
                branch_id: null,
                date: this.formatDate(new Date()),
                salesman_id: null,
                // media: null,
                customer_id: null,
                invoice_details: [{
                    item_id: null,
                    quantity: 0,
                    amount: (0).toFixed(2),
                }]
            };
            this.breakCreate = {
                currency_id: 1,
                rate: 1,
                debit: 0,
                credit: 0,
                local_debit: 0,
                local_credit: 0,
            };
            this.invoice_id = null;

            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.is_disabled = false;
            this.errors = {};
            this.images = [];

        },
        /**
         *  hidden Modal (create)
         */
        async resetModal() {
            await this.getCustomers();
            await this.getBranches();
            await this.getSalesmen();
            await this.getItems();
            this.create = {
                branch_id: null,
                date: this.formatDate(new Date()),
                salesman_id: null,
                customer_id: null,
                invoice_details: [{
                    item_id: null,
                    quantity: 0,
                    amount: (0).toFixed(2),
                }]
            };
            this.breakCreate = {
                currency_id: 1,
                rate: 1,
                debit: 0,
                credit: 0,
                local_debit: 0,
                local_credit: 0,
            };
            await this.getCurrencies();

            this.showPhoto = "./images/img-1.png";
            this.invoice_id = null;

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
            this.invoice_id = null;
            this.breakCreate = {
                currency_id: 1,
                rate: 1,
                debit: 0,
                credit: 0,
                local_debit: 0,
                local_credit: 0,
            };
            this.create = {
                branch_id: null,
                date: this.formatDate(new Date()),
                salesman_id: null,
                customer_id: null,
                invoice_details: [{
                    item_id: null,
                    quantity: 0,
                    amount: (0).toFixed(2),
                }]
            };
            this.is_disabled = false;
            this.media = {};
            this.images = [];

            this.$nextTick(() => {
                this.$v.$reset();
            });
        },
        AddSubmit(showBreak = false) {
            if (this.$v.create.$invalid) {
                this.$v.create.$touch();
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                this.is_disabled = false;
                let items = this.create.invoice_details.map(element => {
                    return {
                        ...element,
                        invoice_id: null
                    }
                })
                adminApi
                    .post(`real-estate/invoices`, {
                        ...this.create, items: items,document_id:4 ,
                        company_id: this.$store.getters["auth/company_id"]
                    })
                    .then((res) => {
                        this.getData();
                        this.invoice_id = res.data.data.id;
                        this.is_disabled = true;
                        if (showBreak) {
                            this.showBreakCreate();
                        }
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
        editSubmit(id, showBreak = false) {
            this.$v.edit.$touch();
            // this.images.forEach((e) => {
            //     this.edit.old_media.push(e.id);
            // });

            if (this.$v.edit.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                let items = this.edit.invoice_details.map(element => {
                    return {
                        ...element,
                        invoice_id: this.invoice_id
                    }
                })
                adminApi
                    .put(`real-estate/invoices/${id}`, { ...this.edit, items: items,document_id:4 })
                    .then((res) => {
                        this.$bvModal.hide(`modal-edit-${id}`);
                        this.getData();
                        if (showBreak) {
                            this.showBreakEdit();
                        }
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
                    if(this.isPermission('create customer')){
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
                .get(`/branches`)
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
        async getItems() {
            this.isLoader = true;
            await adminApi
                .get(`real-estate/item`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    if(this.isPermission('create items RealState')){
                        l.unshift({ id: 0, name: "اضف صنف", name_e: "Add item" });
                    }
                    this.items = l;
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
        async getSerialNumber(branchId) {
            this.isLoader = true;
            await adminApi
                .get(`/serial/branch?branch_id=${branchId}`)
                .then((res) => {
                    this.isLoader = false;
                    this.serial_number = res.data.data.serial_number ? res.data.data.serial_number : "";
                })
                .catch((err) => {
                    if (err.response.status == 404) {
                        this.isLoader = false;
                        this.serial_number = "";
                        return;
                    }
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
            let invoice = this.invoices.find((e) => id == e.id);
            this.invoice_id = invoice.id;
            await this.getCurrencies();
            await this.getCustomers();
            await this.getSalesmen();
            await this.getBranches();
            await this.getItems();
            this.edit.date = invoice.date;
            this.edit.customer_id = invoice.customer.id;
            this.edit.salesman_id = invoice.salesman.id;
            this.edit.branch_id = invoice.branch.id;
            this.edit.serial_number = invoice.serial_number;
            // await this.getSerialNumber(this.edit.branch_id);
            if (invoice.invoice_details && invoice.invoice_details.length) {
                this.edit.invoice_details = invoice.invoice_details.map(detail => {
                    return {
                        code_number: this.getCodeNumber(detail.item_id),
                        item_id: detail.item_id,
                        quantity: detail.quantity,
                        amount: parseFloat(detail.amount).toFixed(2),
                    };
                })
            }
            // if (this.images && this.images.length > 0) {
            //     this.showPhoto = this.images[this.images.length - 1].webp;
            // } else {
            //     this.showPhoto = "./images/img-1.png";
            // }
            this.errors = {};
        },
        /**
         *  hidden Modal (edit)
         */
        resetModalHiddenEdit(id) {
            this.errors = {};
            this.edit = {
                branch_id: null,
                date: this.formatDate(new Date()),
                salesman_id: null,
                customer_id: null,
                serial_number: null,
                // old_media: [],
                invoice_details: [{
                    item_id: null,
                    quantity: 0,
                    amount: (0).toFixed(2),
                }]
            };
            this.invoice_id = null;
            this.images = [];
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
                    .get(`real-estate/invoices/logs/${id}`)
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
    }
}
</script>

<style lang="scss">
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
