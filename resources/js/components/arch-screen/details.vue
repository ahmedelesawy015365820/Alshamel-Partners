<template>
  <div>
    <div class="row details mt-2" v-if="$store.state.archiving.archiveFile.length == 1">
      <div class="col-md-12 mt-2">
        <div
          class="row justify-content-center align-items-center details-img position-relative"
        >
          <div class="col-4 icon">
            <i class="fa fa-file-pdf"></i>
          </div>
          <span class="chat-icon position-absolute" v-if="0">
            <i class="fas fa-comments"></i>
          </span>
        </div>
      </div>
      <!--show file-->

      <b-modal
        id="show-whatup"
        title="Send Whatsapp"
        title-class="font-18"
        body-class="p-4 "
        :hide-footer="true"
        @hidden="resetWhatsApp"
      >
        <form>
          <div class="d-flex justify-content-end">
            <button @click.prevent="getLinkWhastapp" type="button" class="btn btn-info">
              {{ $t("general.send") }}
            </button>
          </div>
          <div class="card" style="background: none !important">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="control-label">
                    {{ $t("general.phone") }}
                  </label>
                  <input
                    v-model="$v.watsApp.phone.$model"
                    type="number"
                    class="form-control"
                    :class="{
                      'is-invalid': $v.watsApp.phone.$error,
                      'is-valid': !$v.watsApp.phone.$invalid,
                    }"
                    data-create="9"
                  />
                </div>
              </div>
            </div>
          </div>
        </form>
      </b-modal>
      <b-modal
        id="show-email"
        title="Send Email"
        title-class="font-18"
        body-class="p-4 "
        :hide-footer="true"
        @hidden="resetSendEmail"
      >
        <div class="d-flex justify-content-end">
          <button type="button" class="btn btn-info">
            {{ $t("general.send") }}
          </button>
        </div>
        <form>
          <div class="card" style="background: none !important">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="control-label">
                    {{ $t("general.email") }}
                  </label>
                  <input
                    v-model="$v.sendEmail.email.$model"
                    type="email"
                    class="form-control"
                    data-create="9"
                    :class="{
                      'is-invalid': $v.sendEmail.email.$error,
                      'is-valid': !$v.sendEmail.email.$invalid,
                    }"
                  />
                </div>
              </div>
            </div>
          </div>
        </form>
      </b-modal>
      <b-modal
        id="show-notify"
        title="Show Notify"
        title-class="font-18"
        body-class="p-4 "
        :hide-footer="true"
        @show="getUsersAll"
        @hidden="resetSendNotify"
      >
        <div class="d-flex justify-content-end">
          <button
            type="button"
            v-if="!isLoader"
            @click.prevent="sendNotifyFun"
            class="btn btn-success"
          >
            {{ $t("general.send") }}
          </button>
          <b-button variant="success" class="mx-1" disabled v-else>
            <b-spinner small></b-spinner>
            <span class="sr-only">{{ $t("login.Loading") }}...</span>
          </b-button>
        </div>
        <form>
          <div class="card" style="background: none !important">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="control-label"> Title </label>
                  <input
                    type="text"
                    v-model="$v.sendNotify.title.$model"
                    class="form-control"
                    :class="{
                      'is-invalid': $v.sendNotify.title.$error,
                      'is-valid': !$v.sendNotify.title.$invalid,
                    }"
                    data-create="9"
                  />
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                  <label class="control-label"> User </label>
                  <multiselect
                    multiple
                    v-model="$v.sendNotify.users.$model"
                    :options="getUsers.map((type) => type.id)"
                    :custom-label="
                      (opt) =>
                        $i18n.locale
                          ? getUsers.find((x) => x.id == opt).name
                          : getUsers.find((x) => x.id == opt).name_e
                    "
                  >
                  </multiselect>
                </div>
              </div>
              <div v-if="$store.state.archiving.objectActive.media" class="col-md-12">
                <div class="form-group">
                  <label class="control-label"> Link </label>
                  <input
                    type="text"
                    disabled
                    :value="
                      $store.state.archiving.objectActive.media[
                        $store.state.archiving.objectActive.media.length - 1
                      ].url
                    "
                    class="form-control"
                    data-create="9"
                  />
                </div>
              </div>
            </div>
          </div>
        </form>
      </b-modal>

      <div class="col-md-12 my-2">
        <div class="details-nav">
          <div class="d-flex justify-content-between">
            <div class="col-8">
              <span v-if="
                  $store.state.archiving.objectActive.media &&
                  $store.state.archiving.objectActive.media.length > 0
                ">
                <a
                class="btn-action-cutom"
                :href="
                  $store.state.archiving.objectActive.media[
                    $store.state.archiving.objectActive.media.length - 1
                  ].url
                "
                download
              >
                <i class="fas fa-upload"></i>
              </a>
              </span>
              <span class="btn-action-cutom" @click.prevent="$bvModal.show('show-email')">
                <i class="fas fa-envelope-open"></i>
              </span>
              <span
                class="btn-action-cutom"
                @click.prevent="$bvModal.show('show-whatup')"
              >
                <i class="fab fa-whatsapp"></i>
              </span>
              <span
                class="btn-action-cutom"
                @click.prevent="$bvModal.show('show-notify')"
              >
                <i class="fe-bell noti-icon"></i>
              </span>
            </div>
            <div class="col-2">
              <span
                class="btn-action-cutom"
                @click.prevent="deleteFile($store.state.archiving.objectActive.id)"
              >
                <i class="fas fa-trash"></i>
              </span>
            </div>
          </div>
        </div>
        <div class="inputs mt-3">
          <div class="form-group d-flex justify-content-between">
            <label class="control-label m-1"> Name </label>
            <input
              type="text"
              disabled
              :value="$store.state.archiving.objectActive.data_type_value[0].value"
              class="form-control form-control-custom"
            />
          </div>
          <!--              <div class="form-group d-flex justify-content-between mt-2">-->
          <!--                  <label class="control-label m-1">-->
          <!--                      Contact-->
          <!--                  </label>-->
          <!--                  <select class="form-control">-->
          <!--                      <option>ksjdjk</option>-->
          <!--                      <option>jksdkj</option>-->
          <!--                  </select>-->
          <!--              </div>-->
          <div class="form-group d-flex justify-content-between mt-2">
            <label class="control-label m-1"> Owner </label>
            <select class="form-control form-control-custom">
              <option>Owner</option>
            </select>
          </div>
          <div class="form-group d-flex justify-content-between mt-2">
            <label class="control-label m-1"> Workspace </label>
            <input
              type="text"
              disabled
              :value="$i18n.locale == 'ar' ? currentNode.name : currentNode.name_e"
              class="form-control form-control-custom"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import ErrorMessage from "../widgets/errorMessage";
