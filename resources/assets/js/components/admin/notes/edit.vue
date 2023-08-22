<template>
	<div class="row">
		<div class="col-md-offset-1 col-md-10">

			<div class="panel panel-primary" data-collapsed="0">

				<div class="panel-heading">
					<div class="panel-title">
						<i class="fas fa-sticky-note"></i> Notas
					</div>
					<div class="panel-options">
						<a @click="$router.push('/notes/')"><i class="fas fa-times"></i></a>
					</div>
				</div>

				<div class="panel-body">
					<form role="form" class="form-horizontal" @submit.prevent="newRow($event.target)">
						<div class="row">
							<div class="col-sm-3" style="text-align: right;">
								<img src="/public/images/logo.png" width="150">
							</div>
							<label class="col-sm-5 control-label">Estado de cuenta:</label>
							
				
							<div class="col-sm-3">
									<input name="account_status" class="form-control" id="account_status" v-model="row.account_status">
								
							</div>
						</div>
						

						<!-- <input-form name="name" text="Estado de cuenta" :data.sync="row.account_status" ></input-form> -->
                        <!-- <input-form name="wekeend" text="Semana" :data.sync="row.wekeend" ></input-form> -->

						<div class="form-group">
                            <label class="col-sm-8 control-label">Semana:</label>
                            <div class="col-sm-3">
                                <input type="week" name="wekeend" class="form-control" id="camp-week" v-model="row.wekeend">
                            </div>
                        </div>

						

                        <div class="form-group">
							<div class="col-sm-1">
								
							</div>
                            
                            <div class="col-sm-10">
								<label>Doctor / Óptica:</label>
                                <v-select v-model="row.clients_id" :options="clientsOpcs" label="name" index="id" :filterable="true" :on-search="searchCustomers" @change="getOrders">
									<template slot="no-options">
										Escriba el nombre del cliente para buscar...
									</template> 
								</v-select>
                            </div>
                        </div>
                        <br>
						<div class="row">
							<table id="tablemodels" style="margin-left:auto;margin-right: auto;">
								<tr>
									<th style="width: 15%;">Fecha</th>
									<th style="width: 15%;">RX´S</th>
									<th style="width: 30%;">Descripción</th>
									<th style="width: 15%;">Importe</th>
								</tr>
								<tr v-for="(rx,indx) in row.rxs" :key="indx">
									<td><input type="date" v-model="rx.date"></td>
									<td> <v-select v-model="rx.orders_id" :options="ordersOpcs" label="rx" index="id" @change="chageRx(indx)"/></td>
									<td><textarea style="width: 100%;" v-model="rx.description"></textarea></td>
									<td>$  {{ Intl.NumberFormat("en-US", { minimumFractionDigits: 2 }).format(rx.amount) }}</td>
								</tr>

								<tr >
									<td colspan="2" style="border:none;text-align: left;"><button type="button" class="btn btn-info btn-sm" @click="addRx"><i class="fa fa-plus"></i> Agregar RX</button></td>
									
									<p style="padding-top: 10px;text-align: right;" v-if="row.rxs.length > 0"><b>Total: $</b></p>
									<th style="border-radius: 20px 20px 20px 20px" v-if="row.rxs.length > 0">$  {{ Intl.NumberFormat("en-US", { minimumFractionDigits: 2 }).format(total) }}</th>
								</tr>
							</table>

						</div>
                        <br>
                        
                        <br><br>
                        <text-form name="observations" text="Observaciones" :data.sync="row.observations" ></text-form>

						<div class="form-group">
							<div class="col-sm-12">
								<button type="button" class="btn btn-danger" @click="deleteRow" v-show="$route.params.id"><i class="fa fa-trash"></i> Borrar</button>
								<button type="submit" class="btn btn-success pull-right"><i class="far fa-save"></i> Guardar</button>
								<button type="button" class="btn btn-default pull-right" @click="$router.push('/notes/')">Cancelar</button>
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
				row:{
					rxs:[],
				},
                clientsOpcs:[],
                ordersOpcs:[]

			}
		},
        computed:{
            total: function(){
                var total = 0;
                for (let x = 0; x < this.row.rxs.length; x++) {
                    total += parseFloat(this.row.rxs[x]['amount']);
                }
                return total;
            },
        },
        watch:{
           
        },
		methods:{
			getRow(){
				this.$parent.inPetition=true;

				axios.get(tools.url("/api/notes/"+this.id))
				.then((response)=>{
			    	this.row=response.data;
			    	this.searchCustomers(this.row.client_name);
			    	this.$parent.inPetition=false;
			    }).catch((error)=>{
			    	this.$parent.handleErrors(error);
			        this.$parent.inPetition=false;
			    });
			},
			
			newRow(form){
				this.$parent.inPetition=true;
				this.$parent.validateAll(()=>{

					//var data=tools.params(form,this.row);
                    this.row.total = this.total;
					if(this.$route.params.id){
						axios.post(tools.url("/api/notes/"+this.id),this.row)
						.then((response)=>{
					    	this.getRow();
					    	this.$parent.showMessage("Registro modificado correctamente!","success");
					    	this.$parent.inPetition=false;
					    }).catch((error)=>{
					    	this.$parent.handleErrors(error);
					        this.$parent.inPetition=false;
					    });
					}
					else{
						axios.post(tools.url("/api/notes"),this.row)
						.then((response)=>{
							var row=response.data;
					    	this.$parent.showMessage("Registro agregado correctamente!","success");
					    	this.$router.push('/notes/edit/'+row.id);
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
			deleteRow:function(){
				alertify.confirm("Alerta!","¿Seguro que deseas borrar esta sucursal?",()=>{
					this.$parent.inPetition=true;
					axios.delete(tools.url("/api/notes/"+this.id))
					.then((response)=>{
						this.$parent.showMessage(response.data.msg,"success");
						this.$router.push("/notes/");
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
            searchCustomers(search){
                if(search.length > 2){
	        		this.$parent.inPetition=true;
                    axios.get('/api/clients?search='+ search + '&ajax=true')
                    .then((response)=>{
                        this.$parent.inPetition=false;
                        this.clientsOpcs = response.data.rows;
                    }).catch((error)=>{
                         
                    });
			    }
                
            },
            addRx(){
                var aux = {
                    date:null,
                    orders_id:null,
                    description:null,
                    amount:0
                };

                this.row.rxs.push(aux);
            },
            chageRx(indx){
				console.log(indx);
                var auxrow = null;
                for (let z = 0; z < this.ordersOpcs.length; z++) {
                    if (this.ordersOpcs[z]['id'] == this.row.rxs[indx]['orders_id']) {
                        auxrow = this.ordersOpcs[z];
                    }
                }
				if (auxrow) {
					this.row.rxs[indx]['amount'] = auxrow['total'];
				}
                
            },
            getOrders:function(){
                
                axios.get(tools.url('api/ordersnotes/'+this.row.clients_id))
                .then((res)=>{
                    
                    this.ordersOpcs=res.data;
                })
                .catch((err)=>{
                    this.$parent.inPetition=false;
                    this.$parent.handleErrors(err);
                });
            },
            isNumber: function(evt) {
				evt = (evt) ? evt : window.event;
				var charCode = (evt.which) ? evt.which : evt.keyCode;
				if ((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode !== 46) {
					evt.preventDefault();
				} else {
					return true;
				}
			},

		},
		mounted(){
			if(this.$route.params.id){
				this.id=this.$route.params.id;
				this.getRow();
			}
			

            
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