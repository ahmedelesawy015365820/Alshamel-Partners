<template>
    <!--  create   -->
    <b-modal
         :id="id"
         :title="type != 'edit'?getCompanyKey('document_create_form'):getCompanyKey('document_edit_form')"
         title-class="font-18"
         body-class="p-4"  :hide-footer="true"
         @hidden="resetModalHidden"
         @show="resetModal"
         size="lg"
    >
        <h5 v-if="type == 'edit'">{{ getHeader() }}</h5>
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
            <b-tabs>
                <b-tab :title="$t('general.DataEntry')" active>
                    <div class="row">
                        <div class="col-md-6" v-if="isVisible('name')">
                            <div class="form-group">
                                <label for="field-u-1" class="control-label">
                                    {{ getCompanyKey("document_name_ar") }}
                                    <span v-if="isRequired('name')"
                                          class="text-danger">*</span>
                                </label>
                                <div dir="rtl">
                                    <input type="text"
                                           :disabled="type == 'edit'"
                                           class="form-control arabicInput"
                                           v-model="$v.create.name.$model"
                                           @keyup="arabicValueName(create.name)"
                                           :class="{
                                                'is-invalid': $v.create.name.$error || errors.name,
                                                'is-valid': !$v.create.name.$invalid && !errors.name,
                                           }"
                                           id="field-u-1"
                                    />
                                </div>
                                <div v-if="!$v.create.name.minLength"
                                     class="invalid-feedback">
                                    {{ $t("general.Itmustbeatleast") }}
                                    {{ $v.create.name.$params.minLength.min }}
                                    {{ $t("general.letters") }}
                                </div>
                                <div v-if="!$v.create.name.maxLength"
                                     class="invalid-feedback">
                                    {{ $t("general.Itmustbeatmost") }}
                                    {{ $v.create.name.$params.maxLength.max }}
                                    {{ $t("general.letters") }}
                                </div>
                                <template v-if="errors.name">
                                    <ErrorMessage
                                        v-for="(errorMessage, index) in errors.name"
                                        :key="index">{{ errorMessage }}
                                    </ErrorMessage>
                                </template>
                            </div>
                        </div>
                        <div class="col-md-6" v-if="isVisible('name_e')">
                            <div class="form-group">
                                <label for="field-u-2" class="control-label">
                                    {{ getCompanyKey("document_name_en") }}
                                    <span v-if="isRequired('name_e')"
                                          class="text-danger">*</span>
                                </label>
                                <div dir="ltr">
                                    <input type="text"
                                           :disabled="type == 'edit'"
                                           @keyup="englishValueName(create.name_e)"
                                           class="form-control englishInput"
                                           v-model="$v.create.name_e.$model"
                                           :class="{
                                                'is-invalid':
                                                    $v.create.name_e.$error || errors.name_e,
                                                'is-valid':
                                                    !$v.create.name_e.$invalid && !errors.name_e,
                                            }" id="field-u-2"
                                    />
                                </div>
                                <div v-if="!$v.create.name_e.minLength"
                                     class="invalid-feedback">
                                    {{ $t("general.Itmustbeatleast") }}
                                    {{ $v.create.name_e.$params.minLength.min }}
                                    {{ $t("general.letters") }}
                                </div>
                                <div v-if="!$v.create.name_e.maxLength"
                                     class="invalid-feedback">
                                    {{ $t("general.Itmustbeatmost") }}
                                    {{ $v.create.name_e.$params.maxLength.max }}
                                    {{ $t("general.letters") }}
                                </div>
                                <div v-if="!$v.create.name_e.alphaEnglish"
                                     class="invalid-feedback">
                                    {{ $t("general.alphaEnglish") }}
                                </div>
                                <template v-if="errors.name_e">
                                    <ErrorMessage
                                        v-for="(errorMessage, index) in errors.name_e"
                                        :key="index">{{ errorMessage }}
                                    </ErrorMessage>
                                </template>
                            </div>
                        </div>
                        <div class="col-md-6" v-if="isVisible('required')">
                            <div class="form-group">
                                <label class="mr-2">
                                    {{ getCompanyKey("document_required") }}
                                    <span v-if="isRequired('required')"
                                          class="text-danger">*</span>
                                </label>
                                <b-form-group :class="{
                                        'is-invalid':$v.create.required.$error || errors.required,
                                        'is-valid':!$v.create.required.$invalid && !errors.required,
                                    }"
                                >
                                    <b-form-radio class="d-inline-block" v-model="$v.create.required.$model" name="some-radios" value="2">
                                        {{$t("general.yesAndNo")}}
                                    </b-form-radio>
                                    <b-form-radio class="d-inline-block" v-model="$v.create.required.$model" name="some-radios" value="1">
                                        {{$t("general.Yes")}}
                                    </b-form-radio>
                                    <b-form-radio class="d-inline-block m-1" v-model="$v.create.required.$model" name="some-radios" value="0">
                                        {{ $t("general.No") }}
                                    </b-form-radio>
                                </b-form-group>
                                <template v-if="errors.required">
                                    <ErrorMessage
                                        v-for="(errorMessage, index) in errors.required"
                                        :key="index">{{ errorMessage }}
                                    </ErrorMessage>
                                </template>
                            </div>
                        </div>
                        <div class="col-md-6" v-if="isVisible('need_approve')">
                            <div class="form-group">
                                <label class="mr-2">
                                    {{ getCompanyKey("document_need_approve") }}
                                    <span v-if="isRequired('need_approve')"
                                          class="text-danger">*</span>
                                </label>
                                <b-form-group :class="{
                                                'is-invalid':
                                                    $v.create.need_approve.$error || errors.need_approve,
                                                'is-valid':
                                                    !$v.create.need_approve.$invalid && !errors.need_approve,
                                            }">
                                    <b-form-radio class="d-inline-block"
                                                  v-model="$v.create.need_approve.$model"
                                                  name="need_approve" value="1">{{
                                            $t("general.Yes")
                                        }}</b-form-radio>
                                    <b-form-radio class="d-inline-block m-1"
                                                  v-model="$v.create.need_approve.$model"
                                                  name="need_approve" value="0">{{
                                            $t("general.No")
                                        }}</b-form-radio>
                                </b-form-group>
                                <template v-if="errors.need_approve">
                                    <ErrorMessage
                                        v-for="(errorMessage, index) in errors.need_approve"
                                        :key="index">{{ errorMessage }}
                                    </ErrorMessage>
                                </template>
                            </div>
                        </div>
                        <div class="col-md-6" v-if="isVisible('is_copy')">
                            <div class="form-group">
                                <label class="mr-2">
                                    {{ getCompanyKey("document_need_is_copy") }}
                                    <span v-if="isRequired('is_copy')"
                                          class="text-danger">*</span>
                                </label>
                                <b-form-group :class="{
                                            'is-invalid': $v.create.is_copy.$error || errors.is_copy,
                                            'is-valid': !$v.create.is_copy.$invalid && !errors.is_copy,
                                        }">
                                    <b-form-radio class="d-inline-block" v-model="$v.create.is_copy.$model" name="is_copy" value="2">
                                        {{$t("general.yesAndNo")}}
                                    </b-form-radio>
                                    <b-form-radio class="d-inline-block" v-model="$v.create.is_copy.$model" name="is_copy" value="1">
                                        {{ $t("general.Yes") }}
                                    </b-form-radio>
                                    <b-form-radio class="d-inline-block m-1" v-model="$v.create.is_copy.$model" name="is_copy" value="0">
                                        {{$t("general.No") }}
                                    </b-form-radio>
                                </b-form-group>
                                <template v-if="errors.is_copy">
                                    <ErrorMessage
                                        v-for="(errorMessage, index) in errors.is_copy"
                                        :key="index">{{ errorMessage }}
                                    </ErrorMessage>
                                </template>
                            </div>
                        </div>
                        <div class="col-md-6" v-if="isVisible('is_partially')">
                            <div class="form-group">
                                <label class="mr-2">
                                    {{ getCompanyKey("document_need_is_partially") }}
                                    <span v-if="isRequired('is_partially')" class="text-danger">*</span>
                                </label>
                                <b-form-group :class="{
                                            'is-invalid': $v.create.is_partially.$error || errors.is_partially,
                                            'is-valid': !$v.create.is_partially.$invalid && !errors.is_partially,
                                        }">
                                    <b-form-radio class="d-inline-block" v-model="$v.create.is_partially.$model" name="is_partially" value="1">
                                        {{ $t("general.Yes") }}
                                    </b-form-radio>
                                    <b-form-radio class="d-inline-block m-1" v-model="$v.create.is_partially.$model" name="is_partially" value="0">
                                        {{$t("general.No") }}
                                    </b-form-radio>
                                </b-form-group>
                                <template v-if="errors.is_partially">
                                    <ErrorMessage v-for="(errorMessage, index) in errors.is_partially" :key="index">
                                        {{ errorMessage }}
                                    </ErrorMessage>
                                </template>
                            </div>
                        </div>
                        <div class="col-md-6" v-if="isVisible('document_detail_type')">
                            <div class="form-group">
                                <label class="mr-2">
                                    {{ getCompanyKey("document_detail_type") }}
                                    <span v-if="isRequired('document_detail_type')"
                                          class="text-danger">*</span>
                                </label>
                                <select
                                    class="custom-select"
                                    v-model="create.document_detail_type"
                                >
                                    <option value="normal">{{ $t('general.normal') }}</option>
                                    <option value="rent_unit">{{ $t('general.rent_unit') }}</option>
                                    <option value="sell_unit">{{ $t('general.sell_unit') }}</option>
                                    <option value="board_rent">{{ $t('general.board_rent') }}</option>
                                    <option value="document_money">{{ $t('general.document_money') }}</option>
                                    <option value="booking">{{ $t('general.booking') }}</option>
                                </select>
                                <template v-if="errors.document_detail_type">
                                    <ErrorMessage
                                        v-for="(errorMessage, index) in errors.document_detail_type"
                                        :key="index">{{ errorMessage }}
                                    </ErrorMessage>
                                </template>
                            </div>
                        </div>
                        <div class="col-md-6" v-if="isVisible('is_break')">
                            <div class="form-group">
                                <label class="mr-2">
                                    {{ getCompanyKey("document_need_is_break") }}
                                    <span v-if="isRequired('is_break')" class="text-danger">*</span>
                                </label>
                                <b-form-group :class="{
                                            'is-invalid': $v.create.is_break.$error || errors.is_break,
                                            'is-valid': !$v.create.is_break.$invalid && !errors.is_break,
                                        }">
                                    <b-form-radio class="d-inline-block" v-model="$v.create.is_break.$model" name="is_break" value="2">
                                        {{$t("general.yesAndNo")}}
                                    </b-form-radio>
                                    <b-form-radio class="d-inline-block" v-model="$v.create.is_break.$model" name="is_break" value="1">
                                        {{ $t("general.Yes") }}
                                    </b-form-radio>
                                    <b-form-radio class="d-inline-block m-1" v-model="$v.create.is_break.$model" name="is_break" value="0">
                                        {{$t("general.No") }}
                                    </b-form-radio>
                                </b-form-group>
                                <template v-if="errors.is_break">
                                    <ErrorMessage v-for="(errorMessage, index) in errors.is_break" :key="index">
                                        {{ errorMessage }}
                                    </ErrorMessage>
                                </template>
                            </div>
                        </div>
                        <div class="col-md-6" v-if="isVisible('document_module_type_id')">
                            <div class="form-group">
                                <label class="control-label">
                                    {{ getCompanyKey("document_document_module_type") }}
                                </label>
                                <multiselect
                                     v-model="create.document_module_type_id"
                                     :options="moduleTypes.map((type) => type.id)"
                                     :custom-label="
                                        (opt) => moduleTypes.find((x) => x.id == opt)
                                        ? $i18n.locale == 'ar'
                                        ? moduleTypes.find((x) => x.id == opt).name
                                        : moduleTypes.find((x) => x.id == opt).name_e: null
                                    "
                                >
                                </multiselect>
                                <template v-if="errors.document_module_type_id">
                                    <ErrorMessage v-for="(errorMessage, index) in errors.document_module_type_id" :key="index">
                                        {{errorMessage}}
                                    </ErrorMessage>
                                </template>
                            </div>
                        </div>
                    </div>
                </b-tab>
                <b-tab :disabled="!document_id" :title="$t('general.effects')">
                    <div  class="d-flex justify-content-end">
                        <b-button variant="success" v-if="!isLoader"
                            class="mx-1" @click.prevent="addEffects(type)"
                        >
                            {{ type != 'edit'? $t("general.Add"): $t("general.edit") }}
                        </b-button>
                        <b-button variant="success" class="mx-1" disabled v-else>
                            <b-spinner small></b-spinner>
                            <span class="sr-only">{{ $t("login.Loading")}}...</span>
                        </b-button>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>
                                    {{ $t("general.cash") }}
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="custom-select"
                                        :disabled="parseInt(create.relation_voucher_header) == 1 ? true : false"
                                        v-model="create_effects.cash">
                                    <option value="0" selected>{{
                                            $t("general.noeffect") }}...</option>
                                    <option value="1">{{ $t("general.increase") }}
                                    </option>
                                    <option value="-1">{{ $t("general.decrease") }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>
                                    {{ $t("general.client?") }}
                                    <span class="text-danger">*</span>
                                </label>
                                <select :disabled="parseInt(create.relation_voucher_header) == 1 ? true : false" class="custom-select" v-model="create_effects.customer">
                                    <option value="0" selected>{{$t("general.noeffect") }}...</option>
                                    <option value="1">{{ $t("general.increase") }}</option>
                                    <option value="-1">{{ $t("general.decrease") }}</option>
                                </select>
                            </div>
                        </div>
<!--                        <div class="col-md-4">-->
<!--                            <div class="form-group">-->
<!--                                <label>-->
<!--                                    {{ $t("general.clientCredit") }}-->
<!--                                    <span class="text-danger">*</span>-->
<!--                                </label>-->
<!--                                <select class="custom-select"-->
<!--                                        v-model="create_effects.supplier">-->
<!--                                    <option value="0" selected>{{-->
<!--                                            $t("general.noeffect") }}...</option>-->
<!--                                    <option value="1">{{ $t("general.increase") }}-->
<!--                                    </option>-->
<!--                                    <option value="-1">{{ $t("general.decrease") }}-->
<!--                                    </option>-->
<!--                                </select>-->
<!--                            </div>-->
<!--                        </div>-->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>
                                    {{ $t("general.item_quantity") }}
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="custom-select"
                                        v-model="create_effects.item_quantity">
                                    <option value="0" selected>{{
                                            $t("general.noeffect") }}...</option>
                                    <option value="1">{{ $t("general.increase") }}
                                    </option>
                                    <option value="-1">{{ $t("general.decrease") }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>
                                    {{ $t("general.reserved_quantity") }}
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="custom-select"
                                        v-model="create_effects.reserved_quantity">
                                    <option value="0" selected>{{
                                            $t("general.noeffect") }}...</option>
                                    <option value="1">{{ $t("general.increase") }}
                                    </option>
                                    <option value="-1">{{ $t("general.decrease") }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>
                                    {{ $t("general.ordered_quantity") }}
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="custom-select"
                                        v-model="create_effects.ordered_quantity">
                                    <option value="0" selected>{{
                                            $t("general.noeffect") }}...</option>
                                    <option value="1">{{ $t("general.increase") }}
                                    </option>
                                    <option value="-1">{{ $t("general.decrease") }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>
                                    {{ $t("general.available") }}
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="custom-select"
                                        v-model="create_effects.available">
                                    <option value="0" selected>{{$t("general.noeffect") }}...</option>
                                    <option value="1">{{ $t("general.increase") }}
                                    </option>
                                    <option value="-1">{{ $t("general.decrease") }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div v-if="type == 'edit'" class="col-md-4">
                            <div class="form-group">
                                <label>
                                    {{ $t("general.unrealized_revenue") }}
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="custom-select"
                                        v-model="create_effects.unrealized_revenue">
                                    <option value="0" selected>{{ $t("general.noeffect") }}...</option>
                                    <option value="1">{{ $t("general.increase") }}</option>
                                    <option value="-1">{{ $t("general.decrease") }}</option>
                                </select>
                            </div>
                        </div>
                        <div v-if="type == 'edit'" class="col-md-4">
                            <div class="form-group">
                                <label>
                                    {{ $t("general.revenue") }}
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="custom-select"
                                        v-model="create_effects.revenue">
                                    <option value="0" selected>{{ $t("general.noeffect") }}...</option>
                                    <option value="1">{{ $t("general.increase") }}</option>
                                    <option value="-1">{{ $t("general.decrease") }}</option>
                                </select>
                            </div>
                        </div>
                        <div v-if="type == 'edit'" class="col-md-4">
                            <div class="form-group">
                                <label>
                                    {{ $t("general.unrealized_commission") }}
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="custom-select"
                                        v-model="create_effects.unrealized_commission">
                                    <option value="0" selected>{{ $t("general.noeffect") }}...</option>
                                    <option value="1">{{ $t("general.increase") }}</option>
                                    <option value="-1">{{ $t("general.decrease") }}</option>
                                </select>
                            </div>
                        </div>
                        <div v-if="type == 'edit'" class="col-md-4">
                            <div class="form-group">
                                <label>
                                    {{ $t("general.commission") }}
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="custom-select"
                                        v-model="create_effects.commission">
                                    <option value="0" selected>{{ $t("general.noeffect") }}...</option>
                                    <option value="1">{{ $t("general.increase") }}</option>
                                    <option value="-1">{{ $t("general.decrease") }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </b-tab>
                <!-- <b-tab :title="$t('general.branch')">
                    <div class="d-flex justify-content-end">
                        <b-button variant="success" v-if="!isLoader" type="submit"
                            class="mx-1" @click.prevent="addBranchForm('update')">
                            {{ $t("general.Add") }}
                        </b-button>
                        <b-button variant="success" class="mx-1" disabled v-else>
                            <b-spinner small></b-spinner>
                            <span class="sr-only">{{ $t("login.Loading")
                            }}...</span>
                        </b-button>
                    </div>
                    <div class="row">
                        <div class="col-md-6 position-relative">
                            <div class="form-group">
                                <label class="my-1 mr-2">{{
                                    $t("general.branch")
                                }}</label>
                                <span class="text-danger">*</span>
                                <multiselect
                                    v-model="$v.branchFormEdit.branche_id.$model"
                                    :options="branches.map((type) => type.id)"
                                    :custom-label="
                                        (opt) =>
                                            $i18n.locale == 'ar'
                                                ? branches.find((x) => x.id == opt).name
                                                : branches.find((x) => x.id == opt).name_e"
                                    :class="{ 'is-invalid': $v.branchFormEdit.branche_id.$error }">
                                </multiselect>
                                <div v-if="!$v.branchFormEdit.branche_id.required"
                                    class="invalid-feedback">
                                    {{ $t("general.fieldIsRequired") }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 position-relative">
                            <div class="form-group">
                                <label class="my-1 mr-2">{{
                                    $t("general.serial")
                                }}</label>
                                <span class="text-danger">*</span>
                                <multiselect
                                    v-model="$v.branchFormEdit.serial_id.$model"
                                    :options="serials.map((type) => type.id)"
                                    :custom-label="
                                        (opt) => serials.find((type) => type.id == opt).start_no"
                                    :class="{ 'is-invalid': $v.branchFormEdit.serial_id.$error }">
                                </multiselect>
                                <div v-if="!$v.branchFormEdit.serial_id.required"
                                    class="invalid-feedback">
                                    {{ $t("general.fieldIsRequired") }}
                                </div>
                            </div>
                        </div>
                    </div>
                </b-tab> -->
                <b-tab :disabled="!document_id" :title="$t('general.linkedWithDocs')">
                    <div class="d-flex justify-content-end">
                        <b-button
                            variant="success" v-if="!isLoader"
                            class="mx-1" @click.prevent="addDocs(type)"
                        >
                            {{ type != 'edit'? $t("general.Add"): $t("general.edit") }}
                        </b-button>
                        <b-button variant="success" class="mx-1" disabled v-else>
                            <b-spinner small></b-spinner>
                            <span class="sr-only">{{ $t("login.Loading")
                                }}...</span>
                        </b-button>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">
                                    {{ $t("general.linkedWithDocs") }}
                                    <span class="text-danger">*</span>
                                </label>
                                <multiselect :multiple="true"
                                             v-model="create_linked_with_docs"
                                             :options="docsList.map((type) => type.id)"
                                             :custom-label="
                                                (opt) => docsList.find((x) => x.id == opt)?
                                                    $i18n.locale == 'ar'
                                                        ? docsList.find((x) => x.id == opt).name
                                                        : docsList.find((x) => x.id == opt).name_e: null
                                            ">
                                </multiselect>
                                <template v-if="errors.document_types">
                                    <ErrorMessage
                                        v-for="(errorMessage, index) in errors.document_types"
                                        :key="index">{{
                                            errorMessage
                                        }}</ErrorMessage>
                                </template>
                            </div>
                        </div>
                    </div>
                </b-tab>
                <b-tab :disabled="!document_id" v-if="type == 'edit'"  :title="$t('general.ApproveEmployee')">
                    <div class="d-flex justify-content-end">
                        <b-button
                            variant="success" v-if="!isLoader"
                            class="mx-1" @click.prevent="addApproveEmployee(type)"
                        >
                            {{ type != 'edit'? $t("general.Add"): $t("general.edit") }}
                        </b-button>
                        <b-button variant="success" class="mx-1" disabled v-else>
                            <b-spinner small></b-spinner>
                            <span class="sr-only">{{ $t("login.Loading")}}...</span>
                        </b-button>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">
                                    {{ $t("general.employees") }}
                                    <span class="text-danger">*</span>
                                </label>
                                <multiselect :multiple="true"
                                             v-model="create_linked_with_docs"
                                             :options="employees.map((type) => type.id)"
                                             :custom-label="
                                                (opt) => employees.find((x) => x.id == opt)?
                                                    $i18n.locale == 'ar'
                                                        ? employees.find((x) => x.id == opt).name
                                                        : employees.find((x) => x.id == opt).name_e: null
                                            ">
                                </multiselect>
                                <template v-if="errors.employees">
                                    <ErrorMessage
                                        v-for="(errorMessage, index) in errors.employees"
                                        :key="index">{{
                                            errorMessage
                                        }}</ErrorMessage>
                                </template>
                            </div>
                        </div>
                    </div>
                </b-tab>
            </b-tabs>
        </form>
    </b-modal>
    <!--  /create   -->
</template>

<script>
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import Multiselect from "vue-multiselect";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import {maxLength, minLength, required, requiredIf} from "vuelidate/lib/validators";
import adminApi from "../../../api/adminAxios";
import {arabicValue, englishValue} from "../../../helper/langTransform";
import successError from "../../../helper/mixin/success&error";
import {formatDateOnly} from "../../../helper/startDate";

export default {
    name: "document",
    mixins: [transMixinComp,successError],
    props: {
        id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/document'}
    },
    components: {
        ErrorMessage,
        loader,
        Multiselect,
    },
    data() {
        return {
            fields: [],
            document_id: null,
            branches: [],
            serials: [],
            employees: [],
            create_linked_with_docs: [],
            create_approve_employee: [],
            moduleTypes: [],
            docsList: [],
            create: {
                name: "",
                name_e: "",
                document_detail_type: "normal",
                required: 0,
                need_approve: 0,
                is_copy: 0,
                is_break: 0,
                is_partially: 0,
                relation_voucher_header: 0,
                document_module_type_id: null,
            },
            create_effects: {
                cash: 0,
                customer: 0,
                supplier: 0,
                item_quantity: 0,
                reserved_quantity: 0,
                ordered_quantity: 0,
                available: 0,
                unrealized_revenue: 0,
                revenue: 0,
                unrealized_commission: 0,
                commission: 0
            },
            branchFormCreate: {
                branche_id: null,
                serial_id: null,
            },
            company_id: null,
            isLoader: false,
            errors: {},
            is_disabled: false,
            isCustom:false,
        };
    },
    validations: {
        create: {
            name: {
                required: requiredIf(function (model) {
                    return this.isRequired("name");
                }), minLength: minLength(3), maxLength: maxLength(100)
            },
            name_e: {
                required: requiredIf(function (model) {
                    return this.isRequired("name_e");
                }), minLength: minLength(3), maxLength: maxLength(100)
            },
            required: {
                required: requiredIf(function (model) {
                    return this.isRequired("required");
                })
            },
            document_detail_type: {
                required: requiredIf(function (model) {
                    return this.isRequired("document_detail_type");
                })
            },
            need_approve: {
                required: requiredIf(function (model) {
                    return this.isRequired("need_approve");
                })
            },
            is_copy: {
                required: requiredIf(function (model) {
                    return this.isRequired("is_copy");
                })
            },
            is_break: {
                required: requiredIf(function (model) {
                    return this.isRequired("is_break");
                })
            },
            is_partially: {
                required: requiredIf(function (model) {
                    return this.isRequired("is_partially");
                })
            },
        },
        branchFormCreate: {
            branche_id: { required },
            serial_id: { required },
        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
        this.companyId(this.company_id);
    },
    methods: {
        async getCustomTableFields() {
            this.isCustom = true;
             await adminApi
                .get(`/customTable/table-columns/general_documents`)
                .then((res) => {
                    this.fields = res.data;
                })
                .catch((err) => {
                    this.errorFun('Error', 'Thereisanerrorinthesystem');
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
        defaultData(){
            this.create = {
                name: "", name_e: "", document_detail_type: "normal",
                required: 0, need_approve: 0, is_copy: 0,is_break: 0,is_partially: 0,relation_voucher_header:0,document_module_type_id:null
            };
            this.create_linked_with_docs = [];
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
            if('edit' == this.type){
                this.create_approve_employee = [];
                this.create_effects = {
                    cash: 0,
                    customer: 0,
                    supplier: 0,
                    item_quantity: 0,
                    reserved_quantity: 0,
                    ordered_quantity: 0,
                    available: 0,
                    unrealized_revenue: 0,
                    revenue: 0,
                    unrealized_commission: 0,
                    commission: 0
                };
            }else {
                this.create_effects = {
                    cash: 0,
                    customer: 0,
                    supplier: 0,
                    item_quantity: 0,
                    reserved_quantity: 0,
                    ordered_quantity: 0,
                    available: 0,
                }
            }
            this.branchFormCreate = {
                branche_id: null,
                serial_id: null,
            };
            this.document_id = null;
            this.is_disabled = false;
        },
        resetModalHidden() {
            this.defaultData();
            this.$bvModal.hide(this.id);
        },
        resetModal() {
            setTimeout( async () => {
                if(this.type != 'edit'){
                    this.defaultData();
                    if(!this.isPage) await this.getCustomTableFields();
                    // this.getBranches();
                    // this.getSerials();
                    this.docType();
                    this.getEmployees();
                }else {
                    if(this.idObjEdit.dataObj){
                        let module = this.idObjEdit.dataObj;
                        this.document_id = module.id;
                        this.create.name = module.name;
                        this.create.name_e = module.name_e;
                        this.create.required = module.required;
                        this.create.need_approve = module.need_approve;
                        this.create.is_copy = module.is_copy;
                        this.create.is_break = module.is_break;
                        this.create.is_partially = module.is_partially;
                        this.create.document_detail_type = module.document_detail_type;
                        this.create.relation_voucher_header = module.relation_voucher_header;
                        this.create.document_module_type_id = module.document_module_type_id;
                        if (module.attributes) {
                            this.create_effects.cash = module.attributes.cash;
                            this.create_effects.customer = module.attributes.customer;
                            this.create_effects.supplier = module.attributes.supplier;
                            this.create_effects.item_quantity = module.attributes.item_quantity;
                            this.create_effects.reserved_quantity = module.attributes.reserved_quantity;
                            this.create_effects.ordered_quantity = module.attributes.ordered_quantity;
                            this.create_effects.available = module.attributes.available;
                            this.create_effects.unrealized_revenue = module.attributes.unrealized_revenue;
                            this.create_effects.revenue = module.attributes.revenue;
                            this.create_effects.unrealized_commission = module.attributes.unrealized_commission;
                            this.create_effects.commission = module.attributes.commission;
                        }
                        this.docType();
                        this.getEmployees();
                        this.getModuleTypes();
                        // await this.getBranches();
                        // await this.getSerials();
                        // this.branchFormEdit.branche_id = module.branche_id;
                        // this.branchFormEdit.serial_id = module.serial_id;
                        this.create_linked_with_docs = module.document_relateds.map(doc => { return doc.id });
                        this.create_approve_employee = module.employees.map(doc => { return doc.id });
                    }
                }
            },50);
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

            if(this.type == 'edit'){
                if(parseInt(this.create.required) == 1 && this.create_linked_with_docs.length ==0)
                {
                    this.errorFun('Error', 'PleaseSelectAtLeastOneDocument');
                    return ;
                }
                if(parseInt(this.create.need_approve) == 1 && this.create_linked_with_docs.length ==0)
                {
                    this.errorFun('Error', 'PleaseSelectAtLeastOneEmployee');
                    return ;
                }
            }
            if (this.create.document_detail_type == 'document_money' && parseInt(this.create_effects.cash) == 0)
            {
                this.errorFun('Error','YouMustAddAnEffectToTheCacheFirst');
                return;
            }

            this.$v.create.$touch();

            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                if(this.type != 'edit') {
                    adminApi
                        .post(this.url, {
                            ...this.create,
                            company_id: this.$store.getters["auth/company_id"],
                        })
                        .then((res) => {
                            this.is_disabled = true;
                            this.document_id = res.data.data.id;
                            if (!this.isPage)
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
                                this.errorFun('Error', 'Thereisanerrorinthesystem');

                            }
                        })
                        .finally(() => {
                            this.isLoader = false;
                        });
                }else {

                    let { name, name_e, required, need_approve,is_copy,is_break,is_partially, document_detail_type,document_module_type_id } = this.create;
                    adminApi
                        .put(`${this.url}/${this.idObjEdit.idEdit}`, { name, name_e,required, need_approve,is_copy,is_break,is_partially, document_detail_type,document_module_type_id })
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
                                this.errorFun('Error', 'Thereisanerrorinthesystem');
                            }
                        })
                        .finally(() => {
                            this.isLoader = false;
                        });
                }
            }
        },
        formatDate(value) {
            return formatDateOnly(value);
        },
        getEmployees() {
             adminApi
                .get(`/employees`)
                .then((res) => {
                    let l = res.data.data;
                    this.employees = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                });
        },
        getModuleTypes() {
             adminApi
                .get(`/document-module-type/get-drop-down`)
                .then((res) => {
                    let l = res.data.data;
                    this.moduleTypes = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                });
        },
        companyId(id) {
            axios
                .get(
                    `${process.env.MIX_APP_URL_OUTSIDE}api/everything_about_the_company/${id}`
                )
                .then((res) => {
                    let l = res.data.data;
                    if (l.document_company_module.length > 0) {
                        let documents = [];
                        l.document_company_module.forEach((e) => {
                            if (e.document_types.length > 0) {
                                e.document_types.forEach((w) => {
                                    documents.push({
                                        id: w.id,
                                        name: w.name,
                                        name_e: w.name_e,
                                        is_admin: w.is_admin,
                                        is_default: 0,
                                        company_id: id,
                                        document_relateds: w.document_relateds.map((el) => el.id),
                                    });
                                });
                            }
                        });
                        if (documents.length > 0) {
                            documents.forEach((e) => (e.is_admin = 1));
                            adminApi
                                .post(`/document/from_admin`, { documents })
                                .then((res) => {})
                                .catch((err) => {
                                    this.errorFun('Error','Thereisanerrorinthesystem');
                                });
                        }
                    }
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                });
        },
        getHeader() {
            if (this.document_id) {
                return this.$i18n.locale == 'ar' ? this.idObjEdit.dataObj.name : this.idObjEdit.dataObj.name_e;
            }
            return ""
        },
        addDocs($action) {
            this.isLoader = true;
            let linked_with_docs = this.create_linked_with_docs;
            adminApi
                .put(`/document/${this.document_id}`, {
                    document_relateds: linked_with_docs,
                })
                .then((res) => {
                    this.isLoader = false;
                    this.getData();
                    setTimeout(() => {
                        this.successFun('Addedsuccessfully');
                    }, 500);

                }).catch((err) => {
                this.errorFun('Error','Thereisanerrorinthesystem');

            });
        },
        addApproveEmployee($action) {
            this.isLoader = true;
            let linked_with_docs = this.create_approve_employee;
            let data = $action == "create" ? this.create : this.edit;
            adminApi
                .put(`/document/${this.document_id}`, {
                    employees: linked_with_docs,
                })
                .then((res) => {
                    this.isLoader = false;
                    this.getData();
                    setTimeout(() => {
                        this.successFun('Addedsuccessfully');
                    }, 500);

                }).catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                });
        },
        docType() {
             adminApi
                .get(
                    `/document`
                )
                .then((res) => {
                    let l = res.data;
                    this.docsList = l.data;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                })
        },
        addBranchForm(method) {
            let form = null;

            form = { ...this.branchFormCreate, id: this.document_id };
            this.$v.branchFormCreate.$touch();
            if (this.$v.branchFormCreate.$invalid) return;

            this.isLoader = true;
            adminApi
                .put(`/document/${this.document_id}`, form)
                .then((res) => {
                    this.getData();
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
        },
        getBranches() {
            this.isLoader = true;
             adminApi
                .get(`/branches`)
                .then((res) => {
                    this.branches = res.data.data;
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
        },
        getSerials() {
            this.isLoader = true;
             adminApi
                .get(`/serials`)
                .then((res) => {
                    this.serials = res.data.data;
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
        },
        addEffects(method) {
            if (parseInt(this.create_effects.cash) == parseInt(this.create_effects.customer))
            {
                this.errorFun('Error','TheEffectOnTheCashMustNotBeEqualToTheEffectOnTheCustomer');
                return;
            }
            let form = null;
             form = { id: this.document_id, attributes: this.create_effects };

            this.isLoader = true;
            adminApi
                .put(`/document/${this.document_id}`, form)
                .then((res) => {
                    this.getData();
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
        },
        arabicValueName(txt) {
            this.create.name = arabicValue(txt);
        },
        englishValueName(txt) {
            this.create.name_e = englishValue(txt);
        },
    }
}
</script>

<style>
form {
    position: relative;
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
</style>
