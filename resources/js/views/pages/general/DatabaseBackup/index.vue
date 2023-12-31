<script>
import permissionGuard from "../../../../helper/permission";

import Layout from "../../../layouts/main";
import PageHeader from "../../../../components/general/Page-header";
import adminApi from "../../../../api/adminAxios";
import Switches from "vue-switches";
import {
  required,
  minLength,
  maxLength,
  integer,
  requiredIf,
} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../../components/widgets/errorMessage";
import loader from "../../../../components/general/loader";
import alphaArabic from "../../../../helper/alphaArabic";
import alphaEnglish from "../../../../helper/alphaEnglish";
import {
  dynamicSortString,
  dynamicSortNumber,
} from "../../../../helper/tableSort";
import translation from "../../../../helper/mixin/translation-mixin";
import { formatDateOnly } from "../../../../helper/startDate";
import { arabicValue, englishValue } from "../../../../helper/langTransform";

/**
 * Advanced Table component
 */
export default {
  page: {
    title: "Database backup",
    meta: [{ name: "description", content: "Database backup" }],
  },
  mixins: [translation],
  components: {
    Layout,
    PageHeader,
    Switches,
    ErrorMessage,
    loader,
  },
  data() {
    return {
      per_page: 50,
      search: "",
      debounce: {},
      enabled3: true,
      itemsPagination: {},
      progress: 0,
      items: [],
      isLoader: false,
      Tooltip: "",
      mouseEnter: "",
      fields: [],
      company_id: null,
      errors: {},
      isCheckAll: false,
      checkAll: [],
      current_page: 1,
      setting: {
        id: true,
        path: true,
        created_at: true,
      },
      is_disabled: false,
      filterSetting: ["id", "path", "created_at"],
      printLoading: true,
      printObj: {
        id: "printData",
      },
    };
  },
  validations: {},
  watch: {
    /**
     * watch per_page
     */
    per_page(after, befour) {
      this.getData();
    },
    /**
     * watch search
     */
    search(after, befour) {
      clearTimeout(this.debounce);
      this.debounce = setTimeout(() => {
        this.getData();
      }, 400);
    },
    /**
     * watch check All table
     */
    isCheckAll(after, befour) {
      if (after) {
        this.items.forEach((el) => {
          if (!this.checkAll.includes(el.id)) {
            this.checkAll.push(el.id);
          }
        });
      } else {
        this.checkAll = [];
      }
    },
  },
  mounted() {
    this.company_id = this.$store.getters["auth/company_id"];
    this.getData();
  },
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      return permissionGuard(vm, "Database Backup", "all Customer");
    });
  },
  methods: {
    createBackup() {
      setTimeout(() => {
        let bar = document.getElementById("progress-bar");
        let self = this;
        const config = {
          onUploadProgress: function (progressEvent) {
            const percentCompleted = Math.round(
              (progressEvent.loaded / progressEvent.total) * 100
            );
            self.progress = percentCompleted;
            bar.innerHTML = `${percentCompleted}%`;
            bar.style.width = `${percentCompleted}%`;
          },
        };
        adminApi
          .post(`/backups`, {}, config)
          .then((res) => {
            this.getData();
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
      }, 500);
    },

    formatDate(value) {
      return formatDateOnly(value);
    },
    getData(page = 1) {
      this.isLoader = true;
      let filter = "";
      for (let i = 0; i < this.filterSetting.length; ++i) {
        filter += `columns[${i}]=${this.filterSetting[i]}&`;
      }

      adminApi
        .get(
          `/backups?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
        )
        .then((res) => {
          let l = res.data;
          this.items = l.data;
          this.itemsPagination = l.pagination;
          this.current_page = l.pagination.current_page;
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
    getDataCurrentPage(page = 1) {
      if (
        this.current_page <= this.itemsPagination.last_page &&
        this.current_page != this.itemsPagination.current_page &&
        this.current_page
      ) {
        this.isLoader = true;
        let filter = "";
        for (let i = 0; i < this.filterSetting.length; ++i) {
          filter += `columns[${i}]=${this.filterSetting[i]}&`;
        }

        adminApi
          .get(
            `/backups?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
          )
          .then((res) => {
            let l = res.data;
            this.items = l.data;
            this.itemsPagination = l.pagination;
            this.current_page = l.pagination.current_page;
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

    sortString(value) {
      return dynamicSortString(value);
    },
    SortNumber(value) {
      return dynamicSortNumber(value);
    },
    /**
     *  start  ckeckRow
     */
    checkRow(id) {
      if (!this.checkAll.includes(id)) {
        this.checkAll.push(id);
      } else {
        let index = this.checkAll.indexOf(id);
        this.checkAll.splice(index, 1);
      }
    },
    /**
     *  end  ckeckRow
     */
    moveInput(tag, c, index) {
      document.querySelector(`${tag}[data-${c}='${index}']`).focus();
    },
    /**
     *   Export Excel
     */
    ExportExcel(type, fn, dl) {
      this.enabled3 = false;
      setTimeout(() => {
        let elt = this.$refs.exportable_table;
        let wb = XLSX.utils.table_to_book(elt, { sheet: "Sheet JS" });
        if (dl) {
          XLSX.write(wb, { bookType: type, bookSST: true, type: "base64" });
        } else {
          XLSX.writeFile(
            wb,
            fn || ("Branch" + "." || "SheetJSTableExport.") + (type || "xlsx")
          );
        }
        this.enabled3 = true;
      }, 100);
    },
  },
};
</script>

<template>
  <Layout>
    <PageHeader />
    <b-modal
      id="progress"
      :title="$t('general.DatabaseBackup')"
      title-class="font-18"
      body-class="p-4 "
      :hide-footer="true"
      @show="createBackup"
    >
      <h4 v-if="progress == 100" class="header-title text-center mb-2">
        {{ $t("general.completed") }}
      </h4>
      <h4 v-else class="header-title text-center mb-2">
        {{ $t("general.loading") }}...
      </h4>
      <div style="background-color: #fff" class="progress">
        <div
          id="progress-bar"
          class="progress-bar"
          role="progressbar"
          style="width: 0%; background-color: #3bafda; transition: width 0.5s"
          aria-valuenow="25"
          aria-valuemin="0"
          aria-valuemax="100"
        >
          0%
        </div>
      </div>
    </b-modal>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <!-- start search -->
            <div class="row justify-content-between align-items-center mb-2">
              <h4 class="header-title">{{ $t("general.DatabaseBackup") }}</h4>
              <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                <div class="d-inline-block" style="width: 22.2%">
                  <!-- Basic dropdown -->
                  <b-dropdown
                    variant="primary"
                    :text="$t('general.searchSetting')"
                    ref="dropdown"
                    class="btn-block setting-search"
                  >
                    <b-form-checkbox
                      v-model="filterSetting"
                      value="id"
                      class="mb-1"
                    >
                      {{ $t("general.id") }}
                    </b-form-checkbox>
                    <b-form-checkbox
                      v-model="filterSetting"
                      value="path"
                      class="mb-1"
                    >
                      {{ $t("general.path") }}
                    </b-form-checkbox>
                    <b-form-checkbox
                      v-model="filterSetting"
                      value="created_at"
                      class="mb-1"
                    >
                      {{ $t("general.created_at") }}
                    </b-form-checkbox>
                  </b-dropdown>
                  <!-- Basic dropdown -->
                </div>

                <div
                  class="d-inline-block position-relative"
                  style="width: 77%"
                >
                  <span
                    :class="[
                      'search-custom position-absolute',
                      $i18n.locale == 'ar' ? 'search-custom-ar' : '',
                    ]"
                  >
                    <i class="fe-search"></i>
                  </span>
                  <input
                    class="form-control"
                    style="display: block; width: 93%; padding-top: 3px"
                    type="text"
                    v-model.trim="search"
                    :placeholder="`${$t('general.Search')}...`"
                  />
                </div>
              </div>
            </div>
            <!-- end search -->

            <div
              class="row justify-content-between align-items-center mb-2 px-1"
            >
              <div class="col-md-3 d-flex align-items-center mb-1 mb-xl-0">
                <!-- start create and printer -->
                <b-button
                  v-b-modal.progress
                  variant="primary"
                  class="btn-sm mx-1 font-weight-bold"
                >
                  {{ $t("general.Create") }}
                  <i class="fas fa-plus"></i>
                </b-button>
                <div class="d-inline-flex">
                  <button
                    @click="ExportExcel('xlsx')"
                    class="custom-btn-dowonload"
                  >
                    <i class="fas fa-file-download"></i>
                  </button>
                  <button v-print="'#printData'" class="custom-btn-dowonload">
                    <i class="fe-printer"></i>
                  </button>
                  <button
                    class="custom-btn-dowonload"
                    @click="$bvModal.show(`modal-edit-${checkAll[0]}`)"
                    v-if="checkAll.length == 1"
                  >
                    <i class="mdi mdi-square-edit-outline"></i>
                  </button>
                  <!-- start mult delete  -->
                  <button
                    class="custom-btn-dowonload"
                    v-if="checkAll.length > 1"
                    @click.prevent="deleteBranch(checkAll)"
                  >
                    <i class="fas fa-trash-alt"></i>
                  </button>
                  <!-- end mult delete  -->
                  <!--  start one delete  -->
                  <button
                    class="custom-btn-dowonload"
                    v-if="checkAll.length == 1"
                    @click.prevent="deleteBranch(checkAll[0])"
                  >
                    <i class="fas fa-trash-alt"></i>
                  </button>
                  <!--  end one delete  -->
                </div>
                <!-- end create and printer -->
              </div>
              <div
                class="col-xs-10 col-md-9 col-lg-7 d-flex align-items-center justify-content-end"
              >
                <div class="d-fex">
                  <!-- start filter and setting -->
                  <div class="d-inline-block">
                    <b-button class="mx-1 custom-btn-background">
                      {{ $t("general.filter") }}
                      <i class="fas fa-filter"></i>
                    </b-button>
                    <b-button class="mx-1 custom-btn-background">
                      {{ $t("general.group") }}
                      <i class="fe-menu"></i>
                    </b-button>
                    <!-- Basic dropdown -->
                    <b-dropdown
                      variant="primary"
                      :html="`${$t(
                        'general.setting'
                      )} <i class='fe-settings'></i>`"
                      ref="dropdown"
                      class="dropdown-custom-ali"
                    >
                      <b-form-checkbox v-model="setting.id" class="mb-1">
                        {{ $t("general.id") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.path" class="mb-1">
                        {{ $t("general.path") }}
                      </b-form-checkbox>
                      <b-form-checkbox
                        v-model="setting.created_at"
                        class="mb-1"
                      >
                        {{ $t("general.created_at") }}
                      </b-form-checkbox>
                      <div class="d-flex justify-content-end">
                        <a
                          href="javascript:void(0)"
                          class="btn btn-primary btn-sm"
                          >Apply</a
                        >
                      </div>
                    </b-dropdown>
                    <!-- Basic dropdown -->
                  </div>
                  <!-- end filter and setting -->

                  <!-- start Pagination -->
                  <div
                    class="d-inline-flex align-items-center pagination-custom"
                  >
                    <div class="d-inline-block" style="font-size: 13px">
                      {{ itemsPagination.from }}-{{ itemsPagination.to }} /
                      {{ itemsPagination.total }}
                    </div>
                    <div class="d-inline-block">
                      <a
                        href="javascript:void(0)"
                        :style="{
                          'pointer-events':
                            itemsPagination.current_page == 1 ? 'none' : '',
                        }"
                        @click.prevent="
                          getData(itemsPagination.current_page - 1)
                        "
                      >
                        <span>&lt;</span>
                      </a>
                      <input
                        type="text"
                        @keyup.enter="getDataCurrentPage()"
                        v-model="current_page"
                        class="pagination-current-page"
                      />
                      <a
                        href="javascript:void(0)"
                        :style="{
                          'pointer-events':
                            itemsPagination.last_page ==
                            itemsPagination.current_page
                              ? 'none'
                              : '',
                        }"
                        @click.prevent="
                          getData(itemsPagination.current_page + 1)
                        "
                      >
                        <span>&gt;</span>
                      </a>
                    </div>
                  </div>
                  <!-- end Pagination -->
                </div>
              </div>
            </div>

            <!-- start .table-responsive-->
            <div
              class="table-responsive mb-3 custom-table-theme position-relative"
            >
              <!--       start loader       -->
              <loader size="large" v-if="isLoader" />
              <!--       end loader       -->
              <table
                class="table table-borderless table-hover table-centered m-0"
                ref="exportable_table"
                id="printData"
              >
                <thead>
                  <tr>
                    <th
                      v-if="enabled3"
                      class="do-not-print"
                      scope="col"
                      style="width: 0"
                    >
                      <div class="form-check custom-control">
                        <input
                          class="form-check-input"
                          type="checkbox"
                          v-model="isCheckAll"
                          style="width: 17px; height: 17px"
                        />
                      </div>
                    </th>
                    <th v-if="setting.id">
                      <div class="d-flex justify-content-center">
                        <span>{{ $t("general.id") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="items.sort(sortString('id'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="items.sort(sortString('-id'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.path">
                      <div class="d-flex justify-content-center">
                        <span>{{ $t("general.path") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="items.sort(sortString('path'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="items.sort(sortString('-path'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.created_at">
                      <div class="d-flex justify-content-center">
                        <span>{{ $t("general.created_at") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="items.sort(sortString('created_at'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="items.sort(sortString('-created_at'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="enabled3" class="do-not-print">
                      {{ $t("general.Action") }}
                    </th>
                  </tr>
                </thead>
                <tbody v-if="items.length > 0">
                  <tr
                    @click.capture="checkRow(data.id)"
                    @dblclick.prevent="$bvModal.show(`modal-edit-${data.id}`)"
                    v-for="(data, index) in items"
                    :key="data.id"
                    class="body-tr-custom"
                  >
                    <td v-if="enabled3" class="do-not-print">
                      <div
                        class="form-check custom-control"
                        style="min-height: 1.9em"
                      >
                        <input
                          style="width: 17px; height: 17px"
                          class="form-check-input"
                          type="checkbox"
                          :value="data.id"
                          v-model="checkAll"
                        />
                      </div>
                    </td>
                    <td v-if="setting.id">
                      <h5 class="m-0 font-weight-normal">{{ data.id }}</h5>
                    </td>
                    <td v-if="setting.path">
                      <h5 class="m-0 font-weight-normal">{{ data.path }}</h5>
                    </td>
                    <td v-if="setting.created_at">
                      {{ data.created_at }}
                    </td>
                    <td v-if="enabled3" class="do-not-print">
                      <div class="btn-group">
                        <button
                          type="button"
                          class="btn btn-sm dropdown-toggle dropdown-coustom"
                          data-toggle="dropdown"
                          aria-expanded="false"
                        >
                          {{ $t("general.commands") }}
                          <i class="fas fa-angle-down"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-custom">
                          <a
                            target="_blank"
                            class="dropdown-item text-black"
                            :href="data.path"
                          >
                            <div
                              class="d-flex justify-content-between align-items-center text-black"
                            >
                              <span>{{ $t("general.Download") }}</span>
                              <i class="mx-2 fas fa-download text-success"></i>
                            </div>
                          </a>
                        </div>
                      </div>
                    </td>
                  </tr>
                </tbody>
                <tbody v-else>
                  <tr>
                    <th class="text-center" colspan="11">
                      {{ $t("general.notDataFound") }}
                    </th>
                  </tr>
                </tbody>
              </table>
            </div>
            <!-- end .table-responsive-->
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<style scoped>
@media print {
  .do-not-print {
    display: none;
  }

  .arrow-sort {
    display: none;
  }
}
</style>
