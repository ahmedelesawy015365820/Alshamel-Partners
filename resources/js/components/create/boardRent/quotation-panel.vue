<template>
    <b-modal
        :id="id"
        :title="getCompanyKey('boardRent_panel_create_form')"
        title-class="font-18"
        body-class="p-4 "
        dialog-class="modal-full-width"
        @hidden="resetModalHidden"
        @show="resetModal"
        :hide-footer="true"
    >
        <template v-for="(item, index) in panels">
            <div class="row" :key="index">
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label">
                            {{ getCompanyKey("boardRent_panel_governorate") }}
                            <span class="text-danger">*</span>
                        </label>
                        <multiselect
                            @input="showGovernorateModal"
                            v-model="location[index].governorate_id"
                            :options="governorates.map((type) => type.id)"
                            :custom-label="(opt) => $i18n.locale == 'ar' ? governorates.find((x) => x.id == opt).name : governorates.find((x) => x.id == opt).name_e"
                        >
                        </multiselect>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label">
                            {{ getCompanyKey("boardRent_panel_city") }}
                            <span class="text-danger">*</span>
                        </label>
                        <multiselect
                            @input="showCityModal(index)"
                            v-model="location[index].city_id"
                            :options="cities[index].cities.map((type) => type.id)"
                            :custom-label="(opt) => $i18n.locale == 'ar' ? cities[index].cities.find((x) => x.id == opt).name : cities[index].cities.find((x) => x.id == opt).name_e"
                        >
                        </multiselect>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label">
                            {{ getCompanyKey("boardRent_panel_avenue") }}
                            <span class="text-danger">*</span>
                        </label>
                        <multiselect
                            @input="showAvenueModal(index)"
                            v-model="location[index].avenue_id"
                            :options="avenues[index].avenues.map((type) => type.id)"
                            :custom-label="(opt) => $i18n.locale == 'ar' ? avenues[index].avenues.find((x) => x.id == opt).name : avenues[index].avenues.find((x) => x.id == opt).name_e"
                        >
                        </multiselect>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label">
                            {{ getCompanyKey("boardRent_panel_street") }}
                            <span class="text-danger">*</span>
                        </label>
                        <multiselect
                            @input="showStreetModal(index)"
                            v-model="location[index].street_id"
                            :options="streets[index].streets.map((type) => type.id)"
                            :custom-label="(opt) => $i18n.locale == 'ar' ? streets[index].streets.find((x) => x.id == opt).name : streets[index].streets.find((x) => x.id == opt).name_e"
                        >
                        </multiselect>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label">
                            {{ getCompanyKey("boardRent_panel_category") }}
                            <span class="text-danger">*</span>
                        </label>
                        <multiselect
                            @input="showCategoryModal"
                            v-model="location[index].category_id"
                            :options="categories.map((type) => type.id)"
                            :custom-label="(opt) => $i18n.locale == 'ar' ? categories.find((x) => x.id == opt).name : categories.find((x) => x.id == opt).name_e"
                        >
                        </multiselect>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label">
                            {{ getCompanyKey("boardRent_panel_face") }}
                            <span class="text-danger">*</span>
                        </label>
                        <multiselect
                            v-model="location[index].face"
                            :options="faces"
                        >
                        </multiselect>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ $t('general.panel') }}</label>
                        <multiselect
                            v-model="$v.panels.$each[index].panel_id.$model"
                            :options="pans[index].pans.map((type) => type.id)" :custom-label="
                                                                                (opt) =>
                                                                                  $i18n.locale == 'ar'
                                                                                    ? pans[index].pans.find((x) => x.id == opt).name
                                                                                    : pans[index].pans.find((x) => x.id == opt).name_e
                                                                              ">
                        </multiselect>
                        <div v-if="$v.panels.$each[index].panel_id.$error" class="text-danger">
                            {{ $t("general.fieldIsRequired") }}
                        </div>
                    </div>
                </div>
                <div class="col-md-2 p-0 pt-3">
                    <button v-if="(panels.length - 1) == index" type="button"
                            @click.prevent="addNewField" class="custom-btn-dowonload">
                        <i class="fas fa-plus"></i>
                    </button>
                    <button v-if="panels.length > 1" type="button"
                            @click.prevent="removeNewField(index)" class="custom-btn-dowonload">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>
        </template>
    </b-modal>
</template>

<script>
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import adminApi from "../../../api/adminAxios";
import Swal from "sweetalert2";
import { required } from "vuelidate/lib/validators";
import Layout from "../../../views/layouts/main";
import PageHeader from "../../general/Page-header";
import Switches from "vue-switches";
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import Multiselect from "vue-multiselect";
import Governate from "../general/governate";
import Avnue from "../general/avenue";
import Street from "../general/street";
import City from "../general/city";
import Category from "../general/category";

