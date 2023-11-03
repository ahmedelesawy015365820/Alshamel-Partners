<template>
    <div>
        <Priority
            :id="'priority-create-location'"
            :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getPriority"
            :isPage="false" type="create" :isPermission="isPermission"
        />
        <!--  create   -->
        <b-modal
            :id="id"
            :title="type != 'edit'?getCompanyKey('location_create_form'):getCompanyKey('location_edit_form')"
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
                    <!-- Emulate built in modal footer ok and cancel button actions -->
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
                        variant="danger"
                        type="button"
                        @click.prevent="resetModalHidden"
                    >
                        {{ $t("general.Cancel") }}
                    </b-button>
                </div>
                <div class="row">
                    <div v-if="isVisible('parent_id')" class="col-6" :class="$i18n.locale == 'ar' ? 'rtl' : 'ltr'">
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
                              @click="setCreateParentId(node)"
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
                                  @click="setCreateParentId(childNode)"
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
                                      @click="setCreateParentId(child)"
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
                                          @click="setCreateParentId(_child)"
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
                            <div class="col-12 direction" v-if="isVisible('name')" dir="rtl">
                                <div class="form-group">
                                    <label for="field-1" class="control-label">
                                        {{ getCompanyKey("location_name_ar") }}
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
                            <div class="col-12 direction-ltr" v-if="isVisible('name_e')" dir="ltr">
                                <div class="form-group">
                                    <label for="field-2" class="control-label">
                                        {{ getCompanyKey("location_name_en") }}
                                        <span v-if="isRequired('name_e')"  class="text-danger">*</span>
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
                            <div class="col-md-12" v-if="isVisible('priority_id')">
                                <div class="form-group">
                                    <label class="my-1">{{ getCompanyKey("location_priority_id") }}</label>
                                    <span v-if="isRequired('priority_id')"  class="text-danger">*</span>
                                    <multiselect
                                        @input="showPriorityModal"
                                        v-model="$v.create.priority_id.$model"
                                        :options="priorities.map((type) => type.id)"
                                        :custom-label="(opt) => priorities.find((x) => x.id == opt)?
                                            $i18n.locale == 'ar'? priorities.find((x) => x.id == opt).name:priorities.find((x) => x.id == opt).name_e
                                            :null
                                        "
                                        :class="{
                                      'is-invalid': $v.create.priority_id.$error,
                                    }"
                                    >
                                    </multiselect>
                                    <template v-if="errors.priority_id">
                                        <ErrorMessage
                                            v-for="(errorMessage, index) in errors.priority_id"
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
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import {maxLength, minLength, requiredIf} from "vuelidate/lib/validators";
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import Multiselect from "vue-multiselect";
import adminApi from "../../../api/adminAxios";
import successError from "../../../helper/mixin/success&error";
import Priority from "./Priority";
import {arabicValue, englishValue} from "../../../helper/langTransform";

let self;

export default {
    name: "location",
    data() {
        return {
            isCustom: false,
            priorities: [],
            fields: [],
            enabled3: true,
            isLoader: false,
            rootNodes: [],
            childNodes: [],
            current_id: null,
            create: {
                name: "",
                name_e: "",
                parent_id: null,
                priority_id: null
            },
            is_disabled: false,
            company_id: null,
            errors: {}
        };
    },
    mixins: [transMixinComp,successError],
    components: {
        Multiselect,
        ErrorMessage,
        loader,
        Priority,
    },
    props: {
        id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/locations'}

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
            priority_id: { required: requiredIf(function (model) {
                    return this.isRequired("priority_id");
                })  },
        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
        self = this;
    },
    methods: {
        arabicValue(txt){
            this.create.name = arabicValue(txt);
        } ,
        englishValue(txt){
            this.create.name_e = englishValue(txt);
        } ,
        async getCustomTableFields() {
            this.isCustom = true;
            await adminApi
                .get(`/customTable/table-columns/general_locations`)
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
            this.create = { name: "", name_e: "", parent_id: null , priority_id: null};
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
            setTimeout( async  () => {
                if(this.type != 'edit'){
                    if(!this.isPage)  await this.getCustomTableFields();
                    if(this.isVisible("parent_id")) this.getRootNodes();
                    if(this.isVisible("priority_id")) this.getPriority();
                }else {
                    if(this.idObjEdit.dataObj){
                        let propertyTree = this.idObjEdit.dataObj;
                        this.errors = {};
                        this.create.name = propertyTree.name;
                        this.create.name_e = propertyTree.name_e;
                        if(this.isVisible("parent_id")) this.getRootNodes();
                        this.create.parent_id = propertyTree.parent_id;
                        if(this.isVisible("priority_id")) this.getPriority();
                        this.create.priority_id = propertyTree.priority_id;
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
                if(this.type != 'edit') {
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
                .get(`/locations/root-nodes`)
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
                    .get(`/locations/child-nodes/${node.id}`)
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
                    .get(`/locations/child-nodes/${parentNode.id}`)
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
                    .get(`/locations/child-nodes/${secondParentNode.id}`)
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
                    .get(`/locations/child-nodes/${thirdParentNode.id}`)
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
        showPriorityModal() {
            if (this.create.priority_id == 0) {
                this.$bvModal.show("priority-create-location");
                this.create.priority_id = null;
            }
        },
         getPriority() {
            this.isLoader = true;

             adminApi
                .get(`/priorities/get-drop-down`)
                .then((res) => {
                    let l = res.data.data;
                    if(this.isPermission('create Priority')){
                        l.unshift({ id: 0, name: "اضافه اولويه", name_e: "Add Priority" });
                    }
                    this.priorities = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
    }
}
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
</style>
