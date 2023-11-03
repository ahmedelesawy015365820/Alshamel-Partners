b<script>
import Layout from "../../../layouts/main";
import PageHeader from "../../../../components/general/Page-header";
import permissionGuard from "../../../../helper/permission";

import FullCalendar from "@fullcalendar/vue";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import interactionPlugin from "@fullcalendar/interaction";
import bootstrapPlugin from "@fullcalendar/bootstrap";
import listPlugin from "@fullcalendar/list";
import Swal from "sweetalert2";
import {maxLength, minLength, required, requiredIf} from "vuelidate/lib/validators";
import adminApi from "../../../../api/adminAxios";
import Multiselect from "vue-multiselect";
import translation from "../../../../helper/mixin/translation-mixin";
import ErrorMessage from "../../../../components/widgets/errorMessage";
import DocumentHeaderDetail from "../../../../components/document/withItem/documentHeaderDetail";
import loader from "../../../../components/general/loader";
import {formatDateOnly} from "../../../../helper/startDate";
import DatePicker from "vue2-datepicker";

/**
 * Calendar component
 */
export default {
    page: {
        title: "Reservation",
        meta: [{name: "description", content: 'Reservation'}],
    },
    components: {
        Layout,
        FullCalendar,
        PageHeader,
        Multiselect,
        ErrorMessage,
        loader,
        DatePicker,
        DocumentHeaderDetail
    },
    mixins: [translation],
    beforeRouteEnter(to, from, next) {
         next((vm) => {
                return permissionGuard(vm, "Reservation Calender", "all Reservation");
         });
    },
    beforeMount() {
        let l = new Date();
        this.search.months_number = l.getMonth() + 1;
        this.search.year = l.getFullYear();
        this.getStatus();
        this.getfloors();
        this.getRooms();
        // this.getData();
    },
    mounted() {
        this.$store.dispatch('locationIp/getIp');
    },
    data() {
        return {
            isSearch: 0,
            title: "Calendar",
            items: [
                {
                    text: "Minton",
                },
                {
                    text: "Apps",
                },
                {
                    text: "Calendar",
                    active: true,
                },
            ],
            create: {
                employee_id: null,
                department_id: null,
                customer_id:null,
                contact_person: '',
                contact_phone:'',
                task_title:'',
                department_task_id:null,
                owners:[],
                supervisors:[],
                notifications:[],
                status_id:null,
                execution_date:this.formatDate(new Date()),
                start_time:'',
                end_time:'',
                execution_duration:'0 Day 0 Minutes',
                notification_date:this.formatDate(new Date()),
                execution_end_date:this.formatDate(new Date()),
                note:'',
                media: [],
                type: 'general',
                equipment_id: null,
                location_id: null,
                priority_id: null,
                admin_note: '',
                is_closed: 0,
                task_requirement: ''
            },
            edit: {
                employee_id: null,
                department_id: null,
                customer_id:null,
                contact_person: '',
                contact_phone:'',
                task_title:'',
                department_task_id:null,
                owners:[],
                supervisors:[],
                notifications:[],
                status_id:null,
                execution_date:this.formatDate(new Date()),
                start_time:'',
                end_time:'',
                execution_duration:'0 Day 0 Minutes',
                notification_date:this.formatDate(new Date()),
                execution_end_date:this.formatDate(new Date()),
                note:'',
                old_media: [],
                type: '',
                equipment_id: null,
                location_id: null,
                priority_id: null,
                admin_note: '',
                is_closed: 0,
                task_requirement: ''
            },
            calendarEvents: [],
            calendarOptions: {
                headerToolbar: {
                    left: "prev,next today",
                    center: "title",
                    right: "dayGridMonth,timeGridWeek,timeGridDay,listWeek",
                },
                plugins: [
                    dayGridPlugin,
                    timeGridPlugin,
                    interactionPlugin,
                    bootstrapPlugin,
                    listPlugin,
                ],
                views: {
                    timeGrid: {
                        dayMaxEventRows: 3 // adjust to 6 only for timeGridWeek/timeGridDay
                    }
                },
                initialView: "dayGridMonth",
                themeSystem: "bootstrap",
                initialEvents: [],
                events: [],
                editable: true,
                droppable: true,
                eventResizableFromStart: true,
                dateClick: this.editEvent,
                eventClick: this.editEvent ,
                // eventsSet: this.handleEvents,
                weekends: true,
                selectable: true,
                selectMirror: true,
                dayMaxEvents: true,
                dayMaxEventRows: true,
                customButtons: {
                    prev: { // this overrides the prev button
                        text: "PREV",
                        click: () => {
                            if(this.search.months_number != 1){
                                this.search.months_number -= 1;
                            }else {
                                this.search.months_number = 12;
                                this.search.year -= 1
                            }
                            this.getData();
                            let calendarApi = this.$refs.fullCalendar.getApi();
                            calendarApi.prev();
                        }
                    },
                    next: { // this overrides the next button
                        text: "NEXT",
                        click: () => {
                            console.log(1)
                            if(this.search.months_number != 12){
                                this.search.months_number += 1;
                            }else {
                                this.search.months_number = 1;
                                this.search.year += 1
                            }
                            this.getData();
                            let calendarApi = this.$refs.fullCalendar.getApi();
                            calendarApi.next();
                        }
                    }
                }
            },
            showModal: false,
            eventModal: false,
            submitted: false,
            submit: false,
            newEventData: {},
            deleteId: {},
            search: {
                unit_status_id: null,
                unit_id: null,
                date_from: this.formatDate(new Date()),
                date_to: this.formatDate(new Date()),
                months_number: 0,
                year: 0
            },
            is_disabled: false,
            isLoader: false,
            errors: {},
            statuses: [],
            units: [],
            document_id: null,
            documents:[],
            floors: [],
            rooms: [],
            floor_id: null,
            document_create:{
                document_id: null,
                unit_id: null,
                date_from: this.formatDate(new Date()),
                rent_days:0,
                date_to: this.formatDate(new Date()),
            },
            check_availability: false,
            document:''
        };
    },
    validations: {
        search: {
            date_from: {required},
            date_to: {required}
        },
        document_create: {
            document_id: {required},
            unit_id: {required},
            date_from: {required},
            rent_days: {required},
            date_to: {required},
        }
    },
    methods: {
        getToday(){
            this.search.date_from = this.formatDate(new Date());
            this.search.date_to = this.formatDate(new Date());
            this.getRooms();
        },
        getNameUnit(ChooseUnits){
            let data = '';
            ChooseUnits.forEach((el,index) => {
                data += `${ this.$i18n.locale == 'ar' ? this.units.find(e => e.id == el).name: this.units.find(e => e.id == el).name_e },`
            });
            return data;
        },
        formatDate(value) {
            return formatDateOnly(value);
        },
        isPermission(item) {
            if (this.$store.state.auth.type == 'user'){
                return this.$store.state.auth.permissions.includes(item)
            }
            return true;
        },
        getData(){
            this.isLoader = true;

            adminApi.post(`/filter-calender`,this.search)
                .then((res) => {
                    let l = res.data.data;
                    this.calendarOptions.initialEvents = l;
                    this.calendarOptions.events = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: 'error',
                        title: `${this.$t('general.Error')}`,
                        text: `${this.$t('general.Thereisanerrorinthesystem')}`,
                    });
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        getDataSearch(id = null){
            this.isLoader = true;
            this.search.unit_id = id;

            adminApi.post(`/filter-calender/get-rooms-calender-filter`, {
                ...this.search,
                unit_id: [id]
            })
                .then((res) => {
                    let l = res.data.data;
                    this.isSearch = 1;
                    this.calendarOptions.initialEvents = l;
                    this.calendarOptions.events = l;
                    this.$bvModal.hide(`search`);
                    let dateCurrent = new Date();
                    let dateCustom = new Date(this.search.date_from);
                    if(
                        (dateCustom.getMonth() + 1) != (dateCurrent.getMonth() + 1) ||
                        dateCustom.getFullYear() != dateCurrent.getFullYear()
                    ){
                        this.search.year = dateCustom.getFullYear();
                        let calendarApi = this.$refs.fullCalendar.getApi();
                        if(dateCustom.getFullYear() != dateCurrent.getFullYear()){
                            if(dateCustom.getFullYear() > dateCurrent.getFullYear()){
                                calendarApi.nextYear();
                            }else {
                                calendarApi.prevYear();
                            }
                        }

                        if((dateCustom.getMonth() + 1) != (dateCurrent.getMonth() + 1)){
                            let i = 0;
                            while (i < this.search.months_number) {
                                calendarApi.prev();
                                i++;
                            }
                            this.search.months_number = dateCustom.getMonth() + 1;
                            let g = 0;
                            while (g < dateCustom.getMonth() + 1) {
                                calendarApi.next();
                                g++;
                            }
                        }
                    }
                })
                .catch((err) => {
                    Swal.fire({
                        icon: 'error',
                        title: `${this.$t('general.Error')}`,
                        text: `${this.$t('general.Thereisanerrorinthesystem')}`,
                    });
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        /**
         * Modal form submit
         */
        // eslint-disable-next-line no-unused-vars
        AddSubmit() {
            this.create.company_id = JSON.parse(localStorage.getItem("company_id"));
            this.$v.create.$touch();
            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};

                adminApi.post(`/tasks`, {
                    ...this.create,
                    execution_date: this.create.execution_date.slice(0,10)  + (this.create.start_time ? ` ${this.create.start_time}` : ' 00:00:00'),
                    execution_end_date: this.create.execution_end_date.slice(0,10)  + (this.create.end_time ? ` ${this.create.end_time}` : ' 00:00:00'),
                    company_id: this.$store.getters["auth/company_id"]
                }).then((res) => {
                        this.is_disabled = true;
                        this.task_id = res.data.data.id;
                        this.getData();
                        setTimeout(() => {
                            Swal.fire({
                                icon: 'success',
                                text: `${this.$t('general.Addedsuccessfully')}`,
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }, 500);
                    })
                    .catch((err) => {
                        if (err.response.data) {
                            this.errors = err.response.data.errors;
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: `${this.$t('general.Error')}`,
                                text: `${this.$t('general.Thereisanerrorinthesystem')}`,
                            });
                        }
                    }).finally(() => {
                    this.isLoader = false;
                });
            }
        },
        // eslint-disable-next-line no-unused-vars
        /**
         * Edit event modal submit
         */
        // eslint-disable-next-line no-unused-vars
        editSubmit() {
            this.edit.company_id = JSON.parse(localStorage.getItem("company_id"));
            this.$v.edit.$touch();
            this.images.forEach((e) => {
                this.edit.old_media.push(e.id);
            });

            if (this.$v.edit.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                adminApi.put(`/tasks/${this.task_id}`, {
                    ...this.edit,
                    execution_date: this.edit.execution_date.slice(0,10) +  (this.edit.start_time ? ` ${this.edit.start_time}` : ' 00:00:00'),
                    execution_end_date: this.edit.execution_end_date.slice(0,10)  +  (this.edit.end_time ? ` ${this.edit.end_time}`  : ' 00:00:00')
                })
                    .then((res) => {
                        this.getData();
                        this.eventModal = false;
                        setTimeout(() => {
                            Swal.fire({
                                icon: 'success',
                                text: `${this.$t('general.Editsuccessfully')}`,
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }, 500);
                    })
                    .catch((err) => {
                        if (err.response.data) {
                            this.errors = err.response.data.errors;
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: `${this.$t('general.Error')}`,
                                text: `${this.$t('general.Thereisanerrorinthesystem')}`,
                            });
                        }
                    }).finally(() => {
                    this.isLoader = false;
                });
            }
        },
        /**
         * Modal open for add event
         */
        dataCreate() {
            let time = new Date();
            this.create = {
                employee_id: null,
                department_id: null,
                customer_id: null,
                contact_person: '',
                contact_phone: '',
                task_title: '',
                department_task_id: null,
                owners: [],
                supervisors: [],
                notifications: [],
                execution_date: this.formatDate(this.newEventData.date),
                start_time: `${time.getHours()}:${time.getMinutes()}:${time.getSeconds()}`,
                end_time: `${time.getHours() + 2}:${time.getMinutes()}:${time.getSeconds()}`,
                execution_duration: '0 Day 0 Minutes',
                notification_date: this.formatDate(this.newEventData.date),
                execution_end_date: this.formatDate(this.newEventData.date),
                note:'',
                type: 'general',
                equipment_id: null,
                location_id: null,
                priority_id: null,
                admin_note: '',
                is_closed: 0,
                task_requirement: ''
            };
            this.department_id = null;
            this.task_id = null;
            this.$nextTick(() => {
                this.$v.$reset()
            });
            this.errors = {};
            this.media = {};
            this.images = [];
        },
        resetModalHidden() {
            this.create = {
                employee_id: null,
                department_id: null,
                customer_id: null,
                contact_person: '',
                contact_phone: '',
                task_title: '',
                department_task_id: null,
                owners: [],
                supervisors: [],
                notifications: [],
                status_id: null,
                execution_date: this.formatDate(new Date()),
                start_time: '',
                end_time: '',
                execution_duration: '0 Day 0 Minutes',
                notification_date: this.formatDate(new Date()),
                execution_end_date: this.formatDate(new Date()),
                note: '',
                media: null,
                type: 'general',
                equipment_id: null,
                location_id: null,
                priority_id: null,
                admin_note: '',
                is_closed: 0,
                task_requirement: ''
            };
            this.$nextTick(() => {
                this.$v.$reset()
            });
            this.errors = {};
            this.images = [];
            this.department_id = null;
            this.task_id = null;
            this.is_disabled = false;
            this.showModal = false;
        },
        async dateClicked(info) {
            this.newEventData = info;
            this.showModal = true;
            await this.getStatus();
            this.dataCreate();
            await this.getEmployees();
            await this.getEmployeeDepartments();
            await this.getDepartment();
            await this.getPriority();
            this.calcDurationCreate();
            this.showPhoto = "../../../../../images/img-1.png";
        },
        resetForm() {
            this.dataCreate();
            this.is_disabled = false;
        },
        /**
         * Modal open for edit event
         */
        confirm() {
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
                        .delete(`/tasks/${this.task_id}`)
                        .then((res) => {
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
        },
        handleEvents(events) {
            this.currentEvents = events;
        },
        resetModalHiddenSearch() {
            this.$nextTick(() => {
                this.$v.$reset()
            });
            this.errors = {};
            this.$bvModal.hide(`search`);
        },
        resetModalSearch() {
            this.getStatus();
            this.getUnits();
            this.is_disabled = false;
            this.$nextTick(() => {
                this.$v.$reset()
            });
            this.errors = {};
        },
        // start status
        getStatus() {
            adminApi
                .get(`/booking/unit-status`)
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

        },
        showStatusModal() {
            if (this.create.status_id == 0) {
                this.$bvModal.show("status-create");
                this.create.status_id = null;
            }
        },
        getfloors() {
            adminApi
                .get(`/booking/floors`)
                .then((res) => {
                    let l = res.data.data;
                    this.floors = l;
                    this.floor_id = 1;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                })

        },
        getRooms(id = 1) {
            if(this.search.date_from && this.search.date_to){
                this.floor_id = id;
                this.isLoader = true;
                adminApi
                    .get(`/filter-calender/get-rooms-floors?booking_floor_id=${id}&date_from=${this.search.date_from}&date_to=${this.search.date_to}${this.search.unit_status_id?'&unit_status_id='+this.search.unit_status_id:''}`)
                    .then((res) => {
                        let l = res.data.data;
                        this.rooms = l;
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
        showStatusModalEdit() {
            if (this.edit.status_id == 0) {
                this.$bvModal.show("status-create");
                this.edit.status_id = null;
            }
        },
        //start employee
        isActiveFun(item){
            if(Array.isArray(item)){
                return this.search.unit_id == item[0].id;
            }else {
                return this.search.unit_id == item.id;
            }
        },
        isClassName(item){
            if(Array.isArray(item)){
                return item[0].className;
            }else {
                return item.className;
            }
        },
        changeStatus(item){
            if(this.search.unit_status_id == item){
                this.search.unit_status_id = null
            }else {
                this.search.unit_status_id = item;
            }
        },

        //start add document
        getUnits() {
            adminApi
                .get(`/booking/units/get-drop-down`)
                .then((res) => {
                    let l = res.data.data;
                    this.units = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });

        },
        getDoc() {
            adminApi
                .get(`/document?ModuleStatuses=1`)
                .then((res) => {
                    let l = res.data.data;
                    this.documents = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                })

        },
        checkUnits() {
            this.isLoader = true;
            adminApi
            .get(`/filter-calender/get-rooms-floors?&date_from=${this.document_create.date_from}&date_to=${this.document_create.date_to}`)
                .then((res) => {
                    let l = res.data.data;
                    let unit_availability = 1;
                    l.forEach((el,index) => {
                        if (Array.isArray(el))
                        {
                            if (el.find(elm => elm.id == this.document_create.unit_id))
                            {
                                unit_availability = el.find(elm => elm.id == this.document_create.unit_id).available;
                            }
                        }else{
                            if (el.id == this.document_create.unit_id)
                            {
                                unit_availability = el.available
                            }
                        }
                    });
                        if(unit_availability)
                        {
                            this.check_availability = true

                        }else{
                            this.check_availability = false
                            Swal.fire({
                                icon: "error",
                                title: `${this.$t("general.Error")}`,
                                text: `${this.$t("general.ThisRoomIsNotAvailableDuringThisPeriod")}`,
                            });
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
        editEvent(info,room_id=null) {
            console.log(info)
            let date_from = new Date(this.search.date_from);
            let date_to = new Date(this.search.date_to);
            let difference = date_to.getTime() - date_from.getTime();
            let TotalDays = Math.ceil(difference / (1000 * 3600 * 24));
            this.check_availability = false;
            this.getDoc();
            this.getUnits();
            this.document_create={
                document_id:null,
                unit_id:room_id?room_id:null,
                date_from: this.search.date_from,
                rent_days:TotalDays,
                date_to: this.search.date_to,
            }
            if(this.document_create.unit_id)
            {
                this.checkUnits();
            }
            this.eventModal = true;
        },
        calcDateTo()
        {
            let days = parseInt(this.document_create.rent_days);
            let date_from = new Date(this.document_create.date_from);
            date_from.setDate(date_from.getDate() + days);
            this.document_create.date_to = new Date(date_from).toISOString().slice(0, 10);
            if(this.document_create.unit_id)
            {
                this.checkUnits();
            }
        },
        submitDocument()
        {
            this.$v.document_create.$touch();
            if (this.$v.document_create.$invalid || !this.check_availability) {
                return;
            } else {
                this.$bvModal.show(`create`)
            }
        },
        getDocumentData()
        {
            if (this.document_create.document_id)
            {
                this.document = this.documents.find((x) => x.id == this.document_create.document_id)
            }
        }
    },
}
</script>

<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>
        <DocumentHeaderDetail :document="document" :document_id="document_create.document_id" :other_data="document_create" @created="getRooms()" />
        <div class="calender-card-body position-relative">
            <!--       start loader       -->
            <loader size="large" v-if="isLoader" />
            <!--       end loader       -->
            <div class="row justify-content-center position-relative">

                <div class="col-6">
                    <div class="d-inline-flex">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">
                                    {{ $t('general.startDate') }}
                                </label>
                                <date-picker
                                    v-model="search.date_from"
                                    type="date"
                                    format="YYYY-MM-DD"
                                    valueType="format"
                                ></date-picker>
                                <div
                                    v-if="!$v.search.date_from.required"
                                    class="invalid-feedback"
                                >
                                    {{ $t("general.fieldIsRequired") }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">
                                    {{ $t('general.endDate') }}
                                </label>
                                <date-picker
                                    v-model="search.date_to"
                                    type="date"
                                    format="YYYY-MM-DD"
                                    valueType="format"
                                ></date-picker>
                                <div
                                    v-if="!$v.search.date_to.required"
                                    class="invalid-feedback"
                                >
                                    {{ $t("general.fieldIsRequired") }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4" style="margin: 23px 0 0;">
                            <b-button
                                variant="success"
                                type="button" class="mx-1"
                                v-if="!isLoader"
                                @click.prevent="getRooms()"
                            >
                                {{ $t('general.Search') }}
                            </b-button>

                            <b-button
                                variant="secondary"
                                type="button" class="mx-1"
                                v-if="!isLoader"
                                @click.prevent="getToday"
                            >
                                {{ $t('general.today') }}
                            </b-button>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <ul class="list-unstyled status-choose text-right mt-3">
                        <li
                            @click.prevent="changeStatus(status.id)"
                            :class="[search.unit_status_id == status.id? 'active':'',status.color]"
                            v-for="(status,index) in statuses" :key="index"
                        >
                            {{ $i18n.locale == 'ar' ? status.name : status.name_e }}
                        </li>
                    </ul>
                </div>
                <div :class="['col-12','mt-2']">
                   <ul class="list-unstyled floor-choose">
                       <li @click.prevent="getRooms(floor.id)" :class="floor_id == floor.id? 'active':''" v-for="(floor,index) in floors" :key="index">
                           {{ $i18n.locale == 'ar' ? floor.name : floor.name_e }}
                       </li>
                   </ul>
                </div>

                <div :class="['col-12','mt-2']">
                    <ul class="list-unstyled room-choose text-center">
                        <li v-for="(room,index) in rooms" :key="index" class="mt-1 text-center" :class="[isClassName(room),isActiveFun(room)?'active':'']">
                            <template v-if="Array.isArray(room)">
                                <b-dropdown
                                    v-if="room.length > 1"
                                    class="dropdown-room"
                                    :html="`${room[0]['title']} ^`"
                                    style="margin: 0 4px; font-size: 14px;"
                                >
                                    <template
                                        v-for="da in room"
                                    >
                                        <b-dropdown-item :class="da['className']" @click.prevent="getDataSearch(da.id)">
                                            {{ da['title'] }}
                                        </b-dropdown-item>
                                    </template>
                                </b-dropdown>
                                <template v-if="room.length == 1">
                                    <div style="display: inline-block; margin: 0 4px; font-size: 14px;" @click.prevent="getDataSearch(room[0].id)">
                                        {{ room[0]['title'] }}
                                    </div>
                                </template>
                                <i style="font-size: 22px;" @click.prevent="editEvent($event,room[0].id)" class="fe-briefcase"></i>
                            </template>
                            <template v-else>
                                <div style="display: inline-block; margin: 0 4px; font-size: 14px;" @click.prevent="getDataSearch(room.id)">{{ room['title'] }}</div>
                                <i style="font-size: 22px;" @click.prevent="editEvent($event,room.id)" class="fe-briefcase"></i>
                            </template>

                        </li>
                    </ul>
                </div>

                <div class="col-12 mt-3">
                    <div class="app-calendar">
                        <FullCalendar
                            ref="fullCalendar"
                            :options="calendarOptions"
                        ></FullCalendar>
                    </div>
                </div>
                <!-- Edit Modal -->
                <b-modal
                    v-model="eventModal"
                    :title="$t('general.document')"
                    title-class="font-18"
                    body-class=""
                    :hide-footer="true"
                    hide-footer
                >
                    <form>
                        <div :class="['d-flex justify-content-end']">
                            <template v-if="!is_disabled && check_availability">
                                <a class="btn btn-success mx-1" v-if="!isLoader" @click.prevent="submitDocument">
                                    {{ $t("general.Add") }}
                                </a>
                                <b-button variant="success" class="mx-1" disabled v-else>
                                    <b-spinner small></b-spinner>
                                    <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                </b-button>

                            </template>
                            <b-button
                                variant="danger"
                                type="button"
                                @click.prevent="eventModal = false"
                            >
                                {{ $t('general.Cancel') }}
                            </b-button>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ $t('general.documentType') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <multiselect
                                        :show-labels="false"
                                        @input="getDocumentData"
                                        v-model="document_create.document_id"
                                        :options="documents.map((type) => type.id)"
                                        :custom-label="(opt) => documents.find((x) => x.id == opt)?
                                                $i18n.locale == 'ar'?
                                                documents.find((x) => x.id == opt).name : documents.find((x) => x.id == opt).name_e: null
                                            "
                                        :class="{'is-invalid': $v.document_create.document_id.$error}"
                                    >
                                    </multiselect>
                                    <div v-if="!$v.document_create.document_id.required" class="invalid-feedback">
                                        {{ $t("general.fieldIsRequired") }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ $t('general.room') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <multiselect
                                        :show-labels="false"
                                        @input="checkUnits"
                                        v-model="document_create.unit_id"
                                        :options="units.map((type) => type.id)"
                                        :custom-label="(opt) => units.find((x) => x.id == opt)?
                                                $i18n.locale == 'ar'?
                                                units.find((x) => x.id == opt).name : units.find((x) => x.id == opt).name_e: null
                                            "
                                        :class="{'is-invalid': $v.document_create.unit_id.$error}"
                                    >
                                    </multiselect>
                                    <div v-if="!$v.document_create.unit_id.required" class="invalid-feedback">
                                        {{ $t("general.fieldIsRequired") }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ $t('general.fromDate') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <date-picker
                                        @input="calcDateTo"
                                        type="date"
                                        v-model="$v.document_create.date_from.$model"
                                        format="YYYY-MM-DD"
                                        valueType="format"
                                        :confirm="false"
                                        :class="{
                                            'is-invalid': $v.document_create.date_from.$error,
                                            'is-valid':!$v.document_create.date_from.$invalid,
                                            }"
                                    ></date-picker>
                                    <div v-if="!$v.document_create.date_from.required" class="invalid-feedback">
                                        {{ $t("general.fieldIsRequired") }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ $t('general.bookingDays') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        required
                                        @input="calcDateTo"
                                        type="number"
                                        class="form-control"
                                        v-model="$v.document_create.rent_days.$model"
                                        :class="{'is-invalid':$v.document_create.rent_days.$error }"
                                    />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ $t('general.toDate') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <date-picker
                                        :disabled="true"
                                        type="date"
                                        v-model="$v.document_create.date_to.$model"
                                        format="YYYY-MM-DD"
                                        valueType="format"
                                        :confirm="false"
                                        :class="{
                                            'is-invalid':$v.document_create.date_to.$error,
                                            'is-valid':!$v.document_create.date_to.$invalid,
                                            }"
                                    ></date-picker>
                                    <div v-if="!$v.document_create.date_to.required" class="invalid-feedback">
                                        {{ $t("general.fieldIsRequired") }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </b-modal>
<!--                <b-modal-->
<!--                    v-model="showModal"-->
<!--                    :title="getCompanyKey('boardRent_task_create_form')"-->
<!--                    title-class="font-18"-->
<!--                    dialog-class="modal-full-width"-->
<!--                    body-class=""-->
<!--                    :hide-footer="true"-->
<!--                    @hidden="resetModalHidden"-->
<!--                >-->
<!--                    <form>-->
<!--                        <div :class="['d-flex justify-content-end position-absolute',$i18n.locale == 'en' ? 'button-left' : 'button-right']">-->
<!--                                    <b-button-->
<!--                                        variant="success"-->
<!--                                        :disabled="!is_disabled"-->
<!--                                        @click.prevent="resetForm"-->
<!--                                        type="button" :class="['font-weight-bold px-2',is_disabled?'mx-2': '']"-->
<!--                                    >-->
<!--                                        {{ $t('general.AddNewRecord') }}-->
<!--                                    </b-button>-->
<!--                                    <template v-if="!is_disabled">-->
<!--                                        <b-button-->
<!--                                            variant="success"-->
<!--                                            type="button" class="mx-1"-->
<!--                                            v-if="!isLoader"-->
<!--                                            @click.prevent="AddSubmit"-->
<!--                                        >-->
<!--                                            {{ $t('general.Add') }}-->
<!--                                        </b-button>-->

<!--                                        <b-button variant="success" class="mx-1" disabled v-else>-->
<!--                                            <b-spinner small></b-spinner>-->
<!--                                            <span class="sr-only">{{ $t('login.Loading') }}...</span>-->
<!--                                        </b-button>-->
<!--                                    </template>-->
<!--                                    &lt;!&ndash; Emulate built in modal footer ok and cancel button actions &ndash;&gt;-->

<!--                                    <b-button variant="danger" type="button" @click.prevent="resetModalHidden">-->
<!--                                        {{ $t('general.Cancel') }}-->
<!--                                    </b-button>-->
<!--                                </div>-->
<!--                        <b-tabs nav-class="nav-tabs nav-bordered">-->
<!--                                <b-tab :title="$t('general.DataEntry')" active>-->
<!--                                    <div class="row">-->
<!--                                        <div class="col-md-4">-->
<!--                                            <div class="form-group">-->
<!--                                                <label>{{ getCompanyKey('boardRent_task_type') }}<span class="text-danger">*</span></label>-->
<!--                                                <multiselect-->
<!--                                                    @input="showTypeModal('create')"-->
<!--                                                    v-model="create.type"-->
<!--                                                    :options="types.map((type) => type)"-->
<!--                                                    :class="{'is-invalid': $v.create.type.$error || errors.type,'is-valid': !$v.create.type.$invalid && !errors.type,}"-->
<!--                                                >-->
<!--                                                </multiselect>-->
<!--                                                <div v-if="!$v.create.type.required" class="invalid-feedback">-->
<!--                                                    {{ $t("general.fieldIsRequired") }}-->
<!--                                                </div>-->

<!--                                                <template v-if="errors.type">-->
<!--                                                    <ErrorMessage v-for="(errorMessage, index) in errors.type"-->
<!--                                                                  :key="index">{{ errorMessage }}-->
<!--                                                    </ErrorMessage>-->
<!--                                                </template>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <div class="col-md-4">-->
<!--                                            <div class="form-group">-->
<!--                                                <label>{{ getCompanyKey('employee') }}<span class="text-danger">*</span></label>-->
<!--                                                <multiselect-->
<!--                                                    @input="showEmployeeModal"-->
<!--                                                    v-model="create.employee_id"-->
<!--                                                    :options="employeeDepartments.map((type) => type.id)"-->
<!--                                                    :custom-label=" (opt) => $i18n.locale == 'ar' ? employeeDepartments.find((x) => x.id == opt).name : employeeDepartments.find((x) => x.id == opt).name_e "-->
<!--                                                    :class="{'is-invalid': $v.create.employee_id.$error || errors.employee_id,'is-valid': !$v.create.employee_id.$invalid && !errors.employee_id,}"-->
<!--                                                >-->
<!--                                                </multiselect>-->
<!--                                                <div v-if="!$v.create.employee_id.required" class="invalid-feedback">-->
<!--                                                    {{ $t("general.fieldIsRequired") }}-->
<!--                                                </div>-->

<!--                                                <template v-if="errors.employee_id">-->
<!--                                                    <ErrorMessage v-for="(errorMessage, index) in errors.employee_id"-->
<!--                                                                  :key="index">{{ errorMessage }}-->
<!--                                                    </ErrorMessage>-->
<!--                                                </template>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <div class="col-md-4">-->
<!--                                            <div class="form-group position-relative">-->
<!--                                                <label class="control-label">-->
<!--                                                    {{ getCompanyKey("boardRent_task_department") }}-->
<!--                                                    <span class="text-danger">*</span>-->
<!--                                                </label>-->
<!--                                                <multiselect-->
<!--                                                    @input="showDepartmentModal" v-model="create.department_id"-->
<!--                                                    :options="departments.map((type) => type.id)"-->
<!--                                                    :custom-label="(opt) => $i18n.locale == 'ar' ? departments.find((x) => x.id == opt).name : departments.find((x) => x.id == opt).name_e"-->
<!--                                                    :class="{'is-invalid': $v.create.department_id.$error || errors.department_id,'is-valid': !$v.create.department_id.$invalid && !errors.department_id,}"-->
<!--                                                >-->
<!--                                                </multiselect>-->
<!--                                                <div v-if="$v.create.department_id.$error || errors.department_id" class="text-danger">-->
<!--                                                    {{ $t("general.fieldIsRequired") }}-->
<!--                                                </div>-->
<!--                                                <template v-if="errors.department_id">-->
<!--                                                    <ErrorMessage v-for="(errorMessage, index) in errors.department_id" :key="index">{{ errorMessage }}-->
<!--                                                    </ErrorMessage>-->
<!--                                                </template>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <div class="col-md-12">-->
<!--                                            <hr style="margin: 10px 0 !important;  border-top: 1px solid rgb(141 163 159 / 42%)" />-->
<!--                                        </div>-->
<!--                                        <div v-if="create.type == 'customer'" class="col-md-4">-->
<!--                                            <div class="form-group position-relative">-->
<!--                                                <label-->
<!--                                                    class="control-label">{{ getCompanyKey("customer") }}-->
<!--                                                    <span class="text-danger">*</span>-->
<!--                                                </label>-->
<!--                                                <multiselect-->
<!--                                                    @input="showCustomerModal"-->
<!--                                                    :internalSearch="false"-->
<!--                                                    @search-change="searchCustomer"-->
<!--                                                    v-model="$v.create.customer_id.$model"-->
<!--                                                    :options="customers.map((type) => type.id)"-->
<!--                                                    :custom-label="(opt) => $i18n.locale == 'ar' ?customers.find((x) => x.id == opt).name:customers.find((x) => x.id == opt).name_e"-->
<!--                                                    :class="{'is-invalid': $v.create.customer_id.$error || errors.customer_id,'is-valid': !$v.create.customer_id.$invalid && !errors.customer_id,}"-->
<!--                                                >-->
<!--                                                </multiselect>-->

<!--                                                <template v-if="errors.customer_id">-->
<!--                                                    <ErrorMessage-->
<!--                                                        v-for="(errorMessage, index) in errors.customer_id"-->
<!--                                                        :key="index"-->
<!--                                                    >{{ errorMessage }}-->
<!--                                                    </ErrorMessage-->
<!--                                                    >-->
<!--                                                </template>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <div v-if="create.type == 'customer'" class="col-md-4">-->
<!--                                            <div class="form-group">-->
<!--                                                <label class="control-label">-->
<!--                                                    {{ getCompanyKey('general_customer_contact_person') }}-->
<!--                                                    <span class="text-danger">*</span>-->
<!--                                                </label>-->
<!--                                                <input-->
<!--                                                    type="text"-->
<!--                                                    class="form-control"-->
<!--                                                    data-create="9"-->
<!--                                                    v-model="$v.create.contact_person.$model"-->
<!--                                                    :class="{-->
<!--                                                                'is-invalid':$v.create.contact_person.$error || errors.contact_person,-->
<!--                                                                'is-valid':!$v.create.contact_person.$invalid && !errors.contact_person-->
<!--                                                            }"-->
<!--                                                />-->
<!--                                                <template v-if="errors.contact_person">-->
<!--                                                    <ErrorMessage v-for="(errorMessage,index) in errors.contact_person" :key="index">{{ errorMessage }}</ErrorMessage>-->
<!--                                                </template>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <div v-if="create.type == 'customer'" class="col-md-4">-->
<!--                                            <div class="form-group">-->
<!--                                                <label  class="control-label">-->
<!--                                                    {{ getCompanyKey('general_customer_contact_phones') }}-->
<!--                                                    <span class="text-danger">*</span>-->
<!--                                                </label>-->
<!--                                                <input-->
<!--                                                    type="text"-->
<!--                                                    class="form-control"-->
<!--                                                    data-create="9"-->
<!--                                                    v-model="$v.create.contact_phone.$model"-->
<!--                                                    :class="{-->
<!--                                                                'is-invalid':$v.create.contact_phone.$error || errors.contact_phone,-->
<!--                                                                'is-valid':!$v.create.contact_phone.$invalid && !errors.contact_phone-->
<!--                                                            }"-->
<!--                                                />-->
<!--                                                <template v-if="errors.contact_phone">-->
<!--                                                    <ErrorMessage v-for="(errorMessage,index) in errors.contact_phone" :key="index">{{ errorMessage }}</ErrorMessage>-->
<!--                                                </template>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <div v-if="create.type == 'equipment'" class="col-md-4">-->
<!--                                            <div class="form-group position-relative">-->
<!--                                                <label-->
<!--                                                    class="control-label">{{ getCompanyKey("boardRent_task_location") }}-->
<!--                                                    <span class="text-danger">*</span>-->
<!--                                                </label>-->
<!--                                                <multiselect-->
<!--                                                    @input="showLocationModal"-->
<!--                                                    v-model="$v.create.location_id.$model"-->
<!--                                                    :options="locations.map((type) => type.id)"-->
<!--                                                    :custom-label="(opt) => $i18n.locale == 'ar' ?locations.find((x) => x.id == opt).name:locations.find((x) => x.id == opt).name_e"-->
<!--                                                    :class="{'is-invalid': $v.create.location_id.$error || errors.location_id,'is-valid': !$v.create.location_id.$invalid && !errors.location_id,}"-->
<!--                                                >-->
<!--                                                </multiselect>-->

<!--                                                <template v-if="errors.location_id">-->
<!--                                                    <ErrorMessage-->
<!--                                                        v-for="(errorMessage, index) in errors.location_id"-->
<!--                                                        :key="index"-->
<!--                                                    >{{ errorMessage }}-->
<!--                                                    </ErrorMessage-->
<!--                                                    >-->
<!--                                                </template>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <div v-if="create.type == 'equipment'" class="col-md-4">-->
<!--                                            <div class="form-group position-relative">-->
<!--                                                <label-->
<!--                                                    class="control-label">{{ getCompanyKey("equipment") }}-->
<!--                                                    <span class="text-danger">*</span>-->
<!--                                                </label>-->
<!--                                                <multiselect-->
<!--                                                    @input="showEquipmentModal"-->
<!--                                                    v-model="equipment_id"-->
<!--                                                    :options="equipments.map((type) => type.id)"-->
<!--                                                    :custom-label="(opt) => $i18n.locale == 'ar' ?equipments.find((x) => x.id == opt).name:equipments.find((x) => x.id == opt).name_e"-->
<!--                                                >-->
<!--                                                </multiselect>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <div v-if="create.type == 'equipment'" class="col-md-4">-->
<!--                                            <div class="form-group position-relative">-->
<!--                                                <label-->
<!--                                                    class="control-label">{{ getCompanyKey("boardRent_task_equipment") }}-->
<!--                                                    <span class="text-danger">*</span>-->
<!--                                                </label>-->
<!--                                                <multiselect-->
<!--                                                    v-model="$v.create.equipment_id.$model"-->
<!--                                                    :options="equipment_childs.map((type) => type.id)"-->
<!--                                                    :custom-label="(opt) => $i18n.locale == 'ar' ?equipment_childs.find((x) => x.id == opt).name:equipment_childs.find((x) => x.id == opt).name_e"-->
<!--                                                    :class="{'is-invalid': $v.create.equipment_id.$error || errors.equipment_id,'is-valid': !$v.create.equipment_id.$invalid && !errors.equipment_id,}"-->
<!--                                                >-->
<!--                                                </multiselect>-->

<!--                                                <template v-if="errors.equipment_id">-->
<!--                                                    <ErrorMessage-->
<!--                                                        v-for="(errorMessage, index) in errors.equipment_id"-->
<!--                                                        :key="index"-->
<!--                                                    >{{ errorMessage }}-->
<!--                                                    </ErrorMessage-->
<!--                                                    >-->
<!--                                                </template>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <div v-if="create.type == 'general'" class="col-md-6">-->
<!--                                            <div class="form-group position-relative">-->
<!--                                                <label-->
<!--                                                    class="control-label">{{ getCompanyKey("boardRent_task_task_requirement") }}-->
<!--                                                    <span class="text-danger">*</span>-->
<!--                                                </label>-->
<!--                                                <input-->
<!--                                                    type="text"-->
<!--                                                    class="form-control"-->
<!--                                                    v-model="$v.create.task_requirement.$model"-->
<!--                                                    :class="{-->
<!--                                                                'is-invalid':$v.create.task_requirement.$error || errors.task_requirement,-->
<!--                                                                'is-valid':!$v.create.task_requirement.$invalid && !errors.task_requirement-->
<!--                                                            }"-->
<!--                                                />-->
<!--                                                <template v-if="errors.task_requirement">-->
<!--                                                    <ErrorMessage-->
<!--                                                        v-for="(errorMessage, index) in errors.task_requirement"-->
<!--                                                        :key="index"-->
<!--                                                    >{{ errorMessage }}-->
<!--                                                    </ErrorMessage-->
<!--                                                    >-->
<!--                                                </template>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <div class="col-md-12">-->
<!--                                            <hr style="margin: 10px 0 !important;  border-top: 1px solid rgb(141 163 159 / 42%)" />-->
<!--                                        </div>-->
<!--                                        <div class="col-md-4">-->
<!--                                            <div class="form-group">-->
<!--                                                <label  class="control-label">-->
<!--                                                    {{ getCompanyKey('task_title') }}-->
<!--                                                    <span class="text-danger">*</span>-->
<!--                                                </label>-->
<!--                                                <input-->
<!--                                                    type="text"-->
<!--                                                    class="form-control"-->
<!--                                                    data-create="9"-->
<!--                                                    v-model="$v.create.task_title.$model"-->
<!--                                                    :class="{-->
<!--                                                                'is-invalid':$v.create.task_title.$error || errors.task_title,-->
<!--                                                                'is-valid':!$v.create.task_title.$invalid && !errors.task_title-->
<!--                                                            }"-->
<!--                                                />-->
<!--                                                <template v-if="errors.task_title">-->
<!--                                                    <ErrorMessage v-for="(errorMessage,index) in errors.task_title" :key="index">{{ errorMessage }}</ErrorMessage>-->
<!--                                                </template>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <div class="col-md-4">-->
<!--                                            <div class="form-group position-relative">-->
<!--                                                <label class="control-label">-->
<!--                                                    {{ getCompanyKey("task_type") }}-->
<!--                                                    <span class="text-danger">*</span>-->
<!--                                                </label>-->
<!--                                                <multiselect-->
<!--                                                    @input="showDepartmentTaskModal" v-model="create.department_task_id"-->
<!--                                                    :options="departmentTasks.map((type) => type.id)"-->
<!--                                                    :custom-label="(opt) => $i18n.locale == 'ar' ? departmentTasks.find((x) => x.id == opt).name : departmentTasks.find((x) => x.id == opt).name_e"-->
<!--                                                    :class="{'is-invalid': $v.create.department_task_id.$error || errors.department_task_id,'is-valid': !$v.create.department_task_id.$invalid && !errors.department_task_id,}"-->
<!--                                                >-->
<!--                                                </multiselect>-->
<!--                                                <div v-if="$v.create.department_task_id.$error || errors.department_task_id" class="text-danger">-->
<!--                                                    {{ $t("general.fieldIsRequired") }}-->
<!--                                                </div>-->
<!--                                                <template v-if="errors.department_task_id">-->
<!--                                                    <ErrorMessage v-for="(errorMessage, index) in errors.department_task_id" :key="index">{{ errorMessage }}-->
<!--                                                    </ErrorMessage>-->
<!--                                                </template>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <div class="col-md-4">-->
<!--                                            <div class="form-group">-->
<!--                                                <label>{{ getCompanyKey('task_status') }}<span class="text-danger">*</span></label>-->
<!--                                                <multiselect-->
<!--                                                    @input="showStatusModal"-->
<!--                                                    v-model="create.status_id"-->
<!--                                                    :options="statuses.map((type) => type.id)"-->
<!--                                                    :custom-label="(opt) => $i18n.locale == 'ar'? statuses.find((x) => x.id == opt).name : statuses.find((x) => x.id == opt).name_e"-->
<!--                                                    :class="{'is-invalid': $v.create.status_id.$error || errors.status_id,'is-valid': !$v.create.status_id.$invalid && !errors.status_id,}"-->
<!--                                                >-->
<!--                                                </multiselect>-->
<!--                                                <div v-if="!$v.create.status_id.required" class="invalid-feedback">-->
<!--                                                    {{ $t("general.fieldIsRequired") }}-->
<!--                                                </div>-->

<!--                                                <template v-if="errors.status_id">-->
<!--                                                    <ErrorMessage v-for="(errorMessage, index) in errors.status_id"-->
<!--                                                                  :key="index">{{ errorMessage }}-->
<!--                                                    </ErrorMessage>-->
<!--                                                </template>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <div class="col-md-4">-->
<!--                                            <div class="form-group">-->
<!--                                                <label>{{ getCompanyKey('task_owners') }}<span class="text-danger">*</span></label>-->
<!--                                                <multiselect-->
<!--                                                    :disabled="!(create.type == 'general')"-->
<!--                                                    :multiple="true"-->
<!--                                                    @input="checkIncloudIdOwners"-->
<!--                                                    v-model="create.owners"-->
<!--                                                    :options="employees.map((type) => type.id)"-->
<!--                                                    :custom-label=" (opt) => $i18n.locale == 'ar' ? employees.find((x) => x.id == opt).name : employees.find((x) => x.id == opt).name_e "-->
<!--                                                    :class="{'is-invalid': $v.create.owners.$error || errors.owners,'is-valid': !$v.create.owners.$invalid && !errors.owners,}"-->
<!--                                                >-->
<!--                                                </multiselect>-->
<!--                                                <div v-if="!$v.create.owners.required" class="invalid-feedback">-->
<!--                                                    {{ $t("general.fieldIsRequired") }}-->
<!--                                                </div>-->

<!--                                                <template v-if="errors.owners">-->
<!--                                                    <ErrorMessage v-for="(errorMessage, index) in errors.owners"-->
<!--                                                                  :key="index">{{ errorMessage }}-->
<!--                                                    </ErrorMessage>-->
<!--                                                </template>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <div class="col-md-4">-->
<!--                                            <div class="form-group">-->
<!--                                                <label>{{ getCompanyKey('task_supervisors') }}<span class="text-danger">*</span></label>-->
<!--                                                <multiselect-->
<!--                                                    :multiple="true"-->
<!--                                                    @input="checkIncloudIdSupervisors"-->
<!--                                                    v-model="create.supervisors"-->
<!--                                                    :options="employees.map((type) => type.id)"-->
<!--                                                    :custom-label=" (opt) => $i18n.locale == 'ar' ? employees.find((x) => x.id == opt).name : employees.find((x) => x.id == opt).name_e "-->
<!--                                                    :class="{'is-invalid': $v.create.supervisors.$error || errors.supervisors,'is-valid': !$v.create.supervisors.$invalid && !errors.supervisors,}"-->
<!--                                                >-->
<!--                                                </multiselect>-->
<!--                                                <div v-if="!$v.create.supervisors.required" class="invalid-feedback">-->
<!--                                                    {{ $t("general.fieldIsRequired") }}-->
<!--                                                </div>-->

<!--                                                <template v-if="errors.supervisors">-->
<!--                                                    <ErrorMessage v-for="(errorMessage, index) in errors.supervisors"-->
<!--                                                                  :key="index">{{ errorMessage }}-->
<!--                                                    </ErrorMessage>-->
<!--                                                </template>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <div class="col-md-4">-->
<!--                                            <div class="form-group">-->
<!--                                                <label>{{ getCompanyKey('task_notifications') }}<span class="text-danger">*</span></label>-->
<!--                                                <multiselect-->
<!--                                                    :multiple="true"-->
<!--                                                    @input="checkIncloudIdNotifications"-->
<!--                                                    v-model="create.notifications"-->
<!--                                                    :options="employees.map((type) => type.id)"-->
<!--                                                    :custom-label=" (opt) => $i18n.locale == 'ar' ? employees.find((x) => x.id == opt).name : employees.find((x) => x.id == opt).name_e "-->
<!--                                                    :class="{'is-invalid': $v.create.notifications.$error || errors.notifications,'is-valid': !$v.create.notifications.$invalid && !errors.notifications,}"-->
<!--                                                >-->
<!--                                                </multiselect>-->
<!--                                                <div v-if="!$v.create.notifications.required" class="invalid-feedback">-->
<!--                                                    {{ $t("general.fieldIsRequired") }}-->
<!--                                                </div>-->

<!--                                                <template v-if="errors.notifications">-->
<!--                                                    <ErrorMessage v-for="(errorMessage, index) in errors.notifications"-->
<!--                                                                  :key="index">{{ errorMessage }}-->
<!--                                                    </ErrorMessage>-->
<!--                                                </template>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <div class="col-md-12">-->
<!--                                            <hr style="margin: 10px 0 !important;  border-top: 1px solid rgb(141 163 159 / 42%)" />-->
<!--                                        </div>-->
<!--                                        <div class="col-md-3">-->
<!--                                            <div class="form-group">-->
<!--                                                <label class="control-label">-->
<!--                                                    {{ getCompanyKey("execution_date") }}-->
<!--                                                    <span class="text-danger">*</span>-->
<!--                                                </label>-->
<!--                                                <date-picker-->
<!--                                                    @input="calcDurationCreate"-->
<!--                                                    type="date"-->
<!--                                                    v-model="$v.create.execution_date.$model"-->
<!--                                                    format="YYYY-MM-DD"-->
<!--                                                    valueType="format"-->
<!--                                                    :confirm="false"-->
<!--                                                    :class="{-->
<!--                                                                'is-invalid': $v.create.execution_date.$error || errors.execution_date,-->
<!--                                                                'is-valid': !$v.create.execution_date.$invalid && !errors.execution_date,-->
<!--                                                            }">-->
<!--                                                </date-picker>-->
<!--                                                <template v-if="errors.execution_date">-->
<!--                                                    <ErrorMessage v-for="(errorMessage, index) in errors.execution_date" :key="index">-->
<!--                                                        {{-->
<!--                                                            errorMessage-->
<!--                                                        }}-->
<!--                                                    </ErrorMessage>-->
<!--                                                </template>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <div class="col-md-2">-->
<!--                                            <div class="form-group">-->
<!--                                                <label class="control-label">-->
<!--                                                    {{ getCompanyKey("task_start_time") }}-->
<!--                                                </label>-->
<!--                                                <date-picker-->
<!--                                                    @input="calcDurationCreate"-->
<!--                                                    type="time"-->
<!--                                                    v-model="$v.create.start_time.$model"-->
<!--                                                    format="HH:mm:ss"-->
<!--                                                    valueType="format"-->
<!--                                                    :confirm="false"-->
<!--                                                    :class="{-->
<!--                                                                'is-invalid': $v.create.start_time.$error || errors.start_time,-->
<!--                                                                'is-valid': !$v.create.start_time.$invalid && !errors.start_time,-->
<!--                                                            }">-->
<!--                                                </date-picker>-->
<!--                                                <template v-if="errors.start_time">-->
<!--                                                    <ErrorMessage v-for="(errorMessage, index) in errors.start_time" :key="index">-->
<!--                                                        {{-->
<!--                                                            errorMessage-->
<!--                                                        }}-->
<!--                                                    </ErrorMessage>-->
<!--                                                </template>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <div class="col-md-3">-->
<!--                                            <div class="form-group">-->
<!--                                                <label class="control-label">-->
<!--                                                    {{ getCompanyKey("execution_end_date") }}-->
<!--                                                    <span class="text-danger">*</span>-->
<!--                                                </label>-->
<!--                                                <date-picker-->
<!--                                                    @input="calcDurationCreate"-->
<!--                                                    type="date"-->
<!--                                                    v-model="$v.create.execution_end_date.$model"-->
<!--                                                    format="YYYY-MM-DD"-->
<!--                                                    valueType="format"-->
<!--                                                    :confirm="false"-->
<!--                                                    :class="{-->
<!--                                                                'is-invalid': $v.create.execution_end_date.$error || errors.execution_end_date,-->
<!--                                                                'is-valid': !$v.create.execution_end_date.$invalid && !errors.execution_end_date,-->
<!--                                                            }">-->
<!--                                                </date-picker>-->
<!--                                                <template v-if="errors.execution_end_date">-->
<!--                                                    <ErrorMessage v-for="(errorMessage, index) in errors.execution_end_date" :key="index">-->
<!--                                                        {{-->
<!--                                                            errorMessage-->
<!--                                                        }}-->
<!--                                                    </ErrorMessage>-->
<!--                                                </template>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <div class="col-md-2">-->
<!--                                            <div class="form-group">-->
<!--                                                <label class="control-label">-->
<!--                                                    {{ getCompanyKey("task_end_time") }}-->
<!--                                                </label>-->
<!--                                                <date-picker-->
<!--                                                    @input="calcDurationCreate"-->
<!--                                                    type="time"-->
<!--                                                    v-model="$v.create.end_time.$model"-->
<!--                                                    format="HH:mm:ss"-->
<!--                                                    valueType="format"-->
<!--                                                    :confirm="false"-->
<!--                                                    :class="{-->
<!--                                                                'is-invalid': $v.create.end_time.$error || errors.end_time,-->
<!--                                                                'is-valid': !$v.create.end_time.$invalid && !errors.end_time,-->
<!--                                                            }">-->
<!--                                                </date-picker>-->
<!--                                                <template v-if="errors.end_time">-->
<!--                                                    <ErrorMessage v-for="(errorMessage, index) in errors.end_time" :key="index">-->
<!--                                                        {{-->
<!--                                                            errorMessage-->
<!--                                                        }}-->
<!--                                                    </ErrorMessage>-->
<!--                                                </template>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <div class="col-md-2">-->
<!--                                            <div class="form-group">-->
<!--                                                <label  class="control-label">-->
<!--                                                    {{ getCompanyKey('execution_duration') }}-->
<!--                                                    <span class="text-danger">*</span>-->
<!--                                                </label>-->
<!--                                                <input-->
<!--                                                    type="text"-->
<!--                                                    :disabled="true"-->
<!--                                                    class="form-control"-->
<!--                                                    data-create="9"-->
<!--                                                    v-model="$v.create.execution_duration.$model"-->
<!--                                                    :class="{-->
<!--                                                                'is-invalid':$v.create.execution_duration.$error || errors.execution_duration,-->
<!--                                                                'is-valid':!$v.create.execution_duration.$invalid && !errors.execution_duration-->
<!--                                                            }"-->
<!--                                                />-->
<!--                                                <template v-if="errors.execution_duration">-->
<!--                                                    <ErrorMessage v-for="(errorMessage,index) in errors.execution_duration" :key="index">{{ errorMessage }}</ErrorMessage>-->
<!--                                                </template>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <div class="col-md-3">-->
<!--                                            <div class="form-group">-->
<!--                                                <label class="control-label">-->
<!--                                                    {{ getCompanyKey("notification_date") }}-->
<!--                                                    <span class="text-danger">*</span>-->
<!--                                                </label>-->
<!--                                                <date-picker-->
<!--                                                    type="date"-->
<!--                                                    v-model="$v.create.notification_date.$model"-->
<!--                                                    format="YYYY-MM-DD"-->
<!--                                                    valueType="format"-->
<!--                                                    :confirm="false"-->
<!--                                                    :class="{-->
<!--                                                                'is-invalid': $v.create.notification_date.$error || errors.notification_date,-->
<!--                                                                'is-valid': !$v.create.notification_date.$invalid && !errors.notification_date,-->
<!--                                                            }">-->
<!--                                                </date-picker>-->
<!--                                                <template v-if="errors.notification_date">-->
<!--                                                    <ErrorMessage v-for="(errorMessage, index) in errors.notification_date" :key="index">-->
<!--                                                        {{-->
<!--                                                            errorMessage-->
<!--                                                        }}-->
<!--                                                    </ErrorMessage>-->
<!--                                                </template>-->
<!--                                            </div>-->
<!--                                            <div class="form-group">-->
<!--                                                <label class="control-label">-->
<!--                                                    {{ getCompanyKey("task_priority") }}-->
<!--                                                    <span class="text-danger">*</span>-->
<!--                                                </label>-->
<!--                                                <multiselect-->
<!--                                                    v-model="$v.create.priority_id.$model"-->
<!--                                                    :options="priorities.map((type) => type.id)"-->
<!--                                                    :custom-label="(opt) => $i18n.locale == 'ar' ? priorities.find((x) => x.id == opt).name : priorities.find((x) => x.id == opt).name_e"-->
<!--                                                    :class="{'is-invalid': $v.create.priority_id.$error || errors.priority_id,'is-valid': !$v.create.priority_id.$invalid && !errors.priority_id,}"-->
<!--                                                >-->
<!--                                                </multiselect>-->
<!--                                                <template v-if="errors.priority_id">-->
<!--                                                    <ErrorMessage v-for="(errorMessage, index) in errors.priority_id" :key="index">-->
<!--                                                        {{-->
<!--                                                            errorMessage-->
<!--                                                        }}-->
<!--                                                    </ErrorMessage>-->
<!--                                                </template>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <div class="col-md-2" >-->
<!--                                            <div class="form-group">-->
<!--                                                <label class="my-1 mr-2">-->
<!--                                                    {{ getCompanyKey("boardRent_task_is_closed") }}-->
<!--                                                    <span class="text-danger">*</span>-->
<!--                                                </label>-->
<!--                                                <b-form-group>-->
<!--                                                    <b-form-radio-->
<!--                                                        class="d-inline-block"-->
<!--                                                        v-model="$v.create.is_closed.$model"-->
<!--                                                        name="some-radios-create"-->
<!--                                                        value="1"-->
<!--                                                    >{{ $t("general.Yes") }}</b-form-radio-->
<!--                                                    >-->
<!--                                                    <b-form-radio-->
<!--                                                        class="d-inline-block m-1"-->
<!--                                                        v-model="$v.create.is_closed.$model"-->
<!--                                                        name="some-radios-create"-->
<!--                                                        value="0"-->
<!--                                                    >{{ $t("general.No") }}</b-form-radio-->
<!--                                                    >-->
<!--                                                </b-form-group>-->
<!--                                                <template v-if="errors.is_closed">-->
<!--                                                    <ErrorMessage-->
<!--                                                        v-for="(errorMessage, index) in errors.is_closed"-->
<!--                                                        :key="index"-->
<!--                                                    >{{ errorMessage }}-->
<!--                                                    </ErrorMessage>-->
<!--                                                </template>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <div class="col-md-3">-->
<!--                                            <div class="form-group">-->
<!--                                                <label class="control-label">-->
<!--                                                    {{ getCompanyKey("boardRent_panel_note") }}-->
<!--                                                    <span class="text-danger">*</span>-->
<!--                                                </label>-->
<!--                                                <textarea v-model="$v.create.note.$model" class="form-control" :maxlength="1000" rows="4"></textarea>-->
<!--                                                <template v-if="errors.note">-->
<!--                                                    <ErrorMessage-->
<!--                                                        v-for="(errorMessage, index) in errors.note"-->
<!--                                                        :key="index"-->
<!--                                                    >{{ errorMessage }}</ErrorMessage-->
<!--                                                    >-->
<!--                                                </template>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <div class="col-md-3">-->
<!--                                            <div class="form-group">-->
<!--                                                <label class="control-label">-->
<!--                                                    {{ getCompanyKey("boardRent_panel_admin_note") }}-->
<!--                                                    <span class="text-danger">*</span>-->
<!--                                                </label>-->
<!--                                                <textarea v-model="$v.create.admin_note.$model" class="form-control" :maxlength="1000" rows="4"></textarea>-->
<!--                                                <template v-if="errors.admin_note">-->
<!--                                                    <ErrorMessage-->
<!--                                                        v-for="(errorMessage, index) in errors.admin_note"-->
<!--                                                        :key="index"-->
<!--                                                    >{{ errorMessage }}</ErrorMessage-->
<!--                                                    >-->
<!--                                                </template>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                </b-tab>-->
<!--                                <b-tab :disabled="!task_id" :title="$t('general.attachment')">-->
<!--                                    <div class="row">-->
<!--                                        <b-modal-->
<!--                                            id="uploadPhotoTitleCreate"-->
<!--                                            :title="$t('general.ImageUploads')"-->
<!--                                            title-class="font-18"-->
<!--                                            body-class="p-4 "-->
<!--                                            :hide-footer="true"-->
<!--                                            @show="uploadPhotoTitle"-->
<!--                                            @hidden="uploadPhotoTitleHidden"-->
<!--                                        >-->
<!--                                            <div class="form-group">-->
<!--                                                <label class="control-label">-->
<!--                                                    {{ $t("general.titleFile") }}-->
<!--                                                    <span class="text-danger">*</span>-->
<!--                                                </label>-->
<!--                                                <div dir="rtl">-->
<!--                                                    <input-->
<!--                                                        type="text"-->
<!--                                                        class="form-control"-->
<!--                                                        data-create="1"-->
<!--                                                        v-model="$v.titleFile.$model"-->
<!--                                                        :class="{-->
<!--                                                                        'is-invalid':$v.titleFile.$error || errors.title,-->
<!--                                                                        'is-valid':!$v.titleFile.$invalid && !errors.title-->
<!--                                                                    }"-->
<!--                                                    />-->
<!--                                                </div>-->
<!--                                                <div v-if="!$v.titleFile.minLength" class="invalid-feedback">{{ $t('general.Itmustbeatleast') }} {{ $v.titleFile.$params.minLength.min }} {{ $t('general.letters') }}</div>-->
<!--                                                <div v-if="!$v.titleFile.maxLength" class="invalid-feedback">{{ $t('general.Itmustbeatmost') }}  {{ $v.titleFile.$params.maxLength.max }} {{ $t('general.letters') }}</div>-->
<!--                                                <template v-if="errors.title">-->
<!--                                                    <ErrorMessage v-for="(errorMessage,index) in errors.title" :key="index">{{ errorMessage }}</ErrorMessage>-->
<!--                                                </template>-->
<!--                                            </div>-->

<!--                                            <input accept="image/png, image/gif, image/jpeg, image/jpg" type="file" id="uploadImageCreate"-->
<!--                                                   @change.prevent="onImageChanged" class="input-file-upload position-absolute" :class="[-->
<!--                                                        'd-none',-->
<!--                                                        {-->
<!--                                                          'is-invalid': $v.create.media.$error || errors.media,-->
<!--                                                          'is-valid': !$v.create.media.$invalid && !errors.media,-->
<!--                                                        },-->
<!--                                                      ]" />-->

<!--                                            <b-button :disabled="!titleFile && $v.titleFile.$error" @click="changePhoto" variant="success" type="button"-->
<!--                                                      class="mx-1 font-weight-bold px-3" v-if="!isLoader">-->
<!--                                                {{ $t("general.Add") }}-->
<!--                                            </b-button>-->
<!--                                            <b-button variant="success" class="mx-1" disabled v-else>-->
<!--                                                <b-spinner small></b-spinner>-->
<!--                                                <span class="sr-only">{{ $t("login.Loading") }}...</span>-->
<!--                                            </b-button>-->

<!--                                        </b-modal>-->
<!--                                        <div class="col-md-8 my-1">-->
<!--                                            &lt;!&ndash; file upload &ndash;&gt;-->
<!--                                            <div class="row align-content-between" style="width: 100%; height: 100%">-->
<!--                                                <div class="col-12">-->
<!--                                                    <div class="d-flex flex-wrap">-->
<!--                                                        <div :class="[-->
<!--                                                          'dropzone-previews col-4 position-relative mb-2',-->
<!--                                                        ]" v-for="(photo, index) in images" :key="photo.id">-->
<!--                                                            <div :class="[-->
<!--                                                            'card mb-0 shadow-none border',-->
<!--                                                            images.length - 1 == index ? 'bg-primary' : '',-->
<!--                                                          ]">-->
<!--                                                                <div class="p-2">-->
<!--                                                                    <div class="row align-items-center">-->
<!--                                                                        <div class="col-auto" @click="showPhoto = photo.webp">-->
<!--                                                                            <img data-dz-thumbnail :src="photo.webp" class="avatar-sm rounded bg-light"-->
<!--                                                                                 @error="src = '../../../../../images/img-1.png'" />-->
<!--                                                                        </div>-->
<!--                                                                        <div class="col pl-0">-->
<!--                                                                            <a href="javascript:void(0);" :class="[-->
<!--                                                                    'font-weight-bold',-->
<!--                                                                    images.length - 1 == index-->
<!--                                                                      ? 'text-white'-->
<!--                                                                      : 'text-muted',-->
<!--                                                                  ]" data-dz-name>-->
<!--                                                                                {{ photo.title }}-->
<!--                                                                            </a>-->
<!--                                                                        </div>-->
<!--                                                                        &lt;!&ndash; Button &ndash;&gt;-->
<!--                                                                        <a href="javascript:void(0);" :class="[-->
<!--                                                                  'btn-danger dropzone-close',-->
<!--                                                                  $i18n.locale == 'ar'-->
<!--                                                                    ? 'dropzone-close-rtl'-->
<!--                                                                    : '',-->
<!--                                                                ]" data-dz-remove @click.prevent="-->
<!--                                                                    deleteImageCreate(photo.id, index)-->
<!--                                                                    ">-->
<!--                                                                            <i class="fe-x"></i>-->
<!--                                                                        </a>-->
<!--                                                                    </div>-->
<!--                                                                </div>-->
<!--                                                            </div>-->
<!--                                                        </div>-->
<!--                                                    </div>-->
<!--                                                </div>-->
<!--                                                <div class="footer-image col-12">-->
<!--                                                    <b-button @click="uploadPhotoTitle" variant="success" type="button"-->
<!--                                                              class="mx-1 font-weight-bold px-3" v-if="!isLoader">-->
<!--                                                        {{ $t("general.Add") }}-->
<!--                                                    </b-button>-->
<!--                                                </div>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <div class="col-md-4">-->
<!--                                            <div class="show-dropzone">-->
<!--                                                <img :src="showPhoto" class="img-thumbnail" @error="src = '../../../../../images/img-1.png'" />-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                </b-tab>-->
<!--                            </b-tabs>-->
<!--                    </form>-->
<!--                </b-modal>-->


            </div>
        </div>
    </Layout>
