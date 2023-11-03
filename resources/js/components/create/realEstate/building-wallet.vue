<script>
import adminApi from "../../../api/adminAxios";
import { required } from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/general/loader";
import Building from "./building";
import Wallet from "./wallet";
import Multiselect from "vue-multiselect";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import successError from "../../../helper/mixin/success&error";

/**
 * Advanced Table component
 */
export default {
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
    url: { default: "/real-estate/building-wallet" },
  },
  components: {
    ErrorMessage,
    loader,
    Multiselect,
    Building,
    Wallet,
  },
  data() {
    return {
      wallets: [],
      buildings: [],
      isLoader: false,
      isCustom: false,
      create: {
        wallet_id: null,
        building_id: null,
        bu_ty: "active",
      },
      errors: {},
      is_disabled: false,
      current_page: 1,
      company_id: 48,
    };
  },
  validations: {
    create: {
      building_id: { required },
      wallet_id: { required },
      bu_ty: { required },
    },
  },

  methods: {
    async getCustomTableFields() {
      this.isCustom = true;
      await adminApi
        .get(`/customTable/table-columns/rlst_building_wallet`)
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
    showBuildingModal() {
      if (this.create.building_id == 0) {
        this.$bvModal.show("building-create");
        this.create.building_id = null;
      }
    },
    showWalletModal() {
      if (this.create.wallet_id == 0) {
        this.$bvModal.show("wallet-create");
        this.create.wallet_id = null;
      }
    },
    defaultData(edit = null) {
      this.create = { wallet_id: null, building_id: null, bu_ty: "active" };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.is_disabled = false;

      this.errors = {};
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
          this.$nextTick(() => {
            this.$v.$reset();
          });
          this.getBuildings();
          this.getWallets();
        } else {
          if (this.idObjEdit.dataObj) {
            let buildingWallet = this.idObjEdit.dataObj;
            this.errors = {};
            this.create.building_id = buildingWallet.building.id;
            this.create.wallet_id = buildingWallet.wallet.id;
            this.create.bu_ty =
              buildingWallet.bu_ty == 1 ? "active" : "inactive";
            this.getBuildings();
            this.getWallets();
          }
        }
      }, 50);
    },

    resetForm() {
      this.defaultData();
    },
    AddSubmit() {
      if (this.$v.create.$invalid) {
        this.$v.create.$touch();
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        this.is_disabled = false;

        if (this.type != "edit") {
          adminApi
            .post(this.url, {
              ...this.create,
              bu_ty: this.create.bu_ty == "active" ? 1 : 2,
              company_id: this.$store.getters["auth/company_id"],
            })
            .then((res) => {
              this.is_disabled = true;
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

    async getBuildings() {
      this.isLoader = true;
      await adminApi
        .get(`/real-estate/buildings`)
        .then((res) => {
          this.isLoader = false;
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
        });
    },

    async getWallets() {
      this.isLoader = true;
      await adminApi
        .get(`/real-estate/wallets`)
        .then((res) => {
          this.isLoader = false;
          let l = res.data.data;
          if (this.isPermission("create wallet RealState")) {
            l.unshift({ id: 0, name: "اضافة محفظة", name_e: "Add wallet" });
          }
          this.wallets = l;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
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
      :isPage="false"
      type="create"
      :isPermission="isPermission"
      :id="'building-create'"
    />
    <Wallet
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getWallets"
      :isPage="false"
      type="create"
      :isPermission="isPermission"
      :id="'wallet-create'"
    />

    <!--  create   -->
    <b-modal
      id="create"
      :title="
        type != 'edit'
          ? getCompanyKey('building_wallet_create_form')
          : getCompanyKey('building_wallet_edit_form')
      "
      title-class="font-18"
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

        <div class="row">
          <div class="col-md-12 position-relative">
            <div class="form-group">
              <label class="my-1 mr-2">{{ getCompanyKey("building") }}</label>
              <multiselect
                @input="showBuildingModal"
                v-model="create.building_id"
                :options="buildings.map((type) => type.id)"
                :custom-label="
                  (opt) =>
                    buildings.find((x) => x.id == opt)
                      ? $i18n.locale == 'ar'
                        ? buildings.find((x) => x.id == opt).name
                        : buildings.find((x) => x.id == opt).name_e
                      : ''
                "
                :class="{
                  'is-invalid':
                    $v.create.building_id.$error || errors.building_id,
                }"
              >
              </multiselect>
              <div
                v-if="!$v.create.building_id.required"
                class="invalid-feedback"
              >
                {{ $t("general.fieldIsRequired") }}
              </div>

              <template v-if="errors.building_id">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.building_id"
                  :key="index"
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>
          <div class="col-md-12 position-relative">
            <div class="form-group">
              <label class="my-1 mr-2">{{ getCompanyKey("wallet") }}</label>
              <multiselect
                @input="showWalletModal"
                v-model="create.wallet_id"
                :options="wallets.map((type) => type.id)"
                :custom-label="
                  (opt) =>
                    wallets.find((x) => x.id == opt)
                      ? $i18n.locale == 'ar'
                        ? wallets.find((x) => x.id == opt).name
                        : wallets.find((x) => x.id == opt).name_e
                      : ''
                "
                :class="{
                  'is-invalid': $v.create.wallet_id.$error || errors.wallet_id,
                }"
              >
              </multiselect>
              <div
                v-if="!$v.create.wallet_id.required"
                class="invalid-feedback"
              >
                {{ $t("general.fieldIsRequired") }}
              </div>
              <template v-if="errors.wallet_id">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.wallet_id"
                  :key="index"
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group">
              <label class="mr-2">
                {{ getCompanyKey("building_wallet_bu_ty") }}
                <span class="text-danger">*</span>
              </label>
              <b-form-group
                :class="{
                  'is-invalid': $v.create.bu_ty.$error || errors.bu_ty,
                  'is-valid': !$v.create.bu_ty.$invalid && !errors.bu_ty,
                }"
              >
                <b-form-radio
                  class="d-inline-block"
                  v-model="$v.create.bu_ty.$model"
                  name="some-radios"
                  value="active"
                  >{{ $t("general.Building") }}</b-form-radio
                >
                <b-form-radio
                  class="d-inline-block m-1"
                  v-model="$v.create.bu_ty.$model"
                  name="some-radios"
                  value="inactive"
                  >{{ $t("general.Unit") }}</b-form-radio
                >
              </b-form-group>
              <template v-if="errors.bu_ty">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.bu_ty"
                  :key="index"
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>
        </div>
      </form>
    </b-modal>
    <!--  /create   -->
  </div>
</template>
