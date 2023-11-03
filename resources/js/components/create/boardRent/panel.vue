<template>
<div>
    <UnitStatus
        :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getUnitStatus"
        :isPage="false" type="create" :isPermission="isPermission" :id="'unitStatus-create-panel'"
    />
    <Category
        :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getCategory"
        :isPage="false" type="create" :isPermission="isPermission" :id="'category-create-panel'"
    />
    <Governate
        :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getGovernorate"
        :isPage="false" type="create" :isPermission="isPermission" :id="'governorate-create-panel'"
        :country_id="this.create.country_id"
    />
    <City
        :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getCity"
        :isPage="false" type="create" :isPermission="isPermission" :id="'city-create-panel'"
        :country_id="this.create.country_id" :governate_id="this.create.governorate_id"
    />
    <Avnue
        :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getAvnue"
        :isPage="false" type="create" :isPermission="isPermission" :id="'avnue-create-panel'"
        :country_id="this.create.country_id" :governorate_id="this.create.governorate_id"
        :city_id="this.create.city_id"
    />
    <Street
        :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getStreet"
        :isPage="false" type="create" :isPermission="isPermission" :id="'street-create-panel'"
        :avenue_id="this.create.avenue_id"
    />

    <!--  create   -->
    <b-modal
        :id="id"
        :title="type != 'edit'?getCompanyKey('boardRent_panel_create_form'):getCompanyKey('boardRent_panel_edit_form')"
        title-class="font-18"
        body-class="p-4"
        dialog-class="modal-full-width"
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
                        type="submit"
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
                <b-button
                    @click.prevent="resetModalHidden"
                    variant="danger"
                    type="button"
                >
                    {{ $t("general.Cancel") }}
                </b-button>
            </div>
            <div class="row">
                <div class="col-md-6" v-if="isVisible('name')">
                    <div class="form-group">
                        <label class="control-label">
                            {{ getCompanyKey("boardRent_panel_name_ar") }}
                            <span v-if="isRequired('name')" class="text-danger">*</span>
                        </label>
                        <div dir="rtl">
                            <input
                                disabled
                                @keyup="arabicValue(create.name)"
                                type="text"
                                class="form-control"
                                v-model="$v.create.name.$model"
                                :class="{
                                                    'is-invalid': $v.create.name.$error || errors.name,
                                                    'is-valid': !$v.create.name.$invalid && !errors.name,
                                                  }"
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
                            {{ getCompanyKey("boardRent_panel_name_en") }}
                            <span v-if="isRequired('name_e')" class="text-danger">*</span>
                        </label>
                        <div dir="ltr">
                            <input
                                disabled
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
            </div>
            <hr style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)"  v-if="isVisible('new_code')||isVisible('code')||isVisible('face')||isVisible('size')" />
            <div class="row">
                <div class="col-md-3" v-if="isVisible('code')">
                    <div class="form-group">
                        <label for="field-3" class="control-label">
                            {{ getCompanyKey("boardRent_panel_code") }}
                            <span v-if="isRequired('code')" class="text-danger">*</span>
                        </label>
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
                        <template v-if="errors.code">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.code"
                                :key="index"
                            >{{ errorMessage }}</ErrorMessage
                            >
                        </template>
                    </div>
                </div>
                <div class="col-md-3" v-if="isVisible('new_code')">
                    <div class="form-group">
                        <label for="field-4" class="control-label">
                            {{ getCompanyKey("boardRent_panel_new_code") }}
                            <span v-if="isRequired('new_code')" class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            class="form-control"
                            v-model="$v.create.new_code.$model"
                            :class="{
                                                    'is-invalid': $v.create.new_code.$error || errors.new_code,
                                                    'is-valid': !$v.create.new_code.$invalid && !errors.new_code,
                                                  }"
                            id="field-4"
                        />
                        <template v-if="errors.new_code">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.new_code"
                                :key="index"
                            >{{ errorMessage }}</ErrorMessage
                            >
                        </template>
                    </div>
                </div>
                <div class="col-md-3" v-if="isVisible('face')">
                    <div class="form-group">
                        <label class="control-label">
                            {{ getCompanyKey("boardRent_panel_face") }}
                            <span v-if="isRequired('face')" class="text-danger">*</span>
                        </label>
                        <multiselect
                            v-model="create.face"
                            :options="faces"
                        >
                        </multiselect>
                        <div v-if="$v.create.face.$error || errors.face" class="text-danger">
                            {{ $t("general.fieldIsRequired") }}
                        </div>
                        <template v-if="errors.face">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.face"
                                :key="index"
                            >{{ errorMessage }}</ErrorMessage
                            >
                        </template>
                    </div>
                </div>
                <div class="col-md-3" v-if="isVisible('size')">
                    <div class="form-group">
                        <label for="field-1" class="control-label">
                            {{ getCompanyKey("boardRent_panel_size") }}
                            <span v-if="isRequired('size')" class="text-danger">*</span>
                        </label>
                        <div dir="rtl">
                            <input
                                type="text"
                                class="form-control"
                                v-model="$v.create.size.$model"
                                :class="{
                                                    'is-invalid': $v.create.size.$error || errors.size,
                                                    'is-valid': !$v.create.size.$invalid && !errors.size,
                                                  }"
                                id="field-1"
                            />
                        </div>
                        <template v-if="errors.size">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.size"
                                :key="index"
                            >{{ errorMessage }}</ErrorMessage
                            >
                        </template>
                    </div>
                </div>
            </div>
            <hr style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" v-if="isVisible('category_id')||isVisible('unit_status_id')||isVisible('governorate_id')||isVisible('city_id')||isVisible('avenue_id')||isVisible('street_id')||isVisible('lng')||isVisible('lat')" />
            <div class="row">
                <div class="col-md-3" v-if="isVisible('category_id')">
                    <div class="form-group">
                        <label class="control-label">
                            {{ getCompanyKey("boardRent_panel_category") }}
                            <span  v-if="isRequired('category_id')" class="text-danger">*</span>
                        </label>
                        <multiselect
                            @input="showeCategoryModal"
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
                <div class="col-md-3" v-if="isVisible('unit_status_id')">
                    <div class="form-group">
                        <label class="control-label">
                            {{ getCompanyKey("boardRent_panel_unit_status") }}
                            <span v-if="isRequired('unit_status_id')" class="text-danger">*</span>
                        </label>
                        <multiselect
                            @input="showUnitStatusModal"
                            v-model="$v.create.unit_status_id.$model"
                            :options="unit_statuses.map((type) => type.id)"
                            :custom-label="(opt) => unit_statuses.find((x) => x.id == opt)?
                                 $i18n.locale == 'ar' ? unit_statuses.find((x) => x.id == opt).name : unit_statuses.find((x) => x.id == opt).name_e
                                 :null
                            "
                        >
                        </multiselect>
                        <div v-if="$v.create.unit_status_id.$error || errors.unit_status_id" class="text-danger">
                            {{ $t("general.fieldIsRequired") }}
                        </div>
                        <template v-if="errors.unit_status_id">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.unit_status_id"
                                :key="index"
                            >{{ errorMessage }}</ErrorMessage
                            >
                        </template>
                    </div>
                </div>
                <div class="col-md-3" v-if="isVisible('governorate_id')">
                    <div class="form-group">
                        <label class="control-label">
                            {{ getCompanyKey("boardRent_panel_governorate") }}
                            <span v-if="isRequired('governorate_id')" class="text-danger">*</span>
                        </label>
                        <multiselect
                            @input="showGovernorateModal"
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
                <div class="col-md-3" v-if="isVisible('city_id')">
                    <div class="form-group">
                        <label class="control-label">
                            {{ getCompanyKey("boardRent_panel_city") }}
                            <span v-if="isRequired('city_id')" class="text-danger">*</span>
                        </label>
                        <multiselect
                            @input="showCityModal"
                            v-model="$v.create.city_id.$model"
                            :options="cities.map((type) => type.id)"
                            :custom-label="(opt) => cities.find((x) => x.id == opt)?
                                 $i18n.locale == 'ar' ? cities.find((x) => x.id == opt).name : cities.find((x) => x.id == opt).name_e
                                 :null
                            "
                        >
                        </multiselect>
                        <template v-if="errors.city_id">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.city_id"
                                :key="index"
                            >{{ errorMessage }}</ErrorMessage
                            >
                        </template>
                    </div>
                </div>
                <div class="col-md-3" v-if="isVisible('avenue_id')">
                    <div class="form-group">
                        <label class="control-label">
                            {{ getCompanyKey("boardRent_panel_avenue") }}
                            <span v-if="isRequired('avenue_id')" class="text-danger">*</span>
                        </label>
                        <multiselect
                            @input="showAvenueModal"
                            v-model="$v.create.avenue_id.$model"
                            :options="avenues.map((type) => type.id)"
                            :custom-label="(opt) => avenues.find((x) => x.id == opt)?
                                 $i18n.locale == 'ar' ? avenues.find((x) => x.id == opt).name : avenues.find((x) => x.id == opt).name_e
                                 :null
                            "
                        >
                        </multiselect>
                        <template v-if="errors.avenue_id">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.avenue_id"
                                :key="index"
                            >{{ errorMessage }}</ErrorMessage
                            >
                        </template>
                    </div>
                </div>
                <div class="col-md-3" v-if="isVisible('street_id')">
                    <div class="form-group">
                        <label class="control-label">
                            {{ getCompanyKey("boardRent_panel_street") }}
                            <span v-if="isRequired('street_id')" class="text-danger">*</span>
                        </label>
                        <multiselect
                            @input="showStreetModal"
                            v-model="$v.create.street_id.$model"
                            :options="streets.map((type) => type.id)"
                            :custom-label="(opt) => streets.find((x) => x.id == opt)?
                                 $i18n.locale == 'ar' ? streets.find((x) => x.id == opt).name : streets.find((x) => x.id == opt).name_e
                                 :null
                            "
                        >
                        </multiselect>
                        <template v-if="errors.street_id">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.street_id"
                                :key="index"
                            >{{ errorMessage }}</ErrorMessage
                            >
                        </template>
                    </div>
                </div>
                <div class="col-md-3" v-if="isVisible('lng')">
                    <div class="form-group">
                        <label class="control-label">
                            {{ getCompanyKey("boardRent_panel_lng") }}
                            <span v-if="isRequired('lng')" class="text-danger">*</span>
                        </label>
                        <input
                            type="number"
                            class="form-control"
                            step="0.00000000000001"
                            v-model="$v.create.lng.$model"
                            :class="{
                                                'is-invalid': $v.create.lng.$error || errors.lng,
                                                'is-valid': !$v.create.lng.$invalid && !errors.lng,
                                              }"
                        />
                        <template v-if="errors.lng">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.lng"
                                :key="index"
                            >{{ errorMessage }}</ErrorMessage
                            >
                        </template>
                    </div>
                </div>
                <div class="col-md-3" v-if="isVisible('lat')">
                    <div class="form-group">
                        <label class="control-label">
                            {{ getCompanyKey("boardRent_panel_lat") }}
                            <span v-if="isRequired('lat')" class="text-danger">*</span>
                        </label>
                        <input
                            type="number"
                            class="form-control"
                            data-create="9"
                            step="0.00000000000001"
                            v-model="$v.create.lat.$model"
                            :class="{
                                                'is-invalid': $v.create.lat.$error || errors.lat,
                                                'is-valid': !$v.create.lat.$invalid && !errors.lat,
                                              }"
                        />
                        <template v-if="errors.lat">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.lat"
                                :key="index"
                            >{{ errorMessage }}</ErrorMessage
                            >
                        </template>
                    </div>
                </div>
                <div v-if="isVisible('lat') || isVisible('lng')" class="col-md-3 d-flex align-items-center mt-3">
                    <button
                        class="btn btn-primary"
                        type="button"
                        @click="getLocation"
                    >
                        {{ $t('general.location') }}
                    </button>
                </div>
            </div>
            <hr style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" v-if="isVisible('price')" />
            <div class="row">
                <div class="col-md-3" v-if="isVisible('price')">
                    <div class="form-group">
                        <label class="control-label">
                            {{ getCompanyKey("boardRent_panel_day") }}
                            <span v-if="isRequired('price')" class="text-danger">*</span>
                        </label>
                        <input
                            type="number"
                            class="form-control"
                            step="0.01"
                            v-model="$v.create.price.day.$model"
                            :class="{
                                                'is-invalid': $v.create.price.day.$error,
                                                'is-valid': !$v.create.price.day.$invalid,
                                              }"
                        />
                    </div>
                </div>
                <div class="col-md-3" v-if="isVisible('price')">
                    <div class="form-group">
                        <label class="control-label">
                            {{ getCompanyKey("boardRent_panel_week") }}
                            <span class="text-danger">*</span>
                        </label>
                        <input
                            type="number"
                            class="form-control"
                            data-create="9"
                            step="0.01"
                            v-model="$v.create.price.week.$model"
                            :class="{
                                                'is-invalid': $v.create.price.week.$error,
                                                'is-valid': !$v.create.price.week.$invalid
                                              }"
                        />
                    </div>
                </div>
                <div class="col-md-3" v-if="isVisible('price')">
                    <div class="form-group">
                        <label class="control-label">
                            {{ getCompanyKey("boardRent_panel_month") }}
                            <span class="text-danger">*</span>
                        </label>
                        <input
                            type="number"
                            class="form-control"
                            step="0.01"
                            v-model="$v.create.price.month.$model"
                            :class="{
                                                'is-invalid': $v.create.price.month.$error,
                                                'is-valid': !$v.create.price.month.$invalid
                                              }"
                        />
                    </div>
                </div>
                <div class="col-md-3" v-if="isVisible('price')">
                    <div class="form-group">
                        <label class="control-label">
                            {{ getCompanyKey("boardRent_panel_quarter_year") }}
                            <span class="text-danger">*</span>
                        </label>
                        <input
                            type="number"
                            class="form-control"
                            data-create="9"
                            step="0.01"
                            v-model="$v.create.price.quarter_year.$model"
                            :class="{
                                                'is-invalid': $v.create.price.quarter_year.$error,
                                                'is-valid': !$v.create.price.quarter_year.$invalid,
                                              }"
                        />
                    </div>
                </div>
                <div class="col-md-3" v-if="isVisible('price')">
                    <div class="form-group">
                        <label class="control-label">
                            {{ getCompanyKey("boardRent_panel_half_year") }}
                            <span class="text-danger">*</span>
                        </label>
                        <input
                            type="number"
                            class="form-control"
                            step="0.01"
                            v-model="$v.create.price.half_year.$model"
                            :class="{
                                                'is-invalid': $v.create.price.half_year.$error,
                                                'is-valid': !$v.create.price.half_year.$invalid
                                              }"
                        />
                    </div>
                </div>
                <div class="col-md-3" v-if="isVisible('price')">
                    <div class="form-group">
                        <label class="control-label">
                            {{ getCompanyKey("boardRent_panel_year") }}
                            <span class="text-danger">*</span>
                        </label>
                        <input
                            type="number"
                            class="form-control"
                            step="0.01"
                            v-model="$v.create.price.year.$model"
                            :class="{
                                                'is-invalid': $v.create.price.year.$error,
                                                'is-valid': !$v.create.price.year.$invalid,
                                              }"
                        />
                    </div>
                </div>
            </div>
            <hr style="margin: 10px 0 !important;border-top: 1px solid rgb(141 163 159 / 42%)" v-if="isVisible('note')||isVisible('description')||isVisible('description_e')" />
            <div class="row">
                <div class="col-md-6" v-if="isVisible('note')">
                    <div class="form-group">
                        <label for="field-6" class="control-label">
                            {{ getCompanyKey("boardRent_panel_note") }}
                            <span v-if="isRequired('note')" class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            class="form-control"
                            v-model="$v.create.note.$model"
                            :class="{
                                                    'is-invalid': $v.create.note.$error || errors.note,
                                                    'is-valid': !$v.create.note.$invalid && !errors.note,
                                                  }"
                            id="field-6"
                        />
                        <template v-if="errors.note">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.note"
                                :key="index"
                            >{{ errorMessage }}</ErrorMessage
                            >
                        </template>
                    </div>
                </div>
                <div class="col-md-6" v-if="isVisible('note')"></div>
                <div class="col-md-6" v-if="isVisible('description')">
                    <div class="form-group">
                        <label class="mr-2">
                            {{ getCompanyKey('boardRent_panel_description') }}
                            <span v-if="isRequired('description')" class="text-danger">*</span>
                        </label>
                        <textarea
                            @input="arabicValueDescription(create.description)"
                            v-model="$v.create.description.$model"
                            class="form-control"
                            :class="{
                                                    'is-invalid': $v.create.description.$error || errors.description,
                                                    'is-valid': !$v.create.description.$invalid && !errors.description,
                                                  }"
                            :maxlength="1000" rows="5"
                        ></textarea>
                        <template v-if="errors.description">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.description"
                                :key="index"
                            >{{ errorMessage }}</ErrorMessage
                            >
                        </template>
                    </div>
                </div>
                <div class="col-md-6"  v-if="isVisible('description_e')">
                    <div class="form-group">
                        <label class="mr-2">
                            {{ getCompanyKey('boardRent_panel_description_e') }}
                            <span  v-if="isRequired('description_e')" class="text-danger">*</span>
                        </label>
                        <textarea
                            @input="englishValueDescription(create.description_e)"
                            v-model="$v.create.description_e.$model" class="form-control"
                            :maxlength="1000" rows="5"
                            :class="{
                                                    'is-invalid': $v.create.description_e.$error || errors.description_e,
                                                    'is-valid': !$v.create.description_e.$invalid && !errors.description_e,
                                                  }"
                        ></textarea>
                        <template v-if="errors.description_e">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.description_e"
                                :key="index"
                            >{{ errorMessage }}</ErrorMessage
                            >
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
import Multiselect from "vue-multiselect";
import ErrorMessage from "../../widgets/errorMessage";
import Governate from "../general/governate";
import Avnue from "../general/avenue";
import Street from "../general/street";
import City from "../general/city";
import {arabicValue,englishValue} from "../../../helper/langTransform";
import loader from "../../general/loader";
import Category from "../general/category";
import UnitStatus from "./unitStatus";
import {decimal, maxLength, minLength, minValue, requiredIf} from "vuelidate/lib/validators";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import successError from "../../../helper/mixin/success&error";
import adminApi from "../../../api/adminAxios";

