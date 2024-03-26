<template>
	
	<div class="row">
	
		<!-- Profile Info and Notifications -->
		<div class="col-md-6 col-sm-8 clearfix">
	
			<ul class="user-info pull-left pull-none-xsm">
	
				<!-- Profile Info -->
				<li class="profile-info dropdown"><!-- add class "pull-right" if you want to place this from right -->
	
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						<img :src="profile()" alt="" class="img-circle" width="44" />
						{{this.$parent.user.name}}
					</a>
	
					<ul class="dropdown-menu">
	
						<!-- Reverse Caret -->
						<li class="caret"></li>
	
						<!-- Profile sub-links -->
						<li>
							<router-link to="/profile/"><i class="fa fa-edit"></i> Editar perfil</router-link>
							
						</li>

					</ul>
				</li>
	
			</ul>
			
			<ul class="user-info pull-left pull-right-xs pull-none-xsm">
	
				<!-- Raw Notifications -->
				<li class="notifications dropdown">
	
					<a class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" @click="readAll">
						<i class="fa fa-bell"></i>
						<span class="badge badge-info" v-if="unreaded!=0">{{unreaded}}</span>
					</a>
	
					<ul class="dropdown-menu">
						<li class="top">
							<p class="small">								
								Notificaciones:
							</p>
						</li>
						
						<li>
							<ul class="dropdown-menu-list scroller">
								<li v-for="notification in notifications" :class="'notification-'+notification.type">
									<template v-if="notification.type == 'rx'">
										<a href="#" @click="openRX(notification)">
											<i :class="notification.icon+' pull-right'"></i>
											
											<span class="line">
												<strong>{{notification.text}}</strong>
											</span>
											
											<span class="line small">
												{{notification.created_at}}
											</span>
										</a>
									</template>
									<template v-else>
										<router-link v-if="notification.url!=null" :to="notification.url">
											<i :class="notification.icon+' pull-right'"></i>
											
											<span class="line">
												<strong>{{notification.text}}</strong>
											</span>
											
											<span class="line small">
												{{notification.created_at}}
											</span>
										</router-link>
										<router-link to="/home" v-else>
											<i :class="notification.icon+' pull-right'"></i>
											
											<span class="line">
												<strong>{{notification.text}}</strong>
											</span>
											
											<span class="line small">
												{{notification.created_at}}
											</span>
										</router-link>
									</template>	
								</li>
								
							</ul>
						</li>
						
						<li class="external">
							<router-link to="/home" >Ver mas</router-link>
						</li>
					</ul>
	
				</li>		
			</ul>
	
		</div>
	
	
		<!-- Raw Links -->
		<div class="col-md-6 col-sm-4 clearfix hidden-xs">
	
			<ul class="list-inline links-list pull-right">	
				<li>
					<a @click="logout">
						Cerrar session <i class="entypo-logout right"></i>
					</a>
				</li>
			</ul>
	
		</div>
		<sweet-modal ref="showRxModal" width="60%">
            <form role="form" class="form-horizontal" v-if="order != null">
                <div>
                    <div class="col-sm-6" style="text-align: left;">
                        <img src="https://dev.augenlabs.com/public/images/logo.png" width="25%">
                        <div style="font-size: 25px;display: inline-block;padding-left: 10px;">|</div>
                    </div>
                    <div class="col-sm-3"></div>
                    <div class="col-sm-3" style="text-align: right;">
                        <h3><b>{{ order.branch.name }}</b></h3>
                        <h4><b>{{ order.branch.laboratory.name }}</b></h4>
                    </div>
                </div>
                <hr>
                <br>
                <p style="text-align: left;"><b>CAPTURA DE DATOS</b></p>
                <div class="form-group">
                    <div class="col-sm-3" style="text-align: left;">
                        <label style="font-weight:300">RX:</label>
                        <input v-model="order.rx_rx" class="form-control" id="rx" disabled>
                    </div>
                    <div class="col-sm-3" style="text-align: left;">
                        <label style="font-weight:300">FECHA:</label>
                        <input v-model="order.rx_fecha" class="form-control" id="fecha" type="date" disabled>
                    </div>
                    <div class="col-sm-6" style="text-align: left;">
                        <label style="font-weight:300">CLIENTE:</label>
                        <input v-model="order.rx_cliente" disabled class="form-control" id="fecha">
                    </div>
                </div>
                <br>
                <p style="text-align: left;"><b>GRADUACION</b></p>
                <div class="form-group">
                    <div class="col-sm-2" style="text-align: left;">
                        <label style="font-weight:300">OD ESFERA</label>
                        <input v-model="order.rx_od_esfera" class="form-control" id="od_esfera" disabled>
                    </div>
                    <div class="col-sm-2" style="text-align: left;">
                        <label style="font-weight:300">OD CILINDRO</label>
                        <input v-model="order.rx_od_cilindro" class="form-control" id="od_cilindro" disabled>
                    </div>
                    <div class="col-sm-2" style="text-align: left;">
                        <label style="font-weight:300">OD EJE</label>
                        <input v-model="order.rx_od_eje" class="form-control" id="od_eje" disabled>
                    </div>
                    <div class="col-sm-2" style="text-align: left;">
                        <label style="font-weight:300">OD ADICION</label>
                        <input v-model="order.rx_od_adicion" class="form-control" id="od_adicion" disabled>
                    </div>
                    <div class="col-sm-2" style="text-align: left;">
                        <label style="font-weight:300">OD DIP</label>
                        <input v-model="order.rx_od_dip" class="form-control" id="od_dip" disabled>
                    </div>
                    <div class="col-sm-2" style="text-align: left;">
                        <label style="font-weight:300">OD ALTURA</label>
                        <input v-model="order.rx_od_altura" class="form-control" id="od_altura" disabled>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-2" style="text-align: left;">
                        <label style="font-weight:300">OI ESFERA</label>
                        <input v-model="order.rx_od_esfera_dos" class="form-control" id="od_esfera_dos" disabled>
                    </div>
                    <div class="col-sm-2" style="text-align: left;">
                        <label style="font-weight:300">OI CILINDRO</label>
                        <input v-model="order.rx_od_cilindro_dos" class="form-control" id="od_cilindro_dos" disabled>
                    </div>
                    <div class="col-sm-2" style="text-align: left;">
                        <label style="font-weight:300">OI EJE</label>
                        <input v-model="order.rx_od_eje_dos" class="form-control" id="od_eje_dos" disabled>
                    </div>
                    <div class="col-sm-2" style="text-align: left;">
                        <label style="font-weight:300">OI ADICION</label>
                        <input v-model="order.rx_od_adicion_dos" class="form-control" id="od_adicion_dos" disabled>
                    </div>
                    <div class="col-sm-2" style="text-align: left;">
                        <label style="font-weight:300">OI DIP</label>
                        <input v-model="order.rx_od_dip_dos" class="form-control" id="od_dip_dos" disabled>
                    </div>
                    <div class="col-sm-2" style="text-align: left;">
                        <label style="font-weight:300">OI ALTURA</label>
                        <input v-model="order.rx_od_altura_dos" class="form-control" id="od_altura_dos" disabled>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-6" style="text-align: left;">
                        <label style="font-weight:300">DISEÑO:</label>
                        <input v-model="order.rx_diseno" class="form-control" id="rx_diseno" disabled>
                    </div>
                    <div class="col-sm-6" style="text-align: left;">
                        <label style="font-weight:300">MATERIAL:</label>
                        <input v-model="order.rx_material" class="form-control" id="rx_diseno" disabled>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-6" style="text-align: left;">
                        <label style="font-weight:300">TIPO AR:</label>
                        <v-select v-model="order.rx_tipo_ar" :options="tipoarOpcs" label="label" index="value" disabled/>
                    </div>
                    <div class="col-sm-6" style="text-align: left;">
                        <label style="font-weight:300">TALLADO:</label>
                        <v-select v-model="order.rx_tallado" :options="talladoOpcs" label="label" index="value" disabled/>
                    </div>
                </div>
                <br>
                <p style="text-align: left;"><b>SERVICIOS</b></p>
                <div class="form-group">
                    <div class="col-sm-12" style="text-align: left;">
                        <input v-model="order.rx_servicios" class="form-control" id="rx_servicios" disabled>
                    </div>
                </div>
                <br>
                <p style="text-align: left;"><b>ARMAZÓN</b></p>
                <div class="form-group">
                    <div class="col-sm-4" style="text-align: left;">
                        <label style="font-weight:300;font-size: 13px;">TIPO DE ARMAZÓN:</label>
                        <v-select v-model="order.rx_tipo_armazon" :options="tipo_armazonOpcs" label="label" index="value" disabled/>
                    </div>
                    <div class="col-sm-2" style="text-align: left;">
                        <label style="font-weight:300;font-size: 13px;">HORIZONTAL "A"</label>
                        <input v-model="order.rx_horizontal_a" class="form-control" id="rx_horizontal_a" disabled>
                    </div>
                    <div class="col-sm-2" style="text-align: left;">
                        <label style="font-weight:300;font-size: 13px;">VERTICAL "B"</label>
                        <input v-model="order.rx_vertical_b" class="form-control" id="vertical_b" disabled>
                    </div>
                    <div class="col-sm-2" style="text-align: left;">
                        <label style="font-weight:300;font-size: 13px;">DIAGONAL "ED"</label>
                        <input v-model="order.rx_diagonal_ed" class="form-control" id="diagonal_ed" disabled>
                    </div>
                    <div class="col-sm-2" style="text-align: left;">
                        <label style="font-weight:300;font-size: 13px;">PUENTE</label>
                        <input v-model="order.rx_puente" class="form-control" id="rx_puente" disabled>
                    </div>
                </div>
                <br>
                <p style="text-align: left;"><b>OBSERVACIONES</b></p>
                <div class="form-group">
                    <div class="col-sm-12" style="text-align: left;">
                        <textarea v-model="order.rx_observaciones" class="form-control" id="rx_rx_observaciones" disabled></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <button type="button" class="btn btn-success pull-right" @click="approveRX()" style="margin-right: 15px">Aceptar</button>
                        <button type="button" class="btn btn-danger pull-right" @click="rejectRX()" style="margin-right: 15px">Rechazar</button>
                    </div>
                </div>
            </form>
        </sweet-modal>
	
	</div>
	