</template>

<style>
.calender-card-body{
    padding: 0 20px;
}
.calender-card-body .search{
    top: 10px;
    left: 150px;
    z-index: 99;
}

.calender-card-body .search-ar{
    top: 10px;
    left: unset;
    right: 77.6%;
    z-index: 99;
}

.tabs .tab-content {
    padding: 10px 60px 30px;
    min-height: 300px;
    background-color: #f5f5f5;
    position: relative;
}

.nav-bordered {
    border: unset !important;
}

.nav {
    background-color: #dff0fe;
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

.button-left {
    right: 20px;
    left: unset;
}
.button-right {
    right: unset;
    left: 20px;
}

.calender-card-body .result-search{
    top: 40px;
    display: block;
    width: 100%;
    text-align: center;
    z-index: 99;
}

.calender-card-body .result-search-ar{
    top: 40px;
    display: block;
    width: 100%;
    text-align: center;
    z-index: 99;
}
.floor-choose li, .room-choose li, .status-choose li{
    display: inline-block;
    background-color: #75c6e7;
    padding: 7px 20px;
    margin: 0 3px;
    font-size: 17px;
    color: #fffafa;
    font-weight: 500;
    border-radius: 5px;
    cursor: pointer;
    transition: .5s;
}
.floor-choose li:hover {
    background-color: #3bafda;
}
.floor-choose li.active{
    background-color: #3bafda;
}

.room-choose li {
    width: 230px;
}

.room-choose li.active{
    outline: 3px solid #000;
}

.dropdown.dropdown-room button{
    padding: 0 !important;
    background-color: transparent !important;
    border: none;
    font-weight: 500;
}

.dropdown.dropdown-room ul{
    padding: 0 !important;
}

.status-choose li.active{
    outline: 3px solid #000;
}
</style>
