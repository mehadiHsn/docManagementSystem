<template>
  <div>
    <form v-on:submit.prevent="addNewDocStatus" v-bind:model="addNewDocStatusForm">
      <div class="form-group">
        <label for="doctype">Document Status</label>
        <input
          type="text"
          v-model="addNewDocStatusForm.status_code"
          class="form-control"
          id="doctype"
          aria-describedby="doctype"
          placeholder="Enter Document Status"
        >
        <small id="typeHelp" class="form-text text-muted">Must be unique.</small>
      </div>
      <div class="form-group">
        <label for="groupdesc">Status Description</label>
        <input
          type="text"
          v-model="addNewDocStatusForm.status_desc"
          class="form-control"
          id="groupdesc"
          placeholder="Enter Status Description"
        >
      </div>
      <button type="submit" class="btn bg-my1">Submit</button>
    </form>
  </div>
</template>

<script>
import swal from "sweetalert";
export default {
  name: "AddDocStatus",
  data() {
    return {
      addNewDocStatusForm: {
        status_code: "",
        status_desc: ""
      }
    };
  },
  methods: {
    addNewDocStatus() {
      this.addNewDocStatusForm.status_code = this.addNewDocStatusForm.status_code.toUpperCase();
      axios
        .post(this.$route.path + "/status", this.addNewDocStatusForm)
        .then(response => {
          if (response.data > 0) {
            this.addNewDocStatusForm.status_code = this.addNewDocStatusForm.status_desc =
              "";
            swal({
              icon: "success",
              title: "Success",
              text: "New Document Status added",
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