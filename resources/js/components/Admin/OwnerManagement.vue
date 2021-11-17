  <template>
  <div>
    <h5>Manage Group Owners</h5>
    <ul class="nav nav-pills" id="myTab" role="tablist">
      <li class="nav-item">
        <a
          class="nav-link"
          id="group-tab"
          data-toggle="tab"
          href="#add-owner-group"
          role="tab"
          aria-controls="group"
          aria-selected="true"
        >Add new owner</a>
      </li>
      <li class="nav-item">
        <a
          class="nav-link active"
          id="type-tab"
          data-toggle="tab"
          href="#type"
          role="tab"
          aria-controls="type"
          aria-selected="false"
        >Change Owner</a>
      </li>
    </ul>
    <hr>
    <div class="row justify-content-between">
      <div class="col-md-12 tab-content" id="myTabContent">
        <div class="tab-pane fade" id="add-owner-group" role="tabpanel" aria-labelledby="group-tab">
          <div class="col-md-11">
            <form method="POST" v-on:submit.prevent="registerOwner" v-bind:model="newOwnerForm">
              <div class="form-group row">
                <label for="usercode" class="col-md-4 col-form-label text-md-right">Username</label>
                <div class="col-md-6">
                  <input
                    id="usercode"
                    type="text"
                    class="form-control"
                    name="usercode"
                    v-model="newOwnerForm.usercode"
                    required
                    autofocus
                  >
                </div>
              </div>
              <div class="form-group row">
                <label for="name" class="col-md-4 col-form-label text-md-right">Name</label>
                <div class="col-md-6">
                  <input
                    id="name"
                    type="text"
                    class="form-control"
                    name="name"
                    v-model="newOwnerForm.name"
                    required
                    autofocus
                  >
                </div>
              </div>
              <div class="form-group row">
                <label for="email" class="col-md-4 col-form-label text-md-right">E-Mail Address</label>
                <div class="col-md-6">
                  <input
                    id="email"
                    type="email"
                    v-model="newOwnerForm.email"
                    class="form-control"
                    name="email"
                    value
                    required
                  >
                </div>
              </div>
              <div class="form-group row">
                <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>
                <div class="col-md-6">
                  <input
                    id="password"
                    type="password"
                    class="form-control"
                    name="password"
                    v-model="newOwnerForm.password"
                    required
                  >
                </div>
              </div>
              <div class="form-group row">
                <label
                  for="password-confirm"
                  class="col-md-4 col-form-label text-md-right"
                >Confirm Password</label>
                <div class="col-md-6">
                  <input
                    id="password-confirm"
                    type="password"
                    class="form-control"
                    name="password_confirmation"
                    v-model="newOwnerForm.password_confirmation"
                    required
                  >
                </div>
              </div>
              <div class="form-group row">
                <label for="owner-group" class="col-md-4 col-form-label text-md-right">Owner Group</label>
                <div class="col-md-6">
                  <select class="custom-select" v-on:change="onChange" required>
                    <option value="0">Select one group</option>
                    <option
                      :value="dg.group_code"
                      v-bind:key="dg.group_code"
                      v-for="dg in noOwners"
                    >{{dg.group_desc}}</option>
                  </select>
                </div>
              </div>
              <div class="form-group row mb-0">
                <div class="col-md-6 offset-md-4">
                  <button type="submit" class="btn btn-primary">Register</button>
                </div>
              </div>
            </form>
          </div>
        </div>
        <div class="tab-pane fade show active" id="type" role="tabpanel" aria-labelledby="type-tab">
          <div
            class="d-flex justify-content-center"
            v-if="ownerGroups.length == 0 && doc_group.length == 0 && noOwners.length == 0 && allUsers.length == 0"
            style="margin-top: 10vh"
          >
            <div class="spinner-grow text-primary" role="status">
              <span class="sr-only">Loading...</span>
            </div>
          </div>
          <span v-else>
            <table class="table table-sm table-hover">
              <caption>List of Owners</caption>
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Owner Group</th>
                  <th>Remove</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="og in ownerGroups">
                  <td>{{og.name}}</td>
                  <td>{{og.email}}</td>
                  <td>{{og.owner_group}}</td>
                  <td>
                    <button class="btn btn-sm btn-outline-danger" @click="removeOwner(og.id)">
                      <i class="fas fa-minus-circle" title="delete this person as owner"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>Add existing users as Owners
            <table class="table table-sm table-hover">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Owner Group</th>
                  <th>Edit</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <select class="custom-select" @change="newOwner" id="users">
                      <option readonly value="0">Select non-owner user</option>
                      <option v-for="user in allUsers" :value="user.id">{{user.name}}</option>
                    </select>
                  </td>
                  <td>
                    <select class="custom-select" @change="newOwnerGroup">
                      <option readonly value="0">Select document group</option>
                      <option v-for="no in noOwners" :value="no.group_code">{{no.group_desc}}</option>
                    </select>
                  </td>
                  <td>
                    <button
                      class="btn btn-sm btn-outline-success"
                      @click="makeNewOwner"
                      :disabled="(userSelected == '' || userSelectedGroup == '') ? true : false"
                    >
                      <i class="fas fa-pencil-alt" title="save group owner"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

  <script>
