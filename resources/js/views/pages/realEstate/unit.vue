<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import loader from "../../../components/general/loader";
import translation from "../../../helper/mixin/translation-mixin";
import customTable from "../../../helper/mixin/customTable";
import successError from "../../../helper/mixin/success&error";
import crudHelper from "../../../helper/mixin/crudHelper";
import Unit from "../../../components/create/realEstate/unit";
import searchPage from "../../../components/general/searchPage";
import actionSetting from "../../../components/general/actionSetting";
import tableCustom from "../../../components/general/tableCustom";
import permissionGuard from "../../../helper/permission";

/**
 * Advanced Table component
 */

export default {
  page: {
    title: "units",
    meta: [{ name: "description", content: "units" }],
  },
  mixins: [translation, customTable, successError, crudHelper],
  components: {
    Layout, PageHeader, loader, searchPage,
    actionSetting, tableCustom, Unit
  },
  beforeRouteEnter(to, from, next) {
        next((vm) => {
      return permissionGuard(vm, "Unit Realestate", "all units RealState");
    });

 },
  data() {
    return {
        url: "/real-estate/units",
        searchMain: "",
        tableSetting: [
            {
                isFilter: true, isSet: true, trans: "realEstate_unit_name_ar", isV: "name",
                type: "string", sort: true, setting: { name: true }, isSetting: true,
            },
            {
                isFilter: true, isSet: true, trans: "realEstate_unit_name_en", isV: "name_e",
                type: "string", sort: true, setting: { name_e: true }, isSetting: true,
            },
            {
                isFilter: true, isSet: true, trans: "realEstate_unit_code", isV: "code",
                type: "string", sort: true, setting: { code: true }, isSetting: true,
            },
            {
                isFilter: true, isSet: true, trans: "realEstate_unit_unit_ty", isV: "unit_ty",
                type: "string", sort: true, setting: { unit_ty: true }, isSetting: true,
            },
            {
                isFilter: true, isSet: true, trans: "realEstate_unit_unit_area", isV: "unit_area",
                type: "string", sort: true, setting: { unit_area: true }, isSetting: true,
            },
            {
                isFilter: true, isSet: true, trans: "realEstate_unit_module", isV: "module_id",
                type: "string", sort: true, setting: { module_id: true }, isSetting: true,
            },
            {
                isFilter: true, isSet: true, trans: "realEstate_unit_rooms", isV: "rooms",
                type: "string", sort: true, setting: { rooms: true }, isSetting: true,
            },
            {
                isFilter: true, isSet: true, trans: "realEstate_unit_path", isV: "path",
                type: "string", sort: true, setting: { path: true }, isSetting: true,
            },
            {
                isFilter: true, isSet: true, trans: "realEstate_unit_floor", isV: "floor",
                type: "string", sort: true, setting: { floor: true }, isSetting: true,
            },
            {
                isFilter: true, isSet: true, trans: "realEstate_unit_unit_net_area", isV: "unit_net_area",
                type: "string", sort: true, setting: { unit_net_area: true }, isSetting: true,
            },
            {
                isFilter: true, isSet: true, trans: "realEstate_unit_building", isV: "building_id", type: "relation",
                name: "building", sort: false, col1: "name", col2: "name_e", setting: { building_id: true },
                isSetting: true,
            },
            {
                isFilter: true, isSet: true, trans: "realEstate_unit_status", isV: "unit_status_id", type: "relation",
                name: "unitStatus", sort: false, col1: "name", col2: "name_e", setting: { unit_status_id: true },
                isSetting: true,
            },
            {
                isFilter: true, isSet: true, trans: "realEstate_unit_description_ar", isV: "description",
                type: "string", sort: true, setting: { description: true }, isSetting: true,
            },
            {
                isFilter: true, isSet: true, trans: "realEstate_unit_description_en", isV: "description_e",
                type: "string", sort: true, setting: { description_e: true }, isSetting: true,
            },
        ],
        sendSetting: {},
        searchField: [],
    };
  },
  mounted() {
      this.searchField = this.tableSetting.filter((e) => e.isFilter).map((el) => el.isV);
      this.settingFun();
      this.getCustomTableFields("rlst_units");
      this.getData(1, this.url, this.filterSearch(this.searchField));
  },
  methods: {
      filterSearch(fields) {
          let indexC = fields.indexOf("building_id"),
              indexx = fields.indexOf("building_id"),
              filter = "";
          if (indexC > -1) {
              fields[indexC] =
                  this.$i18n.locale == "ar" ? "building.name" : "building.name_e";
          }
          if (indexx > -1) {
              fields[indexx] =
                  this.$i18n.locale == "ar" ? "unitStatus.name" : "unitStatus.name_e";
          }
          for (let i = 0; i < fields.length; ++i) {
              filter += `columns[${i}]=${fields[i]}&`;
          }
          return filter;
      },
      settingFun(setting = null) {
          if (setting) this.tableSetting = setting;
          let l = {},
              names = [];
          names = this.tableSetting.filter((e) => e.isSet).map((el) => el.setting);
          this.tableSetting.forEach((e, i) => {
              l[e.isV] = names[i][e.isV];
              e["isSetting"] = l[e.isV];
          });
          this.sendSetting = l;
      },
  },
};
</script>

