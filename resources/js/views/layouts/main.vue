<script>
import { layoutComputed } from "../../state/helpers";

import Vertical from "./vertical";
import Horizontal from "./horizontal";
import Detached from "./detached";
import TwoColumn from "./two-column";

/**
 * Main layout
 */
export default {
  components: {
    Vertical,
    Horizontal,
    Detached,
    TwoColumn,
  },
  data() {
    return {};
  },
  computed: {
    ...layoutComputed,
  },
};
</script>

<template>
  <div>
    <!-- Begin page -->
    <Vertical>
      <slot />
    </Vertical>
    <!-- END layout-wrapper -->

<!--    <Horizontal v-if="layoutType === 'horizontal'" :layout="layoutType">-->
<!--      <slot />-->
<!--    </Horizontal>-->

<!--    <Detached>-->
<!--      <slot />-->
<!--    </Detached>-->

<!--    <TwoColumn>-->
<!--      <slot />-->
<!--    </TwoColumn>-->
  </div>
</template>
