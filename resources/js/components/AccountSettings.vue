<template>
  <div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-md-10">
        <ul class="nav nav-pills justify-content-center" id="myTab" role="tablist">
          <li class="nav-item">
            <a
            class="nav-link active"
            id="profile-tab"
            data-toggle="tab"
            href="#profile"
            role="tab"
            aria-controls="profile"
            aria-selected="false"
            >
            <i class="fas fa-user"></i> Profile
          </a>
        </li>
        <li class="nav-item">
          <a
          class="nav-link"
          id="request-access-tab"
          data-toggle="tab"
          href="#request-access"
          role="tab"
          aria-controls="request-access"
          aria-selected="false"
          >
          <i class="fas fa-pray"></i> Request Access
        </a>
      </li>
    </ul>
    <hr>
    <div class="tab-content" id="myTabContent">
      <div
      class="tab-pane fade show active"
      id="profile"
      role="tabpanel"
      aria-labelledby="profile-tab"
      >
      <table class="table table-bordered" v-if="user.id > 0">
        <caption>Current profile details</caption>
        <tbody>
          <tr>
            <th>Username</th>
            <td>{{user.usercode}}</td>
          </tr>
          <tr>
            <th @click="editEnabled = 'name'">
              <i class="fas fa-pencil-alt"></i> Name
            </th>
            <td v-if="editEnabled == 'name'">
              <div class="input-group mb-2">
                <input
                type="text"
                :value="user.name"
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
        <td v-else>{{user.name}}</td>
      </tr>
      <tr>
        <th @click="editEnabled = 'email'">
          <i class="fas fa-pencil-alt"></i> Email
        </th>
        <td v-if="editEnabled == 'email'">
          <div class="input-group mb-2">
            <input
            type="text"
            :value="user.email"
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
    <td v-else>{{user.email}}</td>
  </tr>
  <tr>
    <th @click="editEnabled = 'password'">
      <i class="fas fa-pencil-alt"></i> Password
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
<tr>
  <td style="color: red;font-family: consolas;font-size: 11px;text-align: center;" colspan="2">
    click on the (
    <i class="fas fa-pencil-alt"></i> ) Signed field to edit them
  </td>
</tr>
</tbody>
</table>
<div class="d-flex justify-content-center" v-else style="margin-top: 10vh">
  <div class="spinner-grow text-primary" role="status">
    <span class="sr-only">Loading...</span>
  </div>
</div>
</div>
<div
class="tab-pane fade"
id="request-access"
role="tabpanel"
aria-labelledby="request-access-tab"
>
<div class="row">
  <div class="col-md-5 col-lg-5">
    <table class="table table-sm" v-if="accessList.length > 0">
      <caption>Current Access to Document Group</caption>
      <tr>
        <th>Docuemnt Groups</th>
      </tr>
      <tr v-for="(uaccess,index) in accessList">
        <td>{{index+1 +') '+ doc_group.filter(data => data.group_code == uaccess.access_code)[0].group_desc}} 
          - <small v-if="uaccess.access_code == userInfo.owner_group"> Owner</small>
        </td>
      </tr>
    </table>
    <div class="alert alert-info" v-else>User has no access</div>
  </div>
  <div class="col-md-5 col-lg-5">
    <table class="table table-sm" v-if="accessList != null">
      <caption>Request for permission</caption>
      <tbody>
        <tr>
          <th>Docuemnt Groups</th>
          <th>Action</th>
        </tr>
        <tr>
          <td colspan="2">
            <textarea
            class="form-control"
            placeholder="Enter valid reason for the access"
            id="comment"
            @input="comment = $event.target.value"
            ></textarea>
          </td>
        </tr>
        <tr>
          <td>
            <select class="custom-select" @change="onChange">
              <option value="0">-- Select --</option>
              <option
              v-for="ag in availableGroups"
              :value="ag.group_code"
              :disabled="requestedPermissions.findIndex(data => data.access_code==ag.group_code)==-1?false:
              requestedPermissions.filter(data => data.access_code==ag.group_code)[0].status=='Rejected'?false:true
              "
              >{{ag.group_desc}}</option>
            </select>
          </td>
          <td>
            <button
            type="button"
            class="btn btn-xs btn-outline-success"
            title="Save"
            @click="makeRequest"
            :disabled="requestForAccess==''?true:false"
            >
            <i class="fas fa-check-square" style="font-size: 26px"></i>
          </button>
        </td>
      </tr>
    </tbody>
  </table>
