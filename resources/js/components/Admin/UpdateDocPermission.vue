<template>
  <div>
    <h5>Update existing Document Permissions</h5>
    <ul class="list-group">
      <li class="list-group-item" v-bind:key="dt.id" v-for="(dt,index) in permissionList">
        <strong>{{index+1}})</strong>
        <span v-if="editEnabled == dt.id">
          <input
            type="text"
            :value="dt.access_desc"
            class="form-control"
            @input="editedData = $event.target.value"
            @keyup.enter="saveChanges(dt.id)"
            @keyup.enter.native="saveChanges(dt.id)"
          >
        </span>
        <span v-else>{{dt.access_desc}}</span>
        <span style="display:inline-block;float:right;">
          <button
            v-if="editEnabled != dt.id"
            type="button"
            class="btn btn-sm btn-warning mb-2"
            @click="editEnabled = dt.id"
          >edit</button>
          <button
            v-if="editEnabled == dt.id"
            type="button"
            class="btn btn-sm btn-warning mb-2"
            @click="editEnabled = 0;editedData=''"
          >cancel</button>
          <!-- <button
            v-if="editEnabled != dt.id"
            type="button"
            class="btn btn-sm btn-danger mb-2"
          >delete</button> -->
          <button
            v-if="editEnabled == dt.id"
            type="button"
            class="btn btn-sm btn-success mb-2"
            @click="saveChanges(dt.id)"
          >save</button>
        </span>
      </li>
    </ul>
  </div>
</template>

<script>
import swal from "sweetalert";
import { mapState, mapMutations } from "vuex";

export default {
  data() {
    return {
      editEnabled: 0,
      editedData: ""
    };
  },
  watch: {
    editEnabled(val) {
      this.editEnabled = val;
    }
  },
  methods: {
    saveChanges(id) {
      this.editEnabled = 0;
      if (this.editedData != "") {
        let formdata = new FormData();
        formdata.append("id", id);
        formdata.append(
          "access_desc",
          this.editedData == ""
            ? this.permissionList.filter(data => data.id == id)[0].access_desc
            : this.editedData
        );
        axios
          .post(this.$route.path + "/permission", formdata)
          .then(response => {
            if (response.data == id) {
              let index = this.permissionList.findIndex(data => data.id == id);
              this.permissionList[index].access_desc =
                this.editedData == ""
                  ? this.permissionList.filter(data => data.id == id)[0]
                      .access_desc
                  : this.editedData;
              this.editedData = "";

              swal({ title: "Success", icon: "success", timer: 3000 });
            }
          });
      } else {
        this.editedData = "";
        this.editEnabled = 0;
        swal({ title: "No Changes", icon: "info", timer: 3000 });
      }
    }
  },
  computed: {
    ...mapState({
      permissionList: "permissionList"
    })
  },
  mounted() {
    this.editEnabled;
  }
};
</script>

<style scoped>
@media only screen and (min-width: 520px) {
  input.form-control {
    width: 70%;
    display: inline-block;
  }
}
div > h5 {
  margin: 1rem 0rem;
  border-bottom: 1px solid grey;
}
</style>