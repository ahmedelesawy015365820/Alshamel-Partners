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
import AccountStatementPrint from "../print/account-statement-booking";
import transactionBreak from "../../../create/receivablePayment/transactionBreak/transactionBreak";


/**
 * Advanced Table component
 */
export default {
    name: "createOrUpdateCheckOut",
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
        other_data:{
            default:null,
        },
    },
    mixins: [translation],
    components: {
        ErrorMessage,
        loader,
        DatePicker,
        Multiselect,
        AccountStatementPrint,
        transactionBreak
    },
    data() {
        return {
            financial_years_validate:true,
            customer_data_edit: "",
            customer_data_create: "",
            openingBreak:'',
            debounce: {},
            customers: [],
            companies: [],
            salesmen: [],
            statuses: [],
            serial_number:"",
            relatedDocumentNumbers: [],
            relatedDocuments: [],
            paymentMethods: [],
            rooms: [{rooms: []}],
            services: [],
            escorts: [],
            clientRooms: [],
            roomsFilter: [],
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
                employee_id: null,
                customer_id: null,
                attendants: [],
                attendans_num:0,
                company_id: null,
                customer_type:1,
                payment_method_id: 1,
                total_invoice: 0,
                paid: 0,
                invoice_discount: 0,
                net_invoice: 0,
                sell_method_discount: 0,
                unrelaized_revenue: 0,
                check_in_time:'',
                check_out_time:'',
                header_details: [{
                    unit_id:null, //for rooms
                    item_id:null, //for service
                    quantity: 0,
                    price_per_uint: 0,
                    total: 0,
                    payment: 0,
                    unit_type: "Booking",
                    date_from: this.formatDate(new Date()),
                    rent_days:0,
                    date_to: this.formatDate(new Date()),
                    check_in_time:'',
                    discount: 0,
                    is_stripe: 0,
                    note:'',
                    sell_method_discount: 0,
                    prefix_related:''
                }],
                break_settlement:[
                    {
                        document_id: this.document_id,
                        affect:1,
                        amount:0
                    }
                ],
                voucher_headers:[]
            },
            errors: {},
            branches: [],
            is_disabled: false,
            company_id: null,
            printObj: {
                id: "printReservation",
            },
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
            employee_id: { required },
            customer_id: { required },
            company_id: {  required: requiredIf(function (model) {
                    return this.create.customer_type == 0;
                }),
            },
            customer_type: { },
            attendants: {},
            check_in_time: {},
            check_out_time: { required },
            payment_method_id: { required },
            total_invoice: { required },
            invoice_discount: { required },
            net_invoice: { required },
            sell_method_discount: {  },
            attendans_num: {  },
            unrelaized_revenue: {  },
            header_details: {
                required,
                $each: {
                    unit_id: {},
                    item_id: {},
                    quantity: {  },
                    date_from: {},
                    rent_days:{},
                    date_to: {},
                    check_in_time:{},
                    unit_type: {},
                    price_per_uint: {},
                    total: {},
                    discount: {},
                    is_stripe: {},
                    sell_method_discount: {},
                    prefix_related: {},
                    note: {},
                }
            }
        },
        roomsFilter:{required},
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
            let paid = 0;
            this.create.header_details.forEach(e => {
                sum += parseFloat(e.price_per_uint * e.quantity);
                paid += parseFloat(e.payment);
            });

            this.create.total_invoice = sum;
            this.create.paid = paid;
            let net_invoice = (sum - this.create.invoice_discount);
            this.create.net_invoice = parseFloat(net_invoice).toFixed(3);
            let remaining = this.create.net_invoice - this.create.paid
            this.create.break_settlement = [
                {
                    document_id: this.document_id,
                    affect:remaining >= 0 ? 1 : -1,
                    amount:remaining
                }
            ]
            // this.calculateDiscountLine();
        },
        calculateDiscountLine()
        {
            this.create.header_details.forEach((e,index) => {
                this.create.header_details[index].discount =(this.create.invoice_discount * e.total) / this.create.total_invoice ;
            });
        },
        addNewField(){
            this.rooms.push({rooms: []});
            this.create.header_details.push({
                unit_id:null, //for rooms
                item_id:null, //for service
                quantity: 0,
                price_per_uint: 0,
                date_from: this.formatDate(new Date()),
                rent_days:0,
                date_to: this.formatDate(new Date()),
                check_in_time:'',
                total: 0,
                payment: 0,
                discount: 0,
                unit_type: "Booking",
                is_stripe: 0,
                sell_method_discount: 0,
                prefix_related: '',
                note: '',
            });
            this.changeValue();
        },
        removeNewField(index){
            if(this.create.header_details.length > 1){
                this.create.header_details.splice(index,1);
                this.rooms.splice(index,1);
            }
            this.changeValue();
        },
        /**
         *  data create (create)
         */
        dataCreate()
        {
            this.customer_data_edit = '';
            this.customer_data_create = '';
            this.serial_number = "";
            this.roomsFilter = [];
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
                employee_id: null,
                customer_id: null,
                company_id: null,
                sell_method_id: null,
                payment_method_id: 1,
                customer_type:1,
                attendants: [],
                total_invoice: 0,
                paid: 0,
                invoice_discount: 0,
                attendans_num: 0,
                net_invoice: 0,
                check_in_time:'',
                check_out_time:this.currentTime(),
                sell_method_discount: 0,
                unrelaized_revenue: 0,
                header_details: [{
                    unit_id:null, //for rooms
                    item_id:null, //for service
                    quantity: 0,
                    price_per_uint: 0,
                    total: 0,
                    payment: 0,
                    unit_type: "Booking",
                    date_from: this.formatDate(new Date()),
                    rent_days:0,
                    date_to: this.formatDate(new Date()),
                    check_in_time:'',
                    discount: 0,
                    is_stripe: 0,
                    sell_method_discount: 0,
                    prefix_related: '',
                    note: '',
                }],
                break_settlement:[
                    {
                        document_id: this.document_id,
                        affect:1,
                        amount:0
                    }
                ],
                voucher_headers:[]
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
            this.rooms= [{rooms: []}];
            if (this.checkDocumentNeedApprove() )
            {
                this.getStatus();
            }
            this.dataCreate();
            this.getBranches();
            this.getPaymentMethod();
            this.getSalesmen();
            this.getServices();
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
            this.rooms= [{rooms: []}];
            this.dataCreate();
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
                let header_details = this.create.header_details
                let data_create = this.create;
                data_create.header_details = [];
                header_details.forEach((e,index) => {
                    if (e.is_stripe != null )
                    {
                        data_create.header_details.push({
                            unit_id:e.unit_id, //for rooms
                            item_id:e.item_id, //for service
                            date_from: this.formatDate(e.date_from),
                            rent_days: e.rent_days,
                            date_to: this.formatDate(e.date_to),
                            check_in_time:e.check_in_time,
                            unit_type: e.unit_type,
                            quantity: e.quantity,
                            price_per_uint: e.price_per_uint,
                            total: e.total,
                            payment: e.payment,
                            discount: e.discount,
                            is_stripe: e.is_stripe,
                            sell_method_discount: e.sell_method_discount,
                            note: e.note,
                            prefix_related: e.prefix_related,
                        });
                    }
                });
                adminApi.post(`document-headers`, {...data_create})
                    .then((res) => {
                        this.$emit("created");
                        this.is_disabled = true;
                        this.create.id = res.data.data.id;
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
                    if (!this.dataRow){
                        if (this.branches.length == 1)
                        {
                            this.create.branch_id = this.branches[0].id;
                            this.getSerialNumber(this.create.branch_id);
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
        getSalesmen() {
            this.isLoader = true;
            adminApi
                .get(`/employees?is_salesman=1&customer_handel=1`)
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
                let customer_handel = this.salesmen.find( (el) => el.id == employee_id ).customer_handel;
                this.getCustomers(customer_handel,employee_id);
                if (parseInt(this.create.customer_type) == 0)
                {
                    this.getCompanies(customer_handel,employee_id);
                }
            }
        },
        getCompanies(customer_handel,employee_id=null,search='') {
            this.isLoader = true;
            adminApi
                .get(`/general-customer?limet=10&type=0&company_id=${this.company_id}&employee_id=${customer_handel=='his_customer'?employee_id:''}&search=${search}&columns[0]=name&columns[1]=name_e&columns[2]=id`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    this.companies = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        getCustomers(customer_handel,employee_id=null,search='') {
            this.isLoader = true;
            adminApi
                .get(`/general-customer?limet=10&type=1&company_id=${this.company_id}&employee_id=${customer_handel=='his_customer'?employee_id:''}&search=${search}&columns[0]=name&columns[1]=name_e&columns[2]=id`)
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
        getEscorts(customer_id) {
            this.isLoader = true;
            adminApi
                .get(`/attendant?customer_id=${customer_id}`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    this.escorts = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        getDocumentCustomer() {
            if(!this.create.customer_id || this.roomsFilter.length == 0)
            {
                return 0;
            }
            this.isLoader = true;
            adminApi
                .get(`/document-headers/document-customer/${this.create.customer_id}?units=${this.roomsFilter}`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data;
                    if (l.details.length > 0)
                    {
                        this.displayDetailData(l);
                    }else{
                        Swal.fire({
                            icon: "error",
                            title: `${this.$t("general.Error")}`,
                            text: `${this.$t("general.ThereAreNoReservationsOrServicesForThisCustomer")}`,
                        });
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
        getCustomerRooms(customer_id) {
            this.isLoader = true;
            adminApi
                .get(`/booking/units/get-client-units?client_id=${customer_id}`)
                .then((res) => {
                    let l = res.data.data;
                    this.clientRooms = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                }).finally(() => {
                this.isLoader = false;
            });
        },
        getPaymentMethod() {
            this.isLoader = true;
            adminApi.get(`/payment-methods`)
                .then((res) => {
                    let l = res.data.data;
                    this.paymentMethods = l;
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
        getRooms(index,date_from,date_to){
            this.isLoader = true;
            this.rooms[index].rooms = [];
            adminApi
                .get(`/document-header-details/getRooms?date_from=${date_from}&date_to=${date_to}`)
                .then((res) => {
                    let l = res.data.data;
                    this.rooms[index].rooms = l;
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
        getServices(){
            this.isLoader = true;
            adminApi
                .get(`/General-item`)
                .then((res) => {
                    let l = res.data.data;
                    this.services = l;
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
         *   show Modal (edit)
         */
        async resetModalEdit(id) {
            this.rooms = [];
            this.customer_data_edit = '';
            this.customer_data_create = '';
            this.isLoader = true;
            this.financial_years_validate = true;
            if (this.checkDocumentNeedApprove()) {
                this.getStatus();
            }
            this.getBranches();
            this.getPaymentMethod();
            this.getSalesmen();
            this.getServices();
            let reservation = this.dataOfRow;
            this.customer_data_edit = reservation.customer;

            this.getCustomers(reservation.employee.customer_handel,reservation.employee_id);
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
            if(reservation.document_number)
            {
                this.relatedDocumentNumbers.push(reservation.document_number);
            }
            this.getEscorts(reservation.customer_id)
            this.create.id = reservation.id;
            this.create.customer_id = reservation.customer_id;
            this.create.company_id = reservation.company_id;
            this.create.customer_type = reservation.customer_type;
            this.create.attendants = reservation.attendants;
            this.create.check_in_time = reservation.check_in_time;
            this.create.check_out_time = reservation.check_out_time;
            if(reservation.customer_type == 0)
            {
                this.getCompanies(reservation.employee.customer_handel,reservation.employee_id)
            }
            this.create.employee_id = reservation.employee_id;
            this.create.payment_method_id = reservation.payment_method_id;
            this.create.sell_method_id = reservation.sell_method_id;
            this.create.total_invoice = reservation.total_invoice;
            this.create.paid = reservation.paid ? reservation.paid :0;
            this.create.invoice_discount = reservation.invoice_discount;
            this.create.attendans_num = reservation.attendans_num;
            this.create.net_invoice = reservation.net_invoice;
            this.create.sell_method_discount = reservation.sell_method_discount;
            this.create.unrelaized_revenue = reservation.unrelaized_revenue;
            this.create.related_document_number = reservation.related_document_number;
            this.create.related_document_prefix = reservation.related_document_prefix;
            this.create.header_details = [];
            reservation.header_details.forEach((e,index) => {
                if (e.unit_id)
                {
                    this.rooms.push({rooms: [e.unit]});
                }else{
                    this.rooms.push({rooms: []});
                }

                this.create.header_details.push({
                    unit_id:e.unit_id, //for rooms
                    item_id:e.item_id, //for service
                    date_from: this.formatDate(e.date_from),
                    rent_days:e.rent_days,
                    date_to: this.formatDate(e.date_to),
                    check_in_time:e.check_in_time,
                    unit_type: e.unit_type,
                    quantity: e.quantity,
                    price_per_uint: e.price_per_uint,
                    discount: e.discount,
                    total: e.total,
                    payment: e.payment ? e.payment : 0,
                    is_stripe: e.is_stripe,
                    prefix_related: e.prefix_related,
                    sell_method_discount: e.sell_method_discount,
                    note: e.note,
                });
            });
            this.isLoader = false;
        },
        /**
         *  hidden Modal (edit)
         */
        resetModalHiddenEdit(id) {
            this.customer_data_edit = '';
            this.rooms= [{rooms: []}];
            this.errors = {};
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
                employee_id: null,
                customer_id: null,
                company_id: null,
                sell_method_id: null,
                payment_method_id: 1,
                customer_type:1,
                attendants: [],
                check_in_time:'',
                check_out_time:this.currentTime(),
                total_invoice: 0,
                paid: 0,
                invoice_discount: 0,
                attendans_num: 0,
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

        showPackageModal(index) {
            if (this.create.header_details[index].package_id) {
                this.create.header_details[index].price_per_uint = this.packages.find(el => el.id == this.create.header_details[index].package_id).price;
                this.totalCreate(index);
            }
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
        changeStrip(index)
        {
            this.create.header_details[index].unit_id = null;
            this.create.header_details[index].item_id = null;
            this.create.header_details[index].quantity = 0;
            this.create.header_details[index].price_per_uint = 0;
            this.create.header_details[index].total = 0;
            this.create.header_details[index].discount = 0;
            this.create.header_details[index].unit_type = "Booking";
            this.create.header_details[index].date_from = this.formatDate(new Date());
            this.create.header_details[index].rent_days = 0;
            this.create.header_details[index].date_to = this.formatDate(new Date());
            this.create.header_details[index].check_in_time=''
        },
        searchCustomer(e) {
            let search = e??'';
            clearTimeout(this.debounce);
            this.debounce = setTimeout(() => {
                let customer_handel = this.salesmen.find( (el) => el.id == this.create.employee_id )?this.salesmen.find( (el) => el.id == this.create.employee_id ).customer_handel:'';
                this.getCustomers(customer_handel,this.create.employee_id,search);
            }, 500);

        },
        searchCompany(e) {
            let search = e??'';
            clearTimeout(this.debounce);
            this.debounce = setTimeout(() => {
                let customer_handel = this.salesmen.find( (el) => el.id == this.create.employee_id )?this.salesmen.find( (el) => el.id == this.create.employee_id ).customer_handel:'';
                this.getCompanies(customer_handel,this.create.employee_id,search);
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
            this.rooms = [];
            this.isLoader = true;
            let relatedDocument = this.relatedDocumentNumbers.find(el => el.id == related_document_number);
            this.create.header_details = [];
            this.customer_data_edit = relatedDocument.customer;
            this.customer_data_create = relatedDocument.customer;
            this.getCustomers(relatedDocument.employee.customer_handel,relatedDocument.employee_id);
            this.getEscorts(relatedDocument.customer_id)
            this.create.related_document_prefix = relatedDocument.serial_number;
            this.create.customer_type = relatedDocument.customer_type;
            this.create.attendants = relatedDocument.attendants;
            this.create.check_in_time = relatedDocument.check_in_time;
            this.create.check_out_time = relatedDocument.check_out_time;
            this.create.customer_id = relatedDocument.customer_id;
            this.create.company_id = relatedDocument.company_id;
            this.create.employee_id = relatedDocument.employee_id;
            if (relatedDocument.customer_type == 0)
            {
                this.getCompanies(relatedDocument.employee.customer_handel,relatedDocument.employee_id);
            }
            this.create.payment_method_id = relatedDocument.payment_method_id;
            this.create.sell_method_id = relatedDocument.sell_method_id;
            this.create.total_invoice = relatedDocument.total_invoice;
            this.create.paid = relatedDocument.paid ? relatedDocument.paid : 0;
            this.create.invoice_discount = relatedDocument.invoice_discount;
            this.create.attendans_num = relatedDocument.attendans_num;
            this.create.net_invoice = relatedDocument.net_invoice;
            this.create.sell_method_discount = relatedDocument.sell_method_discount;
            this.create.unrelaized_revenue = relatedDocument.unrelaized_revenue;

            relatedDocument.header_details.forEach((e,index) => {
                if (e.unit_id)
                {
                    this.rooms.push({rooms: [e.unit]});
                }else{
                    this.rooms.push({rooms: []});
                }
                this.create.header_details.push({
                    unit_id:e.unit_id, //for rooms
                    item_id:e.item_id, //for service
                    date_from: this.formatDate(e.date_from),
                    rent_days: e.rent_days,
                    date_to: this.formatDate(e.date_to),
                    check_in_time:e.check_in_time,
                    unit_type: e.unit_type,
                    quantity: e.quantity,
                    price_per_uint: e.price_per_uint,
                    total: e.total,
                    payment: e.payment ? e.payment : 0,
                    discount: e.discount,
                    is_stripe: e.is_stripe,
                    prefix_related: e.prefix_related,
                    sell_method_discount: e.sell_method_discount,
                    note: e.note,
                });
            });
            this.isLoader = false;
        },

        calcDateTo(index)
        {
            let days = parseInt(this.create.header_details[index].rent_days);
            let date_from = new Date(this.create.header_details[index].date_from);
            date_from.setDate(date_from.getDate() + days);
            this.create.header_details[index].date_to = new Date(date_from).toISOString().slice(0, 10);
            this.create.header_details[index].quantity = parseInt(this.create.header_details[index].rent_days);
            this.totalCreate(index);
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
                    customer_id: this.create.customer_type == 0 ? this.create.company_id : this.create.customer_id,
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
        addRoomPrice(index)
        {
            if(this.create.header_details[index].unit_id)
            {
                let price = this.rooms[index].rooms.find(el => el.id == this.create.header_details[index].unit_id) ? this.rooms[index].rooms.find(el => el.id == this.create.header_details[index].unit_id).price : 0;
                this.create.header_details[index].price_per_uint = price;
            }else {
                this.create.header_details[index].price_per_uint = 0 ;
            }
            this.totalCreate(index);
        },
        addServicePrice(index)
        {
            if(this.create.header_details[index].item_id)
            {
                let price = this.services.find(el => el.id == this.create.header_details[index].item_id) ? this.services.find(el => el.id == this.create.header_details[index].item_id).price : 0;
                this.create.header_details[index].price_per_uint = price;
            }else {
                this.create.header_details[index].price_per_uint = 0 ;
            }
            this.totalCreate(index);
        },
        customerTypeHandel(e)
        {
            this.create.customer_type = parseInt(e.target.value);
            if (this.create.customer_type == 0 )
            {
                let customer_handel = this.salesmen.find( (el) => el.id == this.create.employee_id )?this.salesmen.find( (el) => el.id == this.create.employee_id ).customer_handel:'';
                this.getCompanies(customer_handel,this.create.employee_id);
            }
        },
        escortsHandel()
        {
            this.rooms= [{rooms: []}];
            this.create.attendants =[];
            this.roomsFilter = [];
            this.create.header_details =[];
            this.customer_data_create = this.customers.find((e) => e.id == this.create.customer_id);
            if (this.create.customer_id)
            {
                this.getEscorts(this.create.customer_id);
                this.getCustomerRooms(this.create.customer_id);
                // this.getDocumentCustomer(this.create.customer_id);
            }
        },
        discountLine()
        {
            let sum = parseFloat(this.create.invoice_discount);
            this.create.header_details.forEach(e => {
                sum += parseFloat(e.discount);
            });
            this.create.invoice_discount = sum;
            this.changeValue();
        },
        displayDetailData(data)
        {
            this.rooms = [];
            this.isLoader = true;
            this.create.header_details = [];
            this.create.check_in_time = data.header.check_in_time;
            this.create.related_document_id = data.header.document_header.related_document_id;
            this.create.related_document_number = data.header.document_header.related_document_number;
            this.create.related_document_prefix = data.header.document_header.related_document_prefix;
            let attendants =[];
            data.header.document_header.attendants_document.forEach((e,index) => {
                attendants.push(e.id) ;
            });
            this.create.attendants = attendants ;
            let calcDiscount = 0;
            if(data.header.document_header.invoice_discount > 0 && parseFloat(data.details[0].discount) == 0)
            {
               let dataDetailDiscount = data.details.filter((x) => x.item_id == null);
                calcDiscount = (parseFloat(data.header.document_header.invoice_discount)/parseFloat(data.rent_days)) * dataDetailDiscount.length;
                this.create.invoice_discount = parseFloat(calcDiscount).toFixed(3);
            }

            data.details.forEach((e,index) => {
                if (e.unit_id)
                {
                    this.rooms.push({rooms: [e.unit]});
                }else{
                    this.rooms.push({rooms: []});
                }
                this.create.header_details.push({
                    unit_id:e.unit_id, //for rooms
                    item_id:e.item_id, //for service
                    date_from: this.formatDate(e.date_from),
                    rent_days: e.rent_days,
                    date_to: this.formatDate(e.date_to),
                    check_in_time:e.check_in_time,
                    unit_type: e.unit_type,
                    quantity: e.quantity,
                    price_per_uint: e.price_per_uint,
                    total: e.total,
                    payment: e.payment ? e.payment : 0,
                    discount: e.discount,
                    is_stripe: e.is_stripe,
                    sell_method_discount: e.sell_method_discount,
                    note: e.note,
                    prefix_related: e.document_header.prefix,
                });
            });
            data.voucher.forEach((e,index) => {
                if (e.unit_id)
                {
                    this.rooms.push({rooms: [e.unit]});
                }else{
                    this.rooms.push({rooms: []});
                }
                this.create.voucher_headers.push(e.id);
                this.create.header_details.push({
                    unit_id:null, //for rooms
                    item_id:null, //for service
                    date_from: this.formatDate(e.date),
                    rent_days: 1,
                    date_to: this.formatDate(e.date),
                    check_in_time:'',
                    unit_type: this.$i18n.locale == 'ar' ? e.document.name : e.document.name_e,
                    quantity: 0,
                    price_per_uint: 1,
                    total: parseInt(e.document.attributes.customer) == 1 ? parseFloat(e.amount) : 0,
                    payment: parseInt(e.document.attributes.customer) == -1 ? parseFloat(e.amount) : 0,
                    discount: 0,
                    is_stripe: null,
                    sell_method_discount: 0,
                    note: '',
                    prefix_related: e.prefix,
                });
            });

            this.discountLine();
            this.isLoader = false;
        },
        currentTime()
        {
            const currentTime = new Date();
            const hours = currentTime.getHours();
            const minutes = currentTime.getMinutes();

            const formattedTime = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
            return formattedTime;
        },
    }
};
</script>

<template>
    <div>
        <div v-if="create.id" style="display:none;">
            <AccountStatementPrint :id="'CreatePrintStatement'+'-'+create.id" :document_row_id="create.id"/>
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
                        <b-button v-if="create.id" @click="$bvModal.show(`${'CreatePrintStatement'+'-'+create.id}`)" variant="primary" type="button" class="font-weight-bold px-2 mx-1">
                            {{ $t("general.print") }}
                            <i class="fe-printer"></i>
                        </b-button>
                        <b-button v-else disabled variant="primary" type="button" class="font-weight-bold px-2 mx-1">
                            {{ $t("general.print") }}
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
                            <label class="control-label">{{ $t('general.paymentMethod') }} <span class="text-danger">*</span></label>

                            <multiselect
                                :show-labels="false"
                                :disabled="true"
                                v-model="create.payment_method_id"
                                :options="paymentMethods.map((type) => type.id)"
                                :custom-label=" (opt) => paymentMethods.find((x) => x.id == opt) ? $i18n.locale == 'ar' ? paymentMethods.find((x) => x.id == opt).name: paymentMethods.find((x) => x.id == opt).name_e :''"
                                :class="{'is-invalid':$v.create.payment_method_id.$error || errors.payment_method_id,}"
                            >
                            </multiselect>
                            <div v-if="!$v.create.payment_method_id.required" class="invalid-feedback">
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.payment_method_id">
                                <ErrorMessage v-for="(errorMessage, index) in errors.payment_method_id"
                                              :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">{{ $t('general.receptionist') }} <span class="text-danger">*</span></label>

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
                        <div class="form-group position-relative">
                            <label
                                class="control-label">{{ $t('general.customerType') }}
                            </label>
                            <select
                                :disabled="!create.branch_id"
                                class="custom-select"
                                v-model="create.customer_type"
                                @input="customerTypeHandel"
                            >
                                <option value="1">{{ $t('general.guest') }}</option>
                                <option value="0">{{ $t('general.company') }}</option>
                            </select>

                            <template v-if="errors.customer_type">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.customer_type"
                                    :key="index"
                                >{{ errorMessage }}
                                </ErrorMessage
                                >
                            </template>
                        </div>
                    </div>
                    <div v-if="parseInt(create.customer_type) == 0" class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">{{ $t('general.company') }} <span class="text-danger">*</span></label>
                            <multiselect
                                :show-labels="false"
                                :disabled="!create.branch_id"
                                :internalSearch="false"
                                @search-change="searchCompany"
                                v-model="create.company_id"
                                :options="companies.map((type) => type.id)"
                                :custom-label="(opt) => companies.find((x) => x.id == opt) ? $i18n.locale == 'ar' ? companies.find((x) => x.id == opt).name: companies.find((x) => x.id == opt).name_e:''"
                                :class="{'is-invalid':$v.create.company_id.$error || errors.company_id,}"
                            >
                            </multiselect>
                            <div v-if="!$v.create.company_id.required" class="invalid-feedback">
                                {{ $t("general.fieldIsRequired") }}
                            </div>

                            <template v-if="errors.company_id">
                                <ErrorMessage v-for="(errorMessage, index) in errors.company_id"
                                              :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">{{ $t('general.guest') }} <span class="text-danger">*</span></label>
                            <multiselect
                                :show-labels="false"
                                :disabled="!create.branch_id"
                                :internalSearch="false"
                                @search-change="searchCustomer"
                                @input="escortsHandel"
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
                    <div class="col-md-2">
                        <div class="form-group position-relative">
                            <label class="control-label">
                                {{$t('general.room')}}
                                <span class="text-danger">*</span>
                            </label>
                            <multiselect
                                :show-labels="false"
                                :multiple="true"
                                @input="getDocumentCustomer"
                                v-model="roomsFilter"
                                :options="clientRooms.map((type) => type.id)"
                                :custom-label="(opt) => clientRooms.find((x) => x.id == opt) ? $i18n.locale == 'ar'? clientRooms.find((x) => x.id == opt).name : clientRooms.find((x) => x.id == opt).name_e :''"
                            >
                            </multiselect>
                            <div v-if="!$v.roomsFilter.required" class="invalid-feedback">
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                        </div>
                    </div>
                    <div v-if="create.customer_id" class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">{{ $t('general.escorts') }} </label>
                            <multiselect
                                :multiple="true"
                                :show-labels="false"
                                :disabled="!create.branch_id"
                                v-model="create.attendants"
                                :options="escorts.map((type) => type.id)"
                                :custom-label="(opt) => escorts.find((x) => x.id == opt) ? $i18n.locale == 'ar' ? escorts.find((x) => x.id == opt).name: escorts.find((x) => x.id == opt).name_e:''"
                                :class="{'is-invalid':$v.create.attendants.$error || errors.attendants,}"
                            >
                            </multiselect>

                            <template v-if="errors.attendants">
                                <ErrorMessage v-for="(errorMessage, index) in errors.attendants"
                                              :key="index">{{ errorMessage }}
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
                    <div class="col-md-2">
                        <div class="form-group position-relative">
                            <label class="control-label">
                                {{$t('general.check_in_time')}}
                            </label>
                            <input
                                :disabled="true"
                                type="time"
                                class="form-control"
                                v-model="$v.create.check_in_time.$model"
                                :class="{
                                    'is-invalid': $v.create.check_in_time.$error || errors.check_in_time,
                                    'is-valid': !$v.create.check_in_time.$invalid && !errors.check_in_time,
                                  }"
                            />
                            <template v-if="errors.check_in_time">
                                <ErrorMessage v-for="(errorMessage, index) in errors.check_in_time" :key="index">
                                    {{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group position-relative">
                            <label class="control-label">
                                {{$t('general.check_out_time')}}
                            </label>
                            <input
                                type="time"
                                class="form-control"
                                v-model="$v.create.check_out_time.$model"
                                :class="{
                                    'is-invalid': $v.create.check_out_time.$error || errors.check_out_time,
                                    'is-valid': !$v.create.check_out_time.$invalid && !errors.check_out_time,
                                  }"
                            />
                            <template v-if="errors.check_out_time">
                                <ErrorMessage v-for="(errorMessage, index) in errors.check_out_time" :key="index">
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
                                                <div class="col-2">{{ $t('general.serial_number') }}</div>
                                                <div class="col-1">{{ $t('general.Date') }}</div>
                                                <div class="col-2">{{ $t('general.BookingDiscription') }}</div>
                                                <div class="col-2">{{ $t('general.typeService') }}</div>
                                                <div class="col-2">{{ $t('general.note') }}</div>
                                                <div class="col-1">{{ $t('general.totalDiscount') }}</div>
                                                <div class="col-1">{{ $t('general.charges') }}</div>
                                                <div class="col-1">{{ $t('general.payments') }}</div>

                                            </div>
                                            <div v-for="(item, gIndex) in create.header_details" class="text-95  text-secondary-d3">
                                                <div class="row mb-2 mb-sm-0 py-25" :class="[gIndex % 2 == 0 ? 'bgc-default-l4' : '' ]" >
                                                    <div class="col-2 p-0">
                                                        <input
                                                            :disabled="true"
                                                            v-model.number="$v.create.header_details.$each[gIndex].prefix_related.$model"
                                                            class="form-control"
                                                            type="text"
                                                        />
                                                    </div>
                                                    <div class="col-1 p-0">
                                                        <div class="form-group">
                                                            <date-picker
                                                                :disabled="true"
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
                                                    <div class="col-2 p-0" v-if="create.header_details[gIndex].is_stripe == 0">
                                                        <multiselect
                                                            :show-labels="false"
                                                            :disabled="true"
                                                            @input="addRoomPrice(gIndex)"
                                                            :id="`create-${gIndex}-3`"
                                                            v-model="$v.create.header_details.$each[gIndex].unit_id.$model"
                                                            :options="rooms[gIndex].rooms.map((type) => type.id)"
                                                            :custom-label="(opt) => rooms[gIndex].rooms.find((x) => x.id == opt) ? $i18n.locale == 'ar'? rooms[gIndex].rooms.find((x) => x.id == opt).name : rooms[gIndex].rooms.find((x) => x.id == opt).name_e :''"
                                                            :class="{'is-invalid':$v.create.header_details.$each[gIndex].unit_id.$error || errors.unit_id,}"
                                                        >
                                                        </multiselect>
                                                    </div>
                                                    <div class="col-2 p-0" v-else-if="create.header_details[gIndex].is_stripe == 1">
                                                        <multiselect
                                                            :show-labels="false"
                                                            :disabled="true"
                                                            @input="addServicePrice(gIndex)"
                                                            v-model="$v.create.header_details.$each[gIndex].item_id.$model"
                                                            :options="services.map((type) => type.id)"
                                                            :custom-label="(opt) => services.find((x) => x.id == opt) ? $i18n.locale == 'ar' ? services.find((x) => x.id == opt).name: services.find((x) => x.id == opt).name_e : ''"
                                                            :class="{'is-invalid':$v.create.header_details.$each[gIndex].item_id.$error || errors.item_id,}"
                                                        >
                                                        </multiselect>
                                                    </div>
                                                    <div class="col-2 p-0" v-else>
                                                        <input
                                                            :disabled="true"
                                                            v-model.number="create.header_details[gIndex].unit_type"
                                                            class="form-control"
                                                            type="text"
                                                        />
                                                    </div>
                                                    <div class="col-2 p-0">
                                                        <select
                                                            :disabled="true"
                                                            class="custom-select"
                                                            v-model="$v.create.header_details.$each[gIndex].is_stripe.$model"
                                                            @input="changeStrip(gIndex)"
                                                        >
                                                            <option value="0">{{ $t('general.accommodation') }}</option>
                                                            <option value="1">{{ $t('general.ServiceRequest') }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-2">
                                                        <input
                                                            :disabled="true"
                                                            v-model="$v.create.header_details.$each[gIndex].note.$model"
                                                            class="form-control"
                                                            type="text"
                                                        />
                                                    </div>
                                                    <div class="col-1 p-0">
                                                        <input
                                                            :disabled="true"
                                                            @input="discountLine"
                                                            @keyup="addLineEnter($event)"
                                                            v-model.number="$v.create.header_details.$each[gIndex].discount.$model"
                                                            class="form-control" step="any"
                                                            type="number"
                                                        />
                                                    </div>
                                                    <div class="col-1 p-0">
                                                        <input
                                                            :disabled="true"
                                                            @input="PricePerUnitByTotal(gIndex)"
                                                            @keyup="addLineEnter($event)"
                                                            v-model.number="$v.create.header_details.$each[gIndex].total.$model"
                                                            class="form-control" step="any"
                                                            type="number"
                                                        />
                                                    </div>
                                                    <div class="col-1 p-0">
                                                        <input
                                                            :disabled="true"
                                                            v-model.number="create.header_details[gIndex].payment"
                                                            class="form-control" step="any"
                                                            type="number"
                                                        />
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
                                                                {{ !create.total_invoice ? '0.000' : create.total_invoice }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="row align-items-center bgc-primary-l3">
                                                        <div class="col-7 text-right">
                                                            {{ $t("general.InvoiceDiscount") }}
                                                        </div>
                                                        <div class="col-5">
                                                            <span class="text-150 text-success-d3 opacity-2">
                                                                {{ !create.invoice_discount ? '0.000' : create.invoice_discount }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="row align-items-center bgc-primary-l3">
                                                        <div class="col-7 text-right">
                                                            {{ $t("general.NetInvoice") }}
                                                        </div>
                                                        <div class="col-5">
                                                            <span class="text-150 text-success-d3 opacity-2">
                                                                {{ !create.net_invoice ? '0.000' : create.net_invoice }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="row align-items-center bgc-primary-l3">
                                                        <div class="col-7 text-right">
                                                            {{ $t("general.paid") }}
                                                        </div>
                                                        <div class="col-5">
                                                            <span class="text-150 text-success-d3 opacity-2">
                                                                {{ !create.paid ? '0.000' : create.paid }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="row align-items-center bgc-primary-l3">
                                                        <div class="col-7 text-right">
                                                            {{ $t("general.remaining") }}
                                                        </div>
                                                        <div class="col-5">
                                                            <span class="text-150 text-success-d3 opacity-2">
                                                                {{ create.break_settlement[0].amount }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <hr />
                                            <div>
                                                <span class="text-secondary-d1 text-105">{{$t("general.Thank_you") }}</span>
                                                <!--                                                <a @click.prevent="addNewField" class="btn btn-info btn-bold px-4 float-right mt-3 mx-2 mt-lg-0">-->
                                                <!--                                                    {{$t("general.AddNewLine") }}-->
                                                <!--                                                </a>-->
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
