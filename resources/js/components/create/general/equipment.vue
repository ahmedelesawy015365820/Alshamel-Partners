<template>
   <div>
       <Location
           :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getLocation"
           :isPage="false" type="create" :isPermission="isPermission"
           id="location-create-equipment"
       />
       <PeriodicMaintenance
           :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
           :isPage="false" type="create" :isPermission="isPermission"
           @created="getPeriodic_maintenance"
           id="periodic-maintenance-create-equipment"
       />

       <!--  create   -->
       <b-modal
           :id="id"
           :title="type != 'edit'?getCompanyKey('equipment_create_form'):getCompanyKey('equipment_edit_form')"
           title-class="font-18"
           body-class="p-4 "
           :hide-footer="true"
           size="lg"
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
                           type="submit"
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
                   <b-button
                       @click.prevent="resetModalHidden"
                       variant="danger"
                       type="button"
                   >
                       {{ $t("general.Cancel") }}
                   </b-button>
               </div>
               <div class="row justify-content-between">
                   <div v-if="isVisible('parent_id')" class="col-4" style="background-color: #f5f5f5" :class="$i18n.locale == 'ar' ? 'rtl' : 'ltr'">
                       <h4 class="text-center">{{ $t('general.equipments') }}</h4>
                       <ul id="myUL">
                           <li v-for="node in rootNodes" :key="node.id">
                        <span>
                          <i
                              @click="getFirstLevelChildNodes(node)"
                              v-if="node.haveChildren"
                              :class="
                              node.collapsed
                                ? 'fa fa-caret-down'
                                : $i18n.locale == 'ar'
                                ? 'fa fa-caret-left'
                                : 'fa fa-caret-right'
                            "
                          ></i>
                          <span
                              @click="type == 'craete'?setCreateParentId(node):setUpdateParentId([], node)"
                              :class="{
                              'without-children': !node.haveChildren,
                              active: node.id == create.parent_id,
                            }"
                          >
                            {{ $i18n.locale == "ar" ? node.name : node.name_e }}
                          </span>
                        </span>
                               <ul v-if="node.children && node.children.length" class="nested">
                                   <li v-for="childNode in node.children" :key="childNode.id">
                            <span>
                              <i
                                  @click="getSecondLevelChildNodes(node, childNode)"
                                  v-if="childNode.haveChildren"
                                  :class="
                                  childNode.collapsed
                                    ? 'fa fa-caret-down'
                                    : $i18n.locale == 'ar'
                                    ? 'fa fa-caret-left'
                                    : 'fa fa-caret-right'
                                "
                              >
                              </i>
                              <span
                                  @click="type == 'create'?setCreateParentId(childNode):setUpdateParentId([node.id], childNode)"
                                  :class="{
                                  'without-children': !childNode.haveChildren,
                                  active: childNode.id == create.parent_id,
                                }"
                              >
                                {{
                                      $i18n.locale == "ar" ? childNode.name : childNode.name_e
                                  }}
                              </span>
                            </span>
                                       <ul
                                           v-if="childNode.children && childNode.children.length"
                                           class="nested"
                                       >
                                           <li v-for="child in childNode.children" :key="child.id">
                                <span>
                                  <i
                                      @click="
                                      getThirdLevelChildNodes(node, childNode, child)
                                    "
                                      v-if="child.haveChildren"
                                      :class="
                                      child.collapsed
                                        ? 'fa fa-caret-down'
                                        : $i18n.locale == 'ar'
                                        ? 'fa fa-caret-left'
                                        : 'fa fa-caret-right'
                                    "
                                  >
                                  </i>
                                  <span
                                      @click="type  == 'create'?
                                            setCreateParentId(child):
                                            setUpdateParentId([node.id, childNode.id],child)
                                        "
                                      :class="{
                                      'without-children': !child.haveChildren,
                                      active: child.id == create.parent_id,
                                    }"
                                  >
                                    {{ $i18n.locale == "ar" ? child.name : child.name_e }}
                                  </span>
                                </span>
                                               <ul
                                                   v-if="child.children && child.children.length"
                                                   class="nested"
                                               >
                                                   <li v-for="_child in child.children" :key="_child.id">
                                    <span>
                                      <i
                                          @click="
                                          getFourthLevelChildNodes(
                                            node,
                                            childNode,
                                            child,
                                            _child
                                          )
                                        "
                                          v-if="_child.haveChildren"
                                          :class="
                                          _child.collapsed
                                            ? 'fa fa-caret-down'
                                            : $i18n.locale == 'ar'
                                            ? 'fa fa-caret-left'
                                            : 'fa fa-caret-right'
                                        "
                                      >
                                      </i>
                                      <span
                                          @click="type == 'create' ?
                                               setCreateParentId(_child):
                                               setUpdateParentId([node.id, childNode.id, child.id],_child)
                                            "
                                          :class="{
                                          'without-children': !_child.haveChildren,
                                          active: _child.id == create.parent_id,
                                        }"
                                      >
                                        {{
                                              $i18n.locale == "ar"
                                                  ? _child.name
                                                  : _child.name_e
                                          }}
                                      </span>
                                    </span>
                                                       <ul
                                                           v-if="_child.children && _child.children.length"
                                                           class="nested"
                                                       >
                                                           <li
                                                               v-for="__child in _child.children"
                                                               :key="__child.id"
                                                           >
                                                               {{
                                                                   $i18n.locale == "ar"
                                                                       ? __child.name
                                                                       : __child.name_e
                                                               }}
                                                           </li>
                                                       </ul>
                                                   </li>
                                               </ul>
                                           </li>
                                       </ul>
                                   </li>
                               </ul>
                           </li>
                       </ul>
                   </div>
                   <div :class="[isVisible('parent_id')?'col-6':'col-12']">
                       <div class="row">
                           <div class="col-12 direction" dir="rtl" v-if="isVisible('name')">
                               <div class="form-group">
                                   <label for="field-1" class="control-label">
                                       {{ getCompanyKey("equipment_name_ar") }}
                                       <span v-if="isRequired('name')" class="text-danger">*</span>
                                   </label>
                                   <input
                                       type="text"
                                       class="form-control arabicInput"
                                       v-model="$v.create.name.$model"
                                       :class="{
                              'is-invalid': $v.create.name.$error || errors.name,
                              'is-valid': !$v.create.name.$invalid && !errors.name,
                            }"
                                       @keyup="arabicValue(create.name)"
                                       id="field-1"
                                   />
                                   <div v-if="!$v.create.name.minLength" class="invalid-feedback">
                                       {{ $t("general.Itmustbeatleast") }}
                                       {{ $v.create.name.$params.minLength.min }}
                                       {{ $t("general.letters") }}
                                   </div>
                                   <div v-if="!$v.create.name.maxLength" class="invalid-feedback">
                                       {{ $t("general.Itmustbeatmost") }}
                                       {{ $v.create.name.$params.maxLength.max }}
                                       {{ $t("general.letters") }}
                                   </div>
                                   <template v-if="errors.name">
                                       <ErrorMessage
                                           v-for="(errorMessage, index) in errors.name"
                                           :key="index"
                                       >{{ $t(errorMessage) }}</ErrorMessage
                                       >
                                   </template>
                               </div>
                           </div>
                           <div class="col-12 direction-ltr" dir="ltr" v-if="isVisible('name_e')">
                               <div class="form-group">
                                   <label for="field-2" class="control-label">
                                       {{ getCompanyKey("equipment_name_en") }}
                                       <span v-if="isRequired('name_e')" class="text-danger">*</span>
                                   </label>
                                   <input
                                       type="text"
                                       class="form-control englishInput"
                                       v-model="$v.create.name_e.$model"
                                       :class="{
                              'is-invalid': $v.create.name_e.$error || errors.name_e,
                              'is-valid': !$v.create.name_e.$invalid && !errors.name_e,
                            }"
                                       @keyup="englishValue(create.name_e)"
                                       id="field-2"
                                   />
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
                                       >{{ $t(errorMessage) }}</ErrorMessage
                                       >
                                   </template>
                               </div>
                           </div>
                           <div class="col-md-12" v-if="isVisible('location_id')">
                               <div class="form-group">
                                   <label class="my-1">{{ getCompanyKey("equipment_location") }}</label>
                                   <span v-if="isRequired('location_id')"  class="text-danger">*</span>
                                   <multiselect
                                       @input="showLocationModal"
                                       v-model="$v.create.location_id.$model"
                                       :options="locations.map((type) => type.id)"
                                       :custom-label="(opt) => locations.find((x) => x.id == opt)?locations.find((x) => x.id == opt).name : null"
                                       :class="{
                                      'is-invalid': $v.create.location_id.$error,
                                    }"
                                   >
                                   </multiselect>
                                   <template v-if="errors.location_id">
                                       <ErrorMessage
                                           v-for="(errorMessage, index) in errors.location_id"
                                           :key="index"
                                       >{{ errorMessage }}</ErrorMessage
                                       >
                                   </template>
                               </div>
                           </div>
                           <div class="col-md-12" v-if="isVisible('periodic_maintenance_id')">
                               <div class="form-group">
                                   <label class="my-1">{{ getCompanyKey("equipment_periodic_maintenance") }}</label>
                                   <span v-if="isRequired('periodic_maintenance_id')"  class="text-danger">*</span>
                                   <multiselect
                                       @input="showPeriodic_maintenanceModal"
                                       v-model="$v.create.periodic_maintenance_id.$model"
                                       :options="periodic_maintenances.map((type) => type.id)"
                                       :custom-label="(opt) => periodic_maintenances.find((x) => x.id == opt)?$i18n.locale == 'ar' ? periodic_maintenances.find((x) => x.id == opt).name:periodic_maintenances.find((x) => x.id == opt).name_e: null"
                                       :class="{
                                      'is-invalid': $v.create.periodic_maintenance_id.$error,
                                    }"
                                   >
                                   </multiselect>
                                   <template v-if="errors.periodic_maintenance_id">
                                       <ErrorMessage
                                           v-for="(errorMessage, index) in errors.periodic_maintenance_id"
                                           :key="index"
                                       >{{ errorMessage }}</ErrorMessage
                                       >
                                   </template>
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

