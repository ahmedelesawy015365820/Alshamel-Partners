<template>
    <div
        class="row justify-content-between align-items-center mb-2 px-1"
    >
        <div v-if="sideAction" class="col-md-3 d-flex align-items-center mb-1 mb-xl-0">
            <!-- start create and printer -->
            <b-button
                @click.prevent="$emit('actionChange',{typeAction:'create',id:null});$bvModal.show(`create`);"
                v-if="isCreate && permissionCreate"
                variant="primary"
                class="btn-sm mx-1 font-weight-bold"
            >
                {{ $t("general.Create") }}
                <i class="fas fa-plus"></i>
            </b-button>
            <div class="d-inline-flex">
                <button
                    class="custom-btn-dowonload"
                    v-if="isExcl"
                    @click="$emit('gen_xsl')"
                >
                    <i class="fas fa-file-download"></i>
                </button>
                <button v-if="isPrint" class="custom-btn-dowonload" v-print="'#printCustom'">
                    <i class="fe-printer"></i>
                </button>
                <button
                    class="custom-btn-dowonload"
                    @click.prevent="$emit('actionChange',{typeAction:'edit',id:checkAll[0]});$bvModal.show(`create`);"
                    v-if="isEdit && checkAll.length == 1 && permissionUpdate"
                >
                    <i class="mdi mdi-square-edit-outline"></i>
                </button>
                <!-- start mult delete  -->
                <button
                    class="custom-btn-dowonload"
                    v-if="isDelete && checkAll.length > 1 && permissionDelete"
                    @click.prevent="$emit('delete',checkAll)"
                >
                    <i class="fas fa-trash-alt"></i>
                </button>
                <!-- end mult delete  -->
                <!--  start one delete  -->
                <button
                    class="custom-btn-dowonload"
                    v-if="isDelete && checkAll.length == 1 && permissionDelete"
                    @click.prevent="$emit('delete',checkAll[0])"
                >
                    <i class="fas fa-trash-alt"></i>
                </button>
                <!--  end one delete  -->
            </div>
            <!-- end create and printer -->
        </div>
        <div
            v-if="sidePaginate"
            :class="`col-xs-10 col-md-9 col-lg-7 d-flex align-items-center justify-content-${isPaginate?'end':'center'}`"
        >
            <div class="d-fex">
                <!-- start filter and setting -->
                <div class="d-inline-block">
                    <b-button v-if="isFilter" @click="$bvModal.show('filter')" class="mx-1 custom-btn-background">
                        {{ $t("general.filter") }}
                        <i class="fas fa-filter"></i>
                    </b-button>
                    <b-button v-if="isGroup" class="mx-1 custom-btn-background">
                        {{ $t("general.group") }}
                        <i class="fe-menu"></i>
                    </b-button>
                    <!-- Basic dropdown -->
                    <b-dropdown
                        v-if="isSetting"
                        variant="primary"
                        :html="`${$t(
                        'general.setting'
                      )} <i class='fe-settings'></i>`"
                        ref="dropdown"
                        class="dropdown-custom-ali"
                    >
                        <template  v-for="(i,index) in setting">
                            <b-form-checkbox
                                :key="index"
                                v-model="i.setting[i.isV]"
                                v-if="isVisible(i.isV) && i.isSet"
                                @change="$emit('settingFun',setting)"
                                class="mb-1"
                            >
                                {{ getCompanyKey(i.trans) }}
                            </b-form-checkbox>
                        </template>
                        <div class="d-flex justify-content-end">
                            <a
                                href="javascript:void(0)"
                                class="btn btn-primary btn-sm"
                            >
                                {{ $t('general.Apply') }}
                            </a>
                        </div>
                    </b-dropdown>
                    <!-- Basic dropdown -->
                </div>
                <!-- end filter and setting -->

                <!-- start Pagination -->
                <div
                    v-if="isPaginate"
                    class="d-inline-flex align-items-center pagination-custom"
                >
                    <div class="d-inline-block" style="font-size: 13px">
                        {{ objPagination.from }}-{{ objPagination.to }}
                        /
                        {{ objPagination.total }}
                    </div>
                    <div class="d-inline-block">
                        <a
                            href="javascript:void(0)"
                            :style="{
                          'pointer-events':
                            parseInt(objPagination.current_page == 1) ? 'none' : '',
                        }"
                            @click.prevent="$emit('perviousOrNext',parseInt(objPagination.current_page) - 1)"
                        >
                            <span>&lt;</span>
                        </a>
                        <input
                            type="text"
                            @keyup.enter="$emit('DataCurrentPage',page)"
                            v-model="page"
                            class="pagination-current-page"
                        />
                        <a
                            href="javascript:void(0)"
                            :style="{
                          'pointer-events':
                            objPagination.last_page ==
                            parseInt(objPagination.current_page)
                              ? 'none'
                              : '',
                        }"
                            @click.prevent="$emit('perviousOrNext',parseInt(objPagination.current_page) + 1)"
                        >
                            <span>&gt;</span>
                        </a>
                    </div>
                </div>
                <!-- end Pagination -->
            </div>
        </div>
    </div>
</template>

<script>
import transMixinComp from "../../helper/mixin/translation-comp-mixin";

export default {
    name: "actionSetting",
    mixins: [transMixinComp],
    props: [
        'companyKeys','defaultsKeys','isCreate','permissionCreate','isExcl',
        'isPrint','permissionUpdate','permissionDelete','isEdit','isDelete',
        'checkAll','sideAction','sidePaginate','isFilter','isGroup','isSetting',
        'settings','isVisible','isPaginate','objPagination','current_page'
    ],
    data(){
        return {
            setting: [],
            page: 1
        }
    },
    mounted(){
        if(this.settings.length > 0){
            this.setting = this.settings;
        }
    },
    watch: {
        page(after,befour){
            if(this.page >= 1 && this.page){
                this.$emit('currentPage',this.page);
            }
        },
        current_page(){
            this.page = this.current_page;
        }
    }
}
</script>

<style scoped>

</style>
