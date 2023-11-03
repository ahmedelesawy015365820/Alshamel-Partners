import translationCompMixin from "./translation-comp-mixin";
export default {
  data() {
    return {
      defaultsKeys: {},
      companyKeys: {},
      filterResult: {}
    }
  },
  mixins: [translationCompMixin]
}
