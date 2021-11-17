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
          id="type-tab"
          data-toggle="tab"
          href="#type"
          role="tab"
          aria-controls="type"
          aria-selected="false"
        >Document Type</a>
      </li>
      <li class="nav-item">
        <a
          class="nav-link"
          id="status-tab"
          data-toggle="tab"
          href="#status"
          role="tab"
          aria-controls="status"
          aria-selected="false"
        >Document Status</a>
      </li>
      <li class="nav-item">
        <a
          class="nav-link"
          id="permission-tab"
          data-toggle="tab"
          href="#permission"
          role="tab"
          aria-controls="permission"
          aria-selected="false"
        >Permissions</a>
      </li>
    </ul>
    <hr>
    <div class="tab-content" id="myTabContent">
      <div class="tab-pane fade show active" id="group" role="tabpanel" aria-labelledby="group-tab">
        <AddDocGroup></AddDocGroup>
      </div>
      <div class="tab-pane fade" id="type" role="tabpanel" aria-labelledby="type-tab">
        <AddDocType></AddDocType>
      </div>
      <div class="tab-pane fade" id="status" role="tabpanel" aria-labelledby="status-tab">
        <AddDocStatus></AddDocStatus>
      </div>
      <div class="tab-pane fade" id="permission" role="tabpanel" aria-labelledby="permission-tab">
        <AddPermission></AddPermission>
      </div>
    </div>
  </div>
</template>
<script>
import AddDocGroup from "./AddDocGroup";
import AddDocType from "./AddDocType";
import AddDocStatus from "./AddDocStatus";
import AddPermission from "./AddUserPermission";
export default {
  name: "Addnew",
  props: ["admin"],
  components: { AddDocGroup, AddDocType, AddDocStatus, AddPermission }
};
</script>
<style>
</style>