<template>
  <div class="row justify-content-between">
    <div class="col-md-10">
      <div class="d-flex justify-content-center" v-if="docInfo.length == 0">
        <div class="spinner-grow text-primary" role="status">
          <span class="sr-only">Loading...</span>
        </div>
      </div>
      <div v-else>
        <table class="table table-bordered">
          <tbody>
            <tr>
              <th>Document code, revision</th>
              <td>{{docInfoViewer.doc_code}}, {{docInfoViewer.doc_rev}}</td>
            </tr>
            <tr>
              <th>Document desc1</th>
              <td v-if="editEnabled.attr=='desc1'">
                <div class="input-group">
                  <input
                  type="text"
                  :value="docInfoViewer.desc1"
                  class="form-control"
                  @input="editEnabled.newData = $event.target.value"
                  >
                  <div class="input-group-append">
                    <button class="btn btn-sm btn-outline-success" title="Save"  
                    @click="makeChangeRequest"><i class="far fa-save"></i>
                  </button>
                  <button
                  class="btn btn-sm btn-outline-danger"
                  @click="editEnabled.attr=0;editEnabled.newData=''"
                  title="Cancel">
                  <i class="fas fa-ban"></i>
                </button>
              </div>
            </div>
          </td>
          <td v-else>{{docInfoViewer.desc1}}
            <button class="btn btn-xs btn-outline-primary float-right" @click="editEnabled.attr='desc1'" title="Enable Edit">
              <i class="fas fa-pencil-alt"></i>
            </button>
          </td>
        </tr>
        <tr>
          <th>Document Info</th>
          <td v-if="editEnabled.attr=='doc_info'">
            <div class="input-group">
              <input
              type="text"
              class="form-control"
              :value="docInfoViewer.doc_info"
              @input="editEnabled.newData = $event.target.value"
              >
              <div class="input-group-append">
                <button class="btn btn-sm btn-outline-success" title="Save" @click="makeChangeRequest"><i class="far fa-save"></i></button>
                <button
                class="btn btn-sm btn-outline-danger"
                @click="editEnabled.attr=0;editEnabled.newData=''"
                title="Cancel">
                <i class="fas fa-ban"></i>
              </button>
            </div>
          </div>
        </td>
        <td v-else>{{docInfoViewer.doc_info}}
          <button class="btn btn-xs btn-outline-primary float-right" @click="editEnabled.attr='doc_info'" title="Enable Edit">
            <i class="fas fa-pencil-alt"></i>
          </button>
        </td>
      </tr>
      <tr>
        <th>Document Group</th>
        <td v-if="editEnabled.attr=='doc_group'">
          <div class="input-group">
            <select class="custom-select" @change="editEnabled.newData = $event.target.value">
              <option v-for="dg in doc_group" :value="dg.group_code" 
              :hidden="accessList.findIndex(data => data.access_code == dg.group_code) == -1 ? true: false"
              :selected="dg.group_code == docInfo.doc_group ? true: false"
              :disabled="dg.group_code == docInfo.doc_group ? true: false"
              >
              {{dg.group_desc}}
            </option>
          </select>
          <div class="input-group-append">
            <button class="btn btn-sm btn-outline-success" title="Save"
            @click="makeChangeRequest"><i class="far fa-save"></i></button>
            <button
            class="btn btn-sm btn-outline-danger"
            @click="editEnabled.attr=0;editEnabled.newData=''"
            title="Cancel">
            <i class="fas fa-ban"></i>
          </button>
        </div>
      </div>
    </td>
    <td v-else>{{docInfoViewer.group_desc}}
      <button class="btn btn-xs btn-outline-primary float-right" @click="editEnabled.attr='doc_group'" title="Enable Edit">
        <i class="fas fa-pencil-alt"></i>
      </button>
    </td>
  </tr>
  <tr>
    <th>Document Type</th>
    <td v-if="editEnabled.attr=='doc_type'">
      <div class="input-group">
        <select class="custom-select" @change="editEnabled.newData = $event.target.value">
          <option v-for="dt in doc_type" :value="dt.doc_ext"
          :disabled="docInfo.doc_type == dt.doc_ext ? true: false"
          :selected="docInfo.doc_type == dt.doc_ext ? true: false">
          {{dt.type_code}}
        </option>
      </select>
      <div class="input-group-append">
        <button class="btn btn-sm btn-outline-success" title="Save" @click="makeChangeRequest"><i class="far fa-save"></i></button>
        <button
        class="btn btn-sm btn-outline-danger"
        @click="editEnabled.attr=0;editEnabled.newData='';docFile=''"
        title="Cancel">
        <i class="fas fa-ban"></i>
      </button>
    </div>
  </div>
</td>
<td v-else>{{docInfoViewer.type_code}}
  <button class="btn btn-xs btn-outline-primary float-right" @click="editEnabled.attr='doc_type'" title="Enable Edit">
    <i class="fas fa-pencil-alt"></i>
  </button>
</td>
</tr>
<tr v-if="editEnabled.attr=='doc_type' && editEnabled.newData != '' && editEnabled.newData != docInfo.doc_type">
  <th>New file</th>
  <td>
    <div class="input-group mb-3">
      <div class="col-sm-11 custom-file">
        <input
        type="file"
        class="custom-file-input"
        :accept="'.'+editEnabled.newData"
        @change="docFile = $event.target.files[0]"
        required
        >
        <label class="custom-file-label">{{docFile!=''?docFile.name:'Choose the document file'}}</label>
      </div>
    </div>
  </td>
