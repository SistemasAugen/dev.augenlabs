<template>
	<div class="row">
		<div class="col-md-offset-1 col-md-10">

			<div class="panel panel-primary" data-collapsed="0">

				<div class="panel-heading">
					<div class="panel-title">
						<i class="fa fa-user"></i> Usuario
					</div>
					<div class="panel-options">
						<a @click="$router.push('/users/')"><i class="fas fa-times"></i></a>
					</div>
				</div>

				<div class="panel-body">
					<form role="form" class="form-horizontal" @submit.prevent="newUser($event.target)">

						<input-form name="nombre" text="Nombre" :data.sync="user.name" validate="alpha_spaces|required|min:5"></input-form>
						<input-form name="email" text="Email" :data.sync="user.email" validate="email|required"></input-form>
						<input-form type="password" name="password" text="Password" :data.sync="user.password" :validate="rule_password" place="Solo si desea cambiarla"></input-form>
						<input-form type="tel" name="telefono" text="Telefono" :data.sync="user.phone" validate="digits:10"></input-form>
						<input-form type="tel" name="celular" text="Celular" :data.sync="user.celphone" validate="digits:10"></input-form>


						<div class="form-group">
							<label class="col-sm-3 control-label">Foto:</label>

							<div class="col-sm-7">

								<div class="fileinput fileinput-new" data-provides="fileinput">
									<div class="fileinput-new thumbnail" style="width: 200px; height: 150px;" data-trigger="fileinput">
										<img :src="user.imageUrl" alt="..." v-if="id!=''">
										<img src="//placehold.it/200x150?text=Imagen" alt="..." v-else>
									</div>
									<div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px"></div>
									<div>
										<span class="btn btn-white btn-file">
											<span class="fileinput-new">Seleccionar</span>
											<span class="fileinput-exists">Cambiar</span>
											<input type="file" accept="image/*" name="image">
										</span>
										<a href="#" class="btn btn-orange fileinput-exists" data-dismiss="fileinput">Eliminar</a>
									</div>
								</div>

							</div>
						</div>

						<select-form text="Roles" name="roles" :options="roles" :data.sync="user.roles" :multiple="true"></select-form>
						<select-form text="Laboratorio" name="laboratorio" :options="laboratories" :data.sync="user.laboratory" v-if="user.roles.indexOf('laboratorio')!=-1 || user.roles.indexOf('Status')!=-1"></select-form>
						<select-form text="Punto de venta" name="PDV" :options="branches" :data.sync="user.branch" v-if="user.roles.indexOf('punto de ventas') != -1 || user.roles.indexOf('Ejecutivo') != -1 || user.roles.indexOf('laboratorio') != -1"></select-form>

						<checkbox-form text="Acceso" :data.sync="user.access" value="1" name="access" key="a"></checkbox-form>

						<div class="form-group">
							<div class="col-sm-12">
								<button type="button" class="btn btn-danger" @click="deleteUser" v-show="$route.params.id"><i class="fa fa-trash"></i> Borrar</button>
								<button type="submit" class="btn btn-success pull-right"><i class="far fa-save"></i> Guardar</button>
								<button type="button" class="btn btn-default pull-right" @click="$router.push('/users/')">Cancelar</button>
							</div>
						</div>

					</form>
				</div>
			</div>
		</div>

	</div>
