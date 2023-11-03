<template>
    <div>
        <b-modal
            id="branch"
            :title="$t('general.choose_branch')"
            title-class="font-18"
            :hide-footer="true"
        >
            <div class="list-group">
                <a
                    v-for="item in branches"
                    href=""
                    @click.prevent="selectBranch(item)"
                    :key="item.id"
                    :class="{ active: selectedBranch && selectedBranch.id == item.id }"
                    class="list-group-item list-group-item-action"
                >
                    {{ $i18n.locale == "ar" ? item.name : item.name_e }}
                </a>
            </div>
        </b-modal>
        <b-modal
            id="page"
            :title="$t('general.choose_Page')"
            title-class="font-18"
            :hide-footer="true"
        >
            <div class="list-group">
                <template v-if="type != 'purchase'">
                    <router-link
                        to="/dashboard/point-of-sell/sales"
                        :class="['list-group-item list-group-item-action',$route.name == 'POS sales'? 'active': '']"
                    >
                        {{$t('general.Sales')}}
                    </router-link>
                    <router-link
                        to="/dashboard/point-of-sell/return"
                        :class="['list-group-item list-group-item-action',$route.name == 'POS return'? 'active': '']"
                    >
                        {{$t('general.returnOrder')}}
                    </router-link>
                </template>
                <template v-else>
                    <router-link
                        to="/dashboard/point-of-sell/purchase"
                        :class="['list-group-item list-group-item-action',$route.name == 'POS purchase'? 'active': '']"
                    >
                        {{$t('general.Purchase')}}
                    </router-link>
                    <router-link
                        to="/dashboard/point-of-sell/purchase-return"
                        :class="['list-group-item list-group-item-action',$route.name == 'POS purchase return'? 'active': '']"
                    >
                        {{$t('general.purchaseReturn')}}
                    </router-link>
                </template>
            </div>
        </b-modal>

        <div class="nav-tabs">
            <div v-b-modal.page class="tab">
                <div>
                    <span v-if="$route.name == 'POS sales'">{{$t('general.Sales')}}</span>
                    <span v-if="$route.name == 'POS return'">{{$t('general.returnOrder')}}</span>
                    <span v-if="$route.name == 'POS purchase'">{{$t('general.Purchase')}}</span>
                    <span v-if="$route.name == 'POS purchase return'">{{$t('general.purchaseReturn')}}</span>
                    <i class="fa-solid fa-angle-down"></i>
                </div>
            </div>
            <div style="color: #60bcff"><i class="fa-solid fa-angle-right"></i></div>
            <div v-b-modal.branch class="tab">
                <template v-if="selectedBranch">
                    <span>{{ $i18n.locale == "ar" ? selectedBranch.name : selectedBranch.name_e }}</span>
                    <i class="fa-solid fa-angle-down"></i>
                </template>
                <template v-else>
                    <div
                        class="spinner-border text-light spinner-border-sm"
                        role="status"
                    >
                        <span class="sr-only">Loading...</span>
                    </div>
                </template>
            </div>
            <div style="color: #60bcff" v-if="0"><i class="fa-solid fa-angle-right"></i></div>
            <div class="last-tab" v-if="0">
                <div class="tab">
                    <span>Main Cash Register</span>
                    <i class="fa-solid fa-angle-down"></i>
                </div>
                <div class="tab square">
                    <span>{{$t('general.RegisterInfo')}}</span>
                    <i class="fa-solid fa-angle-down"></i>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import adminApi from "../../../../api/adminAxios";

export default {
    props: ['type'],
    data() {
        return {
            branches: [],
            selectedBranch: null,
        };
    },
    methods: {
        selectBranch(branch) {
            this.$emit("selectedBranch", (this.selectedBranch = branch));
        },
        async getBranches() {
            await adminApi
                .get(`/branches?products=1`)
                .then((res) => {
                    this.branches = res.data.data;

                    this.$emit(
                        "selectedBranch",
                        (this.selectedBranch = this.branches.length
                            ? this.branches[this.branches.length - 1]
                            : null)
                    );
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
    mounted() {
        this.getBranches();
    },
};
</script>
<style lang="scss" scoped>
.list-group-item {
    transition: all 0.5s;
}

.list-group-item.active {
    background-color: #3bafda;
    border-color: #3bafda;
}

.nav-tabs {
    .last-tab {
        display: flex;
        gap: 9px;
    }

    padding: 15px 23px;
    gap: 8px;
    display: flex;
    align-items: center;

    .tab {
        cursor: pointer;
        font-size: 15px;
        transition: all 0.5s;
        color: #ffffff !important;
        padding: 5px 22px !important;
        border-radius: 1rem;
        border-color: #3bafda !important;
        background-color: #3bafda !important;
        box-shadow: none !important;
        color: #fff;

        &:hover {
            color: #fff;
            background: #0a8fc0 !important;
            border-color: #0a8fc0 !important;
        }
    }

    .square {
        border-radius: 6px;
    }
}
</style>
