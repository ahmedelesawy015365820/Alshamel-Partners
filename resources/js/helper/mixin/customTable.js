import adminApi from "../../api/adminAxios";
import successError from "./success&error.js";
export default {
    data(){
        return {
            fields: []
        }
    },
    mixins: [successError],
    methods: {
        getCustomTableFields(table_name) {
              adminApi
                .get(`/customTable/table-columns/${table_name}`)
                .then((res) => {
                    this.fields = res.data;
                })
                .catch((err) => {
                    errorFun('Error','Thereisanerrorinthesystem');
                });
        },
        isVisible(fieldName) {
            if(this.fields.length > 0){
                let res = this.fields.filter((field) => {
                    return field.column_name == fieldName;
                });
                return res.length > 0 && res[0].is_visible == 1 ? true : false;
            }
            return true;
        },
        isRequired(fieldName) {
            if(this.fields.length > 0) {
                let res = this.fields.filter((field) => {
                    return field.column_name == fieldName;
                });
                return res.length > 0 && res[0].is_required == 1 ? true : false;
            }
            return true;
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
    },
}
