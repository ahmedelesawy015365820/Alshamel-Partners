<script>

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
import loader from "../../../general/loader";
import {formatDateOnly} from "../../../../helper/startDate";
import DatePicker from "vue2-datepicker";

/**
 * Calendar component
 */
export default {
    name: "Calender",
    props: {
        document_detail_type: {
            default: '',
        },
    },
    components: {
        FullCalendar,
        Multiselect,
        ErrorMessage,
        loader,
        DatePicker
    },
    mixins: [translation],
    beforeMount() {
        let l = new Date();
        this.search.months_number = l.getMonth() + 1;
        this.search.year = l.getFullYear();
        this.getData();
    },
    mounted() {
        this.$store.dispatch('locationIp/getIp');
    },
    data() {
        return {
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
                initialView: "dayGridMonth",
                themeSystem: "bootstrap",
                initialEvents: [],
                events: [],
                editable: true,
                droppable: true,
                eventResizableFromStart: true,
                eventsSet: this.handleEvents,
                weekends: true,
                selectable: true,
                selectMirror: true,
                dayMaxEvents: true,
                customButtons: {
                    prev: { // this overrides the prev button
                        text: "<",
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
                        text: ">",
                        click: () => {
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
            search: {
                category_id: null,
                governorate_id: null,
                months_number: 0,
                year: 0
            },
            categories: [],
            governorates: [],
            is_disabled: false,
            isLoader: false,
            errors: {},
        };
    },
    methods: {
        getData(){
            this.isLoader = true;

            adminApi.get(`/document-header-details/panel-usage-rate-report?governorate_id=${this.search.governorate_id??''}&category_id=${this.search.category_id??''}&document_detail_type=${this.document_detail_type}&year=${this.search.year??''}&month=${this.search.months_number??''}`)
                .then((res) => {
                    let l = res.data.data;
                    // l.forEach((el,index) => {
                    //     el.start = new Date().setDate(new Date(el.start).getDate());
                    //     el.end = new Date().setDate(new Date(el.start).getDate());
                    // });
                    this.calendarOptions.initialEvents = l;
                    this.calendarOptions.events = l;
                    this.$bvModal.hide(`search`);
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
         * Show list of events
         */
        handleEvents(events) {
            this.currentEvents = events;
        },
        formatDate(value) {
            return formatDateOnly(value);
        },
        async resetModalSearch() {
            await this.getGovernorate();
            await this.getCategory();
            this.is_disabled = false;
            this.$nextTick(() => {
                this.$v.$reset()
            });
            this.errors = {};
        },
        resetModalHiddenSearch() {
            this.$nextTick(() => {
                this.$v.$reset()
            });
            this.errors = {};
            this.$bvModal.hide(`search`);
            this.search = {
                category_id: null,
                governorate_id: null,
                months_number: this.search.months_number,
                year: this.search.year
            };
        },
        async getGovernorate() {
            this.isLoader = true;
            await adminApi
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
        async getCategory() {
            this.isLoader = true;
            await adminApi
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
    },
}
</script>

<template>
    <div>
        <div class="calender-card-body position-relative">
            <!--       start loader       -->
            <loader size="large" v-if="isLoader" />
            <!--       end loader       -->
            <div class="row position-relative">
                <div :class="['col-12 position-absolute',$i18n.locale == 'en' ?'search':'search-ar']">
                    <b-button
                        v-b-modal.search
                        variant="primary"
                        class="btn-sm mx-1 font-weight-bold"
                    >
                        {{ $t('general.Search') }}
                        <i class="fe-search"></i>
                    </b-button>
                    <b-modal
                        id="search"
                        :title="$t('general.Search')"
                        title-class="font-18"
                        body-class="p-4"
                        size="lg"
                        :hide-footer="true"
                        @show="resetModalSearch"
                        @hidden="resetModalHiddenSearch"
                    >
                        <form>
                            <div class="d-flex justify-content-end">
                                <template v-if="!is_disabled">
                                    <b-button
                                        variant="success"
                                        type="button" class="mx-1"
                                        v-if="!isLoader"
                                        @click.prevent="getData"
                                    >
                                        {{ $t('general.Search') }}
                                    </b-button>

                                    <b-button variant="success" class="mx-1" disabled v-else>
                                        <b-spinner small></b-spinner>
                                        <span class="sr-only">{{ $t('login.Loading') }}...</span>
                                    </b-button>
                                </template>
                                <!-- Emulate built in modal footer ok and cancel button actions -->

                                <b-button variant="danger" type="button" @click.prevent="resetModalHiddenSearch">
                                    {{ $t('general.Cancel') }}
                                </b-button>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ $t('general.category') }}<span class="text-danger">*</span></label>
                                        <multiselect
                                            v-model="search.category_id"
                                            :options="categories.map((type) => type.id)"
                                            :custom-label="(opt) => $i18n.locale == 'ar'? categories.find((x) => x.id == opt).name : categories.find((x) => x.id == opt).name_e"
                                        >
                                        </multiselect>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ $t('general.governorate') }}<span class="text-danger">*</span></label>
                                        <multiselect
                                            v-model="search.governorate_id"
                                            :options="governorates.map((type) => type.id)"
                                            :custom-label="(opt) => $i18n.locale == 'ar'? governorates.find((x) => x.id == opt).name : governorates.find((x) => x.id == opt).name_e"
                                        >
                                        </multiselect>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </b-modal>
                </div>

                <div class="col-12">
                    <div class="app-calendar">
                        <FullCalendar
                            ref="fullCalendar"
                            :options="calendarOptions"
                        ></FullCalendar>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
.calender-card-body{
    padding: 0 5px;
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
</style>