</div>
</div>
<div class="col-md-12">
  <table class="table table-sm table-bordered">
    <caption>User requests</caption>
    <thead>
      <th>Usercode</th>
      <th>Access requested</th>
      <th>Status</th>
      <th>Requested at</th>
      <th>Action</th>
    </thead>
    <tbody>
      <tr v-for="rp in requestedPermissions">
        <td>{{rp.usercode.toUpperCase()}}</td>
        <td>{{doc_group.filter(data => data.group_code == rp.access_code)[0].group_desc}}</td>
        <td>{{rp.status}}</td>
        <td>{{rp.created_at.split(' ')[0]}}</td>
        <td>
          <div class="input-group">
            <button
            type="button"
            class="btn btn-sm btn-outline-danger"
            title="Delete"
            :disabled="rp.status!='Not Approved'?true:false"
            @click="deleteRequest(rp.id)"
            >
            <i class="fas fa-trash"></i>
          </button>
        </div>
      </td>
    </tr>
  </tbody>
</table>
</div>
</div>
</div>
</div>
</div>
</div>
</template>

<script>
  import swal from "sweetalert";
  import { mapState } from "vuex";
  export default {
    name: "ASettings",
    props: ["userInfo"],
    data() {
      return {
        user: [],
        editEnabled: "",
        editedData: "",
        passwords: {
          oldPass: "",
          newPass: ""
        },
        requestedPermissions: [],
        availableGroups: [],
        requestForAccess: "",
        comment: ""
      };
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
      },
      requestedPermissions(val) {
        this.requestedPermissions = val;
      },
      availableGroups(val) {
        this.availableGroups = val;
      },
      requestForAccess(val) {
        this.requestForAccess = val;
      },
      comment(val) {
        this.comment = val;
      }
    },
    methods: {
      getUserInfo() {
        axios.get("/get-user-info").then(response => {
          this.user = response.data;
        });
        this.getUserRequests();
      },
      getUserRequests() {
        axios.get(this.$route.path + "/requested-permissions").then(response => {
          this.requestedPermissions = response.data;
        });
      },
      update(field) {
        let updateForm = new FormData();
        updateForm.append("id", this.user.id);
        updateForm.append("attr", field);
        updateForm.append("value", this.editedData);

        if (this.editedData != "") {
          axios
          .post(this.$route.path + "/update-profile", updateForm)
          .then(response => {
            if (response.data == this.user.id) {
              swal({
                title: "Success",
                icon: "success",
                text: "Profile updated",
                timer: "2000"
              });
              this.getUserInfo();
              this.editEnabled = this.editedData = "";
            }
          });
        } else {
          swal({
            icon: "info",
            text: "Make some changes first",
            button: false,
            timer: 2000
          });
        }
      },
      updatePassword() {
        let upwd = new FormData();
        upwd.append("id", this.user.id);
        upwd.append("oldPass", this.passwords.oldPass);
        upwd.append("newPass", this.passwords.newPass);
        axios
        .post(this.$route.path + "/update-password", upwd)
        .then(response => {
          if (response.data == this.user.id) {
            this.$router.go(0);
          }
        })
        .catch(error => {
          if (error.response.data == "123") {
            swal({
              icon: "error",
              text: "Old password did not match",
              timer: 3000
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
      },
      onChange(e) {
        this.requestForAccess = e.target.value;
      },
      makeRequest() {
        let makeRequest = new FormData();
        makeRequest.append("access_code", this.requestForAccess);
        makeRequest.append("comment", this.comment);
        axios
        .post(this.$route.path + "/make-request", makeRequest)
        .then(response => {
          if (response.data == 1) {
            (this.comment = ""), $("#comment").val("");
            this.getUserRequests();
            swal({
              icon: "success",
              text: "Permission request successfully saved",
              timer: "2000"
            });
          }
        });
      },
      deleteRequest(id) {
        axios
        .post(this.$route.path + "/delete-old-request", {
          id: id
        })
        .then(response => {
          if (response.data == id) {
            this.getUserRequests();
            swal({ icon: "success", timer: 1000, button: false });
          }
        });
      }
    },
    computed: {
      ...mapState({
        doc_group: "doc_group",
        accessList: "user_access"
      })
    },
    created() {
      var x = setInterval(() => {
        if (this.accessList.length > 0) {
          this.doc_group.forEach((value, index) => {
            if (
              this.accessList.findIndex(
                data => data.access_code == value.group_code
                ) == -1
              ) {
              this.availableGroups.push(this.doc_group[index]);
          }
        });
          clearInterval(x);
        } else if (this.doc_group.length > 0) {
          this.doc_group.forEach((value, index) => {
            if (
              this.accessList.findIndex(
                data => data.access_code == value.group_code
                ) == -1
              ) {
              this.availableGroups.push(this.doc_group[index]);
          }
        });
          clearInterval(x);
        }
      }, 800);
    },
    mounted() {
      this.$store.dispatch("getDocTypeGroup");
      this.$store.dispatch("getUserAccess");
      this.getUserInfo();
    }
  };
</script>
<style scoped>
h6 {
  border-bottom: 1px solid lightgrey;
}
</style>