import swal from "sweetalert";
import { mapState } from "vuex";
export default {
  name: "Ownermanager",
  data() {
    return {
      newOwnerForm: {
        owner_group: ""
      },
      ownerGroups: [],
      noOwners: [],
      allUsers: [],
      userSelected: "",
      userSelectedGroup: ""
    };
  },
  methods: {
    onChange(e) {
      if (e.target.value != 0) {
        this.newOwnerForm.owner_group = e.target.value;
      } else {
        this.newOwnerForm.owner_group = "";
      }
    },
    newOwner(e) {
      if (e.target.value != 0) {
        this.userSelected = e.target.value;
      }
    },
    newOwnerGroup(e) {
      if (e.target.value != 0) {
        this.userSelectedGroup = e.target.value;
      }
    },
    getRelevantData() {
      axios.get(this.$route.path + "/get-relevant-data").then(response => {
        this.ownerGroups = response.data.owner_group;
        this.allUsers = response.data.users;
        this.noOwners = response.data.no_owner;
      });
    },
    makeNewOwner() {
      let newowner = new FormData();
      newowner.append("id", this.userSelected);
      newowner.append("group_code", this.userSelectedGroup);
      axios
        .post(this.$route.path + "/make-user-owner", newowner)
        .then(response => {
          if (response.data == this.userSelected) {
            this.getRelevantData();
            this.userSelected = this.userSelectedGroup = "";
            swal({
              icon: "success",
              title: "Success",
              timer: 3500
            });
          }
        });
    },
    removeOwner(id) {
      let removeOwner = new FormData();
      removeOwner.append("id", id);
      axios
        .post(this.$route.path + "/remove-owner", removeOwner)
        .then(response => {
          if (response.data == id) {
            this.getRelevantData();
            this.userSelected = this.userSelectedEmail = "";
            swal({
              icon: "success",
              title: "Success",
              timer: 3500
            });
          }
        });
    },
    registerOwner() {
      let newOwner = new FormData();
      newOwner.append("usercode", this.newOwnerForm.usercode);
      newOwner.append("name", this.newOwnerForm.name);
      newOwner.append("email", this.newOwnerForm.email);
      newOwner.append("password", this.newOwnerForm.password);
      newOwner.append(
        "password_confirmation",
        this.newOwnerForm.password_confirmation
      );
      newOwner.append(
        "owner_group",
        this.newOwnerForm.owner_group == "undefined"
          ? ""
          : this.newOwnerForm.owner_group
      );
      axios
        .post(this.$route.path + "/create-owner", newOwner)
        .then(response => {
          if (response.data > 0) {
            this.getRelevantData();
            this.newOwnerForm = "";
            swal({
              icon: "success",
              title: "Success",
              text: "New owner created",
              timer: 3000
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
            timer: 5000
          });
        });
    }
  },
  created() {
    this.getRelevantData();
  },
  computed: {
    ...mapState({
      doc_group: "doc_group"
    })
  },
  mounted() {}
};
</script>

  <style scoped>
h5 {
  border-bottom: 1px solid grey;
}
</style>