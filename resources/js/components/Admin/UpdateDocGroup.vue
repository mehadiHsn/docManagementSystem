<template>
  <div>
    <h5>Update existing Document Groups</h5>
    <ul class="list-group">
      <li class="list-group-item" v-bind:key="dg.id" v-for="(dg,index) in doc_group">
        <strong>{{index+1}})</strong>
        <span v-if="editEnabled == dg.id">
          <input
            type="text"
            :value="dg.group_desc"
            class="form-control"
            @input="editedData = $event.target.value"
            @keyup.enter="saveChanges(dg.id)"
            @keyup.enter.native="saveChanges(dg.id)"
          >
        </span>
        <span v-else>{{dg.group_desc}}</span>
        <span style="display:inline-block;float:right;">
          <button
            v-if="editEnabled != dg.id"
            type="button"
            class="btn btn-sm btn-warning mb-2"
            @click="editEnabled = dg.id"
          >edit</button>
          <button
            v-if="editEnabled == dg.id"
            type="button"
            class="btn btn-sm btn-warning mb-2"
            @click="editEnabled = 0;editedData=''"
          >cancel</button>
          <!-- <button
            v-if="editEnabled != dg.id"
            type="button"
            class="btn btn-sm btn-danger mb-2"
            @click="deleteGroup(dg.id)"
          >delete</button> -->
          <button
            v-if="editEnabled == dg.id"
            type="button"
            class="btn btn-sm btn-success mb-2"
            @click="saveChanges(dg.id)"
          >save</button>
        </span>
      </li>
    </ul>
  </div>
</template>

<script>
import swal from "sweetalert";
import { mapState } from "vuex";

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
      if (this.editedData != "") {
        let formdata = new FormData();
        formdata.append("id", id);
        formdata.append(
          "group_desc",
          this.editedData == ""
            ? this.doc_group.filter(data => data.id == id)[0].group_desc
            : this.editedData
        );
        axios.post(this.$route.path + "/group", formdata).then(response => {
          if (response.data == id) {
            let index = this.doc_group.findIndex(data => data.id == id);
            this.doc_group[index].group_desc =
              this.editedData == ""
                ? this.doc_group.filter(data => data.id == id)[0].group_desc
                : this.editedData;
            this.editedData = "";
            this.editEnabled = 0;
            swal({ title: "Success", icon: "success", timer: 3000 });
          }
        });
      } else {
        this.editedData = "";
        this.editEnabled = 0;
        swal({ title: "No Changes", icon: "info", timer: 3000 });
      }
    },
    deleteGroup(id) {
      swal("A wild Pikachu appeared! What do you want to do?", {
        buttons: {
          cancel: "Abort",
          proceed: {
            text: "Proceed",
            value: "proceed"
          }
        }
      }).then(value => {
        switch (value) {
          case "proceed":
            swal(id);
            break;

          default:
            swal("Operation cancelled");
        }
      });
    }
  },
  computed: {
    ...mapState({
      doc_group: "doc_group"
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