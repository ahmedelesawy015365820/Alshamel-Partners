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
import translation from "../../../helper/mixin/translation-mixin";
import {
  formatDateTime,
  formatDateOnly,
  formatTime,
} from "../../../helper/startDate";
import loader from "../../../components/general/loader";
import DatePicker from "vue2-datepicker";
import { dynamicSortString, dynamicSortDate } from "../../../helper/tableSort";
import { arabicValue, englishValue } from "../../../helper/langTransform";
import Multiselect from "vue-multiselect";
import permissionGuard from "../../../helper/permission";

/**
 * Advanced Table component
 */
export default {
  page: {
      title: "Online request",
      meta: [{ name: "description", content: "Online request" }],
  },
  mixins: [translation],
  components: {
    Layout,
    PageHeader,
    Multiselect,
    Switches,
    ErrorMessage,
    loader,
    DatePicker,
  },
  data() {
    return {
      manager: true,
      fields: [],
      per_page: 50,
      search: "",
      debounce: {},
      onlineRequestsPagination: {},
      onlineRequests: [],
      isLoader: false,
      isUserManager: false,
      employee_id: null,
      requestTypes: [],
      employees: [],
      statuses: [],
      manageOthers: 1,
      from_id:null,
      to_id:null,
      request_id:null,
      create: {
          custom_request_datetime: new Date(),
          custom_approved_date: null,
          custom_approved_from_date: null,
          custom_approved_to_date: null,
          custom_approved_from_hour: null,
          custom_approved_to_hour: null,
          request_status_id: null,
          request_datetime: formatDateTime(new Date()),
          from_date: formatDateOnly(new Date()),
          to_date: this.formatDate(new Date()),
          remark: '',
          request_types_id: null,
          employee_id: null,
          amount: 0,
          from_hour: formatTime(new Date()),
          to_hour: formatTime(new Date()),
          approved_date: null,
          approved_from_date: null,
          approved_to_date: null,
          approved_from_hour: null,
          approved_to_hour: null,
          approved_amount: 0,
          media: null,
      },
      edit: {
          custom_request_datetime: new Date(),
          custom_approved_date: new Date(),
          custom_approved_from_date: new Date(),
          custom_approved_to_date: new Date(),
          custom_approved_from_hour: new Date(),
          custom_approved_to_hour: new Date(),
          request_status_id: null,
          request_datetime: formatDateTime(new Date()),
          from_date: formatDateOnly(new Date()),
          to_date: formatDateOnly(new Date()),
          remark: '',
          request_types_id: null,
          employee_id: null,
          amount: 0,
          from_hour: formatTime(new Date()),
          to_hour: formatTime(new Date()),
          approved_date: formatDateOnly(new Date()),
          approved_from_date: formatDateOnly(new Date()),
          approved_to_date: formatDateOnly(new Date()),
          approved_from_hour: formatTime(new Date()),
          approved_to_hour: formatTime(new Date()),
          approved_amount: 0,
      },
      errors: {},
      isCheckAll: false,
      checkAll: [],
      current_page: 1,
      setting: {
        status_id: true,
        approved_date: true,
        approved_from_date: true,
        approved_to_date: true,
        approved_from_hour: true,
        approved_to_hour: true,
        approved_amount: true,
        request_types_id: true,
        request_datetime: true,
        employee_id: true,
        start_from: true,
        end_date: true,
        amount: true,
        from_hour: true,
        to_hour: true,
        remark: true,
      },
      is_disabled: false,
      filterSetting: ["request_types_id", "employee_id"],
      Tooltip: "",
      company_id: null,
      mouseEnter: null,
      enabled3: true,
      printLoading: true,
      idDelete: null,
      printObj: {
        id: "printBuilding",
      },
      images: [],
      media: {},
      saveImageName: [],
      showPhoto: "./images/img-1.png",
      isUpdate: false
    };
  },
  validations: {
    create: {
          request_types_id: { required },
          request_status_id: {required,},
          employee_id: { required },
          approved_from_date: { required: requiredIf(function (model) {
                  return this.$store.state.auth.type == "admin"
              }), },
          approved_to_date: { required: requiredIf(function (model) {
                  return this.$store.state.auth.type == "admin"
              }), },
          request_datetime: { },
          approved_date: { required: requiredIf(function (model) {
                  return this.$store.state.auth.type == "admin"
              }), },
          amount: { required: requiredIf(function (model) {
                  return (
                      !this.create.request_types_id ||
                      (this.create.request_types_id &&
                          this.getRequestType(this.create.request_types_id).amount)
                  );
              }) },
          approved_amount: { required: requiredIf(function (model) {
                  return this.$store.state.auth.type == "admin"
              }), },
          from_hour: { required: requiredIf(function (model) {
                  return (
                      !this.create.request_types_id ||
                      (this.create.request_types_id &&
                          this.getRequestType(this.create.request_types_id).from_hour)
                  );
              }), },
          approved_from_hour: { required: requiredIf(function (model) {
                  return this.$store.state.auth.type == "admin"
              }), },
          to_hour: {required: requiredIf(function (model) {
                  return (
                      !this.create.request_types_id ||
                      (this.create.request_types_id &&
                          this.getRequestType(this.create.request_types_id).to_hour)
                  );
              }),},
          approved_to_hour: { required: requiredIf(function (model) {
                  return this.$store.state.auth.type == "admin"
              }), },
          from_date: { required: requiredIf(function (model) {
                  return (
                      !this.create.request_types_id ||
                      (this.create.request_types_id &&
                          this.getRequestType(this.create.request_types_id).start_from)
                  );
              }),
          },
          to_date: { required: requiredIf(function (model) {
                  return (
                      !this.create.request_types_id ||
                      (this.create.request_types_id &&
                          this.getRequestType(this.create.request_types_id).end_date)
                  );
              }),
          },
          remark: { required },
          media: {}
    },
    edit: {
        media: {},
        request_types_id: { required },
        request_status_id: {required,},
        employee_id: { required },
        approved_from_date: { required: requiredIf(function (model) {
                return this.$store.state.auth.type == "admin"
            }),
        },
        approved_to_date: { required: requiredIf(function (model) {
                return this.$store.state.auth.type == "admin"
            }),
        },
        request_datetime: { },
        approved_date: { required: requiredIf(function (model) {
                return this.$store.state.auth.type == "admin"
            }), },
        amount: { required: requiredIf(function (model) {
                return (
                    !this.edit.request_types_id ||
                    (this.edit.request_types_id &&
                        this.getRequestType(this.edit.request_types_id).amount)
                );
            }) },
        approved_amount: { required: requiredIf(function (model) {
                return this.$store.state.auth.type == "admin"
            }), },
        from_hour: { required: requiredIf(function (model) {
                return (
                    !this.edit.request_types_id ||
                    (this.edit.request_types_id &&
                        this.getRequestType(this.edit.request_types_id).from_hour)
                );
            }), },
        approved_from_hour: { required: requiredIf(function (model) {
                return this.$store.state.auth.type == "admin"
            }), },
        to_hour: {required: requiredIf(function (model) {
                return (
                    !this.edit.request_types_id ||
                    (this.edit.request_types_id &&
                        this.getRequestType(this.edit.request_types_id).to_hour)
                );
            }),},
        approved_to_hour: { required: requiredIf(function (model) {
                return this.$store.state.auth.type == "admin"
            }), },
        from_date: { required: requiredIf(function (model) {
                return (
                    !this.edit.request_types_id ||
                    (this.edit.request_types_id &&
                        this.getRequestType(this.edit.request_types_id).start_from)
                );
            }), },
        to_date: { required: requiredIf(function (model) {
                return (
                    !this.edit.request_types_id ||
                    (this.edit.request_types_id &&
                        this.getRequestType(this.edit.request_types_id).end_date)
                );
            }), },
        remark: { required },
    },
  },
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
        this.onlineRequests.forEach((el) => {
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
    this.manager = this.$store.state.auth.type == 'admin' ? true:false;
    this.getData();
  },
    beforeRouteEnter(to, from, next) {
        next((vm) => {
            return permissionGuard(vm, "Online Request", "all onlineRequest hr");
        });
    },
  methods: {
    showModule(id,type){
          let onlineRequest = this.onlineRequests.find((e) => id == e.id);
          if(type == 'edit' && onlineRequest && onlineRequest.request_status_id != 1){
              Swal.fire({
                  icon: "error",
                  title: `${this.$t("general.Error")}`,
                  text: `${this.$t("general.YoucantUpdatethisrequestthemanagerhasreceivedit")}`,
              });

              return false;
          }else if(type == 'delete' && onlineRequest && onlineRequest.request_status_id != 1){
              Swal.fire({
                  icon: "error",
                  title: `${this.$t("general.Error")}`,
                  text: `${this.$t("general.YoucantDeletethisrequestthemanagerhasreceivedit")}`,
              });
              return false;
          }

          return true;
      },
    approved_date(e) {
          if (e) {
              this.create.approved_date = formatDateOnly(e);
              this.edit.approved_date = formatDateOnly(e);
          } else {
              this.create.approved_date = null;
              this.edit.approved_date = null;
          }
      },
    approved_from_date(e) {
          if (e) {
              this.create.approved_from_date = formatDateOnly(e);
              this.edit.approved_from_date = formatDateOnly(e);
          } else {
              this.create.approved_from_date = null;
              this.edit.approved_from_date = null;
          }
      },
    approved_to_date(e) {
          if (e) {
              this.create.approved_to_date = formatDateOnly(e);
              this.edit.approved_to_date = formatDateOnly(e);
          } else {
              this.create.approved_to_date = null;
              this.edit.approved_to_date = null;
          }
      },
    to_hour(e) {
          if (e) {
              this.create.to_hour = formatTime(e);
              this.edit.to_hour = formatTime(e);
          } else {
              this.create.to_hour = null;
              this.edit.to_hour = null;
          }
      },
    from_hour(e) {
          if (e) {
              this.create.from_hour = formatTime(e);
              this.edit.from_hour = formatTime(e);
          } else {
              this.create.from_hour = null;
              this.edit.from_hour = null;
          }
      },
    approved_to_hour(e) {
          if (e) {
              this.create.approved_to_hour = formatTime(e);
              this.edit.approved_to_hour = formatTime(e);
          } else {
              this.create.approved_to_hour = null;
              this.edit.approved_to_hour = null;
          }
      },
    approved_from_hour(e) {
          if (e) {
              this.create.approved_from_hour = formatTime(e);
              this.edit.approved_from_hour = formatTime(e);
          } else {
              this.create.approved_from_hour = null;
              this.edit.approved_from_hour = null;
          }
      },
    isPermission(item) {
        if (this.$store.state.auth.type == "admin") {
            return this.$store.state.auth.is_web == 1;
        }
        if (this.$store.state.auth.type == "user") {
            return this.$store.state.auth.permissions.includes(item);
        }
        return true;
    },
    getRequestType(id) {
      let rt = this.requestTypes.filter((t) => t.id == id);
      return rt.length > 0 ? rt[0] : null;
    },
    async getEmployees() {
      if(this.$store.state.auth.type == 'user'){
          await adminApi
              .get(`/hr/requests/employee-login`)
              .then((res) => {
                  this.employees = [res.data.data];
              })
              .catch((err) => {
                  Swal.fire({
                      icon: "error",
                      title: `${this.$t("general.Error")}`,
                      text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                  });
              });
      }else {
          await adminApi
              .get(`/employees`)
              .then((res) => {
                  this.employees = res.data.data;
              })
              .catch((err) => {
                  Swal.fire({
                      icon: "error",
                      title: `${this.$t("general.Error")}`,
                      text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                  });
              });
      }
    },
    async getEmployeeChildren() {
      this.isLoader = true;
      await adminApi
        .get(`employees?id=${this.$store.state.auth.user.employee_id}`)
        .then((res) => {
          let l = res.data.data;
          this.employees = l;
          let result = l.filter(
            (e) => e.id == this.$store.state.auth.user.employee_id
          );
          this.manageOthers = result.length && result[0].manage_others ? 1 : 0;
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
    async getStatuses() {
      await adminApi
        .get(`/hr/statues`)
        .then((res) => {
          this.statuses = res.data.data;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        });
    },
    async getRequestTypes() {
      await adminApi
        .get(`/hr/request-types`)
        .then((res) => {
          this.requestTypes = res.data.data;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        });
    },
    /**
     *  start get Data countrie && pagination
     */
    getData(page = 1) {
      this.isLoader = true;
      let filter = "";
      let _filterSetting = [...this.filterSetting];
      let index = this.filterSetting.indexOf("employee_id");
      if (index > -1) {
        _filterSetting[index] =
          this.$i18n.locale == "ar" ? "employee.name" : "employee.name_e";
      }
      index = this.filterSetting.indexOf("request_types_id");
      if (index > -1) {
        _filterSetting[index] =
          this.$i18n.locale == "ar" ? "requestType.name" : "requestType.name_e";
      }
      for (let i = 0; i < _filterSetting.length; ++i) {
        filter += `columns[${i}]=${_filterSetting[i]}&`;
      }

      adminApi
        .get(
          `/hr/requests?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}&from_id=${this.from_id??''}&to_id=${this.to_id??''}&employee_id=${this.employee_id??''}`
        )
        .then((res) => {
          let l = res.data;
          this.onlineRequests = l.data;
          this.onlineRequestsPagination = l.pagination;
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
        this.current_page <= this.onlineRequestsPagination.last_page &&
        this.current_page != this.onlineRequestsPagination.current_page &&
        this.current_page
      ) {
        this.isLoader = true;
        let filter = "";
        for (let i = 0; i < this.filterSetting.length; ++i) {
          filter += `columns[${i}]=${this.filterSetting[i]}&`;
        }

        adminApi
          .get(
            `/hr/requests?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
          )
          .then((res) => {
            let l = res.data;
            this.onlineRequests = l.data;
            this.onlineRequestsPagination = l.pagination;
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
    /**
     *  end get Data countrie && pagination
     */
    /**
     *  start delete countrie
     */
    deleteFinancialYear(id, index) {
      if (Array.isArray(id)) {
        let onlineRequest = this.onlineRequests.find((e) => 1 != e.request_status_id);
        if(!onlineRequest){
            Swal.fire({
                title: `${this.$t("general.Areyousure")}`,
                text: `${this.$t("general.Youwontbeabletoreverthis")}`,
                type: "warning",
                showCancelButton: true,
                confirmButtonText: `${this.$t("general.Yesdeleteit")}`,
                cancelButtonText: `${this.$t("general.Nocancel")}`,
                confirmButtonClass: "btn btn-success mt-2",
                cancelButtonClass: "btn btn-danger ml-2 mt-2",
                buttonsStyling: false,
            }).then((result) => {
                if (result.value) {
                    this.isLoader = true;
                    adminApi
                        .post(`/hr/requests/bulk-delete`, { ids: id })
                        .then((res) => {
                            this.checkAll = [];
                            this.getData();
                            Swal.fire({
                                icon: "success",
                                title: `${this.$t("general.Deleted")}`,
                                text: `${this.$t("general.Yourrowhasbeendeleted")}`,
                                showConfirmButton: false,
                                timer: 1500,
                            });
                        })
                        .catch((err) => {
                            if (err.response.status == 400) {
                                Swal.fire({
                                    icon: "error",
                                    title: `${this.$t("general.Error")}`,
                                    text: `${this.$t("general.CantDeleteRelation")}`,
                                });
                                this.getData();
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
            });
        }else {
            this.showModule(onlineRequest.id,'delete')
        }
      } else {
        Swal.fire({
          title: `${this.$t("general.Areyousure")}`,
          text: `${this.$t("general.Youwontbeabletoreverthis")}`,
          type: "warning",
          showCancelButton: true,
          confirmButtonText: `${this.$t("general.Yesdeleteit")}`,
          cancelButtonText: `${this.$t("general.Nocancel")}`,
          confirmButtonClass: "btn btn-success mt-2",
          cancelButtonClass: "btn btn-danger ml-2 mt-2",
          buttonsStyling: false,
        }).then((result) => {
          if (result.value) {
            this.isLoader = true;

            adminApi
              .delete(`/hr/requests/${id}`)
              .then((res) => {
                this.checkAll = [];
                this.getData();
                Swal.fire({
                  icon: "success",
                  title: `${this.$t("general.Deleted")}`,
                  text: `${this.$t("general.Yourrowhasbeendeleted")}`,
                  showConfirmButton: false,
                  timer: 1500,
                });
              })

              .catch((err) => {
                if (err.response.status == 400) {
                  Swal.fire({
                    icon: "error",
                    title: `${this.$t("general.Error")}`,
                    text: `${this.$t("general.CantDeleteRelation")}`,
                  });
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
        });
      }
    },
    /**
     *  end delete countrie
     */
    /**
     *  reset Modal (create)
     */
    resetModalHidden() {
      this.manageOthers = 1;

      this.create = {
          custom_request_datetime: new Date(),
          custom_approved_date: null,
          custom_approved_from_date: null,
          custom_approved_to_date: null,
          custom_approved_from_hour: null,
          custom_approved_to_hour: null,
          request_status_id: null,
          request_datetime: formatDateTime(new Date()),
          from_date: formatDateOnly(new Date()),
          to_date: formatDateOnly(new Date()),
          remark: '',
          request_types_id: null,
          employee_id: null,
          amount: 0,
          from_hour: formatTime(new Date()),
          to_hour: formatTime(new Date()),
          approved_date: null,
          approved_from_date: null,
          approved_to_date: null,
          approved_from_hour: null,
          approved_to_hour: null,
          approved_amount: 0,
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
      this.images = [];
      this.request_id = null;
      this.is_disabled = false;
      this.$bvModal.hide(`create`);
    },
    /**
     *  hidden Modal (create)
     */
    async resetModal() {
          this.manager = this.$store.state.auth.type == 'admin' ? true:false;
          await this.getStatuses();
          await this.getEmployees();
          this.create = {
              custom_request_datetime: new Date(),
              custom_approved_date: null,
              custom_approved_from_date: null,
              custom_approved_to_date: null,
              custom_approved_from_hour: null,
              custom_approved_to_hour: null,
              request_status_id: 1,
              request_datetime: formatDateTime(new Date()),
              from_date: formatDateOnly(new Date()),
              to_date: formatDateOnly(new Date()),
              remark: '',
              request_types_id: null,
              employee_id: this.$store.state.auth.user.employee_id,
              amount: 0,
              from_hour: formatTime(new Date()),
              to_hour: formatTime(new Date()),
              approved_date: null,
              approved_from_date: null,
              approved_to_date: null,
              approved_from_hour: null,
              approved_to_hour: null,
              approved_amount: 0,
          };
          await this.getRequestTypes();
          this.showPhoto = "./images/img-1.png";
         this.$nextTick(() => {
             this.$v.$reset();
         });
         this.request_id = null;
         this.media = {};
         this.images = [];
         this.errors = {};
    },
    /**
     *  create countrie
     */
    resetForm() {
      this.manager = this.$store.state.auth.type == 'admin' ? true:false;
      this.manageOthers = 1;
      this.create = {
          custom_request_datetime: new Date(),
          custom_approved_date: null,
          custom_approved_from_date: null,
          custom_approved_to_date: null,
          custom_approved_from_hour: null,
          custom_approved_to_hour: null,
          request_status_id: 1,
          request_datetime: formatDateTime(new Date()),
          from_date: formatDateOnly(new Date()),
          to_date: formatDateOnly(new Date()),
          remark: '',
          request_types_id: null,
          employee_id: this.$store.state.auth.user.employee_id,
          amount: 0,
          from_hour: formatTime(new Date()),
          to_hour: formatTime(new Date()),
          approved_date: null,
          approved_from_date: null,
          approved_to_date: null,
          approved_from_hour: null,
          approved_to_hour: null,
          approved_amount: 0,
      };
      this.is_disabled = false;
        this.showPhoto = "./images/img-1.png";
        this.$nextTick(() => {
            this.$v.$reset();
        });
        this.request_id = null;
        this.media = {};
        this.images = [];
      this.errors = {};
    },

    AddSubmit() {
      this.$v.create.$touch();

      if (this.$v.create.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        adminApi
          .post(`/hr/requests`, {...this.create,company_id: this.$store.getters["auth/company_id"],})
          .then((res) => {
            this.request_id = res.data.data.id;
            this.is_disabled = true;
            this.getData();
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
    /**
     *  edit countrie
     */
    editSubmit(id) {
        this.$v.edit.$touch();
        this.images.forEach((e) => {
            this.edit.old_media.push(e.id);
        });
        if (this.$v.edit.$invalid) {
            return;
        } else {
            this.isLoader = true;
            this.errors = {};
            if(this.edit.employee_id != this.$store.state.auth.user.employee_id){
                adminApi
                    .post(`/hr/requests/updateManager`, {data: {
                        ...this.edit,
                    }
                    })
                    .then((res) => {
                        this.$bvModal.hide(`modal-edit-${id}`);
                        this.getData();
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
            }else {
                adminApi
                    .put(`/hr/requests/${this.request_id}`, {
                        ...this.edit,
                        approved_by: parseInt(this.$store.state.auth.user.employee_id)
                    })
                    .then((res) => {
                        this.$bvModal.hide(`modal-edit-${id}`);
                        this.getData();
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
    /**
     *   show Modal (edit)
     */
    async resetModalEdit(id) {
      let financialYear = this.onlineRequests.find((e) => id == e.id);
        this.request_id = financialYear.id;
        this.getManager(financialYear);
        this.date(financialYear.request_datetime);
        let currDateFromHour = financialYear.from_hour ? new Date(): '';
        if(currDateFromHour){
            financialYear.from_hour?currDateFromHour.setHours(
                financialYear.from_hour.split(":")[0],
                financialYear.from_hour.split(":")[1],
                financialYear.from_hour.split(":")[2]
            ): false;
        }
        let currDateToHour = financialYear.to_hour ? new Date(): '';
        if(currDateToHour){
            financialYear.to_hour?currDateToHour.setHours(
                financialYear.to_hour.split(":")[0],
                financialYear.to_hour.split(":")[1],
                financialYear.to_hour.split(":")[2]
            ): false;
        }

        this.edit.id = financialYear.id;
        this.edit.custom_from_hour = financialYear.from_hour? currDateFromHour: financialYear.from_hour;
        this.edit.custom_to_hour = financialYear.to_hour? currDateToHour : financialYear.to_hour;
        this.edit.from_hour = financialYear.from_hour?this.formatTime(currDateFromHour):financialYear.from_hour;
        this.edit.to_hour = financialYear.to_hour?this.formatTime(currDateToHour):financialYear.to_hour;
        this.edit.to_date = financialYear.to_date;
        this.edit.from_date = financialYear.from_date
        this.edit.custom_request_datetime = financialYear.request_datetime? new Date(financialYear.request_datetime):new Date();
        if(this.$store.state.auth.type == 'user') {
            if (financialYear.employee_id != this.$store.state.auth.user.employee_id && financialYear.request_status_id < 3) {
                this.edit.approved_date= formatDateOnly(new Date());
                this.edit.approved_from_date = financialYear.from_date;
                this.edit.approved_to_date = financialYear.to_date;
                let currDateApprovedFromHour = financialYear.from_hour ? new Date(): '';
                if(currDateApprovedFromHour){
                    financialYear.from_hour?currDateApprovedFromHour.setHours(
                        financialYear.from_hour.split(":")[0],
                        financialYear.from_hour.split(":")[1],
                        financialYear.from_hour.split(":")[2]
                    ): false;
                }

                let currDateApprovedToHour = financialYear.to_hour ? new Date(): '';
                if(currDateApprovedToHour){
                    financialYear.to_hour?currDateApprovedToHour.setHours(
                        financialYear.to_hour.split(":")[0],
                        financialYear.to_hour.split(":")[1],
                        financialYear.to_hour.split(":")[2]
                    ): false;
                }
                this.edit.custom_approved_from_hour = currDateApprovedFromHour;
                this.edit.custom_approved_to_hour = currDateApprovedToHour;
                this.edit.approved_from_hour = currDateApprovedFromHour?this.formatTime(currDateApprovedFromHour):null;
                this.edit.approved_to_hour = currDateApprovedToHour?this.formatTime(currDateApprovedToHour):null;
                this.edit.approved_amount = financialYear.amount;
            }else {
                this.edit.approved_date= financialYear.approved_date;
                this.edit.approved_from_date = financialYear.approved_from_date;
                this.edit.approved_to_date = financialYear.approved_to_date;
                this.edit.approved_amount = financialYear.approved_amount;
                let currDateApprovedFromHour = financialYear.approved_from_hour ? new Date(): '';
                if(currDateApprovedFromHour){
                    financialYear.approved_from_hour?currDateApprovedFromHour.setHours(
                        financialYear.approved_from_hour.split(":")[0],
                        financialYear.approved_from_hour.split(":")[1],
                        financialYear.approved_from_hour.split(":")[2]
                    ): false;
                }

                let currDateApprovedToHour = financialYear.approved_to_hour ? new Date(): '';
                if(currDateApprovedToHour){
                    financialYear.approved_to_hour?currDateApprovedToHour.setHours(
                        financialYear.approved_to_hour.split(":")[0],
                        financialYear.approved_to_hour.split(":")[1],
                        financialYear.approved_to_hour.split(":")[2]
                    ): false;
                }
                this.edit.custom_approved_from_hour = currDateApprovedFromHour;
                this.edit.custom_approved_to_hour = currDateApprovedToHour;
                this.edit.approved_from_hour = currDateApprovedFromHour?this.formatTime(currDateApprovedFromHour):null;
                this.edit.approved_to_hour = currDateApprovedToHour?this.formatTime(currDateApprovedToHour):null;
            }
        }else {
            this.edit.approved_date= financialYear.approved_date;
            this.edit.approved_from_date = financialYear.approved_from_date;
            this.edit.approved_to_date = financialYear.approved_to_date;
            this.edit.approved_amount = financialYear.approved_amount;
            let currDateApprovedFromHour = financialYear.approved_from_hour ? new Date(): '';
            if(currDateApprovedFromHour){
                financialYear.approved_from_hour?currDateApprovedFromHour.setHours(
                    financialYear.approved_from_hour.split(":")[0],
                    financialYear.approved_from_hour.split(":")[1],
                    financialYear.approved_from_hour.split(":")[2]
                ): false;
            }

            let currDateApprovedToHour = financialYear.approved_to_hour ? new Date(): '';
            if(currDateApprovedToHour){
                financialYear.approved_to_hour?currDateApprovedToHour.setHours(
                    financialYear.approved_to_hour.split(":")[0],
                    financialYear.approved_to_hour.split(":")[1],
                    financialYear.approved_to_hour.split(":")[2]
                ): false;
            }
            this.edit.custom_approved_from_hour = currDateApprovedFromHour;
            this.edit.custom_approved_to_hour = currDateApprovedToHour;
            this.edit.approved_from_hour = currDateApprovedFromHour? this.formatTime(currDateApprovedFromHour):null;
            this.edit.approved_to_hour = currDateApprovedToHour?this.formatTime(currDateApprovedToHour):null;
        }
        // if(this.$store.state.auth.type == 'admin'){
        //     this.edit.approved_date = financialYear.approved_date ? financialYear.approved_date : formatDateOnly(new Date());
        //     this.edit.approved_from_date= financialYear.approved_from_date ?financialYear.approved_from_date: formatDateOnly(new Date());
        //     this.edit.approved_to_date= financialYear.approved_to_date ?financialYear.approved_to_date: formatDateOnly(new Date());
        // }
      this.edit.remark = financialYear.remark;
      this.edit.amount = financialYear.amount;
      this.employees.push(financialYear.employee);
      this.edit.employee_id = financialYear.employee_id;
      await this.getRequestTypes();
      this.edit.request_types_id = financialYear.request_types_id;
      await this.getStatuses();
      this.edit.request_status_id = financialYear.request_status_id;

        this.images = financialYear.media ?? [];
        if (this.images && this.images.length > 0) {
            this.showPhoto = this.images[this.images.length - 1].webp;
        } else {
            this.showPhoto = "./images/img-1.png";
        }
      this.errors = {};
    },
    /**
     *  hidden Modal (edit)
     */
    resetModalHiddenEdit(id) {
      this.manageOthers = 1;
      this.errors = {};
      this.edit = {
          custom_request_datetime: new Date(),
          custom_approved_date: new Date(),
          custom_approved_from_date: new Date(),
          custom_approved_to_date: new Date(),
          custom_approved_from_hour: new Date(),
          custom_approved_to_hour: new Date(),
          request_status_id: null,
          request_datetime: formatDateTime(new Date()),
          from_date: formatDateOnly(new Date()),
          to_date: formatDateOnly(new Date()),
          remark: '',
          request_types_id: null,
          employee_id: null,
          amount: 0,
          from_hour: formatTime(new Date()),
          to_hour: formatTime(new Date()),
          approved_date: formatDateOnly(new Date()),
          approved_from_date: formatDateOnly(new Date()),
          approved_to_date: formatDateOnly(new Date()),
          approved_from_hour: formatTime(new Date()),
          approved_to_hour: formatTime(new Date()),
          approved_amount: 0,
      };
    },
    /*
     *  start  dynamicSortString
     */
    sortString(value) {
      return dynamicSortString(value);
    },
    SortDate(value) {
      return dynamicSortDate(value);
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
    /**
     * watch start_date end date
     */
    start_from(e) {
      if (e) {
        this.create.from_date = formatDateOnly(e);
        this.edit.from_date = formatDateOnly(e);
      } else {
        this.create.from_date = null;
        this.edit.from_date = null;
      }
    },
    end_date(e) {
      if (e) {
        this.create.to_date = formatDateOnly(e);
        this.edit.to_date = formatDateOnly(e);
      } else {
        this.create.to_date = null;
        this.edit.to_date = null;
      }
    },
    date(e) {
      if (e) {
        this.create.request_datetime = formatDateTime(e);
        this.edit.request_datetime = formatDateTime(e);
      } else {
        this.create.request_datetime = null;
        this.edit.request_datetime = null;
      }
    },
    formatDate(value) {
      return formatDateOnly(value);
    },
    formatTime(value) {
      return formatTime(value);
    },
    log(id) {
      if (this.mouseEnter != id) {
        this.Tooltip = "";
        this.mouseEnter = id;
        adminApi
          .get(`/hr/requests/logs/${id}`)
          .then((res) => {
            let l = res.data.data;
            l.forEach((e) => {
              this.Tooltip += `Created By: ${e.causer_type}; Event: ${
                e.event
              }; Description: ${e.description} ;Created At: ${this.formatDate(
                e.created_at
              )} \n`;
            });
            $(`#tooltip-${id}`).tooltip();
          })
          .catch((err) => {
            Swal.fire({
              icon: "error",
              title: `${this.$t("general.Error")}`,
              text: `${this.$t("general.Thereisanerrorinthesystem")}`,
            });
          });
      }
    },
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
            fn ||
              ("FinanicalYear" + "." || "SheetJSTableExport.") +
                (type || "xlsx")
          );
        }
        this.enabled3 = true;
      }, 100);
    },
    arabicValueName(txt) {
      this.create.name = arabicValue(txt);
      this.edit.name = arabicValue(txt);
    },
    englishValueName(txt) {
      this.create.name_e = englishValue(txt);
      this.edit.name_e = englishValue(txt);
    },
    showRequestTypeModel(id){
        if(id){
            this.create.amount = this.getRequestType(id).amount ? 0: 0;
            this.create.from_date = this.getRequestType(id).start_from ? formatDateOnly(new Date())  : '';
            this.create.to_date = this.getRequestType(id).end_date ? formatDateOnly(new Date()) : '';
            this.create.from_hour = this.getRequestType(id).from_hour? formatTime(new Date()) : '';
            this.create.to_hour = this.getRequestType(id).to_hour ? formatTime(new Date()): '';
            return true;
        }
        this.create.amount = 0;
        this.create.from_date = formatDateOnly(new Date());
        this.create.to_date = formatDateOnly(new Date());
        this.create.from_hour =  formatTime(new Date())
        this.create.to_hour = formatTime(new Date());
    },
    changePhoto() {
          document.getElementById("uploadImageCreate").click();
      },
    changePhotoEdit() {
          document.getElementById("uploadImageEdit").click();
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
                              .put(`/hr/requests/${this.request_id}`, {
                                  old_media,
                                  media: new_media,
                              })
                              .then((res) => {
                                  this.images = res.data.data.media ?? [];
                                  if (this.images && this.images.length > 0) {
                                      this.showPhoto = this.images[this.images.length - 1].webp;
                                  } else {
                                      this.showPhoto = "./images/img-1.png";
                                  }
                                  this.getData();
                              })
                              .catch((err) => {
                                  Swal.fire({
                                      icon: "error",
                                      title: `${this.$t("general.Error")}`,
                                      text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                                  });
                              });
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
                                      .put(`/hr/requests/${this.request_id}`, {
                                          old_media,
                                          media: new_media,
                                      })
                                      .then((res) => {
                                          this.images = res.data.data.media ?? [];
                                          if (this.images && this.images.length > 0) {
                                              this.showPhoto = this.images[this.images.length - 1].webp;
                                          } else {
                                              this.showPhoto = "./images/img-1.png";
                                          }
                                          this.getData();
                                      })
                                      .catch((err) => {
                                          Swal.fire({
                                              icon: "error",
                                              title: `${this.$t("general.Error")}`,
                                              text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                                          });
                                      });
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
              .put(`/hr/requests/${this.request_id}`, { old_media })
              .then((res) => {
                  this.onlineRequests[index] = res.data.data;
                  this.images = res.data.data.media ?? [];
                  if (this.images && this.images.length > 0) {
                      this.showPhoto = this.images[this.images.length - 1].webp;
                  } else {
                      this.showPhoto = "./images/img-1.png";
                  }
              })
              .catch((err) => {
                  Swal.fire({
                      icon: "error",
                      title: `${this.$t("general.Error")}`,
                      text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                  });
              });
      },
    getManager(request){
        if(this.$store.state.auth.type == 'user'){
            if(request.employee_id == this.$store.state.auth.user.employee_id){
                if(request.request_status_id > 1){
                    this.manager = true;
                    this.isUserManager = true;
                    this.isUpdate = true;
                }else{
                    this.manager = false;
                    this.isUserManager = true;
                    this.isUpdate = false;
                }
            }else {
                if(request.request_status_id < 3){
                    this.manager = true;
                    this.isUserManager = false;
                    this.isUpdate = false;
                }else{
                    this.manager = true;
                    this.isUserManager = true;
                    this.isUpdate = true;
                }
            }
        }else {
            this.manager = true;
            this.isUserManager = true;
            this.isUpdate = true;
        }
      }
  },
};
</script>