import loader from "../general/loader";
import Multiselect from "vue-multiselect";
import adminApi from "../../api/adminAxios";
import Swal from "sweetalert2";
import { integer, maxLength, minLength, required, email } from "vuelidate/lib/validators";
import transMixinComp from "../../helper/mixin/translation-comp-mixin";
export default {
  props: ["currentNode"],
  data() {
    return {
      watsApp: {
        phone: "",
      },
      sendEmail: {
        email: "",
      },
      sendNotify: {
        title: "",
        users: [],
        id: null,
        sender: {},
      },
      isLoader: false,
      getUsers: [],
    };
  },
  mixins: [transMixinComp],

  components: {
    ErrorMessage,
    loader,
    Multiselect,
  },
  validations: {
    watsApp: {
      phone: { required },
    },
    sendEmail: {
      email: { required, email },
    },
    sendNotify: {
      title: { required, minLength: minLength(3), maxLength: maxLength(200) },
      users: { required },
    },
  },
  methods: {
    // start whats app
    resetWhatsApp() {
      this.watsApp = { phone: "" };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.$bvModal.hide("show-whatup");
    },
    getLinkWhastapp() {
      this.$v.watsApp.$touch();
      if (this.$v.watsApp.$invalid) {
        return;
      } else {
        var url =
          "https://api.whatsapp.com/send?phone=" +
          this.watsApp.phone +
          "&text=" +
          encodeURIComponent(
            this.$store.state.archiving.objectActive.media[
              this.$store.state.archiving.objectActive.media.length - 1
            ].url
          );

        window.open(url);
      }
    },
    // start email
    resetSendEmail() {
      this.sendEmail = { email: "" };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.$bvModal.hide("show-email");
    },
    SendEmail() {
      this.$v.sendEmail.$touch();
      if (this.$v.sendEmail.$invalid) {
        return;
      } else {
        adminApi
          .post(`/send-email`, {
            ...this.sendEmail,
          })
          .then((res) => {
            setTimeout(() => {
              Swal.fire({
                icon: "success",
                text: `${this.$t("general.Addedsuccessfully")}`,
                showConfirmButton: false,
                timer: 1500,
              });
            }, 500);
          })
          .catch((err) => {
            if (err.response.data) {
              this.errors = err.response.data.errors;
            } else {
              Swal.fire({
                icon: "error",
                title: `${this.$t("general.Error")}`,
                text: `${this.$t("general.Thereisanerrorinthesystem")}`,
              });
            }
          });
      }
    },

    deleteFile(id) {
      Swal.fire({
        title: `${this.$t("general.Areyousure")}`,
        text: `${this.$t("general.Youwontbeabletoreverthis")}`,
        type: "warning",
        showCancelButton: true,
        confirmButtonText: `${this.$t("general.Yesdeleteit")}`,
        cancelButtonText: `${this.$t("general.Nocancel")}`,
        confirmButtonClass: "btn btn-success mt-2",
        cancelButtonClass: "btn btn-danger ml-2 mt-2",
        buttonsStyling: false,
      }).then((result) => {
        if (result.value) {
          this.isLoader = true;
          this.$emit("deleteFile", id);
          adminApi
            .delete(`/arch-archive-files/${id}`)
            .then((res) => {
              Swal.fire({
                icon: "success",
                title: `${this.$t("general.Deleted")}`,
                text: `${this.$t("general.Yourrowhasbeendeleted")}`,
                showConfirmButton: false,
                timer: 1500,
              });
            })
            .catch((err) => {
              if (err.response.status == 400) {
                Swal.fire({
                  icon: "error",
                  title: `${this.$t("general.Error")}`,
                  text: `${this.$t("general.CantDeleteRelation")}`,
                });
              } else {
                Swal.fire({
                  icon: "error",
                  title: `${this.$t("general.Error")}`,
                  text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                });
              }
            });
        }
      });
    },
    resetSendNotify() {
      this.sendNotify = {
        title: "",
        users: [],
        id: null,
        sender: {},
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.$bvModal.hide("show-notify");
    },
    sendNotifyFun() {
      this.sendNotify.id = this.$store.state.archiving.objectActive.id;
      this.sendNotify.sender =
        this.$store.state.auth.type == "admin"
          ? this.$store.state.auth.partner
          : this.$store.state.auth.user;

      this.$v.sendNotify.$touch();
      if (this.$v.sendNotify.$invalid) {
        return;
      } else {
        this.isLoader = true;
        adminApi
          .post(`/arch-archive-files/file-notify`, this.sendNotify)
          .then((res) => {
            this.resetSendNotify();
            setTimeout(() => {
              Swal.fire({
                icon: "success",
                text: `${this.$t("general.Addedsuccessfully")}`,
                showConfirmButton: false,
                timer: 1500,
              });
            }, 200);
          })
          .catch((err) => {
            Swal.fire({
              icon: "error",
              title: `${this.$t("general.Error")}`,
              text: `${this.$t("general.CantDeleteRelation")}`,
            });
          })
          .finally(() => {
            this.isLoader = false;
          });
      }
    },
    async getUsersAll() {
      this.isLoader = true;
      await adminApi
        .get(`/users?company_id=${this.$store.state.auth.company_id}`)
        .then((res) => {
          let l = res.data;
          this.getUsers = l.data;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        })
        .finally(() => {
          this.isLoader = false;
        });
    },
  },
};
</script>

<style scoped>
.details {
  background-color: #f8fcff;
  border-radius: 7px;
  width: 100%;
}
.details-img {
  height: 150px;
  background: #e8ecee;
  border-radius: 7px;
  margin: 0 0px;
}
.icon {
  text-align: center;
}

.icon {
  font-size: 85px;
  color: hsl(6deg, 37%, 60%);
}

.btn-action-cutom {
  display: inline-block;
  width: 34px;
  line-height: 26px;
  height: 30px;
  color: #fff;
  border-radius: 7px;
  font-size: 17px;
  text-align: center;
  background-color: #3eafd9;
  cursor: pointer;
}
.form-control.form-control-custom {
  display: inline-block !important;
  width: 70%;
}
.chat-icon {
  top: 4px;
  right: 4px;
  width: 35px;
  font-size: 20px;
  height: 35px;
  text-align: center;
  border-radius: 50%;
  background-color: #f8fcff;
  cursor: pointer;
}

.chat-icon i {
  color: #3eafd9;
}
</style>
