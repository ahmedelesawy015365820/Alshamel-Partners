<template>
   <div>
       <!--  create   -->
       <b-modal
           :id="id"
           :title="getCompanyKey('new_subscription_create_form')"
           title-class="font-18"
           body-class="p-4 "
           size="lg"
           :hide-footer="true"
           @show="resetModal"
           @hidden="resetModalHidden"
       >
           <form>
               <div v-if="dataInv" style="display:none;">
                   <PrintSubscription :data_row="dataInv"/>
               </div>
               <div class="mb-3 d-flex justify-content-end">
                   <b-button
                       variant="success"
                       :disabled="!is_disabled"
                       @click.prevent="resetForm"
                       type="button"
                       :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : '']"
                   >
                       {{ $t("general.AddNewRecord") }}
                   </b-button>
<!--                   <b-button-->
<!--                       variant="primary"-->
<!--                       :disabled="!is_disabled"-->
<!--                       type="button"-->
<!--                       v-print="'#printInv'"-->
<!--                       :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : 'mx-2']"-->
<!--                   >-->
<!--                       {{ $t("general.print") }}-->
<!--                       <i class="fe-printer"></i>-->
<!--                   </b-button>-->
                   <!-- Emulate built in modal footer ok and cancel button actions -->
                   <template v-if="!is_disabled">
                       <b-button
                           variant="success"
                           type="submit"
                           class="mx-1"
                           v-if="!isLoader"
                           @click.prevent="AddSubmit"
                       >
                           {{ $t("general.Add") }}
                       </b-button>

                       <b-button variant="success" class="mx-1" disabled v-else>
                           <b-spinner small></b-spinner>
                           <span class="sr-only">{{ $t("login.Loading") }}...</span>
                       </b-button>
                   </template>
                   <b-button
                       variant="danger"
                       type="button"
                       @click.prevent="$bvModal.hide(id)"
                   >
                       {{ $t("general.Cancel") }}
                   </b-button>
               </div>
               <div class="row">
                   <div class="col-md-6" v-if="isVisible('date')">
                       <div class="form-group">
                           <label class="control-label">
                               {{ $t("general.date") }}
                           </label>
                           <date-picker
                               @input="handelDateCheck"
                               type="date"
                               v-model="create.date"
                               format="YYYY-MM-DD"
                               valueType="format"
                               :confirm="false"
                           ></date-picker>
                           <template v-if="errors.date">
                               <ErrorMessage v-for="(errorMessage, index) in errors.date" :key="index">
                                   {{ errorMessage }}
                               </ErrorMessage>
                           </template>
                       </div>
                   </div>
                   <div class="col-md-6" v-if="isVisible('branch_id')">
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
                   <div class="col-md-6" v-if="isVisible('serial_id')">
                       <div class="form-group">
                           <label>{{ $t("general.serial_number") }}</label>
                           <multiselect @input="showBranchModal" v-model="create.serial_id"
                                        :options="serials.map((type) => type.id)" :custom-label="
                                                    (opt) =>
                                                        $i18n.locale == 'ar'
                                                            ? serials.find((x) => x.id == opt).name
                                                            : serials.find((x) => x.id == opt).name_e
                                                " :class="{
                                                        'is-invalid':
                                                            $v.create.serial_id.$error || errors.serial_id,
                                                    }">
                           </multiselect>
                           <div v-if="!$v.create.serial_id.required" class="invalid-feedback">
                               {{ $t("general.fieldIsRequired") }}
                           </div>

                           <template v-if="errors.serial_id">
                               <ErrorMessage v-for="(errorMessage, index) in errors.serial_id"
                                             :key="index">{{ errorMessage }}
                               </ErrorMessage>
                           </template>
                       </div>
                   </div>
                   <div class="col-md-6" v-if="isVisible('member_request_id')">
                       <div class="form-group position-relative">
                           <label class="control-label">
                               {{ getCompanyKey("new_subscription_member") }}
                               <span v-if="isRequired('member_request_id')" class="text-danger">*</span>
                           </label>
                           <multiselect
                               :internalSearch="false"
                               @search-change="searchMember"
                               v-model="create.member_request_id"
                               :options="members.map((type) => type.id)"
                               :custom-label="(opt) => members.find((x) => x.id == opt).full_name"
                           >
                           </multiselect>
                           <div
                               v-if="$v.create.member_request_id.$error || errors.member_request_id"
                               class="text-danger"
                           >
                               {{ $t("general.fieldIsRequired") }}
                           </div>
                           <template v-if="errors.member_request_id">
                               <ErrorMessage
                                   v-for="(errorMessage, index) in errors.member_request_id"
                                   :key="index"
                               >{{ errorMessage }}
                               </ErrorMessage>
                           </template>
                       </div>
                   </div>
                   <div class="col-md-6" v-if="isVisible('type')">
                       <div class="form-group">
                           <label  class="control-label">
                               {{ getCompanyKey("new_subscription_type") }}
                               <span  v-if="isRequired('type')" class="text-danger">*</span>
                           </label>
                           <select :disabled="true"  class="form-control" v-model="create.type" :class="{
                                                  'is-invalid': $v.create.type.$error || errors.amount,
                                                  'is-valid':
                                                    !$v.create.type.$invalid && !errors.amount,
                                                }">
                               <option value="subscribe">{{$t('general.subscribe')}}</option>
                               <option value="renew">{{$t('general.renew')}}</option>
                           </select>

                           <template v-if="errors.type">
                               <ErrorMessage
                                   v-for="(errorMessage, index) in errors.type"
                                   :key="index"
                               >{{ errorMessage }}
                               </ErrorMessage>
                           </template>
                       </div>
                   </div>
                   <div class="col-md-6" v-if="isVisible('year') && create.member_request_id">
                       <div class="form-group">
                           <label class="control-label">
                               {{ $t('general.ForAYear') }}
                               <span v-if="isRequired('year')" class="text-danger">*</span>
                           </label>
                           <date-picker
                               :disabled="true"
                               type="year"
                               v-model="$v.create.year.$model"
                               format="YYYY"
                               valueType="format"
                               :confirm="false"
                               :class="{ 'is-invalid':
                                                        $v.create.year.$error ||
                                                        errors.year,
                                                    'is-valid':
                                                        !$v.create.year
                                                            .$invalid &&
                                                        !errors.year,
                                                }"
                           ></date-picker>
                           <template v-if="errors.year">
                               <ErrorMessage v-for="(errorMessage,index) in errors.year"
                                             :key="index">
                                   {{ errorMessage }}
                               </ErrorMessage>
                           </template>
                       </div>
                   </div>
                   <div class="col-md-6" v-if="isVisible('date_from') && create.member_request_id">
                       <div class="form-group">
                           <label class="control-label">
                               {{ $t('general.from_date') }}
                               <span v-if="isRequired('date_from')" class="text-danger">*</span>
                           </label>
                           <date-picker
                               :disabled="true"
                               type="date"
                               v-model="$v.create.date_from.$model"
                               format="YYYY-MM-DD"
                               valueType="format"
                               :confirm="false"
                               :class="{ 'is-invalid':
                                                        $v.create.date_from.$error ||
                                                        errors.date_from,
                                                    'is-valid':
                                                        !$v.create.date_from
                                                            .$invalid &&
                                                        !errors.date_from,
                                                }"
                           ></date-picker>
                           <template v-if="errors.date_from">
                               <ErrorMessage v-for="(errorMessage,index) in errors.date_from"
                                             :key="index">
                                   {{ errorMessage }}
                               </ErrorMessage>
                           </template>
                       </div>
                   </div>
                   <div class="col-md-6" v-if="isVisible('date_to') && create.member_request_id">
                       <div class="form-group">
                           <label class="control-label">
                               {{ $t('general.to_date') }}
                               <span v-if="isRequired('date_to')" class="text-danger">*</span>
                           </label>
                           <date-picker
                               :disabled="true"
                               type="date"
                               v-model="$v.create.date_to.$model"
                               format="YYYY-MM-DD"
                               valueType="format"
                               :confirm="false"
                               :class="{ 'is-invalid':
                                                        $v.create.date_to.$error ||
                                                        errors.date_to,
                                                    'is-valid':
                                                        !$v.create.date_to
                                                            .$invalid &&
                                                        !errors.date_to,
                                                }"
                           ></date-picker>
                           <template v-if="errors.date_to">
                               <ErrorMessage v-for="(errorMessage,index) in errors.date_to"
                                             :key="index">
                                   {{ errorMessage }}
                               </ErrorMessage>
                           </template>
                       </div>
                   </div>
                   <div class="col-md-6" v-if="isVisible('amount') && create.member_request_id">
                       <div class="form-group">
                           <label  class="control-label">
                               {{ getCompanyKey("new_subscription_amount") }}
                               <span v-if="isRequired('amount')" class="text-danger">*</span>
                           </label>
                           <input
                               :disabled="true"
                               type="number"
                               step="any"
                               class="form-control"
                               v-model="$v.create.amount.$model"
                               :class="{
                                                  'is-invalid': $v.create.amount.$error || errors.amount,
                                                  'is-valid':
                                                    !$v.create.amount.$invalid && !errors.amount,
                                                }"
                           />
                           <template v-if="errors.amount">
                               <ErrorMessage
                                   v-for="(errorMessage, index) in errors.amount"
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
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import {maxLength, minLength, requiredIf} from "vuelidate/lib/validators";
import adminApi from "../../../api/adminAxios";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import successError from "../../../helper/mixin/success&error";
import Swal from "sweetalert2";
import DatePicker from "vue2-datepicker";
import Multiselect from "vue-multiselect";
import PrintSubscription from "../../../views/pages/club/print/print-subscription";

