<template>
  <div>
    <form v-on:submit.prevent="addNewDocType" v-bind:model="addNewDocTypeForm">
      <div class="form-group">
        <label for="doctype">Document Type</label>
        <input
          type="text"
          v-model="addNewDocTypeForm.type_code"
          class="form-control"
          id="doctype"
          aria-describedby="doctype"
          placeholder="Enter Document type"
        >
        <small id="typeHelp" class="form-text text-muted">Must be unique.</small>
      </div>
      <div class="form-group">
        <label for="groupdesc">Type extension</label>
        <input
          type="text"
          v-model="addNewDocTypeForm.doc_ext"
          class="form-control"
          id="groupdesc"
          placeholder="Enter Type extension"
        >
      </div>
      <button type="submit" class="btn bg-my1">Submit</button>
    </form>
  </div>
</template>

<script>
import swal from "sweetalert";
/* import { mapMutations } from "vuex";*/
export default {
  name: "AddDocType",
  data() {
    return {
      addNewDocTypeForm: {
        type_code: "",
        doc_ext: ""
      }
    };
  },
  methods: {
    addNewDocType() {
      this.addNewDocTypeForm.type_code = this.addNewDocTypeForm.type_code.toUpperCase();
      axios
        .post(this.$route.path + "/type", this.addNewDocTypeForm)
        .then(response => {
          if (response.data > 0) {
            this.addNewDocTypeForm.type_code = this.addNewDocTypeForm.doc_ext =
              "";
            swal({
              icon: "success",
              title: "Success",
              text: "New Document Type added",
              timer: 1500
            });
          }
        })
        .catch(error => {
          let err = [];
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
            timer: 2000
          });
        });
    }
  }
};
</script>

<style scoped>
#doctype {
  text-transform: uppercase;
}
</style>