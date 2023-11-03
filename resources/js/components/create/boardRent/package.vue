<template>
    <div>
        <!--  search   -->
        <b-modal
            id="search"
            :title="$t('general.Search')"
            title-class="font-18"
            body-class="p-4"
            size="lg"
            :hide-footer="true"
        >
            <form>
                <div class="mb-3 d-flex justify-content-end">
                    <b-button
                        variant="success"
                        type="button" class="mx-1"
                        v-if="!isLoader"
                        @click.prevent="getPanel"
                    >
                        {{ $t('general.Search') }}
                    </b-button>

                    <b-button variant="success" class="mx-1" disabled v-else>
                        <b-spinner small></b-spinner>
                        <span class="sr-only">{{ $t('login.Loading') }}...</span>
                    </b-button>
                    <!-- Emulate built in modal footer ok and cancel button actions -->

                    <b-button variant="danger" type="button" @click.prevent="$bvModal.hide(`search`)">
                        {{ $t('general.Cancel') }}
                    </b-button>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey("boardRent_panel_code") }}
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                v-model="location.code"
                            />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey("boardRent_panel_category") }}
                            </label>
                            <multiselect
                                :disabled="true"
                                v-model="location.category_id"
                                :options="categories.map((type) => type.id)"
                                :custom-label="(opt) => categories.find((x) => x.id == opt)?
                                    $i18n.locale == 'ar' ? categories.find((x) => x.id == opt).name : categories.find((x) => x.id == opt).name_e
                                    : null
                                "
                            >
                            </multiselect>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey("boardRent_panel_governorate") }}
                            </label>
                            <multiselect
                                :disabled="true"
                                @input="showGovernorateModal"
                                v-model="location.governorate_id"
                                :options="governorates.map((type) => type.id)"
                                :custom-label="(opt) => governorates.find((x) => x.id == opt)?
                                    $i18n.locale == 'ar' ? governorates.find((x) => x.id == opt).name : governorates.find((x) => x.id == opt).name_e
                                    : null
                                "
                            >
                            </multiselect>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey("boardRent_panel_city") }}
                            </label>
                            <multiselect
                                @input="showCityModal"
                                v-model="location.city_id"
                                :options="cities.map((type) => type.id)"
                                :custom-label="(opt) => cities.find((x) => x.id == opt)?
                                    $i18n.locale == 'ar' ? cities.find((x) => x.id == opt).name : cities.find((x) => x.id == opt).name_e
                                    : null
                                "
                            >
                            </multiselect>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey("boardRent_panel_avenue") }}
                            </label>
                            <multiselect
                                @input="showAvenueModal"
                                v-model="location.avenue_id"
                                :options="avenues.map((type) => type.id)"
                                :custom-label="(opt) => avenues.find((x) => x.id == opt)?
                                    $i18n.locale == 'ar' ? avenues.find((x) => x.id == opt).name : avenues.find((x) => x.id == opt).name_e
                                    : null
                                "
                            >
                            </multiselect>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey("boardRent_panel_street") }}
                            </label>
                            <multiselect
                                v-model="location.street_id"
                                :options="streets.map((type) => type.id)"
                                :custom-label="(opt) => streets.find((x) => x.id == opt)?
                                    $i18n.locale == 'ar' ? streets.find((x) => x.id == opt).name : streets.find((x) => x.id == opt).name_e
                                    : null
                                "
                            >
                            </multiselect>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey("boardRent_panel_face") }}
                            </label>
                            <multiselect
                                v-model="location.face"
                                :options="faces"
                            >
                            </multiselect>
                        </div>
                    </div>
                </div>
            </form>
        </b-modal>
        <!--  /search   -->

        <!--  create   -->
        <b-modal
            :id="id"
            :title="type != 'edit'?getCompanyKey('boardRent_package_create_form'):getCompanyKey('boardRent_package_edit_form')"
            title-class="font-18"
            :body-class="is_panel?'p-1':'p-4'"
            :hide-footer="true"
            @show="resetModal"
            @hidden="resetModalHidden"
            dialog-class="modal-full-width"
        >
            <form>
                <loader size="large" v-if="isCustom && !isPage" />
                <div :class="{'position-relative': is_panel}">
                    <div
                        :class="['row justify-content-center']"
                        v-if="package_id && is_panel"
                    >
                        <div :class="['col-5',{'position-absolute': is_panel}]">
                            <div class="face">
                                <span class="face-name">A : {{ faceNumber.A }}</span>
                            </div>
                            <div class="face">
                                <span class="face-name">B : {{ faceNumber.B }}</span>
                            </div>
                            <div class="face">
                                <span class="face-name">Multi : {{ faceNumber.Multi }}</span>
                            </div>
                            <div class="face">
                                <span class="face-name">One-Face : {{ faceNumber["One-Face"] }}</span>
                            </div>
                        </div>
                    </div>

                    <div :class="['d-flex justify-content-end']">
                        <div :class="{'position-absolute': is_panel}">
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

                    <b-tabs :content-class="is_panel?'tab-content-custom':''" nav-class="nav-tabs nav-bordered">
                        <b-tab :title="$t('general.DataEntry')" active @click="is_panel = false">
                            <div class="row">
                                <div class="row col-8">
                                    <div class="col-md-6" v-if="isVisible('category_id')">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("boardRent_package_category") }}
                                                <span  v-if="isRequired('category_id')" class="text-danger">*</span>
                                            </label>
                                            <multiselect
                                                @input="addLocationCategory"
                                                v-model="$v.create.category_id.$model"
                                                :options="categories.map((type) => type.id)"
                                                :custom-label="(opt) => categories.find((x) => x.id == opt)?
                                                     $i18n.locale == 'ar' ? categories.find((x) => x.id == opt).name : categories.find((x) => x.id == opt).name_e
                                                     :null
                                                "
                                            >
                                            </multiselect>
                                            <div v-if="$v.create.category_id.$error || errors.category_id" class="text-danger">
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>
                                            <template v-if="errors.category_id">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.category_id"
                                                    :key="index"
                                                >{{ errorMessage }}</ErrorMessage
                                                >
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-6" v-if="isVisible('governorate_id')">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("boardRent_package_governorate") }}
                                                <span v-if="isRequired('governorate_id')" class="text-danger">*</span>
                                            </label>
                                            <multiselect
                                                @input="addLocationGovernorate"
                                                v-model="$v.create.governorate_id.$model"
                                                :options="governorates.map((type) => type.id)"
                                                :custom-label="(opt) => governorates.find((x) => x.id == opt)?
                                                         $i18n.locale == 'ar' ? governorates.find((x) => x.id == opt).name : governorates.find((x) => x.id == opt).name_e
                                                         :null
                                                    "
                                            >
                                            </multiselect>
                                            <template v-if="errors.governorate_id">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.governorate_id"
                                                    :key="index"
                                                >{{ errorMessage }}</ErrorMessage
                                                >
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-6" v-if="isVisible('name')">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">
                                                {{ getCompanyKey("boardRent_package_name_ar") }}
                                                <span v-if="isRequired('name')" class="text-danger">*</span>
                                            </label>
                                            <div dir="rtl">
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
                                    <div class="col-md-6" v-if="isVisible('name_e')">
                                        <div class="form-group">
                                            <label for="field-2" class="control-label">
                                                {{ getCompanyKey("boardRent_package_name_en") }}
                                                <span v-if="isRequired('name_e')" class="text-danger">*</span>
                                            </label>
                                            <div dir="ltr">
                                                <input
                                                    @keyup="englishValue(create.name_e)"
                                                    type="text"
                                                    class="form-control englishInput"
                                                    data-create="2"
                                                    v-model="$v.create.name_e.$model"
                                                    :class="{
                                                        'is-invalid': $v.create.name_e.$error || errors.name_e,
                                                        'is-valid': !$v.create.name_e.$invalid && !errors.name_e,
                                                      }"
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
                                    <div class="col-md-6" v-if="isVisible('code')">
                                        <div class="form-group">
                                            <label for="field-3" class="control-label">
                                                {{ getCompanyKey("boardRent_package_code") }}
                                                <span v-if="isRequired('code')" class="text-danger">*</span>
                                            </label>
                                            <div >
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    v-model="$v.create.code.$model"
                                                    :class="{
                                                    'is-invalid': $v.create.code.$error || errors.code,
                                                    'is-valid': !$v.create.code.$invalid && !errors.code,
                                                  }"
                                                    id="field-3"
                                                />
                                            </div>
                                            <template v-if="errors.code">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.code"
                                                    :key="index"
                                                >{{ errorMessage }}</ErrorMessage
                                                >
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-6" v-if="isVisible('price')">
                                        <div class="form-group">
                                            <label for="field-4" class="control-label">
                                                {{ getCompanyKey("boardRent_package_price") }}
                                                <span v-if="isRequired('price')" class="text-danger">*</span>
                                            </label>
                                            <div>
                                                <input
                                                    step=".01"
                                                    type="number"
                                                    class="form-control"
                                                    v-model="$v.create.price.$model"
                                                    :class="{
                                                    'is-invalid': $v.create.price.$error || errors.price,
                                                    'is-valid': !$v.create.price.$invalid && !errors.price,
                                                  }"
                                                    id="field-4"
                                                />
                                            </div>
                                            <template v-if="errors.price">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.price"
                                                    :key="index"
                                                >{{ errorMessage }}</ErrorMessage
                                                >
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-12" v-if="isVisible('note')">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("boardRent_package_note") }}
                                                <span v-if="isRequired('note')" class="text-danger">*</span>
                                            </label>
                                            <textarea
                                                class="form-control"
                                                v-model="$v.create.note.$model"
                                                :class="{
                                                    'is-invalid':$v.create.note.$error || errors.note,
                                                    'is-valid':!$v.create.note.$invalid && !errors.note,
                                                  }"
                                                rows="12"
                                            ></textarea>
                                            <template v-if="errors.note">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.note":key="index">
                                                    {{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </b-tab>
                        <b-tab
                            :title="$t('general.panel')"
                            :disabled="!package_id? true:false"
                            @click="is_panel = !is_panel"
                        >
                            <div class="d-flex justify-content-end position-relative">
                                <div>
                                    <b-button
                                        v-if="package_id && is_panel"
                                        @click.prevent="$bvModal.show(`search`)"
                                        variant="primary"
                                        class="mx-1 font-weight-bold"
                                    >
                                        {{ $t('general.Search') }}
                                        <i class="fe-search"></i>
                                    </b-button>
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
                                </div>
                            </div>
                            <div class="row">
                                <!-- selet panals -->
                                <div class="col-md-6">
                                    <!-- start Pagination -->
                                    <div class="d-inline-flex align-items-center pagination-custom position-relative">
                                        <div>
                                            <div class="d-inline-block" style="font-size: 13px">
                                                {{ pansPagination.from }}-{{ pansPagination.to }} /
                                                {{ pansPagination.total }}
                                            </div>
                                            <div class="d-inline-block">
                                                <a
                                                    href="javascript:void(0)"
                                                    :style="{
                                                          'pointer-events':
                                                            pansPagination.current_page > 1 ? '' : 'none',
                                                        }"
                                                    @click.prevent="getPanel(pansPagination.current_page - 1)"
                                                >
                                                    <span>&lt;</span>
                                                </a>
                                                <input
                                                    type="text"
                                                    @keyup.enter="getPanelPagination()"
                                                    v-model="current_page_pans"
                                                    class="pagination-current-page"
                                                />
                                                <a
                                                    href="javascript:void(0)"
                                                    :style="{
                                                          'pointer-events':
                                                            (pansPagination.last_page ==
                                                            pansPagination.current_page) || !pansPagination
                                                              ? 'none'
                                                              : '',
                                                        }"
                                                    @click.prevent="getPanel(pansPagination.current_page + 1)"
                                                >
                                                    <span>&gt;</span>
                                                </a>
                                            </div>
                                        </div>
                                        <div style="position: absolute;transform: translate(230px, 0px)">
                                            <h3>{{ $t('general.SelectPanel') }}</h3>
                                        </div>
                                    </div>
                                    <!-- end Pagination -->
                                    <table class="table table-borderless table-hover table-centered m-0">
                                        <thead>
                                        <tr>
                                            <th>
                                                <div class="d-flex justify-content-center">
                                                    <span>{{ getCompanyKey("boardRent_panel_code") }}</span>
                                                </div>
                                            </th>
                                            <th>
                                                <div class="d-flex justify-content-center">
                                                    <span>{{ getCompanyKey("boardRent_panel_category") }}</span>
                                                </div>
                                            </th>
                                            <th >
                                                <div class="d-flex justify-content-center">
                                                    <span>{{ getCompanyKey("boardRent_panel_governorate") }}</span>
                                                </div>
                                            </th>
                                            <th >
                                                <div class="d-flex justify-content-center">
                                                    <span>{{ getCompanyKey("boardRent_panel_city") }}</span>
                                                </div>
                                            </th>
                                            <th >
                                                <div class="d-flex justify-content-center">
                                                    <span>{{ getCompanyKey("boardRent_panel_avenue") }}</span>
                                                </div>
                                            </th>
                                            <th >
                                                <div class="d-flex justify-content-center">
                                                    <span>{{ getCompanyKey("boardRent_panel_street") }}</span>
                                                </div>
                                            </th>
                                            <th >
                                                <div class="d-flex justify-content-center">
                                                    <span>{{ getCompanyKey("boardRent_panel_face") }}</span>
                                                </div>
                                            </th>
                                            <th>
                                                {{ $t("general.Action") }}
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody v-if="pans.length > 0">
                                        <tr
                                            v-for="(data, index) in pans"
                                            :key="data.id"
                                            class="body-tr-custom"
                                        >
                                            <td scope="col">
                                                {{ data.code }}
                                            </td>
                                            <td scope="col">
                                                {{ $i18n.locale == 'ar' ? data.category.name : data.category.name_e }}
                                            </td>
                                            <td scope="col">
                                                {{  data.governorate ? $i18n.locale == 'ar' ? data.governorate.name : data.governorate.name_e : '-' }}
                                            </td>
                                            <td scope="col">
                                                {{ data.city ? $i18n.locale == 'ar' ? data.city.name : data.city.name_e : '-' }}
                                            </td>
                                            <td scope="col">
                                                {{ data.avenue ? $i18n.locale == 'ar' ? data.avenue.name : data.avenue.name_e : '-' }}
                                            </td>
                                            <td scope="col">
                                                {{ data.street ? $i18n.locale == 'ar' ? data.street.name : data.street.name_e : '-' }}
                                            </td>
                                            <td scope="col">
                                                {{ data.face }}
                                            </td>
                                            <td scope="col" style="width: 0">
                                                <div class="form-check custom-control">
                                                    <input
                                                        class="form-check-input"
                                                        type="checkbox"
                                                        :value="data.id"
                                                        @change="checkRowPanel(data)"
                                                        v-model="CheckAllPanel"
                                                        style="width: 17px; height: 17px"
                                                    />
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

                                <!-- panals package -->
                                <div class="col-md-6">
                                    <!-- start Pagination -->
                                    <div class="d-inline-flex align-items-center pagination-custom position-relative">
                                        <div>
                                            <div class="d-inline-block" style="font-size: 13px">
                                                {{ panelPackagesPaginatation.from }}-{{ panelPackagesPaginatation.to }} /
                                                {{ panelPackagesPaginatation.total }}
                                            </div>
                                            <div class="d-inline-block">
                                                <a
                                                    href="javascript:void(0)"
                                                    :style="{
                                                          'pointer-events':
                                                            current_page_pans_pack > 1 ? '' : 'none',
                                                        }"
                                                    @click.prevent="paginate(current_page_pans_pack - 1)"
                                                >
                                                    <span>&lt;</span>
                                                </a>
                                                <input
                                                    type="text"
                                                    @keyup.enter="paginate(current_page_pans_pack)"
                                                    v-model="current_page_pans_pack"
                                                    class="pagination-current-page"
                                                />
                                                <a
                                                    href="javascript:void(0)"
                                                    :style="{
                                                          'pointer-events':
                                                            (panelPackagesPaginatation.last_page ==
                                                            current_page_pans_pack) || !panelPackagesPaginatation
                                                              ? 'none'
                                                              : '',
                                                        }"
                                                    @click.prevent="paginate(current_page_pans_pack + 1)"
                                                >
                                                    <span>&gt;</span>
                                                </a>
                                            </div>
                                        </div>
                                        <div style="position: absolute;transform: translate(230px, 0px)">
                                            <h3>{{ $t('general.Selected') }}</h3>
                                        </div>
                                    </div>
                                    <!-- end Pagination -->

                                    <table class="table table-borderless table-hover table-centered">
                                        <thead>
                                        <tr>
                                            <th>
                                                <div class="d-flex justify-content-center">
                                                    <span>{{ getCompanyKey("boardRent_panel_code") }}</span>
                                                </div>
                                            </th>
                                            <th>
                                                <div class="d-flex justify-content-center">
                                                    <span>{{ getCompanyKey("boardRent_panel_category") }}</span>
                                                </div>
                                            </th>
                                            <th >
                                                <div class="d-flex justify-content-center">
                                                    <span>{{ getCompanyKey("boardRent_panel_governorate") }}</span>
                                                </div>
                                            </th>
                                            <th >
                                                <div class="d-flex justify-content-center">
                                                    <span>{{ getCompanyKey("boardRent_panel_city") }}</span>
                                                </div>
                                            </th>
                                            <th >
                                                <div class="d-flex justify-content-center">
                                                    <span>{{ getCompanyKey("boardRent_panel_avenue") }}</span>
                                                </div>
                                            </th>
                                            <th >
                                                <div class="d-flex justify-content-center">
                                                    <span>{{ getCompanyKey("boardRent_panel_street") }}</span>
                                                </div>
                                            </th>
                                            <th >
                                                <div class="d-flex justify-content-center">
                                                    <span>{{ getCompanyKey("boardRent_panel_face") }}</span>
                                                </div>
                                            </th>
                                            <th>
                                                {{ $t("general.Action") }}
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody v-if="panelPackages.length > 0">
                                        <tr
                                            v-for="(data, index) in panelPackages"
                                            :key="data.id"
                                            class="body-tr-custom"
                                        >
                                            <td scope="col">
                                                {{ data.code }}
                                            </td>
                                            <td scope="col">
                                                {{ $i18n.locale == 'ar' ? data.category.name : data.category.name_e }}
                                            </td>
                                            <td scope="col">
                                                {{  data.governorate ? $i18n.locale == 'ar' ? data.governorate.name : data.governorate.name_e : '-' }}
                                            </td>
                                            <td scope="col">
                                                {{ data.city ? $i18n.locale == 'ar' ? data.city.name : data.city.name_e : '-' }}
                                            </td>
                                            <td scope="col">
                                                {{ data.avenue ? $i18n.locale == 'ar' ? data.avenue.name : data.avenue.name_e : '-' }}
                                            </td>
                                            <td scope="col">
                                                {{ data.street ? $i18n.locale == 'ar' ? data.street.name : data.street.name_e : '-' }}
                                            </td>
                                            <td scope="col">
                                                {{ data.face }}
                                            </td>
                                            <td scope="col" style="width: 0">
                                                <div class="form-check custom-control">
                                                    <input
                                                        class="form-check-input"
                                                        type="checkbox"
                                                        :value="data.id"
                                                        @change="checkRowPanel(data)"
                                                        v-model="CheckAllPanel"
                                                        style="width: 17px; height: 17px"
                                                    />
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
                            </div>

                        </b-tab>
                    </b-tabs>
                </div>
            </form>
        </b-modal>
        <!--  /create   -->
    </div>
