<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import {required, minLength, maxLength, integer, requiredIf} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/general/loader";
import {dynamicSortString, dynamicSortNumber} from "../../../helper/tableSort";
import Multiselect from "vue-multiselect";
import {formatDateOnly} from "../../../helper/startDate";
import translation from "../../../helper/mixin/translation-mixin";
import DatePicker from "vue2-datepicker";
import permissionGuard from "../../../helper/permission";
import PrintRenewal from "./print/print-renewal";

/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Subscription",
        meta: [{name: "description", content: "Subscription"}],
    },
    mixins: [translation],
    components: {
        Layout,
        PageHeader,
        Switches,
        ErrorMessage,
        loader,
        Multiselect,
        DatePicker,
        PrintRenewal
    },
    beforeRouteEnter(to, from, next) {
            next((vm) => {
      return permissionGuard(vm, "Subscription", "all subscription club");
    });
    },
    data() {
        return {
            fields: [],
            per_page: 50,
            search: "",
            dataInv:"",
            debounce: {},
            transactionsPagination: {},
            transactions: [],
            serials: [],
            branches: [],
            renewal: [],
            enabled3: true,
            is_disabled: false,
            isLoader: false,
            create: {
                branch_id: null,
                serial_id: null,
                cm_member_id: null,
                document_id: 8,
                date_from: '',
                date_to: '',
                year: '',
                type: "renew",
                amount: "",
                module_type:"club",
                date:new Date().toISOString().slice(0, 10),
            },
            company_id: null,
            edit: {
                branch_id: null,
                cm_member_id: null,
                document_id: 8,
                date_from: '',
                date_to: '',
                year: '',
                type: "renew",
                amount: "",
                module_type:"club",
                date:new Date().toISOString().slice(0, 10),
            },
            setting: {
                branch_id: true,
                serial_number: true,
                cm_member_id: true,
                document_no: true,
                serial_id: true,
                date: true,
                date_from: true,
                date_to: true,
                year: true,
                amount: true,
            },
            members: [],
            Tooltip: "",
            mouseEnter: null,
            errors: {},
            isCheckAll: false,
            checkAll: [],
            current_page: 1,
            filterSetting: [
                "cm_member_id",
                "document_no",
                "serial_id",
                "date",
                "amount",
                "prefix",
                "year",
            ],
            printLoading: true,
            printObj: {
                id: "printData",
            }
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
            cm_member_id: {required: requiredIf(function (model) {
                    return this.isRequired("cm_member_id");
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
        edit: {
            branch_id: {required: requiredIf(function (model) {
                    return this.isRequired("branch_id");
                })},
            serial_id: {required: requiredIf(function (model) {
                    return this.isRequired("serial_id");
                })},
            cm_member_id: {required: requiredIf(function (model) {
                    return this.isRequired("cm_member_id");
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
                this.transactions.forEach((el) => {
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
        this.getCustomTableFields();
        this.getData();
    },
    methods: {
        getCustomTableFields() {
            adminApi
                .get(`/customTable/table-columns/cm_transactions`)
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
                    this.isLoader = false;
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
        isPermission(item) {
            if (this.$store.state.auth.type == 'user'){
                return this.$store.state.auth.permissions.includes(item)
            }
            return true;
        },
        async showBranchModal() {
            if (this.create.branch_id == 0) {
                this.$bvModal.show("create_branch");
                this.create.branch_id = null;
            }else{
                await this.getSerials();
            }
        },
        showBranchModalEdit() {
            if (this.edit.branch_id == 0) {
                this.$bvModal.show("create_branch");
                this.edit.branch_id = null;
            }
        },
        resetForm() {
            this.create = {
                branch_id: null,
                serial_id: null,
                cm_member_id: null,
                date_from: '',
                date_to: '',
                year: '',
                type: "renew",
                document_id: 8,
                amount: "",
                module_type:"club",
                date:new Date().toISOString().slice(0, 10),
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.is_disabled = false;
        },
        /**
         *  start get Data && pagination
         */
        getData(page = 1) {
            this.isLoader = true;
            let _filterSetting = [...this.filterSetting];
            let index = this.filterSetting.indexOf("serial_id");
            if (index > -1) {
                _filterSetting[index] =
                    this.$i18n.locale == "ar" ? "serial.name" : "serial.name_e";
            }
            index = this.filterSetting.indexOf("cm_member_id");
            if (index > -1) {
                _filterSetting[index] ="member.full_name";
            }
            let filter = "";
            for (let i = 0; i < _filterSetting.length; ++i) {
                filter += `columns[${i}]=${_filterSetting[i]}&`;
            }
            adminApi
                .get(
                    `/club-members/transactions?document_id=8&module_type=club&sponsor=0&page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
                )
                .then((res) => {
                    let l = res.data;
                    this.transactions = l.data;
                    this.transactionsPagination = l.pagination;
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
        getDataCurrentPage(page = 1) {
            if (
                this.current_page <= this.transactionsPagination.last_page &&
                this.current_page != this.transactionsPagination.current_page &&
                this.current_page
            ) {
                this.isLoader = true;
                let _filterSetting = [...this.filterSetting];
                let index = this.filterSetting.indexOf("serial_id");
                if (index > -1) {
                    _filterSetting[index] =
                        this.$i18n.locale == "ar" ? "serial.name" : "serial.name_e";
                }
                let filter = "";
                for (let i = 0; i < _filterSetting.length; ++i) {
                    filter += `columns[${i}]=${_filterSetting[i]}&`;
                }
                adminApi
                    .get(
                        `/club-members/transactions?document_id=8&module_type=club&sponsor=0&page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&${filter}`
                    )
                    .then((res) => {
                        let l = res.data;
                        this.transactions = l.data;
                        this.transactionsPagination = l.pagination;
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
                .get(`/serials?branch_id=${this.create.branch_id}&document_id=8`)
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
        /**
         *  end get Data && pagination
         */
        /**
         *  start delete
         */
        deleteBranch(id, index) {
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
                            .post(`/club-members/transactions/bulk-delete`, {ids: id})
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
                            .delete(`/club-members/transactions/${id}`)
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
         *  end delete
         */
        /**
         *  reset Modal (create)
         */
        resetModalHidden() {
            this.create = {
                branch_id: null,
                serial_id: null,
                cm_member_id: null,
                date_from: '',
                date_to: '',
                year: '',
                type: "renew",
                document_id: 8,
                amount: "",
                date:new Date().toISOString().slice(0, 10),
                module_type:"club"
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
            this.is_disabled = false;
            this.members = [];
        },
        /**
         *  hidden Modal (create)
         */
        async resetModal() {
            if(this.isVisible('cm_member_id')) await this.getMember();
            if(this.isVisible('branch_id')) await this.getBranches();
            this.create = {
                branch_id: null,
                serial_id: null,
                cm_member_id: null,
                date_from: '',
                date_to: '',
                year: '',
                type: "renew",
                document_id: 8,
                amount: "",
                date:new Date().toISOString().slice(0, 10),
                module_type:"club"
            };
            await this.getRenewal();
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.is_disabled = false;
            this.errors = {};
        },
        /**
         *  create countrie
         */
        AddSubmit() {
            this.create.year_from = new Date(this.create.date_from).getFullYear();
            this.create.year_to = new Date(this.create.date_to).getFullYear();
            this.$v.create.$touch();
            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                this.is_disabled = false;
                let transactions = [this.create]
                adminApi
                    .post(`/club-members/transactions`, {transactions, company_id: this.company_id})
                    .then((res) => {
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
        /**
         *  edit countrie
         */
        editSubmit(id) {
            this.$v.edit.$touch();
            if (this.$v.edit.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                adminApi
                    .put(`/club-members/transactions/${id}`, this.edit)
                    .then((res) => {
                        this.$bvModal.hide(`modal-edit-${id}`);
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
         *   show Modal (edit)
         */
        async resetModalEdit(id) {
            if(this.isVisible('cm_member_id')) await this.getMember();
            if(this.isVisible('branch_id')) await this.getBranches();
            let setting = this.transactions.find((e) => id == e.id);
            this.edit.cm_member_id = setting.member.id;
            this.edit.branch_id = setting.branch.id;
            this.edit.date_from = setting.date_from;
            this.edit.date_to = setting.date_to;
            this.edit.year = setting.year;
            this.edit.year_from = setting.year_from;
            this.edit.type = setting.type;
            this.edit.document_id = setting.document_id;
            this.edit.year_to = setting.year_to;
            this.edit.amount = setting.amount;
            this.edit.module_type = "club";
            this.edit.date = new Date().toISOString().slice(0, 10);
            if(this.isVisible('serial_id')) await this.getSerials();
            this.errors = {};
        },
        /**
         *  hidden Modal (edit)
         */
        resetModalHiddenEdit(id) {
            this.errors = {};
            this.edit = {
                branch_id: null,
                cm_member_id: null,
                date_from: '',
                date_to: '',
                year: '',
                type: "renew",
                document_id: 8,
                amount: "",
                date:new Date().toISOString().slice(0, 10),
                module_type:"club"
            };
            this.members = [];
        },
        /*
         *  start  dynamicSortString
         */
        sortString(value) {
            return dynamicSortString(value);
        },
        sortNumber(value) {
            return dynamicSortNumber(value);
        },
        /**
         *  start  ckeckRow
         */
        checkRow(id) {
            if (!this.checkAll.includes(id)) {
                this.checkAll.push(id);
            } else {
                let index = this.checkAll.indexOf(id);
                this.checkAll.splice(index, 1);
            }
        },
        /**
         *  end  ckeckRow
         */

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

        formatDate(value) {
            return formatDateOnly(value);
        },
        log(id) {
            if (this.mouseEnter != id) {
                this.Tooltip = "";
                this.mouseEnter = id;
                adminApi
                    .get(`/club-members/transactions/logs/${id}`)
                    .then((res) => {
                        let l = res.data.data;
                        l.forEach((e) => {
                            this.Tooltip += `Created By: ${e.causer_type}; Event: ${
                                e.event
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
                    this.DataOfModelFinancialYear();
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

        /**
         *   Export Excel
         */
        ExportExcel(type, fn, dl) {
            this.enabled3 = false;
            setTimeout(() => {
                let elt = this.$refs.exportable_table;
                let wb = XLSX.utils.table_to_book(elt, {sheet: "Sheet JS"});
                if (dl) {
                    XLSX.write(wb, {bookType: type, bookSST: true, type: 'base64'});
                } else {
                    XLSX.writeFile(wb, fn || (('subscription' + '.' || 'SheetJSTableExport.') + (type || 'xlsx')));
                }
                this.enabled3 = true;
            }, 100);
        },
         getMemberTransaction(){
            this.isLoader = true;
             adminApi
                .get(`/club-members/transactions/member-last-transaction/${this.create.cm_member_id}`)
                .then((res) => {
                    let l = res.data.data;
                    if (l.year)
                    {
                        this.create.year = `${parseInt(l.year) + 1}`
                    }else{
                        this.create.year = `${parseInt(l.year_from) + 1}`
                    }
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.ThisMemberIsNotSubscribedOrHasBeenDeleted")}`,
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

        printInv(data){
            this.dataInv = data
        }
    },
};
</script>

<template>
    <Layout>
        <PageHeader/>
        <div v-if="dataInv" style="display:none;">
            <PrintRenewal :data_row="dataInv"/>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- start search -->
                        <div class="row justify-content-between align-items-center mb-2">
                            <h4 class="header-title">{{ $t("general.MembershipRenewalTable") }}</h4>
                            <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                                <div class="d-inline-block" style="width: 22.2%">
                                    <!-- Basic dropdown -->
                                    <b-dropdown
                                        variant="primary"
                                        :text="$t('general.searchSetting')"
                                        ref="dropdown"
                                        class="btn-block setting-search"
                                    >
                                        <b-form-checkbox v-if="isVisible('cm_member_id')" v-model="filterSetting" value="cm_member_id"
                                                         class="mb-1"
                                        >{{ getCompanyKey("member") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-if="isVisible('document_no')" v-model="filterSetting" value="document_no"
                                                         class="mb-1"
                                        >{{ $t("general.DocumentNumber") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-if="isVisible('serial_id')" v-model="filterSetting" value="serial_id"
                                                         class="mb-1"
                                        >{{ $t("general.serialName") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-if="isVisible('date')" v-model="filterSetting"
                                                         value="date" class="mb-1"
                                        >{{ $t("general.date") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-if="isVisible('amount')" v-model="filterSetting"
                                                         value="amount" class="mb-1"
                                        >{{ getCompanyKey("subscription_amount") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-if="isVisible('year')" v-model="filterSetting"
                                                         value="year" class="mb-1"
                                        >{{ $t("general.ForAYear") }}
                                        </b-form-checkbox>
                                        <!-- Basic dropdown -->
                                    </b-dropdown>
                                </div>

                                <div class="d-inline-block position-relative" style="width: 77%">
                                      <span :class="[ 'search-custom position-absolute', $i18n.locale == 'ar' ? 'search-custom-ar' : '', ]">
                                        <i class="fe-search"></i>
                                      </span>
                                    <input
                                        class="form-control"
                                        style="display: block; width: 93%; padding-top: 3px"
                                        type="text"
                                        v-model.trim="search"
                                        :placeholder="`${$t('general.Search')}...`"
                                    />
                                </div>
                            </div>
                        </div>
                        <!-- end search -->

                        <div class="row justify-content-between align-items-center mb-2 px-1">
                            <div class="col-md-3 d-flex align-items-center mb-1 mb-xl-0">
                                <!-- start create and printer -->
                                <b-button
                                    v-if="isPermission('create subscription club')"
                                    v-b-modal.create
                                    variant="primary"
                                    class="btn-sm mx-1 font-weight-bold"
                                >
                                    {{ $t("general.Create") }}
                                    <i class="fas fa-plus"></i>
                                </b-button>
                                <div class="d-inline-flex">
                                    <button @click="ExportExcel('xlsx')" class="custom-btn-dowonload">
                                        <i class="fas fa-file-download"></i>
                                    </button>
                                    <button v-print="'#printData'" class="custom-btn-dowonload">
                                        <i class="fe-printer"></i>
                                    </button>
<!--                                    <button-->
<!--                                        class="custom-btn-dowonload"-->
<!--                                        @click="$bvModal.show(`modal-edit-${checkAll[0]}`)"-->
<!--                                        v-if="checkAll.length == 1"-->
<!--                                    >-->
<!--                                        <i class="mdi mdi-square-edit-outline"></i>-->
<!--                                    </button>-->
                                    <!-- start mult delete  -->
                                    <button
                                        class="custom-btn-dowonload"
                                        v-if="checkAll.length > 1 && isPermission('delete subscription club')"
                                        @click.prevent="deleteBranch(checkAll)"
                                    >
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <!-- end mult delete  -->
                                    <!--  start one delete  -->
                                    <button
                                        class="custom-btn-dowonload"
                                        v-if="checkAll.length == 1 && isPermission('delete subscription club')"
                                        @click.prevent="deleteBranch(checkAll[0])"
                                    >
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <!--  end one delete  -->
                                </div>
                                <!-- end create and printer -->
                            </div>
                            <div
                                class="col-xs-10 col-md-9 col-lg-7 d-flex align-items-center justify-content-end"
                            >
                                <div class="d-fex">
                                    <!-- start filter and setting -->
                                    <div class="d-inline-block">
                                        <b-button class="mx-1 custom-btn-background">
                                            {{ $t("general.filter") }}
                                            <i class="fas fa-filter"></i>
                                        </b-button>
                                        <b-button class="mx-1 custom-btn-background">
                                            {{ $t("general.group") }}
                                            <i class="fe-menu"></i>
                                        </b-button>
                                        <!-- Basic dropdown -->
                                        <b-dropdown
                                            variant="primary"
                                            :html="`${$t('general.setting')} <i class='fe-settings'></i>`"
                                            ref="dropdown"
                                            class="dropdown-custom-ali"
                                        >
                                            <b-form-checkbox v-if="isVisible('cm_member_id')" v-model="setting.cm_member_id"
                                                             class="mb-1"
                                            >{{ getCompanyKey("member") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-if="isVisible('serial_number')" v-model="setting.serial_number" class="mb-1">
                                                {{ $t("general.serial_number") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-if="isVisible('document_no')" v-model="setting.document_no" class="mb-1">
                                                {{ $t("general.DocumentNumber") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-if="isVisible('serial_id')" v-model="setting.serial_id" class="mb-1">
                                                {{ $t("general.serialName") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-if="isVisible('date')" v-model="setting.date" class="mb-1">
                                                {{ $t("general.date") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-if="isVisible('amount')" v-model="setting.amount"
                                                             class="mb-1">
                                                {{ getCompanyKey("subscription_amount") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-if="isVisible('year')" v-model="setting.year"
                                                             class="mb-1">
                                                {{ $t("general.ForAYear") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-if="isVisible('date_from')" v-model="setting.date_from"
                                                             class="mb-1">
                                                {{ getCompanyKey("year_from") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-if="isVisible('date_to')" v-model="setting.date_to"
                                                             class="mb-1">
                                                {{ getCompanyKey("year_to") }}
                                            </b-form-checkbox>
                                            <div class="d-flex justify-content-end">
                                                <a href="javascript:void(0)" class="btn btn-primary btn-sm">{{$t("general.Apply")}}</a>
                                            </div>
                                        </b-dropdown>
                                        <!-- Basic dropdown -->
                                    </div>
                                    <!-- start Pagination -->
                                    <div class="d-inline-flex align-items-center pagination-custom">
                                        <div class="d-inline-block" style="font-size: 13px">
                                            {{ transactionsPagination.from }}-{{ transactionsPagination.to }} /
                                            {{ transactionsPagination.total }}
                                        </div>
                                        <div class="d-inline-block">
                                            <a
                                                href="javascript:void(0)"
                                                :style="{
                                                  'pointer-events':
                                                    transactionsPagination.current_page == 1 ? 'none' : '',
                                                }"
                                                @click.prevent="getData(transactionsPagination.current_page - 1)"
                                            >
                                                <span>&lt;</span>
                                            </a>
                                            <input
                                                type="text"
                                                @keyup.enter="getDataCurrentPage()"
                                                v-model="current_page"
                                                class="pagination-current-page"
                                            />
                                            <a
                                                href="javascript:void(0)"
                                                :style="{
                                                  'pointer-events':
                                                    transactionsPagination.last_page == transactionsPagination.current_page
                                                      ? 'none'
                                                      : '',
                                                }"
                                                @click.prevent="getData(transactionsPagination.current_page + 1)"
                                            >
                                                <span>&gt;</span>
                                            </a>
                                        </div>
                                    </div>
                                    <!-- end Pagination -->
                                </div>
                            </div>
                        </div>

                        <!--  create   -->
                        <b-modal
                            id="create"
                            :title="getCompanyKey('subscription_create_form')"
                            title-class="font-18"
                            body-class="p-4 "
                            size="lg"
                            :hide-footer="true"
                            @show="resetModal"
                            @hidden="resetModalHidden"
                        >
                            <form>
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
                                    <b-button
                                        variant="primary"
                                        :disabled="!is_disabled"
                                        type="button"
                                        v-print="'#printInv'"
                                        :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : 'mx-2']"
                                    >
                                        {{ $t("general.print") }}
                                        <i class="fe-printer"></i>
                                    </b-button>
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
                                        @click.prevent="$bvModal.hide('create')"
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
                                                @input="getRenewal"
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
                                    <div class="col-md-6" v-if="isVisible('cm_member_id')">
                                        <div class="form-group position-relative">
                                            <label class="control-label">
                                                {{ getCompanyKey("member") }}
                                                <span v-if="isRequired('cm_member_id')" class="text-danger">*</span>
                                            </label>
                                            <multiselect
                                                :internalSearch="false"
                                                @input="getMemberTransaction"
                                                @search-change="searchMember"
                                                v-model="create.cm_member_id"
                                                :options="members.map((type) => type.id)"
                                                :custom-label="
                                                  (opt) => members.find((x) => x.id == opt).full_name
                                                "
                                            >
                                            </multiselect>
                                            <div
                                                v-if="$v.create.cm_member_id.$error || errors.cm_member_id"
                                                class="text-danger"
                                            >
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>
                                            <template v-if="errors.cm_member_id">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.cm_member_id"
                                                    :key="index"
                                                >{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
<!--                                    <div class="col-md-6" v-if="isVisible('type')">-->
<!--                                        <div class="form-group">-->
<!--                                            <label  class="control-label">-->
<!--                                                {{ getCompanyKey("subscription_type") }}-->
<!--                                                <span  v-if="isRequired('type')" class="text-danger">*</span>-->
<!--                                            </label>-->
<!--                                            <select :disabled="true"  class="form-control" v-model="create.type" :class="{-->
<!--                                                  'is-invalid': $v.create.type.$error || errors.type,-->
<!--                                                  'is-valid':-->
<!--                                                    !$v.create.type.$invalid && !errors.type,-->
<!--                                                }">-->
<!--                                                <option value="subscribe">{{$t('general.subscribe')}}</option>-->
<!--                                                <option value="renew">{{$t('general.renew')}}</option>-->
<!--                                            </select>-->

<!--                                            <template v-if="errors.type">-->
<!--                                                <ErrorMessage-->
<!--                                                    v-for="(errorMessage, index) in errors.type"-->
<!--                                                    :key="index"-->
<!--                                                >{{ errorMessage }}-->
<!--                                                </ErrorMessage>-->
<!--                                            </template>-->
<!--                                        </div>-->
<!--                                    </div>-->
                                    <div class="col-md-6" v-if="isVisible('year') && create.cm_member_id">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ $t('general.ForAYear') }}
                                                <span v-if="isRequired('year')" class="text-danger">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                placeholder="yyyy"
                                                v-model="$v.create.year.$model"
                                                :class="{ 'is-invalid':
                                                        $v.create.year.$error ||
                                                        errors.year,
                                                    'is-valid':
                                                        !$v.create.year
                                                            .$invalid &&
                                                        !errors.year,
                                                }"
                                            >
                                            <template v-if="errors.year">
                                                <ErrorMessage v-for="(errorMessage,index) in errors.year"
                                                              :key="index">
                                                    {{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-6" v-if="isVisible('date_from') && create.cm_member_id">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ $t('general.from_date') }}
                                                <span v-if="isRequired('date_from')" class="text-danger">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                placeholder="yyyy-mm-dd"
                                                v-model="$v.create.date_from.$model"
                                                :class="{ 'is-invalid':
                                                        $v.create.date_from.$error ||
                                                        errors.date_from,
                                                    'is-valid':
                                                        !$v.create.date_from
                                                            .$invalid &&
                                                        !errors.date_from,
                                                }"
                                            >
                                            <template v-if="errors.date_from">
                                                <ErrorMessage v-for="(errorMessage,index) in errors.date_from"
                                                              :key="index">
                                                    {{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-6" v-if="isVisible('date_to') && create.cm_member_id">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ $t('general.to_date') }}
                                                <span v-if="isRequired('date_to')" class="text-danger">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                placeholder="yyyy-mm-dd"
                                                v-model="$v.create.date_to.$model"
                                                :class="{ 'is-invalid':
                                                        $v.create.date_to.$error ||
                                                        errors.date_to,
                                                    'is-valid':
                                                        !$v.create.date_to.$invalid &&
                                                        !errors.date_to,
                                                }"
                                            >
                                            <template v-if="errors.date_to">
                                                <ErrorMessage v-for="(errorMessage,index) in errors.date_to"
                                                              :key="index">
                                                    {{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-6" v-if="isVisible('amount') && create.cm_member_id">
                                        <div class="form-group">
                                            <label  class="control-label">
                                                {{ getCompanyKey("subscription_amount") }}
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

                        <!-- start .table-responsive-->
                        <div class="table-responsive mb-3 custom-table-theme position-relative">
                            <!--       start loader       -->
                            <loader size="large" v-if="isLoader"/>
                            <!--       end loader       -->

                            <table class="table table-borderless table-hover table-centered m-0" ref="exportable_table"
                                   id="printData">
                                <thead>
                                <tr>
                                    <th v-if="enabled3" class="do-not-print" scope="col" style="width: 0">
                                        <div class="form-check custom-control">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                v-model="isCheckAll"
                                                style="width: 17px; height: 17px"
                                            />
                                        </div>
                                    </th>
                                    <th v-if="setting.cm_member_id && isVisible('cm_member_id')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member") }}</span>
                                            <div class="arrow-sort">
                                                <i
                                                    class="fas fa-arrow-up"
                                                    @click="transactions.sort(sortString('full_name'))"
                                                ></i>
                                                <i
                                                    class="fas fa-arrow-down"
                                                    @click="transactions.sort(sortString('-full_name'))"
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.serial_number && isVisible('serial_number')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t("general.serial_number") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="transactions.sort(sortString('prefix'))"></i>
                                                <i class="fas fa-arrow-down" @click="transactions.sort(sortString('-prefix'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.document_no && isVisible('document_no')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t("general.DocumentNumber") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="transactions.sort(sortString($i18n.locale == 'ar' ? 'document_no' : 'document_no'))"
                                                ></i>
                                                <i class="fas fa-arrow-down"
                                                   @click=" transactions.sort(sortString($i18n.locale == 'ar' ? '-document_no' : '-document_no'))"
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.serial_id && isVisible('serial_id')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t("general.serialName") }}</span>
                                            <div class="arrow-sort">
                                                <i
                                                    class="fas fa-arrow-up"
                                                    @click="
                                                      transactions.sort(
                                                        sortString(($i18n.locale = 'ar' ? 'name' : 'name_e'))
                                                      )
                                                    "
                                                ></i>
                                                <i
                                                    class="fas fa-arrow-down"
                                                    @click="
                                                      transactions.sort(
                                                        sortString(($i18n.locale = 'ar' ? '-name' : '-name_e'))
                                                      )
                                                    "
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.date && isVisible('date')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t("general.date") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="transactions.sort(sortString($i18n.locale == 'ar' ? 'date' : 'date'))"
                                                ></i>
                                                <i class="fas fa-arrow-down"
                                                   @click=" transactions.sort(sortString($i18n.locale == 'ar' ? '-date' : '-date'))"
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.amount && isVisible('amount')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("subscription_amount") }}</span>
                                            <div class="arrow-sort">
                                                <i
                                                    class="fas fa-arrow-up"
                                                    @click="transactions.sort(sortString('amount'))"
                                                ></i>
                                                <i
                                                    class="fas fa-arrow-down"
                                                    @click="transactions.sort(sortString('-amount'))"
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.year && isVisible('year')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t("general.ForAYear") }}</span>
                                            <div class="arrow-sort">
                                                <i
                                                    class="fas fa-arrow-up"
                                                    @click="transactions.sort(sortString('year'))"
                                                ></i>
                                                <i
                                                    class="fas fa-arrow-down"
                                                    @click="transactions.sort(sortString('-year'))"
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.date_from && isVisible('date_from')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("year_from") }}</span>
                                            <div class="arrow-sort">
                                                <i
                                                    class="fas fa-arrow-up"
                                                    @click="transactions.sort(sortString('date_from'))"
                                                ></i>
                                                <i
                                                    class="fas fa-arrow-down"
                                                    @click="transactions.sort(sortString('-date_from'))"
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.date_to && isVisible('date_to')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("year_to") }}</span>
                                            <div class="arrow-sort">
                                                <i
                                                    class="fas fa-arrow-up"
                                                    @click="transactions.sort(sortString('date_to'))"
                                                ></i>
                                                <i
                                                    class="fas fa-arrow-down"
                                                    @click="transactions.sort(sortString('-date_to'))"
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="enabled3" class="do-not-print">
                                        {{ $t("general.Action") }}
                                    </th>
                                    <th v-if="enabled3" class="do-not-print"><i class="fas fa-ellipsis-v"></i></th>
                                </tr>
                                </thead>
                                <tbody v-if="transactions.length > 0">
                                <tr
                                    @click.capture="checkRow(data.id)"
                                    v-for="(data, index) in transactions"
                                    :key="data.id"
                                    class="body-tr-custom"
                                >

<!--                                    @dblclick.prevent="$bvModal.show(`modal-edit-${data.id}`)"-->
                                    <td v-if="enabled3" class="do-not-print">
                                        <div class="form-check custom-control" style="min-height: 1.9em">
                                            <input
                                                style="width: 17px; height: 17px"
                                                class="form-check-input"
                                                type="checkbox"
                                                :value="data.id"
                                                v-model="checkAll"
                                            />
                                        </div>
                                    </td>
                                    <td v-if="setting.cm_member_id && isVisible('cm_member_id')">
                                        <h5 class="m-0 font-weight-normal">
                                            {{data.member ? data.member.full_name:''}}
                                        </h5>
                                    </td>
                                    <td v-if="setting.serial_number && isVisible('serial_number')">
                                        <h5 class="m-0 font-weight-normal">
                                            {{ data.prefix }}
                                        </h5>
                                    </td>
                                    <td v-if="setting.document_no && isVisible('document_no')">
                                        <h5 class="m-0 font-weight-normal">{{ data.document_no }}</h5>
                                    </td>
                                    <td v-if="setting.serial_id && isVisible('serial_id')">
                                        <h5 class="m-0 font-weight-normal">
                                            {{ data.serial ? $i18n.locale == 'ar' ? data.serial.name : data.serial.name : '---' }}
                                        </h5>
                                    </td>
                                    <td v-if="setting.date && isVisible('date')">
                                        <h5 class="m-0 font-weight-normal">{{ formatDate(data.date) }}</h5>
                                    </td>
                                    <td v-if="setting.amount && isVisible('amount')">
                                        <h5 class="m-0 font-weight-normal">{{ data.amount }}</h5>
                                    </td>
                                    <td v-if="setting.year && isVisible('year')">
                                        <h5 class="m-0 font-weight-normal">{{ data.year ? data.year : data.year_to}}</h5>
                                    </td>
                                    <td v-if="setting.date_from && isVisible('date_from')">
                                        <h5 class="m-0 font-weight-normal">{{ data.date_from ? data.date_from: data.year_from }}</h5>
                                    </td>
                                    <td v-if="setting.date_to && isVisible('date_to')">
                                        <h5 class="m-0 font-weight-normal">{{ data.date_to ? data.date_to: data.year_to }}</h5>
                                    </td>
                                    <td v-if="enabled3" class="do-not-print">
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
                                            <div class="dropdown-menu dropdown-menu-custom">
<!--                                                <a-->
<!--                                                    class="dropdown-item"-->
<!--                                                    href="#"-->
<!--                                                    @click="$bvModal.show(`modal-edit-${data.id}`)"-->
<!--                                                >-->
<!--                                                    <div-->
<!--                                                        class="d-flex justify-content-between align-items-center text-black"-->
<!--                                                    >-->
<!--                                                        <span>{{ $t("general.edit") }}</span>-->
<!--                                                        <i class="mdi mdi-square-edit-outline text-info"></i>-->
<!--                                                    </div>-->
<!--                                                </a>-->

                                                <a class="dropdown-item"  v-print="'#printInv'" href="#" @click="printInv(data)" >
                                                    <div class="d-flex justify-content-between align-items-center text-black">
                                                        {{ $t("general.print") }}
                                                        <i class="fe-printer"></i>
                                                    </div>
                                                </a>
                                                <a
                                                    v-if="isPermission('delete subscription club')"
                                                    class="dropdown-item text-black"
                                                    href="#"
                                                    @click.prevent="deleteBranch(data.id)"
                                                >
                                                    <div
                                                        class="d-flex justify-content-between align-items-center text-black"
                                                    >
                                                        <span>{{ $t("general.delete") }}</span>
                                                        <i class="fas fa-times text-danger"></i>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>

                                        <!--  edit   -->
<!--                                        <b-modal-->
<!--                                            :id="`modal-edit-${data.id}`"-->
<!--                                            :title="getCompanyKey('subscription_edit_form')"-->
<!--                                            title-class="font-18"-->
<!--                                            body-class="p-4"-->
<!--                                            size="lg"-->
<!--                                            :ref="`edit-${data.id}`"-->
<!--                                            :hide-footer="true"-->
<!--                                            @show="resetModalEdit(data.id)"-->
<!--                                            @hidden="resetModalHiddenEdit(data.id)"-->
<!--                                        >-->
<!--                                            <form>-->
<!--                                                <div class="mb-3 d-flex justify-content-end">-->
<!--                                                    &lt;!&ndash; Emulate built in modal footer ok and cancel button actions &ndash;&gt;-->
<!--                                                    <b-button-->
<!--                                                        variant="success"-->
<!--                                                        @click.prevent="editSubmit(data.id)"-->
<!--                                                        type="button"-->
<!--                                                        class="mx-1 font-weight-bold px-3"-->
<!--                                                        v-if="!isLoader"-->
<!--                                                    >-->
<!--                                                        {{ $t("general.Edit") }}-->
<!--                                                    </b-button>-->

<!--                                                    <b-button variant="success" class="mx-1" disabled v-else>-->
<!--                                                        <b-spinner small></b-spinner>-->
<!--                                                        <span class="sr-only">{{ $t("login.Loading") }}...</span>-->
<!--                                                    </b-button>-->

<!--                                                    <b-button-->
<!--                                                        variant="danger"-->
<!--                                                        class="font-weight-bold"-->
<!--                                                        type="button"-->
<!--                                                        @click.prevent="$bvModal.hide(`modal-edit-${data.id}`)"-->
<!--                                                    >-->
<!--                                                        {{ $t("general.Cancel") }}-->
<!--                                                    </b-button>-->
<!--                                                </div>-->
<!--                                                <div class="row">-->
<!--                                                    <div class="col-md-6" v-if="isVisible('branch_id')">-->
<!--                                                        <div class="form-group">-->
<!--                                                            <label>{{ getCompanyKey("branch") }}</label>-->
<!--                                                            <multiselect @input="showBranchModalEdit"-->
<!--                                                                         v-model="edit.branch_id"-->
<!--                                                                         :options="branches.map((type) => type.id)"-->
<!--                                                                         :custom-label="-->
<!--                                                                        (opt) =>$i18n.locale == 'ar'-->
<!--                                                                                ? branches.find((x) => x.id == opt).name-->
<!--                                                                                : branches.find((x) => x.id == opt).name_e"-->
<!--                                                                         :class="{-->
<!--                                                                            'is-invalid':$v.edit.branch_id.$error || errors.branch_id,-->
<!--                                                                        }">-->
<!--                                                            </multiselect>-->
<!--                                                            <div v-if="!$v.edit.branch_id.required"-->
<!--                                                                 class="invalid-feedback">-->
<!--                                                                {{ $t("general.fieldIsRequired") }}-->
<!--                                                            </div>-->

<!--                                                            <template v-if="errors.branch_id">-->
<!--                                                                <ErrorMessage-->
<!--                                                                    v-for="(errorMessage, index) in errors.branch_id"-->
<!--                                                                    :key="index">{{ errorMessage }}-->
<!--                                                                </ErrorMessage>-->
<!--                                                            </template>-->
<!--                                                        </div>-->
<!--                                                    </div>-->
<!--                                                    <div class="col-md-6" v-if="isVisible('cm_member_id')">-->
<!--                                                        <div class="form-group position-relative">-->
<!--                                                            <label class="control-label">-->
<!--                                                                {{ getCompanyKey("member") }}-->
<!--                                                                <span v-if="isRequired('cm_member_id')" class="text-danger">*</span>-->
<!--                                                            </label>-->
<!--                                                            <multiselect-->
<!--                                                                v-model="edit.cm_member_id"-->
<!--                                                                :options="members.map((type) => type.id)"-->
<!--                                                                :custom-label="-->
<!--                                                                  (opt) => members.find((x) => x.id == opt).first_name +' '+ members.find((x) => x.id == opt).second_name-->
<!--                                                                     +' '+ members.find((x) => x.id == opt).third_name +' '+ members.find((x) => x.id == opt).last_name-->
<!--                                                                "-->
<!--                                                            >-->
<!--                                                            </multiselect>-->
<!--                                                            <div-->
<!--                                                                v-if="$v.edit.cm_member_id.$error || errors.cm_member_id"-->
<!--                                                                class="text-danger"-->
<!--                                                            >-->
<!--                                                                {{ $t("general.fieldIsRequired") }}-->
<!--                                                            </div>-->
<!--                                                            <template v-if="errors.cm_member_id">-->
<!--                                                                <ErrorMessage-->
<!--                                                                    v-for="(errorMessage, index) in errors.cm_member_id"-->
<!--                                                                    :key="index"-->
<!--                                                                >{{ errorMessage }}-->
<!--                                                                </ErrorMessage>-->
<!--                                                            </template>-->
<!--                                                        </div>-->
<!--                                                    </div>-->
<!--                                                    <div class="col-md-6" v-if="isVisible('year_from')">-->
<!--                                                        <div class="form-group">-->
<!--                                                            <label class="control-label">-->
<!--                                                                {{ getCompanyKey('year_from') }}-->
<!--                                                                <span v-if="isRequired('year_from')" class="text-danger">*</span>-->
<!--                                                            </label>-->
<!--                                                            <date-picker-->
<!--                                                                type="date"-->
<!--                                                                v-model="$v.edit.year_from.$model"-->
<!--                                                                @input="dateDifferenceEdit"-->
<!--                                                                format="YYYY-MM-DD"-->
<!--                                                                valueType="format"-->
<!--                                                                :confirm="false"-->
<!--                                                                :class="{ 'is-invalid':-->
<!--                                                                        $v.edit.year_from.$error ||-->
<!--                                                                        errors.year_from,-->
<!--                                                                    'is-valid':-->
<!--                                                                        !$v.edit.year_from-->
<!--                                                                            .$invalid &&-->
<!--                                                                        !errors.year_from,-->
<!--                                                                }"-->
<!--                                                            ></date-picker>-->
<!--                                                            <template v-if="errors.year_from">-->
<!--                                                                <ErrorMessage v-for="(errorMessage,index) in errors.year_from"-->
<!--                                                                              :key="index">-->
<!--                                                                    {{ errorMessage }}-->
<!--                                                                </ErrorMessage>-->
<!--                                                            </template>-->
<!--                                                        </div>-->
<!--                                                    </div>-->
<!--                                                    <div class="col-md-6" v-if="isVisible('year_to')">-->
<!--                                                        <div class="form-group">-->
<!--                                                            <label class="control-label">-->
<!--                                                                {{ getCompanyKey('year_to') }}-->
<!--                                                                <span  v-if="isRequired('year_to')" class="text-danger">*</span>-->
<!--                                                            </label>-->
<!--                                                            <date-picker-->
<!--                                                                type="date"-->
<!--                                                                v-model="$v.edit.year_to.$model"-->
<!--                                                                @input="dateDifferenceEdit"-->
<!--                                                                format="YYYY-MM-DD"-->
<!--                                                                valueType="format"-->
<!--                                                                :confirm="false"-->
<!--                                                                :class="{ 'is-invalid':-->
<!--                                                                        $v.edit.year_to.$error ||-->
<!--                                                                        errors.year_to,-->
<!--                                                                    'is-valid':-->
<!--                                                                        !$v.edit.year_to-->
<!--                                                                            .$invalid &&-->
<!--                                                                        !errors.year_to,-->
<!--                                                                }"-->
<!--                                                            ></date-picker>-->
<!--                                                            <template v-if="errors.year_to">-->
<!--                                                                <ErrorMessage v-for="(errorMessage,index) in errors.year_to"-->
<!--                                                                              :key="index">-->
<!--                                                                    {{ errorMessage }}-->
<!--                                                                </ErrorMessage>-->
<!--                                                            </template>-->
<!--                                                        </div>-->
<!--                                                    </div>-->
<!--                                                    <div class="col-md-6" v-if="isVisible('type')">-->
<!--                                                        <div class="form-group">-->
<!--                                                            <label  class="control-label">-->
<!--                                                                {{ getCompanyKey("subscription_type") }}-->
<!--                                                                <span v-if="isRequired('type')" class="text-danger">*</span>-->
<!--                                                            </label>-->
<!--                                                            <select  class="form-control"  v-model="$v.edit.type.$model" :class="{-->
<!--                                                                  'is-invalid': $v.edit.type.$error || errors.amount,-->
<!--                                                                  'is-valid':-->
<!--                                                                    !$v.edit.type.$invalid && !errors.amount,-->
<!--                                                                }">-->
<!--                                                                <option value="subscribe">{{$t('general.subscribe')}}</option>-->
<!--                                                                <option value="renew">{{$t('general.renew')}}</option>-->
<!--                                                            </select>-->

<!--                                                            <template v-if="errors.type">-->
<!--                                                                <ErrorMessage-->
<!--                                                                    v-for="(errorMessage, index) in errors.type"-->
<!--                                                                    :key="index"-->
<!--                                                                >{{ errorMessage }}-->
<!--                                                                </ErrorMessage>-->
<!--                                                            </template>-->
<!--                                                        </div>-->
<!--                                                    </div>-->
<!--                                                    <div class="col-md-6" v-if="isVisible('amount')">-->
<!--                                                        <div class="form-group">-->
<!--                                                            <label  class="control-label">-->
<!--                                                                {{ getCompanyKey("subscription_amount") }}-->
<!--                                                                <span  v-if="isRequired('amount')" class="text-danger">*</span>-->
<!--                                                            </label>-->
<!--                                                            <input-->
<!--                                                                type="number"-->
<!--                                                                step="any"-->
<!--                                                                class="form-control"-->
<!--                                                                v-model="$v.edit.amount.$model"-->
<!--                                                                :class="{-->
<!--                                                                  'is-invalid': $v.edit.amount.$error || errors.amount,-->
<!--                                                                  'is-valid':-->
<!--                                                                    !$v.edit.amount.$invalid && !errors.amount,-->
<!--                                                                }"-->
<!--                                                            />-->
<!--                                                            <template v-if="errors.amount">-->
<!--                                                                <ErrorMessage-->
<!--                                                                    v-for="(errorMessage, index) in errors.amount"-->
<!--                                                                    :key="index"-->
<!--                                                                >{{ errorMessage }}-->
<!--                                                                </ErrorMessage>-->
<!--                                                            </template>-->
<!--                                                        </div>-->
<!--                                                    </div>-->
<!--                                                </div>-->
<!--                                            </form>-->
<!--                                        </b-modal>-->
                                        <!--  /edit   -->
                                    </td>
                                    <td v-if="enabled3" class="do-not-print">
                                        <button
                                            @mousemove="log(data.id)"
                                            @mouseover="log(data.id)"
                                            type="button"
                                            class="btn"
                                            :id="`tooltip-${data.id}`"
                                            :data-placement="$i18n.locale == 'en' ? 'left' : 'right'"
                                            :title="Tooltip"
                                        >
                                            <i class="fe-info" style="font-size: 22px"></i>
                                        </button>
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
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>
