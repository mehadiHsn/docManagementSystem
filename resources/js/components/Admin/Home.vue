<template>
  <div>
    <div class="container" v-if="$route.name == 'AHome' && admin != '0'">
      <h5>Manage your account</h5>
      <div class="row">
        <table class="table table-sm table-bordered table-hover">
          <caption>Current profile details</caption>
          <tbody>
            <tr>
              <th>Username</th>
              <td>{{adminInfo.usercode}}</td>
            </tr>
            <tr>
              <th @click="editEnabled = 'name'">
                <i class="fas fa-star"></i> Name
              </th>
              <td v-if="editEnabled == 'name'">
                <div class="input-group mb-2">
                  <input
                    type="text"
                    :value="adminInfo.name"
                    class="form-control"
                    @input="editedData = $event.target.value"
                  >
                  <div class="input-group-append">
                    <button
                      class="btn btn-sm btn-outline-success"
                      title="Save"
                      @click="update(editEnabled)"
                    >
                      <i class="fas fa-edit"></i>
                    </button>
                    <button
                      class="btn btn-sm btn-outline-danger"
                      title="Cancel"
                      @click="editEnabled = 0 ; editedData = ''"
                    >
                      <i class="fas fa-times-circle"></i>
                    </button>
                  </div>
                </div>
              </td>
              <td v-else>{{adminInfo.name}}</td>
            </tr>
            <tr>
              <th @click="editEnabled = 'email'">
                <i class="fas fa-star"></i> Email
              </th>
              <td v-if="editEnabled == 'email'">
                <div class="input-group mb-2">
                  <input
                    type="text"
                    :value="adminInfo.email"
                    class="form-control"
                    @input="editedData = $event.target.value"
                  >
                  <div class="input-group-append">
                    <button
                      class="btn btn-sm btn-outline-success"
                      title="Save"
                      @click="update(editEnabled)"
                    >
                      <i class="fas fa-edit"></i>
                    </button>
                    <button
                      class="btn btn-sm btn-outline-danger"
                      title="Cancel"
                      @click="editEnabled = 0 ; editedData = ''"
                    >
                      <i class="fas fa-times-circle"></i>
                    </button>
                  </div>
                </div>
              </td>
              <td v-else>{{adminInfo.email}}</td>
            </tr>
            <tr>
              <th @click="editEnabled = 'password'">
                <i class="fas fa-star"></i> Password
              </th>
              <td v-if="editEnabled == 'password'">
                <div class="input-group">
                  <input
                    @input="passwords.oldPass = $event.target.value"
                    type="password"
                    aria-label="Old password"
                    placeholder="Old password"
                    class="form-control"
                  >
                  <input
                    @input="passwords.newPass = $event.target.value"
                    type="password"
                    aria-label="New password"
                    placeholder="New password"
                    class="form-control"
                  >
                  <div class="input-group-append">
                    <button
                      class="btn btn-sm btn-outline-success"
                      title="Save"
                      @click="updatePassword"
                    >
                      <i class="fas fa-edit"></i>
                    </button>
                    <button
                      class="btn btn-sm btn-outline-danger"
                      title="Cancel"
                      @click="editEnabled = 0 ; editedData = '';passwords.oldPass = passwords.newPass = ''"
                    >
                      <i class="fas fa-times-circle"></i>
                    </button>
                  </div>
                </div>
              </td>
              <td v-else>you'll be logged out after password change</td>
            </tr>
          </tbody>
        </table>
        <small style="color: red">
          click on the (
          <i class="fas fa-star"></i>) Signed field to edit them
        </small>
      </div>
    </div>
    <router-view v-else></router-view>
  </div>
</template>
<script>
import { mapState } from "vuex";
export default {
  name: "AHome",
  props: ["admin"],
  data() {
    return {
      adminInfo: [],
      editEnabled: "",
      editedData: "",
      passwords: {
        oldPass: "",
        newPass: ""
      }
    }
  },
  watch: {
    editEnabled(val) {
      this.editEnabled = val;
    },
    editedData(val) {
      this.editedData = val;
    },
    "passwords.oldPass"(val) {
      this.passwords.oldPass = val;
    },
    "passwords.newPass"(val) {
      this.passwords.newPass = val;
    }
  },
  methods: {
    getAdminInfo() {
      axios.get(this.$route.path + "/get-admin-info").then(response => {
        this.adminInfo = response.data;
      });
    },
    update(field) {
      let updateForm = new FormData();
      updateForm.append("id", this.adminInfo.id);
      updateForm.append("attr", field);
      updateForm.append("value", this.editedData);
      if (this.editedData != "") {
        axios
          .post(this.$route.path + "/update-profile", updateForm)
          .then(response => {
            if (response.data == 1) {
              this.editEnabled = this.editedData = "";
              this.getAdminInfo();
            }
          });
      }
    },
    updatePassword() {
      let upwd = new FormData();
      upwd.append("id", this.admin.id);
      upwd.append("oldPass", this.passwords.oldPass);
      upwd.append("newPass", this.passwords.newPass);
      axios
        .post(this.$route.path + "/update-password", upwd)
        .then(response => {
          if (response.data == this.admin.id) {
            this.$router.go(0);
          }
        })
        .catch(error => {
          if (error.response.data == "123") {
            swal({
              icon: "error",
              text: "Old password did not match",
              timer: 4000
            });
          } else {
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
              timer: 4000
            });
          }
        });
    }
  },
  created() {
    if (this.$route.name == "AHome") {
      this.getAdminInfo();
    }
  },
  mounted() {
    this.editEnabled;
    this.adminInfo = this.admin;
    this.$store.dispatch("getDocTypeGroup");
  }
};
</script>
<style scoped>
h5 {
  border-bottom: 1px solid grey;
}
</style>