</template>
<script type="text/javascript">
	export default {
		data(){
			return {
				user:{
					name:"",
					email:"",
					password:"",
					phone:"",
					celphone:"",
					access:0,
					roles:[],
					laboratory:{value:"",label:"Selecciona uno.."},
					laboratory_id:"",
					branch:{value:"",label:"Selecciona uno.."},
					branch_id:"",
				},
				roles:{},
				laboratories:[],
				branches:[],
				id:"",
				date:"",
				check:false,
			}
		},
		computed:{
			rule_password:function(){
				if(this.user.password==undefined || this.user.password.length==0){
					return '';
				}
				else{
					return 'min:5|required';
				}
			},
		},
		methods:{
			getUser(){
				this.$parent.inPetition=true;

				axios.get(tools.url("/api/user/"+this.id))
				.then((response)=>{
			    	this.user=response.data;
			    	this.user.roles=jQuery.map(this.user.roles,(row)=>{
						return row.name;
					});
					if(this.user.laboratory){
						this.user.laboratory={value:this.user.laboratory.id,label:this.user.laboratory.name};
					}
					else{
						this.user.laboratory={value:"",label:"Selecciona uno.."};
					}
					if(this.user.branch){
						this.user.branch={value:this.user.branch.id,label:this.user.branch.name};
					}
					else{
						this.user.branch={value:"",label:"Selecciona uno.."};
					}

			    	this.$parent.inPetition=false;
			    }).catch((error)=>{
			    	this.$parent.handleErrors(error);
			        this.$parent.inPetition=false;
			    });
			},
			getRoles:function(){
				this.$parent.inPetition=true;
				axios.get(tools.url("/api/roles"))
				.then((response)=>{
					this.roles=response.data;
					this.roles=jQuery.map(this.roles,(row)=>{
						return row.name;
					});
					this.$parent.inPetition=false;
				})
				.catch((error)=>{
					this.$parent.handleErrors(error);
					this.$parent.inPetition=false;
				});
			},
			getLaboratories:function(){
				this.$parent.inPetition=true;
				axios.get(tools.url("/api/laboratories"))
				.then((response)=>{
					this.laboratories=response.data;
					this.laboratories=jQuery.map(this.laboratories,(row)=>{
						return {label:row.name,value:row.id};
					});
					this.$parent.inPetition=false;
				})
				.catch((error)=>{
					this.$parent.handleErrors(error);
					this.$parent.inPetition=false;
				});
			},
			getBranches:function(){
				this.$parent.inPetition=true;
				axios.get(tools.url("/api/branches"))
				.then((response)=>{
					this.branches=response.data;
					this.branches=jQuery.map(this.branches,(row)=>{
						return {label:row.name,value:row.id};
					});
					this.$parent.inPetition=false;
				})
				.catch((error)=>{
					this.$parent.handleErrors(error);
					this.$parent.inPetition=false;
				});
			},
			newUser(form){
				this.$parent.inPetition=true;
				this.$parent.validateAll(()=>{
					this.user.laboratory_id=this.user.laboratory.value;
					this.user.branch_id=this.user.branch.value;
					var data=tools.params(form,this.user);

					if(this.$route.params.id){
						axios.post(tools.url("/api/user/"+this.id),data)
						.then((response)=>{
					    	this.getUser();
					    	this.$parent.showMessage("Usuario "+this.user.name+" modificado correctamente!","success");
					    	this.$parent.inPetition=false;
					    }).catch((error)=>{
					    	this.$parent.handleErrors(error);
					        this.$parent.inPetition=false;
					    });
					}
					else{
						axios.post(tools.url("/api/user"),data)
						.then((response)=>{
							var user=response.data;
					    	this.$parent.showMessage("Usuario "+user.name+" agregado correctamente!","success");
					    	this.$router.push('/users/edit/'+user.id);
					    	this.$parent.inPetition=false;
					    }).catch((error)=>{
					    	this.$parent.handleErrors(error);
					        this.$parent.inPetition=false;
					    });
					}
				},(e)=>{
					console.log(e);
					this.$parent.inPetition=false;
				});
			},
			deleteUser:function(){
				alertify.confirm("Alerta!","¿Seguro que deseas borrar este usuario?",()=>{
					this.$parent.inPetition=true;
					axios.delete(tools.url("/api/user/"+this.id))
					.then((response)=>{
						this.$parent.showMessage(response.data.msg,"success");
						this.$router.push("/users/");
						this.$parent.inPetition=false;
					})
					.catch((error)=>{
						this.$parent.handleErrors(error);
				        this.$parent.inPetition=false;
					});
				},
				()=>{

				});
			},

		},
		mounted(){
			if(this.$route.params.id){
				this.id=this.$route.params.id;
				this.getUser();
			}
			this.getRoles();
			this.getLaboratories();
			this.getBranches();
		}
	}
</script>