export default {
    name: "subscription",
    components: {
        ErrorMessage,
        loader,
        Multiselect,
        DatePicker,
        PrintSubscription
    },
    mixins: [transMixinComp,successError],
    props: {
        id: {default: "add_subscription"},member_request_id: {default: null}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        type: {default: 'create'},url: {default: '/club-members/transactions'}
    },
    data() {
        return {
            fields: [],
            isLoader: false,
            isCustom:false,
            company_id:null,
            financial_years_validate:true,
            dataInv:"",
            serials: [],
            branches: [],
            renewal: [],
            members: [],
            Tooltip: "",
            debounce: {},
            create: {
                branch_id: null,
                serial_id: null,
                member_request_id: null,
                document_id: 38,
                date_from: '',
                date_to: '',
                year: '',
                type: "subscribe",
                amount: "",
                module_type:"club",
                date:new Date().toISOString().slice(0, 10),
            },
            errors: {},
            is_disabled: false,
        };
    },
    validations: {
        create: {
            branch_id: {required: requiredIf(function (model) {
                    return this.isRequired("branch_id");
                })},
            serial_id: {required: requiredIf(function (model) {
                    return this.isRequired("serial_id");
                })},
            member_request_id: {required: requiredIf(function (model) {
                    return this.isRequired("member_request_id");
                })},
            date_from: {required: requiredIf(function (model) {
                    return this.isRequired("date_from");
                })},
            date_to: {required: requiredIf(function (model) {
                    return this.isRequired("date_to");
                })},
            year: {required: requiredIf(function (model) {
                    return this.isRequired("year");
                })},
            amount: {required: requiredIf(function (model) {
                    return this.isRequired("amount");
                })},
            type: {required: requiredIf(function (model) {
                    return this.isRequired("type");
                })},
        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
    },
    methods: {
       async getCustomTableFields() {
            this.isCustom = true;
           await  adminApi
                .get(`/customTable/table-columns/cm_transactions`)
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
            let res = this.fields.filter((field) => {
                return field.column_name == fieldName;
            });
            return res.length > 0 && res[0].is_visible == 1 ? true : false;
        },
        isRequired(fieldName) {
            let res = this.fields.filter((field) => {
                return field.column_name == fieldName;
            });
            return res.length > 0 && res[0].is_required == 1 ? true : false;
        },
        defaultData(edit = null){
            this.create = {
                branch_id: null,
                serial_id: null,
                member_request_id: null,
                document_id: 38,
                date_from: '',
                date_to: '',
                year: '',
                type: "subscribe",
                amount: "",
                module_type:"club",
                date:new Date().toISOString().slice(0, 10),
            };
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
                  await this.getCustomTableFields();
                 if(this.isVisible('member_request_id')) await this.getMember();
                 if(this.isVisible('branch_id')) await this.getBranches();
                 this.financial_years_validate = true;
                 this.create.member_request_id = this.member_request_id;
                 this.handelDateCheck();
             },50);

        },
        resetForm() {
            this.defaultData();
        },
        AddSubmit() {
            this.create.year_from = new Date(this.create.date_from).getFullYear();
            this.create.year_to = new Date(this.create.date_to).getFullYear();
            this.$v.create.$touch();
            if (this.$v.create.$invalid || !this.financial_years_validate) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                this.is_disabled = false;
                let transactions = [this.create]
                adminApi
                    .post(`/club-members/transactions`, {transactions, company_id: this.company_id})
                    .then((res) => {
                        this.$emit("created");
                        this.is_disabled = true;
                        setTimeout(() => {
                            Swal.fire({
                                icon: "success",
                                text: `${this.$t("general.Addedsuccessfully")}`,
                                showConfirmButton: false,
                                timer: 1500,
                            });
                        }, 500);
                        this.printInv(res.data.data[0])
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
        async showBranchModal() {
            if (this.create.branch_id == 0) {
                this.$bvModal.show("create_branch");
                this.create.branch_id = null;
            }else{
                await this.getSerials();
            }
        },
        async getBranches() {
            this.isLoader = true;
            await adminApi
                .get(`/branches?document_id=${this.create.document_id}`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
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
        async getSerials() {
            this.isLoader = true;
            await adminApi
                .get(`/serials?branch_id=${this.create.branch_id}&document_id=38`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    this.serials = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
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
                .get(`/club-members/member-requests?doesNotHaveTransaction=1&limet=10&company_id=${this.company_id}&search=${search}&columns[0]=national_id&columns[1]=membership_number&columns[2]=full_name`)
                .then((res) => {
                    let l = res.data.data;
                    this.members = l;
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
        async getRenewal()
        {
            this.isLoader = true;
            await adminApi
                .get(`/club-members/memberships-renewals?date_search=${this.create.date}`)
                .then((res) => {
                    let l = res.data.data;
                    this.renewal = l;
                    if (this.create.type)
                    {
                        this.renewalAmount();
                    }
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.PleaseSelectAMember")}`,
                    });
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },

        renewalAmount()
        {
            if (this.renewal.length > 0)
            {
                if (this.create.type == "subscribe")
                {
                    this.create.amount = this.renewal[0].membership_cost;
                }else {
                    this.create.amount = this.renewal[0].renewal_cost;
                }
            }
        },
        async checkFinancialYears() {
            this.isLoader = true;
            await adminApi
                .get(`/document-headers/check-date?date=${this.create.date}`)
                .then((res) => {
                    let l = res.data;
                    if(!l)
                    {
                        this.financial_years_validate= false;
                        Swal.fire({
                            icon: "error",
                            title: `${this.$t("general.Error")}`,
                            text: `${this.$t("general.The date is outside the permitted fiscal year")}`,
                        });
                    }else{
                        this.financial_years_validate= true;
                        this.DataOfModelFinancialYear();
                    }
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
        async DataOfModelFinancialYear() {
            this.isLoader = true;
            await adminApi
                .get(`/financial-years/DataOfModelFinancialYear?date=${this.create.date}`)
                .then((res) => {
                    let l = res.data;
                    if(l)
                    {
                        this.create.year = l.data.year+'';
                        this.create.date_from = l.data.start_date;
                        this.create.date_to = l.data.end_date;
                    }
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

        async handelDateCheck()
        {
            await this.checkFinancialYears();
            await this.getRenewal();
        },
        printInv(data){
            this.dataInv = data
        }
    }
}
</script>

<style scoped>
form {
    position: relative;
}
</style>
