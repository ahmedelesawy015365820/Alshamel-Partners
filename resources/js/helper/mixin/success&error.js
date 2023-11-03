import Swal from "sweetalert2";
export default {
    methods: {
        errorFun(Error,text) {
            Swal.fire({
                icon: "error",
                title: `${this.$t(`general.${Error}`)}`,
                text: `${this.$t(`general.${text}`)}`,
            });
        },
        successFun(text,title = null) {
            if(title){
                Swal.fire({
                    icon: "success",
                    title: `${this.$t(`general.${title}`)}`,
                    text: `${this.$t(`general.${text}`)}`,
                    showConfirmButton: false,
                    timer: 1500,
                });
            }else {
                Swal.fire({
                    icon: "success",
                    text: `${this.$t(`general.${text}`)}`,
                    showConfirmButton: false,
                    timer: 1500,
                });
            }
        }
    }
}