<template>
  <Layout>

    <PageHeader />
     <b-modal
      id="filter"
      :title="$t('general.filter')"
      title-class="font-18"
      body-class="p-4"
      :hide-footer="true"
      @show="getEmployees"
      @hidden="from_id=null;to_id=null;employee_id=null;"
    >
      <div class="row px-2 mb-4">
        <div class="col-md-6">
              <div class="form-group position-relative">
                  <label class="control-label">
                      {{ getCompanyKey("employee") }}
                      <span class="text-danger">*</span>
                  </label>
                  <multiselect
                      v-model="employee_id"
                      :options="employees.map((type) => type.id)"
                      :custom-label="
                        (opt) =>
                          employees.find((x) => x.id == opt).name
                      "
                  >
                  </multiselect>
              </div>
          </div>
        <div class="col-md-6"></div>
        <div class="col-md-6">
          <label>{{ $t("general.from_id") }}</label>
          <input class="form-control" type="number" v-model="from_id" />
        </div>
        <div class="col-md-6">
          <label>{{ $t("general.to_id") }}</label>
          <input class="form-control" type="number" v-model="to_id" />
        </div>
      </div>
      <div class="d-flex">
        <b-button
          variant="success"
          type="submit"
          class="mx-2"
          v-if="!isLoader"
          @click.prevent="getData(1)"
        >
          {{ $t("general.filter") }}
        </b-button>

        <b-button variant="success" class="mx-1" disabled v-else>
          <b-spinner small></b-spinner>
          <span class="sr-only">{{ $t("login.Loading") }}...</span>
        </b-button>
      </div>
    </b-modal>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <!-- start search -->
            <div class="row justify-content-between align-items-center mb-2">
              <h4 class="header-title">
                  {{ $t("general.onlineRequestsTable") }}
              </h4>
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
                      value="request_types_id"
                      class="mb-1"
                      >{{ getCompanyKey("request_type") }}</b-form-checkbox
                    >
                    <b-form-checkbox
                      v-model="filterSetting"
                      value="employee_id"
                      class="mb-1"
                      >{{ getCompanyKey("employee") }}</b-form-checkbox
                    >
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
                  v-if="isPermission('create onlineRequest hr') && $store.state.auth.type == 'user'"
                  v-b-modal.create
                  variant="primary"
                  class="btn-sm mx-1 font-weight-bold"
                >
                  {{ $t("general.Create") }}
                  <i class="fas fa-plus"></i>
                </b-button>
                <div class="d-inline-flex">
                  <button
                    class="custom-btn-dowonload"
                    @click="ExportExcel('xlsx')"
                  >
                    <i class="fas fa-file-download"></i>
                  </button>
                  <button class="custom-btn-dowonload" v-print="'#printCustom'">
                    <i class="fe-printer"></i>
                  </button>
                  <button
                    class="custom-btn-dowonload"
                    v-if="isPermission('update onlineRequest hr') && checkAll.length == 1"
                    @click="$bvModal.show(`modal-edit-${checkAll[0]}`)"
                  >
                      <i class="mdi mdi-square-edit-outline"></i>
                  </button>
                  <!-- start mult delete  -->
                  <button
                    class="custom-btn-dowonload"
                    v-if="
                      checkAll.length > 1 &&
                      isPermission('delete onlineRequest hr') && !manager
                    "
                    @click.prevent="deleteFinancialYear(checkAll)"
                  >
                    <i class="fas fa-trash-alt"></i>
                  </button>
                  <!-- end mult delete  -->
                  <!--  start one delete  -->
                  <button
                    class="custom-btn-dowonload"
                    v-if="
                      checkAll.length == 1 &&
                      isPermission('delete onlineRequest hr') && !manager
                    "
                    @click.prevent="showModule(checkAll[0],'delete')?deleteFinancialYear(checkAll[0]):false"
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
                    <b-button v-b-modal.filter  class="mx-1 custom-btn-background">
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
                      <b-form-checkbox
                        v-model="setting.employee_id"
                        class="mb-1"
                      >
                        {{ getCompanyKey("employee") }}
                      </b-form-checkbox>

                      <b-form-checkbox
                        v-model="setting.request_types_id"
                        class="mb-1"
                        >{{ getCompanyKey("request_type") }}
                      </b-form-checkbox>

                      <b-form-checkbox
                        v-model="setting.start_from"
                        class="mb-1"
                      >
                        {{ getCompanyKey("online_request_start_from") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.end_date" class="mb-1">
                        {{ getCompanyKey("online_request_end_date") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.remark" class="mb-1">
                        {{ getCompanyKey("online_request_remark") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.amount" class="mb-1">
                        {{ getCompanyKey("online_request_amount") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.from_hour" class="mb-1">
                        {{ getCompanyKey("online_request_from_hour") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.to_hour" class="mb-1">
                        {{ getCompanyKey("online_request_to_hour") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.status_id" class="mb-1"
                        >{{ getCompanyKey("online_request_last_status") }}
                      </b-form-checkbox>
                      <b-form-checkbox
                        v-model="setting.approved_from_date"
                        class="mb-1"
                        >{{
                          getCompanyKey("online_request_approved_start_from")
                        }}
                      </b-form-checkbox>

                      <b-form-checkbox
                        v-model="setting.approved_to_date"
                        class="mb-1"
                        >{{ getCompanyKey("online_request_approved_end_date") }}
                      </b-form-checkbox>

                      <b-form-checkbox
                        v-model="setting.approved_from_hour"
                        class="mb-1"
                        >{{
                          getCompanyKey("online_request_approved_from_hour")
                        }}
                      </b-form-checkbox>
                      <b-form-checkbox
                        v-model="setting.approved_to_hour"
                        class="mb-1"
                        >{{ getCompanyKey("online_request_approved_to_hour") }}
                      </b-form-checkbox>
                      <b-form-checkbox
                        v-model="setting.approved_amount"
                        class="mb-1"
                        >{{ getCompanyKey("online_request_approved_amount") }}
                      </b-form-checkbox>
                      <b-form-checkbox
                        v-model="setting.approved_date"
                        class="mb-1"
                        >{{ getCompanyKey("online_request_approved_date") }}
                      </b-form-checkbox>
                        <b-form-checkbox
                            v-model="setting.request_datetime"
                            class="mb-1"
                        >{{ getCompanyKey("online_request_date") }}
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
                      {{ onlineRequestsPagination.from }}-{{
                        onlineRequestsPagination.to
                      }}
                      / {{ onlineRequestsPagination.total }}
                    </div>
                    <div class="d-inline-block">
                      <a
                        href="javascript:void(0)"
                        :style="{
                          'pointer-events':
                            onlineRequestsPagination.current_page == 1
                              ? 'none'
                              : '',
                        }"
                        @click.prevent="
                          getData(onlineRequestsPagination.current_page - 1)
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
                            onlineRequestsPagination.last_page ==
                            onlineRequestsPagination.current_page
                              ? 'none'
                              : '',
                        }"
                        @click.prevent="
                          getData(onlineRequestsPagination.current_page + 1)
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

              <!--  create   -->
              <b-modal
                  :id="`create`"
                  :title="getCompanyKey('request_view_form')"
                  title-class="font-18"
                  body-class="p-4"
                  dialog-class="modal-full-width"
                  :hide-footer="true"
                  @show="resetModal"
                  @hidden="resetModalHidden"
              >
                  <form>
                      <div class="card">
                          <div class="card-body">
                              <div class="mt-1 d-flex justify-content-end">
                                  <!-- Emulate built in modal footer ok and cancel button actions -->
                                  <b-button
                                      variant="success"
                                      :disabled="!request_id"
                                      @click.prevent="resetForm"
                                      type="button"
                                      :class="['font-weight-bold px-2', request_id ? 'mx-2' : '']"
                                  >
                                      {{ $t("general.AddNewRecord") }}
                                  </b-button>

                                  <template v-if="!request_id">
                                      <b-button
                                          variant="success"
                                          type="button"
                                          class="mx-1 font-weight-bold px-3"
                                          v-if="!isLoader"
                                          @click.prevent="AddSubmit"
                                      >
                                          {{ $t("general.Save") }}
                                      </b-button>

                                      <b-button variant="success" class="mx-1" disabled v-else>
                                          <b-spinner small></b-spinner>
                                          <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                      </b-button>
                                  </template>

                                  <b-button
                                      variant="danger"
                                      class="font-weight-bold"
                                      type="button"
                                      @click.prevent="resetModalHidden"
                                  >
                                      {{ $t("general.Cancel") }}
                                  </b-button>
                              </div>
                          </div>
                          <b-tabs nav-class="nav-tabs nav-bordered">
                              <b-tab :title="$t('general.DataEntry')" active>
                                  <div class="row">
                                      <div class="col-md-4">
                                          <div class="form-group position-relative">
                                              <label class="control-label">
                                                  {{ getCompanyKey("employee") }}
                                                  <span class="text-danger">*</span>
                                              </label>
                                              <multiselect
                                                  :disabled="true"
                                                  v-model="create.employee_id"
                                                  :options="employees.map((type) => type.id)"
                                                  :custom-label="
                                    (opt) =>
                                      employees.find((x) => x.id == opt).name
                                  "
                                              >
                                              </multiselect>
                                              <div
                                                  v-if="
                                    $v.create.employee_id.$error ||
                                    errors.employee_id
                                  "
                                                  class="text-danger"
                                              >
                                                  {{ $t("general.fieldIsRequired") }}
                                              </div>
                                              <template v-if="errors.employee_id">
                                                  <ErrorMessage
                                                      v-for="(
                                      errorMessage, index
                                    ) in errors.employee_id"
                                                      :key="index"
                                                  >{{ errorMessage }}
                                                  </ErrorMessage>
                                              </template>
                                          </div>
                                      </div>
                                      <div class="col-md-4">
                                          <div class="form-group position-relative">
                                              <label class="control-label">
                                                  {{ getCompanyKey("request_type") }}
                                                  <span class="text-danger">*</span>
                                              </label>
                                              <multiselect
                                                  @input="showRequestTypeModel(create.request_types_id)"
                                                  v-model="create.request_types_id"
                                                  :options="requestTypes.map((type) => type.id)"
                                                  :custom-label="
                                    (opt) =>
                                      requestTypes.find((x) => x.id == opt).name
                                  "
                                              >
                                              </multiselect>
                                              <div
                                                  v-if="
                                    $v.create.request_types_id.$error ||
                                    errors.request_types_id
                                  "
                                                  class="text-danger"
                                              >
                                                  {{ $t("general.fieldIsRequired") }}
                                              </div>
                                              <template v-if="errors.request_types_id">
                                                  <ErrorMessage
                                                      v-for="(
                                      errorMessage, index
                                    ) in errors.request_types_id"
                                                      :key="index"
                                                  >{{ errorMessage }}
                                                  </ErrorMessage>
                                              </template>
                                          </div>
                                      </div>
                                      <div class="col-md-4">
                                          <div class="form-group">
                                              <label class="control-label">
                                                  {{ getCompanyKey("online_request_date") }}
                                                  <span class="text-danger">*</span>
                                              </label>
                                              <date-picker
                                                  :disabled="true"
                                                  v-model="create.custom_request_datetime"
                                                  type="datetime"
                                                  :default-value="create.custom_request_datetime"
                                                  @change="date"
                                                  confirm
                                              ></date-picker>
                                              <div
                                                  v-if="!$v.create.request_datetime.required"
                                                  class="invalid-feedback"
                                              >
                                                  {{ $t("general.fieldIsRequired") }}
                                              </div>
                                              <template v-if="errors.request_datetime">
                                                  <ErrorMessage
                                                      v-for="(errorMessage, index) in errors.request_datetime"
                                                      :key="index"
                                                  >{{ errorMessage }}</ErrorMessage
                                                  >
                                              </template>
                                          </div>
                                      </div>
                                      <div
                                          class="col-md-4"
                                          v-if="!create.request_types_id || (create.request_types_id && getRequestType(create.request_types_id).start_from)"
                                      >
                                          <div class="form-group">
                                              <label class="control-label">
                                                  {{ getCompanyKey("online_request_start_from") }}
                                                  <span class="text-danger">*</span>
                                              </label>
                                              <date-picker
                                                  v-model="create.from_date"
                                                  type="date"
                                                  format="YYYY-MM-DD"
                                                  valueType="format"
                                                  confirm
                                              ></date-picker>
                                              <div
                                                  v-if="!$v.create.from_date.required"
                                                  class="invalid-feedback"
                                              >
                                                  {{ $t("general.fieldIsRequired") }}
                                              </div>
                                              <template v-if="errors.from_date">
                                                  <ErrorMessage
                                                      v-for="(errorMessage, index) in errors.from_date"
                                                      :key="index"
                                                  >{{ errorMessage }}</ErrorMessage
                                                  >
                                              </template>
                                          </div>
                                      </div>
                                      <div
                                          class="col-md-4"
                                          v-if="
                                  !create.request_types_id ||
                                  (create.request_types_id &&
                                    getRequestType(create.request_types_id).end_date)
                                "
                                      >
                                          <div class="form-group">
                                              <label class="control-label">
                                                  {{ getCompanyKey("online_request_end_date") }}
                                                  <span class="text-danger">*</span>
                                              </label>
                                              <date-picker
                                                  v-model="create.to_date"
                                                  format="YYYY-MM-DD"
                                                  valueType="format"
                                                  type="date"
                                                  confirm
                                              ></date-picker>
                                              <div
                                                  v-if="!$v.create.to_date.required"
                                                  class="invalid-feedback"
                                              >
                                                  {{ $t("general.fieldIsRequired") }}
                                              </div>
                                              <template v-if="errors.to_date">
                                                  <ErrorMessage
                                                      v-for="(errorMessage, index) in errors.to_date"
                                                      :key="index"
                                                  >{{ errorMessage }}</ErrorMessage
                                                  >
                                              </template>
                                          </div>
                                      </div>
                                      <div class="col-md-4">
                                          <div class="form-group">
                                              <label class="control-label">
                                                  {{ getCompanyKey("online_request_remark") }}
                                                  <span class="text-danger">*</span>
                                              </label>
                                              <input
                                                  type="text"
                                                  v-model="create.remark"
                                                  class="form-control"
                                              />
                                          </div>
                                      </div>
                                      <div
                                          class="col-md-4"
                                          v-if="
                                  !create.request_types_id ||
                                  (create.request_types_id &&
                                    getRequestType(create.request_types_id).amount)
                                "
                                      >
                                          <div class="form-group">
                                              <label class="control-label">
                                                  {{ getCompanyKey("online_request_amount") }}
                                                  <span class="text-danger">*</span>
                                              </label>
                                              <input
                                                  type="number"
                                                  v-model="create.amount"
                                                  class="form-control"
                                              />
                                          </div>
                                      </div>
                                      <div
                                          class="col-md-4"
                                          v-if="
                                  !create.request_types_id ||
                                  (create.request_types_id &&
                                    getRequestType(create.request_types_id).from_hour)
                              "
                                      >
                                          <div class="form-group">
                                              <label class="control-label">
                                                  {{ getCompanyKey("online_request_from_hour") }}
                                                  <span class="text-danger">*</span>
                                              </label>
                                              <date-picker
                                                  v-model="create.custom_from_hour"
                                                  type="time"
                                                  format="hh:mm A"
                                                  :default-value="create.custom_from_hour"
                                                  @change="from_hour"
                                                  confirm
                                              ></date-picker>
                                              <div
                                                  v-if="!$v.create.from_hour.required"
                                                  class="invalid-feedback"
                                              >
                                                  {{ $t("general.fieldIsRequired") }}
                                              </div>
                                              <template v-if="errors.from_hour">
                                                  <ErrorMessage
                                                      v-for="(errorMessage, index) in errors.from_hour"
                                                      :key="index"
                                                  >{{ errorMessage }}</ErrorMessage
                                                  >
                                              </template>
                                          </div>
                                      </div>
                                      <div
                                          class="col-md-4"
                                          v-if="
                                  !create.request_types_id ||
                                  (create.request_types_id &&
                                    getRequestType(create.request_types_id).to_hour)
                                "
                                      >
                                          <div class="form-group">
                                              <label class="control-label">
                                                  {{ getCompanyKey("online_request_to_hour") }}
                                                  <span class="text-danger">*</span>
                                              </label>
                                              <date-picker
                                                  v-model="create.custom_to_hour"
                                                  type="time"
                                                  format="hh:mm A"
                                                  :default-value="create.custom_to_hour"
                                                  @change="to_hour"
                                                  confirm
                                              ></date-picker>
                                              <div
                                                  v-if="!$v.create.to_hour.required"
                                                  class="invalid-feedback"
                                              >
                                                  {{ $t("general.fieldIsRequired") }}
                                              </div>
                                              <template v-if="errors.to_hour">
                                                  <ErrorMessage
                                                      v-for="(errorMessage, index) in errors.to_hour"
                                                      :key="index"
                                                  >{{ errorMessage }}</ErrorMessage
                                                  >
                                              </template>
                                          </div>
                                      </div>
                                      <div class="col-md-4">
                                          <div class="form-group position-relative">
                                              <label class="control-label">
                                                  {{
                                                      getCompanyKey("online_request_last_status")
                                                  }}
                                                  <span class="text-danger">*</span>
                                              </label>
                                              <multiselect
                                                  :disabled="true"
                                                  v-model="create.request_status_id"
                                                  :options="statuses.map((type) => type.id)"
                                                  :custom-label="
                                                    (opt) => $i18n.locale == 'ar' ?statuses.find((x) => x.id == opt).name : statuses.find((x) => x.id == opt).name_e
                                                  "
                                              >
                                              </multiselect>
                                              <div
                                                  v-if="
                                    $v.create.request_status_id.$error || errors.request_status_id
                                  "
                                                  class="text-danger"
                                              >
                                                  {{ $t("general.fieldIsRequired") }}
                                              </div>
                                              <template v-if="errors.request_status_id">
                                                  <ErrorMessage
                                                      v-for="(
                                      errorMessage, index
                                    ) in errors.request_status_id"
                                                      :key="index"
                                                  >{{ errorMessage }}
                                                  </ErrorMessage>
                                              </template>
                                          </div>
                                      </div>
                                  </div>
                                  <hr style="
                        margin: 10px 0 !important;
                        border-top: 1px solid rgb(141 163 159 / 42%);
                      "/>
                                  <div class="row">
                                      <div class="col-md-4">
                                          <div class="form-group">
                                              <label class="control-label">
                                                  {{ getCompanyKey("online_request_approved_date") }}
                                              </label>
                                              <date-picker
                                                  :disabled="true"
                                                  v-model="create.custom_approved_date"
                                                  type="date"
                                                  :default-value="create.custom_approved_date"
                                                  @change="approved_date"
                                                  confirm
                                              ></date-picker>
                                              <div
                                                  v-if="!$v.create.approved_date.required"
                                                  class="invalid-feedback"
                                              >
                                                  {{ $t("general.fieldIsRequired") }}
                                              </div>
                                              <template v-if="errors.approved_date">
                                                  <ErrorMessage
                                                      v-for="(errorMessage, index) in errors.approved_date"
                                                      :key="index"
                                                  >{{ errorMessage }}</ErrorMessage
                                                  >
                                              </template>
                                          </div>
                                      </div>
                                      <div
                                          class="col-md-4"
                                          v-if="!create.request_types_id || (create.request_types_id && getRequestType(create.request_types_id).start_from)"
                                      >
                                          <div class="form-group">
                                              <label class="control-label">
                                                  {{
                                                      getCompanyKey("online_request_approved_start_from")
                                                  }}
                                              </label>
                                              <date-picker
                                                  :disabled="true"
                                                  v-model="create.approved_from_date"
                                                  type="date"
                                                  format="YYYY-MM-DD"
                                                  valueType="format"
                                                  confirm
                                              ></date-picker>
                                              <div
                                                  v-if="!$v.create.approved_from_date.required"
                                                  class="invalid-feedback"
                                              >
                                                  {{ $t("general.fieldIsRequired") }}
                                              </div>
                                              <template v-if="errors.approved_from_date">
                                                  <ErrorMessage
                                                      v-for="(
                            errorMessage, index
                          ) in errors.approved_from_date"
                                                      :key="index"
                                                  >{{ errorMessage }}</ErrorMessage
                                                  >
                                              </template>
                                          </div>
                                      </div>
                                      <div
                                          class="col-md-4"
                                          v-if="
                                  !create.request_types_id ||
                                  (create.request_types_id &&
                                    getRequestType(create.request_types_id).end_date)
                                "
                                      >
                                          <div class="form-group">
                                              <label class="control-label">
                                                  {{ getCompanyKey("online_request_approved_end_date") }}
                                              </label>
                                              <date-picker
                                                  :disabled="true"
                                                  v-model="create.approved_to_date"
                                                  format="YYYY-MM-DD"
                                                  valueType="format"
                                                  type="date"
                                                  confirm
                                              ></date-picker>
                                              <div
                                                  v-if="!$v.create.approved_to_date.required"
                                                  class="invalid-feedback"
                                              >
                                                  {{ $t("general.fieldIsRequired") }}
                                              </div>
                                              <template v-if="errors.approved_to_date">
                                                  <ErrorMessage
                                                      v-for="(
                            errorMessage, index
                          ) in errors.approved_to_date"
                                                      :key="index"
                                                  >{{ errorMessage }}</ErrorMessage
                                                  >
                                              </template>
                                          </div>
                                      </div>
                                      <div
                                          class="col-md-4"
                                          v-if="
                                  !create.request_types_id ||
                                  (create.request_types_id &&
                                    getRequestType(create.request_types_id).from_hour)
                              "
                                      >
                                          <div class="form-group">
                                              <label class="control-label">
                                                  {{ getCompanyKey("online_request_approved_from_hour") }}
                                              </label>
                                              <date-picker
                                                  format="hh:mm A"
                                                  :disabled="true"
                                                  v-model="create.custom_approved_from_hour"
                                                  type="time"
                                                  :default-value="create.custom_approved_from_hour"
                                                  @change="approved_from_hour"
                                                  confirm
                                              ></date-picker>
                                              <div
                                                  v-if="!$v.create.approved_from_hour.required"
                                                  class="invalid-feedback"
                                              >
                                                  {{ $t("general.fieldIsRequired") }}
                                              </div>
                                              <template v-if="errors.approved_from_hour">
                                                  <ErrorMessage
                                                      v-for="(
                            errorMessage, index
                          ) in errors.approved_from_hour"
                                                      :key="index"
                                                  >{{ errorMessage }}</ErrorMessage
                                                  >
                                              </template>
                                          </div>
                                      </div>
                                      <div
                                          class="col-md-4"
                                          v-if="
                                  !create.request_types_id ||
                                  (create.request_types_id &&
                                    getRequestType(create.request_types_id).to_hour)
                                "
                                      >
                                          <div class="form-group">
                                              <label class="control-label">
                                                  {{ getCompanyKey("online_request_approved_to_hour") }}
                                              </label>
                                              <date-picker
                                                  format="hh:mm A"
                                                  :disabled="true"
                                                  v-model="create.custom_approved_to_hour"
                                                  type="time"
                                                  :default-value="create.custom_approved_to_hour"
                                                  @change="approved_to_hour"
                                                  confirm
                                              ></date-picker>
                                              <div
                                                  v-if="!$v.create.approved_to_hour.required"
                                                  class="invalid-feedback"
                                              >
                                                  {{ $t("general.fieldIsRequired") }}
                                              </div>
                                              <template v-if="errors.approved_to_hour">
                                                  <ErrorMessage
                                                      v-for="(
                            errorMessage, index
                          ) in errors.approved_to_hour"
                                                      :key="index"
                                                  >{{ errorMessage }}</ErrorMessage
                                                  >
                                              </template>
                                          </div>
                                      </div>
                                      <div
                                          class="col-md-4"
                                          v-if="
                                  !create.request_types_id ||
                                  (create.request_types_id &&
                                    getRequestType(create.request_types_id).amount)
                                "
                                      >
                                          <div class="form-group">
                                              <label class="control-label">
                                                  {{getCompanyKey("online_request_approved_amount") }}
                                              </label>
                                              <input
                                                  :disabled="true"
                                                  type="number"
                                                  v-model="create.approved_amount"
                                                  class="form-control"
                                              />
                                          </div>
                                      </div>
                                  </div>
                              </b-tab>
                              <b-tab
                                  :disabled="!request_id"
                                  :title="$t('general.ImageUploads')"
                              >
                                  <div class="row">
                                      <input
                                          accept="image/png, image/gif, image/jpeg, image/jpg"
                                          type="file"
                                          id="uploadImageCreate"
                                          @change.prevent="onImageChanged"
                                          class="input-file-upload position-absolute d-none"
                                      />
                                      <div class="col-md-8 my-1">
                                          <!-- file upload -->
                                          <div
                                              class="row align-content-between"
                                              style="width: 100%; height: 100%"
                                          >
                                              <div class="col-12">
                                                  <div class="d-flex flex-wrap">
                                                      <div class="col-4" v-for="(photo, index) in images">
                                                          <div
                                                              class="dropzone-previews position-relative mb-2"
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
                                                                              @click="showPhoto = photo.webp"
                                                                          >
                                                                              <img
                                                                                  data-dz-thumbnail
                                                                                  :src="photo.webp"
                                                                                  class="avatar-sm rounded bg-light"
                                                                                  @error="src = './images/img-1.png'"
                                                                              />
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
                                                    'btn-danger text-muted dropzone-close',
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
                                                          <div class="text-center">
                                                              <a download class="btn btn-danger" :href="photo.webp">{{ $t('general.download') }}</a>
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
                                                  <b-button variant="success" class="mx-1" disabled v-else>
                                                      <b-spinner small></b-spinner>
                                                      <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                                  </b-button>
                                              </div>
                                          </div>
                                      </div>
                                      <div class="col-md-4">
                                          <div class="show-dropzone">
                                              <img
                                                  :src="showPhoto"
                                                  class="img-thumbnail"
                                                  @error="src = './images/img-1.png'"
                                              />
                                          </div>
                                      </div>
                                  </div>
                              </b-tab>
                          </b-tabs>
                      </div>
                  </form>
              </b-modal>
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

              <table
                class="table table-borderless table-hover table-centered m-0"
              >
                <thead>
                  <tr>
                    <th
                      scope="col"
                      style="width: 0"
                      v-if="enabled3"
                      class="do-not-print"
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

                    <th v-if="setting.employee_id">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("employee") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="onlineRequests.sort(sortString('name_e'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="onlineRequests.sort(sortString('-name_e'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.request_types_id">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("request_type") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="onlineRequests.sort(sortString('name'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="onlineRequests.sort(sortString('-name'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.request_datetime">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("online_request_date") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="onlineRequests.sort(SortDate('end_date'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="onlineRequests.sort(SortDate('-end_date'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.start_from">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("online_request_start_from")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="onlineRequests.sort(SortDate('start_date'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="
                              onlineRequests.sort(SortDate('-start_date'))
                            "
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.end_date">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("online_request_end_date")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="onlineRequests.sort(SortDate('end_date'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="onlineRequests.sort(SortDate('-end_date'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.remark">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("online_request_remark")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="onlineRequests.sort(SortDate('end_date'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="onlineRequests.sort(SortDate('-end_date'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.amount">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("online_request_amount")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="onlineRequests.sort(SortDate('end_date'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="onlineRequests.sort(SortDate('-end_date'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.from_hour">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("online_request_from_hour")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="onlineRequests.sort(SortDate('end_date'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="onlineRequests.sort(SortDate('-end_date'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.to_hour">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("online_request_to_hour")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="onlineRequests.sort(SortDate('end_date'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="onlineRequests.sort(SortDate('-end_date'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.status_id">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("online_request_last_status")
                        }}</span>
                      </div>
                    </th>
                    <th v-if="setting.approved_date">
                          <div class="d-flex justify-content-center">
                              <span>{{ getCompanyKey("online_request_approved_date") }}</span>
                          </div>
                    </th>
                    <th v-if="setting.approved_from_date">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("online_request_approved_start_from")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="onlineRequests.sort(sortString('name'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="onlineRequests.sort(sortString('-name'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.approved_to_date">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("online_request_approved_end_date")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="onlineRequests.sort(sortString('name'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="onlineRequests.sort(sortString('-name'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.approved_from_hour">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("online_request_approved_from_hour")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="onlineRequests.sort(sortString('name'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="onlineRequests.sort(sortString('-name'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.approved_to_hour">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("online_request_approved_to_hour")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="onlineRequests.sort(sortString('name'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="onlineRequests.sort(sortString('-name'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.approved_amount">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("online_request_approved_amount")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="onlineRequests.sort(sortString('name'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="onlineRequests.sort(sortString('-name'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="enabled3" class="do-not-print">
                      {{ $t("general.Action") }}
                    </th>
                    <th v-if="enabled3" class="do-not-print">
                      <i class="fas fa-ellipsis-v"></i>
                    </th>
                  </tr>
                </thead>
                <tbody
                    v-if="onlineRequests.length > 0"
                >
                  <tr
                    @click.capture="checkRow(data.id)"
                    @dblclick.prevent="
                          isPermission('update onlineRequest hr')
                          ? $bvModal.show(`modal-edit-${data.id}`)
                          : false
                    "
                    v-for="(data, index) in onlineRequests"
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
                    <td v-if="setting.employee_id">
                      <h5 v-if="data.employee" class="m-0 font-weight-normal">
                        {{
                          $i18n.locale == "ar"
                            ? data.employee.name
                            : data.employee.name_e
                        }}
                      </h5>
                    </td>
                    <td v-if="setting.request_types_id">
                      <h5
                        v-if="data.request_types"
                        class="m-0 font-weight-normal"
                      >
                        {{
                          $i18n.locale == "ar"
                            ? data.request_types.name
                            : data.request_types.name_e
                        }}
                      </h5>
                    </td>
                    <th v-if="setting.request_datetime">
                        {{ data.request_datetime }}
                    </th>
                    <td v-if="setting.start_from">
                      <h5 class="m-0 font-weight-normal">
                        {{ data.from_date }}
                      </h5>
                    </td>
                    <td v-if="setting.end_date">
                      <h5 class="m-0 font-weight-normal">
                        {{ data.to_date }}
                      </h5>
                    </td>
                    <td v-if="setting.remark">
                      <h5 class="m-0 font-weight-normal">
                        {{ data.remark }}
                      </h5>
                    </td>
                    <td v-if="setting.amount">
                      <h5 class="m-0 font-weight-normal">{{ data.amount }}</h5>
                    </td>
                    <td v-if="setting.from_hour">
                      <h5 class="m-0 font-weight-normal">
                        {{ data.from_hour }}
                      </h5>
                    </td>
                    <td v-if="setting.to_hour">
                      <h5 class="m-0 font-weight-normal">
                        {{ data.to_hour }}
                      </h5>
                    </td>
                    <td v-if="setting.status_id">
                      <h5 v-if="data.request_status" class="m-0 font-weight-normal">
                        {{
                          $i18n.locale == "ar"
                            ? data.request_status.name
                            : data.request_status.name_e
                        }}
                      </h5>
                    </td>
                    <td v-if="setting.approved_date">
                          {{data.approved_date}}
                    </td>
                    <td v-if="setting.approved_from_date">
                      {{ data.approved_from_date }}
                    </td>
                    <td v-if="setting.approved_to_date">
                      {{ data.approved_to_date }}
                    </td>
                    <td v-if="setting.approved_from_hour">
                      {{ data.approved_from_hour }}
                    </td>
                    <td v-if="setting.approved_to_hour">
                      {{ data.approved_to_hour }}
                    </td>
                    <td v-if="setting.approved_amount">
                      {{ data.approved_amount }}
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
                            class="dropdown-item"
                            href="#"
                            v-if="isPermission('update onlineRequest hr')"
                            @click="$bvModal.show(`modal-edit-${data.id}`)"
                          >
                            <div
                              class="d-flex justify-content-between align-items-center text-black"
                            >
                                <span>{{ $t('general.edit') }}</span>
                                <i class="mdi mdi-square-edit-outline text-info"></i>
                            </div>
                          </a>
                          <a
                            class="dropdown-item text-black"
                            href="#"
                            v-if="!manager&&isPermission('delete onlineRequest hr')"
                            @click.prevent="showModule(data.id,'delete')?deleteFinancialYear(data.id):false"
                          >
                            <div
                              class="d-flex justify-content-between align-items-center text-black"
                            >
                              <span>{{ $t("general.delete") }}</span>
                              <i class="fas fa-times text-danger"></i>
                            </div>
                          </a>
                        </div>
                      </div>
                        <!--  edit   -->
                      <b-modal
                        :id="`modal-edit-${data.id}`"
                        :title="getCompanyKey('request_view_form')"
                        title-class="font-18"
                        body-class="p-4"
                        dialog-class="modal-full-width"
                        :ref="`edit-${data.id}`"
                        :hide-footer="true"
                        @show="resetModalEdit(data.id)"
                        @hidden="resetModalHiddenEdit(data.id)"
                      >
                        <form>
                            <div class="card">
                                <div class="card-body">
                                    <div class="mt-1 d-flex justify-content-end">
                                        <!-- Emulate built in modal footer ok and cancel button actions -->
                                        <b-button
                                            variant="success"
                                            type="submit"
                                            class="mx-1"
                                            :disabled="isUpdate"
                                            v-if="!isLoader"
                                            @click.prevent="editSubmit(data.id)"
                                        >
                                            {{ $t("general.Edit") }}
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

                                        <b-button
                                            variant="danger"
                                            type="button"
                                            @click.prevent="
                                $bvModal.hide(`modal-edit-${data.id}`)
                              "
                                        >
                                            {{ $t("general.Cancel") }}
                                        </b-button>
                                    </div>
                                </div>
                                <b-tabs nav-class="nav-tabs nav-bordered">
                                    <b-tab :title="$t('general.DataEntry')" active>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group position-relative">
                                                    <label class="control-label">
                                                        {{ getCompanyKey("employee") }}
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <multiselect
                                                        :disabled="true"
                                                        v-model="edit.employee_id"
                                                        :options="employees.map((type) => type.id)"
                                                        :custom-label="
                                                (opt) =>
                                                  employees.find((x) => x.id == opt).name
                                              "
                                                    >
                                                    </multiselect>
                                                    <div
                                                        v-if="
                                    $v.edit.request_types_id.$error ||
                                    errors.request_types_id
                                  "
                                                        class="text-danger"
                                                    >
                                                        {{ $t("general.fieldIsRequired") }}
                                                    </div>
                                                    <template v-if="errors.request_types_id">
                                                        <ErrorMessage
                                                            v-for="(
                                      errorMessage, index
                                    ) in errors.request_types_id"
                                                            :key="index"
                                                        >{{ errorMessage }}
                                                        </ErrorMessage>
                                                    </template>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group position-relative">
                                                    <label class="control-label">
                                                        {{ getCompanyKey("request_type") }}
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <multiselect
                                                        :disabled="manager"
                                                        v-model="edit.request_types_id"
                                                        :options="requestTypes.map((type) => type.id)"
                                                        :custom-label="
                                    (opt) =>
                                      requestTypes.find((x) => x.id == opt).name
                                  "
                                                    >
                                                    </multiselect>
                                                    <div
                                                        v-if="
                                    $v.edit.request_types_id.$error ||
                                    errors.request_types_id
                                  "
                                                        class="text-danger"
                                                    >
                                                        {{ $t("general.fieldIsRequired") }}
                                                    </div>
                                                    <template v-if="errors.request_types_id">
                                                        <ErrorMessage
                                                            v-for="(
                                      errorMessage, index
                                    ) in errors.request_types_id"
                                                            :key="index"
                                                        >{{ errorMessage }}
                                                        </ErrorMessage>
                                                    </template>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">
                                                        {{ getCompanyKey("online_request_date") }}
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <date-picker
                                                        :disabled="true"
                                                        v-model="edit.custom_request_datetime"
                                                        type="datetime"
                                                        :default-value="edit.custom_request_datetime"
                                                        @change="date"
                                                        confirm
                                                    ></date-picker>
                                                    <div
                                                        v-if="!$v.edit.request_datetime.required"
                                                        class="invalid-feedback"
                                                    >
                                                        {{ $t("general.fieldIsRequired") }}
                                                    </div>
                                                    <template v-if="errors.request_datetime">
                                                        <ErrorMessage
                                                            v-for="(errorMessage, index) in errors.request_datetime"
                                                            :key="index"
                                                        >{{ errorMessage }}</ErrorMessage
                                                        >
                                                    </template>
                                                </div>
                                            </div>
                                            <div
                                                class="col-md-4"
                                                v-if="
                                        !edit.request_types_id ||
                                        (edit.request_types_id &&
                                          getRequestType(edit.request_types_id)
                                            .start_from)
                                      "
                                            >
                                                <div class="form-group">
                                                    <label class="control-label">
                                                        {{ getCompanyKey("online_request_start_from") }}
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <date-picker
                                                        :disabled="manager"
                                                        v-model="edit.from_date"
                                                        type="date"
                                                        format="YYYY-MM-DD"
                                                        valueType="format"
                                                        confirm
                                                    ></date-picker>
                                                    <div
                                                        v-if="!$v.edit.from_date.required"
                                                        class="invalid-feedback"
                                                    >
                                                        {{ $t("general.fieldIsRequired") }}
                                                    </div>
                                                    <template v-if="errors.from_date">
                                                        <ErrorMessage
                                                            v-for="(errorMessage, index) in errors.from_date"
                                                            :key="index"
                                                        >{{ errorMessage }}</ErrorMessage
                                                        >
                                                    </template>
                                                </div>
                                            </div>
                                            <div
                                                class="col-md-4"
                                                v-if="
                                        !edit.request_types_id ||
                                        (edit.request_types_id &&
                                          getRequestType(edit.request_types_id).end_date)
                                      "
                                            >
                                                <div class="form-group">
                                                    <label class="control-label">
                                                        {{ getCompanyKey("online_request_end_date") }}
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <date-picker
                                                        :disabled="manager"
                                                        v-model="edit.to_date"
                                                        type="date"
                                                        format="YYYY-MM-DD"
                                                        valueType="format"
                                                        confirm
                                                    ></date-picker>
                                                    <div
                                                        v-if="!$v.edit.to_date.required"
                                                        class="invalid-feedback"
                                                    >
                                                        {{ $t("general.fieldIsRequired") }}
                                                    </div>
                                                    <template v-if="errors.to_date">
                                                        <ErrorMessage
                                                            v-for="(errorMessage, index) in errors.to_date"
                                                            :key="index"
                                                        >{{ errorMessage }}</ErrorMessage
                                                        >
                                                    </template>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">
                                                        {{ getCompanyKey("online_request_remark") }}
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input
                                                        :disabled="manager"
                                                        type="text"
                                                        v-model="edit.remark"
                                                        class="form-control"
                                                    />
                                                </div>
                                            </div>
                                            <div
                                                class="col-md-4"
                                                v-if="
                                        !edit.request_types_id ||
                                        (edit.request_types_id &&
                                          getRequestType(edit.request_types_id).amount)
                                      "
                                            >
                                                <div class="form-group">
                                                    <label class="control-label">
                                                        {{ getCompanyKey("online_request_amount") }}
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input
                                                        :disabled="manager"
                                                        type="number"
                                                        v-model="edit.amount"
                                                        class="form-control"
                                                    />
                                                </div>
                                            </div>
                                            <div
                                                class="col-md-4"
                                                v-if="
                                    !edit.request_types_id ||
                                    (edit.request_types_id &&
                                      getRequestType(edit.request_types_id)
                                        .from_hour)
                                  "
                                            >
                                                <div class="form-group">
                                                    <label class="control-label">
                                                        {{ getCompanyKey("online_request_from_hour") }}
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <date-picker
                                                        format="hh:mm A"
                                                        :disabled="manager"
                                                        v-model="edit.custom_from_hour"
                                                        type="time"
                                                        :default-value="edit.custom_from_hour"
                                                        @change="from_hour"
                                                        confirm
                                                    ></date-picker>
                                                    <div
                                                        v-if="!$v.edit.from_hour.required"
                                                        class="invalid-feedback"
                                                    >
                                                        {{ $t("general.fieldIsRequired") }}
                                                    </div>
                                                    <template v-if="errors.from_hour">
                                                        <ErrorMessage
                                                            v-for="(errorMessage, index) in errors.from_hour"
                                                            :key="index"
                                                        >{{ errorMessage }}</ErrorMessage
                                                        >
                                                    </template>
                                                </div>
                                            </div>
                                            <div
                                                class="col-md-4"
                                                v-if="
                                    !edit.request_types_id ||
                                    (edit.request_types_id &&
                                      getRequestType(edit.request_types_id).to_hour)
                                  "
                                            >
                                                <div class="form-group">
                                                    <label class="control-label">
                                                        {{ getCompanyKey("online_request_to_hour") }}
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <date-picker
                                                        format="hh:mm A"
                                                        :disabled="manager"
                                                        v-model="edit.custom_to_hour"
                                                        type="time"
                                                        :default-value="edit.custom_to_hour"
                                                        @change="to_hour"
                                                        confirm
                                                    ></date-picker>
                                                    <div
                                                        v-if="!$v.edit.to_hour.required"
                                                        class="invalid-feedback"
                                                    >
                                                        {{ $t("general.fieldIsRequired") }}
                                                    </div>
                                                    <template v-if="errors.to_hour">
                                                        <ErrorMessage
                                                            v-for="(errorMessage, index) in errors.to_hour"
                                                            :key="index"
                                                        >{{ errorMessage }}</ErrorMessage
                                                        >
                                                    </template>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group position-relative">
                                                    <label class="control-label">
                                                        {{
                                                            getCompanyKey("online_request_last_status")
                                                        }}
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <multiselect
                                                        :disabled="isUserManager"
                                                        v-model="edit.request_status_id"
                                                        :options="statuses.map((type) => type.id)"
                                                        :custom-label="
                                                        (opt) => $i18n.locale == 'ar' ?statuses.find((x) => x.id == opt).name : statuses.find((x) => x.id == opt).name_e
                                                      "
                                                    >
                                                    </multiselect>
                                                    <div
                                                        v-if="
                                    $v.edit.request_status_id.$error || errors.request_status_id
                                  "
                                                        class="text-danger"
                                                    >
                                                        {{ $t("general.fieldIsRequired") }}
                                                    </div>
                                                    <template v-if="errors.request_status_id">
                                                        <ErrorMessage
                                                            v-for="(
                                      errorMessage, index
                                    ) in errors.request_status_id"
                                                            :key="index"
                                                        >{{ errorMessage }}
                                                        </ErrorMessage>
                                                    </template>
                                                </div>
                                            </div>
                                            <div class="col-md-4" v-if="data.approved">
                                                <div class="form-group position-relative">
                                                    <label class="control-label">
                                                        {{ getCompanyKey("online_request_approved_by") }}
                                                    </label>
                                                    <input
                                                        disabled
                                                        type="text"
                                                        class="form-control"
                                                        :value="$i18n.locale == 'ar'?data.approved.name:data.approved.name_e"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <hr style="
                        margin: 10px 0 !important;
                        border-top: 1px solid rgb(141 163 159 / 42%);
                      "/>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">
                                                        {{ getCompanyKey("online_request_approved_date") }}
                                                    </label>
                                                    <date-picker
                                                        :disabled="true"
                                                        v-model="edit.approved_date"
                                                        format="YYYY-MM-DD"
                                                        valueType="format"
                                                        type="date"
                                                        confirm
                                                    ></date-picker>
                                                    <div
                                                        v-if="!$v.edit.approved_date.required"
                                                        class="invalid-feedback"
                                                    >
                                                        {{ $t("general.fieldIsRequired") }}
                                                    </div>
                                                    <template v-if="errors.approved_date">
                                                        <ErrorMessage
                                                            v-for="(errorMessage, index) in errors.approved_date"
                                                            :key="index"
                                                        >{{ errorMessage }}</ErrorMessage
                                                        >
                                                    </template>
                                                </div>
                                            </div>
                                            <div
                                                class="col-md-4"
                                                v-if="
                                        !edit.request_types_id ||
                                        (edit.request_types_id &&
                                          getRequestType(edit.request_types_id).start_from)
                                      "
                                            >
                                                <div class="form-group">
                                                    <label class="control-label">
                                                        {{
                                                            getCompanyKey("online_request_approved_start_from")
                                                        }}
                                                    </label>
                                                    <date-picker
                                                        :disabled="isUserManager"
                                                        v-model="edit.approved_from_date"
                                                        type="date"
                                                        format="YYYY-MM-DD"
                                                        valueType="format"
                                                        confirm
                                                    ></date-picker>
                                                    <div
                                                        v-if="!$v.edit.approved_from_date.required"
                                                        class="invalid-feedback"
                                                    >
                                                        {{ $t("general.fieldIsRequired") }}
                                                    </div>
                                                    <template v-if="errors.approved_from_date">
                                                        <ErrorMessage
                                                            v-for="(
                            errorMessage, index
                          ) in errors.approved_from_date"
                                                            :key="index"
                                                        >{{ errorMessage }}</ErrorMessage
                                                        >
                                                    </template>
                                                </div>
                                            </div>
                                            <div
                                                class="col-md-4"
                                                v-if="
                                        !edit.request_types_id ||
                                        (edit.request_types_id &&
                                          getRequestType(edit.request_types_id).end_date)
                                      "
                                            >
                                                <div class="form-group">
                                                    <label class="control-label">
                                                        {{ getCompanyKey("online_request_approved_end_date") }}
                                                    </label>
                                                    <date-picker
                                                        :disabled="isUserManager"
                                                        v-model="edit.approved_to_date"
                                                        format="YYYY-MM-DD"
                                                        valueType="format"
                                                        type="date"
                                                        confirm
                                                    ></date-picker>
                                                    <div
                                                        v-if="!$v.edit.approved_to_date.required"
                                                        class="invalid-feedback"
                                                    >
                                                        {{ $t("general.fieldIsRequired") }}
                                                    </div>
                                                    <template v-if="errors.approved_to_date">
                                                        <ErrorMessage
                                                            v-for="(
                            errorMessage, index
                          ) in errors.approved_to_date"
                                                            :key="index"
                                                        >{{ errorMessage }}</ErrorMessage
                                                        >
                                                    </template>
                                                </div>
                                            </div>
                                            <div
                                                class="col-md-4"
                                                v-if="
                                    !edit.request_types_id ||
                                    (edit.request_types_id &&
                                      getRequestType(edit.request_types_id)
                                        .from_hour)
                                  "
                                            >
                                                <div class="form-group">
                                                    <label class="control-label">
                                                        {{ getCompanyKey("online_request_approved_from_hour") }}
                                                    </label>
                                                    <date-picker
                                                        format="hh:mm A"
                                                        :disabled="isUserManager"
                                                        v-model="edit.custom_approved_from_hour"
                                                        type="time"
                                                        :default-value="edit.custom_approved_from_hour"
                                                        @change="approved_from_hour"
                                                        confirm
                                                    ></date-picker>
                                                    <div
                                                        v-if="!$v.edit.approved_from_hour.required"
                                                        class="invalid-feedback"
                                                    >
                                                        {{ $t("general.fieldIsRequired") }}
                                                    </div>
                                                    <template v-if="errors.approved_from_hour">
                                                        <ErrorMessage
                                                            v-for="(
                            errorMessage, index
                          ) in errors.approved_from_hour"
                                                            :key="index"
                                                        >{{ errorMessage }}</ErrorMessage
                                                        >
                                                    </template>
                                                </div>
                                            </div>
                                            <div
                                                class="col-md-4"
                                                v-if="
                                    !edit.request_types_id ||
                                    (edit.request_types_id &&
                                      getRequestType(edit.request_types_id).to_hour)
                                  "
                                            >
                                                <div class="form-group">
                                                    <label class="control-label">
                                                        {{ getCompanyKey("online_request_approved_to_hour") }}
                                                    </label>
                                                    <date-picker
                                                        format="hh:mm A"
                                                        :disabled="isUserManager"
                                                        v-model="edit.custom_approved_to_hour"
                                                        type="time"
                                                        :default-value="edit.custom_approved_to_hour"
                                                        @change="approved_to_hour"
                                                        confirm
                                                    ></date-picker>
                                                    <div
                                                        v-if="!$v.edit.approved_to_hour.required"
                                                        class="invalid-feedback"
                                                    >
                                                        {{ $t("general.fieldIsRequired") }}
                                                    </div>
                                                    <template v-if="errors.approved_to_hour">
                                                        <ErrorMessage
                                                            v-for="(
                            errorMessage, index
                          ) in errors.approved_to_hour"
                                                            :key="index"
                                                        >{{ errorMessage }}</ErrorMessage
                                                        >
                                                    </template>
                                                </div>
                                            </div>
                                            <div
                                                class="col-md-4"
                                                v-if="
                                        !edit.request_types_id ||
                                        (edit.request_types_id &&
                                          getRequestType(edit.request_types_id).amount)
                                      "
                                            >
                                                <div class="form-group">
                                                    <label class="control-label">
                                                        {{getCompanyKey("online_request_approved_amount") }}
                                                    </label>
                                                    <input
                                                        format="hh:mm A"
                                                        :disabled="isUserManager"
                                                        type="number"
                                                        v-model="edit.approved_amount"
                                                        class="form-control"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </b-tab>
                                    <b-tab :title="$t('general.ImageUploads')">
                                        <div class="row">
                                            <input
                                                accept="image/png, image/gif, image/jpeg, image/jpg"
                                                type="file"
                                                id="uploadImageEdit"
                                                @change.prevent="onImageChanged"
                                                class="input-file-upload position-absolute d-none"
                                            />
                                            <div class="col-md-8 my-1">
                                                <!-- file upload -->
                                                <div
                                                    class="row align-content-between"
                                                    style="width: 100%; height: 100%"
                                                >
                                                    <div class="col-12">
                                                        <div class="d-flex flex-wrap">
                                                            <div class="col-4" v-for="(photo, index) in images">
                                                                <div
                                                                    class="dropzone-previews position-relative mb-2"
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
                                                                                    @click="showPhoto = photo.webp"
                                                                                >
                                                                                    <img
                                                                                        data-dz-thumbnail
                                                                                        :src="photo.webp"
                                                                                        class="avatar-sm rounded bg-light"
                                                                                        @error="src = './images/img-1.png'"
                                                                                    />
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
                                                    'btn-danger text-muted dropzone-close',
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
                                                                <div class="text-center">
                                                                    <a download class="btn btn-danger" :href="photo.webp">{{ $t('general.download') }}</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="footer-image col-12">
                                                        <b-button
                                                            @click="changePhotoEdit"
                                                            :disabled="isUpdate"
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
                                                <div class="show-dropzone">
                                                    <img
                                                        :src="showPhoto"
                                                        class="img-thumbnail"
                                                        @error="src = './images/img-1.png'"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </b-tab>
                                </b-tabs>
                            </div>
                        </form>
                      </b-modal>
                      <!--  /edit   -->
                    </td>
                    <td v-if="enabled3" class="do-not-print">
                      <button
                        @mousemove="log(data.id)"
                        @mouseover="log(data.id)"
                        type="button"
                        class="btn"
                        :id="`tooltip-${data.id}`"
                        :data-placement="
                          $i18n.locale == 'en' ? 'left' : 'right'
                        "
                        :title="Tooltip"
                      >
                        <i class="fe-info" style="font-size: 22px"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
                <tbody v-else>
                  <tr>
                    <th class="text-center" colspan="20">
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
.line {
    border-bottom: 1px solid #fff !important;
    margin-top: 0.5rem !important;
    margin-bottom: 0.5rem !important;
}
table {
    td,
    th {
        white-space: nowrap;
    }
}
.modal-dialog .card {
    margin: 0 !important;
}

.bankAccount.modal-body {
    padding: 0 !important;
}

.modal-dialog .card-body {
    padding: 1.5rem 1.5rem 0 1.5rem !important;
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
</style>