</tr>
<tr>
  <th>Document Status</th>
  <td>{{docInfoViewer.status_desc}}</td>
</tr>
<tr>
  <th>Document Handler</th>
  <td>{{docInfoViewer.handler}}</td>
</tr>
</tbody>
</table>
<hr/>
<h5>Previous change requests made by you</h5>
  <table class="table table-sm fixed_header">
    <thead>
      <tr>
        <th>Attribute</th>
        <th>Requested value</th>
        <th>Create Date</th>
        <th>Request Status</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="cr in changeReqs">
        <td>{{cr.attr_name}}</td>
        <td>{{cr.new_value}}</td>
        <td>{{cr.created_at}}</td>
        <td v-if="cr.request_status=='approve'">Approved</td>
        <td v-else-if="cr.request_status == 'reject'">Rejected</td>
        <td v-else>{{cr.request_status}}</td>
      </tr>
    </tbody>
  </table>
</div>

</div>
</div>
</template>

<script>
  import swal from 'sweetalert';
  import { mapState } from "vuex";
  export default {
    name: "CR",
    props:['userInfo'],
    data() {
      return {
        userPermissions: [],
        docInfo: [],
        docInfoViewer: [],
        changeReqs: [],
        editEnabled: {
          attr: 0,
          newData: ""
        },
        docFile:"",
      };
    },
    watch: {
      "editEnabled.attr"(val) {
        this.editEnabled.attr = val;
      },
      "editEnabled.newData"(val) {
        this.editEnabled.newData = val;
      }
    },
    methods: {
      getDocInfo(id) {
        axios
        .get("/get-docinfo/" + id)
        .then(response => {
          if(response.data[0] == 'rejected'){
            swal({icon:"info",text:"Rejected documents do not has this feature",timer:2500});
            this.$router.push({path:response.data[1]});
          }
          this.docInfoViewer = response.data.viewer;
          this.docInfo = response.data.docInfo;
          this.changeReqs = response.data.change_req;
          this.changeReqs.forEach((value,index)=>{
            if(value.attr_name == 'desc1'){
              this.changeReqs[index].attr_name = 'Description1';
            }else if(value.attr_name == 'doc_group'){
              this.changeReqs[index].attr_name = 'Document Group';
              this.changeReqs[index].new_value = this.doc_group.filter(data=>data.group_code == value.new_value)[0].group_desc;
            }else if(value.attr_name == 'doc_info'){
              this.changeReqs[index].attr_name = 'Document Info';
            }else if(value.attr_name == 'doc_type'){
              this.changeReqs[index].attr_name = 'Document Type';
              this.changeReqs[index].new_value = this.doc_type.filter(data=>data.doc_ext == value.new_value)[0].type_code;
            }else{
              this.changeReqs[index].attr_name = 'New File'
            }
          });
        })
        .catch(error => {});
      },
      makeChangeRequest(){
        let changeReqForm = new FormData();
        changeReqForm.append('doc_id',this.$route.params.id);
        changeReqForm.append('attr_name',this.editEnabled.attr);
        changeReqForm.append('new_value',this.editEnabled.newData);
        changeReqForm.append('request_by',this.userInfo.usercode);
        this.docFile != ''? changeReqForm.append('doc_file',this.docFile) : '';
        if(this.editEnabled.newData != ''){
          axios.post(this.$route.path+'/create',changeReqForm)
          .then(response =>{
            if(response.data >= 1){
              this.getDocInfo(this.$route.params.id);
              this.editEnabled.attr = 0;this.editEnabled.newData = '';
              swal({
                icon:'success',text:'Your change request is saved',timer: 2500
              });
            }
          });
        }else{
          swal({
            icon:'info',text:'Selected field value not changed',timer:3000
          })
        }
      }
    },
    created() {
      var x = setInterval(() => {
        if (this.docInfo.length == 0) {
          this.getDocInfo(this.$route.params.id);
          clearInterval(x);
          
        }
      }, 1000);
    },
    computed: {
      ...mapState({
        doc_type: "doc_type",
        doc_group: "doc_group",
        doc_status: "doc_status",
        accessList: "user_access"
      })
    },
    mounted() {
      this.$store.dispatch("getDocTypeGroup");
      this.$store.dispatch("getUserAccess");
      this.editEnabled;
    }
  };
</script>
<style>
.btn-xs, .btn-group-xs > .btn {
  padding: 0.15rem 0.25rem;
  font-size: 0.5875rem;
  line-height: 1.15;
  border-radius: 0.2rem;
}

.fixed_header{ 
    table-layout: fixed;
    border-collapse: collapse;
}

.fixed_header tbody{
  display:block;
  width: 100%;
  overflow: auto;
  height: 20vh;
}

.fixed_header thead tr {
   display: block;
  width: 100%;
}

.fixed_header thead {
  background: black;
  color:#fff;
}

.fixed_header th, .fixed_header td {
  padding: 5px;
  text-align: left;
  width: 25rem;
}
</style>