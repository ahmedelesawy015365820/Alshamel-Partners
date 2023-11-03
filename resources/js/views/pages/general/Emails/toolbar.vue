<script>

export default {
    props: {
        selectedMessageIds: Array,
    },
    data() {
        return {
            searchTerm: '',
        };
    },

    methods: {
        deleteSelectedEmails() {
            this.$emit("deleteEmails");
        },
        markAsUnread() {
            this.$emit("mark-as-unread", this.selectedMessageIds)
        },
        addStar() {
            this.$emit("add-star", this.selectedMessageIds);
        },
        search() {
        // Implement your search logic here
        // You can filter or search messages based on user input
        // For example:
        this.$emit("search", this.searchTerm);
    }
}
}
</script>

<template>
<div>
    <b-button-group class="mr-1">
        <b-button size="sm" variant="light" @click="deleteSelectedEmails">
            <i class="mdi mdi-archive font-18"></i>
        </b-button>
        <b-button size="sm" variant="light">
            <i class="mdi mdi-alert-octagon font-18"></i>
        </b-button>
        <b-button size="sm" variant="light">
            <i class="mdi mdi-delete-variant font-18"></i>
        </b-button>
    </b-button-group>

    <b-dropdown class="btn-group mr-1" size="sm" variant="light">
        <template slot="button-content">
            <i class="mdi mdi-folder font-18"></i>
            <i class="mdi mdi-chevron-down"></i>
        </template>

        <b-dropdown-item>Updates</b-dropdown-item>
        <b-dropdown-item>Social</b-dropdown-item>
        <b-dropdown-item>Team Manage</b-dropdown-item>
    </b-dropdown>

    <b-dropdown class="btn-group mr-1" size="sm" variant="light">
        <template slot="button-content">
            <i class="mdi mdi-label font-18"></i>
            <i class="mdi mdi-chevron-down"></i>
        </template>
        <b-dropdown-item>Updates</b-dropdown-item>
        <b-dropdown-item>Social</b-dropdown-item>
        <b-dropdown-item>Team Manage</b-dropdown-item>
    </b-dropdown>

    <b-dropdown class="btn-group mr-1" size="sm" variant="light">
        <template slot="button-content">
            <i class="mdi mdi-dots-horizontal font-18"></i> More
            <i class="mdi mdi-chevron-down"></i>
        </template>
        <b-dropdown-item @click="markAsUnread">Mark as Unread</b-dropdown-item>
        <b-dropdown-item >Add to Tasks</b-dropdown-item>
        <b-dropdown-item @click="addStar">Add Star</b-dropdown-item>
        <b-dropdown-item>Mute</b-dropdown-item>
    </b-dropdown>
</div>
</template>

