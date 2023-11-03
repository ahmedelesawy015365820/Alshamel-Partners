<template>
    <div class="arrow-sort">
        <i
            class="fas fa-arrow-up"
            @click="sortTable(col)"
        ></i>
        <i
            class="fas fa-arrow-down"
            @click="sortTable('-'+col)"
        ></i>
    </div>
</template>

<script>
import { dynamicSortString } from "../../../../helper/tableSort";

export default {
    name: "sortStringConponent",
    props: ['tables','col'],
    methods:{
        sortString(value) {
            return dynamicSortString(value);
        },
        sortTable(name){
            this.$emit('sortData',this.tables.sort(this.sortString(name)));
        }
    }
}
</script>

<style scoped>

</style>
