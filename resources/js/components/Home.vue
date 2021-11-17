<template>
  <div>
    <div class="col-md-12" v-if="userInfo!=0">
      <!-- <form class="form-inline"> -->
        <div class="col-md-12">
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <select
              class="custom-select"
              id="doc-group"
              @change="docByGroup"
              :disabled="doc_group.length == 0? true:false"
              >
              <option selected value="0" disabled="true">Document Group</option>
              <option
              :value="dg.group_code"
              v-for="dg in doc_group"
              v-bind:key="dg.group_code"
              >{{dg.group_desc}}</option>
            </select>
          </div>
          <input
          type="text"
          placeholder="Search documents by its code, short description"
          class="form-control"
          id="search-box"
          @input="searchString = $event.target.value"
          @keyup.enter="search"
          @keyup.enter.native="search"
          >
          <div class="input-group-append">
            <button
            :disabled="searchString == ''?true:false"
            class="btn btn-outline-success"
            type="button"
            @click="search"
            >Search</button>
            <button class="btn btn-outline-secondary" type="button" @click="clearGroup">Clear</button>
          </div>
        </div>
      </div>
    <!-- </form> -->
    <div class="row" v-if="searchGroup != '' && docsByGroup.length > 0">
      <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
        <h5>Documents under group: {{doc_group.filter(data=>data.group_code == searchGroup)[0].group_desc}}</h5>
      </div>
      <div
      class="col-md-3 col-lg-3 col-sm-6 col-xs-6 card"
      style="padding: auto 0px"
      v-bind:key="doc.id"
      v-for="doc in docsByGroup"
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
          :disabled="accessList.findIndex(data=>data.access_code==doc_group.filter(data=>data.group_desc==doc.group_desc)[0].group_code)==-1?  true: false"
          >View</button>
        </div>
      </div>
      <img :src="bg" class="book_cover" style>
    </div>
    <div class="card-body">
      <h6 class="card-title">{{doc.desc1}}</h6>
      <p class="card-text">
        <small>Created: {{doc.created_at.split(' ')[0]}}</small>
        <span class="float-right">
          <i
          title="Document Approved"
          class="fas fa-check-circle status-approved"
          v-if="doc.status_desc=='Approved'"
          ></i>
          <i
          title="Document Rejected"
          class="fas fa-times-circle status-rejected"
          v-if="doc.status_desc=='Rejected'"
          ></i>
        </span>
      </p>
    </div>
  </div>
</div>
<div v-else-if="searchGroup == -1 && docsByGroup.length == 0" class="row">
  <center>
    <div class="col-md-12">
      <span class="alert alert-warning">No Document found</span>
    </div>
  </center>
</div>
<hr>
<h4 v-if="(searchGroup== '' || searchGroup==0)&& docsByGroup.length == 0">
  <span v-if="userInfo.is_owner == 0">Your documents</span>
  <span
  v-else-if="userInfo.is_owner == 1 && typeof doc_group != 'undefined'"
  >Your owner group documents</span>
</h4>
<div class="row" v-if="(searchGroup== '' || searchGroup==0)&& docsByGroup.length == 0">
  <div
  class="col-md-4 col-lg-3 col-sm-6 col-xs-6 card"
  style="padding: auto 0px"
  v-bind:key="doc.id"
  v-for="doc in myDocs"
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
      :disabled="doc.status_desc == 'Rejected' ? true:false"
      >
      <span class="sr-only">Toggle Dropdown</span>
    </button>
    <div class="dropdown-menu">
      <button
      class="dropdown-item"
      @click="viewDocument(doc.doc_id)"
      :disabled="accessList.findIndex(data=>data.access_code==doc_group.filter(data=>data.group_desc==doc.group_desc)[0].group_code)==-1?  true: false"
      >View</button>
      <div class="dropdown-divider"></div>
      <button
      class="dropdown-item"
      @click="$router.push('/make-change-request/'+doc.doc_id)"
      >Change Request</button>
    </div>
  </div>
  <img :src="bg" class="book_cover" style>
</div>
<div class="card-body">
  <h6 class="card-title">{{doc.desc1}}</h6>
  <p class="card-text">
    <small>Created: {{doc.created_at.split(' ')[0]}}</small>
    <span class="float-right">
      <i
      title="Document Approved"
      class="fas fa-check-circle status-approved"
      v-if="doc.status_desc=='Approved'"
      ></i>
      <i
      title="Document Rejected"
      class="fas fa-times-circle status-rejected"
      v-else-if="doc.status_desc=='Rejected'"
      ></i>
      <i v-else class="fas fa-exclamation-circle" :title="doc.status_desc"></i>
    </span>
  </p>
