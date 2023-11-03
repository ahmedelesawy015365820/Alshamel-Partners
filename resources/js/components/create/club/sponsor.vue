<script>
import adminApi from "../../../api/adminAxios";
import {required, minLength, maxLength, integer, requiredIf} from "vuelidate/lib/validators";
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import Multiselect from "vue-multiselect";
import { formatDateOnly } from "../../../helper/startDate";
import { arabicValue, englishValue } from "../../../helper/langTransform";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import successError from "../../../helper/mixin/success&error";

/**
 * Advanced Table component
 */
export default {
    mixins: [transMixinComp,successError],
    components: {
        ErrorMessage,
        loader,
        Multiselect
    },
    props: {
        id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/club-members/sponsers'}
    },
    data() {
        return {
            groups: [],
            fields: [],
            members: [],
            isCustom : false,
            isLoader: false,
            create: {
                name: "",
                name_e: "",
                parent_id: null,
                cm_member_id: null,
            },
            errors: {},
            company_id:null,
            is_disabled: false
        };
    },
    validations: {
        create: {
            name: { required: requiredIf(function (model) {
                    return this.isRequired("name");
                }), minLength: minLength(3), maxLength: maxLength(100) },
            name_e: {
                required: requiredIf(function (model) {
                    return this.isRequired("name_e");
                }),
                minLength: minLength(3),
                maxLength: maxLength(100),
            },
            group_id: {required: requiredIf(function (model) {
                    return this.isRequired("group_id");
                })
            },
            cm_member_id: {required: requiredIf(function (model) {
                    return this.isRequired("cm_member_id");
                })
            },
        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
    },
    methods: {
        async getCustomTableFields() {
            this.isCustom = true;
            await adminApi
                .get(`/customTable/table-columns/cm_sponsers`)
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
        getGroup(id) {
            this.isLoader = true;

            adminApi
                .get(
                    `/club-members/sponsor-group`
                )
                .then((res) => {
                    let l = res.data.data;
                    this.groups = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
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
        formatDate(value) {
            return formatDateOnly(value);
        },
        defaultData(edit = null){
            this.create = { name: "", name_e: "", group_id : null , cm_member_id : null };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
            this.is_disabled = false;
        },
        resetModalHidden() {
            this.defaultData();
            this.$bvModal.hide(this.id);
        },
        resetModal() {
            this.defaultData();
            setTimeout( async () => {
                if(this.type != 'edit'){
                    if(!this.isPage) await  this.getCustomTableFields();
                    if (this.isVisible("group_id")) this.getGroup();
                    if (this.isVisible("cm_member_id")) this.getMember();
                }else {
                    if(this.idObjEdit.dataObj){
                        if (this.isVisible("group_id")) this.getGroup();
                        if (this.isVisible("cm_member_id")) this.getMember();
                        let module = this.idObjEdit.dataObj;
                        this.errors = {};
                        this.create.name = module.name;
                        this.create.name_e = module.name_e;
                        this.create.group_id = module.group_id;
                        this.create.cm_member_id = module.cm_member_id;
                    }
                }
            },50);
        },
        resetForm() {
            this.defaultData();
        },
        AddSubmit() {
            if (this.create.name || this.create.name_e) {
                this.create.name = this.create.name ? this.create.name : this.create.name_e;
                this.create.name_e = this.create.name_e ? this.create.name_e : this.create.name;
            }
            this.$v.create.$touch();
            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                this.is_disabled = false;
                if(this.type != 'edit') {
                    adminApi
                        .post(this.url, { ...this.create, company_id: this.company_id })
                        .then((res) => {
                            this.is_disabled = true;
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
                    adminApi
                        .put(`${this.url}/${this.idObjEdit.idEdit}`, {
                            ...this.create,
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
        async searchMember(e)
        {
            let search = e??'';
            clearTimeout(this.debounce);
            this.debounce = setTimeout(() => {
                this.getMember(search);
            }, 500);
        },

        async getMember(search='') {
            this.isLoader = true;
            await adminApi
                .get(`/club-members/members?hasTransaction=1&member_status_id=1&limet=10&company_id=${this.company_id}&search=${search}&columns[0]=national_id&columns[1]=membership_number&columns[2]=full_name`)
                .then((res) => {
                    let l = res.data.data;
                    this.members = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        getMemberName () {
            if (this.create.cm_member_id)
            {
               this.create.name = this.members.find((row) => this.create.cm_member_id == row.id).full_name;
               this.create.name_e = '';
            }

        }

    },
};
</script>

<template>
    <!--  create   -->
    <b-modal
        :id="id"
        :title="type != 'edit'? getCompanyKey('sponsor_create_form'):getCompanyKey('sponsor_edit_form')"
        title-class="font-18"
        body-class="p-4 "
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
                    :class="[
                      'font-weight-bold px-2',
                      is_disabled ? 'mx-2' : '',
                    ]"
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
            <div class="row">
                <div class="col-md-12" v-if="isVisible('cm_member_id')">
                    <div class="form-group position-relative">
                        <label class="control-label">
                            {{ getCompanyKey("member") }}
                            <span v-if="isRequired('cm_member_id')" class="text-danger">*</span>
                        </label>
                        <multiselect
                            :internalSearch="false"
                            @input="getMemberName"
                            @search-change="searchMember"
                            v-model="create.cm_member_id"
                            :options="members.map((type) => type.id)"
                            :custom-label="(opt) => members.find((x) => x.id == opt).full_name"
                        >
                        </multiselect>
                        <template v-if="errors.cm_member_id">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.cm_member_id"
                                :key="index"
                            >{{ errorMessage }}
                            </ErrorMessage>
                        </template>
                    </div>
                </div>
<!--                <div class="col-12 direction" v-if="isVisible('name')" dir="rtl">-->
<!--                    <div class="form-group">-->
<!--                        <label for="field-1" class="control-label">-->
<!--                            {{ getCompanyKey("sponsor_name_ar") }}-->
<!--                            <span v-if="isRequired('name')"  class="text-danger">*</span>-->
<!--                        </label>-->
<!--                        <input-->
<!--                            disabled-->
<!--                            type="text"-->
<!--                            class="form-control arabicInput"-->
<!--                            v-model="$v.create.name.$model"-->
<!--                            :class="{-->
<!--                              'is-invalid': $v.create.name.$error || errors.name,-->
<!--                              'is-valid': !$v.create.name.$invalid && !errors.name,-->
<!--                            }"-->
<!--                            @keyup="arabicValue(create.name)"-->
<!--                            id="field-1"-->
<!--                        />-->
<!--                        <div v-if="!$v.create.name.minLength" class="invalid-feedback">-->
<!--                            {{ $t("general.Itmustbeatleast") }}-->
<!--                            {{ $v.create.name.$params.minLength.min }}-->
<!--                            {{ $t("general.letters") }}-->
<!--                        </div>-->
<!--                        <div v-if="!$v.create.name.maxLength" class="invalid-feedback">-->
<!--                            {{ $t("general.Itmustbeatmost") }}-->
<!--                            {{ $v.create.name.$params.maxLength.max }}-->
<!--                            {{ $t("general.letters") }}-->
<!--                        </div>-->
<!--                        <template v-if="errors.name">-->
<!--                            <ErrorMessage-->
<!--                                v-for="(errorMessage, index) in errors.name"-->
<!--                                :key="index"-->
<!--                            >{{ $t(errorMessage) }}</ErrorMessage-->
<!--                            >-->
<!--                        </template>-->
<!--                    </div>-->
<!--                </div>-->
<!--                <div class="col-12 direction-ltr" v-if="isVisible('name_e')" dir="ltr">-->
<!--                    <div class="form-group">-->
<!--                        <label for="field-2" class="control-label">-->
<!--                            {{ getCompanyKey("sponsor_name_en") }}-->
<!--                            <span v-if="isRequired('name_e')" class="text-danger">*</span>-->
<!--                        </label>-->
<!--                        <input-->
<!--                            disabled-->
<!--                            type="text"-->
<!--                            class="form-control englishInput"-->
<!--                            v-model="$v.create.name_e.$model"-->
<!--                            :class="{-->
<!--                              'is-invalid': $v.create.name_e.$error || errors.name_e,-->
<!--                              'is-valid': !$v.create.name_e.$invalid && !errors.name_e,-->
<!--                            }"-->
<!--                            @keyup="englishValue(create.name_e)"-->
<!--                            id="field-2"-->
<!--                        />-->
<!--                        <div-->
<!--                            v-if="!$v.create.name_e.minLength"-->
<!--                            class="invalid-feedback"-->
<!--                        >-->
<!--                            {{ $t("general.Itmustbeatleast") }}-->
<!--                            {{ $v.create.name_e.$params.minLength.min }}-->
<!--                            {{ $t("general.letters") }}-->
<!--                        </div>-->
<!--                        <div-->
<!--                            v-if="!$v.create.name_e.maxLength"-->
<!--                            class="invalid-feedback"-->
<!--                        >-->
<!--                            {{ $t("general.Itmustbeatmost") }}-->
<!--                            {{ $v.create.name_e.$params.maxLength.max }}-->
<!--                            {{ $t("general.letters") }}-->
<!--                        </div>-->
<!--                        <template v-if="errors.name_e">-->
<!--                            <ErrorMessage-->
<!--                                v-for="(errorMessage, index) in errors.name_e"-->
<!--                                :key="index"-->
<!--                            >{{ $t(errorMessage) }}</ErrorMessage-->
<!--                            >-->
<!--                        </template>-->
<!--                    </div>-->
<!--                </div>-->
                <div class="col-12" v-if="isVisible('group_id')">
                    <div class="form-group position-relative">
                        <label class="control-label">
                            {{ getCompanyKey("sponsor_group") }}
                            <span v-if="isRequired('group_id')" class="text-danger">*</span>
                        </label>
                        <multiselect
                            v-model="create.group_id"
                            :options="groups.map((type) => type.id)"
                            :custom-label="
                                  (opt) => groups.find((x) => x.id == opt)?
                                    groups.locale == 'ar'
                                      ? groups.find((x) => x.id == opt)
                                          .name
                                      : groups.find((x) => x.id == opt)
                                          .name_e: null
                                "
                        >
                        </multiselect>
                        <div
                            v-if="
                                  $v.create.group_id.$error ||
                                  errors.group_id
                                "
                            class="text-danger"
                        >
                            {{ $t("general.fieldIsRequired") }}
                        </div>
                        <template v-if="errors.group_id">
                            <ErrorMessage
                                v-for="(
                                    errorMessage, index
                                  ) in errors.group_id"
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
</template>
<style scoped lang="scss">
form {
    position: relative;
}
</style>
