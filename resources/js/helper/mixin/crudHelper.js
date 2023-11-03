import adminApi from "../../api/adminAxios";
import successError from "./success&error";
import Swal from "sweetalert2";
import { formatDateOnly } from "../startDate";
import { dynamicSortNumber, dynamicSortString } from "../tableSort";
export default {
    data() {
        return {
            objPagination: {},
            tables: [],
            current_page: 1,
            Tooltip: "",
            mouseEnter: null,
            checkAll: [],
            isLoader: false,
            per_page: 50,
            type: '',
            idEdit: null,
            enabled3: true,
            printLoading: true,
            company_id: null,
            printObj: {
                id: "printCustom",
            }
        }
    },
    mixins: [successError],
    methods: {
        /**
         *  start get Data countrie && pagination
         */
        getData(page = 1, url, filter = null, params = null) {
            this.isLoader = true;
            adminApi
                .get(
                    `${url}?page=${page}&per_page=${this.per_page}${this.searchMain ? `&search=${this.searchMain}${params ? params : ""}` : ''}${this.searchField.length > 0 ? `&${filter}` : ''}`
                )
                .then((res) => {
                    let l = res.data;
                    this.tables = l.data;
                    this.objPagination = l.pagination;
                    this.current_page = l.pagination.current_page;
                    this.idEdit = null;
                })
                .catch((err) => {
                    this.errorFun('Error', 'Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        getDataCurrentPage(page = 1, params = null) {
            if (
                this.current_page <= this.objPagination.last_page &&
                this.current_page != this.objPagination.current_page &&
                this.current_page
            ) {
                this.getData(page, this.url, null, params);
            }
        },
        /***  start delete */
        deleteRow(id, url) {
            if (Array.isArray(id)) {
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
                            .post(`${url}/bulk-delete`, { ids: id })
                            .then((res) => {
                                this.checkAll = [];
                                this.getData(1, this.url);
                                this.successFun('Yourrowhasbeendeleted', 'Deleted');
                            })
                            .catch((err) => {
                                if (err.response.status == 400) {
                                    this.errorFun('Error', 'CantDeleteRelation');
                                } else {
                                    this.errorFun('Error', 'Thereisanerrorinthesystem');
                                }
                            })
                            .finally(() => {
                                this.isLoader = false;
                            });
                    }
                });
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
                            .delete(`${url}/${id}`)
                            .then((res) => {
                                this.checkAll = [];
                                this.getData(1, this.url);
                                this.successFun('Yourrowhasbeendeleted', 'Deleted');
                            })

                            .catch((err) => {
                                if (err.response.status == 400) {
                                    this.errorFun('Error', 'CantDeleteRelation');
                                } else {
                                    this.errorFun('Error', 'Thereisanerrorinthesystem');
                                }
                            })
                            .finally(() => {
                                this.isLoader = false;
                            });
                    }
                });
            }
        },
        /***  end delete */
        /***  start Excel*/
        ExportExcel(type, fn, dl) {
            this.enabled3 = false;
            setTimeout(() => {
                let elt = this.$refs.exportable_table;
                let wb = XLSX.utils.table_to_book(elt, { sheet: "Sheet JS" });
                if (dl) {
                    XLSX.write(wb, {
                        bookType: type,
                        bookSST: true,
                        type: "base64",
                    });
                } else {
                    XLSX.writeFile(
                        wb,
                        fn || ("SheetTableExport.") + (type || "xlsx")
                    );
                }
                this.enabled3 = true;
            }, 100);
        },
        /***  end Excel */
        /***  start log*/
        log(id, url) {
            if (this.mouseEnter != id) {
                this.Tooltip = "";
                this.mouseEnter = id;
                adminApi
                    .get(`${url}/logs/${id}`)
                    .then((res) => {
                        let l = res.data.data;
                        l.forEach((e) => {
                            this.Tooltip += `Created By: ${e.causer_type}; Event: ${e.event
                                }; Description: ${e.description} ;Created At: ${this.formatDate(
                                    e.created_at
                                )} \n`;
                        });
                        $(`#tooltip-${id}`).tooltip();
                    })
                    .catch((err) => {
                        this.errorFun('Error', 'Thereisanerrorinthesystem');
                    });
            }
        },
        /***  end log*/
        formatDate(value) {
            return formatDateOnly(value);
        },
        /***  start  dynamicSortString*/
        sortString(value) {
            return dynamicSortString(value);
        },
        SortNumber(value) {
            return dynamicSortNumber(value);
        },
        /***  end  dynamicSortString*/
        /***  start  ckeckRow*/
        checkRow(id) {
            if (!this.checkAll.includes(id)) {
                this.checkAll.push(id);
            } else {
                let index = this.checkAll.indexOf(id);
                this.checkAll.splice(index, 1);
            }
        },
        /***  end  ckeckRow*/
        changeType({ typeAction, id }) {
            console.log(typeAction,id)
            this.type = typeAction;
            this.idEdit = id;
        },
        dbClickRow(id) {
            this.type = 'edit';
            this.idEdit = id;
            this.$bvModal.show(`create`);
        }
    },
}
