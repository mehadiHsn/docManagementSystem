<template>
	<div class="container-fluid">
		<div class="row greetings">
			<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
				<center>
					<h1>Document Management System</h1>
				</center>
				<div class="footer">					
					<h3>To access our system please</h3>
					<button class="btn my-btn btn-outline-secondary" @click="goto('login')">Login</button> <i class="fas fa-slash" style="font-size: 20px"></i> 
					<button class="btn my-btn btn-outline-secondary" @click="goto('register')">Register</button>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
	export default{
		name:"nologin",
		methods:{
			goto(action){
				if(action == 'login'){
					window.location = '/login';
				}else{
					window.location = '/register';
				}
			}
		}
	}
</script>
<style scoped>
@import url('https://fonts.googleapis.com/css?family=Lato');

@media screen and (min-width:650px),screen and (min-height: 560px){
	div.greetings{
		margin: 5vh;
		padding: 1rem; 
		min-height: 90vh;
		background: rgba(93, 94, 96,0.75);
		vertical-align: middle;
	}
	h1{
		color: ghostwhite;
		font-family: 'Lato', sans-serif;
		font-variant: small-caps;
		text-overflow: ellipsis;
		border-bottom: 4px double ghostwhite;
	}
	h3{
		color: ghostwhite;
		font-family: 'Lato', sans-serif;
		text-transform: capitalize;
		font-variant: small-caps;
		text-overflow: ellipsis;
		border-top: 4px double ghostwhite;
	}
	.footer{
		position: relative;top:60vh;text-align: center;align-content: center;color:ghostwhite;
	}
	.my-btn{
		color: ghostwhite; padding: 0.9rem;border: 1px solid ghostwhite;
	}
}
@media screen and (max-width:649px),screen and (max-height: 559px){
	div.greetings{
		margin: 5vh;
		padding: 0.5rem; 
		min-height: 90vh;
		background: rgba(93, 94, 96,0.75);
		vertical-align: middle;
	}
	h1{
		color: ghostwhite;
		font-size: 1.6rem;
		font-family: 'Lato', sans-serif;
		text-overflow: ellipsis;
		border-bottom: 1px solid ghostwhite;
	}
	h3{
		color: ghostwhite;
		font-size: 1.15rem;
		font-family: 'Lato', sans-serif;
		text-transform: capitalize;
		font-variant: small-caps;
		text-overflow: ellipsis;
		border-top: 1px solid ghostwhite;
	}
	.footer{
		position: relative;top:50vh;text-align: center;align-content: center;color:ghostwhite;
	}
	.my-btn{
		color: ghostwhite; padding: 0.35rem 0.45rem;border: 1px solid ghostwhite;
	}
}


</style>