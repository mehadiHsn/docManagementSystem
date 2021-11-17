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
        >Documents Pending Approval&emsp;<span v-if="new_docs.length > 0" class="badge badge-dark">{{new_docs.length}}</span></a>
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
        >Change Request Pending&emsp;<span v-if="changeReqs.length > 0" class="badge badge-dark">{{changeReqs.length}}</span></a>
      </li>
    </ul>
    <br>
    <div class="tab-content" id="myTabContent">
      <div class="tab-pane fade show active" id="group" role="tabpanel" aria-labelledby="group-tab">
        <h5>Documents pending your approval</h5>
        <div
          class="col-md-4 col-lg-4 col-sm-6 col-xs-6 card"
          style="padding: auto 0px"
          v-bind:key="doc.id"
          v-for="doc in new_docs"
        >
          <div style="display: inline-block;">
            <div class="btn-group dropleft" style="right:0;position: absolute;">
              <button
                type="button"
                class="btn btn-sm btn-outline-primary"
                @click="preview(doc.doc_id)"
              >more</button>
              <button
                type="button"
                title="More menu"
                class="btn btn-sm btn-outline-primary dropdown-toggle dropdown-toggle-split"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false"
              >
                <span class="sr-only">Toggle Dropdown</span>
              </button>
              <div class="dropdown-menu">
                <button
                  class="dropdown-item"
                  @click="viewDocument(doc.doc_id)"
                  data-target=".bd-example-modal-lg"
                >View</button>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#approve" @click="approve(doc.doc_id)">Approve</a>
                <a class="dropdown-item" href="#" @click="reject(doc.doc_id)">Reject</a>
              </div>
            </div>
          </div>
          <div class="card-body">
            <h6 class="card-title">{{doc.doc_code}}, {{doc.doc_rev}}</h6>
            <div class="card-text">
              <table class="table table-sm table-bordered">
                <tbody>
                  <tr>
                    <td>Desc</td>
                    <td>{{doc.desc1}}</td>
                  </tr>
                  <tr>
                    <td>Info</td>
                    <td>{{doc.doc_info}}</td>
                  </tr>
                  <tr>
                    <td>Handler</td>
                    <td>{{doc.handler}}</td>
                  </tr>
                </tbody>
              </table>
              <small>Created: {{doc.created_at.split(' ')[0]}}</small>
            </div>
          </div>
        </div>
      </div>
      <div class="tab-pane fade" id="type" role="tabpanel" aria-labelledby="type-tab">
        <h5>Document change requests</h5>
        <table class="table table-sm">
          <thead>
            <tr>
              <th>Docment Code, Revision</th>
              <th>Attribute Name</th>
              <th>New Value</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="cr in changeReqs">
              <td>
                {{myOwnerDocs.filter(data => data.id == cr.doc_id)[0].doc_code}},
                {{myOwnerDocs.filter(data => data.id == cr.doc_id)[0].doc_rev}}
              </td>
              <td>{{cr.attr_name}}</td>
              <td
                :id="'oldvalue_'+cr.id"
                @mouseover="oldval(cr.id,cr.doc_id,cr.attr_name)"
              >{{cr.new_value}}</td>
              <td>
                <div class="input-group">
                  <button
                    class="btn btn-sm btn-outline-success"
                    title="Approve"
                    @click="actions('approve',cr.id,cr.doc_id)"
                  >
                    <i class="far fa-check-circle"></i>
                  </button>
                  <button
                    class="btn btn-sm btn-outline-danger"
                    title="Reject"
                    @click="actions('reject',cr.id,cr.doc_id)"
                  >
                    <i class="far fa-times-circle"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal -->
    <div
      ref="docmodal"
      class="modal fade"
      id="DocumentModalCenter"
      tabindex="-1"
      role="dialog"
      aria-labelledby="DocumentModalCenterTitle"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5
              class="modal-title"
              id="DocumentModalLongTitle"
            >{{selectedDoc.doc_code+', '+selectedDoc.doc_rev}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <table class="table table-sm table-bordered table-hover table-responsive-xs">
              <tbody>
                <tr>
                  <td>Document Code, Version</td>
                  <td>{{selectedDoc.doc_code+', '+selectedDoc.doc_rev}}</td>
                </tr>
                <tr>
                  <td>Desc</td>
                  <td>{{selectedDoc.desc1}}</td>
                </tr>
                <tr>
                  <td>Group</td>
                  <td>{{selectedDoc.group_desc}}</td>
                </tr>
                <tr>
                  <td>Type</td>
                  <td>{{selectedDoc.type_code}}</td>
                </tr>
                <tr>
                  <td>Status</td>
                  <td>{{selectedDoc.status_desc}}</td>
                </tr>
                <tr>
                  <td>Created by</td>
                  <td>{{selectedDoc.handler}}</td>
                </tr>
                <tr>
                  <td>Additional Info</td>
                  <td>{{selectedDoc.doc_info!= null ? selectedDoc.doc_info: 'No info'}}</td>
                </tr>
                <tr>
                  <td>Date created</td>
                  <td>{{selectedDoc.created_at}}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-outline-danger" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!-- doc preview modal -->
    <div
      v-if="userInfo != 0 && typeof selectedDoc.doc_id != undefined"
      class="modal fade bd-example-modal-lg"
      tabindex="-1"
      role="dialog"
      id="docPreviewModal"
    >
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{selectedDoc.doc_code+', '+selectedDoc.doc_rev}}</h5>
          </div>
          <div class="modal-body file-preview" id="file_preview"></div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-danger btn-sm" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import swal from "sweetalert";
