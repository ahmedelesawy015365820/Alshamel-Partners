<template>
    <!--  create   -->
    <b-modal
        id="reservation-create"
        :title="getCompanyKey('reservation_create_form')"
        title-class="font-18"
        body-class="p-4 "
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
                    @click.prevent="resetModalHidden"
                >
                    {{ $t("general.Cancel") }}
                </b-button>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label">
                            {{ $t("general.Date") }}
                            <span class="text-danger">*</span>
                        </label>
                        <input
                            type="date"
                            class="form-control"
                            data-create="9"
                            v-model="$v.create.date.$model"
                            :class="{
                              'is-invalid': $v.create.date.$error || errors.date,
                              'is-valid': !$v.create.date.$invalid && !errors.date,
                            }"
                        />
                        <template v-if="errors.date">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.date"
                                :key="index"
                            >{{ errorMessage }}
                            </ErrorMessage
                            >
                        </template>
                    </div>
                </div>
                <div class="col-md-12 position-relative">
                    <div class="form-group">
                        <label class="my-1 mr-2">{{ getCompanyKey("customer") }}</label>
                        <multiselect
                            v-model="create.customer_id"
                            :options="customers.map((type) => type.id)"
                            :custom-label="
                                                  (opt) =>
                                                    $i18n.locale == 'ar'
                                                      ? customers.find((x) => x.id == opt).name
                                                      : customers.find((x) => x.id == opt).name_e
                                                "
                            :class="{
                                                  'is-invalid':
                                                    $v.create.customer_id.$error || errors.customer_id,
                                                }"
                        >
                        </multiselect>
                        <div
                            v-if="!$v.create.customer_id.required"
                            class="invalid-feedback"
                        >
                            {{ $t("general.fieldIsRequired") }}
                        </div>

                        <template v-if="errors.customer_id">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.customer_id"
                                :key="index"
                            >{{ errorMessage }}
                            </ErrorMessage
                            >
                        </template>
                    </div>
                </div>
                <div class="col-md-12 position-relative">
                    <div class="form-group">
                        <label class="my-1 mr-2">{{ getCompanyKey("sale_man") }}</label>
                        <multiselect
                            v-model="create.salesman_id"
                            :options="salesmen.map((type) => type.id)"
                            :custom-label="
                                                  (opt) =>
                                                    $i18n.locale == 'ar'
                                                      ? salesmen.find((x) => x.id == opt).name
                                                      : salesmen.find((x) => x.id == opt).name_e
                                                "
                            :class="{
                                                  'is-invalid':
                                                    $v.create.salesman_id.$error || errors.salesman_id,
                                                }"
                        >
                        </multiselect>
                        <div
                            v-if="!$v.create.salesman_id.required"
                            class="invalid-feedback"
                        >
                            {{ $t("general.fieldIsRequired") }}
                        </div>
                        <template v-if="errors.salesman_id">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.button_id"
                                :key="index"
                            >{{ errorMessage }}
                            </ErrorMessage
                            >
                        </template>
                    </div>
                </div>
                <div class="col-md-12 position-relative">
                    <div class="form-group">
                        <label class="my-1 mr-2">
                            {{getCompanyKey("payment_plan") }}
                        </label>
                        <input
                            v-model="$v.create.payment_plan_id.$model"
                            class="form-control"
                            type="number"
                            :class="{
                                                  'is-invalid':
                                                    $v.create.payment_plan_id.$error || errors.payment_plan_id,
                                                }"
                        />
                        <div
                            v-if="!$v.create.payment_plan_id.required"
                            class="invalid-feedback"
                        >
                            {{ $t("general.fieldIsRequired") }}
                        </div>
                        <template v-if="errors.payment_plan_id">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.payment_plan_id"
                                :key="index"
                            >{{ errorMessage }}
                            </ErrorMessage
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
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import adminApi from "../../../api/adminAxios";
import {required} from "vuelidate/lib/validators";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../general/loader";
import {dynamicSortString} from "../../../helper/tableSort";
import Multiselect from "vue-multiselect";

export default {
    name: "reservation",
    props: {
        companyKeys: {
            default: [],
        },
        defaultsKeys: {
            default: [],
        },
    },
    components: {
        ErrorMessage,
        loader,
        Multiselect,
    },
    validations: {
        create: {
            date: {required},
            customer_id: {required},
            payment_plan_id: {required},
            salesman_id: {required},
        },
    },
    mixins: [transMixinComp],
    data() {
        return {
            customers: [],
            salesmen: [],
            paymentPlans: [],
            isLoader: false,
            create: {
                customer_id: null,
                payment_plan_id: null,
                salesman_id: null,
                date: "",
            },
            errors: {},
            is_disabled: false,
        };
    },
    methods: {
        async getCustomers() {
            this.isLoader = true;
            await adminApi
                .get(`/general-customer`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    // l.unshift({id: 0, name: "اضافة زبون", name_e: "Add customer"});
                    this.customers = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        async getSalesmen() {
            this.isLoader = true;
            await adminApi
                .get(`/salesmen`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    // l.unshift({id: 0, name: "اضافة رجل مبيعات", name_e: "Add sale man"});
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
        async getPaymentPlans() {
            this.isLoader = true;
            await adminApi
                .get(`/real-estate/paymentPlans`)
                .then((res) => {
                    this.isLoader = false;
                    this.paymentPlans = res.data.data;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        resetModalHidden() {
            this.create = {
                customer_id: null,
                payment_plan_id: null,
                salesman_id: null,
                date: "",
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
            this.is_disabled = false;
            this.$bvModal.hide(`reservation-create`);
        },
        /**
         *  hidden Modal (create)
         */
        async resetModal() {
            await this.getCustomers();
            // await this.getPaymentPlans();
            await this.getSalesmen();
            this.create = {
                customer_id: null,
                payment_plan_id: null,
                salesman_id: null,
                date: "",
            };
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
            this.create = {
                customer_id: null,
                payment_plan_id: null,
                salesman_id: null,
                date: "",
            };
            this.is_disabled = false;
            this.$nextTick(() => {
                this.$v.$reset();
            });
        },
        AddSubmit() {
            if (this.$v.create.$invalid) {
                this.$v.create.$touch();
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                this.is_disabled = false;
                adminApi
                    .post(`real-estate/reservations`, {...this.create,company_id: this.$store.getters["auth/company_id"],})
                    .then((res) => {
                        this.$emit('created');
                        this.is_disabled = true;

                        setTimeout(() => {
                            Swal.fire({
                                icon: "success",
                                text: `${this.$t("general.Addedsuccessfully")}`,
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
    }
}
</script>

<style scoped>

</style>