<script>
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import Multiselect from "vue-multiselect";
import Location from "./location";
import PeriodicMaintenance from "./periodic_maintenance";
import {maxLength, minLength, required, requiredIf} from "vuelidate/lib/validators";
import adminApi from "../../../api/adminAxios";
import {arabicValue, englishValue} from "../../../helper/langTransform";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import successError from "../../../helper/mixin/success&error";
let self;

export default {
    components: {
        ErrorMessage,
        loader,
        Multiselect,
        Location,
        PeriodicMaintenance
    },
    mixins: [transMixinComp,successError],
    props: {
        id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/equipments'}
    },
    data() {
        return {
            isCustom: false,
            isLoader: false,
            rootNodes: [],
            childNodes: [],
            current_id: null,
            create: {
                name: "",
                name_e: "",
                parent_id: null,
                location_id : null,
                periodic_maintenance_id : null,
            },
            errors: {},
            locations : [],
            is_disabled: false,
            periodic_maintenances: [],
            company_id: null,
        };
    },
    validations: {
        create: {
            name: { required: requiredIf(function (model) {
                    return this.isRequired("name");
                }) , minLength: minLength(3), maxLength: maxLength(100) },
            name_e: {
                required: requiredIf(function (model) {
                    return this.isRequired("name_e");
                }) ,
                minLength: minLength(3),
                maxLength: maxLength(100),
            },
            location_id : { required: requiredIf(function (model) {
                    return this.isRequired("location_id");
                })  },
            periodic_maintenance_id : { required: requiredIf(function (model) {
                    return this.isRequired("periodic_maintenance_id");
                })  },
        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
        self = this;
    },
    methods: {
        async getCustomTableFields() {
            this.isCustom = true;
            await adminApi
                .get(`/customTable/table-columns/general_equipments`)
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
            this.create = { name: "", name_e: "", parent_id: null, location_id: null,periodic_maintenance_id :null };
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
                if(this.type != 'edit'){
                    if(!this.isPage)  await this.getCustomTableFields();
                    if(this.isVisible("parent_id")) this.getRootNodes();
                    if(this.isVisible("location_id")) this.getLocation();
                    if(this.isVisible("periodic_maintenance_id")) this.getPeriodic_maintenance();
                }else {
                    if(this.idObjEdit.dataObj){
                        let propertyTree = this.idObjEdit.dataObj;
                        this.errors = {};
                        this.create.name = propertyTree.name;
                        this.create.name_e = propertyTree.name_e;
                        this.getRootNodes();
                        this.create.parent_id = propertyTree.parent_id;
                        this.getLocation();
                        this.create.location_id = propertyTree.location_id;
                        this.getPeriodic_maintenance();
                        this.create.periodic_maintenance_id  = propertyTree.periodic_maintenance_id;
                        this.errors = {};
                        this.current_id = propertyTree.id;
                    }
                }
            },50);
        },
        resetForm() {
            this.defaultData();
        },
        AddSubmit() {
            if (this.create.name || this.create.name_e) {
                this.create.name = this.create.name ? this.create.name : this.create.name_e;
                this.create.name_e = this.create.name_e ? this.create.name_e : this.create.name;
            }
            this.$v.create.$touch();
            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                this.is_disabled = false;
                if(this.type != 'edit'){
                    adminApi
                        .post(this.url, {
                            ...this.create,
                            company_id: this.company_id,
                        })
                        .then((res) => {
                            this.is_disabled = true;
                            if(!this.isPage)
                                this.$emit("created");
                            else
                                this.$emit("getDataTable");

                            this.getRootNodes();
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
        getRootNodes() {
            adminApi
                .get(`/equipments/root-nodes`)
                .then((res) => {
                    this.rootNodes = res.data;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                });
        },
        getFirstLevelChildNodes(node) {
            if (!node.collapsed) {
                adminApi
                    .get(`/equipments/child-nodes/${node.id}`)
                    .then((res) => {
                        this.rootNodes = this.getUpdatedRootNodes(node, res.data);
                    })
                    .catch((err) => {
                        this.errorFun('Error','Thereisanerrorinthesystem');
                    });
            } else {
                this.rootNodes = this.getUpdatedRootNodes(node);
            }
        },
        getSecondLevelChildNodes(rootNode, parentNode) {
            if (!parentNode.collapsed) {
                adminApi
                    .get(`/equipments/child-nodes/${parentNode.id}`)
                    .then((res) => {
                        this.rootNodes = this.getRootNodesAfterCollapse(
                            rootNode,
                            parentNode,
                            res.data
                        );
                    })
                    .catch((err) => {
                        this.errorFun('Error','Thereisanerrorinthesystem');
                    });
            } else {
                this.rootNodes = this.getRootNodesAfterCollapse(rootNode, parentNode);
            }
        },
        getThirdLevelChildNodes(rootNode, parentNode, secondParentNode) {
            if (!secondParentNode.collapsed) {
                adminApi
                    .get(`/equipments/child-nodes/${secondParentNode.id}`)
                    .then((res) => {
                        this.rootNodes = this.getRootNodesAfter2ndCollapse(
                            rootNode,
                            parentNode,
                            secondParentNode,
                            res.data
                        );
                    })
                    .catch((err) => {
                        this.errorFun('Error','Thereisanerrorinthesystem');
                    });
            } else {
                this.rootNodes = this.getRootNodesAfter2ndCollapse(
                    rootNode,
                    parentNode,
                    secondParentNode
                );
            }
        },
        getFourthLevelChildNodes(rootNode, parentNode, secondParentNode, thirdParentNode) {
            if (!thirdParentNode.collapsed) {
                adminApi
                    .get(`/equipments/child-nodes/${thirdParentNode.id}`)
                    .then((res) => {
                        this.rootNodes = this.getRootNodesAfter3rdCollapse(
                            rootNode,
                            parentNode,
                            secondParentNode,
                            thirdParentNode,
                            res.data
                        );
                    })
                    .catch((err) => {
                        this.errorFun('Error','Thereisanerrorinthesystem');
                    });
            } else {
                this.rootNodes = this.getRootNodesAfter3rdCollapse(
                    rootNode,
                    parentNode,
                    secondParentNode,
                    thirdParentNode
                );
            }
        },
        setCreateParentId(node) {
            if (this.create.parent_id != node.id) {
                this.create.parent_id = node.id;
            } else {
                this.create.parent_id = null;
            }
        },
        setUpdateParentId(parents, node) {
            if (parents.includes(this.current_id)) {
                this.errorFun('Error','Thereisanerrorinthesystem');
                return;
            }
            if (this.current_id == node.id) {
                this.errorFun('Error','cantSelectSelfParent');
                return;
            }
            if (this.create.parent_id != node.id) {
                this.create.parent_id = node.id;
            } else {
                this.create.parent_id = null;
            }
        },
        getUpdatedRootNodes(parentNode, children) {
            let rootNodes = [...this.rootNodes];
            rootNodes.forEach((node, index) => {
                if (node.id == parentNode.id) {
                    if (parentNode.collapsed) {
                        rootNodes[index].children = [];
                        rootNodes[index].collapsed = false;
                    } else {
                        rootNodes[index].children = children;
                        rootNodes[index].collapsed = true;
                    }
                    return;
                }
            });
            return rootNodes;
        },
        getRootNodesAfterCollapse(parentNode, secondParentNode, children) {
            let rootNodes = [...this.rootNodes];
            rootNodes.forEach((_parentNode, parentIndex) => {
                if (_parentNode.id == parentNode.id) {
                    if (_parentNode.children && _parentNode.children.length) {
                        _parentNode.children.forEach((child, index) => {
                            if (child.id == secondParentNode.id) {
                                if (secondParentNode.collapsed) {
                                    rootNodes[parentIndex].children[index].children = [];
                                    rootNodes[parentIndex].children[index].collapsed = false;
                                } else {
                                    rootNodes[parentIndex].children[index].children = children;
                                    rootNodes[parentIndex].children[index].collapsed = true;
                                }
                                return;
                            }
                        });
                        return;
                    }
                }
            });
            return rootNodes;
        },
        getRootNodesAfter2ndCollapse(
            parentNode,
            secondParentNode,
            thirdParentNode,
            children
        ) {
            let rootNodes = [...this.rootNodes];
            rootNodes.forEach((_parentNode, parentIndex) => {
                if (_parentNode.id == parentNode.id) {
                    if (_parentNode.children && _parentNode.children.length) {
                        _parentNode.children.forEach((child, index) => {
                            if (child.id == secondParentNode.id) {
                                child.children.forEach((_child, _index) => {
                                    if (thirdParentNode.id == _child.id) {
                                        if (thirdParentNode.collapsed) {
                                            rootNodes[index].children[parentIndex].children[
                                                _index
                                                ].children = [];
                                            rootNodes[index].children[parentIndex].children[
                                                _index
                                                ].collapsed = false;
                                        } else {
                                            rootNodes[index].children[parentIndex].children[
                                                _index
                                                ].children = children;
                                            rootNodes[index].children[parentIndex].children[
                                                _index
                                                ].collapsed = true;
                                        }
                                        return;
                                    }
                                });
                            }
                        });
                        return;
                    }
                }
            });
            return rootNodes;
        },
        getRootNodesAfter3rdCollapse(
            parentNode,
            secondParentNode,
            thirdParentNode,
            fourthParentNode,
            children
        ) {
            let rootNodes = [...this.rootNodes];
            rootNodes.forEach((_parentNode, parentIndex) => {
                if (_parentNode.id == parentNode.id) {
                    if (_parentNode.children && _parentNode.children.length) {
                        _parentNode.children.forEach((child, index) => {
                            if (child.id == secondParentNode.id) {
                                child.children.forEach((_child, _index) => {
                                    if (thirdParentNode.id == _child.id) {
                                        _child.children.forEach((__child, __index) => {
                                            if (__child.id == fourthParentNode.id) {
                                                if (fourthParentNode.collapsed) {
                                                    rootNodes[index].children[parentIndex].children[
                                                        _index
                                                        ].children[__index].children = [];
                                                    rootNodes[index].children[parentIndex].children[
                                                        _index
                                                        ].children[__index].collapsed = false;
                                                } else {
                                                    rootNodes[index].children[parentIndex].children[
                                                        _index
                                                        ].children[__index].children = children;
                                                    rootNodes[index].children[parentIndex].children[
                                                        _index
                                                        ].children[__index].collapsed = true;
                                                }
                                                return;
                                            }
                                        });
                                    }
                                });
                            }
                        });
                        return;
                    }
                }
            });
            return rootNodes;
        },
        arabicValue(txt){
            this.create.name = arabicValue(txt);
        } ,
        englishValue(txt){
            this.create.name_e = englishValue(txt);
        } ,
        showLocationModal() {
            if (this.create.location_id == 0) {
                this.$bvModal.show("location-create-equipment");
                this.create.location_id = null;
            }
        },
        getLocation() {
            this.isLoader = true;

            adminApi
                .get(`/locations/get-drop-down`)
                .then((res) => {
                    let l = res.data.data;
                    if(this.isPermission('create Location')){
                        l.unshift({ id: 0, name: "اضف موقع", name_e: "Add location" });
                    }
                    this.locations = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        showPeriodic_maintenanceModal() {
            if (this.create.periodic_maintenance_id == 0) {
                this.$bvModal.show("periodic-maintenance-create-equipment");
                this.create.periodic_maintenance_id = null;
            }
        },
        getPeriodic_maintenance() {
            this.isLoader = true;

            adminApi
                .get(`/periodic-maintenances/get-drop-down`)
                .then((res) => {
                    let l = res.data.data;
                    if(this.isPermission('create Periodic Maintenances')){
                        l.unshift({ id: 0, name: "صيانة دورية", name_e: "Add Periodic Maintenances" });
                    }
                    this.periodic_maintenances = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
    },
};
</script>

<style scoped lang="scss">
form {
    position: relative;
}
ul,
#myUL {
    list-style-type: none;
}
#myUL {
    margin: 0;
    padding: 0;
    span {
        i {
            font-size: 20px;
            position: relative;
            top: 3px;
            color: black;
        }
        span:hover,
        i:hover {
            cursor: pointer;
        }
    }
}
.nested {
    display: block;
}
.active {
    color: #1abc9c;
}
.rtl {
    #myUL {
        .without-children {
            padding-right: 10px;
        }
        .nested {
            padding-right: 40px;
        }
    }
}
.ltr {
    #myUL {
        .without-children {
            padding-left: 10px;
        }
    }
}
/* Remove default bullets */

@media print {
    .do-not-print {
        display: none;
    }
    .arrow-sort {
        display: none;
    }
    .text-success{
        background-color:unset;
        color: #6c757d !important;
        border: unset;
    }
    .text-danger{
        background-color:unset;
        color: #6c757d !important;
        border: unset;
    }
}
</style>