export default {
  name: "Notice",
  props: ["userInfo"],
  data() {
    return {
      new_docs: [],
      changeReqs: [],
      myOwnerDocs: [],
      oldValues: [],
      selectedDoc: ""
    };
  },
  watch: {
    selectedDoc(val) {
      this.selectedDoc = val;
    }
  },
  methods: {
    getNotApprovedDocs() {
      axios.get(this.$route.path + "/get-created-docs").then(response => {
        this.new_docs = response.data.new_docs;
        this.myOwnerDocs = response.data.my_owner_docs;
        this.changeReqs = response.data.change_requests;
        this.changeReqs.forEach((value, index) => {
          if (value.attr_name == "desc1") {
            this.changeReqs[index].attr_name = "Description1";
          } else if (value.attr_name == "doc_group") {
            this.changeReqs[index].attr_name = "Document Group";
            this.changeReqs[index].new_value = this.doc_group.filter(
              data => data.group_code == value.new_value
            )[0].group_desc;
          } else if (value.attr_name == "doc_info") {
            this.changeReqs[index].attr_name = "Document Info";
          } else if (value.attr_name == "doc_type") {
            this.changeReqs[index].attr_name = "Document Type";
          } else {
            this.changeReqs[index].attr_name = "New File";
          }
        });
      });
    },
    preview(id) {
      $("#DocumentModalCenter").modal("show");
      this.selectedDoc = this.new_docs.filter(data => data.doc_id == id)[0];
    },
    viewDocument(id) {
      this.selectedDoc = this.new_docs.filter(data => data.doc_id == id)[0];
      document.getElementById("file_preview").innerHTML =
        "<object data='/storage/" +
        this.selectedDoc.file_path +
        "' width='100%' height='100%'><iframe src='https://docs.google.com/viewer?url=/storage/" +
        this.selectedDoc.file_path +
        "&embedded=true'></iframe></object>";
      $("#docPreviewModal").modal("show");
    },
    approve(doc_id) {
      let doc = new FormData();
      doc.append("id", doc_id);
      axios.post(this.$route.path + "/approve", doc).then(response => {
        if (response.data == doc_id) {
          this.getNotApprovedDocs();
        }
      });
    },
    reject(doc_id) {
      let doc = new FormData();
      doc.append("id", doc_id);
      axios.post(this.$route.path + "/reject", doc).then(response => {
        if (response.data == doc_id) {
          this.getNotApprovedDocs();
          swal({
            icon: "warning",
            text: "Document was Rejected by you",
            timer: 2000
          });
        }
      });
    },
    oldval(id, doc_id, attr) {
      let oldval = this.myOwnerDocs.filter(data => data.id == doc_id)[0];
      let getAttr = "";
      switch (attr) {
        case "Description1":
          getAttr = "desc1";
          break;
        case "Document Info":
          getAttr = "doc_info";
          break;
        case "Document Group":
          getAttr = "doc_group";
          break;
        case "Document Type":
          getAttr = "doc_type";
          break;
        default:
          getAttr = "";
          break;
      }
      $("#oldvalue_" + id).popover({
        trigger: "hover",
        placement: "bottom",
        title: "Current Value",
        content: oldval[getAttr].toString()
      });
    },
    actions(action, id, doc_id) {
      let crId = new FormData();
      crId.append("id", id);
      crId.append("doc_id", doc_id);
      crId.append("request_status", action);
      axios
        .post(this.$route.path + "/change-request-action", crId)
        .then(response => {
          if (response.data == id || response.data == 1) {
            this.getNotApprovedDocs();
            swal({
              icon: "success",
              timer: 1000
            });
          }
        });
    }
  },
  mounted() {
    this.getNotApprovedDocs();
  }
};
</script>
<style scoped>
h5 {
  border-bottom: 1px solid grey;
}
iframe {
  width: 100%;
  height: 100%;
  padding: 0;
  margin: 0;
}
.modal-lg {
  max-width: 95vw;
}

.file-preview {
  position: relative;
  flex: 1 1 auto;
  padding: 0.6rem;
  height: 80vh;
  min-height: 80vh;
}
.modal-footer,
.modal-header {
  padding: 0.6rem;
}
.badge {
    padding: 0.4rem 0.4rem;
  }
</style>
