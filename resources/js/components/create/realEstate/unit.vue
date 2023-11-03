<script>
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import {
  required,
  requiredIf,
  minLength,
  maxLength,
  integer,
  numeric,
} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../general/loader";
import Multiselect from "vue-multiselect";
import DatePicker from "vue2-datepicker";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import Building from "./building";
import unitStatus from "../../../components/create/realEstate/unitStatus";
import propertyTree from "../general/property-tree";
import { arabicValue, englishValue } from "../../../helper/langTransform";
import ViewComp from "../../../components/create/realEstate/view";
import Finishing from "../../../components/create/realEstate/finishing";
import UnitType from "../../../components/create/realEstate/unit_type";
import successError from "../../../helper/mixin/success&error";

/**
 * Advanced Table component
 */

export default {
    components: {
        ViewComp,
        UnitType,
        ErrorMessage,
        loader,
        DatePicker,
        Multiselect,
        Building,
        unitStatus,
        propertyTree,
        Finishing,
    },
  mixins: [transMixinComp, successError],
  props: {
        id: { default: "create" },
        companyKeys: { default: [] },
        defaultsKeys: { default: [] },
        isPage: { default: true },
        isVisiblePage: { default: null },
        isRequiredPage: { default: null },
        type: { default: "create" },
        idObjEdit: { default: null },
        isPermission: {},
        url: { default: "/real-estate/units" },
    },
  data() {
        return {
            debounce: {},
            images: [],
            media: {},
            isCustom: false,
            unit_id: null,
            saveImageName: [],
            showPhoto: "./images/img-1.png",
            mime_type: "",
            units: [],
            buildings: [],
            unit_status: [],
            properties: [],
            modules: ["sell", "buying"],
            isLoader: false,
            createVideo: "",
            frameUrl: "",
            prevUnitStatusId: null,
            views: [],
            finishings: [],
            unitTypes: [],
            fields: [],
            create: {
                name: "",
                name_e: "",
                description: "",
                description_e: "",
                code: "",
                unit_ty: null,
                unit_area: 0,
                unit_net_area: 0,
                properties: [],
                module: "sell",
                building_id: null,
                unit_status_id: null,
                rooms: 0,
                path: 0,
                view: null,
                floor: 0,
                finishing: null,
            },
            errors: {},
            image: "",
            company_id: null,
            is_disabled: false,
            idDelete: null,
        };
    },
  validations: {
        createVideo: { required },
        create: {
            name: {
                required: requiredIf(function (model) {
                    return this.isRequired("name");
                }),
                minLength: minLength(2),
                maxLength: maxLength(100),
            },
            name_e: {
                required: requiredIf(function (model) {
                    return this.isRequired("name_e");
                }),
                minLength: minLength(2),
                maxLength: maxLength(100),
            },
            description: {
                required: requiredIf(function (model) {
                    return this.isRequired("description");
                }),
                maxLength: maxLength(1000),
            },
            description_e: {
                required: requiredIf(function (model) {
                    return this.isRequired("description_e");
                }),
                maxLength: maxLength(1000),
            },
            code: {
                required: requiredIf(function (model) {
                    return this.isRequired("code");
                }),
                maxLength: maxLength(20),
            },
            unit_ty: {
                required: requiredIf(function (model) {
                    return this.isRequired("unit_ty");
                }),
                integer,
            },
            unit_area: {
                required: requiredIf(function (model) {
                    return this.isRequired("unit_area");
                }),
                numeric,
            },
            // module: {
            //   required: requiredIf(function (model) {
            //     return this.isRequired("module");
            //   }),
            // },
            properties: {
                required: requiredIf(function (model) {
                    return this.isRequired("properties");
                }),
            },
            building_id: {
                required: requiredIf(function (model) {
                    return this.isRequired("building_id");
                }),
            },
            unit_status_id: {
                required: requiredIf(function (model) {
                    return this.isRequired("unit_status_id");
                }),
            },
            rooms: {
                required: requiredIf(function (model) {
                    return this.isRequired("rooms");
                }),
                integer,
            },
            path: {
                required: requiredIf(function (model) {
                    return this.isRequired("path");
                }),
                integer,
            },
            view: {
                required: requiredIf(function (model) {
                    return this.isRequired("view");
                }),
                integer,
            },
            floor: {
                required: requiredIf(function (model) {
                    return this.isRequired("floor");
                }),
                integer,
            },
            finishing: {
                required: requiredIf(function (model) {
                    return this.isRequired("finishing");
                }),
                integer,
            },
            unit_net_area: {
                required: requiredIf(function (model) {
                    return this.isRequired("unit_net_area");
                }),
                numeric,
            },
        },
    },
  mounted() {
      this.company_id = this.$store.getters["auth/company_id"];
  },
  methods: {
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
        async getCustomTableFields() {
            this.isCustom = true;
            await adminApi
                .get(`/customTable/table-columns/rlst_units`)
                .then((res) => {
                    this.fields = res.data;
                })
                .catch((err) => {
                    this.errorFun("Error", "Thereisanerrorinthesystem");
                })
                .finally(() => {
                    this.isCustom = false;
                });
        },
        AddVideo(action) {
            let data = action == "create" ? this.create : this.edit;
            this.$v.createVideo.$touch();
            if (this.$v.createVideo.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                adminApi
                    .put(`/real-estate/units/${this.unit_id}`, {
                        ...data,
                        video_link: this.createVideo,
                    })
                    .then((res) => {
                        this.$emit("getDataTable");
                        this.frameUrl = res.data.data.video_link;
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
                            this.errorFun("Error", "Thereisanerrorinthesystem");
                        }
                    })
                    .finally(() => {
                        this.isLoader = false;
                    });
            }
        },
        arabicValue(txt) {
            this.create.name = arabicValue(txt);
        },
        englishValue(txt) {
            this.create.name_e = englishValue(txt);
        },
        arabicValueDescription(txt) {
            this.create.description = arabicValue(txt);
        },
        englishValueDescription(txt) {
            this.create.description_e = englishValue(txt);
        },
        defaultData(edit = null){
              this.createVideo = "";
              this.frameUrl = "";

              this.create = {
                  name: "",
                  name_e: "",
                  description: "",
                  description_e: "",
                  code: "",
                  properties: [],
                  unit_ty: null,
                  unit_area: 0,
                  unit_net_area: 0,
                  module: "sell",
                  building_id: null,
                  unit_status_id: null,
                  rooms: 0,
                  path: 0,
                  view: null,
                  floor: 0,
                  finishing: null,
              };
              this.$nextTick(() => {
                  this.$v.$reset();
              });
              this.showPhoto = "./images/img-1.png";
              this.errors = {};
              this.images = [];
              this.is_disabled = false;
              this.mime_type = "";
              this.unit_id = null;
              this.media = {};
              this.create.unit_status_id = 1;
          },
        resetModalHidden() {
              this.defaultData();
              this.$bvModal.hide(this.id);
          },
        resetModal() {
            this.defaultData();
            setTimeout(async () => {
                if (this.type != 'edit') {
                    if (!this.isPage) await this.getCustomTableFields();
                    if (this.isVisible("building_id")) this.getBuildings();
                    if (this.isVisible("properties")) this.getProperty();
                    if (this.isVisible("unit_status_id")) this.getUnitStatus();
                    if (this.isVisible("view")) this.getViews();
                    if (this.isVisible("finishing")) this.getFinishing();
                    if (this.isVisible("unit_ty")) this.getUnitTypes();
                } else {
                    if (this.idObjEdit.dataObj) {
                        let unit = this.idObjEdit.dataObj;
                        this.errors = {};
                        this.createVideo = "";
                        this.frameUrl = unit.video_link;
                        this.unit_id = unit.id;
                        this.create.name = unit.name;
                        this.create.name_e = unit.name_e;
                        this.create.properties = unit.properties;
                        this.create.description = unit.description;
                        this.create.description_e = unit.description_e;
                        this.create.building_id = unit.building_id;
                        this.create.unit_status_id = unit.unit_status_id;
                        this.prevUnitStatusId = unit.unit_status_id;
                        this.create.module = unit.name;
                        this.create.code = unit.code;
                        this.create.unit_ty = unit.unit_ty;
                        this.create.finishing = unit.finishing;
                        this.create.unit_area = unit.unit_area;
                        this.create.floor = unit.floor;
                        this.create.path = unit.path;
                        this.create.unit_net_area = unit.unit_net_area;
                        this.create.rooms = unit.rooms;
                        this.create.view = unit.view;
                        this.images = unit.media ?? [];
                        if (this.images && this.images.length > 0) {
                            this.showPhoto = this.images[this.images.length - 1].url;
                            this.mime_type = this.images[this.images.length - 1].mime_type;
                        } else {
                            this.showPhoto = "./images/img-1.png";
                        }
                        if (this.isVisible("building_id")) this.getBuildings();
                        if (this.isVisible("properties")) this.getProperty();
                        if (this.isVisible("unit_status_id")) this.getUnitStatus();
                        if (this.isVisible("view")) this.getViews();
                        if (this.isVisible("finishing")) this.getFinishing();
                        if (this.isVisible("unit_ty")) this.getUnitTypes();
                    }
                }
            }, 50);
        },
        resetForm() {
            this.defaultData();
        },
        AddSubmit() {
            if (!this.create.name) {
                this.create.name = this.create.name_e;
            }
            if (!this.create.name_e) {
                this.create.name_e = this.create.name;
            }
            if (!this.create.description) {
                this.create.description = this.create.description_e;
            }
            if (!this.create.description_e) {
                this.create.description_e = this.create.description;
            }

            this.$v.create.$touch();

            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};

                if (this.type != "edit") {
                    adminApi
                        .post(this.url, {
                            ...this.create,
                            company_id: this.$store.getters["auth/company_id"],
                        })
                        .then((res) => {
                            this.is_disabled = true;
                            this.unit_id = res.data.data.id;
                            if (!this.isPage) this.$emit("created");
                            else this.$emit("getDataTable");

                            setTimeout(() => {
                                this.successFun("Addedsuccessfully");
                            }, 500);
                        })
                        .catch((err) => {
                            if (err.response.data) {
                                this.errors = err.response.data.errors;
                            } else {
                                this.errorFun("Error", "Thereisanerrorinthesystem");
                            }
                        })
                        .finally(() => {
                            this.isLoader = false;
                        });
                } else {
                    adminApi
                        .put(`${this.url}/${this.idObjEdit.idEdit}`, {
                            ...this.create,
                            company_id: this.$store.getters["auth/company_id"],
                        })
                        .then((res) => {
                            this.$bvModal.hide(this.id);
                            this.$emit("getDataTable");
                            setTimeout(() => {
                                this.successFun("Editsuccessfully");
                            }, 500);
                        })
                        .catch((err) => {
                            if (err.response.data) {
                                this.errors = err.response.data.errors;
                            } else {
                                this.errorFun("Error", "Thereisanerrorinthesystem");
                            }
                        })
                        .finally(() => {
                            this.isLoader = false;
                        });
                }

            }
        },
        getBuildings() {
            this.isLoader = true;
             adminApi
                .get(`/real-estate/buildings/get-drop-down`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    if (this.isPermission("create building RealState")) {
                        l.unshift({ id: 0, name: "اضافة مبنى", name_e: "Add building" });
                    }
                    this.buildings = l;
                })
                .catch((err) => {
                    this.errorFun("Error", "Thereisanerrorinthesystem");
                });
        },
         getViews() {
            this.isLoader = true;
             adminApi
                .get(`/real-estate/view/get-drop-down`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف منظر", name_e: "Add view" });
                    this.views = l;
                })
                .catch((err) => {
                    this.errorFun("Error", "Thereisanerrorinthesystem");
                });
        },
         getFinishing() {
            this.isLoader = true;
             adminApi
                .get(`/real-estate/finishing/get-drop-down`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف تشطيب", name_e: "Add finishing" });
                    this.finishings = l;
                })
                .catch((err) => {
                    this.errorFun("Error", "Thereisanerrorinthesystem");
                });
        },
         getUnitTypes() {
            this.isLoader = true;
             adminApi
                .get(`/real-estate/unit-type/get-drop-down`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف نوع وحدة", name_e: "Add unit type" });
                    this.unitTypes = l;
                })
                .catch((err) => {
                    this.errorFun("Error", "Thereisanerrorinthesystem");
                });
        },
         getUnitStatus() {
            this.isLoader = true;

             adminApi
                .get(`real-estate/unit-statuses/get-drop-down?is_active=active`)
                .then((res) => {
                    let l = res.data.data;
                    if (this.isPermission("create unit_status RealState")) {
                        l.unshift({
                            id: 0,
                            name: "اضف حاله الوحده  ",
                            name_e: "Add Unit Status",
                        });
                    }
                    this.unit_status = l;
                })
                .catch((err) => {
                    this.errorFun("Error", "Thereisanerrorinthesystem");
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
         getProperty() {
            this.isLoader = true;

             adminApi
                .get(`/tree-properties/get-drop-down`)
                .then((res) => {
                    let l = res.data.data;
                    if (this.isPermission("create Tree Property")) {
                        l.unshift({ id: 0, name: "اضف خصائص  ", name_e: "Add Property" });
                    }
                    this.properties = l;
                })
                .catch((err) => {
                    this.errorFun("Error", "Thereisanerrorinthesystem");
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        getCurrentYear() {
            return new Date().getFullYear();
        },
        showBuildingModal() {
            if (this.create.building_id == 0) {
                this.$bvModal.show("building-create-unit");
                this.create.building_id = null;
            }
        },
        showViewModal() {
            setTimeout(() => {
                if (this.create.view == 0) {
                    this.$bvModal.show("create_view_unit");
                    this.create.view = null;
                }
            }, 100);
        },
        showFinishingModal() {
            setTimeout(() => {
                if (this.create.finishing == 0) {
                    this.$bvModal.show("create_finishing_unit");
                    this.create.finishing = null;
                }
            }, 100);
        },
        showUnitTypeModal() {
            setTimeout(() => {
                if (this.create.unit_ty == 0) {
                    this.$bvModal.show("create_unit_type_unit");
                    this.create.unit_ty = null;
                }
            }, 100);
        },
        showUnitStatusModal() {
            if (this.create.unit_status_id == 0) {
                this.$bvModal.show("unit-satatus-create_unit");
                this.create.unit_status_id = null;
            }
        },
        showPropertyModal() {
            if (this.create.properties.includes(0)) {
                this.$bvModal.show("property-create-unit");
                this.create.properties.pop();
            }
        },
        changePhoto() {
            document.getElementById("uploadImageCreate").click();
        },
        onImageChanged(e) {
            const file = e.target.files[0];
            this.addImage(file);
        },
        addImage(file) {
            this.media = file; //upload
            if (file) {
                this.idDelete = null;
                let is_media = this.images.find(
                    (e) => e.name == file.name.slice(0, file.name.indexOf("."))
                );
                this.idDelete = is_media ? is_media.id : null;
                if (!this.idDelete) {
                    this.isLoader = true;
                    let formDate = new FormData();
                    formDate.append("media[0]", this.media);
                    adminApi
                        .post(`/media`, formDate)
                        .then((res) => {
                            let old_media = [];
                            this.images.forEach((e) => old_media.push(e.id));
                            let new_media = [];
                            res.data.data.forEach((e) => new_media.push(e.id));

                            adminApi
                                .put(`/real-estate/units/${this.unit_id}`, {
                                    old_media,
                                    media: new_media,
                                })
                                .then((res) => {
                                    this.images = res.data.data.media ?? [];
                                    if (this.images && this.images.length > 0) {
                                        this.showPhoto = this.images[this.images.length - 1].url;
                                        this.mime_type =
                                            this.images[this.images.length - 1].mime_type;
                                    } else {
                                        this.showPhoto = "./images/img-1.png";
                                    }
                                    this.$emit("getDataTable");
                                })
                                .catch((err) => {
                                    this.errorFun("Error", "Thereisanerrorinthesystem");
                                });
                        })
                        .catch((err) => {
                            if (err.response.data) {
                                this.errors = err.response.data.errors;
                            } else {
                                this.errorFun("Error", "Thereisanerrorinthesystem");
                            }
                        })
                        .finally(() => {
                            this.isLoader = false;
                        });
                } else {
                    Swal.fire({
                        title: `${this.$t("general.Thisfilehasalreadybeenuploaded")}`,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonText: `${this.$t("general.Replace")}`,
                        cancelButtonText: `${this.$t("general.Nocancel")}`,
                        confirmButtonClass: "btn btn-success mt-2",
                        cancelButtonClass: "btn btn-danger ml-2 mt-2",
                        buttonsStyling: false,
                    }).then((result) => {
                        if (result.value) {
                            this.isLoader = true;
                            let formDate = new FormData();
                            formDate.append("media[0]", this.media);
                            adminApi
                                .post(`/media`, formDate)
                                .then((res) => {
                                    let old_media = [];
                                    this.images.forEach((e) => old_media.push(e.id));
                                    old_media.splice(old_media.indexOf(this.idDelete), 1);
                                    let new_media = [];
                                    res.data.data.forEach((e) => new_media.push(e.id));

                                    adminApi
                                        .put(`/real-estate/units/${this.unit_id}`, {
                                            old_media,
                                            media: new_media,
                                        })
                                        .then((res) => {
                                            this.images = res.data.data.media ?? [];
                                            if (this.images && this.images.length > 0) {
                                                this.showPhoto =
                                                    this.images[this.images.length - 1].url;
                                                this.mime_type =
                                                    this.images[this.images.length - 1].mime_type;
                                            } else {
                                                this.showPhoto = "./images/img-1.png";
                                            }
                                            this.$emit("getDataTable");
                                        })
                                        .catch((err) => {
                                            this.errorFun("Error", "Thereisanerrorinthesystem");
                                        });
                                })
                                .catch((err) => {
                                    if (err.response.data) {
                                        this.errors = err.response.data.errors;
                                    } else {
                                        this.errorFun("Error", "Thereisanerrorinthesystem");
                                    }
                                })
                                .finally(() => {
                                    this.isLoader = false;
                                });
                        }
                    });
                }
            }
        },
        deleteImageCreate(id, index) {
            let old_media = [];
            this.images.forEach((e) => {
                if (e.id != id) {
                    old_media.push(e.id);
                }
            });
            adminApi
                .put(`/real-estate/units/${this.unit_id}`, { old_media })
                .then((res) => {
                    this.units[index] = res.data.data;
                    this.images = res.data.data.media ?? [];
                    if (this.images && this.images.length > 0) {
                        this.showPhoto = this.images[this.images.length - 1].url;
                        this.mime_type = this.images[this.images.length - 1].mime_type;
                    } else {
                        this.showPhoto = "./images/img-1.png";
                    }
                })
                .catch((err) => {
                    this.errorFun("Error", "Thereisanerrorinthesystem");
                });
        },
    },
};
</script>

<template>
  <div>
    <Building
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getBuildings"
      :id="'building-create-unit'"
      :isPage="false"
      type="create"
      :isPermission="isPermission"
    />
    <ViewComp
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getViews"
      :id="'create_view_unit'"
      :isPage="false"
      type="create"
      :isPermission="isPermission"
    />
    <Finishing
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getFinishing"
      :id="'create_finishing_unit'"
      :isPage="false"
      type="create"
      :isPermission="isPermission"
    />
    <UnitType
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getUnitTypes"
      :id="'create_unit_type_unit'"
      :isPage="false"
      type="create"
      :isPermission="isPermission"
    />
    <unitStatus
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getUnitStatus"
      :id="'unit-satatus-create_unit'"
      :isPage="false"
      type="create"
      :isPermission="isPermission"
    />
    <propertyTree
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getProperty"
      :id="'property-create-unit'"
      :isPage="false"
      type="create"
      :isPermission="isPermission"
    />

    <!--  create   -->
    <b-modal
          :id="id"
          :title="$i18n.locale == 'ar'?getCompanyKey('realEstate_unit_create_form'):getCompanyKey('realEstate_unit_edit_form')"
          title-class="font-18"
          dialog-class="modal-full-width"
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
                      :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : '']"
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
              <b-tabs nav-class="nav-tabs nav-bordered">
                  <b-tab :title="$t('general.DataEntry')" active>
                      <div class="row">
                          <div v-if="isVisible('building_id')" class="col-md-3">
                              <div class="form-group position-relative">
                                  <label class="control-label">
                                      {{ getCompanyKey("realEstate_unit_building") }}
                                      <span
                                          v-if="isRequired('building_id')"
                                          class="text-danger"
                                      >*</span
                                      >
                                  </label>
                                  <multiselect
                                      @input="showBuildingModal"
                                      v-model="$v.create.building_id.$model"
                                      :options="buildings.map((type) => type.id)"
                                      :custom-label="
                              (opt) => buildings.find((x) => x.id == opt)?
                                $i18n.locale == 'ar'
                                  ? buildings.find((x) => x.id == opt).name
                                  : buildings.find((x) => x.id == opt).name_e: null
                            "
                                  >
                                  </multiselect>
                                  <div
                                      v-if="
                              $v.create.building_id.$error || errors.building_id
                            "
                                      class="text-danger"
                                  >
                                      {{ $t("general.fieldIsRequired") }}
                                  </div>
                                  <template v-if="errors.building_id">
                                      <ErrorMessage
                                          v-for="(
                                errorMessage, index
                              ) in errors.building_id"
                                          :key="index"
                                      >{{ errorMessage }}</ErrorMessage
                                      >
                                  </template>
                              </div>
                          </div>
                          <div v-if="isVisible('code')" class="col-md-3">
                              <div class="form-group">
                                  <label for="field-4353" class="control-label">
                                      {{ getCompanyKey("realEstate_unit_code") }}
                                      <span v-if="isRequired('code')" class="text-danger"
                                      >*</span
                                      >
                                  </label>
                                  <div dir="ltr">
                                      <input
                                          type="text"
                                          class="form-control"
                                          v-model="$v.create.code.$model"
                                          :class="{
                                'is-invalid':
                                  $v.create.code.$error || errors.code,
                                'is-valid':
                                  !$v.create.code.$invalid && !errors.code,
                              }"
                                          id="field-4353"
                                      />
                                  </div>
                                  <div
                                      v-if="!$v.create.code.maxLength"
                                      class="invalid-feedback"
                                  >
                                      {{ $t("general.Itmustbeatmost") }}
                                      {{ $v.create.code.$params.maxLength.max }}
                                      {{ $t("general.letters") }}
                                  </div>
                                  <template v-if="errors.code">
                                      <ErrorMessage
                                          v-for="(errorMessage, index) in errors.code"
                                          :key="index"
                                      >{{ errorMessage }}</ErrorMessage
                                      >
                                  </template>
                              </div>
                          </div>
                      </div>
                      <hr
                          style="
                        margin: 10px 0 !important;
                        border-top: 1px solid rgb(141 163 159 / 42%);
                      "
                      />
                      <div class="row">
                          <div v-if="isVisible('unit_ty')" class="col-md-3">
                              <div class="form-group">
                                  <label class="control-label">
                                      {{ getCompanyKey("realEstate_unit_unit_ty") }}
                                      <span
                                          v-if="isRequired('unit_ty')"
                                          class="text-danger"
                                      >*</span
                                      >
                                  </label>
                                  <multiselect
                                      @input="showUnitTypeModal"
                                      v-model="$v.create.unit_ty.$model"
                                      :options="unitTypes.map((type) => type.id)"
                                      :custom-label="
                              (opt) => unitTypes.find((x) => x.id == opt)?
                                $i18n.locale == 'ar'
                                  ? unitTypes.find((x) => x.id == opt).name
                                  : unitTypes.find((x) => x.id == opt).name_e: null
                            "
                                  >
                                  </multiselect>
                                  <div
                                      v-if="$v.create.unit_ty.$error || errors.unit_ty"
                                      class="text-danger"
                                  >
                                      {{ $t("general.fieldIsRequired") }}
                                  </div>
                                  <template v-if="errors.unit_ty">
                                      <ErrorMessage
                                          v-for="(errorMessage, index) in errors.unit_ty"
                                          :key="index"
                                      >{{ errorMessage }}</ErrorMessage
                                      >
                                  </template>
                              </div>
                          </div>
                          <div class="col-md-3" v-if="isVisible('name')">
                              <div class="form-group">
                                  <label for="field-1" class="control-label">
                                      {{ getCompanyKey("realEstate_unit_name_ar") }}
                                      <span v-if="isRequired('name')" class="text-danger"
                                      >*</span
                                      >
                                  </label>
                                  <div dir="rtl">
                                      <input
                                          @keyup="arabicValue(create.name)"
                                          type="text"
                                          class="form-control"
                                          v-model="$v.create.name.$model"
                                          :class="{
                                'is-invalid':
                                  $v.create.name.$error || errors.name,
                                'is-valid':
                                  !$v.create.name.$invalid && !errors.name,
                              }"
                                          id="field-1"
                                      />
                                  </div>
                                  <div
                                      v-if="!$v.create.name.minLength"
                                      class="invalid-feedback"
                                  >
                                      {{ $t("general.Itmustbeatleast") }}
                                      {{ $v.create.name.$params.minLength.min }}
                                      {{ $t("general.letters") }}
                                  </div>
                                  <div
                                      v-if="!$v.create.name.maxLength"
                                      class="invalid-feedback"
                                  >
                                      {{ $t("general.Itmustbeatmost") }}
                                      {{ $v.create.name.$params.maxLength.max }}
                                      {{ $t("general.letters") }}
                                  </div>
                                  <template v-if="errors.name">
                                      <ErrorMessage
                                          v-for="(errorMessage, index) in errors.name"
                                          :key="index"
                                      >{{ errorMessage }}</ErrorMessage
                                      >
                                  </template>
                              </div>
                          </div>
                          <div class="col-md-3" v-if="isVisible('name_e')">
                              <div class="form-group">
                                  <label for="field-2" class="control-label">
                                      {{ getCompanyKey("realEstate_unit_name_en") }}
                                      <span
                                          v-if="isRequired('name_e')"
                                          class="text-danger"
                                      >*</span
                                      >
                                  </label>
                                  <div dir="ltr">
                                      <input
                                          @keyup="englishValue(create.name_e)"
                                          type="text"
                                          class="form-control"
                                          v-model="$v.create.name_e.$model"
                                          :class="{
                                'is-invalid':
                                  $v.create.name_e.$error || errors.name_e,
                                'is-valid':
                                  !$v.create.name_e.$invalid && !errors.name_e,
                              }"
                                          id="field-2"
                                      />
                                  </div>
                                  <div
                                      v-if="!$v.create.name_e.minLength"
                                      class="invalid-feedback"
                                  >
                                      {{ $t("general.Itmustbeatleast") }}
                                      {{ $v.create.name_e.$params.minLength.min }}
                                      {{ $t("general.letters") }}
                                  </div>
                                  <div
                                      v-if="!$v.create.name_e.maxLength"
                                      class="invalid-feedback"
                                  >
                                      {{ $t("general.Itmustbeatmost") }}
                                      {{ $v.create.name_e.$params.maxLength.max }}
                                      {{ $t("general.letters") }}
                                  </div>
                                  <template v-if="errors.name_e">
                                      <ErrorMessage
                                          v-for="(errorMessage, index) in errors.name_e"
                                          :key="index"
                                      >{{ errorMessage }}</ErrorMessage
                                      >
                                  </template>
                              </div>
                          </div>
                          <div class="col-md-3" v-if="isVisible('floor')">
                              <div class="form-group">
                                  <label class="control-label">
                                      {{ getCompanyKey("realEstate_unit_floor") }}
                                      <span v-if="isRequired('floor')" class="text-danger"
                                      >*</span
                                      >
                                  </label>
                                  <input
                                      type="number"
                                      class="form-control"
                                      v-model="$v.create.floor.$model"
                                      :class="{
                              'is-invalid':
                                $v.create.floor.$error || errors.floor,
                              'is-valid':
                                !$v.create.floor.$invalid && !errors.floor,
                            }"
                                  />
                                  <template v-if="errors.floor">
                                      <ErrorMessage
                                          v-for="(errorMessage, index) in errors.floor"
                                          :key="index"
                                      >{{ errorMessage }}</ErrorMessage
                                      >
                                  </template>
                              </div>
                          </div>
                      </div>
                      <hr
                          style="
                        margin: 10px 0 !important;
                        border-top: 1px solid rgb(141 163 159 / 42%);
                      "
                      />
                      <div class="row">
                          <div v-if="isVisible('unit_status_id')" class="col-md-3">
                              <div
                                  v-if="isRequired('unit_status_id')"
                                  class="form-group position-relative"
                              >
                                  <label class="control-label">
                                      {{ getCompanyKey("realEstate_unit_status") }}
                                      <span class="text-danger">*</span>
                                  </label>
                                  <multiselect
                                      :disabled="true"
                                      @input="showUnitStatusModal"
                                      v-model="$v.create.unit_status_id.$model"
                                      :options="unit_status.map((type) => type.id)"
                                      :custom-label="
                              (opt) => unit_status.find((x) => x.id == opt)?
                                $i18n.locale == 'ar'
                                  ? unit_status.find((x) => x.id == opt).name
                                  : unit_status.find((x) => x.id == opt).name_e: null
                            "
                                  >
                                  </multiselect>
                                  <div
                                      v-if="
                              $v.create.unit_status_id.$error ||
                              errors.unit_status_id
                            "
                                      class="text-danger"
                                  >
                                      {{ $t("general.fieldIsRequired") }}
                                  </div>
                                  <template v-if="errors.unit_status_id">
                                      <ErrorMessage
                                          v-for="(
                                errorMessage, index
                              ) in errors.unit_status_id"
                                          :key="index"
                                      >{{ errorMessage }}</ErrorMessage
                                      >
                                  </template>
                              </div>
                          </div>
                          <div v-if="isVisible('unit_area')" class="col-md-3">
                              <div class="form-group">
                                  <label class="control-label">
                                      {{ getCompanyKey("realEstate_unit_unit_area") }}
                                      <span
                                          v-if="isRequired('unit_area')"
                                          class="text-danger"
                                      >*</span
                                      >
                                  </label>
                                  <input
                                      type="number"
                                      class="form-control"
                                      step="0.1"
                                      v-model="$v.create.unit_area.$model"
                                      :class="{
                              'is-invalid':
                                $v.create.unit_area.$error || errors.unit_area,
                              'is-valid':
                                !$v.create.unit_area.$invalid &&
                                !errors.unit_area,
                            }"
                                  />
                                  <template v-if="errors.unit_area">
                                      <ErrorMessage
                                          v-for="(errorMessage, index) in errors.land_area"
                                          :key="index"
                                      >{{ errorMessage }}</ErrorMessage
                                      >
                                  </template>
                              </div>
                          </div>
                          <div v-if="isVisible('unit_net_area')" class="col-md-3">
                              <div class="form-group">
                                  <label class="control-label">
                                      {{ getCompanyKey("realEstate_unit_unit_net_area") }}
                                      <span
                                          v-if="isRequired('unit_net_area')"
                                          class="text-danger"
                                      >*</span
                                      >
                                  </label>
                                  <input
                                      type="number"
                                      class="form-control"
                                      step="0.1"
                                      v-model="$v.create.unit_net_area.$model"
                                      :class="{
                              'is-invalid':
                                $v.create.unit_net_area.$error ||
                                errors.unit_net_area,
                              'is-valid':
                                !$v.create.unit_net_area.$invalid &&
                                !errors.unit_net_area,
                            }"
                                  />
                                  <template v-if="errors.unit_net_area">
                                      <ErrorMessage
                                          v-for="(
                                errorMessage, index
                              ) in errors.unit_net_area"
                                          :key="index"
                                      >{{ errorMessage }}</ErrorMessage
                                      >
                                  </template>
                              </div>
                          </div>
                          <div v-if="isVisible('rooms')" class="col-md-3">
                              <div class="form-group">
                                  <label class="control-label">
                                      {{ getCompanyKey("realEstate_unit_rooms") }}
                                      <span v-if="isRequired('rooms')" class="text-danger"
                                      >*</span
                                      >
                                  </label>
                                  <input
                                      type="number"
                                      class="form-control"
                                      v-model="$v.create.rooms.$model"
                                      :class="{
                              'is-invalid':
                                $v.create.rooms.$error || errors.rooms,
                              'is-valid':
                                !$v.create.rooms.$invalid && !errors.rooms,
                            }"
                                  />
                                  <template v-if="errors.rooms">
                                      <ErrorMessage
                                          v-for="(errorMessage, index) in errors.rooms"
                                          :key="index"
                                      >{{ errorMessage }}</ErrorMessage
                                      >
                                  </template>
                              </div>
                          </div>
                          <div v-if="isVisible('path')" class="col-md-3">
                              <div class="form-group">
                                  <label class="control-label">
                                      {{ getCompanyKey("realEstate_unit_path") }}
                                      <span v-if="isRequired('path')" class="text-danger"
                                      >*</span
                                      >
                                  </label>
                                  <input
                                      type="number"
                                      class="form-control"
                                      v-model="$v.create.path.$model"
                                      :class="{
                              'is-invalid':
                                $v.create.path.$error || errors.path,
                              'is-valid':
                                !$v.create.path.$invalid && !errors.path,
                            }"
                                  />
                                  <template v-if="errors.path">
                                      <ErrorMessage
                                          v-for="(errorMessage, index) in errors.path"
                                          :key="index"
                                      >{{ errorMessage }}</ErrorMessage
                                      >
                                  </template>
                              </div>
                          </div>
                      </div>
                      <hr
                          style="
                        margin: 10px 0 !important;
                        border-top: 1px solid rgb(141 163 159 / 42%);
                      "
                      />
                      <div class="row">
                          <div class="col-md-3" v-if="isVisible('finishing')">
                              <div class="form-group">
                                  <label class="control-label">
                                      {{ getCompanyKey("realEstate_unit_finishing") }}
                                      <span
                                          v-if="isRequired('finishing')"
                                          class="text-danger"
                                      >*</span
                                      >
                                  </label>
                                  <multiselect
                                      @input="showFinishingModal"
                                      v-model="$v.create.finishing.$model"
                                      :options="finishings.map((type) => type.id)"
                                      :custom-label="
                              (opt) => finishings.find((x) => x.id == opt)?
                                $i18n.locale == 'ar'
                                  ? finishings.find((x) => x.id == opt).name
                                  : finishings.find((x) => x.id == opt).name_e:null
                            "
                                  >
                                  </multiselect>
                                  <div
                                      v-if="
                              $v.create.finishing.$error || errors.finishing
                            "
                                      class="text-danger"
                                  >
                                      {{ $t("general.fieldIsRequired") }}
                                  </div>
                                  <template v-if="errors.finishing">
                                      <ErrorMessage
                                          v-for="(errorMessage, index) in errors.finishing"
                                          :key="index"
                                      >{{ errorMessage }}</ErrorMessage
                                      >
                                  </template>
                              </div>
                          </div>
                          <div class="col-md-3" v-if="isVisible('view')">
                              <div class="form-group">
                                  <label class="control-label">
                                      {{ getCompanyKey("realEstate_unit_view") }}
                                      <span v-if="isRequired('view')" class="text-danger"
                                      >*</span
                                      >
                                  </label>
                                  <multiselect
                                      @input="showViewModal"
                                      v-model="$v.create.view.$model"
                                      :options="views.map((type) => type.id)"
                                      :custom-label="
                              (opt) => views.find((x) => x.id == opt)?
                                $i18n.locale == 'ar'
                                  ? views.find((x) => x.id == opt).name
                                  : views.find((x) => x.id == opt).name_e: null
                            "
                                  >
                                  </multiselect>
                                  <div
                                      v-if="$v.create.view.$error || errors.view"
                                      class="text-danger"
                                  >
                                      {{ $t("general.fieldIsRequired") }}
                                  </div>
                                  <template v-if="errors.view">
                                      <ErrorMessage
                                          v-for="(errorMessage, index) in errors.view"
                                          :key="index"
                                      >{{ errorMessage }}</ErrorMessage
                                      >
                                  </template>
                              </div>
                          </div>
                          <div class="col-md-3" v-if="isVisible('properties')">
                              <div class="form-group">
                                  <label class="control-label">
                                      {{ getCompanyKey("realEstate_unit_properties") }}
                                      <span
                                          v-if="isRequired('properties')"
                                          class="text-danger"
                                      >*</span
                                      >
                                  </label>
                                  <multiselect
                                      :multiple="true"
                                      @input="showPropertyModal"
                                      v-model="$v.create.properties.$model"
                                      :options="properties.map((type) => type.id)"
                                      :custom-label="
                              (opt) => properties.find((x) => x.id == opt)?
                                $i18n.locale == 'ar'
                                  ? properties.find((x) => x.id == opt).name
                                  : properties.find((x) => x.id == opt).name_e: null
                            "
                                  >
                                  </multiselect>
                                  <template v-if="errors.properties">
                                      <ErrorMessage
                                          v-for="(errorMessage, index) in errors.properties"
                                          :key="index"
                                      >{{ errorMessage }}</ErrorMessage
                                      >
                                  </template>
                              </div>
                          </div>
                          <div class="col-md-6" v-if="isVisible('description')">
                              <div class="form-group">
                                  <label class="mr-2">
                                      {{
                                          getCompanyKey("realEstate_unit_description_ar")
                                      }}
                                      <span
                                          v-if="isVisible('description')"
                                          class="text-danger"
                                      >*</span
                                      >
                                  </label>
                                  <textarea
                                      @input="arabicValueDescription(create.description)"
                                      v-model="$v.create.description.$model"
                                      class="form-control"
                                      :maxlength="1000"
                                      rows="5"
                                  ></textarea>
                                  <template v-if="errors.description">
                                      <ErrorMessage
                                          v-for="(
                                errorMessage, index
                              ) in errors.description"
                                          :key="index"
                                      >{{ errorMessage }}</ErrorMessage
                                      >
                                  </template>
                              </div>
                          </div>
                          <div class="col-md-6" v-if="isVisible('description_e')">
                              <div class="form-group">
                                  <label class="mr-2">
                                      {{
                                          getCompanyKey("realEstate_unit_description_en")
                                      }}
                                      <span
                                          v-if="isRequired('description_e')"
                                          class="text-danger"
                                      >*</span
                                      >
                                  </label>
                                  <textarea
                                      @input="
                              englishValueDescription(create.description_e)
                            "
                                      v-model="$v.create.description_e.$model"
                                      class="form-control"
                                      :maxlength="1000"
                                      rows="5"
                                  ></textarea>
                                  <template v-if="errors.description_e">
                                      <ErrorMessage
                                          v-for="(
                                errorMessage, index
                              ) in errors.description_e"
                                          :key="index"
                                      >{{ errorMessage }}</ErrorMessage
                                      >
                                  </template>
                              </div>
                          </div>
                      </div>
                  </b-tab>
                  <b-tab :disabled="!unit_id" :title="$t('general.video')">
                      <div class="d-flex justify-content-end">
                          <b-button
                              variant="success"
                              type="button"
                              class="mx-1"
                              v-if="!isLoader"
                              @click.prevent="AddVideo('create')"
                          >
                              {{ $t("general.Add") }}
                          </b-button>
                          <b-button variant="success" class="mx-1" disabled v-else>
                              <b-spinner small></b-spinner>
                              <span class="sr-only"
                              >{{ $t("login.Loading") }}...</span
                              >
                          </b-button>
                      </div>
                      <div class="row">
                          <div class="col-md-4">
                              <div class="form-group">
                                  <label class="control-label">
                                      {{ $t("general.video") }}
                                      <span class="text-danger">*</span>
                                  </label>
                                  <input
                                      type="text"
                                      class="form-control"
                                      v-model="$v.createVideo.$model"
                                      :class="{
                              'is-invalid': $v.createVideo.$error,
                              'is-valid': !$v.createVideo.$invalid,
                            }"
                                  />
                              </div>
                          </div>
                          <div
                              v-html="frameUrl"
                              v-if="frameUrl"
                              class="col-md-12"
                          ></div>
                      </div>
                  </b-tab>
                  <b-tab
                      :disabled="!unit_id"
                      :title="$t('general.ImageUploads')"
                  >
                      <div class="row">
                          <input
                              accept="image/png, image/gif, image/jpeg, image/jpg"
                              type="file"
                              id="uploadImageCreate"
                              @change.prevent="onImageChanged"
                              class="input-file-upload position-absolute"
                              :class="['d-none']"
                          />
                          <div class="col-md-8 my-1">
                              <!-- file upload -->
                              <div
                                  class="row align-content-between"
                                  style="width: 100%; height: 100%"
                              >
                                  <div class="col-12">
                                      <div class="d-flex flex-wrap">
                                          <div
                                              :class="[
                                  'dropzone-previews col-4 position-relative mb-2',
                                ]"
                                              v-for="(photo, index) in images"
                                              :key="photo.id"
                                          >
                                              <div
                                                  :class="[
                                    'card mb-0 shadow-none border',
                                    images.length - 1 == index
                                      ? 'bg-primary'
                                      : '',
                                  ]"
                                              >
                                                  <div class="p-2">
                                                      <div class="row align-items-center">
                                                          <div
                                                              class="col-auto"
                                                              @click="
                                          showPhoto = photo.url;
                                          mime_type = photo.mime_type;
                                        "
                                                          >
                                                              <template
                                                                  v-if="
                                            !photo.mime_type.includes('video')
                                          "
                                                              >
                                                                  <img
                                                                      data-dz-thumbnail
                                                                      :src="photo.url"
                                                                      class="avatar-sm rounded bg-light"
                                                                      @error="
                                              src =
                                                '../../../assets/images/video.jpg'
                                            "
                                                                  />
                                                              </template>
                                                              <template v-else>
                                                                  <img
                                                                      data-dz-thumbnail
                                                                      src="../../../assets/images/video.jpg"
                                                                      class="avatar-sm rounded bg-light"
                                                                      @error="
                                              src =
                                                '../../../assets/images/video.jpg'
                                            "
                                                                  />
                                                              </template>
                                                          </div>
                                                          <div class="col pl-0">
                                                              <a
                                                                  href="javascript:void(0);"
                                                                  :class="[
                                            'font-weight-bold',
                                            images.length - 1 == index
                                              ? 'text-white'
                                              : 'text-muted',
                                          ]"
                                                                  data-dz-name
                                                              >
                                                                  {{ photo.name }}
                                                              </a>
                                                          </div>
                                                          <!-- Button -->
                                                          <a
                                                              href="javascript:void(0);"
                                                              :class="[
                                          'btn-danger dropzone-close',
                                          $i18n.locale == 'ar'
                                            ? 'dropzone-close-rtl'
                                            : '',
                                        ]"
                                                              data-dz-remove
                                                              @click.prevent="
                                          deleteImageCreate(photo.id, index)
                                        "
                                                          >
                                                              <i class="fe-x"></i>
                                                          </a>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                                  <div class="footer-image col-12">
                                      <b-button
                                          @click="changePhoto"
                                          variant="success"
                                          type="button"
                                          class="mx-1 font-weight-bold px-3"
                                          v-if="!isLoader"
                                      >
                                          {{ $t("general.Add") }}
                                      </b-button>
                                      <b-button
                                          variant="success"
                                          class="mx-1"
                                          disabled
                                          v-else
                                      >
                                          <b-spinner small></b-spinner>
                                          <span class="sr-only"
                                          >{{ $t("login.Loading") }}...</span
                                          >
                                      </b-button>
                                  </div>
                              </div>
                          </div>
                          <div class="col-md-4">
                              <div
                                  class="show-dropzone"
                                  v-if="!mime_type.includes('video')"
                              >
                                  <img
                                      :src="showPhoto"
                                      class="img-thumbnail"
                                      @error="src = './images/img-1.png'"
                                  />
                              </div>
                              <div class="show-dropzone" v-else>
                                  <video width="320" height="240" controls autoplay>
                                      <source :src="showPhoto" :type="mime_type" />
                                      <source :src="showPhoto" type="video/mp4" />
                                      <source :src="showPhoto" type="video/ogg" />
                                  </video>
                              </div>
                          </div>
                      </div>
                  </b-tab>
              </b-tabs>
          </form>
      </b-modal>
    <!--  /create   -->
  </div>
</template>
<style scoped>
.dropdown-menu-custom-company.dropdown .dropdown-menu {
  padding: 5px 10px !important;
  overflow-y: scroll;
  height: 300px;
}

.nav-bordered {
  border: unset !important;
}

.nav {
  background-color: #dff0fe;
}

.tab-content {
  padding: 70px 60px 40px;
  min-height: 300px;
  background-color: #f5f5f5;
  position: relative;
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
.img-thumbnail {
  max-height: 400px !important;
}
form {
    position: relative;
}
</style>