</template>

<script>
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import {decimal, maxLength, minLength, minValue, required, requiredIf} from "vuelidate/lib/validators";
import adminApi from "../../../api/adminAxios";
import Swal from "sweetalert2";
import {arabicValue, englishValue} from "../../../helper/langTransform";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import Multiselect from "vue-multiselect";
import Status from "../general/status";
import successError from "../../../helper/mixin/success&error";
import {formatDateOnly} from "../../../helper/startDate";
import {dynamicSortNumber, dynamicSortString} from "../../../helper/tableSort";

export default {
    name: "package",
    components: {
        ErrorMessage,
        loader,
        Multiselect,
        Status
    },
    mixins: [transMixinComp,successError],
    props: {
        id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/boards-rent/packages'}
    },
    data() {
        return {
            isCustom: false,
            fields: [],
            isLoader: false,
            typeCom: 'create',
            package_id: null,
            location: {city_id: null,governorate_id: null,avenue_id: null,category_id: null,face: null,street_id: null,code: ''},
            panels: [],
            create: {
                name: "",
                name_e: "",
                code: "",
                price: "",
                note: "",
                category_id: null,
                governorate_id: null,
            },
            faces: ['A','B','Multi','One-Face'],
            faceNumber: {'A': 0,'B': 0,'Multi': 0,'One-Face': 0},
            company_id: null,
            governorates: [],
            cities: [],
            avenues: [],
            streets: [],
            categories: [],
            pans: [],
            pansPagination: {},
            current_page_pans: 1,
            errors: {},
            allPanelPackages: [],
            panelPackages: [],
            panelPackagesPaginatation: {},
            current_page: 1,
            current_page_pans_pack: 1,
            is_disabled: false,
            is_panel: false,
            CheckAllPanel: [],
        };
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
    },
    validations: {
        create: {
            category_id: { required: requiredIf(function (model) {
                    return this.isRequired("category_id");
                }) },
            governorate_id: { required: requiredIf(function (model) {
                    return this.isRequired("governorate_id");
                }) },
            name: { required: requiredIf(function (model) {
                    return this.isRequired("name");
                }), minLength: minLength(2), maxLength: maxLength(255) },
            name_e: { required: requiredIf(function (model) {
                    return this.isRequired("name_e");
                }), minLength: minLength(2), maxLength: maxLength(255) },
            code: { required: requiredIf(function (model) {
                    return this.isRequired("code");
                }) },
            price: { required: requiredIf(function (model) {
                    return this.isRequired("price");
                }), decimal , minValue: minValue(0.01) },
            note: { required: requiredIf(function (model) {
                    return this.isRequired("note");
                }) },
        },
    },
    methods: {
        async getCustomTableFields() {
            this.isCustom = true;
            await adminApi
                .get(`/customTable/table-columns/boards_rent_packages`)
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
        arabicValue(txt) {
            this.create.name = arabicValue(txt);
        },
        englishValue(txt) {
            this.create.name_e = englishValue(txt);
        },
        defaultData(edit = null){
            this.create = {
                name: "",
                name_e: "",
                code: "",
                price: "",
                note: "",
                category_id: null,
                governorate_id: null,
            };
            this.package_id = null;
            this.location = {city_id: null,governorate_id: null,avenue_id: null,category_id: null,face: null,street_id: null,code: ''};
            this.faceNumber = {'A': 0,'B': 0,'Multi': 0,'One-Face': 0};
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
            this.is_panel = false;
            this.is_disabled = false;
            this.current_page_pans_pack = 1;
            this.allPanelPackages = [];
            this.panelPackages = [];
            this.panelPackagesPaginatation = {};
            this.pans = [];
            this.CheckAllPanel = [];
            this.pansPagination = {};
            this.current_page_pans = 1;
            this.cities = [];this.avenues = [];this.streets = [];this.pans = [];
            if(this.type != 'edit')
                this.typeCom = 'create';
            else
                this.typeCom = 'edit'
        },
        getPanelUpdate(id) {
            adminApi
                .get(`/boards-rent/packages/relation-package-panel/${id}`)
                .then((res) => {
                    let l = res.data.data;
                    if(l.panels && l.panels.length > 0){
                        this.allPanelPackages = l.panels;
                        this.allPanelPackages.forEach((el,index) => {
                            this.CheckAllPanel.push(el.id);
                        });
                        this.paginate();
                        this.changeValuePanel();
                    }
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                });
        },
        resetModalHidden() {
            this.defaultData();
            this.$bvModal.hide(this.id);
        },
        resetModal() {
            this.defaultData();
            setTimeout( () => {
                if(this.type != 'edit'){
                    if(!this.isPage)  this.getCustomTableFields();
                    this.getGovernorate();
                    this.getCategory();
                }else {
                    if(this.idObjEdit.dataObj){
                        this.errors = {};
                        this.cities = [];this.avenues = [];this.streets = [];this.pans = [];
                        this.location = {city_id: null,governorate_id: null,avenue_id: null,category_id: null,face: null,street_id: null,code: ''};
                        this.is_panel = false;
                        this.CheckAllPanel = [];
                        this.current_page_pans_pack = 1;
                        let pack = this.idObjEdit.dataObj;
                        this.package_id = pack.id;
                        this.create.name = pack.name;
                        this.create.name_e = pack.name_e;
                        this.create.code = pack.code;
                        this.create.price = pack.price;
                        this.create.note = pack.note;
                        this.getGovernorate();
                        this.getCategory();
                        this.create.governorate_id = pack.governorate_id;
                        this.create.category_id = pack.category_id;
                        this.location.governorate_id = pack.governorate_id;
                        this.location.category_id = pack.category_id;
                        if (this.location.governorate_id) this.showGovernorateModal();
                        this.getPanelUpdate(pack.id);
                    }
                }
            },50);
        },
        getGovernorate() {

            this.governorates = [];this.cities = [];this.avenues = [];this.streets = [];

             adminApi
                .get(`/governorates?columns[0]=country.id&search=1`)
                .then((res) => {
                    let l = res.data.data;
                    this.governorates = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');

                });
        },
        getCity() {
            this.isLoader = true;
            let governorate = this.location.governorate_id;
            this.location.city_id = null;
            this.location.avenue_id = null;
            this.location.street_id = null;
            this.cities = [];this.avenues = [];this.streets = [];

             adminApi
                .get(`/cities?columns[0]=governorate.id&search=${governorate}`)
                .then((res) => {
                    let l = res.data.data;
                    this.cities = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');

                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        getAvnue() {
            this.isLoader = true;
            let city = this.location.city_id;
            this.location.avenue_id = null;
            this.location.street_id = null;
            this.avenues = [];this.streets = [];

             adminApi
                .get(`/avenues?columns[0]=city.id&search=${city}`)
                .then((res) => {
                    let l = res.data.data;
                    this.avenues = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');

                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        getStreet() {
            this.isLoader = true;
            let avenue = this.location.avenue_id;
            this.location.street_id = null;
            this.streets = [];

             adminApi
                .get(`/streets?columns[0]=avenue.id&search=${avenue}`)
                .then((res) => {
                    let l = res.data.data;
                    this.streets = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');

                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        getPanel() {
            this.isLoader = true;
            let filter = '';
            filter += this.location.category_id ? `&category_id=${this.location.category_id}` : '';
            filter += this.location.governorate_id ? `&governorate_id=${this.location.governorate_id}` : '';
            filter += this.location.city_id ? `&city_id=${this.location.city_id}` : '';
            filter += this.location.avenue_id ? `&avenue_id=${this.location.avenue_id}` : '';
            filter += this.location.face ? `&face=${this.location.face}` : '';
            filter += this.location.street_id ? `&street_id=${this.location.street_id}` : '';
            filter += this.location.code ? `&code=${this.location.code}` : '';

             adminApi
                .get(
                    `/boards-rent/panels/search-drop-down?page=${1}&per_page=${20}&packages=1${filter}`
                )
                .then((res) => {
                    let l = res.data;
                    this.pans = l.data;
                    this.pansPagination = l.pagination;
                    this.current_page_pans = l.pagination.current_page;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');

                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        getPanelPagination() {

            if (
                this.current_page_pans <= this.pansPagination.last_page &&
                this.current_page_pans != this.pansPagination.current_page &&
                this.current_page_pans
            ) {
                this.isLoader = true;
                let filter = '';
                filter += this.location.category_id ? `&category_id=${this.location.category_id}` : '';
                filter += this.location.governorate_id ? `&governorate_id=${this.location.governorate_id}` : '';
                filter += this.location.city_id ? `&city_id=${this.location.city_id}` : '';
                filter += this.location.avenue_id ? `&avenue_id=${this.location.avenue_id}` : '';
                filter += this.location.face ? `&face=${this.location.face}` : '';
                filter += this.location.street_id ? `&street_id=${this.location.street_id}` : '';
                filter += this.location.code ? `&code=${this.location.code}` : '';

                 adminApi
                    .get(
                        `/boards-rent/panels/search-drop-down?page=${20}&per_page=${7}&packages=1${filter}`
                    )
                    .then((res) => {
                        let l = res.data;
                        this.pans = l.data;
                        this.pansPagination = l.pagination;
                        this.current_page_pans = l.pagination.current_page;
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
        getCategory() {
            this.isLoader = true;

             adminApi
                .get(
                    `/categories`
                )
                .then((res) => {
                    let l = res.data.data;
                    this.categories = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');

                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        resetForm() {
            this.defaultData();
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

                if(this.type != 'edit' && this.typeCom != 'edit'){
                    adminApi
                        .post(this.url, {
                            ...this.create,company_id: this.company_id,panels: this.CheckAllPanel
                        })
                        .then((res) => {
                            this.is_disabled = true;
                            this.package_id = res.data.data.id;
                            this.typeCom = 'edit';
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
                    adminApi
                        .put(`${this.url}/${this.package_id}`, {
                            ...this.create,
                            panels: this.CheckAllPanel,
                            company_id: this.$store.getters["auth/company_id"],
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
        checkRowPanel(data) {
            let panel = this.allPanelPackages.find((el) => el.id == data.id);
            if (!panel) {
                this.allPanelPackages.push(data);
            } else {
                let index = this.allPanelPackages.findIndex((el) => el.id == data.id);
                this.allPanelPackages.splice(index, 1);
            }
            this.paginate(this.current_page_pans_pack ? this.current_page_pans_pack : 1);
            this.changeValuePanel();
        },
        showGovernorateModal() {
            if (this.location.governorate_id > 0) {
                 this.getCity();
            }
        },
        showCityModal() {
            if (this.location.city_id > 0) {
                  this.getAvnue();
            }
        },
        showAvenueModal() {
            if (this.location.avenue_id > 0) {
                this.getStreet();
            }
        },
        changeValuePanel (){
            this.faceNumber = {'A': 0,'B': 0,'Multi': 0,'One-Face': 0};
            this.allPanelPackages.forEach((e,index) => {
                if(e.face == 'A'){
                    this.faceNumber.A = this.faceNumber.A + 1
                }else if(e.face == 'B'){
                    this.faceNumber.B = this.faceNumber.B + 1
                }else if(e.face == 'Multi'){
                    this.faceNumber.Multi = this.faceNumber.Multi + 1
                }else if(e.face == 'One-Face'){
                    this.faceNumber['One-Face'] = this.faceNumber['One-Face'] + 1
                }
            });
        },
        // paginate
        paginate(p = 1){

            const page = p;
            this.current_page_pans_pack = page;
            const limit = 20;
            const skip = (page - 1) * limit;
            const endIndex = page * limit;
            const total = this.allPanelPackages.length;

            // Pagination result
            this.panelPackagesPaginatation.total = total;
            this.panelPackagesPaginatation.limit = limit;
            this.panelPackagesPaginatation.last_page = Math.ceil(total / limit);
            this.panelPackagesPaginatation.to = skip? (skip + limit) : limit;
            this.panelPackagesPaginatation.from = skip ? skip : 1;
            this.panelPackages = [];
            this.panelPackages = this.allPanelPackages.slice(skip,skip+limit);

        },
        addLocationCategory()
        {
            if (this.create.category_id)
            {
                this.location.category_id = this.create.category_id;
            }else {
                this.location.category_id = null ;
            }
        },
        addLocationGovernorate()
        {
            if (this.create.governorate_id)
            {
                this.location.governorate_id = this.create.governorate_id;
                this.showGovernorateModal();
            }else {
                this.location.governorate_id = null ;
            }
        },
    },
}
</script>

<style scoped>
form {
    position: relative;
}
.face {
    display: inline-block;
    text-align: center;
    margin: 0 5px;
}

.face .face-name {
    background-color: #6dc6f5;
    padding: 0px 8px;
    font-size: 16px;
    font-weight: 700;
    color: #fff;
    margin-bottom: 7px;
    display: block;
}
</style>
