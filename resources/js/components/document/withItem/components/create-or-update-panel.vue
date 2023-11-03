<script>
import ErrorMessage from "../../../widgets/errorMessage";
import loader from "../../../general/loader";
import DatePicker from "vue2-datepicker";
import Multiselect from "vue-multiselect";
import translation from "../../../../helper/mixin/translation-mixin";
import {minValue, required, requiredIf} from "vuelidate/lib/validators";
import {formatDateOnly} from "../../../../helper/startDate";
import adminApi from "../../../../api/adminAxios";
import Swal from "sweetalert2";
import {dynamicSortString} from "../../../../helper/tableSort";
import PrintInvoice from "../print-invoice";
import FinancialDetails from "./financial-details";
import transactionBreak from "../../../create/receivablePayment/transactionBreak/transactionBreak";


/**
 * Advanced Table component
 */
export default {
    name: "createOrUpdatePanels",
    props: {
        document: {
            default: null,
        },
        document_id: {
            default: null,
        },
        dataRow:{
            default:null,
        },
        id: {
            default:'create'
        },
    },
    mixins: [translation],
    components: {
        ErrorMessage,
        loader,
        DatePicker,
        Multiselect,
        PrintInvoice,
        transactionBreak,
        FinancialDetails
    },
    data() {
        return {
            per_page_panel:25,
            financial_years_validate:true,
            customer_sub_category: "",
            customer_data_edit: "",
            customer_data_create: "",
            openingBreak:'',
            debounce: {},
            customers: [],
            salesmen: [],
            statuses: [],
            tasks: [],
            serial_number:"",
            relatedDocumentNumbers: [],
            externalSalesman: [],
            relatedDocuments: [],
            sellMethods: [],
            categories: [],
            countries: [],
            packages: [],
            governorates: [],
            faceNumbers: [{'A': 0,'B': 0,'Multi': 0,'One-Face': 0}],
            enabled3: true,
            isLoader: false,
            create: {
                document_id: this.document_id,
                document_module_type_id: this.document?this.document.document_module_type_id:null,
                document_status_id: null,
                reason:'',
                branch_id: null,
                date: this.formatDate(new Date()),
                related_document_id: null,
                related_document_number: null,
                related_document_prefix: '',
                sell_method_id: null,
                employee_id: null,
                customer_id: null,
                task_id: null,
                external_salesmen_id: null,
                external_commission:0,
                total_invoice: 0,
                invoice_discount: 0,
                net_invoice: 0,
                sell_method_discount: 0,
                unrelaized_revenue: 0,
                header_details: [{
                    category_id: null,
                    governorate_id: null,
                    package_id: null,
                    quantity: 0,
                    price_per_uint: 0,
                    total: 0,
                    unit_type: "Banners",
                    date_from: this.formatDate(new Date()),
                    rent_days:0,
                    date_to: this.formatDate(new Date()),
                    is_stripe: 0,
                    sell_method_id: null,
                    sell_method_discount: 0,
                    note:'',
                    package_note:'',
                    break_downs: []
                }]
            },
            errors: {},
            branches: [],
            isCheckAll: false,
            checkAll: [],
            is_disabled: false,
            company_id: null,
            Tooltip: "",
            mouseEnter: null,
            printLoading: true,
            date: new Date(),
            printObj: {
                id: "printReservation",
            },
            faces: ['A','B','Multi','One-Face'],
            cities: [{cities: []}],
            avenues: [{avenues: []}],
            streets: [{streets: []}],
            pans: [{pans: []}],
            pansPaginations: [{pansPaginations: {}}],
            locations: [{city_id: null, avenue_id: null, street_id: null, face: '', code: ''}],
            allPanelPackages: [{allPanelPackages: []}],
            panelPackages: [{panelPackages: []}],
            panelPackagesPaginatations: [{panelPackagesPaginatations: {}}],
            current_page_pans: [1],
            current_page_pans_packs: [1],
            CheckAllPanels: [[]],
            dataOfRow:''
        };
    },
    validations: {
        create: {
            document_id: { required },
            document_status_id: { required },
            reason:{ },
            branch_id: { required },
            date: { required },
            related_document_id: {  },
            related_document_number: {  },
            related_document_prefix: {  },
            sell_method_id: { required },
            employee_id: { required },
            customer_id: { required },
            task_id: {  },
            external_salesmen_id: {  },
            external_commission: {  },
            total_invoice: { required },
            invoice_discount: { required },
            net_invoice: { required },
            sell_method_discount: {  },
            unrelaized_revenue: {  },
            header_details: {
                required,
                $each: {
                    category_id: {},
                    governorate_id: {},
                    package_id: {},
                    quantity: { required, minValue: minValue(0) },
                    date_from: {required},
                    rent_days:{},
                    date_to: {required},
                    unit_type: {required},
                    price_per_uint: {required, minValue: minValue(0)},
                    total: {required},
                    is_stripe: {required},
                    sell_method_id: {},
                    sell_method_discount: {},
                    note: {},
                    package_note: {},
                    break_downs: {}
                }
            }
        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
        this.$store.dispatch('locationIp/getIp');
    },
    methods: {
       async resetModalCreateOrUpdate()
        {
            this.relatedDocuments = this.document.document_relateds;
            if (this.dataRow)
            {
               await this.getDataRow();
               await this.resetModalEdit(this.dataRow.id);
            }else{
                await this.resetModal();
            }
        },
        resetModalHiddenCreateOrUpdate ()
        {
            if (this.dataRow)
            {
                 this.resetModalHiddenEdit(this.dataRow.id);
            }else{
                 this.resetModalHidden();
            }
        },
        changeValue(index = null){
            let sum = 0;
            this.create.header_details.forEach(e => {
                sum += e.price_per_uint * e.quantity;
            });
            this.create.total_invoice = sum;
            this.create.net_invoice = sum - this.create.invoice_discount;
            if (this.create.sell_method_id)
            {
               let is_all_value = this.sellMethods.find((row) => this.create.sell_method_id == row.id) ? this.sellMethods.find((row) => this.create.sell_method_id == row.id).is_all_value : '';
               if (parseInt(is_all_value) == 0)
               {
                   this.create.unrelaized_revenue = this.create.net_invoice - this.create.sell_method_discount;
               }else {
                   this.create.sell_method_discount =0;
                   this.create.unrelaized_revenue = 0;
               }
            }

        },
        addNewField(){
            this.locations.push({city_id: null, avenue_id: null, street_id: null, face: '', code: ''});
            this.faceNumbers.push({'A': 0,'B': 0,'Multi': 0,'One-Face': 0});
            this.cities.push({cities: []});
            this.avenues.push({avenues: []});
            this.streets.push({streets: []});
            this.pans.push({pans: []});
            this.pansPaginations.push({pansPaginations: []});
            this.allPanelPackages.push({allPanelPackages: []});
            this.panelPackages.push({panelPackages: []});
            this.panelPackagesPaginatations.push({panelPackagesPaginatations: []});
            this.current_page_pans.push(1);
            this.current_page_pans_packs.push(1);
            this.CheckAllPanels.push([]);
            this.create.header_details.push({
                category_id: null,
                governorate_id: null,
                package_id:null,
                quantity: 0,
                price_per_uint: 0,
                date_from: this.formatDate(new Date()),
                rent_days:0,
                date_to: this.formatDate(new Date()),
                total: 0,
                unit_type: "Banners",
                is_stripe: 0,
                sell_method_id: null,
                sell_method_discount: 0,
                note: '',
                package_note: '',
                break_downs: []
            });
            this.changeValue();
        },
        removeNewField(index){
            if(this.create.header_details.length > 1){
                this.create.header_details.splice(index,1);
                this.cities.splice(index,1);
                this.avenues.splice(index,1);
                this.streets.splice(index,1);
                this.pans.splice(index,1);
                this.locations.splice(index,1);
                this.pansPaginations.splice(index,1);
                this.allPanelPackages.splice(index,1);
                this.panelPackages.splice(index,1);
                this.panelPackagesPaginatations.splice(index,1);
                this.current_page_pans.splice(index,1);
                this.current_page_pans_packs.splice(index,1);
                this.CheckAllPanels.splice(index,1);
                this.faceNumbers.splice(index,1);
            }
            this.changeValue();
        },
        /**
         *  data create (create)
         */
        dataCreate()
        {
            this.customer_sub_category = '';
            this.customer_data_edit = '';
            this.customer_data_create = '';
            this.serial_number = "";
            this.financial_years_validate = true;
            this.create = {
                document_id: this.document_id,
                document_module_type_id: this.document?this.document.document_module_type_id:null,
                document_status_id: parseInt(this.document.need_approve) == 0 ? 5 : 1,
                reason:'',
                branch_id: null,
                date: this.formatDate(new Date()),
                related_document_id: null,
                related_document_number: null,
                related_document_prefix: '',
                sell_method_id: null,
                employee_id: null,
                customer_id: null,
                task_id: null,
                external_salesmen_id: null,
                external_commission: 0,
                total_invoice: 0,
                invoice_discount: 0,
                net_invoice: 0,
                sell_method_discount: 0,
                unrelaized_revenue: 0,
                header_details: [{
                    category_id: null,
                    governorate_id: null,
                    package_id: null,
                    quantity: 0,
                    price_per_uint: 0,
                    total: 0,
                    unit_type: "Banners",
                    date_from: this.formatDate(new Date()),
                    rent_days:0,
                    date_to: this.formatDate(new Date()),
                    is_stripe: 0,
                    sell_method_id: null,
                    sell_method_discount: 0,
                    note: '',
                    package_note: '',
                    break_downs: []
                }]
            };
        },
        /**
         *  reset Modal (create)
         */
        resetModalHidden() {
            this.dataCreate();
            this.is_disabled = false;
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
        },
        /**
         *  hidden Modal (create)
         */
         async resetModal() {
            this.date = new Date();
            this.locations = [{city_id: null, avenue_id: null, street_id: null, face: '', code: ''}];
            this.cities= [{cities: []}];
            this.avenues= [{avenues: []}];
            this.streets = [{streets: []}];
            this.faceNumbers = [{'A': 0,'B': 0,'Multi': 0,'One-Face': 0}];
            this.pans = [{pans: []}];
            this.pansPaginations = [{pansPaginations: []}];
            this.allPanelPackages = [{allPanelPackages: []}];
            this.panelPackages = [{panelPackages: []}];
            this.panelPackagesPaginatations= [{panelPackagesPaginatations: []}];
            this.current_page_pans = [1];
            this.current_page_pans_packs = [1];
            this.CheckAllPanels = [[]];
            if (this.checkDocumentNeedApprove() )
            {
                 this.getStatus();
            }
            this.dataCreate();
            this.getBranches();
            this.getSalesmen();
            this.getExternalSalesmen();
            this.getSellmethod();
            this.getCategory();
            this.getGovernorate();
            this.getPackage();
            if(parseInt(this.document.required) == 1 && this.relatedDocuments.length == 1){
                this.create.related_document_id = this.relatedDocuments[0].id;
            }

            this.is_disabled = false;
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
        },
        /**
         *  create screen
         */
        resetForm() {
            this.locations = [{city_id: null, avenue_id: null, street_id: null, face: '', code: ''}];
            this.cities= [{cities: []}];
            this.avenues= [{avenues: []}];
            this.streets = [{streets: []}];
            this.faceNumbers = [{'A': 0,'B': 0,'Multi': 0,'One-Face': 0}];
            this.pans = [{pans: []}];
            this.pansPaginations = [{pansPaginations: []}];
            this.allPanelPackages = [{allPanelPackages: []}];
            this.panelPackages = [{panelPackages: []}];
            this.panelPackagesPaginatations= [{panelPackagesPaginatations: []}];
            this.current_page_pans = [1];
            this.current_page_pans_packs = [1];
            this.CheckAllPanels = [[]];
            this.dataCreate();
            this.date = new Date();
            this.start_date = new Date();
            this.end_date = new Date();
            this.is_disabled = false;
            this.$nextTick(() => {
                this.$v.$reset();
            });
        },
        Submit(is_break = false)
        {
            if (this.dataRow)
            {
                this.editSubmit(this.dataRow.id);
            }else{
                this.AddSubmit(is_break);
            }
        },
        AddSubmit(is_break = false) {
            if(parseInt(this.document.required) == 1 && !this.create.related_document_number){
                Swal.fire({
                    icon: "error",
                    title: `${this.$t("general.Error")}`,
                    text: `${this.$t("general.PleaseChooseRelatedSerialNumber")}`,
                });
                return 0;
            }
            this.$v.create.$touch();
            if (this.$v.create.$invalid || !this.financial_years_validate) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                this.is_disabled = false;
                this.create.header_details.forEach((detail,index) => {
                    if (detail.is_stripe == 0) {
                        this.CheckAllPanels[index].forEach((panel) => {
                            detail.break_downs.push({
                                'date_from': detail.date_from,
                                'date_to': detail.date_to,
                                'module_type': 'panels',
                                'break_id': panel,
                            })
                        });
                    }
                });
                if (this.document.attributes && parseInt(this.document.attributes.customer) != 0)
                {
                    if (!is_break){
                        this.create.is_break = 1;
                    }else {
                        this.create.is_break = 0;
                    }

                }else{
                    this.create.is_break = 0;
                }
                this.create.company_id = this.company_id;
                adminApi.post(`document-headers`, {...this.create})
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
                        if(this.document.attributes && parseInt(this.document.attributes.customer) != 0 && is_break == true)
                        {
                            this.create.id = res.data.data.id;
                            this.showBreakCreate();
                        }
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
            if(parseInt(this.document.required) == 1 && ! this.create.related_document_number){
                Swal.fire({
                    icon: "error",
                    title: `${this.$t("general.Error")}`,
                    text: `${this.$t("general.PleaseChooseRelatedSerialNumber")}`,
                });
                return 0;
            }
            this.$v.create.$touch();

            if (this.$v.create.$invalid || !this.financial_years_validate) {
                return;
            } else {
                this.create.company_id = this.company_id;
                this.isLoader = true;
                this.errors = {};
                this.create.header_details.forEach((detail,index) => {
                    if (detail.is_stripe == 0) {
                        this.CheckAllPanels[index].forEach((panel) => {
                            detail.break_downs.push({
                                'date_from': detail.date_from,
                                'date_to': detail.date_to,
                                'module_type': 'panels',
                                'break_id': panel,
                            })
                        });
                    }
                });
                adminApi
                    .put(`document-headers/${id}`, {
                        ...this.create
                    })
                    .then((res) => {
                        this.$emit("created");
                        setTimeout(() => {
                            Swal.fire({
                                icon: "success",
                                text: `${this.$t("general.Editsuccessfully")}`,
                                showConfirmButton: false,
                                timer: 1500,
                            });
                        }, 500);
                        if(this.document.attributes && parseInt(this.document.attributes.customer) != 0)
                        {
                            this.showBreakCreate();
                        }
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
         getStatus(){
            this.isLoader = true;

             adminApi
                .get(`/document-statuses`)
                .then((res) => {
                    let l = res.data.data;
                    this.statuses = l;
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
         getBranches() {
            this.isLoader = true;
             adminApi
                .get(`/branches?document_id=${this.document_id}`)
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
         getSerialNumber(e)
        {
            let date = this.create.date;
            let shortYear =new Date(date).getFullYear();
            let twoDigitYear = shortYear.toString().substr(-2);
            let branch = this.branches.find((row) => e == row.id);
            let serial = branch.serials.find((row) => this.document_id == row.document_id);
            this.serial_number = `${twoDigitYear}-${branch.id}-${this.document_id}-${serial.perfix}`;

            let document_id = this.create.related_document_id;
            let branch_id = this.create.branch_id;
            if (document_id && branch_id)
            {
                 this.handelRelatedDocument()
            }
        },

         getSellmethod() {
            this.isLoader = true;
             adminApi
                .get(`/boards-rent/sell-methods?company_id=${this.company_id}`)
                .then((res) => {
                    let l = res.data.data;
                    this.sellMethods = l;
                    let sell_method = l.find((e) => parseInt(e.is_default) == 1);
                    if(sell_method && !this.dataRow)
                    {
                        this.create.sell_method_id = sell_method.id;
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
         getSalesmen() {
            this.isLoader = true;
             adminApi
                .get(`/employees/get-drop-down?is_salesman=1&customer_handel=1`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
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
         getCustomerSalesman(e)
        {
            let employee_id = e;
            if (employee_id)
            {
                 this.getCustomers(employee_id);
            }
        },

         getCustomers(employee_id=null,search='') {
            this.isLoader = true;
             adminApi
                .get(`/general-customer?limet=10&company_id=${this.company_id}&employee_id=${employee_id}&search=${search}&columns[0]=name&columns[1]=name_e&columns[2]=id`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    this.customers = l;
                    if (this.customer_data_edit)
                    {
                        if (!this.customers.find((e) => e.id == this.customer_data_edit.id))
                        {
                            this.customers.push(this.customer_data_edit);
                        }
                    }
                    if (this.customer_data_create)
                    {
                        if (!this.customers.find((e) => e.id == this.customer_data_create.id))
                        {
                            this.customers.push(this.customer_data_create);
                        }
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
         getTasksCreate()
        {
            this.customer_sub_category = '';
            if (this.create.customer_id&&this.create.employee_id)
            {
                this.customer_sub_category=this.customers.find((e) => e.id == this.create.customer_id) ? this.customers.find((e) => e.id == this.create.customer_id).customer_sub_category.name :'';
                this.customer_data_create = this.customers.find((e) => e.id == this.create.customer_id);
                 this.getTaskNumber(this.create.customer_id,this.create.employee_id);
            }
        },
         getTaskNumber(customer_id,employee_id) {
            this.isLoader = true;
             adminApi
                .get(`/tasks?customer_id=${customer_id}&employee_id=${employee_id}`)
                .then((res) => {
                    let l = res.data.data;
                    this.tasks = l;
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
         getExternalSalesmen() {
            this.isLoader = true;
             adminApi
                .get(`/external-salesmen?company_id=${this.company_id}`)
                .then((res) => {
                    let l = res.data.data;
                    this.externalSalesman = l;
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
         getCategory(index = null) {
            this.isLoader = true;
             adminApi
                .get(`/categories`)
                .then((res) => {
                    let l = res.data.data;
                    this.categories = l;
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
         getPackage() {
            this.isLoader = true;
             adminApi
                .get(`/boards-rent/packages`)
                .then((res) => {
                    let l = res.data.data;
                    this.packages = l;
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
         getGovernorate() {
            this.isLoader = true;
             adminApi
                .get(`/governorates`)
                .then((res) => {
                    let l = res.data.data;
                    this.governorates = l;
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
         getCity(index) {
            this.isLoader = true;
            let governorate = this.create.header_details[index].governorate_id;
            this.locations[index].city_id = null;
            this.locations[index].avenue_id = null;
            this.locations[index].street_id = null;
            this.cities[index].cities = [];
            this.avenues[index].avenues = [];
            this.streets[index].streets = [];

             adminApi
                .get(`/cities?columns[0]=governorate.id&search=${governorate}`)
                .then((res) => {
                    let l = res.data.data;
                    this.cities[index].cities = l;
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
         getAvnue(index) {
            this.isLoader = true;
            let city = this.locations[index].city_id;
            this.locations[index].avenue_id = null;
            this.locations[index].street_id = null;
            this.avenues[index].avenues = [];
            this.streets[index].streets = [];

             adminApi
                .get(`/avenues?columns[0]=city.id&search=${city}`)
                .then((res) => {
                    let l = res.data.data;
                    this.avenues[index].avenues = l;
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
         getStreet(index) {
            this.isLoader = true;
            let avenue = this.locations[index].avenue_id;
            this.locations[index].street_id = null;
            this.streets[index].streets = [];

             adminApi
                .get(`/streets?columns[0]=avenue.id&search=${avenue}`)
                .then((res) => {
                    let l = res.data.data;
                    this.streets[index].streets = l;
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
         getPanel(index,page=1) {
            this.isLoader = true;
            let date_from = this.create.header_details[index].date_from;
            let date_to = this.create.header_details[index].date_to;
            let category_id = this.create.header_details[index].category_id;
            let governorate_id = this.create.header_details[index].governorate_id;
            let city_id = this.locations[index].city_id;
            let avenue_id = this.locations[index].avenue_id;
            let face = this.locations[index].face;
            let street_id = this.locations[index].street_id;
            let code = this.locations[index].code;

             adminApi
                .get(
                    `/boards-rent/panels/filter-panel?date_from=${date_from}&date_to=${date_to}&page=${page}&per_page=${this.per_page_panel}&category_id=${category_id}&governorate_id=${governorate_id}&city_id=${city_id}&avenue_id=${avenue_id}&street_id=${street_id}&face=${face}&code=${code}`
                )
                .then((res) => {
                    let l = res.data;
                    this.pans[index].pans = l.data;
                    this.pansPaginations[index].pansPaginations = l.pagination;
                    this.current_page_pans[index] = l.pagination.current_page;
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
         getPanelPagination(index) {

            if (
                this.current_page_pans[index] <= this.pansPaginations[index].pansPaginations.last_page &&
                this.current_page_pans[index] != this.pansPaginations[index].pansPaginations.current_page &&
                this.current_page_pans[index]
            ) {
                this.isLoader = true;
                let category_id = this.location[index].category_id ?? null;
                let governorate_id = this.location[index].governorate_id ?? null;
                let city_id = this.location[index].city_id ?? null;
                let avenue_id = this.location[index].avenue_id ?? null;
                let face = this.location[index].face ?? null;
                let street_id = this.location[index].street_id ?? null;
                let code = this.location[index].code ?? null;

                 adminApi
                    .get(
                        `/boards-rent/panels/filter-panel?page=${1}&per_page=${7}&packages=1&category_id=${category_id}&governorate_id=${governorate_id}&city_id=${city_id}&avenue_id=${avenue_id}&street_id=${street_id}&face=${face}&code=${code}`
                    )
                    .then((res) => {
                        let l = res.data;
                        this.pans[index].pans = l.data;
                        this.pansPaginations[index].pansPaginations = l.pagination;
                        this.current_page_pans[index] = l.pagination.current_page;
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
         *   show Modal (edit)
         */
        async resetModalEdit(id) {
            this.customer_sub_category = '';
            this.customer_data_edit = '';
            this.customer_data_create = '';
            this.isLoader = true;
            this.financial_years_validate = true;

            if (this.checkDocumentNeedApprove()) {
                 this.getStatus();
            }

             this.getBranches();
             this.getSalesmen();
             this.getSellmethod();
             this.getExternalSalesmen();

             this.getPackage();
             this.getGovernorate();
             this.getCategory();

            this.locations = [];
            this.cities = [];
            this.avenues = [];
            this.streets = [];
            this.pans = [];
            this.pansPaginations = [];
            this.allPanelPackages = [];
            this.panelPackages = [];
            this.faceNumbers = [];
            this.panelPackagesPaginatations= [];
            this.current_page_pans = [];
            this.current_page_pans_packs = [];
            this.CheckAllPanels = [];
            let reservation =this.dataOfRow;
            this.customer_data_edit = reservation.customer;
             this.getCustomers(reservation.employee_id);
            this.date = new Date(reservation.date);
            this.serial_number = reservation.prefix;
            this.create.document_status_id = reservation.document_status_id;
            this.create.reason = reservation.reason??'';
            this.create.branch_id = reservation.branch_id;
            this.create.date = reservation.date;
            this.create.related_document_id = reservation.related_document_id;
            if(reservation.related_document_id)
            {
                 this.handelRelatedDocument();
            }

            this.create.sell_method_id = reservation.sell_method_id;
            this.create.id = reservation.id;
            this.create.customer_id = reservation.customer_id;
            this.create.employee_id = reservation.employee_id;
             this.getTasksCreate();
            this.create.external_salesmen_id = reservation.external_salesmen_id;
            this.create.external_commission = reservation.external_commission;
            this.create.total_invoice = reservation.total_invoice;
            this.create.invoice_discount = reservation.invoice_discount;
            this.create.net_invoice = reservation.net_invoice;
            this.create.sell_method_discount = reservation.sell_method_discount;
            this.create.unrelaized_revenue = reservation.unrelaized_revenue;
            this.create.related_document_number = reservation.related_document_number;
            this.create.related_document_prefix = reservation.related_document_prefix;
            this.create.task_id = reservation.task_id;
            this.customer_sub_category=reservation.customer?reservation.customer.customer_sub_category?reservation.customer.customer_sub_category.name:'':'';
            this.create.header_details = [];
            if(reservation.document_number)
            {
              this.relatedDocumentNumbers.push(reservation.document_number);
            }
            let header_details =  reservation.header_details?reservation.header_details:[];
            header_details.forEach((e,index) => {
                this.locations.push({city_id: null, avenue_id: null, street_id: null, face: '', code: ''});
                this.faceNumbers.push({'A': 0,'B': 0,'Multi': 0,'One-Face': 0});
                this.cities.push({cities: []});
                this.avenues.push({avenues: []});
                this.streets.push({streets: []});
                this.pans.push({pans: []});
                this.pansPaginations.push({pansPaginations: []});
                this.allPanelPackages.push({allPanelPackages: []});
                this.panelPackages.push({panelPackages: []});
                this.panelPackagesPaginatations.push({panelPackagesPaginatations: []});
                this.current_page_pans.push(1);
                this.current_page_pans_packs.push(1);
                this.CheckAllPanels.push([]);
                if (parseInt(e.is_stripe) == 0)
                {
                    e.break_downs.forEach((el,i) => {
                        this.allPanelPackages[index].allPanelPackages.push(el.panel);
                        this.panelPackages[index].panelPackages.push(el.panel);
                        this.CheckAllPanels[index].push(el.panel.id);
                    });
                }
                setTimeout( () => {
                    this.create.header_details.push({
                        category_id: e.category_id,
                        governorate_id: e.governorate_id,
                        package_id: e.package_id,
                        date_from: this.formatDate(e.date_from),
                        rent_days:e.rent_days,
                        date_to: this.formatDate(e.date_to),
                        unit_type: e.unit_type,
                        quantity: e.quantity,
                        price_per_uint: e.price_per_uint,
                        total: e.total,
                        is_stripe: e.is_stripe,
                        sell_method_id: e.sell_method_id,
                        sell_method_discount: e.sell_method_discount,
                        note: e.note,
                        package_note:parseInt(e.is_stripe) == 1 ? this.packages.find(el => el.id == e.package_id)?this.packages.find(el => el.id == e.package_id).note:'':'',
                        break_downs: []
                    });
                },10);

                this.paginate(1,index);
                this.changeValuePanel(index);
            });
            this.isLoader = false;
        },
        /**
         *  hidden Modal (edit)
         */
        resetModalHiddenEdit(id) {
            this.customer_sub_category = '';
            this.customer_data_edit = '';
            this.errors = {};
            this.locations = [{city_id: null, avenue_id: null, street_id: null, face: '', code: ''}];
            this.cities= [{cities: []}];
            this.avenues= [{avenues: []}];
            this.streets = [{streets: []}];
            this.pans = [{pans: []}];
            this.pansPaginations = [{pansPaginations: []}];
            this.allPanelPackages = [{allPanelPackages: []}];
            this.panelPackages = [{panelPackages: []}];
            this.panelPackagesPaginatations= [{panelPackagesPaginatations: []}];
            this.current_page_pans = [1];
            this.current_page_pans_packs = [1];
            this.CheckAllPanels = [[]];
            this.create = {
                document_id: this.document_id,
                document_status_id: null,
                id: null,
                reason:'',
                branch_id: null,
                date: this.formatDate(new Date()),
                related_document_id: null,
                related_document_number: null,
                related_document_prefix: '',
                sell_method_id: null,
                employee_id: null,
                customer_id: null,
                task_id: null,
                external_salesmen_id: null,
                external_commission: 0,
                total_invoice: 0,
                invoice_discount: 0,
                net_invoice: 0,
                sell_method_discount: 0,
                unrelaized_revenue: 0,
                header_details: [],
            };
        },
        async getDataRow(){
          this.isLoader = true;
         await adminApi
              .get(`/document-headers/${this.id}`)
              .then((res) => {
                let l = res.data.data;
                this.dataOfRow = l;
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
        /**
         *  start  dynamicSortString
         */

        formatDate(value) {
            return formatDateOnly(value);
        },

        showPanel(index){this.$bvModal.show(`create-panel-quotation-${index}`);},
        changeValuePanel (index){
            this.faceNumbers[index] = {'A': 0,'B': 0,'Multi': 0,'One-Face': 0};
            this.allPanelPackages[index].allPanelPackages.forEach((e) => {
                if(e.face == 'A'){
                    this.faceNumbers[index].A = this.faceNumbers[index].A + 1;
                }else if(e.face == 'B'){
                    this.faceNumbers[index].B = this.faceNumbers[index].B + 1;
                }else if(e.face == 'Multi'){
                    this.faceNumbers[index].Multi = this.faceNumbers[index].Multi + 1;
                }else if(e.face == 'One-Face'){
                    this.faceNumbers[index]['One-Face'] = this.faceNumbers[index]['One-Face'] + 1;
                }
            });
        },
        checkRowPanel(index,data) {

            let panel = this.allPanelPackages[index].allPanelPackages.find((el) => el.id == data.id);
            if (!panel) {
                this.allPanelPackages[index].allPanelPackages.push(data);
            } else {
                let indexPanel = this.allPanelPackages[index].allPanelPackages.findIndex((el) => el.id == data.id);
                this.allPanelPackages[index].allPanelPackages.splice(indexPanel, 1);
            }
            this.paginate(
                this.panelPackagesPaginatations[index].panelPackagesPaginatations.currentPage ?
                    this.panelPackagesPaginatations[index].panelPackagesPaginatations.currentPage : 1,index
            );
            this.changeValuePanel(index);

        },
        // paginate
        paginate(p = 1,index){
            const page = p;
            this.current_page_pans_packs[index] = page;
            const limit = this.per_page_panel;
            const skip = (page - 1) * limit;
            const endIndex = page * limit;
            const total = this.allPanelPackages[index].allPanelPackages.length;
            // Pagination result
            this.panelPackagesPaginatations[index].panelPackagesPaginatations.total = total;
            this.panelPackagesPaginatations[index].panelPackagesPaginatations.limit = limit;
            this.panelPackagesPaginatations[index].panelPackagesPaginatations.last_page = Math.ceil(total / limit);
            this.panelPackages[index].panelPackages = [];
            this.panelPackages[index].panelPackages = this.allPanelPackages[index].allPanelPackages.slice(skip,skip+limit);

            this.panelPackagesPaginatations[index].panelPackagesPaginatations.to = skip? (skip + limit) : limit;
            this.panelPackagesPaginatations[index].panelPackagesPaginatations.from = skip ? skip : 1;
            this.panelPackagesPaginatations[index].panelPackagesPaginatations.currentPage = page;
        },
        async showCityModal(index) {
            await this.getAvnue(index);
        },
        async showAvenueModal(index) {
            await this.getStreet(index);
        },
        async showStreetModal(index) {
           await this.getPanel(index);
        },
        async showGovernorateModal(index) {
            await this.getCity(index);
        },
        showPackageModal(index) {
             if (this.create.header_details[index].package_id) {
                this.create.header_details[index].price_per_uint = this.packages.find(el => el.id == this.create.header_details[index].package_id).price;
                this.create.header_details[index].package_note = this.packages.find(el => el.id == this.create.header_details[index].package_id).note;
                this.create.header_details[index].category_id = this.packages.find(el => el.id == this.create.header_details[index].package_id).category_id;
                this.create.header_details[index].governorate_id = this.packages.find(el => el.id == this.create.header_details[index].package_id).governorate_id;
                this.totalCreate(index);
            }
        },
        async resetModalPanel(index) {
            this.locations[index] = {city_id: null, avenue_id: null, street_id: null, face: '', code: ''};
            await this.getPanel(index);
        },
        totalCreate(index)
        {
            this.create.header_details[index].total = this.create.header_details[index].quantity * this.create.header_details[index].price_per_uint;
            this.changeValue();
        },
        PricePerUnitByTotal(index)
        {
            this.create.header_details[index].price_per_uint = this.create.header_details[index].total / this.create.header_details[index].quantity;
            this.changeValue();
        },
        disableCheckAll(index)
        {
            let quantity = this.create.header_details[index].quantity;
            if(quantity > this.allPanelPackages[index].allPanelPackages.length)
            {
                return false;
            }else {
                return true;
            }
        },
        changeStrip(index)
        {
            this.create.header_details[index].category_id = null;
            this.create.header_details[index].governorate_id = null;
            this.create.header_details[index].package_id = null;
            this.create.header_details[index].quantity = this.create.header_details[index].is_stripe == 0 ? 0 : 1;
            this.create.header_details[index].price_per_uint = 0;
            this.create.header_details[index].total = 0;
            this.create.header_details[index].unit_type = "Banners";
            this.create.header_details[index].date_from = this.formatDate(new Date());
            this.create.header_details[index].rent_days = 0;
            this.create.header_details[index].date_to = this.formatDate(new Date());
            this.create.header_details[index].break_downs = [];
            this.allPanelPackages[index].allPanelPackages=[];
            this.CheckAllPanels[index]=[];
            this.faceNumbers[index]={'A': 0,'B': 0,'Multi': 0,'One-Face': 0};

        },
        async searchCustomer(e) {
            let search = e??'';
            clearTimeout(this.debounce);
            this.debounce = setTimeout(() => {
                this.getCustomers(this.create.employee_id,search);
            }, 500);

        },
        checkDocumentNeedApprove()
        {
            if(this.document)
            {
                if(parseInt(this.document.need_approve) == 1)
                {
                    return true;
                }
            }
            return false;
        },
        checkDocumentLinked()
        {
            if(this.document)
            {
                if(this.document.document_Relateds.length > 0)
                {
                    return true;
                }
            }
            return false;
        },
        titleModelName()
        {
            if(this.document) {
                if (!this.dataRow)
                {
                    return `${this.$t('general.addNew')} ${this.$i18n.locale == 'ar'?this.document.name:this.document.name_e} `;
                }else {
                    return `${this.$t('general.Edit')} ${this.$i18n.locale == 'ar'?this.document.name:this.document.name_e} `;
                }
            }
        },
        async changeDateDocument()
        {
            let date = this.create.date;
            let branch_id = this.create.branch_id;
            await this.checkFinancialYears(date,branch_id);
        },
        async checkFinancialYears(date,branch_id) {
            this.isLoader = true;
            await adminApi
                .get(`/document-headers/check-date?date=${date}`)
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
                        this.serial_number = '';
                    }else{
                        this.financial_years_validate= true;
                        let shortYear =new Date(date).getFullYear();
                        let twoDigitYear = shortYear.toString().substr(-2);
                        let branch = this.branches.find((row) => branch_id == row.id);
                        let serial = branch.serials.find((row) => this.document_id == row.document_id);
                        this.serial_number = `${twoDigitYear}-${branch.id}-${this.document_id}-${serial.perfix}`;
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
        async handelRelatedDocument()
        {
            let related_document_id = this.create.related_document_id;
            let document_id = this.document_id;
            let branch_id = this.create.branch_id;
            await this.getRelatedDocument(related_document_id,branch_id,document_id);
        },
         getRelatedDocument(related_document_id,branch_id,document_id)
        {
            this.isLoader = true;
             adminApi
                .get(`/document-headers/check-related-document?related_document_id=${related_document_id}&document_id=${document_id}&branch_id=${branch_id}`)
                .then((res) => {
                    let l = res.data.data;
                    this.relatedDocumentNumbers = l;
                    if (this.dataOfRow)
                    {
                      this.relatedDocumentNumbers.push(this.dataOfRow.document_number);
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

        RelatedDocumentData()
        {
            let related_document_number = this.create.related_document_number;
            if(related_document_number)
            {
                this.displayDataCreate(related_document_number)
            }
        },
        displayDataCreate(related_document_number)
        {
            this.isLoader = true;
            let relatedDocument = this.relatedDocumentNumbers.find(el => el.id == related_document_number);
            this.create.header_details = [];
            this.customer_data_edit = relatedDocument.customer;
            this.customer_data_create = relatedDocument.customer;
            this.getCustomers(relatedDocument.employee_id);
            this.locations = [];
            this.cities = [];
            this.avenues = [];
            this.streets = [];
            this.pans = [];
            this.pansPaginations = [];
            this.allPanelPackages = [];
            this.panelPackages = [];
            this.faceNumbers = [];
            this.panelPackagesPaginatations= [];
            this.current_page_pans = [];
            this.current_page_pans_packs = [];
            this.CheckAllPanels = [];

            this.create.related_document_prefix = relatedDocument.serial_number;
            this.create.sell_method_id = relatedDocument.sell_method_id;
            this.create.customer_id = relatedDocument.customer_id;
            this.create.employee_id = relatedDocument.employee_id;
            this.getTasksCreate();

            this.create.external_salesmen_id = relatedDocument.external_salesmen_id;
            this.create.external_commission = relatedDocument.external_commission;
            this.create.total_invoice = relatedDocument.total_invoice;
            this.create.invoice_discount = relatedDocument.invoice_discount;
            this.create.net_invoice = relatedDocument.net_invoice;
            this.create.sell_method_discount = relatedDocument.sell_method_discount;
            this.create.unrelaized_revenue = relatedDocument.unrelaized_revenue;
            this.create.task_id = relatedDocument.task_id;

            relatedDocument.header_details.forEach((e,index) => {
                this.pansPaginations.push({pansPaginations: []});
                this.allPanelPackages.push({allPanelPackages: []});
                this.panelPackages.push({panelPackages: []});
                this.panelPackagesPaginatations.push({panelPackagesPaginatations: []});
                this.current_page_pans.push(1);
                this.current_page_pans_packs.push(1);
                this.CheckAllPanels.push([]);
                this.locations.push({city_id: null, avenue_id: null, street_id: null, face: '', code: ''});
                this.faceNumbers.push({'A': 0,'B': 0,'Multi': 0,'One-Face': 0});
                this.cities.push({cities: []});
                this.avenues.push({avenues: []});
                this.streets.push({streets: []});
                this.pans.push({pans: []});

                if (parseInt(e.is_stripe) == 0)
                {
                    e.break_downs.forEach((el,i) => {
                        this.allPanelPackages[index].allPanelPackages.push(el.panel);
                        this.panelPackages[index].panelPackages.push(el.panel);
                        this.CheckAllPanels[index].push(el.panel.id);
                    });
                }
                this.create.header_details.push({
                    category_id: e.category_id,
                    governorate_id: e.governorate_id,
                    package_id: e.package_id,
                    date_from: this.formatDate(e.date_from),
                    rent_days: e.rent_days,
                    date_to: this.formatDate(e.date_to),
                    unit_type: e.unit_type,
                    quantity: e.quantity,
                    price_per_uint: e.price_per_uint,
                    total: e.total,
                    is_stripe: e.is_stripe,
                    sell_method_id: e.sell_method_id,
                    sell_method_discount: e.sell_method_discount,
                    note: e.note,
                    package_note:parseInt(e.is_stripe) == 1 ? this.packages.find(el => el.id == e.package_id)?this.packages.find(el => el.id == e.package_id).note:'':'',
                    break_downs: []
                });
                if (parseInt(e.is_stripe) == 0)
                {
                    this.paginate(1,index);
                    this.changeValuePanel(index);
                }
            });
            this.isLoader = false;
        },

        checkIsAllValue()
        {
            let sell_method_id = this.create.sell_method_id;
            if(sell_method_id)
            {
                let is_all_value = this.sellMethods.find((row) => sell_method_id == row.id) ? this.sellMethods.find((row) => sell_method_id == row.id).is_all_value : '';
                if (parseInt(is_all_value) == 1)
                {
                    this.create.sell_method_discount = 0;
                }
                this.changeValue();
                if (parseInt(is_all_value) == 0)
                {
                    return true;
                }else {
                    return false;
                }
            }else {
                return false;
            }
        },
        calcDateTo(index)
        {
           let days = parseInt(this.create.header_details[index].rent_days);
           let date_from = new Date(this.create.header_details[index].date_from);
            date_from.setDate(date_from.getDate() + days);
            this.create.header_details[index].date_to = new Date(date_from).toISOString().slice(0, 10);
        },
        checkIsAllValueDetail()
        {
            this.create.sell_method_discount = 0;
            this.create.header_details.forEach((el,i) => {
                this.create.sell_method_discount += parseFloat(el.sell_method_discount);
            });
        },
        showBreakCreate(){
            if (this.create.id) {

                this.openingBreak = {
                    customer_id: this.create.customer_id,
                    currency_id: 1,
                    document_id: this.document_id,
                    debit: (this.document.attributes && parseInt(this.document.attributes.customer) == 1)?this.create.net_invoice:0,
                    credit: (this.document.attributes && parseInt(this.document.attributes.customer) == -1)?this.create.net_invoice:0,
                    date: this.create.date,
                    rate: 1,
                    id: this.create.id,
                    break_type: 'documentHeader',
                    is_update: this.dataRow ? true : false,
                };
                this.$bvModal.show("opening-balance-break-create");
            }
        },
        addLineEnter(e)
        {
            const keyCode = e.keyCode;
            if (keyCode === 13)
            {
                this.addNewField();
            }
        },
    }
};
</script>

<template>
    <div>
        <div v-if="dataOfRow" style="display:none;">
            <PrintInvoice :document_data="dataOfRow"/>
        </div>
        <div v-if="dataOfRow" style="display:none;">
            <FinancialDetails :id="id" :document="document" :data="dataOfRow" :categories="categories" :governorates="governorates" :packages="packages" />
        </div>
        <transactionBreak :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :opening="openingBreak"/>
        <!--  create   -->
        <b-modal dialog-class="modal-full-width" :id="id"
                 :title="titleModelName()"
                 title-class="font-18" body-class="p-4 "
                 :hide-footer="true"
                 @show="resetModalCreateOrUpdate"
                 @hidden="resetModalHiddenCreateOrUpdate">
            <form>
                <div class="row" >
                    <div v-if="checkDocumentNeedApprove()" class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group position-relative">
                                    <label class="control-label">
                                        {{ $t('general.Status') }}
                                    </label>
                                    <multiselect v-model="create.document_status_id"
                                                 :show-labels="false"
                                                 :options="statuses.map((type) => type.id)"
                                                 :custom-label="(opt) => statuses.find((x) => x.id == opt)?$i18n.locale == 'ar'? statuses.find((x) => x.id == opt).name : statuses.find((x) => x.id == opt).name_e:''">
                                    </multiselect>
                                    <template v-if="errors.document_status_id">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.document_status_id" :key="index">{{ errorMessage }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group position-relative">
                                    <label class="control-label">
                                        {{$t('general.reason')}}
                                    </label>
                                    <input
                                        type="text"
                                        class="form-control englishInput"
                                        v-model="$v.create.reason.$model"
                                        :class="{
                                            'is-invalid': $v.create.reason.$error || errors.reason,
                                            'is-valid': !$v.create.reason.$invalid && !errors.reason,
                                          }"
                                    />
                                    <template v-if="errors.reason">
                                        <ErrorMessage v-for="(errorMessage, index) in errors.reason" :key="index">{{ errorMessage }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div :class="[ checkDocumentNeedApprove() ? 'col-md-6' : 'col-md-12','mb-3 d-flex justify-content-end',]">
                        <b-button v-if="dataRow && document && document.attributes && (parseInt(document.attributes.commission) != 0 || parseInt(document.attributes.unrealized_commission) != 0 || parseInt(document.attributes.revenue) != 0 || parseInt(document.attributes.unrealized_revenue) != 0 )" @click="$bvModal.show(`financial-details-${id}`)" variant="purple" type="button" class="font-weight-bold px-2 mx-1">
                            {{ $t("general.FinancialDetails") }}
                            <i class="fas fa-dollar-sign"></i>
                        </b-button>
                        <b-button v-if="!dataRow" variant="primary" type="button" class="font-weight-bold px-2 mx-1">
                            {{ $t("general.print") }}
                            <i class="fe-printer"></i>
                        </b-button>
                        <b-button v-else v-print="'#printReservation'" variant="primary" type="button" class="font-weight-bold px-2 mx-1">
                            {{ $t("general.print") }}
                            <i class="fe-printer"></i>
                        </b-button>
                        <b-button variant="primary" type="button" class="font-weight-bold px-2 mx-1">
                            {{ $t("general.printBannerGoogleMap") }}
                            <i class="fe-printer"></i>
                        </b-button>
                        <b-button v-if="!dataRow" variant="success" :disabled="!is_disabled" @click.prevent="resetForm" type="button"
                                  :class="['font-weight-bold px-2 mx-1', is_disabled ? 'mx-2' : '']">
                            {{ $t("general.AddNewRecord") }}
                        </b-button>
                        <template v-if="!is_disabled">
                            <a class="btn btn-success mx-1" v-if="!isLoader" @click.prevent="Submit(false)">
                                {{ dataRow ? $t('general.Edit') : $t("general.Add") }}
                            </a>
                            <b-button variant="success" class="mx-1" disabled v-else>
                                <b-spinner small></b-spinner>
                                <span class="sr-only">{{ $t("login.Loading") }}...</span>
                            </b-button>

                        </template>
                        <b-button variant="danger" type="button" @click.prevent="$bvModal.hide(`${id}`)">
                            {{ $t("general.Cancel") }}
                        </b-button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">{{ $t('general.Branch') }} <span class="text-danger">*</span></label>

                            <multiselect
                                :show-labels="false"
                                @input="getSerialNumber($event)"
                                v-model="create.branch_id"
                                :options="branches.map((type) => type.id)"
                                :custom-label="(opt) => branches.find((x) => x.id == opt)? $i18n.locale == 'ar'? branches.find((x) => x.id == opt).name: branches.find((x) => x.id == opt).name_e:''"
                                :class="{'is-invalid': $v.create.branch_id.$error || errors.branch_id,}"
                            >
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
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">
                                {{ $t('general.Date') }}
                                <span class="text-danger">*</span>
                            </label>
                            <date-picker
                                :disabled="!create.branch_id"
                                @input="changeDateDocument()"
                                type="date"
                                v-model="create.date"
                                format="YYYY-MM-DD"
                                valueType="format"
                                :confirm="false"
                                :class="{'is-invalid': !financial_years_validate}"

                            ></date-picker>
                            <div v-if="!financial_years_validate" class="invalid-feedback">
                                {{ $t("general.The date is outside the permitted fiscal year") }}
                            </div>
                            <template v-if="errors.date">
                                <ErrorMessage v-for="(errorMessage, index) in errors.date" :key="index">
                                    {{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">
                                {{$t('general.serial_number')}}
                            </label>
                            <input
                                :disabled="true"
                                type="text"
                                class="form-control"
                                v-model="serial_number"
                            />
                        </div>
                    </div>
                    <div v-if="checkDocumentLinked()" class="col-md-2">
                        <div class="form-group">
                            <label>{{$t('general.RelatedDocument')}}</label>
                            <multiselect
                                :show-labels="false"
                                :disabled="!create.branch_id || (relatedDocuments.length == 1 && parseInt(document.required) == 1)"
                                @input="handelRelatedDocument()"
                                v-model="create.related_document_id"
                                :options="relatedDocuments.map((type) => type.id)"
                                :custom-label="(opt) => relatedDocuments.find((x) => x.id == opt) ? $i18n.locale == 'ar' ? relatedDocuments.find((x) => x.id == opt).name : relatedDocuments.find((x) => x.id == opt).name_e:''"
                                :class="{'is-invalid': $v.create.related_document_id.$error || errors.related_document_id, }">
                            </multiselect>

                            <template v-if="errors.related_document_id">
                                <ErrorMessage v-for="(errorMessage, index) in errors.related_document_id"
                                              :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div v-if="checkDocumentLinked()" class="col-md-2">
                        <div class="form-group">
                            <label>{{$t('general.related_document_prefix')}}</label>
                            <multiselect
                                :show-labels="false"
                                :disabled="!create.branch_id"
                                @input="RelatedDocumentData()"
                                v-model="create.related_document_number"
                                :options="relatedDocumentNumbers.map((type) => type.id)"
                                :custom-label="(opt) => relatedDocumentNumbers.find((x) => x.id == opt) ? relatedDocumentNumbers.find((x) => x.id == opt).prefix:''"
                                :class="{'is-invalid': $v.create.related_document_number.$error || errors.related_document_number, }">
                            </multiselect>

                            <template v-if="errors.related_document_number">
                                <ErrorMessage v-for="(errorMessage, index) in errors.related_document_number"
                                              :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div v-if="checkDocumentLinked()" class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">
                                {{$t('general.RelatedDocumentNumber')}}
                            </label>
                            <input
                                :disabled="true"
                                type="text"
                                class="form-control"
                                v-model="create.related_document_prefix"
                            />
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">{{ $t('general.sellMethod') }} <span class="text-danger">*</span></label>
                            <multiselect
                                :show-labels="false"
                                @input="checkIsAllValue()"
                                :disabled="!create.branch_id"
                                v-model="create.sell_method_id"
                                :options="sellMethods.map((type) => type.id)"
                                :custom-label="(opt) => sellMethods.find((x) => x.id == opt) ? $i18n.locale == 'ar' ? sellMethods.find((x) => x.id == opt).name: sellMethods.find((x) => x.id == opt).name_e:''"
                                :class="{'is-invalid': $v.create.sell_method_id.$error || errors.sell_method_id,}"
                            >
                            </multiselect>
                            <div v-if="!$v.create.sell_method_id.required" class="invalid-feedback">
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.sell_method_id">
                                <ErrorMessage v-for="(errorMessage, index) in errors.sell_method_id"
                                              :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">{{ $t('general.documentSalesmen') }} <span class="text-danger">*</span></label>

                            <multiselect
                                :show-labels="false"
                                :disabled="!create.branch_id"
                                @input="getCustomerSalesman"
                                v-model="create.employee_id"
                                :options="salesmen.map((type) => type.id)"
                                :custom-label=" (opt) => salesmen.find((x) => x.id == opt) ? $i18n.locale == 'ar' ? salesmen.find((x) => x.id == opt).name: salesmen.find((x) => x.id == opt).name_e :''"
                                :class="{'is-invalid':$v.create.employee_id.$error || errors.employee_id,}"
                            >
                            </multiselect>
                            <div v-if="!$v.create.employee_id.required" class="invalid-feedback">
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.employee_id">
                                <ErrorMessage v-for="(errorMessage, index) in errors.button_id"
                                              :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">{{ $t('general.documentCustomer') }} <span class="text-danger">*</span></label>
                            <multiselect
                                :show-labels="false"
                                :disabled="!create.branch_id"
                                @input="getTasksCreate"
                                :internalSearch="false"
                                @search-change="searchCustomer"
                                v-model="create.customer_id"
                                :options="customers.map((type) => type.id)"
                                :custom-label="(opt) => customers.find((x) => x.id == opt) ? $i18n.locale == 'ar' ? customers.find((x) => x.id == opt).name: customers.find((x) => x.id == opt).name_e:''"
                                :class="{'is-invalid':$v.create.customer_id.$error || errors.customer_id,}"
                            >
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
                    <div v-if="customer_sub_category && create.customer_id" class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">
                                {{$t('general.customer_sub_category')}}
                            </label>
                            <input
                                :disabled="true"
                                type="text"
                                class="form-control"
                                v-model="customer_sub_category"
                            />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>{{$t('general.taskNumber')}}</label>
                            <multiselect
                                :show-labels="false"
                                :disabled="!create.branch_id"
                                v-model="create.task_id"
                                :options="tasks.map((type) => type.id)"
                                :custom-label="(opt) => tasks.find((x) => x.id == opt) ? tasks.find((x) => x.id == opt).id :''"
                                :class="{'is-invalid': $v.create.task_id.$error || errors.task_id, }">
                            </multiselect>

                            <template v-if="errors.task_id">
                                <ErrorMessage v-for="(errorMessage, index) in errors.task_id"
                                              :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>{{$t('general.externalSalesmen')}}</label>
                            <multiselect
                                :show-labels="false"
                                :disabled="!create.branch_id"
                                v-model="create.external_salesmen_id"
                                :options="externalSalesman.map((type) => type.id)"
                                :custom-label="(opt) => externalSalesman.find((x) => x.id == opt) ? $i18n.locale == 'ar' ? externalSalesman.find((x) => x.id == opt).name : externalSalesman.find((x) => x.id == opt).name_e :''"
                                :class="{'is-invalid': $v.create.external_salesmen_id.$error || errors.external_salesmen_id, }">
                            </multiselect>

                            <template v-if="errors.external_salesmen_id">
                                <ErrorMessage v-for="(errorMessage, index) in errors.external_salesmen_id"
                                              :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div v-if="create.external_salesmen_id" class="col-md-2">
                        <div class="form-group position-relative">
                            <label class="control-label">
                                {{$t('general.externalCommission')}}
                            </label>
                            <input
                                :disabled="!create.branch_id"
                                type="number"
                                step="any"
                                class="form-control englishInput"
                                v-model="$v.create.external_commission.$model"
                                :class="{
                                        'is-invalid': $v.create.external_commission.$error || errors.external_commission,
                                        'is-valid': !$v.create.external_commission.$invalid && !errors.external_commission,
                                }"
                            />
                            <template v-if="errors.external_commission">
                                <ErrorMessage v-for="(errorMessage, index) in errors.external_commission" :key="index">
                                    {{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group position-relative">
                            <label class="control-label">
                                {{$t('general.InvoiceDiscount')}}
                            </label>
                            <input
                                :disabled="!create.branch_id"
                                @input="changeValue"
                                type="number"
                                step="any"
                                class="form-control englishInput"
                                v-model="$v.create.invoice_discount.$model"
                                :class="{
                                    'is-invalid': $v.create.invoice_discount.$error || errors.invoice_discount,
                                    'is-valid': !$v.create.invoice_discount.$invalid && !errors.invoice_discount,
                                  }"
                            />
                            <template v-if="errors.invoice_discount">
                                <ErrorMessage v-for="(errorMessage, index) in errors.invoice_discount" :key="index">
                                    {{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div v-if="checkIsAllValue()" class="col-md-2">
                        <div class="form-group position-relative">
                            <label class="control-label">
                                {{$t('general.sellMethodDiscount')}}
                            </label>
                            <input
                                :disabled="!create.branch_id || create.sell_method_id == 1"
                                @input="changeValue"
                                type="number"
                                step="any"
                                class="form-control"
                                v-model="$v.create.sell_method_discount.$model"
                                :class="{
                                    'is-invalid': $v.create.sell_method_discount.$error || errors.sell_method_discount,
                                    'is-valid': !$v.create.sell_method_discount.$invalid && !errors.sell_method_discount,
                                  }"
                            />
                            <template v-if="errors.sell_method_discount">
                                <ErrorMessage v-for="(errorMessage, index) in errors.sell_method_discount" :key="index">
                                    {{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>

                    <div class="col-md-12 p-0 m-0">
                        <div class="page-content">
                            <div class="px-0">
                                <div class="row">
                                    <div class="col-12 col-lg-12">
                                        <!-- .row -->
                                        <hr class="row" />
                                        <div>
                                            <div
                                                class="row text-600 text-white bgc-default-tp1 py-25">
                                                <div class="col-1">{{ $t('general.category') }}</div>
                                                <div class="col-1">{{ $t('general.governorate') }}</div>
                                                <div class="col-1">{{ $t('general.package') }}</div>
                                                <div class="col-1">{{ $t('general.from_date') }}</div>
                                                <div class="col-1">{{ $t('general.rent_days') }}</div>
                                                <div class="col-1">{{ $t('general.to_date') }}</div>
                                                <div class="col-1">{{ $t('general.Unit_type') }}</div>
                                                <div class="col-1">{{ $t('general.quantity') }}</div>
                                                <div class="col-1">{{ $t('general.PricePerUnit') }}</div>
                                                <div class="col-1">{{ $t('general.Total') }}</div>
                                                <div class="col-1">{{ $t('general.is_stripe') }}</div>
                                                <div class="col-1">{{ $t("general.Break") }}</div>
                                            </div>
                                            <div v-for="(item, gIndex) in create.header_details" class="text-95  text-secondary-d3">
                                                <!--  create panels   -->
                                                <b-modal
                                                    :id="`create-panel-quotation-${gIndex}`"
                                                    :title="$t('general.Break')"
                                                    title-class="font-18"
                                                    body-class="p-1"
                                                    dialog-class="modal-full-width"
                                                    :hide-footer="true"
                                                    @show="resetModalPanel(gIndex)"
                                                >
                                                    <div :class="['row justify-content-center']">
                                                        <div :class="['col-5']" v-if="faceNumbers.length > 0">
                                                            <div class="face">
                                                                <span class="face-name">A : {{ faceNumbers[gIndex] ? faceNumbers[gIndex].A:0 }}</span>
                                                            </div>
                                                            <div class="face">
                                                                <span class="face-name">B : {{ faceNumbers[gIndex] ? faceNumbers[gIndex].B:0 }}</span>
                                                            </div>
                                                            <div class="face">
                                                                <span class="face-name">Multi : {{ faceNumbers[gIndex] ? faceNumbers[gIndex].Multi:0 }}</span>
                                                            </div>
                                                            <div class="face">
                                                                <span class="face-name">One-Face : {{ faceNumbers[gIndex]?faceNumbers[gIndex]["One-Face"]:0}}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex justify-content-end">
                                                        <div>
                                                            <b-button
                                                                @click.prevent="$bvModal.show(`search-${gIndex}`)"
                                                                variant="primary"
                                                                class="mx-1 font-weight-bold"
                                                            >
                                                                {{ $t('general.Search') }}
                                                                <i class="fe-search"></i>
                                                            </b-button>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <!-- selet panals -->
                                                        <div class="col-md-6">
                                                            <!-- start Pagination -->
                                                            <div class="d-inline-flex align-items-center pagination-custom position-relative">
                                                                <div v-if="pansPaginations.length > 0 && pansPaginations[gIndex]">
                                                                    <div class="d-inline-block" style="font-size: 13px">
                                                                        {{ pansPaginations[gIndex].pansPaginations.from }}-{{ pansPaginations[gIndex].pansPaginations.to }} /
                                                                        {{ pansPaginations[gIndex].pansPaginations.total }}
                                                                    </div>
                                                                    <div class="d-inline-block">
                                                                        <a
                                                                            href="javascript:void(0)"
                                                                            :style="{
                                                                              'pointer-events':
                                                                                pansPaginations[gIndex].pansPaginations.current_page > 1 ? '' : 'none',
                                                                            }"
                                                                            @click.prevent="getPanel(gIndex,pansPaginations[gIndex].pansPaginations.current_page - 1)"
                                                                        >
                                                                            <span>&lt;</span>
                                                                        </a>
                                                                        <input
                                                                            type="text"
                                                                            @keyup.enter="getPanelPagination(gIndex)"
                                                                            v-model="current_page_pans[gIndex]"
                                                                            class="pagination-current-page"
                                                                        />
                                                                        <a
                                                                            href="javascript:void(0)"
                                                                            :style="{
                                                                              'pointer-events':
                                                                                (pansPaginations[gIndex].pansPaginations.last_page ==
                                                                                pansPaginations[gIndex].pansPaginations.current_page) || !pansPaginations[gIndex].pansPaginations
                                                                                  ? 'none'
                                                                                  : '',
                                                                            }"
                                                                            @click.prevent="getPanel(gIndex,pansPaginations[gIndex].pansPaginations.current_page + 1)"
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
                                                              <!--       start loader       -->
                                                              <loader size="large" v-if="isLoader" />
                                                              <!--       end loader       -->
                                                                <thead>
                                                                <tr>
                                                                    <th>
                                                                        <div class="d-flex justify-content-center">
                                                                            <span>{{ $t('general.panel_code') }}</span>
                                                                        </div>
                                                                    </th>
                                                                    <th>
                                                                        <div class="d-flex justify-content-center">
                                                                            <span>{{ $t('general.category') }}</span>
                                                                        </div>
                                                                    </th>
                                                                    <th >
                                                                        <div class="d-flex justify-content-center">
                                                                            <span>{{ $t('general.governorate') }}</span>
                                                                        </div>
                                                                    </th>
                                                                    <th >
                                                                        <div class="d-flex justify-content-center">
                                                                            <span>{{ $t('general.city') }}</span>
                                                                        </div>
                                                                    </th>
                                                                    <th >
                                                                        <div class="d-flex justify-content-center">
                                                                            <span>{{ $t('general.avenue') }}</span>
                                                                        </div>
                                                                    </th>
                                                                    <th >
                                                                        <div class="d-flex justify-content-center">
                                                                            <span>{{ $t('general.street') }}</span>
                                                                        </div>
                                                                    </th>
                                                                    <th >
                                                                        <div class="d-flex justify-content-center">
                                                                            <span>{{ $t('general.face') }}</span>
                                                                        </div>
                                                                    </th>
                                                                    <th>
                                                                        {{ $t("general.Action") }}
                                                                    </th>
                                                                </tr>
                                                                </thead>
                                                                <tbody v-if="pans.length > 0 && pans[gIndex] && pans[gIndex].pans.length > 0">
                                                                <tr
                                                                    v-for="(data, pIndex) in pans[gIndex].pans"
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
                                                                                :disabled="disableCheckAll(gIndex)"
                                                                                class="form-check-input"
                                                                                type="checkbox"
                                                                                :value="data.id"
                                                                                @change="checkRowPanel(gIndex,data)"
                                                                                v-model="CheckAllPanels[gIndex]"
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
                                                                <div v-if="panelPackagesPaginatations.length >0 && panelPackagesPaginatations[gIndex]">
                                                                    <div class="d-inline-block" style="font-size: 13px">
                                                                        {{ panelPackagesPaginatations[gIndex].panelPackagesPaginatations.from }}-{{ panelPackagesPaginatations[gIndex].panelPackagesPaginatations.to }} /
                                                                        {{ panelPackagesPaginatations[gIndex].panelPackagesPaginatations.total }}
                                                                    </div>
                                                                    <div class="d-inline-block">
                                                                        <a
                                                                            href="javascript:void(0)"
                                                                            :style="{
                                                                                              'pointer-events':
                                                                                                current_page_pans_packs[gIndex] > 1 ? '' : 'none',
                                                                                            }"
                                                                            @click.prevent="paginate(current_page_pans_packs[gIndex] - 1,gIndex)"
                                                                        >
                                                                            <span>&lt;</span>
                                                                        </a>
                                                                        <input
                                                                            type="text"
                                                                            @keyup.enter="paginate(current_page_pans_packs[gIndex],gIndex)"
                                                                            v-model="current_page_pans_packs[gIndex]"
                                                                            class="pagination-current-page"
                                                                        />
                                                                        <a
                                                                            href="javascript:void(0)"
                                                                            :style="{
                                                                                              'pointer-events':
                                                                                                (panelPackagesPaginatations[gIndex].panelPackagesPaginatations.last_page ==
                                                                                                current_page_pans_packs[gIndex]) || !panelPackagesPaginatations[gIndex].panelPackagesPaginatations
                                                                                                  ? 'none'
                                                                                                  : '',
                                                                                            }"
                                                                            @click.prevent="paginate(current_page_pans_packs[gIndex] + 1,gIndex)"
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
                                                              <!--       start loader       -->
                                                              <loader size="large" v-if="isLoader" />
                                                              <!--       end loader       -->
                                                                <thead>
                                                                <tr>
                                                                    <th>
                                                                        <div class="d-flex justify-content-center">
                                                                            <span>{{ $t('general.panel_code') }}</span>
                                                                        </div>
                                                                    </th>
                                                                    <th>
                                                                        <div class="d-flex justify-content-center">
                                                                            <span>{{ $t('general.category') }}</span>
                                                                        </div>
                                                                    </th>
                                                                    <th >
                                                                        <div class="d-flex justify-content-center">
                                                                            <span>{{ $t('general.governorate') }}</span>
                                                                        </div>
                                                                    </th>
                                                                    <th >
                                                                        <div class="d-flex justify-content-center">
                                                                            <span>{{ $t('general.city') }}</span>
                                                                        </div>
                                                                    </th>
                                                                    <th >
                                                                        <div class="d-flex justify-content-center">
                                                                            <span>{{ $t('general.avenue') }}</span>
                                                                        </div>
                                                                    </th>
                                                                    <th >
                                                                        <div class="d-flex justify-content-center">
                                                                            <span>{{ $t('general.street') }}</span>
                                                                        </div>
                                                                    </th>
                                                                    <th >
                                                                        <div class="d-flex justify-content-center">
                                                                            <span>{{ $t('general.face') }}</span>
                                                                        </div>
                                                                    </th>
                                                                    <th>
                                                                        {{ $t("general.Action") }}
                                                                    </th>
                                                                </tr>
                                                                </thead>
                                                                <tbody v-if="panelPackages.length > 0 && panelPackages[gIndex] && panelPackages[gIndex].panelPackages.length > 0">
                                                                <tr
                                                                    v-for="(data, tIndex) in panelPackages[gIndex].panelPackages"
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
                                                                                @change="checkRowPanel(gIndex,data)"
                                                                                v-model="CheckAllPanels[gIndex]"
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
                                                </b-modal>
                                                <!--  /create panels   -->

                                                <!--  search   -->
                                                <b-modal
                                                    :id="`search-${gIndex}`"
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
                                                                @click.prevent="getPanel(gIndex)"
                                                            >
                                                                {{ $t('general.Search') }}
                                                            </b-button>

                                                            <b-button variant="success" class="mx-1" disabled v-else>
                                                                <b-spinner small></b-spinner>
                                                                <span class="sr-only">{{ $t('login.Loading') }}...</span>
                                                            </b-button>
                                                            <!-- Emulate built in modal footer ok and cancel button actions -->

                                                            <b-button variant="danger" type="button" @click.prevent="$bvModal.hide(`search-${gIndex}`)">
                                                                {{ $t('general.Cancel') }}
                                                            </b-button>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6" v-if="cities.length > 0 && locations[gIndex] && cities[gIndex]">
                                                                <div class="form-group">
                                                                    <label class="control-label">
                                                                        {{ $t('general.code') }}
                                                                    </label>
                                                                    <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        v-model="locations[gIndex].code"
                                                                    />
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6" v-if="cities.length > 0 && locations[gIndex] && cities[gIndex]">
                                                                <div class="form-group">
                                                                    <label class="control-label">
                                                                        {{ $t('general.city') }}
                                                                    </label>
                                                                    <multiselect
                                                                        :show-labels="false"
                                                                        @input="showCityModal(gIndex)"
                                                                        v-model="locations[gIndex].city_id"
                                                                        :options="cities[gIndex].cities.map((type) => type.id)"
                                                                        :custom-label="(opt) => cities[gIndex].cities.find((x) => x.id == opt) ? $i18n.locale == 'ar' ? cities[gIndex].cities.find((x) => x.id == opt).name : cities[gIndex].cities.find((x) => x.id == opt).name_e :''"
                                                                    >
                                                                    </multiselect>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6" v-if="avenues.length > 0 && locations[gIndex] && avenues[gIndex]">
                                                                <div class="form-group">
                                                                    <label class="control-label">
                                                                        {{ $t('general.avenue') }}
                                                                    </label>
                                                                    <multiselect
                                                                        :show-labels="false"
                                                                        @input="showAvenueModal(gIndex)"
                                                                        v-model="locations[gIndex].avenue_id"
                                                                        :options="avenues[gIndex].avenues.map((type) => type.id)"
                                                                        :custom-label="(opt) => avenues[gIndex].avenues.find((x) => x.id == opt) ? $i18n.locale == 'ar' ? avenues[gIndex].avenues.find((x) => x.id == opt).name : avenues[gIndex].avenues.find((x) => x.id == opt).name_e :''"
                                                                    >
                                                                    </multiselect>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6" v-if="streets.length > 0 && locations[gIndex] && streets[gIndex]">
                                                                <div class="form-group">
                                                                    <label class="control-label">
                                                                        {{ $t('general.street') }}
                                                                    </label>
                                                                    <multiselect
                                                                        :show-labels="false"
                                                                        @input="showStreetModal(gIndex)"
                                                                        v-model="locations[gIndex].street_id"
                                                                        :options="streets[gIndex].streets.map((type) => type.id)"
                                                                        :custom-label="(opt) => streets[gIndex].streets.find((x) => x.id == opt) ? $i18n.locale == 'ar' ? streets[gIndex].streets.find((x) => x.id == opt).name : streets[gIndex].streets.find((x) => x.id == opt).name_e :''"
                                                                    >
                                                                    </multiselect>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6" v-if="locations.length > 0 && locations[gIndex]">
                                                                <div class="form-group">
                                                                    <label class="control-label">
                                                                        {{ $t('general.face') }}
                                                                    </label>
                                                                    <select
                                                                        class="custom-select"
                                                                        v-model="locations[gIndex].face"
                                                                    >
                                                                        <option v-for="face in faces" :value="face">{{face}}</option>
                                                                    </select>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </b-modal>
                                                <!--  /search   -->

                                                <div class="row mb-2 mb-sm-0 py-25" :class="[gIndex % 2 == 0 ? 'bgc-default-l4' : '',(create.header_details[gIndex]?create.header_details[gIndex].quantity:0) != (panelPackages[gIndex]?panelPackages[gIndex].panelPackages.length:0) &&((create.header_details[gIndex]?create.header_details[gIndex].is_stripe:0)==0) ? 'row-danger':'' ]" >
                                                    <div class="col-1 p-0">
                                                        <multiselect
                                                            :show-labels="false"
                                                            :disabled="item.is_stripe == 1"
                                                            v-model="$v.create.header_details.$each[gIndex].category_id.$model"
                                                            :options="categories.map((type) => type.id)"
                                                            :custom-label="(opt) => categories.find((x) => x.id == opt)? $i18n.locale == 'ar' ? categories.find((x) => x.id == opt).name: categories.find((x) => x.id == opt).name_e:''"
                                                            :class="{
                                                                'is-invalid':$v.create.header_details.$each[gIndex].category_id.$error || errors.category_id,
                                                            }"
                                                        >
                                                        </multiselect>
                                                    </div>
                                                    <div class="col-1 p-0">
                                                        <multiselect
                                                            :show-labels="false"
                                                            :disabled="item.is_stripe == 1"
                                                            @input="showGovernorateModal(gIndex)"
                                                            :id="`create-${gIndex}-3`"
                                                            v-model="$v.create.header_details.$each[gIndex].governorate_id.$model"
                                                            :options="governorates.map((type) => type.id)"
                                                            :custom-label="(opt) => governorates.find((x) => x.id == opt) ? $i18n.locale == 'ar'? governorates.find((x) => x.id == opt).name : governorates.find((x) => x.id == opt).name_e :''"
                                                            :class="{'is-invalid':$v.create.header_details.$each[gIndex].governorate_id.$error || errors.governorate_id,}"
                                                        >
                                                        </multiselect>
                                                    </div>
                                                    <div class="col-1 p-0">
                                                        <multiselect
                                                            :show-labels="false"
                                                            :disabled="item.is_stripe == 0"
                                                            @input="showPackageModal(gIndex)"
                                                            v-model="$v.create.header_details.$each[gIndex].package_id.$model"
                                                            :options="packages.map((type) => type.id)"
                                                            :custom-label="(opt) => packages.find((x) => x.id == opt) ? $i18n.locale == 'ar' ? packages.find((x) => x.id == opt).name: packages.find((x) => x.id == opt).name_e : ''"
                                                            :class="{'is-invalid':$v.create.header_details.$each[gIndex].package_id.$error || errors.package_id,}"
                                                        >
                                                        </multiselect>
                                                    </div>
                                                    <div class="col-1 p-0">
                                                        <div class="form-group">
                                                            <date-picker
                                                                @input="calcDateTo(gIndex)"
                                                                type="date"
                                                                v-model="$v.create.header_details.$each[gIndex].date_from.$model"
                                                                format="YYYY-MM-DD"
                                                                valueType="format"
                                                                :confirm="false"
                                                                :class="{'is-invalid':$v.create.header_details.$each[gIndex].date_from.$error || errors.date_from, }"
                                                            >
                                                            </date-picker>
                                                        </div>
                                                    </div>
                                                    <div class="col-1 p-0">
                                                        <div class="form-group">
                                                            <input
                                                                @input="calcDateTo(gIndex)"
                                                                type="number"
                                                                class="form-control englishInput"
                                                                v-model="$v.create.header_details.$each[gIndex].rent_days.$model"
                                                                :class="{'is-invalid':$v.create.header_details.$each[gIndex].rent_days.$error || errors.rent_days, }"
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="col-1 p-0">
                                                        <div class="form-group">
                                                            <date-picker
                                                                :disabled="true"
                                                                type="date"
                                                                v-model="$v.create.header_details.$each[gIndex].date_to.$model"
                                                                format="YYYY-MM-DD"
                                                                valueType="format"
                                                                :confirm="false"
                                                                :class="{ 'is-invalid': $v.create.header_details.$each[gIndex].date_to.$error || errors.date_to,}"
                                                            >
                                                            </date-picker>
                                                        </div>
                                                    </div>
                                                    <div class="col-1 p-0">
                                                        <input
                                                            v-model="$v.create.header_details.$each[gIndex].unit_type.$model"
                                                            class="form-control"
                                                            type="text"
                                                            :class="{
                                                                'is-invalid': $v.create.header_details.$each[gIndex].unit_type.$error || errors.unit_type,
                                                                'is-valid': !$v.create.header_details.$each[gIndex].unit_type.$invalid && !errors.unit_type,
                                                            }"
                                                        />
                                                    </div>
                                                    <div class="col-1 p-0">
                                                        <input
                                                            :disabled="item.is_stripe == 1"
                                                            @input="totalCreate(gIndex)"
                                                            @keyup="addLineEnter($event)"
                                                            v-model.number="$v.create.header_details.$each[gIndex].quantity.$model"
                                                            class="form-control"
                                                            type="number"
                                                            :class="{'is-invalid': $v.create.header_details.$each[gIndex].quantity.$error || errors.quantity,}"
                                                        />
                                                    </div>
                                                    <div class="col-1 p-0">
                                                        <input
                                                            @input="totalCreate(gIndex)"
                                                            @keyup="addLineEnter($event)"
                                                            v-model.number="$v.create.header_details.$each[gIndex].price_per_uint.$model"
                                                            class="form-control" step="any"
                                                            type="number"
                                                            :class="{
                                                                'is-invalid': $v.create.header_details.$each[gIndex].price_per_uint.$error || errors.price_per_uint,
                                                                'is-valid': !$v.create.header_details.$each[gIndex].price_per_uint.$invalid && !errors.price_per_uint,
                                                            }"
                                                        />
                                                    </div>
                                                    <div class="col-1 p-0">
                                                        <input
                                                            @input="PricePerUnitByTotal(gIndex)"
                                                            v-model.number="$v.create.header_details.$each[gIndex].total.$model"
                                                            class="form-control" step="any"
                                                            type="number"
                                                            :class="{
                                                                'is-invalid': $v.create.header_details.$each[gIndex].total.$error || errors.total,
                                                                'is-valid': !$v.create.header_details.$each[gIndex].total.$invalid && !errors.total,
                                                            }"
                                                        />
                                                    </div>
                                                    <div class="col-1 p-0">
                                                        <b-form-group
                                                            :class="{
                                                                'is-invalid': $v.create.header_details.$each[gIndex].is_stripe.$error || errors.is_stripe,
                                                                'is-valid': !$v.create.header_details.$each[gIndex].is_stripe.$invalid && !errors.is_stripe,
                                                            }"
                                                        >
                                                            <b-form-radio class="d-inline-block" @input="changeStrip(gIndex)" v-model="$v.create.header_details.$each[gIndex].is_stripe.$model" :name="'some-radios-'+gIndex"
                                                                          value="1">{{ $t("general.Yes") }}</b-form-radio>
                                                            <b-form-radio class="d-inline-block m-1" @input="changeStrip(gIndex)" v-model="$v.create.header_details.$each[gIndex].is_stripe.$model" :name="'some-radios-'+gIndex"
                                                                          value="0">{{ $t("general.No") }}</b-form-radio>
                                                        </b-form-group>
                                                    </div>
                                                    <div class="col-1 p-0">
                                                        <button
                                                            v-if="item.is_stripe == 0"
                                                            type="button"
                                                            :disabled="(!item.category_id && !item.governorate_id && !(item.quantity < 0))"
                                                            @click.prevent="showPanel(gIndex)"
                                                            class="custom-panel-quotation"
                                                        >
                                                            {{ $t('general.Break') }}
                                                        </button>
                                                        <button
                                                            v-if="create.header_details.length > 1"
                                                            type="button"
                                                            @click.prevent="removeNewField(gIndex)"
                                                            class="custom-btn-dowonload p-0"
                                                        >
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                        <b-button :disabled="create.sell_method_id != 1" @click.prevent="$bvModal.show(`sell-method-${gIndex}`)" class="custom-panel-quotation p-1" variant="success" :title="$t('general.sellMethod')"><i class="fas fa-handshake"></i></b-button>
                                                        <b-modal
                                                            :id="`sell-method-${gIndex}`"
                                                            size="sm"
                                                            :title="$t('general.sellMethod')"
                                                            title-class="font-18"
                                                            hide-footer
                                                        >
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label class="control-label">{{ $t('general.sellMethod') }} <span class="text-danger">*</span></label>
                                                                        <multiselect
                                                                            :show-labels="false"
                                                                            v-model="$v.create.header_details.$each[gIndex].sell_method_id.$model"
                                                                            :options="sellMethods.map((type) => type.id)"
                                                                            :custom-label="(opt) => sellMethods.find((x) => x.id == opt) ? $i18n.locale == 'ar' ? sellMethods.find((x) => x.id == opt).name: sellMethods.find((x) => x.id == opt).name_e :''"
                                                                            :class="{
                                                                                'is-invalid': $v.create.header_details.$each[gIndex].sell_method_id.$error,
                                                                                'is-valid': !$v.create.header_details.$each[gIndex].sell_method_id.$invalid,
                                                                            }"
                                                                        >
                                                                        </multiselect>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div class="form-group position-relative">
                                                                        <label class="control-label">
                                                                            {{$t('general.sellMethodDiscount')}}
                                                                        </label>
                                                                        <input
                                                                            @input="checkIsAllValueDetail()"
                                                                            type="number"
                                                                            step="any"
                                                                            class="form-control englishInput"
                                                                            v-model="$v.create.header_details.$each[gIndex].sell_method_discount.$model"
                                                                            :class="{
                                                                                'is-invalid': $v.create.header_details.$each[gIndex].sell_method_discount.$error,
                                                                                'is-valid': !$v.create.header_details.$each[gIndex].sell_method_discount.$invalid,
                                                                            }"
                                                                        />

                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </b-modal>
                                                    </div>
                                                    <div class="col-3">
                                                        <div class="form-group row">
                                                            <label class="col-sm-2 col-form-label">{{$t('general.note')}}</label>
                                                            <div class="col-sm-10">
                                                                <input
                                                                    v-model="$v.create.header_details.$each[gIndex].note.$model"
                                                                    class="form-control"
                                                                    type="text"
                                                                    :class="{
                                                                        'is-invalid': $v.create.header_details.$each[gIndex].note.$error || errors.note,
                                                                        'is-valid': !$v.create.header_details.$each[gIndex].note.$invalid && !errors.note,
                                                                    }"
                                                                />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-3" v-if="item.is_stripe == 1">
                                                        <div class="form-group row">
                                                            <label class="col-sm-2 col-form-label">{{$t('general.package_note')}}</label>
                                                            <div class="col-sm-10">
                                                                <input
                                                                    :disabled="true"
                                                                    v-model="$v.create.header_details.$each[gIndex].package_note.$model"
                                                                    class="form-control"
                                                                    type="text"
                                                                    :class="{
                                                                        'is-invalid': $v.create.header_details.$each[gIndex].package_note.$error || errors.package_note,
                                                                        'is-valid': !$v.create.header_details.$each[gIndex].package_note.$invalid && !errors.package_note,
                                                                    }"
                                                                />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row border-b-2 brc-default-l2"></div>
                                            <div class="row mt-3">
                                                <div class="col-12 col-sm-7 text-grey-d2 text-95 mt-2 mt-lg-0">
                                                    {{ $t("general.Extra_note") }}
                                                </div>

                                                <div class="col-12 col-sm-5 text-grey text-90 order-first order-sm-last">
                                                    <div class="row align-items-center bgc-primary-l3">
                                                        <div class="col-7 text-right">
                                                            {{ $t("general.TotalInvoice") }}
                                                        </div>
                                                        <div class="col-5">
                                                            <span class="text-150 text-success-d3 opacity-2">
                                                                {{ !create.total_invoice ? '0.00' : create.total_invoice }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="row align-items-center bgc-primary-l3">
                                                        <div class="col-7 text-right">
                                                            {{ $t("general.InvoiceDiscount") }}
                                                        </div>
                                                        <div class="col-5">
                                                            <span class="text-150 text-success-d3 opacity-2">
                                                                {{ !create.invoice_discount ? '0.00' : create.invoice_discount }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="row align-items-center bgc-primary-l3">
                                                        <div class="col-7 text-right">
                                                            {{ $t("general.NetInvoice") }}
                                                        </div>
                                                        <div class="col-5">
                                                            <span class="text-150 text-success-d3 opacity-2">
                                                                {{ !create.net_invoice ? '0.00' : create.net_invoice }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div v-if="checkIsAllValue('create')" class="row align-items-center bgc-primary-l3">
                                                        <div class="col-7 text-right">
                                                            {{ $t("general.sellMethodDiscount") }}
                                                        </div>
                                                        <div class="col-5">
                                                            <span class="text-150 text-success-d3 opacity-2">
                                                                {{ !create.sell_method_discount ? '0.00' : create.sell_method_discount }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div v-if="checkIsAllValue()" class="row align-items-center bgc-primary-l3">
                                                        <div class="col-7 text-right">
                                                            {{ $t("general.unrelaized_revenue") }}
                                                        </div>
                                                        <div class="col-5">
                                                            <span class="text-150 text-success-d3 opacity-2">
                                                                {{ !create.unrelaized_revenue ? '0.00' : create.unrelaized_revenue }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <hr />
                                            <div>
                                                <span class="text-secondary-d1 text-105">{{$t("general.Thank_you") }}</span>
                                                <a @click.prevent="addNewField" class="btn btn-info btn-bold px-4 float-right mt-3 mx-2 mt-lg-0">
                                                    {{$t("general.AddNewLine") }}
                                                </a>
                                                <div v-if="document && document.attributes && parseInt(document.attributes.customer) != 0" class="px-4 float-right mt-3 mt-lg-0">
                                                    <b-button v-if="!create.id && create.net_invoice > 0"
                                                              variant="primary"
                                                              class="btn btn-primary btn-bold px-4 float-right mt-3 mx-2 mt-lg-0"
                                                              @click="Submit(true)"
                                                    >
                                                        {{ $t('general.Break') }}
                                                    </b-button>

                                                    <b-button v-else
                                                              variant="secondary"
                                                              class="btn btn-secondary btn-bold px-4 float-right mt-3 mx-2 mt-lg-0"
                                                    >
                                                        {{ $t('general.Break') }}
                                                    </b-button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </b-modal>
        <!--  /create   -->
    </div>
</template>

<style scoped>
.custom-panel-quotation{
    background-color: #81afca !important;
    color: lemonchiffon;
    font-size: 16px;
    border: none;
}
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
.row-danger {
    background-color:#f6a9a9 !important;
}
</style>
