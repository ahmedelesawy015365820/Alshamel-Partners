<template>
  <Layout>
    <PageHeader :title="title" />
    <div class="container-fluid custom-container">
      <div class="row">
        <!-- Right Sidebar -->
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <Sidepanel :emailData="messages" />

              <div class="inbox-rightbar">
                <Toolbar
                  :selectedMessageIds="selectedMessageIds"
                  @deleteEmails="deleteMessages"
                  @mark-as-unread="markMessagesAsUnread"
                  @add-star="handleToggleStar"
                />

                <div class="mt-3">
                  <ul class="message-list">
                    <li
                      v-for="(message, index) in paginatedMessages"
                      :key="message.id"
                      :class="{ unread: message.unread === true }"
                    >
                      <div class="col-mail col-mail-1">
                        <div class="checkbox-wrapper-mail">
                          <input
                            :id="`chk-${index}`"
                            type="checkbox"
                            v-model="selectedMessageIds"
                            :value="message.id"
                          />
                          <label :for="`chk-${index}`"></label>
                        </div>

                        <span
                          :class="[
                            'star-toggle',
                            message.starred ? 'fas' : 'far',
                            'fa-star',
                          ]"
                          @click="toggleStar(message)"
                        ></span>

                        <a class="title" @click="openMessage(message.id)">{{
                          message.module
                        }}</a>
                      </div>

                      <div class="col-mail col-mail-2">
                        <a class="subject">{{ message.messageType.name_e }}</a>
                      </div>
                    </li>
                  </ul>
                </div>

                <div
                  class="row justify-content-md-between align-items-md-center"
                >
                  <div class="col-xl-7">
                    Showing {{ startIndex }} - {{ endIndex }} of {{ rows }}
                  </div>
                  <!-- end col-->
                  <div class="col-xl-5">
                    <div
                      class="text-md-right float-xl-right mt-2 pagination-rounded"
                    >
                      <b-pagination
                        v-model="currentPage"
                        :total-rows="rows"
                        :per-page="perPage"
                        @input="onPageChange"
                      ></b-pagination>
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
import Layout from "../../../layouts/main";
import PageHeader from "../../../../components/general/Page-header";
import loader from "../../../../components/general/loader";
import permissionGuard from "../../../../helper/permission";
import searchPage from "../../../../components/general/searchPage";
import translation from "../../../../helper/mixin/translation-mixin";
import customTable from "../../../../helper/mixin/customTable";
import successError from "../../../../helper/mixin/success&error";
import crudHelper from "../../../../helper/mixin/crudHelper";
import adminApi from "../../../../api/adminAxios";

/**
 * Email-inbox component
 */
export default {
  page: {
    title: "Emails-inbox",
    meta: [{ name: "description", content: "Emails" }],
  },
  mixins: [translation, customTable, successError, crudHelper],
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      return permissionGuard(vm, "Email", "all Emails");
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
      title: "Inbox",
      selectedMessageIds: [],
      messages: [],
      paginatedMessages: this.messages,
      title: "Inbox",
      // page number
      currentPage: 1,
      // default page size
      perPage: 15,
      emailIds: [],
      // start and end index
      startIndex: 1,
      endIndex: 15,
      sortDirection: 1,
      dateSearch: "",
      starredMessages: [],
      unreadMessages: [],
    };
  },
  computed: {
    rows() {
      return this.messages.length;
    },
  },
  created() {
    this.fetchMessages();
  },
  methods: {
    onPageChange() {
      this.startIndex = (this.currentPage - 1) * this.perPage;
      this.endIndex = (this.currentPage - 1) * this.perPage + this.perPage;
      this.paginatedMessages = this.messages.slice(
        this.startIndex,
        this.endIndex
      );
    },
    openMessage(messageId) {
      const message = this.messages.find((message) => message.id === messageId);
      if (message) {
        message.unread = false;
        this.$router.push(`/dashboard/ReadEmails/${messageId}`);
      }
    },
    toggleStar(message) {
      message.starred = !message.starred;
    },
    deleteMessages() {
      const updatedMessages = this.messages.filter(
        (message) => !this.selectedMessageIds.includes(message.id)
      );
      this.messages = updatedMessages;
      this.paginatedMessages = this.messages.slice(
        this.startIndex,
        this.endIndex
      );
      this.selectedMessageIds = [];
    },
    markMessagesAsUnread(selectedMessageIds) {
      selectedMessageIds.forEach((messageId) => {
        const message = this.messages.find(
          (message) => message.id === messageId
        );
        if (message) {
          message.unread = true;
        }
      });
    },
    handleToggleStar(selectedMessageIds) {
      selectedMessageIds.forEach((messageId) => {
        const message = this.messages.find(
          (message) => message.id === messageId
        );
        if (message) {
          message.starred = true;
          console.log("starInAddStar", "this works");
          this.toggleStar();
        }
      });
    },
    filterMessagesByDate() {
      const filteredMessages = this.messages.filter((message) => {
        return message.date.includes(this.dateSearch);
      });
      this.paginatedMessages = filteredMessages;
    },
    fetchMessages() {
      adminApi
        .get("messages/")
        .then((response) => {
          const responseData = response.data;
          if (responseData.message === "success") {
            this.messages = responseData.data.map((message) => {
              return {
                ...message,
                starred: false,
                unread: true,
              };
            });
            this.starredMessages = this.messages.filter(
              (message) => message.starred
            );
            this.unreadMessages = this.messages.filter(
              (message) => message.unread
            );
            this.startIndex = 0;
            this.endIndex = this.perPage;
            this.paginatedMessages = this.messages.slice(
              this.startIndex,
              this.endIndex
            );
          } else {
            console.error("Error: Invalid response from the API");
          }
        })
        .catch((error) => {
          console.error("Error fetching messages:", error);
        });
    },
  },
};
</script>

<style scoped>
.card-body {
  color: white;
  background-color: white;
}
.unread-message {
  font-weight: bold; /* Make the text bold */
  color: #222; /* Set a darker text color (e.g., dark gray) */
}
.search-container {
  margin: 20px;
}

.search-container form {
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #f1f1f1;
  padding: 5px;
  border-radius: 5px;
}

.search-container input[type="text"] {
  width: 100%;
  padding: 10px;
  border: none;
  outline: none;
}

.search-container button {
  color: white;
  padding: 10px 20px;
  border: none;
  cursor: pointer;
  border-radius: 5px;
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
.page-item.active .page-link {
  z-index: 3;
  color: #fff;
  background-color: #3bafda;
  border-color: #3bafda;
}
</style>



