<template>
	<div>
		<div class="row">
			<div class="col-md-12">
				<div id="toolbar">
			       
			        <a class="btn btn-success btn-sm" @click="newRow">
				            <i class="fa fa-plus"></i> Nueva
				    </a>
			        
                    <!-- <button class="btn btn-danger btn-sm" @click="deleteRows()">
			            <i class="fa fa-trash"></i> Borrar
			        </button>
					<button class="btn btn-info btn-sm" @click="printRows()">
			            <i class="fas fa-print"></i> Imprimir
			        </button>
			        <button class="btn btn-default btn-sm" @click="sendRows(1)">
			            <i class="fas fa-envelope"></i> Enviar por correo
			        </button> -->
			    </div>

				<div class="form-group">
					<label class="col-sm-3 control-label">Buscar cliente:</label>
					<div class="col-sm-7">
						<v-select v-model="customers_id" :filterable="true" :on-search="searchCustomersFunction" :options="customersOpcs" label="name" index="id" onfocus="this.removeAttribute('readonly');" id="familymember_id_search_select" @change="getOrders()">
						     <template slot="no-options">
						        Escriba el nombre para buscar...
						    </template>                       
					    </v-select>
					</div>
					<div class="col-sm-2">
						<button class="btn btn-info" @click="toggleAll(!hideAll)">{{ hideAll ? 'Mostrar' : 'Ocultar' }} todos los importes</button>
					</div>
				</div> 

				<table id="rowstable" data-pagination="false"></table>
			</div>
		</div>
		<sweet-modal ref="modalrxs" width="90%">
			<form role="form" class="form-horizontal" @submit.prevent="newRowNote($event.target)">
				<div class="row">
					<div class="col-sm-3" style="text-align: right;">
						<img src="/public/images/logo.png" width="150">
					</div>
					<label class="col-sm-5 control-label">Estado de cuenta:</label>
					<div class="col-sm-3">
						<input name="account_status" class="form-control" id="account_status" v-model="form.account_status">
					</div>
				</div>
						
				<div class="form-group">
                    <label class="col-sm-8 control-label">Semana:</label>
                    <div class="col-sm-3">
                        <input type="week" name="wekeend" class="form-control" id="camp-week" v-model="form.wekeend">
                    </div>
                </div>

                <div class="form-group">
					<div class="col-sm-8 control-label"><label>Doctor / Óptica:</label></div>
                            
                    <div class="col-sm-3">{{ form.customer_name }}</div>
                </div>
                    <br>
						<div class="row">
							<table id="tablemodels" style="margin-left:auto;margin-right: auto;" data-pagination="false">
								<tr>
									<th style="width: 15%;">Fecha</th>
									<th style="width: 15%;">RX´S</th>
									<th style="width: 30%;">Descripción</th>
									<th style="width: 15%;">Importe</th>
								</tr>
								<tr v-for="(rx,indx) in form.rxs" :key="indx">
									<td>{{rx.rx_fecha}}</td>
									<td>{{rx.rx}}</td>
									<td>{{rx.description}}</td>
									<td v-if="rx.status != 'garantia'">$  {{ Intl.NumberFormat("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(rx.total) }}</td>
									<td v-else><b>-$  {{ Intl.NumberFormat("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(rx.total) }}</b></td>
								</tr>

								<!-- <tr >
									
									<td colspan="2" style="border:none;text-align: left;">
										<button class="btn btn-info" @click="showAllRx = !showAllRx">Mostrar/Ocultar importes ocultos</button>
									</td>
									<p style="padding-top: 10px;text-align: right;" v-if="form.rxs.length > 0"><b>Subtotal: $</b></p>
									<th style="border-radius: 20px 20px 20px 20px" v-if="form.rxs.length > 0">$  {{ Intl.NumberFormat("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(form.subtotal) }}</th>
								</tr> -->
								<!-- <tr >
									
									<td colspan="2" style="border:none;text-align: left;"></td>
									<p style="padding-top: 10px;text-align: right;" v-if="form.rxs.length > 0"><b>IVA <small v-if="isFrontier">8%</small><small v-else>16%</small>: $</b></p>
									<th style="border-radius: 20px 20px 20px 20px" v-if="form.rxs.length > 0">$  {{ Intl.NumberFormat("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(form.tax) }}</th>
								</tr> -->
								<tr >
									
									<td colspan="2" style="border:none;text-align: left;"></td>
									<p style="padding-top: 10px;text-align: right;" v-if="form.rxs.length > 0"><b>Total: $</b></p>
									<th style="border-radius: 20px 20px 20px 20px" v-if="form.rxs.length > 0">$  {{ Intl.NumberFormat("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(form.total) }}</th>
								</tr>
							</table>

						</div>
                        <br>
                        
                        <br><br>
                        <text-form name="observations" text="Observaciones" :data.sync="form.observations" ></text-form>

						<div class="form-group">
							<div class="col-sm-12">

								<button class="btn btn-info btn-sm" @click="printRows()">
									<i class="fas fa-print"></i> Imprimir
								</button>
								<button class="btn btn-success btn-sm" @click="sendRows(1)">
									<i class="fas fa-envelope"></i> Enviar por correo
								</button>

							</div>
						</div>
			</form>
		</sweet-modal>
   
		<sweet-modal ref="modalEmails">
			<form role="form" class="form-horizontal" >

				<text-form name="name" text="Correos a enviar (separados por coma)" :data.sync="formemails" ></text-form>

				<div class="form-group">
					<div class="col-sm-12">
						
						<button type="button" class="btn btn-success pull-right" @click="sendRows(2)"><i class="far fa-save"></i> Enviar</button>
						<button type="button" class="btn btn-default pull-right" @click="$refs.modalEmails.closest()">Cancelar</button>
					</div>
				</div>
			</form>

			
        </sweet-modal>
		

		<sweet-modal ref="showRxModal" width="60%">

			<form role="form" class="form-horizontal" v-if="indx_show != null">
				<div>	
					<div class="col-sm-6" style="text-align: left;">
						<img src="https://dev.augenlabs.com/public/images/logo.png" width="25%">
						<div style="font-size: 25px;display: inline-block;padding-left: 10px;">|</div>
					</div>
					<div class="col-sm-3"></div>
					<div class="col-sm-3" style="text-align: right;">
						<h3><b>{{ orders[indx_show].branch.name }}</b></h3>
						<h4><b>{{ orders[indx_show].branch.laboratory.name }}</b></h4>
					</div>

				</div>
				<hr><br>

				<p style="text-align: left;"><b>CAPTURA DE DATOS</b></p>
				<div class="form-group">
						
					<div class="col-sm-3" style="text-align: left;">
						<label style="font-weight:300">RX:</label>
						<input v-model="orders[indx_show].rx_rx" disabled class="form-control" id="rx">
					</div>

						
					<div class="col-sm-3" style="text-align: left;">
						<label style="font-weight:300">FECHA:</label>
						<input v-model="orders[indx_show].rx_fecha" class="form-control" id="fecha" type="date" disabled>
					</div>

						
					<div class="col-sm-6" style="text-align: left;">
						<label style="font-weight:300">CLIENTE:</label>
						<input v-model="orders[indx_show].rx_cliente" disabled class="form-control" id="fecha">
				</div>
				</div>


				<br>
				<p style="text-align: left;"><b>GRADUACION</b></p>
				
				<div class="form-group">
								<div class="col-sm-2" style="text-align: left;">
					<label style="font-weight:300">OD ESFERA</label>
					<input v-model="orders[indx_show].rx_od_esfera" class="form-control" id="od_esfera" disabled>
								</div>
					<div class="col-sm-2" style="text-align: left;">
					<label style="font-weight:300">OD CILINDRO</label>
					<input v-model="orders[indx_show].rx_od_cilindro" class="form-control" id="od_cilindro" disabled>
								</div>
					<div class="col-sm-2" style="text-align: left;">
					<label style="font-weight:300">OD EJE</label>
					<input v-model="orders[indx_show].rx_od_eje" class="form-control" id="od_eje" disabled>
								</div>
					<div class="col-sm-2" style="text-align: left;">
					<label style="font-weight:300">OD ADICION</label>
					<input v-model="orders[indx_show].rx_od_adicion" class="form-control" id="od_adicion" disabled>
								</div>
					<div class="col-sm-2" style="text-align: left;">
					<label style="font-weight:300">OD DIP</label>
					<input v-model="orders[indx_show].rx_od_dip" class="form-control" id="od_dip" disabled>
								</div>
					<div class="col-sm-2" style="text-align: left;">
					<label style="font-weight:300">OD ALTURA</label>
					<input v-model="orders[indx_show].rx_od_altura" class="form-control" id="od_altura"  disabled>
								</div>
				</div>

				<div class="form-group">
								<div class="col-sm-2" style="text-align: left;">
					<label style="font-weight:300">OD ESFERA</label>
					<input v-model="orders[indx_show].rx_od_esfera_dos" class="form-control" id="od_esfera_dos" disabled>
								</div>
					<div class="col-sm-2" style="text-align: left;">
					<label style="font-weight:300">OD CILINDRO</label>
					<input v-model="orders[indx_show].rx_od_cilindro_dos" class="form-control" id="od_cilindro_dos" disabled>
								</div>
					<div class="col-sm-2" style="text-align: left;">
					<label style="font-weight:300">OD EJE</label>
					<input v-model="orders[indx_show].rx_od_eje_dos" class="form-control" id="od_eje_dos" disabled>
								</div>
					<div class="col-sm-2" style="text-align: left;">
					<label style="font-weight:300">OD ADICION</label>
					<input v-model="orders[indx_show].rx_od_adicion_dos" class="form-control" id="od_adicion_dos" disabled>
								</div>
					<div class="col-sm-2" style="text-align: left;">
					<label style="font-weight:300">OD DIP</label>
					<input v-model="orders[indx_show].rx_od_dip_dos" class="form-control" id="od_dip_dos" disabled>
								</div>
					<div class="col-sm-2" style="text-align: left;">
					<label style="font-weight:300">OD ALTURA</label>
					<input v-model="orders[indx_show].rx_od_altura_dos" class="form-control" id="od_altura_dos" disabled>
								</div>
				</div>


				<div class="form-group">
								<div class="col-sm-6" style="text-align: left;">
					<label style="font-weight:300">DISEÑO:</label>
					<input v-model="orders[indx_show].rx_diseno" disabled class="form-control" id="rx_diseno">
								</div>
					<div class="col-sm-6" style="text-align: left;">
					<label style="font-weight:300">MATERIAL:</label>
					<input v-model="orders[indx_show].rx_material" disabled class="form-control" id="rx_diseno">
								</div>
				</div>

				<div class="form-group">
								<div class="col-sm-6" style="text-align: left;">
					<label style="font-weight:300">TIPO AR:</label>
					<v-select v-model="orders[indx_show].rx_tipo_ar" :options="tipoarOpcs" label="label" index="value" disabled/>
								</div>
					<div class="col-sm-6" style="text-align: left;">
					<label style="font-weight:300">TALLADO:</label>
					<v-select v-model="orders[indx_show].rx_tallado" :options="talladoOpcs" label="label" index="value" disabled/>
								</div>
				</div>
				<br>
				<p style="text-align: left;"><b>SERVICIOS</b></p>
				<div class="form-group">

					<div class="col-sm-12" style="text-align: left;">
					
					<input v-model="orders[indx_show].rx_servicios" class="form-control" id="rx_servicios" disabled>
								</div>
				</div>

				<br>
				<p style="text-align: left;"><b>ARMAZÓN</b></p>
				<div class="form-group">

				<div class="col-sm-4" style="text-align: left;">
					<label style="font-weight:300;font-size: 13px;">TIPO DE ARMAZÓN:</label>
					<v-select v-model="orders[indx_show].rx_tipo_armazon" :options="tipo_armazonOpcs" label="label" index="value" disabled/>
				</div>
				<div class="col-sm-2" style="text-align: left;">
					<label style="font-weight:300;font-size: 13px;">HORIZONTAL "A"</label>
					<input v-model="orders[indx_show].rx_horizontal_a" class="form-control" id="rx_horizontal_a" disabled>
				</div>
				<div class="col-sm-2" style="text-align: left;">
					<label style="font-weight:300;font-size: 13px;">VERTICAL "B"</label>
					<input v-model="orders[indx_show].rx_vertical_b" class="form-control" id="vertical_b" disabled>
				</div>
				<div class="col-sm-2" style="text-align: left;">
					<label style="font-weight:300;font-size: 13px;">DIAGONAL "ED"</label>
					<input v-model="orders[indx_show].rx_diagonal_ed" class="form-control" id="diagonal_ed" disabled>
				</div>
				<div class="col-sm-2" style="text-align: left;">
					<label style="font-weight:300;font-size: 13px;">PUENTE</label>
					<input v-model="orders[indx_show].rx_puente" class="form-control" id="rx_puente" disabled>
				</div>
				</div>


				<br>
				<p style="text-align: left;"><b>OBSERVACIONES</b></p>
				<div class="form-group">

				<div class="col-sm-12" style="text-align: left;">
					<textarea v-model="orders[indx_show].rx_observaciones" class="form-control" id="rx_rx_observaciones" disabled></textarea>
				</div>
				</div>

				<div class="form-group">
					<div class="col-sm-12">
						<button type="button" class="btn btn-default pull-right" @click="$refs.showRxModal.close()">Cerrar</button>
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
				rows:{},
				formemails:null,
				customersOpcs:[],
				customers_id:null,
				client:{},
				orders:[],
				form:{
					customers_id:null,
					rxs:[],
					observations:null,
					account_status:null,
					wekeend:null,
					total:null
				},
				indx_show:null,
				disenoOpcs:[
					{value:'Monofocal HD',label:'Monofocal HD'},
					{value:'Monofocal Asferico HD',label:'Monofocal Asferico HD'},
					{value:'Flat-Top HD',label:'Flat-Top HD'},
					{value:'Progresivo HD',label:'Progresivo HD'},
					{value:'Progresivo Trinity 11-15 HD',label:'Progresivo Trinity 11-15 HD'},
					{value:'Progresivo Trinity 13-17 Freshman HD',label:'Progresivo Trinity 13-17 Freshman HD'},
					{value:'Progresivo Trinity 13-17 Hypersoft HD',label:'Progresivo Trinity 13-17 Hypersoft HD'},
					{value:'Progresivo Trinity 13-17 Profesional HD',label:'Progresivo Trinity 13-17 Profesional HD'},
					{value:'Progresivo 15-20 Urban HD',label:'Progresivo 15-20 Urban HD'},
					{value:'Progresivo Trinity 8-12 Mini HD',label:'Progresivo Trinity 8-12 Mini HD'},
					{value:'Progresivo Trinity Spacia HD',label:'Progresivo Trinity Spacia HD'},
				],
				materialOpcs:[
					{value:'Alto Índice',label:'Alto Índice'},
					{value:'Parasol',label:'Parasol'},
					{value:'Trivex',label:'Trivex'},
					{value:'Trivex Parasol',label:'Trivex Parasol'},
					{value:'Polarizado',label:'Polarizado'},
					{value:'B BLOCK',label:'B BLOCK'},
				],
				tipoarOpcs:[
					{value:'Ninguno',label:'Ninguno'},
					{value:'Matiz-e',label:'Matiz-e'},
					{value:'Gold',label:'Gold'},
					{value:'Azul',label:'Azul'},
					
				],
				talladoOpcs:[
					{value:'Digital',label:'Digital'},
					{value:'Free Form',label:'Free Form'},
				],
				tipo_armazonOpcs:[
					{value:'Metálico',label:'Metálico'},
					{value:'Perforado',label:'Perforado'},
					{value:'Plástico',label:'Plástico'},
					{value:'Ranurado',label:'Ranurado'},
				],
				isFrontier: null,
				showAllRx: false,
				hideAll: false
			}
		},
		methods:{
			mounthTable(){
				jQuery('#rowstable').bootstrapTable({
					columns: [
                        {
							field:"check",
							checkbox:true,
							align: 'center',
						},
						{
					        field: 'finish_date',
					        title: 'Fecha',
					        sortable:true,
							switchable:false,
							
					    },
						{
					        field: 'rx',
					        title: 'RX',
					        sortable:true,
							switchable:true,
							
					    },
                       
                        {
					        field: 'product.subcategory_name',
					        title: 'Material',
					        sortable:false,
							switchable:false,
							
					    },
                        {
					        field: 'product.name',
					        title: 'Diseño',
					        sortable:false,
							switchable:false,
							
					    },
                        {
					        field: 'product.type_name',
					        title: 'Caracteristicas',
					        sortable:false,
							switchable:false,
							
					    },
						{
					        field: 'rx_tipo_ar',
					        title: 'AR',
					        sortable:false,
							switchable:false,
							
					    },
						{
					        field: 'total_custom',
					        title: 'Total',
					        sortable:false,
							switchable:false,
							
					    },
						{
					        field: 'rxBtn',
					        title: 'RX',
					        sortable:false,
							switchable:false,
							
					    },
						{
					        field: 'actions',
					        title: 'Acciones',
					        sortable:false,
							switchable:false,
					    }
						
					],
					//Boton de refrescar
					showRefresh:true,
		                        		
				});

				jQuery('#rowstable').on('refresh.bs.table',()=>{
					this.getRows();
				});

				jQuery('#rowstable').on('click-row.bs.table',(e,row,data,$element)=>{
					if ($element == 'rxBtn') {
						this.indx_show = data[0]['sectionRowIndex'];
						this.$refs.showRxModal.open();
					}

					if($element == 'actions') {
						const $button = data.find('.btn-discount');
						const $priceTd = data.children('td').eq(7);
						const realTotal = $button.data('total');

						this.$parent.inPetition=true;
						const idx = data[0]['sectionRowIndex'];
						if($button.hasClass('btn-danger')) {
							// enable discount
							this.orders[idx].total = '0.00';
							this.orders[idx].subtotal = '0.00';
							$button.removeClass('btn-danger');
							$button.addClass('btn-success');

							$priceTd.html('<b>$ 0.00</b>');
							this.$parent.showMessage('Importe oculto');
						} else {
							// remove discount
							this.orders[idx].total = realTotal;
							this.orders[idx].subtotal = realTotal;
							$button.removeClass('btn-success');
							$button.addClass('btn-danger');

							let number = parseFloat(realTotal);
							if(this.orders[idx].status == 'garantia')
								number = -number;

							const formattedCurrency = number.toLocaleString('en-US', {
								style: 'currency',
								currency: 'USD' // Change the currency code as needed
							});

							$priceTd.html(formattedCurrency);
						}

						// jQuery('#rowstable').bootstrapTable('removeAll');
			    		// jQuery('#rowstable').bootstrapTable('append',this.orders);

						this.$parent.inPetition=false;
					}
				});

				//this.getRows();

			},
			
           
			printRows(){
				this.$parent.inPetition=true;
				this.$parent.validateAll(()=>{

					//var data=tools.params(form,this.row);
                
					axios.post(tools.url("/api/notes"),this.form).then((response)=>{
						this.$parent.inPetition=false;
						window.open(response.data);

					}).catch((error)=>{
					    	this.$parent.handleErrors(error);
					        this.$parent.inPetition=false;
					});
					
				},(e)=>{
					console.log(e);
					this.$parent.inPetition=false;
				});
			},
			sendRows(type){
				if (type == 1) {
					
					this.$refs.modalEmails.open();
				}
				else{
					this.form.emails = this.formemails;
					axios.post(tools.url("/api/notessend"),this.form).then((response)=>{
						this.$refs.modalEmails.close();
						this.$parent.inPetition=false;
						this.$parent.showMessage('Información enviada correctamte',"success");

					}).catch((error)=>{
					    	this.$parent.handleErrors(error);
					        this.$parent.inPetition=false;
					});

					/*this.$parent.inPetition=true;
					var rows=jQuery('#rowstable').bootstrapTable('getAllSelections');
					var params={};
					params.ids=jQuery.map(rows,(row)=>{
						return row.id;
					});
					params.emails = this.formemails;
					
					axios.post(tools.url('/api/notessend'),params)
					.then((response)=>{
						this.$refs.modalEmails.close();
						this.getRows();
						this.$parent.inPetition=false;
						
						this.$parent.showMessage('Información enviada correctamte',"success");
					})
					.catch((error)=>{
						this.$parent.handleErrors(error);
						this.$parent.inPetition=false;
					});*/
				}
			},
			searchCustomersFunction(search,loading){
        		if(search.length > 2){
	        		this.$parent.inPetition=true;
					axios.get("/api/client/search/" + search).then((response)=>{
						this.$parent.inPetition=false;
						this.customersOpcs = response.data;
					}).catch((error)=>{
						this.$parent.handleErrors(error);
						this.$parent.inPetition=false;
					});
			    }
        	},
			
			getOrders:function(){
				var customer_name = null;
				this.form.customers_id = this.customers_id;
				
				for (let x = 0; x < this.customersOpcs.length; x++) {
					if(this.customersOpcs[x]['id'] == this.customers_id){
						customer_name = this.customersOpcs[x]['comertial_name'];
					}
				}
				this.form.customer_name = customer_name;
				this.$parent.inPetition=true;
                axios.get(tools.url('api/ordersnotes/'+this.customers_id)).then((res)=>{
                    this.orders = res.data;
					this.hideAll = false;
					jQuery('#rowstable').bootstrapTable('removeAll');
			    	jQuery('#rowstable').bootstrapTable('append',this.orders);

					this.$parent.inPetition=false;
                })
                .catch((err)=>{
                    this.$parent.inPetition=false;
                    this.$parent.handleErrors(err);
                });
            },
			newRow(){
				var rows=jQuery('#rowstable').bootstrapTable('getAllSelections');
				if(rows.length==0){
					return false;
				}
				var rxs={};
				rxs = jQuery.map(rows,(row)=>{
					var aux = {
						'id':row.id,
						'rx_fecha':row.finish_date,
						'rx':row.rx,
						'rx_material':row.product.subcategory_name,
						'rx_diseno':row.product.name,
						'rx_caracteristicas':row.product.type_name,
						'rx_tipo_ar':row.rx_tipo_ar,
						'total_custom':row.total_custom,
						'total':row.total,
						'description': [ row.product.name, row.product.subcategory_name, row.product.type_name, row.rx_tipo_ar].filter(c => c != null).join(', '),
						'status': row.status,
						'subtotal': row.subtotal
					};
					return aux;
				});
				var total = 0;
				for (let z = 0; z < rxs.length; z++) {
					if(rxs[z]['status'] != 'garantia') {
						total = total + parseFloat(rxs[z]['total']);
					}
				}

				this.form.total = total;
				this.form.customers_id = this.customers_id;
				this.form.rxs = rxs;
				this.$refs.modalrxs.open();
				/*
				const customer = this.customersOpcs.filter(item => item.id == this.customers_id).shift();
				const frontierLaboratories = [2, 10];
				axios.get('/api/branch/' + customer.branch_id).then(result => {
					const laboratoryId = result.data.laboratory_id;
					if(frontierLaboratories.includes(laboratoryId)) {
						// it's frontier 8% of tax
						this.form.subtotal = this.form.total / 1.08;
						this.form.tax = this.form.total - this.form.subtotal;
						// this.form.tax = this.form.subtotal * 0.08;
						this.isFrontier = true;
					} else {
						// not frontier 16% of tax
						this.form.total = total;
						this.form.subtotal = this.form.total / 1.16;
						this.form.tax = this.form.total - this.form.subtotal;
						this.isFrontier = false;
					}

					//this.form.total = this.form.subtotal + this.form.tax;
					this.form.customers_id = this.customers_id;
					this.form.rxs = rxs;
					this.$refs.modalrxs.open();
				});*/
			},
			toggleAll(status) {
				this.hideAll = status;
				
				this.$parent.inPetition=true;
				$('#rowstable tbody tr').map((index, element) => {
					const data = $(element);
					const $button = data.find('.btn-discount');
						const $priceTd = data.children('td').eq(7);
						const realTotal = $button.data('total');

						const idx = data[0]['sectionRowIndex'];
						if($button.hasClass('btn-danger')) {
							// enable discount
							this.orders[index].total = '0.00';
							this.orders[index].subtotal = '0.00';
							$button.removeClass('btn-danger');
							$button.addClass('btn-success');

							$priceTd.html('<b>$ 0.00</b>');
							this.$parent.showMessage('Importe oculto');
						} else {
							// remove discount
							this.orders[index].total = realTotal;
							this.orders[index].subtotal = realTotal / 1.16;
							$button.removeClass('btn-success');
							$button.addClass('btn-danger');

							let number = parseFloat(realTotal);
							if(this.orders[index].status == 'garantia')
								number = -number;

							const formattedCurrency = number.toLocaleString('en-US', {
								style: 'currency',
								currency: 'USD' // Change the currency code as needed
							});

							$priceTd.html(formattedCurrency);
						}

						// jQuery('#rowstable').bootstrapTable('removeAll');
			    		// jQuery('#rowstable').bootstrapTable('append',this.orders);

					});
					this.$parent.inPetition=false;
			}
			/*newRowNote(form){
				this.$parent.inPetition=true;
				this.$parent.validateAll(()=>{

					//var data=tools.params(form,this.row);
                
					axios.post(tools.url("/api/notes"),this.row).then((response)=>{
							var row=response.data;
					    	this.$parent.showMessage("Registro agregado correctamente!","success");
					    	this.$router.push('/notes/edit/'+row.id);
					    	this.$parent.inPetition=false;
					}).catch((error)=>{
					    	this.$parent.handleErrors(error);
					        this.$parent.inPetition=false;
					});
					
				},(e)=>{
					console.log(e);
					this.$parent.inPetition=false;
				});
			},*/
		},
        mounted() {
            this.mounthTable();
            
        }
    }
</script>
<style>
#tablemodels{
  font-family: arial, sans-serif;
  border-collapse: separate !important;
  border-spacing:20px;

  width: 82%;
}

#tablemodels td {
  border-bottom: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
  text-align: center;

    

}
#tablemodels th {
  text-align: center;
  border: 1px solid #dddddd;
  padding: 8px;
  /* background-color: #dddddd; */
  border-radius: 20px 20px 0 0;
}

</style>