</template>

<script type="text/javascript">
	export default {
		data(){
			return {
				notifications:{},
				order: null,
				notificationId: null,
				tipoarOpcs: [
					{value:'Ninguno',label:'Ninguno'},
					{value:'Matiz-e',label:'Matiz-e'},
					{value:'Gold',label:'Gold'},
					{value:'Azul',label:'Azul'},
					
				],
				talladoOpcs: [
					{value:'Digital',label:'Digital'},
					{value:'Free Form',label:'Free Form'},
					{value:'HD',label:'HD'},
				],
				tipo_armazonOpcs: [
					{value:'Metálico',label:'Metálico'},
					{value:'Perforado',label:'Perforado'},
					{value:'Plástico',label:'Plástico'},
					{value:'Ranurado',label:'Ranurado'},
				],
			}
		},
		computed:{
			unreaded:function(){
				let unread=jQuery.map(this.notification,(row)=>{
					if(row.unread==0)
						return row;
				});

				return unread.length;
			},
		},
		methods:{
			profile(){
				return window.tools.url('/img/'+this.$parent.user.img.key);
			},
			logout(){
				axios.post(tools.url("/api/logout")).then((response)=>{
			    	this.$parent.user={};
			    	this.$parent.token="";
			    	localStorage.removeItem('token');
			    	this.$parent.logged=false;
			    	this.$router.push('/');
			        this.inPetition=false;
			        
			    }).catch((error)=>{
			        this.error=error.response.data.error;			        
			        this.inPetition=false;
			    });
			},
			getNotifications:function(){
				// this.$parent.inPetition=true;
				axios.get(tools.url('/api/notifications/'+this.$parent.user.id+'/resume'))
				.then((response)=>{
					this.notifications=response.data;
					this.$parent.inPetition=false;
				})
				.catch(()=>{
					this.$parent.inPetition=false;
				});
			},
			readAll:function(){
				axios.post(tools.url('/api/notifications/'+this.$parent.user.id+'/read_all'))
				.then((response)=>{
					this.getNotifications();
					// this.$parent.inPetition=false;
				})
				.catch(()=>{
					// this.$parent.inPetition=false;
				});
			},
			openRX(notification) {
				this.order = null;
				this.notificationId = notification.id;
				const order = JSON.parse(notification.metadata);
				this.order = order;

				this.$refs.showRxModal.open();
			},
			approveRX() {
				this.$parent.inPetition = true;
				axios.post('api/v2/rx/approve/' + this.notificationId).then(result => {
					this.$parent.showMessage(result.data.msg, "success");
					this.$parent.inPetition = false;
					this.$refs.showRxModal.close();
					this.getNotifications();
				}, error => {
					this.$parent.inPetition = false;
					console.error(error);
				});
			},
			rejectRX() {
				this.parent.inPetition = true;
				axios.post('api/v2/rx/reject/' + this.notificationId).then(result => {
					this.$parent.showMessage(response.data.msg, "success");
					this.$parent.inPetition = false;
					this.$refs.showRxModal.close();
					this.getNotifications();
				}, error => {
					this.$parent.inPetition=false;
					console.error(error);
				});
			}
		},
        mounted() {
            this.getNotifications();
        }
    }
</script>