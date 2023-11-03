<template>
    <!--  create   -->
    <b-modal
        :id="id"
        :title="type != 'edit'?getCompanyKey('message_create_form'):getCompanyKey('message_edit_form')"
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
                <div class="col-md-12">
                    <table class="table">
                        <tbody>
                            <tr v-for="date in dates">
                                <th scope="row">{{ date.name }}</th>
                                <td>{{ date.var }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-12" v-if="isVisible('message_type_id')">
                    <div class="form-group">
                        <label class="control-label">
                            {{ getCompanyKey("message_type") }}
                            <span
                                v-if="isRequired('message_type_id')"
                                class="text-danger"
                            >*</span
                            >
                        </label>
                        <multiselect
                            v-model="$v.create.message_type_id.$model"
                            :options="types.map((type) => type.id)"
                            :custom-label="
                              (opt) => types.find((x) => x.id == opt)?
                                $i18n.locale == 'ar'
                                  ? types.find((x) => x.id == opt).name
                                  : types.find((x) => x.id == opt).name_e: null
                            "
                        >
                        </multiselect>
                        <div
                            v-if="$v.create.message_type_id.$error || errors.message_type_id"
                            class="text-danger"
                        >
                            {{ $t("general.fieldIsRequired") }}
                        </div>
                        <template v-if="errors.message_type_id">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.message_type_id"
                                :key="index"
                            >{{ errorMessage }}</ErrorMessage
                            >
                        </template>
                    </div>
                </div>
                <div class="col-md-12" v-if="isVisible('content')">
                    <div class="form-group">
                        <label class="mr-2">
                            {{
                                getCompanyKey("message_content_ar")
                            }}
                            <span
                                v-if="isVisible('content')"
                                class="text-danger"
                            >*</span
                            >
                        </label>
                        <textarea
                            v-model="$v.create.content.$model"
                            class="form-control"
                            :maxlength="1000"
                            rows="3"
                        ></textarea>
                        <template v-if="errors.content">
                            <ErrorMessage
                                v-for="(
                                errorMessage, index
                              ) in errors.content"
                                :key="index"
                            >{{ errorMessage }}</ErrorMessage
                            >
                        </template>
                    </div>
                </div>
                <div class="col-md-12" v-if="isVisible('content_e')">
                    <div class="form-group">
                        <label class="mr-2">
                            {{
                                getCompanyKey("message_content_en")
                            }}
                            <span
                                v-if="isRequired('content_e')"
                                class="text-danger"
                            >*</span
                            >
                        </label>
                        <textarea
                            v-model="$v.create.content_e.$model"
                            class="form-control"
                            :maxlength="1000"
                            rows="3"
                        ></textarea>
                        <template v-if="errors.content_e">
                            <ErrorMessage
                                v-for="(
                                errorMessage, index
                              ) in errors.content_e"
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
</template>
<script>
import adminApi from "../../../api/adminAxios";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import successError from "../../../helper/mixin/success&error";
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import {maxLength, minLength, requiredIf} from "vuelidate/lib/validators";
import {arabicValue,englishValue} from "../../../helper/langTransform";
import Multiselect from "vue-multiselect";

export default {
    name: "message",
    mixins: [transMixinComp,successError],
    components: {
        ErrorMessage,
        loader,
        Multiselect
    },
    props: {
        id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/messages'}
    },
    data() {
        return {
            fields: [],
            isLoader: false,
            create: {
                content: "",
                content_e: "",
                message_type_id: null
            },
            company_id: null,
            errors: {},
            isCustom: false,
            is_disabled: false,
            types: [],
            dates: []
        }

    },
    validations: {
        create: {
            content: {
                required: requiredIf(function (model) {
                    return this.isRequired("content");
                }),
                minLength: minLength(3),
                maxLength: maxLength(1000),
            },
            content_e: {
                required: requiredIf(function (model) {
                    return this.isRequired("content_e");
                }),
                minLength: minLength(3),
                maxLength: maxLength(1000),
            },
            message_type_id: {
                required: requiredIf(function (model) {
                    return this.isRequired("message_type_id");
                }),
            },
        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
    },
    methods: {
        getCustomTableFields() {
            this.isCustom = true;
            adminApi
                .get(`/customTable/table-columns/general_colors`)
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
            this.create = { content: "", content_e: "", message_type_id: null };
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
            setTimeout( () => {
                this.getCategory();
                if(this.type != 'edit'){
                    if(!this.isPage)  this.getCustomTableFields();
                    if(this.isVisible('message_type_id')) this.getTypes();
                }else {
                    if(this.idObjEdit.dataObj) {
                        let color = this.idObjEdit.dataObj;
                        if(this.isVisible('message_type_id')) this.getTypes();
                        this.errors = {};
                        this.create.content = color.content;
                        this.create.content_e = color.content_e;
                        this.create.message_type_id = color.message_type_id;
                    }
                }
            },50);
        },
        resetForm() {
            this.defaultData();
        },
        AddSubmit() {
            if (!this.create.content) {
                this.create.content = this.create.content_e;
            }
            if (!this.create.content_e) {
                this.create.content_e = this.create.content;
            }
            this.$v.create.$touch();

            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                if(this.type != 'edit'){
                    adminApi
                        .post(this.url, { ...this.create, company_id: this.company_id })
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
        arabicValueDescription(txt) {
            this.create.content = arabicValue(txt);
        },
        englishValueDescription(txt) {
            this.create.content_e = englishValue(txt);
        },
        getCategory() {
            adminApi
                .get(`/messages-var`)
                .then((res) => {
                    let l = res.data.data;
                    this.dates = l;
                })
                .catch((err) => {
                    this.errorFun("Error", "Thereisanerrorinthesystem");
                });
        },
        getTypes() {
            adminApi
                .get(`/message-types`)
                .then((res) => {
                    let l = res.data.data;
                    this.types = l;
                })
                .catch((err) => {
                    this.errorFun("Error", "Thereisanerrorinthesystem");
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