export default {
    name: "quotation-panel",
    mixins: [transMixinComp],
    props: ["companyKeys", "defaultsKeys","id"],
    components: {
        Layout,
        PageHeader,
        Switches,
        ErrorMessage,
        loader,
        Multiselect,
        Governate,
        Avnue,
        Street,
        City,
        Category
    },
    data() {
        return {
            panel: null,
            panels: [],
            faces: ['A','B','Multi','One-Face'],
            governorates: [],
            cities: [],
            avenues: [],
            streets: [],
            categories: [],
            pans: [],
            location: [],
        }
    },
    validations: {
        panels: {
            required,
            $each: {
                panel_id: {required}
                }
            }
    },
    methods: {
        addNewField() {
            this.cities.push({cities: []});
            this.avenues.push({avenues: []});
            this.streets.push({streets: []});
            this.pans.push({pans: []});
            this.location.push({category_id: null,governorate_id: null,city_id: null, avenue_id: null, street_id: null,face: ''});
            this.panels.push({
                panel_id: null,
            });
        },
        removeNewField(index) {
            if (this.panels.length > 1) {
                this.panels.splice(index, 1);
                this.cities.splice(index, 1);
                this.avenues.splice(index, 1);
                this.streets.splice(index, 1);
                this.location.splice(index, 1);
                this.pans.splice(index, 1);
            }
        },
        resetModalHidden() {
            this.cities = [];this.avenues = [];this.streets = [];
            this.location =[]
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
            this.is_disabled = false;
            this.$bvModal.hide(`${this.id}`);
        },
        async resetModal() {
            await  this.getGovernorate();
            this.cities = [{cities: []}];
            this.avenues = [{avenues: []}];
            this.streets = [{streets: []}];
            this.pans = [{pans: []}];
            this.location = [{category_id: null,governorate_id: null,city_id: null, avenue_id: null, street_id: null,face: ''}];
            this.panels =  [{panel_id: null}];
            await  this.getCategory();
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
        },
        async getGovernorate() {

            this.governorates = [];this.cities = [];this.avenues = [];this.streets = [];

            await adminApi
                .get(`/governorates?columns[0]=country.id&search=1`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف المحافظه", name_e: "Add Governorate" });
                    this.governorates = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        async getCity(index) {
            this.isLoader = true;
            let governorate = this.location[index].governorate_id;
            this.location[index].city_id = null;
            this.location[index].avenue_id = null;
            this.location[index].street_id = null;
            this.cities[index].cities = [];this.avenues[index].avenues = [];this.streets[index].streets = [];

            await adminApi
                .get(`/cities?columns[0]=governorate.id&search=${governorate}`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف المدينه", name_e: "Add City" });
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
        async getAvnue(index) {
            this.isLoader = true;
            let city = this.location[index].city_id;
            this.location[index].avenue_id = null;
            this.location[index].street_id = null;
            this.avenues[index].avenues = [];this.streets[index].streets = [];

            await adminApi
                .get(`/avenues?columns[0]=city.id&search=${city}`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف المنطقه", name_e: "Add Avenue" });
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
        async getStreet(index) {
            this.isLoader = true;
            let avenue = this.location[index].avenue_id;
            this.location[index].street_id = null;
            this.streets[index].streets = [];

            await adminApi
                .get(`/streets?columns[0]=avenue.id&search=${avenue}`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف الشارع", name_e: "Add Street" });
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
        async getPanel(index) {
            this.isLoader = true;
            let category_id = this.location[index].category_id;
            let governorate_id = this.location[index].governorate_id;
            let city_id = this.location[index].city_id;
            let avenue_id = this.location[index].avenue_id;
            let face = this.location[index].face;
            let street_id = this.location[index].street_id;

            await adminApi
                .get(
                    `/boards-rent/panels?packages=1&category_id=${category_id}&governorate_id=${governorate_id}&
                    city_id=${city_id}&avenue_id=${avenue_id}&street_id=${street_id}&face=${face}`
                )
                .then((res) => {
                    let l = res.data.data;
                    this.pens[index].pens = l;
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
                .get(
                    `/categories`
                )
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف الفئه", name_e: "Add Category" });
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
        async showGovernorateModal(index) {
            if (this.location[index].governorate_id == 0) {
                this.$bvModal.show("governorate-create");
                this.location[index].governorate_id = null;
            }else {
                this.panals[index].panel_id = null;
                await this.getCity();
                await this.getPanel(index);
            }
        },
        async showCityModal(index) {
            if (this.location[index].city_id == 0) {
                this.$bvModal.show("city-create");
                this.location[index].city_id = null;
            }else {
                this.panals[index].panel_id = null;
                await this.getAvnue();
                await this.getPanel(index);
            }
        },
        async showAvenueModal(index) {
            if (this.location[index].avenue_id == 0) {
                this.$bvModal.show("avenue-create");
                this.location[index].avenue_id = null;
            }else {
                this.panals[index].panel_id = null;
                await this.getStreet();
                await this.getPanel(index);
            }
        },
        async showStreetModal(index) {
            if (this.location[index].street_id == 0) {
                this.$bvModal.show("street-create");
                this.location[index].street_id = null;
            }else {
                this.panals[index].panel_id = null;
                await this.getPanel(index);
            }
        },
        async showCategoryModal(){
            if (this.location[index].category_id == 0) {
                this.$bvModal.show("category-create");
                this.location[index].category_id = null;
            }else {
                this.panals[index].panel_id = null;
                await this.getPanel(index);
            }
        },
    }
}
</script>

<style scoped>

</style>