</div>
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
      <h5 class="modal-title" id="DocumentModalLongTitle">{{selectedDoc.desc1}}</h5>
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
      <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Close</button>
    </div>
  </div>
</div>
</div>

<!-- doc preview modal -->
<div
class="modal fade bd-example-modal-lg"
tabindex="-1"
role="dialog"
id="docPreviewModal"
ref="docPreviewModal"
v-if="userInfo != 0"
>
<div class="modal-dialogs modal-dialog modal-lg" role="document">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title">{{selectedDoc.doc_code+', '+selectedDoc.doc_rev}}</h5>
      <button type="button" class="btn-sm close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="file-preview" id="file_preview"></div>
    <div class="modal-footer">
      <button
      type="button"
      class="btn btn-sm btn-outline-primary"
      @click="NewWindow"
      >Open in New Window</button>
      <button type="button" class="btn btn-sm btn-outline-danger" data-dismiss="modal">Close</button>
    </div>
  </div>
</div>
</div>
</div>
<div v-else>
  <nologin></nologin>
</div>
</div>
</template>

<script>
  import nologin from "../components/NoLogin";
  import { mapState, mapMutations } from "vuex";
  export default {
    name: "home",
    props: ["userInfo"],
    data() {
      return {
        selectedDoc: {},
        selected: "",
        searchGroup: "",
        docsByGroup: [],
        searchString: "",
        bg:
        "data:image/png;base64,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"
      };
    },
    components: {
      nologin: nologin
    },
    watch: {
      docsByGroup(value) {
        this.docsByGroup = value;
      },
      searchGroup(val) {
        this.searchGroup = val;
      },
      searchString(val) {
        this.searchString = val;
      }
    },
    methods: {
      search() {
        axios.get("/search/" + this.searchString).then(response => {
          if (response.status == 200) {
            /* this.$router.push({path:"/search/" + this.searchString});
            window.open("/search/" + this.searchString);*/
            window.location.href = "/search/" + this.searchString;
          }else{
            alert('Something went wrong!');
          }
        });
      },
      clearGroup(e) {
        this.searchGroup = "";
        this.docsByGroup = [];
        this.searchString = "";
        document.getElementById("search-box").value = "";
        document.getElementById("doc-group").selectedIndex = 0;
      },
      preview(id) {
        if (this.searchGroup != "" || this.searchGroup != 0) {
          this.selectedDoc = this.all_docs_detail.filter(
            data => data.doc_id == id
            )[0];
        } else {
          this.selectedDoc = this.myDocs.filter(data => data.doc_id == id)[0];
        }
        $("#DocumentModalCenter").modal("show");
      },
      viewDocument(id) {
        this.selectedDoc = this.myDocs.filter(data => data.doc_id == id)[0];
        var str = this.selectedDoc.file_path;
        var [filename, extension] = str
        .split(".")
        .reduce(
          (acc, val, i, arr) =>
          i == arr.length - 1
          ? [acc[0].substring(1), val]
          : [[acc[0], val].join(".")],
          []
          );

        if (extension == "docx" || extension == "doc") {
          if (window.location.hostname == "localhost") {
            let file_url =
            window.location.protocol +
            "//" +
            window.location.hostname +
            ":" +
            window.location.port +
            "/storage/" +
            this.selectedDoc.file_path;
            window.open(file_url, "_blank");
          } else {
            let file_url =
            window.location.protocol +
            "//" +
            window.location.hostname +
            "/storage/" +
            this.selectedDoc.file_path;
            window.open(
              "https://docs.google.com/viewer?url=" + file_url,
              "_blank"
              );
          }
        } else {
          let file_url =
          window.location.hostname == "localhost"
          ? window.location.protocol +
          "//" +
          window.location.hostname +
          ":" +
          window.location.port +
          "/storage/" +
          this.selectedDoc.file_path
          : window.location.protocol +
          "//" +
          window.location.hostname +
          "/storage/" +
          this.selectedDoc.file_path;

          document.getElementById("file_preview").innerHTML =
          "<object data='/storage/" +
          this.selectedDoc.file_path +
          "' width='100%' height='100%'><iframe src='https://docs.google.com/viewer?url=" +
          file_url +
          "&embedded=true'></iframe></object>";
          $("#docPreviewModal").modal("show");
        }
      },
      NewWindow() {
        if (window.location.hostname == "localhost") {
          let file_url =
          window.location.protocol +
          "//" +
          window.location.hostname +
          ":" +
          window.location.port +
          "/storage/" +
          this.selectedDoc.file_path;
          window.open(file_url, "_blank");
        } else {
          let file_url =
          window.location.protocol +
          "//" +
          window.location.hostname +
          "/storage/" +
          this.selectedDoc.file_path;
          window.open(
            "https://docs.google.com/viewer?embedded=true&url=" + file_url,
            "_blank"
            );
        }
      },
      docByGroup(e) {
        this.docsByGroup = [];
        if (e.target.value != 0) {
          this.searchGroup = e.target.value;
          let tmp = this.allDocs.filter(
            data => data.doc_group == this.searchGroup
            );
          tmp.forEach((value, index) => {
            this.docsByGroup.push(
              this.all_docs_detail.filter(data => data.doc_id == value.id)[0]
              );
          });
          if (this.docsByGroup.length == 0) {
            this.searchGroup = -1;
          }
        } else {
          this.searchGroup = 0;
        }
      }
    },
    created() {},
    computed: {
      ...mapState({
        all_docs_detail: "all_docs_detail",
        allDocs: "all_docs",
        myDocs: "my_docs",
        doc_group: "doc_group",
        accessList: "user_access"
      })
    },
    mounted() {
      $(this.$refs.docmodal).on("hidden.bs.modal", () => {
        this.selectedDoc = {};
      });
      $(this.$refs.docPreviewModal).on("hidden.bs.modal", () => {
        this.selectedDoc = {};
      });
      if (this.$userToken != 0) {
        this.$store.dispatch("getUserAccess");
        this.$store.dispatch("getMyDocs", this.$userToken);
      }
    }
  };
