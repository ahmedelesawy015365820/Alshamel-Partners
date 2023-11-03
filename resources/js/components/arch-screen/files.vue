<template>
  <div class="arch-files">
    <div class="row">
      <div
        @dblclick="$emit('onDoubleClicked', file)"
        v-for="(file, index) in archiveFiles"
        :key="file.id"
        @click="active(index, file)"
        class="col-lg-6"
      >
        <div :class="[allActive.includes(file.id) ? ' active' : '', 'file']">
          <div class="icon">
            <i class="fa fa-file-pdf"></i>
          </div>
          <div class="info">
            <div class="up-section">
              <h6>
                {{
                  file.data_type_value.length > 0
                    ? typeof file.data_type_value[0].value === "object"
                      ? file.data_type_value[0].value.name_e
                      : file.data_type_value[0].value
                    : "File"
                }}
                ({{ file.id }})
              </h6>
              <span
                :class="[
                  allActive.includes(file.id)
                    ? 'checked-circle active-circle border'
                    : '',
                ]"
              >
                <i v-if="allActive.includes(file.id)" class="fa fa-check" />
              </span>
            </div>
            <div><span class="text-danger">*</span> validated</div>
            <div>{{ formatDate(file.created_at) }}</div>
            <div>
              <a v-if="file.favourite == false" @click="favorite(file.id)">
                <i class="far fa-star" style="color: rgb(218 218 218)"></i>
              </a>
              <a v-else @click="favorite(file.id)">
                <i class="fa fa-star" style="color: rgb(254 204 0)"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { formatDateOnly } from "../../helper/startDate";
import adminApi from "../../api/adminAxios";
import Swal from "sweetalert2";
import transMixinComp from "../../helper/mixin/translation-comp-mixin";

export default {
  props: ["archiveFiles", "isActiveFile", "deleteFileId", "isSearch"],
  mixins: [transMixinComp],

  data() {
    return {
      allActive: [],
      favorite_data: {
        type: null,
        arch_archive_file_id: null,
        user_id: null,
        admin_id: null,
      },
    };
  },
  watch: {
    isActiveFile(after, befour) {
      this.allActive = [];
    },
    deleteFileId(after, befour) {
      if (this.deleteFileId) {
      }
    },
    isSearch(after, befour) {
      this.allActive = [];
      this.$store.commit("archiving/objectActiveEmity");
      this.$store.commit("archiving/archiveFileEmity");
    },
  },
  methods: {
    active(index, file) {
      if (!this.allActive.includes(file.id) && this.allActive.length < 1) {
        this.allActive.push(file.id);
      } else if (this.allActive.includes(file.id)) {
        this.allActive.splice(this.allActive.indexOf(file.id), 1);
      } else {
        this.allActive.pop();
        this.allActive.push(file.id);
      }
      this.$store.commit("archiving/objectActiveUpdate", file);
      this.$store.commit("archiving/archiveFileUpdate", file);
    },
    formatDate(value) {
      return formatDateOnly(value);
    },
    favorite(id) {
      this.favorite_data.type = this.$store.state.auth.type;
      this.favorite_data.arch_archive_file_id = id;
      this.favorite_data.user_id =
        this.$store.state.auth.type == "admin" ? null : this.$store.state.auth.user.id;
      this.favorite_data.admin_id =
        this.$store.state.auth.type == "admin" ? this.$store.state.auth.partner.id : null;
      let { type, arch_archive_file_id, user_id, admin_id } = this.favorite_data;
      adminApi
        .put(`/arch-archive-files/toggle-favourite`, {
          type,
          arch_archive_file_id,
          user_id,
          admin_id,
        })
        .then((res) => {
          let archive = this.archiveFiles.find((e) => id == e.id);
          setTimeout(() => {
            Swal.fire({
              icon: "success",
              text: `${
                archive.favourite == true
                  ? this.$t("general.AddedToFavoritesSuccessfully")
                  : this.$t("general.DeletedFromFavoritesSuccessfully")
              }`,
              showConfirmButton: false,
              timer: 1500,
            });
          }, 500);

          archive.favourite = !archive.favourite;
        })
        .catch((err) => {
          if (err.response.data) {
            Swal.fire({
              icon: "error",
              title: `${this.$t("general.Error")}`,
              text: `${this.$t("general.Thereisanerrorinthesystem")}`,
            });
          }
        });
    },
  },
};
</script>

<style scoped lang="scss">
.arch-files {
  .up-section {
    display: flex;
    justify-content: space-between;
  }

  .active-circle {
    align-items: center;
    font-size: 7px;
    color: #fff;
    background: hsl(184deg, 49%, 45%);
    display: flex !important;
    justify-content: center;
  }

  .checked-circle {
    display: inline-block;
    width: 30px;
    height: 30px;
    border-radius: 50%;
  }

  .active {
    border: 2px solid hsl(180deg 27% 68%);
  }

  .checked-circle {
    border-radius: 50%;
    width: 15px;
    height: 15px;
    position: relative;
    top: 7px;
  }

  .file {
    &:hover {
      cursor: pointer;
    }

    border-radius: 7px;
    margin: 10px 0;
    display: flex;
    background: #fff;
    box-shadow: rgba(0, 0, 0, 0.05) 0px 0px 0px 1px;

    .info {
      width: 100%;
      padding: 10px;
    }

    .icon {
      padding: 20px;
      color: hsl(6deg 37% 60%);
      font-size: 40px;
      background: hsl(200deg 15% 92%);
    }
  }
}
</style>
