  <template>
    <div>
      <h5>Manage all user settings</h5>
      <div class="row justify-content-between">
        <div class="col-md-5">
          <select name="users" id="users" class="custom-select" v-on:change="getUserDetails" :disabled="usersWithPermission.length > 0 ? false: true">
            <option value="0">Select User</option>
            <option
            :value="user.usercode"
            v-bind:key="user.usercode"
            v-for="user in usersWithPermission"
            >{{user.name}}</option>
          </select>
        </div>
        <div class="col-md-3">
          <span v-if="userSelected != ''" style="float:right">
            <button
            class="btn btn-sm bg-my1"
            data-toggle="modal"
            data-target="#AddUserPermissionModal"
            >Change Access</button>
          </span>
        </div>
      </div>
      <hr>
      <div class="col-md-12" id="user-details" v-if="userSelected != ''">
        <table class="table table-sm table-bordered table-responsive-xs">
          <tbody>
            <tr>
              <th>Usercode: </th>
              <td>{{userSelected.usercode.toUpperCase()}}</td>
            </tr>
            <tr>
              <th>Name: </th>
              <td>{{userSelected.name}}</td>
            </tr>
            <tr>
              <th>Email: </th>
              <td>{{userSelected.email}}</td>
            </tr>
            <tr>
              <th>Date Joined: </th>
              <td>{{userSelected.created_at}}</td>
            </tr>
          </tbody>
        </table>
        <table class="table table-sm table-bordered table-responsive-xs">
          <caption>List of <i>{{userSelected.name}}</i> permissions</caption>
          <thead class="thead-light">
            <tr>
              <th>Access Code</th>
              <th>Access Description</th>
              <th><i class="fas fa-trash-alt"></i></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="perm in userPermissions">
              <td>{{perm.access_code}}</td>
              <td>{{permissionList.filter(data=>data.access_code == perm.access_code)[0].access_desc}}</td>
              <td><button class="btn btn-sm btn-outline-danger" @click="removeThisPermission(perm.access_code)"><i class="fas fa-minus-circle" title="delete this permission"></i></button></td>
            </tr>
          </tbody>
        </table>

      </div>
      <div class="col-md-12" v-else>
        <table class="table table-sm table-bordered">
          <caption>All user requests pending for granting</caption>
          <thead>
            <th>User</th><th>Access</th><th>Comment</th><th>Action</th>
          </thead>
          <tbody>
            <tr v-for="pr in permission_requests">
              <td>{{usersWithPermission.filter(data => data.usercode == pr.usercode)[0].name + '('+pr.usercode.toUpperCase()+')'}}</td>
              <td>{{permissionList.filter(data=>data.access_code == pr.access_code)[0].access_desc}}</td>
              <td>{{pr.comment}}<small>{{' &nbsp;&nbsp;'+pr.created_at.split(' ')[0]}}</small></td>
              <td>
                <div class="btn-group mb-3">
                  <button class="btn btn-xs btn-outline-success" title="Approve" @click="action(pr.id,'Approved')"><i class="fas fa-check-circle" style="font-size: 26px"></i></button>
                  <button class="btn btn-xs btn-outline-danger" title="Reject" @click="action(pr.id,'Rejected')"><i class="fas fa-times-circle" style="font-size: 26px"></i></button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="modal fade" id="AddUserPermissionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="false">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Add New</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <br>
            <div class="container">
              <div
              class="input-group mb-3"
              v-bind:key="permission.access_code"
              v-for="permission in permissionList"
              >
              <div class="input-group-prepend">
                <div class="input-group-text">
                  <input
                  v-on:click="prePermissionModify"
                  :data-group="permission.access_code"
                  type="checkbox"
                  aria-label="Checkbox for following text input"
                  :checked="userPermissions.findIndex(data => data.access_code ==permission.access_code)!= -1 ? true:false"
                  :disabled="userPermissions.findIndex(data => data.access_code ==permission.access_code)!= -1 ? true:false"
                  >
                </div>
              </div>
              <input
              type="text"
              class="form-control"
              aria-label="Text input with checkbox"
              :value="permission.access_desc"
              readonly
              >
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
            <button
            type="submit"
            class="btn btn-outline-success"
            v-on:click="modifyPermissions"
            >Save</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import swal from 'sweetalert';
  import { mapState } from "vuex";
  export default {
    name: "Usermanager",
    data() {
      return {
        permissionList: [],
        permission_requests:[],
        usersWithPermission: [],
        userSelected: [],
        userPermissions: [],
        toBeAdded: []
      };
    },
    watch:{
      userPermissions(val){
        this.userPermissions = val;
      },
      userSelected(val){
        this.userSelected = val;
      }
    },
    methods: {
      action(id,action){
        axios.post(this.$route.path+'/permision-request-action',{
          id:id,
          status:action
        }).then(res=>{
          if(res.data == id){
            this.getActiveUsers();
            swal({icon:'success',text:'Permission Granted',timer:2500});
          }
          else if(res.data == 0){            
            this.getActiveUsers();
            swal({icon:'warning',text:'Permission Denied',timer:2500});
          }
        });
      },
      getActiveUsers() {
        axios.get("/get-active-users").then(response => {
          this.permissionList = response.data.permissionList;
          this.usersWithPermission = response.data.users;
          this.permission_requests = response.data.permission_requests;
          if(this.userSelected != ""){
            let user = this.usersWithPermission.filter(data => data.usercode == this.userSelected.usercode)[0];
            this.userSelected = user;
            this.userPermissions = user.user_permission;
          }
        });
      },
      getUserDetails(e) {
        if(e.target.value != 0){
          let user = this.usersWithPermission.filter(
            data => data.usercode == e.target.value
            )[0];
          this.userSelected = user;
          this.userPermissions = user.user_permission;
        }else{
          this.userSelected = [];
        }
      },
      prePermissionModify(e) {
        if (e.target.checked) {
          if (
            this.toBeAdded.findIndex(data => data == e.target.dataset.group) == -1
            ) {
            this.toBeAdded.push(e.target.dataset.group);
        }
      } else {
        if (
          this.toBeAdded.findIndex(data => data == e.target.dataset.group) != -1
          ) {
          this.toBeAdded.splice(
            this.toBeAdded.findIndex(data => data == e.target.dataset.group),
            1
            );
      }
    }
  },
  modifyPermissions() {
    $("#AddUserPermissionModal").hide();
    let newAccessForm = new FormData();
    if (this.toBeAdded.length > 0) {
      newAccessForm.append("add", this.toBeAdded);
    }
    axios
    .post(this.$route.path + "/user-access-modify", {
      params: {
        add: this.toBeAdded,
        user: this.userSelected.usercode
      }
    })
    .then(response => {
      if (
        response.data ==
        parseInt(this.toBeAdded.length)
        ) {
        this.userPermissions = [];
      this.toBeAdded = [];
      this.getActiveUsers();
    }
  });
  },
  removeThisPermission(access_code){
    let rem = new FormData();
    rem.append("access_code",access_code);
    rem.append("usercode",this.userSelected.usercode);
    axios.post(this.$route.path + "/user-access-remove",rem)
    .then(response=>{

      this.getActiveUsers();
    }).catch(error=>{

    });
  }
},
created() {},
computed: {
  ...mapState([])
},
mounted() {
  this.getActiveUsers();
  this.permissionList;
  this.usersWithPermission;
  this.userSelected;
  this.userPermissions;
  this.toBeAdded;
}
}
</script>

<style scoped>
h5 {
  border-bottom: 1px solid grey;
}
</style>