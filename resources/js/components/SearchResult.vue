<template>
	<div>
		<table class="table table-lg table-bordered">
			<caption>Search result</caption>
			<thead class="thead-dark">
				<tr>
					<th>Document Code/Rev</th>
					<th>Short Description</th>
					<th>Detailed Info</th>
					<th>Document Group</th>
					<th>Document Type</th>
					<th>Document Handler</th>
					<th>Created</th>
					<th>File view</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="sr in searchRes.result">
					<td>
						{{sr.doc_code +'/'+sr.doc_rev}}
					</td>
					<td>
						{{sr.desc1}}
					</td>
					<td>
						{{sr.doc_info}}
					</td>
					<td>
						{{sr.group_desc}}
					</td>
					<td>
						{{sr.type_code}}
					</td>
					<td>
						{{sr.handler}}
					</td>
					<td>
						{{sr.created_at.split(' ')[0]}}
					</td>
					<td>
						<button class="btn btn-outline-primary"
						:disabled="searchRes.my_permission.findIndex(data=>data.access_code == sr.group_code) == -1 ? true: false"
						@click="viewDocument(sr.doc_id,sr.doc_code,sr.doc_rev,sr.file_path)">
						<i class="fas fa-file-alt"></i>
					</button>
				</td>
			</tr>
		</tbody>
	</table>
	<!-- Modal -->
	<div
	class="modal fade bd-example-modal-lg"
	tabindex="-1"
	role="dialog"
	id="docPreviewModal"
	ref="docPreviewModal"
	v-if="userInfo != 0"
	>
	<div class="modal-dialogs modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"></h5>
				<button type="button" class="btn-sm close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="file-preview" id="file_preview"></div>
			<div class="modal-footer">
				<span id="download-file"></span>
				<button type="button" class="btn btn-sm btn-outline-danger" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
</div>
</template>

<script>
	import { mapState  } from "vuex";
	export default {
		name: "searchresult",
		props: {
			userInfo:{
				type: Object
			},
			searchRes:{
				type: Object
			}
		},
		methods:{
			viewDocument(id,code,rev,file_path){
				$(".modal-title").text(code+"/"+rev);
				$("#download-file").html(
'<a href="/storage/'+file_path+'" download class="btn btn-outline-primary">Download</a>'
					);
				var str = file_path;
				var [filename, extension] = str.split(".").reduce(
					(acc, val, i, arr) =>
					i == arr.length - 1
					? [acc[0].substring(1), val]
					: [[acc[0], val].join(".")],
					[]
					);

				if (extension == "docx" || extension == "doc") {
					if (window.location.hostname == "localhost") {
						let file_url =
						window.location.protocol +
						"//" +
						window.location.hostname +
						":" +
						window.location.port +
						"/storage/" +
						file_path;
						window.open(file_url, "_blank");
					} else {
						let file_url =
						window.location.protocol +
						"//" +
						window.location.hostname +
						"/storage/" +
						file_path;
						window.open(
							"https://docs.google.com/viewer?url=" + file_url,
							"_blank"
							);
					}
				} else {
					let file_url =
					window.location.hostname == "localhost"
					? window.location.protocol +
					"//" +
					window.location.hostname +
					":" +
					window.location.port +
					"/storage/" +
					file_path
					: window.location.protocol +
					"//" +
					window.location.hostname +
					"/storage/" +
					file_path;

					document.getElementById("file_preview").innerHTML =
					"<object data='/storage/" +
					file_path +
					"' width='100%' height='100%'><iframe src='https://docs.google.com/viewer?url=" +
					file_url +
					"&embedded=true'></iframe></object>";
					$("#docPreviewModal").modal("show");
				}
			}
		},
		mounted() {

		}
	};
</script>

<style scoped>
	iframe {
  width: 100%;
  height: 100%;
  padding: 0;
  margin: 0;
}
.file-preview {
  position: relative;
  flex: 1 1 auto;
  padding: 0.26rem;
  height: 80vh;
  min-height: 80vh;
}
.modal-footer,
.modal-header {
  padding: 0.6rem;
}
.modal-dialogs {
  max-width: 95vw;
  margin: 0.75rem auto;
}
</style>