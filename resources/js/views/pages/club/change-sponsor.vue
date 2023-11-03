<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import {
  required,
  minLength,
  maxLength,
  integer,
  requiredIf,
} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/general/loader";
import {
  dynamicSortString,
  dynamicSortNumber,
} from "../../../helper/tableSort";
import permissionGuard from "../../../helper/permission";

import Multiselect from "vue-multiselect";
import { formatDateOnly } from "../../../helper/startDate";
import { arabicValue, englishValue } from "../../../helper/langTransform";
import translation from "../../../helper/mixin/translation-mixin";
import Sponsor from "../../../components/create/club/sponsor.vue";

/**
 * Advanced Table component
 */
export default {
  page: {
    title: "Change sponsor",
    meta: [{ name: "description", content: "Change sponsor" }],
  },
  mixins: [translation],
  components: {
    Layout,
    PageHeader,
    Switches,
    ErrorMessage,
    loader,
    Multiselect,
  },
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      return permissionGuard(vm, "Change Sponsor", "all changeSpenser club");
    });
  },
  data() {
    return {
      sponsors: [],
      isLoader: false,
      create: {
        old_sponsor_id: null,
        sponsor_id: null,
      },
      members: [],
      enabled3: true,
      memberIds: [],
      isCheckAll: false,
      checkAll: [],
      membersPagination: {},
      current_page: 1
    };
  },
  validations: {
    create: {
      old_sponsor_id: { required },
      sponsor_id: { required },
    },
  },
  mounted() {
    this.getSponsors();
  },
  watch: {
      isCheckAll(after, befour) {
          if (after) {
              this.checkAll = this.memberIds;
          } else {
              this.checkAll = [];
          }
      },
  },
  methods: {
    checkRow(id) {
          if (!this.checkAll.includes(id)) {
              this.checkAll.push(id);
          } else {
              let index = this.checkAll.indexOf(id);
              this.checkAll.splice(index, 1);
          }
    },
    isPermission(item) {
      if (this.$store.state.auth.type == "user") {
        return this.$store.state.auth.permissions.includes(item);
      }
      return true;
    },
    AddSubmit() {
      this.$v.create.$touch();
      if (this.$v.create.$invalid) {
        return;
      } else {
        if (this.create.old_sponsor_id == this.create.sponsor_id) {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.BothSponsorsAreSame")}`,
          });
          return;
        }
        this.isLoader = true;
        this.errors = {};
        adminApi
          .put(`/club-members/members/update-sponsor-members`, {
            sponsor_id: this.create.sponsor_id,
            member_ids: this.checkAll
          })
          .then((res) => {
            this.create.sponsor_id = null;
            this.create.old_sponsor_id = null;
            this.members = [];
            this.memberIds = [];
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
    showSponsorModalOld() {
      if (this.create.old_sponsor_id > 0) {
          this.getData();
      }
    },
    getSponsors() {
      this.isLoader = true;
      adminApi
        .get(`/club-members/sponsers`)
        .then((res) => {
          let l = res.data.data;
          this.sponsors = l;
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
    getData(page = 1) {
          this.isLoader = true;

          adminApi
              .get(
                  `/club-members/members/get-sponsors?page=${page}&per_page=40&sponsor_id=${this.create.old_sponsor_id}&memberNumber=1`
              )
              .then((res) => {
                  let l = res.data.data;
                  this.members = l.data;
                  this.membersPagination = l['0'];
                  this.current_page = l['0'].current_page;
                  this.memberIds = l.memberIds;
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
              this.current_page <= this.membersPagination.last_page &&
              this.current_page != this.membersPagination.current_page &&
              this.current_page
          ) {
              this.isLoader = true;

              adminApi
                  .get(
                      `/club-members/members/get-sponsors?sponsor_id=${this.create.old_sponsor_id}&page=${this.current_page}&per_page=40`
                  )
                  .then((res) => {
                      let l = res.data.data;
                      this.members = l.data;
                      this.membersPagination = l['0'];
                      this.current_page = l['0'].current_page;
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
            <div class="row mb-2">
              <h4 class="header-title">{{ $t("general.changeSpenser") }}</h4>
            </div>
            <div class="row d-flex justify-content-center">
              <div class="col-md-8">
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="my-1 mr-2">
                        {{ getCompanyKey("from-sponsor") }}
                        <span class="text-danger">*</span>
                      </label>
                      <multiselect
                        @input="showSponsorModalOld"
                        v-model="create.old_sponsor_id"
                        :options="sponsors.map((type) => type.id)"
                        :custom-label="
                          (opt) => sponsors.find((x) => x.id == opt)?
                            $i18n.locale == 'ar'
                              ? sponsors.find((x) => x.id == opt).name
                              : sponsors.find((x) => x.id == opt).name_e: null
                        "
                      >
                      </multiselect>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="my-1 mr-2">
                        {{ getCompanyKey("to-sponsor") }}
                        <span class="text-danger">*</span>
                      </label>
                      <multiselect
                        v-model="create.sponsor_id"
                        :options="sponsors.map((type) => type.id)"
                        :custom-label="
                          (opt) => sponsors.find((x) => x.id == opt)?
                            $i18n.locale == 'ar'
                              ? sponsors.find((x) => x.id == opt).name
                              : sponsors.find((x) => x.id == opt).name_e: null
                        "
                      >
                      </multiselect>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <b-button
                      style="margin-top: 33px"
                      variant="success"
                      type="button"
                      :disabled="create.sponsor_id == create.old_sponsor_id"
                      class="mx-1"
                      v-if="
                        !isLoader && isPermission('create changeSpenser club')
                      "
                      @click.prevent="AddSubmit"
                    >
                      {{ $t("general.Add") }}
                    </b-button>

                    <b-button
                      style="margin-top: 33px"
                      variant="success"
                      class="mx-1"
                      disabled
                      v-else-if="isPermission('create sponsor club')"
                    >
                      <b-spinner small></b-spinner>
                      <span class="sr-only">{{ $t("login.Loading") }}...</span>
                    </b-button>
                  </div>
                </div>
              </div>
            </div>

            <!-- paginate -->
            <div
                  v-if="members.length > 0"
                  class="row justify-content-between align-items-center mb-2 px-1"
              >
                  <div class="col-md-3 d-flex align-items-center mb-1 mb-xl-0"></div>
                  <div
                      class="col-md-9 col-lg-7 d-flex align-items-center justify-content-end"
                  >
                      <div class="d-fex">
                          <!-- start filter and setting -->
                          <div class="d-inline-block"></div>
                          <!-- end filter and setting -->

                          <!-- start Pagination -->
                          <div
                              class="d-inline-flex align-items-center pagination-custom"
                          >
                              <div class="d-inline-block" style="font-size: 13px">
                                  {{ membersPagination.from }}-{{ membersPagination.to }} /
                                  {{ membersPagination.total }}
                              </div>
                              <div class="d-inline-block">
                                  <a
                                      href="javascript:void(0)"
                                      :style="{
                          'pointer-events':
                            membersPagination.current_page == 1 ? 'none' : '',
                        }"
                                      @click.prevent="
                          getData(membersPagination.current_page - 1)
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
                            membersPagination.last_page ==
                            membersPagination.current_page
                              ? 'none'
                              : '',
                        }"
                                      @click.prevent="
                          getData(membersPagination.current_page + 1)
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
                  v-if="members.length > 0"
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
                              <th >
                                  <div class="d-flex justify-content-center">
                                    <span>
                                        {{
                                            getCompanyKey("member_membership_number")
                                        }}
                                    </span>
                                  </div>
                              </th>
                              <th>
                                  <div class="d-flex justify-content-center">
                                      <span>{{ $t("general.name") }}</span>
                                  </div>
                              </th>
                              <th>
                                  <div class="d-flex justify-content-center">
                                      <span>{{ getCompanyKey("member_national_id") }}</span>
                                  </div>
                              </th>
                              <th>
                                  <div class="d-flex justify-content-center">
                            <span>{{
                                    getCompanyKey("member_nationality_number")
                                }}</span>
                                  </div>
                              </th>
                              <th>
                                  <div class="d-flex justify-content-center">
                                      <span>{{ getCompanyKey("member_home_phone") }}</span>
                                  </div>
                              </th>
                          </tr>
                      </thead>
                      <tbody
                          v-if="members.length > 0"
                      >
                      <tr
                          @click.capture="checkRow(data.id)"
                          v-for="(data, index) in members"
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
                          <td>
                              {{ data.membership_number }}
                          </td>
                          <td>
                              {{
                                  `${data.first_name} ${data.second_name} ${data.third_name} ${data.last_name} ${data.family_name}`
                              }}
                          </td>
                          <td>
                              {{ data.national_id }}
                          </td>
                          <td>
                              {{ data.nationality_number }}
                          </td>
                          <td>
                              {{ data.home_phone }}
                          </td>
                      </tr>
                      </tbody>
                      <tbody v-else>
                      <tr>
                          <th class="text-center" colspan="30">
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
<style lang="scss">
.buttons {
  margin-top: 20px !important;
  gap: 20px;
  display: flex;
  align-items: center;
  margin: 0 25px;
}

.file-upload {
  i {
    margin-top: 7px;
  }

  .icon {
    &:hover {
      cursor: pointer !important;
    }

    padding: 4px 12px !important;

    i {
      font-size: 14px;
      position: relative;
    }

    color: white !important;
  }

  text-align: center;

  input[type="file"] {
    display: none;
  }
}
</style>
