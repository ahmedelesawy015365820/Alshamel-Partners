<script>
import { layoutComputed } from "../../state/helpers";

/**
 * Page-header component
 */
export default {
  components: {},
  computed: {
    ...layoutComputed,
  },
};
</script>

<template>
  <!-- start page title -->
  <div class="row">
    <div class="col-12">
      <div
        class="page-title-box"
        :class="{
          'page-title-box-alt':
            layoutType !== 'vertical' && layoutType !== 'two-column',
        }"
      >
      </div>
    </div>
  </div>
  <!-- end page title -->
</template>

<style>
    .page-title-box {
        margin: 0 !important;
        padding: 0 !important;
    }
</style>
