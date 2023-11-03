<template>
  <Layout>
    <PageHeader />
    <div class="container-fluid custom-container">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <Sidepanel :emailData="messages" />

              <div class="inbox-rightbar">

                <Toolbar />

                <div class="mt-3">
                  <div v-if="emailread">
                    <div class="email-section">
                      <p class="text-dark">{{ emailread.content }}</p>
                    </div>
                    <div class="email-section">
                      <p class="text-dark">{{ emailread.content_e }}</p>
                    </div>
                    <hr />
                    <h5 class="mb-3">Attachments</h5>
                    <div class="mt-5">
                      <a href class="btn btn-secondary mr-2">
                        <i class="mdi mdi-reply mr-1"></i> Reply
                      </a>
                      <a href class="btn btn-light">
                        Forward
                        <i class="mdi mdi-forward ml-1"></i>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>



<script>
import Toolbar from "./toolbar";
import Sidepanel from "./sidepanel";
import customTable from "../../../../helper/mixin/customTable";
import successError from "../../../../helper/mixin/success&error";
import crudHelper from "../../../../helper/mixin/crudHelper";
import translation from "../../../../helper/mixin/translation-mixin";
import Layout from "../../../layouts/main";
import PageHeader from "../../../../components/general/Page-header";
import permissionGuard from "../../../../helper/permission";
import adminApi from "../../../../api/adminAxios";


/**
 * Read-email component
 */
export default {
  page: {title: "Emails", meta: [{ name: "description", content: "Emails" }],},
  mixins: [translation,customTable,successError,crudHelper],
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      return permissionGuard(vm, "ReadEmails", "all Emails");
    });
  },
  components: {
    Toolbar,
    Sidepanel,
    Layout,
    PageHeader,
  },

data() {
    return {
      emailread: null,
      messages: []

    };
  },
    created() {
        this.fetchMessages()

        console.log(this.emailread);
    },
    methods: {
        fetchMessages() {
      adminApi
        .get('messages/')
        .then((response) => {
          const responseData = response.data;
          if (responseData.message === "success") {
            this.messages = responseData.data
            console.log('message', this.messages);
            const messageId = parseInt(this.$route.params.id);
            console.log('id', this.messageId);
            this.emailread = this.messages.find((message) => message.id === messageId);
            console.log('emailRead', this.emailread);

          } else {
            console.error('Error: Invalid response from the API');
          }
        })
        .catch((error) => {
          console.error('Error fetching messages:', error);
        });
    },
    }
};
</script>

<style scoped>

.card-body{
    color: white;
    background-color: white;
}
.unread-message {
  font-weight: bold; /* Make the text bold */
  color: #222; /* Set a darker text color (e.g., dark gray) */
}
.custom-container {
    width: 100%;
    padding-bottom: 25px;
    padding-top: 25px;
    padding-right: 25px;
    padding-left: 25px;
    margin: auto;
    display: block;
    line-height: 1;
    color: #6c757d;
    text-align: left;
    background-color: #f5f6f8;
}
</style>
