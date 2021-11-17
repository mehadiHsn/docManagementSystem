<template>
  <div>
    <ul class="nav nav-pills" id="myTab" role="tablist">
      <li class="nav-item">
        <a
          class="nav-link active"
          id="group-tab"
          data-toggle="tab"
          href="#group"
          role="tab"
          aria-controls="group"
          aria-selected="true"
        >Document Group</a>
      </li>
      <li class="nav-item">
        <a
          class="nav-link"
          id="profile-tab"
          data-toggle="tab"
          href="#profile"
          role="tab"
          aria-controls="profile"
          aria-selected="false"
        >Document Type</a>
      </li>
      <li class="nav-item">
        <a
          class="nav-link"
          id="contact-tab"
          data-toggle="tab"
          href="#contact"
          role="tab"
          aria-controls="contact"
          aria-selected="false"
        >Document Status</a>
      </li>
      <li class="nav-item">
        <a
          class="nav-link"
          id="contact-tab"
          data-toggle="tab"
          href="#permissions"
          role="tab"
          aria-controls="contact"
          aria-selected="false"
        >Document Permissions</a>
      </li>
    </ul>
    <div class="tab-content" id="myTabContent">
      <div class="tab-pane fade show active" id="group" role="tabpanel" aria-labelledby="group-tab">
        <UpdateDocGroup></UpdateDocGroup>
      </div>
      <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
        <UpdateDocType></UpdateDocType>
      </div>
      <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
        <UpdateDocStatus></UpdateDocStatus>
      </div>
      <div class="tab-pane fade" id="permissions" role="tabpanel" aria-labelledby="contact-tab">
        <UpdateDocPermission></UpdateDocPermission>
      </div>
    </div>
  </div>
</template>
<script>
import { mapState } from "vuex";

import UpdateDocGroup from "./UpdateDocGroup";
import UpdateDocType from "./UpdateDocType";
import UpdateDocStatus from "./UpdateDocStatus";
import UpdateDocPermission from "./UpdateDocPermission";
export default {
  name: "Updateold",
  props: ["admin"],
  components: {
    UpdateDocGroup,
    UpdateDocType,
    UpdateDocStatus,
    UpdateDocPermission
  },
  computed: {
    ...mapState({
      doc_type: "doc_type",
      doc_group: "doc_group",
      doc_status: "doc_status"
    })
  },
  mounted() {
    this.$store.dispatch("getDocTypeGroup");
  }
};
</script>