<template>
  <Layout>
    <PageHeader />

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
                  <!-- start search -->
                  <searchPage
                      page="general.realEstateUnitTable"
                      :isVisible="isVisible"
                      :filterSetting="tableSetting"
                      :companyKeys="companyKeys"
                      :defaultsKeys="defaultsKeys"
                      @dataSearch="() => getData(1, url, filterSearch(searchField))"
                      @searchFun="(fields) => (searchField = fields)"
                      @editSearch="(search) => (searchMain = search)"
                      :isSearch="true"
                  />
                  <!-- end search -->

                  <!-- start setting -->
                  <actionSetting
                      :companyKeys="companyKeys"
                      :defaultsKeys="defaultsKeys"
                      :current_page="current_page"
                      :isCreate="true"
                      :isEdit="true"
                      :isDelete="true"
                      :permissionCreate="isPermission('create units RealState')"
                      :permissionUpdate="isPermission('update units RealState')"
                      :permissionDelete="isPermission('delete units RealState')"
                      :isExcl="true"
                      :isPrint="true"
                      :checkAll="checkAll"
                      :sideAction="true"
                      :sidePaginate="true"
                      :isFilter="true"
                      :group="true"
                      :isGroup="true"
                      :isVisible="isVisible"
                      :isSetting="true"
                      :isPaginate="true"
                      :settings="tableSetting"
                      @delete="(ids) => deleteRow(ids, url)"
                      @gen_xsl="ExportExcel('xlsx')"
                      @settingFun="(setting) => settingFun(setting)"
                      :objPagination="objPagination"
                      @perviousOrNext="
                (current) => getData(current, url, filterSearch(searchField))
              "
                      @currentPage="(page) => (current_page = page)"
                      @DataCurrentPage="(page) => getDataCurrentPage(page)"
                      @actionChange="
                ({ typeAction, id }) => changeType({ typeAction, id })
              "
                  />
                  <!-- end setting -->

                  <!--  create or edit   -->
                  <Unit
                      :id="'create'"
                      :companyKeys="companyKeys"
                      :defaultsKeys="defaultsKeys"
                      :isPage="true"
                      :isVisiblePage="isVisible"
                      :isRequiredPage="isRequired"
                      :url="url"
                      :type="type"
                      :idObjEdit="
                        idEdit
                          ? {
                              idEdit,
                              dataObj: this.tables.find((el) => el.id == idEdit),
                            }
                          : null
                      "
                      @getDataTable="getData(1, url, filterSearch(searchField))"
                      :isPermission="isPermission"
                  />
                  <!--  /create   -->


            <!-- start .table-responsive-->
            <div
              class="table-responsive mb-3 custom-table-theme position-relative"
              ref="exportable_table"
              id="printCustom"
            >
              <!--       start loader       -->
              <loader size="large" v-if="isLoader" />
              <!--       end loader       -->

                <tableCustom
                    :companyKeys="companyKeys"
                    :defaultsKeys="defaultsKeys"
                    :tables="tables"
                    :isEdit="true"
                    :isDelete="true"
                    :permissionUpdate="isPermission('update unit RealState')"
                    :permissionDelete="isPermission('delete unit RealState')"
                    :isVisible="isVisible"
                    :tableSetting="tableSetting"
                    :enabled3="enabled3"
                    :Tooltip="Tooltip"
                    @logFire="(id) => log(id, url)"
                    @delete="(ids) => deleteRow(ids, url)"
                    @editRow="(id) => dbClickRow(id)"
                    @checkRows="(cR) => (checkAll = cR)"
                    @checkRowTable="(id) => checkRow(id)"
                    :isInputCheck="true"
                    :isLog="true"
                    :isAction="true"
                />
            </div>
            <!-- end .table-responsive-->
        </div>
        </div>
      </div>
    </div>
  </Layout>
</template>





