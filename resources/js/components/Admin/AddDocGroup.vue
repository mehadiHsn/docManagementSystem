<template>
  <div>
    <form v-on:submit.prevent="addNewDoc" v-bind:model="addNewDocGrpForm">
      <div class="form-group">
        <label for="groupcode" class="my-label mb-2">Group Code</label>
        <input
          type="text"
          v-model="addNewDocGrpForm.group_code"
          class="form-control"
          id="groupcode"
          aria-describedby="groupcode"
          placeholder="Enter group code"
        >
        <small id="groupHelp" class="form-text text-muted">must be unique</small>
      </div>
      <div class="form-group">
        <label for="groupdesc" class="my-label mb-2">Group Description</label>
        <input
          type="text"
          v-model="addNewDocGrpForm.group_desc"
          class="form-control"
          id="groupdesc"
          placeholder="Enter Group Description"
        >
      </div>
      <button type="submit" class="btn bg-my1">Submit</button>
    </form>
  </div>
</template>

<script>
import swal from "sweetalert";
import { mapState } from "vuex";
export default {
  name: "AddDocGroup",
  data() {
    return {
      addNewDocGrpForm: {
        group_code: "",
        group_desc: ""
      }
    };
  },
  methods: {
    addNewDoc() {
      this.addNewDocGrpForm.group_code = this.addNewDocGrpForm.group_code.toUpperCase();
      axios
        .post(this.$route.path + "/group", this.addNewDocGrpForm)
        .then(response => {
          if (response.data > 0) {
            this.addNewDocGrpForm.group_code = this.addNewDocGrpForm.group_desc =
              "";
            this.$store.dispatch("getDocTypeGroup");
            swal({
              icon: "success",
              title: "Success",
              text: "New Group Code added",
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
#groupcode {
  text-transform: uppercase;
}
.my-label {
  width: 150px;
  white-space: nowrap;
  display: inline-block !important;
  text-overflow: ellipsis;
}
</style>