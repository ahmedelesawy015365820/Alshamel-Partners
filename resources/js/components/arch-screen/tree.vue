<template>
  <draggable class="dragArea" tag="div" :list="nodes">
    <!-- (node.children && node.children.length) ||
        (node.arch_documents && node.arch_documents.length) ||
        (node.key && node.key.length) -->

    <div class="tree-container" v-for="(node, index) in nodes" :key="index" :style="
      $i18n.locale == 'ar'
        ? 'margin-right:10px; text-align: right;'
        : 'margin-left:10px; text-align: left; '
    " :class="[$i18n.locale == 'ar' ? 'dir-node' : '', 'node']">
      <span v-if="node.is_key||node.arch_documents||node.key" class="type" @click="nodeClicked(node)"><i :class="isExpanded(node) ? 'fas fa-minus' : 'fas fa-plus'"></i></span>
      <span :class="{ active: node == currentNode && (depth > 1 && !node.parent_id) }" style="cursor:pointer"
        class="title-tree" @click="onNodeSelected(node)" @dblclick="onDoubleClicked(node)">
        {{
          node.doc_field_id
          ? $i18n.locale == "ar"
            ? node.doc_field_id.name
            : node.doc_field_id.name_e
          : $i18n.locale == "ar"
            ? (typeof node.name === 'object' ? ($i18n.locale == "ar" ? node.name.name : node.name.name_e) : node.name)
            : (typeof node.name_e === 'object' ? ($i18n.locale == "ar" ? node.name_e.name : node.name_e.name_e) : node.name_e)
        }}
        <!-- <span v-if="depth > 1 && !node.parent_id">({{ node.files_count }})</span> -->
      </span>
      <TreeBrowser v-if="
        isExpanded(node) && (node.children || (node.arch_documents && node.arch_documents.length) || (node.key && node.key.length))
      " :nodes="getAppropriateNodes(node)" :depth="depth + 1" @onClick="(node) => $emit('onClick', node)"
        @nodeExpanded="(node) => $emit('nodeExpanded', node)"
        @onDoubleClicked="(node) => $emit('onDoubleClicked', node)" />
    </div>
  </draggable>
</template>

<script>
import draggable from "vuedraggable";
import transMixinComp from "../../helper/mixin/translation-comp-mixin";

export default {
  name: "TreeBrowser",
  mixins: [transMixinComp],
  props: {
    currentNode: Object,
    nodes: Array,
    depth: {
      type: Number,
      default: 0,
    },
    companyKeys: Array,
    defaultsKeys: Array,
  },
  data() {
    return {
      expanded: [],
    };
  },
  methods: {
    getAppropriateNodes(node) {
      if (node.arch_documents && node.arch_documents.length) {
        return node.arch_documents;
      }
      else if (node.key && node.key.length) {
        return node.key;
      }
      else {
        return node.children
      }
    },
    onNodeSelected(node) {
      // if (this.depth > 1 && !node.parent_id) {
      this.$emit("onClick", node);
      // }
    },
    isExpanded(node) {
      return this.expanded.indexOf(node) !== -1;
    },
    onDoubleClicked(node) {
      if (this.depth >= 1 && node.parent_id === null) {
        this.$emit("onDoubleClicked", node);
      }
    },
    nodeClicked(node) {
      if (!this.isExpanded(node)) {
        if (node.is_key) {
          this.$emit("nodeExpanded", { node: node, expanded: this.expanded });
        }
        else {
          this.expanded.push(node);
        }
      } else {
        this.expanded.splice(this.expanded.indexOf(node));
      }
    },
  },
  components: {
    draggable,
  },
  computed: {},
};
</script>

<style scoped>
.title-tree {
  font-size: 12px !important;
  font-weight: 500;
}

span.type i {
  font-size: 10px;
}

.type {
  margin-right: 10px;
}

/*ali*/

.node {
  --spacing: 1.5rem;
  --radius: 10px;
}

.node .node {
  display: block;
  position: relative;
  padding-left: calc(1.5 * var(--spacing) - var(--radius) - 2px);
}

.node .dragArea {
  margin-left: calc(var(--radius) - var(--spacing));
  padding-left: 0;
}

.node .dragArea .node {
  border-left: 2px solid #ddd;
}

.node .dragArea .node.dir-node {
  border-right: 2px solid #ddd;
  border-left: unset;
}

.node .dragArea .node:last-child {
  border-color: transparent;
}

.node .dragArea .node::before {
  content: "";
  display: block;
  position: absolute;
  top: calc(var(--spacing) / -2);
  left: -2px;
  width: calc(var(--spacing) + 2px);
  height: calc(var(--spacing) + 5px);
  border: solid #ddd;
  border-width: 0 0 2px 2px;
}

.node .dragArea .node.dir-node::before {
  content: "";
  display: block;
  position: absolute;
  top: calc(var(--spacing) / -2);
  right: -2px;
  width: 12px;
  height: calc(var(--spacing) + 5px);
  border: solid #ddd;
  border-width: 0 2px 2px 0;
  left: unset;
}

i {
  background-color: #3bafda;
  color: snow;
  border-radius: 50%;
  padding: 3px;
  font-size: 12px;
  margin: 0px;
  position: relative;
  z-index: 1;
}

.active {
  color: #159a80 !important;
}

span,
.tree-container {
  white-space: nowrap;
}
</style>
