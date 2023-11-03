<template>
    <table class="table table-borderless table-hover table-centered m-0">
        <thead>
            <tr>
                <th
                    v-if="enabled3 && isInputCheck"
                    scope="col"
                    style="width: 0"
                    class="do-not-print"
                >
                    <div class="form-check custom-control">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            style="width: 17px; height: 17px"
                            v-model="isCheckAll"
                        />
                    </div>
                </th>
                <template v-for="item in tableSetting">
                    <th v-if="item.isSetting && isVisible(item.isV)">
                        <div class="d-flex justify-content-center">
                            <span>{{ getCompanyKey(item.trans) }}</span>
                            <sortStringConponent
                                v-if="item.sort" :col="item.isV"
                                :tables="tables"
                                @sortData="(data) => tables = data"
                            />
                        </div>
                    </th>
                </template>
                <th v-if="enabled3 && isAction"  class="do-not-print">
                    {{ $t("general.Action") }}
                </th>
                <th v-if="enabled3 && isLog"  class="do-not-print">
                    <i class="fas fa-ellipsis-v"></i>
                </th>
            </tr>
        </thead>
        <tbody v-if="tables.length > 0">
            <tr
            v-for="(data, index) in tables"
            :key="data.id"
            class="body-tr-custom"
            @click.capture="$emit('checkRowTable',data.id)"
            @dblclick.prevent="
                permissionUpdate && isEdit
                ? $emit('editRow',data.id)
                : false
            "
        >
            <td v-if="enabled3 && isInputCheck"  class="do-not-print">
                <div
                    class="form-check custom-control"
                    style="min-height: 1.9em"
                >
                    <input
                        style="width: 17px; height: 17px"
                        class="form-check-input"
                        type="checkbox"
                        :value="data.id"
                        @change="$emit('checkRows',checkAll)"
                        v-model="checkAll"
                    />
                </div>
            </td>
            <template v-for="item in tableSetting">
                <td v-if="item.isSetting && isVisible(item.isV) && item.type == 'string' && !item.columnCustom">
                    <h5 class="m-0 font-weight-normal">
                        {{ data[item.isV] }}
                    </h5>
                </td>
                <td v-if="item.isSetting && isVisible(item.isV) && item.type == 'relation'">
                    {{
                        data[item.name]
                            ? $i18n.locale == "ar"
                            ? data[item.name][item.col1]
                            : data[item.name][item.col2]
                            : " - "
                    }}
                </td>
                <td v-if="item.isSetting && isVisible(item.isV) && item.type == 'relation1'">
                    {{
                        data[item.name]
                            ? data[item.name][item.name1] ? $i18n.locale == "ar"
                            ? data[item.name][item.name1][item.col1]
                            : data[item.name][item.name1][item.col2]
                            :" - "
                            : " - "
                    }}
                </td>
                <td v-if="item.isSetting && item.type == 'relationMany'">
                    <h5 v-if="data[item.name].length > 0" class="m-0 font-weight-normal">
                        <span v-for="(i,index) in data[item.name]" :key="i.id">
                            {{ $i18n.locale == "ar" ? i[item.col1] : i[item.col2]}}
                            <span> - </span>
                        </span>
                    </h5>
                </td>
                <td v-if="item.isSetting && isVisible(item.isV) && item.type == 'boolean'">
                      <span
                          :class="[
                              data[item.isV] == 'active' || data[item.isV] == 1
                                ? 'text-success'
                                : 'text-danger',
                              'badge',
                            ]"
                      >
                        {{
                              data[item.isV] == "active" || data[item.isV] == 1
                                  ? `${$t("general.Active")}`
                                  : `${$t("general.Inactive")}`
                          }}
                      </span>
                </td>
                <td v-if="item.isSetting && isVisible(item.isV) && item.type == 'boolean1'">
                      <span
                          :class="[
                              data[item.isV] == 'active' || data[item.isV] == 1 || data[item.isV]
                                ? 'text-success'
                                : 'text-danger',
                              'badge',
                            ]"
                      >
                        {{
                              data[item.isV] == "active" || data[item.isV] == 1 || data[item.isV]
                                  ? `${$t("general.Yes")}`
                                  : `${$t("general.No")}`
                          }}
                      </span>
                </td>
                <td v-if="item.isSetting && isVisible(item.isV) && item.type == 'date'">
                      {{ formatDate(data[item.isV]) }}
                </td>
                <td v-if="item.isSetting && isVisible(item.isV) && item.columnCustom == 'allowed_subscription_date'">
                    <h5 class="m-0 font-weight-normal">{{ data[item.isV].slice(3) + '-' + data[item.isV].slice(0,2) }}</h5>
                </td>
            </template>
            <td v-if="enabled3 && isAction"  class="do-not-print">
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
                            v-if="permissionUpdate && isEdit"
                            @click="$emit('editRow',data.id)"
                        >
                            <div
                                class="d-flex justify-content-between align-items-center text-black"
                            >
                                <span>{{ $t("general.edit") }}</span>
                                <i
                                    class="mdi mdi-square-edit-outline text-info"
                                ></i>
                            </div>
                        </a>
                        <a
                            v-if="permissionDelete && isDelete"
                            class="dropdown-item text-black"
                            href="#"
                            @click.prevent="$emit('delete',data.id)"
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
            </td>
            <td v-if="enabled3 && isLog"  class="do-not-print">
                <button
                    @mousemove="!isLogClick? $emit('logFire',data.id): null"
                    @mouseover="!isLogClick? $emit('logFire',data.id): null"
                    @click="isLogClick? $emit('logFire',data.id): false"
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
                <th class="text-center" colspan="30">
                    {{ $t("general.notDataFound") }}
                </th>
            </tr>
        </tbody>
    </table>
</template>

<script>
import transMixinComp from "../../helper/mixin/translation-comp-mixin";
import sortStringConponent from "./page-component/tableSort/sortStringConponent";
import {formatDateOnly} from "../../helper/startDate";

export default {
    name: "tableCustom",
    mixins: [transMixinComp],
    components: {
        sortStringConponent
    },
    data(){
        return {
            checkAll: [],
            isCheckAll: false
        }
    },
    props:[
        'tables','permissionUpdate','permissionDelete','isEdit','isDelete',
        'isVisible','tableSetting','enabled3','Tooltip','isLog','isAction',
        'isInputCheck','isLogClick'
    ],
    watch: {
        /*** watch check All table*/
        isCheckAll(after, befour) {
            if (after) {
                this.tables.forEach((el) => {
                    if (!this.checkAll.includes(el.id)) {
                        this.checkAll.push(el.id);
                    }
                });
            } else {
                this.checkAll = [];
            }
            this.$emit('checkRows',this.checkAll)
        },
    },
    methods: {
        formatDate(value) {
            return formatDateOnly(value);
        },
    }
}
</script>
