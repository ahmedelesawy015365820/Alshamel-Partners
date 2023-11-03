export default {
  methods: {
    getCompanyKey(key) {
      let returnedKey = null;
      for (let _key in this.companyKeysFun) {
        if (_key == key) {
          returnedKey =
            this.$i18n.locale == "ar"
              ? this.companyKeysFun[_key].new_ar
              : this.companyKeysFun[_key].new_en;
          return returnedKey;
        }
      }
      for (let _key in this.defaultsKeysFun) {
        if (_key == key) {
          returnedKey =
            this.$i18n.locale == "ar"
              ? this.defaultsKeysFun[_key].default_ar
              : this.defaultsKeysFun[_key].default_en;
          return returnedKey;
        }
      }
    },
    getKeyInfo(key) {
      let keyInfo = null;
      for (let _key in this.companyKeysFun) {
        if (_key == key) {
          keyInfo = this.companyKeysFun[_key];
          return keyInfo;
        }
      }
      return keyInfo;
    },
    getCompanyKeyLang(key, lang) {
      let returnedKey = null;
      for (let _key in this.companyKeysFun) {
        if (_key == key) {
          returnedKey =
            lang == "ar"
              ? this.companyKeysFun[_key].new_ar
              : this.companyKeysFun[_key].new_en;
          return returnedKey;
        }
      }
      for (let _key in this.defaultsKeysFun) {
        if (_key == key) {
          returnedKey =
            lang == "ar"
              ? this.defaultsKeysFun[_key].default_ar
              : this.defaultsKeysFun[_key].default_en;
          return returnedKey;
        }
      }
    },
  },
  computed: {
      companyKeysFun(){
          return this.$store.getters['translation/companyKeys'];
      },
      defaultsKeysFun(){
          return this.$store.getters['translation/defaultsKeys'];
      }
  }
}
