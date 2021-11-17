<template>
  <div>
    <form v-on:submit.prevent="addNewDoc" v-bind:model="addNewPermissionForm">
      <div class="form-group">
        <label for="groupcode" class="my-label mb-2">Permission For Document Group</label>
        <select
        ref="docgroup"
        name="groupcode"
        id="groupcode"
        class="custom-select"
        v-model="addNewPermissionForm.access_code"
        >
        <option
        :value="dg.group_code"
        v-bind:key="dg.group_code"
        v-for="dg in no_permissions"
        >{{dg.group_desc}}</option>
      </select>
    </div>
    <div class="form-group">
      <label for="groupdesc" class="my-label mb-2">Access Description</label>
      <input
      type="text"
      v-model="addNewPermissionForm.access_desc"
      class="form-control"
      id="groupdesc"
      placeholder="Permission to access group.."
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
    name: "AddUserPermissions",
    data() {
      return {
        addNewPermissionForm: {
          access_code: "",
          access_desc: ""
        }
      };
    },
    methods: {
      addNewDoc() {
        this.addNewPermissionForm.access_code = this.addNewPermissionForm.access_code.toUpperCase();
        axios
        .post(this.$route.path + "/access", this.addNewPermissionForm)
        .then(response => {
          if (response.data > 0) {
            
            this.$store.dispatch("getDocTypeGroup");
            this.addNewPermissionForm.access_code = this.addNewPermissionForm.access_desc =
            "";
            swal({
              icon: "success",
              title: "Success",
              text: "New Access added",
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
    },
    computed: {
      ...mapState({
        doc_group: "doc_group",
        no_permissions: "no_permissions"
      })
    },
    mounted() {
      this.$store.dispatch("getDocTypeGroup");
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