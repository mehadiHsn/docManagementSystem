<template>
  <div v-if="this.userInfo != 0">
    <h3>Add a new document</h3>
    <hr>
    <form
      enctype="multipart/form-data"
      v-bind:model="addNewDocForm"
      v-on:submit.prevent="addNewDocSubmit"
    >
      <div class="form-group row">
        <label for="documentcode" class="col-sm-3 col-form-label">Document code:</label>
        <div class="col-sm-8">
          <input type="text" class="form-control" v-model="addNewDocForm.doc_code" placeholder="No Spaces allowed">
        </div>
      </div>
      <div class="form-group row">
        <label for="docver" class="col-sm-3 col-form-label">Document version:</label>
        <div class="col-sm-8">
          <input type="text" class="form-control" value="0">
        </div>
      </div>
      <div class="form-group row">
        <label for="desc1" class="col-sm-3 col-form-label">Short Description:</label>
        <div class="col-sm-8">
          <input type="text" class="form-control" v-model="addNewDocForm.desc1" placeholder="Document tags will be used for searching documents">
        </div>
      </div>
      <div class="form-group row">
        <label for="desc1" class="col-sm-3 col-form-label">Document Info:</label>
        <div class="col-sm-8">
          <textarea placeholder="Detailed info about your document" type="text" class="form-control" v-model="addNewDocForm.doc_info"></textarea>
        </div>
      </div>
      <div class="form-group row">
        <label for="group" class="col-sm-3 col-form-label">Document group:</label>
        <div class="col-sm-8">
          <select class="custom-select" v-model="addNewDocForm.doc_group">
            <option
              v-if="no_permissions.findIndex(data=>data.group_code==group.group_code)==-1"
              :value="group.group_code"
              v-for="group in doc_group"
              v-bind:key="group.id"
              :disabled="accessList.findIndex(data => data.access_code == group.group_code)!= -1 ? false: true"
            >{{group.group_desc}}</option>
          </select>
          <small
            id="typeHelp"
            class="form-text text-muted"
          >You can add document to groups only which you have access to</small>
        </div>
      </div>
      <div class="form-group row">
        <label for="doc_type" class="col-sm-3 col-form-label">Document type:</label>
        <div class="col-sm-8">
          <select class="custom-select" v-model="addNewDocForm.doc_type">
            <option
              :value="types.doc_ext"
              v-for="types in doc_type"
              v-bind:key="types.id"
            >{{types.type_code}}</option>
          </select>
        </div>
      </div>
      <div class="form-group row">
        <label for="document_handler" class="col-sm-3 col-form-label">Document handler:</label>
        <div class="col-sm-8">
          <input type="text" class="form-control" :value="userInfo.name" readonly>
        </div>
      </div>
      <div class="form-group row">
        <label for="document_status" class="col-sm-3 col-form-label">Document status:</label>
        <div class="col-sm-8">
          <input type="text" class="form-control" value="Created" readonly>
        </div>
      </div>
      <div class="input-group mb-3">
        <div class="col-sm-11 custom-file">
          <input
            type="file"
            :disabled="addNewDocForm.doc_type ==''?true:false"
            class="custom-file-input"
            :accept="'.'+addNewDocForm.doc_type"
            @change="onChange"
            required
          >
          <label class="custom-file-label">{{docFile!=''?docFile.name:'Choose the document file after selecting the document type'}}</label>
        </div>
      </div>
      <div class="form-group row-justified-center">
          <input type="submit" class="btn btn-my" value="Add new document">
      </div>
    </form>
  </div>
</template>

<script>
import swal from "sweetalert";
import { mapState } from "vuex";
export default {
  name: "addnewdocument",
  props: ["userInfo"],
  data() {
    return {
      addNewDocForm: {
        doc_code: "",
        doc_rev: "0",
        desc1: "",
        doc_info: "",
        doc_group: "",
        doc_type: "",
        doc_status: "Created"
      },
      docFile: ""
    };
  },
  methods: {
    onChange(e) {
      this.docFile = e.target.files[0];
    },
    addNewDocSubmit() {
      const config = {
        headers: {
          "content-type": "multipart/form-data"
        }
      };
      let formData = new FormData();
      let fileData = new FormData();
      formData.append("doc_code", this.addNewDocForm.doc_code);
      formData.append(
        "doc_rev",
        this.addNewDocForm.doc_rev != null ? this.addNewDocForm.doc_rev : "0"
      );
      formData.append("desc1", this.addNewDocForm.desc1);
      formData.append("doc_info", this.addNewDocForm.doc_info);
      formData.append("doc_group", this.addNewDocForm.doc_group);
      formData.append("doc_type", this.addNewDocForm.doc_type);
      formData.append("handler", this.userInfo.usercode);
      formData.append("doc_status", this.addNewDocForm.doc_status);
      formData.append("file_path", this.docFile);

      axios
        .post("/add-new-document", formData, config)
        .then(response => {
          if (parseInt(response.data) > 0) {
            this.addNewDocForm = {
              doc_code: "",
              doc_rev: "0",
              desc1: "",
              doc_info: "",
              doc_group: "",
              doc_type: "",
              doc_status: "Created"
            };
            this.docFile = "";
            swal({
              icon: "success",
              title: "Congratulations!",
              text: "Your document created successfully",
              timer: "2000"
            });
          } else if (
            response.data == "Document type and file extension not matched"
          ) {
            swal({
              icon: "error",
              title: "Error creating new document",
              text: "Document type and file extension not matched",
              timer: "4000"
            });
          }
        })
        .catch(error => {
          let err = Array();
          let lists = document.createElement("span");
          lists.innerHTML = '<ul class="list-group">';
          for (let index in error.response.data.errors) {
            err.push(error.response.data.errors[index][0]);
            lists.innerHTML +=
              '<li class="list-group-item">' +
              error.response.data.errors[index][0] +
              "</li>";
          }
          lists.innerHTML += "</ul>";
          swal({
            icon: "error",
            content: lists,
            timer: 4000
          });
        });
    }
  },
  created() {},
  computed: {
    ...mapState({
      doc_type: "doc_type",
      doc_group: "doc_group",
      doc_status: "doc_status",
      accessList: "user_access",
      no_permissions: "no_permissions"
    })
  },
  mounted() {
    if (this.userInfo == 0) {
      window.location.href = "/";
    }
    this.$store.dispatch("getUserAccess");
    this.$store.dispatch("getDocTypeGroup");
  }
};
</script>
<style>
</style>