export default {
    name: "panel",
    mixins: [transMixinComp,successError],
    props: {
        id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/boards-rent/panels'}
    },
    components: {
        Multiselect,
        ErrorMessage,
        Governate,
        Avnue,
        Street,
        City,
        loader,
        Category,
        UnitStatus
    },
    data() {
        return {
            isCustom: false,
            fields: [],
            isLoader: false,
            unit_statuses: [],
            departments: [],
            categories: [],
            countries: [],
            governorates: [],
            cities: [],
            avenues: [],
            streets: [],
            faces: ['A','B','Multi','One-Face'],
            create: {
                name: "",
                name_e: "",
                description: "",
                description_e: "",
                price: {
                    day: .00,
                    week: .00,
                    month: .00,
                    quarter_year: .00,
                    half_year: .00,
                    year: .00,
                },
                code: "",
                new_code: "",
                size: "",
                note: "",
                face: "A",
                unit_status_id: null,
                category_id: null,
                country_id: 1,
                governorate_id: null,
                city_id: null,
                avenue_id: null,
                street_id: null,
                lat: 0,
                lng: 0,
            },
            company_id: null,
            errors: {},
            current_page: 1,
            is_disabled: false,
        };
    },
    validations: {
        create: {
            name: { required: requiredIf(function (model) {
                    return this.isRequired("name");
                }), minLength: minLength(2), maxLength: maxLength(255) },
            name_e: { required: requiredIf(function (model) {
                    return this.isRequired("name_e");
                }), minLength: minLength(2), maxLength: maxLength(255) },
            description: {  required: requiredIf(function (model) {
                    return this.isRequired("description");
                }),maxLength: maxLength(1000) },
            description_e: {  required: requiredIf(function (model) {
                    return this.isRequired("description_e");
                }),maxLength: maxLength(1000) },
            price: {
                required: requiredIf(function (model) {
                    return this.isRequired("price");
                }),
                day: { required: requiredIf(function (model) {
                        return this.isRequired("price");
                    }) , minValue: minValue(.01)},
                week: {  minValue: minValue(.00)},
                month: {  minValue: minValue(.00)},
                quarter_year: {   minValue: minValue(.00)},
                half_year: {  minValue: minValue(.00)},
                year: { decimal , minValue: minValue(.00)},
            },
            code: { required: requiredIf(function (model) {
                    return this.isRequired("code");
                }), minLength: minLength(1), maxLength: maxLength(255) },
            new_code: {  required: requiredIf(function (model) {
                    return this.isRequired("new_code");
                }),minLength: minLength(1), maxLength: maxLength(255) },
            size: { required: requiredIf(function (model) {
                    return this.isRequired("size");
                }) ,maxLength: maxLength(255) },
            note: {  required: requiredIf(function (model) {
                    return this.isRequired("note");
                }),maxLength: maxLength(255) },
            face: { required: requiredIf(function (model) {
                    return this.isRequired("face");
                }) },
            unit_status_id: { required: requiredIf(function (model) {
                    return this.isRequired("unit_status_id");
                }) },
            category_id: { required: requiredIf(function (model) {
                    return this.isRequired("category_id");
                }) },
            governorate_id: { required: requiredIf(function (model) {
                    return this.isRequired("governorate_id");
                }) },
            city_id: { required: requiredIf(function (model) {
                    return this.isRequired("city_id");
                }) },
            avenue_id: { required: requiredIf(function (model) {
                    return this.isRequired("avenue_id");
                }) },
            street_id: { required: requiredIf(function (model) {
                    return this.isRequired("street_id");
                }) },
            lat: { required: requiredIf(function (model) {
                    return this.isRequired("lat");
                }) , decimal },
            lng: { required: requiredIf(function (model) {
                    return this.isRequired("lng");
                }) , decimal },
        }
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
    },
    methods: {
        async getCustomTableFields() {
            this.isCustom = true;
            await adminApi
                .get(`/customTable/table-columns/boards_rent_panels`)
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
        arabicValueDescription(txt){
            this.create.description = arabicValue(txt);
        },
        englishValueDescription(txt){
            this.create.description_e = englishValue(txt);
        },
        defaultData(edit = null){
            this.create = {
                name: "",
                name_e: "",
                description: "",
                description_e: "",
                price: {
                    day: 0,
                    week: 0,
                    month: 0,
                    quarter_year: 0,
                    half_year: 0,
                    year: 0,
                },
                code: "",
                new_code: "",
                size: "",
                note: "",
                face: "A",
                unit_status_id: null,
                category_id: null,
                country_id: 1,
                governorate_id: null,
                city_id: null,
                avenue_id: null,
                street_id: null,
                lat: 0,
                lng: 0,
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.is_disabled = false;
            this.errors = {};
        },
        resetModalHidden() {
            this.defaultData();
            this.$bvModal.hide(this.id);
        },
        resetModal() {
            this.defaultData();
            setTimeout( async () => {
                if(this.type != 'edit'){
                    if(!this.isPage)  await this.getCustomTableFields();
                    if(this.isVisible('governorate_id'))   this.getGovernorate();
                    if(this.isVisible('unit_status_id'))   this.getUnitStatus();
                    if(this.isVisible('category_id'))   this.getCategory();
                }else {
                    if(this.idObjEdit.dataObj){
                        let panels = this.idObjEdit.dataObj;
                        this.errors = {};
                        this.create.name = panels.name;
                        this.create.name_e = panels.name_e;
                        this.create.description = panels.description;
                        this.create.description_e = panels.description_e;
                        this.create.lng = panels.lng;
                        this.create.lat = panels.lat;
                        this.create.code = panels.code;
                        this.create.new_code = panels.new_code;
                        this.create.face = panels.face;
                        if(this.isVisible('category_id'))  this.getCategory();
                        this.create.category_id = panels.category_id;
                        if(this.isVisible('unit_status_id'))  this.getUnitStatus();
                        this.create.unit_status_id = panels.unit_status_id;
                        this.create.country_id = 1;
                        if(this.isVisible('governorate_id'))  this.getGovernorate();
                        this.create.governorate_id = panels.governorate_id;
                        if(this.isVisible('city_id'))  this.getCity();
                        this.create.city_id = panels.city_id;
                        if(this.isVisible('avenue_id'))  this.getAvnue();
                        this.create.avenue_id = panels.avenue_id;
                        if(this.isVisible('street_id'))  this.getStreet();
                        this.create.street_id = panels.street_id;
                        this.create.note = panels.note;
                        this.create.size = panels.size;
                        if(panels.price){
                            this.create.price.day = panels.price.day;
                            this.create.price.week = panels.price.week;
                            this.create.price.month = panels.price.month;
                            this.create.price.quarter_year = panels.price.quarter_year;
                            this.create.price.half_year = panels.price.half_year;
                            this.create.price.year = panels.price.year;
                        }

                    }
                }
            },50);
        },
        resetForm() {
            this.defaultData();
        },
        AddSubmit() {
            this.create.name = `${this.create.code} - ${this.create.face} - ${this.create.category_id?this.categories.find( e => e.id == this.create.category_id).name:null} - ${this.create.governorate_id?this.governorates.find( e => e.id == this.create.governorate_id).name:''}`;
            this.create.name_e =  `${this.create.code} - ${this.create.face} - ${this.create.category_id?this.categories.find( e => e.id == this.create.category_id).name_e:null} - ${this.create.governorate_id?this.governorates.find( e => e.id == this.create.governorate_id).name_e:null}`;
            if (!this.create.description) {this.create.description = this.create.description_e;}
            if (!this.create.description_e) {this.create.description_e = this.create.description;}

            this.$v.create.$touch();

            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};

                if(this.type != 'edit'){
                    adminApi
                        .post(this.url, {
                            ...this.create, company_id: this.$store.getters["auth/company_id"],
                        })
                        .then((res) => {
                            this.is_disabled = true;
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
                        .put(`${this.url}/${this.idObjEdit.idEdit}`, {
                            ...this.create,
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
        getLocation() {
            if (navigator.geolocation) {
                return  navigator.geolocation.getCurrentPosition(this.showPosition);
            }
        },
        showPosition(position) {
            this.create.lat = position.coords.latitude;
            this.create.lng = position.coords.longitude;
        },
        showUnitStatusModal() {
            if (this.create.unit_status_id == 0) {
                this.$bvModal.show("unitStatus-create-panel");
                this.create.unit_status_id = null;
            }
        },
        showeCategoryModal() {
            if (this.create.category_id == 0) {
                this.$bvModal.show("category-create-panel");
                this.create.category_id = null;
            }
        },
        showGovernorateModal() {
            if (this.create.governorate_id == 0) {
                this.$bvModal.show("governorate-create-panel");
                this.create.governorate_id = null;
            }else {
                this.getCity();
            }
        },
        showCityModal() {
            if (this.create.city_id == 0) {
                this.$bvModal.show("city-create-panel");
                this.create.city_id = null;
            }else {
                this.getAvnue();
            }
        },
        showAvenueModal() {
            if (this.create.avenue_id == 0) {
                this.$bvModal.show("avnue-create-panel");
                this.create.avenue_id = null;
            }else {
                this.getStreet();
            }
        },
        showStreetModal() {
            if (this.create.street_id == 0) {
                this.$bvModal.show("street-create-panel");
                this.create.street_id = null;
            }
        },
        getUnitStatus(){
            this.isLoader = true;

            adminApi
                .get(
                    `/boards-rent/unit_statuses`
                )
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف حاله الوحده", name_e: "Add Unit Status" });
                    this.unit_statuses = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        getCategory() {
            this.isLoader = true;

             adminApi
                .get(
                    `/categories`
                )
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف الفئه", name_e: "Add Category" });
                    this.categories = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        getCountry() {
            this.isLoader = true;

            adminApi
                .get(
                    `/countries`
                )
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف الدوله", name_e: "Add Country" });
                    this.countries = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        getGovernorate() {
            this.create.governorate_id = null;
            this.create.city_id = null;
            this.create.avenue_id = null;
            this.create.street_id = null;
            this.governorates = [];this.cities = [];this.avenues = [];
            this.streets = [];

            adminApi
                .get(`/governorates?columns[0]=country.id&search=${1}`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف المحافظه", name_e: "Add Governorate" });
                    this.governorates = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                });
        },
        getCity() {
            this.isLoader = true;
            let governorate = this.create.governorate_id;
            this.create.city_id = null;
            this.create.avenue_id = null;
            this.create.street_id = null;
            this.cities = [];this.avenues = [];this.streets = [];
            adminApi
                .get(`/cities?columns[0]=governorate.id&search=${governorate}`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف المدينه", name_e: "Add City" });
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
            let city = this.create.city_id;
            this.create.avenue_id = null;
            this.create.street_id = null;
            this.avenues = [];this.streets = [];

             adminApi
                .get(`/avenues?columns[0]=city.id&search=${city}`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف المنطقه", name_e: "Add Avenue" });
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
            let avenue = this.create.avenue_id;
            this.create.street_id = null;
            this.streets = [];

             adminApi
                .get(`/streets?columns[0]=avenue.id&search=${avenue}`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف الشارع", name_e: "Add Street" });
                    this.streets = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
    },
}
</script>

<style scoped>
    form {
        position: relative;
    }
</style>
