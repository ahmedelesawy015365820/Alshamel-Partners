export default function(vm, screen, permission) {
    if(vm.$store.state.auth.type == 'user'){
        if (vm.$store.state.auth.permissions.includes(permission)) {
            return true;
        } else {
            return vm.$router.push({ name: "home" });
        }
    }else {
        if (vm.$store.state.auth.work_flow_trees.includes(screen)) {
            return true;
        } else {
            return vm.$router.push({ name: "home" });
        }
    }
};