</script>
<style scoped>
.card-body {
  padding: 0.25rem 0rem;
}

div#suggest-pane {
  padding-top: 10px;
}

div.col-md-9 {
  padding: 0;
}
.autosuggest__results-container {
  position: relative;
  width: 100% auto;
}

.autosuggest__results {
  font-weight: 300;
  margin: 0;
  position: absolute;
  z-index: 10000001;
  width: 100%;
  border: 1px solid #e0e0e0;
  border-bottom-left-radius: 4px;
  border-bottom-right-radius: 4px;
  background: white;
  padding: 0px;
  overflow: scroll;
  max-height: 200px;
}

.autosuggest__results ul {
  list-style: none;
  padding-left: 0;
  margin: 0;
}

.autosuggest__results .autosuggest__results_item {
  cursor: pointer;
  padding: 10px;
}

#autosuggest ul:nth-child(1) > .autosuggest__results_title {
  border-top: none;
}

.autosuggest__results .autosuggest__results_title {
  color: gray;
  font-size: 11px;
  margin-left: 0;
  padding: 15px 13px 5px;
  border-top: 1px solid lightgray;
}

.autosuggest__results .autosuggest__results_item:active,
.autosuggest__results .autosuggest__results_item:hover,
.autosuggest__results .autosuggest__results_item:focus,
.autosuggest__results
.autosuggest__results_item.autosuggest__results_item-highlighted {
  background-color: #ddd;
}
.custom-select {
  display: inline-block;
  width: 100%;
  /* height: calc(1.6em + 0.75rem + 2px); */
  /* padding: 0.375rem 1.75rem 0.375rem 0.75rem; */
  font-size: 0.9rem;
  font-weight: 400;
  line-height: 1.6;
  color: #495057;
  vertical-align: middle;
}
.book_cover {
  max-width: 80%;
  width: auto;
  height: auto;
  align-content: center;
}

h4 {
  border-bottom: 1px solid grey;
}
iframe {
  width: 100%;
  height: 100%;
  padding: 0;
  margin: 0;
}
.file-preview {
  position: relative;
  flex: 1 1 auto;
  padding: 0.26rem;
  height: 80vh;
  min-height: 80vh;
}
.modal-footer,
.modal-header {
  padding: 0.6rem;
}
.modal-dialogs {
  max-width: 95vw;
  margin: 0.75rem auto;
}
.status-approved {
  color: #38c172 !important;
}
.status-rejected {
  color: #e3342f !important;
}
</style>
