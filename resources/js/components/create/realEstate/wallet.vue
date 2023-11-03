<template>
  <div>
    <!--  create   -->
    <div>
      <Owner
        :companyKeys="companyKeys"
        :defaultsKeys="defaultsKeys"
        @created="getOwner"
        :id="'owner-create-wallet'"
        :isPage="false"
        type="create"
        :isPermission="isPermission"
      />
      <Building
        :companyKeys="companyKeys"
        :defaultsKeys="defaultsKeys"
        @created="getBuildings"
        :id="'building-create-owner'"
        :isPage="false"
        type="create"
        :isPermission="isPermission"
      />
      <!--  create   -->
      <b-modal
        :id="id"
        :title="
          type != 'edit'
            ? getCompanyKey('wallet_create_form')
            : getCompanyKey('wallet_edit_form')
        "
        title-class="font-18"
        body-class="p-4 "
        size="lg"
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
                type="submit"
                class="mx-1"
                v-if="!isLoader"
                @click.prevent="AddSubmit"
              >
                {{ type != "edit" ? $t("general.Add") : $t("general.edit") }}
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
          <b-tabs nav-class="nav-tabs nav-bordered">
            <b-tab :title="$t('general.DataEntry')" active>
              <div class="row justify-content-center">
                <div class="col-md-10" v-if="isVisible('name')">
                  <div class="form-group">
                    <label for="field-1" class="control-label">
                      {{ getCompanyKey("wallet_name_ar") }}
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
                          'is-invalid': $v.create.name.$error || errors.name,
                          'is-valid': !$v.create.name.$invalid && !errors.name,
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
                      >
                        {{ errorMessage }}
                      </ErrorMessage>
                    </template>
                  </div>
                </div>
                <div class="col-md-10" v-if="isVisible('name_e')">
                  <div class="form-group">
                    <label for="field-2" class="control-label">
                      {{ getCompanyKey("wallet_name_en") }}
                      <span v-if="isRequired('name_e')" class="text-danger"
                        >*</span
                      >
                    </label>
                    <div>
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
                        >{{ errorMessage }}
                      </ErrorMessage>
                    </template>
                  </div>
                </div>
              </div>
            </b-tab>
            <b-tab :disabled="!wallet_id" :title="$t('general.owner')">
              <div class="d-flex justify-content-end">
                <template v-if="!is_disabledOwner">
                  <b-button
                    variant="success"
                    type="button"
                    class="mx-1"
                    v-if="!isLoader"
                    @click.prevent="AddSubmitOwber"
                  >
                    {{ $t("general.Add") }}
                  </b-button>

                  <b-button variant="success" class="mx-1" disabled v-else>
                    <b-spinner small></b-spinner>
                    <span class="sr-only">{{ $t("login.Loading") }}...</span>
                  </b-button>
                </template>
                <!-- Emulate built in modal footer ok and cancel button actions -->
                <b-button
                  variant="success"
                  type="button"
                  disabled
                  class="mx-1"
                  v-if="is_disabledOwner"
                >
                  {{ $t("general.Add") }}
                </b-button>
              </div>
              <template v-for="(item, index) in createOwner.wallet_owners">
                <div class="row" :key="index">
                  <div class="col-md-5">
                    <div class="form-group">
                      <label>{{ getCompanyKey("owner") }}</label>
                      <multiselect
                        @input="showOwnerModal(index)"
                        v-model="
                          $v.createOwner.wallet_owners.$each[index].owner_id
                            .$model
                        "
                        :options="owners.map((type) => type.id)"
                        :custom-label="
                          (opt) =>
                            owners.find((x) => x.id == opt)
                              ? $i18n.locale == 'ar'
                                ? owners.find((x) => x.id == opt).name
                                : owners.find((x) => x.id == opt).name_e
                              : null
                        "
                      >
                      </multiselect>
                      <div
                        v-if="
                          $v.createOwner.wallet_owners.$each[index].owner_id
                            .$error
                        "
                        class="text-danger"
                      >
                        {{ $t("general.fieldIsRequired") }}
                      </div>
                      <template v-if="errors.owner_id">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.owner_id"
                          :key="index"
                          >{{ errorMessage }}</ErrorMessage
                        >
                      </template>
                    </div>
                  </div>
                  <div class="col-md-5">
                    <div class="form-group">
                      <label class="control-label">
                        {{ getCompanyKey("wallet_owner_percentage") }}
                        <span class="text-danger">*</span>
                      </label>
                      <input
                        type="number"
                        step="0.01"
                        class="form-control"
                        data-create="3"
                        @input="changeNumber('add')"
                        v-model="
                          $v.createOwner.wallet_owners.$each[index].percentage
                            .$model
                        "
                        :class="{
                          'is-invalid':
                            $v.createOwner.wallet_owners.$each[index].percentage
                              .$error ||
                            errors.percentage ||
                            !is_persentage,
                          'is-valid':
                            !$v.createOwner.wallet_owners.$each[index]
                              .percentage.$invalid &&
                            !errors.percentage &&
                            is_persentage,
                        }"
                      />
                      <template v-if="errors.percentage">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.percentage"
                          :key="index"
                          >{{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
                  <div class="col-md-2 p-0 pt-3">
                    <button
                      v-if="createOwner.wallet_owners.length - 1 == index"
                      type="button"
                      @click.prevent="addNewField"
                      class="custom-btn-dowonload"
                    >
                      <i class="fas fa-plus"></i>
                    </button>
                    <button
                      v-if="createOwner.wallet_owners.length > 1"
                      type="button"
                      @click.prevent="removeNewField(index)"
                      class="custom-btn-dowonload"
                    >
                      <i class="fas fa-trash-alt"></i>
                    </button>
                  </div>
                </div>
              </template>
            </b-tab>
            <b-tab :title="$t('general.Building')" :id="'tab-building'">
              <div class="d-flex justify-content-end">
                <template v-if="!is_disabledBuilding">
                  <b-button
                    variant="success"
                    type="submit"
                    class="mx-1"
                    v-if="!isLoader"
                    @click.prevent="AddSubmitBuilding"
                  >
                    {{ $t("general.Add") }}
                  </b-button>

                  <b-button variant="success" class="mx-1" disabled v-else>
                    <b-spinner small></b-spinner>
                    <span class="sr-only">{{ $t("login.Loading") }}...</span>
                  </b-button>
                </template>
                <b-button
                  variant="success"
                  type="button"
                  disabled
                  class="mx-1"
                  v-if="is_disabledBuilding"
                >
                  {{ $t("general.Add") }}
                </b-button>
              </div>
              <template v-for="(item, index) in createBuilding">
                <div class="row" :key="index">
                  <div class="col-md-6 float-md-left">
                    <div class="form-group">
                      <label class="my-1 mr-2">{{
                        getCompanyKey("building")
                      }}</label>
                      <multiselect
                        @input="showBuildingModal"
                        v-model="createBuilding.building_id"
                        :options="buildings.map((type) => type.id)"
                        :custom-label="
                          (opt) =>
                            buildings.find((x) => x.id == opt)
                              ? $i18n.locale == 'ar'
                                ? buildings.find((x) => x.id == opt).name
                                : buildings.find((x) => x.id == opt).name_e
                              : null
                        "
                        :class="{
                          'is-invalid':
                            $v.createBuilding.building_id.$error ||
                            errors.building_id,
                        }"
                      ></multiselect>
                      <div
                        v-if="!$v.createBuilding.building_id.required"
                        class="invalid-feedback"
                      >
                        {{ $t("general.fieldIsRequired") }}
                      </div>

                      <template v-if="errors.building_id">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.building_id"
                          :key="index"
                          >{{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
                  <div class="col-md-2 p-0 pt-3">
                    <button
                      v-if="createBuilding.length - 1 == index"
                      type="button"
                      @click.prevent="addNewBuilding"
                      class="custom-btn-dowonload"
                    >
                      <i class="fas fa-plus"></i>
                    </button>
                    <button
                      v-if="createOwner.length > 1"
                      type="button"
                      @click.prevent="removeNewBuilding(index)"
                      class="custom-btn-dowonload"
                    >
                      <i class="fas fa-trash-alt"></i>
                    </button>
                  </div>
                </div>
              </template>
            </b-tab>
          </b-tabs>
        </form>
      </b-modal>
      <!--  /create   -->
    </div>
    <!--  /create   -->
  </div>
</template>

<script>
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import {
  decimal,
  maxLength,
  minLength,
  minValue,
  required,
  requiredIf,
} from "vuelidate/lib/validators";
import adminApi from "../../../api/adminAxios";
import Swal from "sweetalert2";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import { arabicValue, englishValue } from "../../../helper/langTransform";
import successError from "../../../helper/mixin/success&error";
import Owner from "./owner";
import Building from "./building";
import Multiselect from "vue-multiselect";

export default {
  name: "wallet",
  components: {
    ErrorMessage,
    loader,
    Owner,
    Building,
    Multiselect,
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
    url: { default: "/real-estate/wallets" },
  },
  mounted() {
    this.company_id = this.$store.getters["auth/company_id"];
  },
  data() {
    return {
      isCustom: false,
      fields: [],
      tab: "",
      parents: [],
      isLoader: false,
      create: {
        name: "",
        name_e: "",
      },
      createOwner: {
        wallet_owners: [
          {
            owner_id: null,
            percentage: 0,
          },
        ],
      },
      createBuilding: {
        wallet_id: null,
        building_id: null,
      },
      wallet_id: null,
      errors: {},
      owners: [],
      buildings: [],
      is_disabled: false,
      is_disabledOwner: false,
      is_disabledBuilding: false,
    };
  },
  validations: {
    create: {
      name: {
        required: requiredIf(function (model) {
          return this.isRequired("name");
        }),
        minLength: minLength(3),
        maxLength: maxLength(100),
      },
      name_e: {
        required: requiredIf(function (model) {
          return this.isRequired("name_e");
        }),
        minLength: minLength(3),
        maxLength: maxLength(100),
      },
    },
    createOwner: {
      wallet_id: { required },
      wallet_owners: {
        required,
        $each: {
          owner_id: { required },
          percentage: { required, decimal, minValue: minValue(0.01) },
        },
      },
    },
    createBuilding: {
      building_id: { required },
      wallet_id: { required },
    },
  },
  methods: {
    addNewField() {
      this.createOwner.wallet_owners.push({
        owner_id: null,
        percentage: 0,
      });
    },
    removeNewField(index) {
      if (this.createOwner.wallet_owners.length > 1) {
        this.createOwner.wallet_owners.splice(index, 1);
        let totel = this.createOwner.wallet_owners.reduce(
          (accumulator, curValue) =>
            parseFloat(accumulator) + parseFloat(curValue.percentage),
          0
        );
        this.is_persentage = totel == 100 ? true : false;
      }
    },
    addNewBuilding() {
      this.createBuilding.building_id.push({
        building_id: null,
      });
    },
    removeNewBuilding(index) {
      if (this.createBuilding.building_id.length > 1) {
        this.createBuilding.building_id.splice(index, 1);
      }
    },

    getOwner() {
      this.isLoader = true;

      adminApi
        .get(`/real-estate/owners`)
        .then((res) => {
          let l = res.data.data;
          if (this.isPermission("create owner RealState")) {
            l.unshift({ id: 0, name: "اضف مالك  ", name_e: "Add Owner" });
          }
          this.owners = l;
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
    getBuildings() {
      this.isLoader = true;
      adminApi
        .get(`/real-estate/buildings`)
        .then((res) => {
          let l = res.data.data;
          if (this.isPermission("create building RealState")) {
            l.unshift({ id: 0, name: "اضافة مبنى", name_e: "Add building" });
          }
          this.buildings = l;
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
    showOwnerModal(index) {
      if (this.createOwner.wallet_owners[index].owner_id == 0) {
        console.log(this.createOwner.wallet_owners[index].owner_id);
        this.$bvModal.show("owner-create-wallet");
        this.createOwner.wallet_owners[index].owner_id = null;
      }
    },
    showBuildingModal() {
      if (this.createBuilding.building_id == 0) {
        this.$bvModal.show("building-create-owner");
        this.createBuilding.building_id = null;
      }
    },
    changeNumber(add) {
      let totel = this.createOwner.wallet_owners.reduce(
        (accumulator, curValue) =>
          parseFloat(accumulator) + parseFloat(curValue.percentage),
        0
      );
      this.is_persentage = totel == 100 ? true : false;
    },
    async getCustomTableFields() {
      this.isCustom = true;
      await adminApi
        .get(`/customTable/table-columns/rlst_wallets`)
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
    isVisible(fieldName) {
      if (!this.isPage) {
        let res = this.fields.filter((field) => {
          return field.column_name == fieldName;
        });
        return res.length > 0 && res[0].is_visible == 1 ? true : false;
      } else {
        return this.isVisiblePage(fieldName);
      }
    },
    isRequired(fieldName) {
      if (!this.isPage) {
        let res = this.fields.filter((field) => {
          return field.column_name == fieldName;
        });
        return res.length > 0 && res[0].is_required == 1 ? true : false;
      } else {
        return this.isRequiredPage(fieldName);
      }
    },
    defaultData(edit = null) {
      this.create = { name: "", name_e: "" };
      this.createOwner = { wallet_owners: [{ owner_id: null, percentage: 0 }] };
      this.createBuilding = {
        wallet_id: null,
        building_id: null,
        bu_ty: "active",
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.is_persentage = true;
      this.errors = {};
      this.wallet_id = null;
      this.is_disabled = false;
      this.is_disabledBuilding = false;
      this.is_disabledOwner = false;
    },
    resetModalHidden() {
      this.defaultData();
      this.$bvModal.hide(this.id);
    },
    resetModal() {
      this.defaultData();
      setTimeout(async () => {
        if (this.type != "edit") {
          if (!this.isPage) await this.getCustomTableFields();
          this.getOwner();
          this.getBuildings();
        } else {
          if (this.idObjEdit.dataObj) {
            let module = this.idObjEdit.dataObj;
            this.getOwner();
            this.getBuildings();
            this.wallet_id = module.id;
            this.is_persentage = true;
            this.create.name = module.name;
            this.create.name_e = module.name_e;
            if (module.owners && module.owners.length) {
              module.owners.forEach((e) => {
                adminApi
                  .get(
                    `/real-estate/owners/ownerWalletPercentage/${this.wallet_id}/${e.id}`
                  )
                  .then((res) => {
                    this.createOwner.wallet_owners.push({
                      owner_id: e.id,
                      percentage: res.data.data.percentage,
                    });
                  })
                  .catch((err) => {
                    Swal.fire({
                      icon: "error",
                      title: `${this.$t("general.Error")}`,
                      text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                  })
                  .finally(() => {});
              });
            } else {
              this.createOwner.wallet_owners.push({
                owner_id: null,
                percentage: 0,
              });
            }
            this.createBuilding.building_id = module.buildings.length
              ? module.buildings[0].id
              : null;
            adminApi
              .get(
                `/real-estate/wallets/bu-ty/${this.wallet_id}/${this.createBuilding.building_id}`
              )
              .then((res) => {
                this.createBuilding.bu_ty =
                  res.data.data.bu_ty == 1 ? "inactive" : "active";
              })
              .catch((err) => {
                Swal.fire({
                  icon: "error",
                  title: `${this.$t("general.Error")}`,
                  text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                });
              })
              .finally(() => {});

            this.createBuilding.wallet_id = this.wallet_id;
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
      this.$v.create.$touch();

      if (this.$v.create.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        if (this.type != "edit") {
          adminApi
            .post(this.url, { ...this.create, company_id: this.company_id })
            .then((res) => {
              this.is_disabled = true;
              this.wallet_id = res.data.data.id;
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
    arabicValue(txt) {
      this.create.name = arabicValue(txt);
    },
    englishValue(txt) {
      this.create.name_e = englishValue(txt);
    },
    AddSubmitOwber() {
      this.$v.createOwner.$touch();

      let totel = this.createOwner.wallet_owners.reduce(
        (accumulator, curValue) =>
          parseFloat(accumulator) + parseFloat(curValue.percentage),
        0
      );
      this.is_persentage = totel == 100;

      if (this.$v.createOwner.$invalid && !this.is_persentage) {
        return;
      } else {
        this.isLoader = true;
        this.createOwner.wallet_owners.map(
          (e) => (e.wallet_id = this.wallet_id)
        );
        this.errors = {};
        if (this.type != "edit") {
          adminApi
            .post(`/real-estate/wallet-owner`, {
              "wallet-owner": this.createOwner.wallet_owners,
            })
            .then((res) => {
              this.is_disabledOwner = true;
              if (!this.isPage) return;
              else this.$emit("getDataTable");
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
        } else {
          adminApi
            .put(`/real-estate/wallet-owner/${this.wallet_id}`, {
              "wallet-owner": this.createOwner.wallet_owners,
            })
            .then((res) => {
              if (!this.isPage) return;
              else this.$emit("getDataTable");
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
      }
    },
    AddSubmitBuilding() {
      this.createBuilding.wallet_id = this.wallet_id;
      if (this.$v.createBuilding.$invalid) {
        this.$v.createBuilding.$touch();
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        if (this.type != "edit") {
          adminApi
            .post(`/real-estate/building-wallet`, {
              ...this.createBuilding,
              bu_ty: this.createBuilding.bu_ty == "active" ? 1 : 2,
            })
            .then((res) => {
              if (!this.isPage) return;
              else this.$emit("getDataTable");
              this.is_disabledBuilding = true;
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
        } else {
          adminApi
            .put(`/real-estate/building-wallet/${this.wallet_id}`, {
              ...this.createBuilding,
              bu_ty: this.createBuilding.bu_ty == "active" ? 1 : 2,
            })
            .then((res) => {
              if (!this.isPage) return;
              else this.$emit("getDataTable");
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
      }
    },
  },
};
</script>

<style scoped>
form {
  position: relative;
}
</style>
