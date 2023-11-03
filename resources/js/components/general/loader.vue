<template>
    <div class="d-flex justify-content-center align-items-center position-absolute loader">
        <b-spinner :class="['m-2',`loader-${size}`]" variant="success" role="status"></b-spinner>
    </div>
</template>

<script>
export default {
    name: "loader",
    props:{
        size:{
            default: 'small',
            type: String,
            validator(value){
                return ['large','small','middle'].indexOf(value) != -1;
            }
        }
    }
}
</script>

<style scoped>
    .loader {
        width: 100%;
        height: 100%;
        background-color: #fff;
        opacity: .6;
        z-index: 100;
    }
    .loader-large {
        width: 4rem;
        height: 4rem
    }
    .loader-small {
        width: 1rem;
        height: 1rem
    }
    .loader-middle {
        width: 2.25rem;
        height: 2.25rem
    }
</style>
