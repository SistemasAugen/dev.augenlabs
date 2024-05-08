<template>
    <div>
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-primary" data-collapsed="0">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <i class="fab fa-odnoklassniki"></i> Clientes
                        </div>
                    </div>

                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-8">
                                <input type="text" name="" id="" class="form-control" v-model="client" @keydown.enter="searchClient">
                            </div>
                            <div class="col-md-4"><button class="btn btn-success" @click="searchClient">Buscar cliente</button></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-primary" data-collapsed="0">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <i class="fas fa-clipboard"></i> RX
                        </div>
                    </div>

                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-8">
                                <input type="text" name="" id="" class="form-control" v-model="rx" @keydown.enter="searchRX">
                            </div>
                            <div class="col-md-4"><button class="btn btn-success" @click="searchRX">Buscar RX</button></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12" v-if="clients.length>0">
                    <table class="table responsive" id="clients_table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombres</th>
                                <th>Nombre comercial</th>
                                <th>Email</th>
                                <th>Celular</th>
                                <th>Domicilio</th>
                                <th>RX pendientes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="client in clients" :key="client.id">
                                <td>{{ client.id }}</td>
                                <td>{{ client.name }}</td>
                                <td>{{ client.comertial_name }}</td>
                                <td>{{ client.email }}</td>
                                <td>{{ client.celphone }}</td>
                                <td>{{ client.address }}</td>
                                <td><button class="btn btn-sm" @click="selectClient(client.id)">RX's</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div style="display:none;">
                <div id="orders_modal">
                    <h4>RX pendientes:</h4>
                    <table class="table responsive">
                        <thead>
                            <tr>
                                <th>RX</th>
                                <th>Producto</th>
                                <th>Cliente</th>
                                <th>Nombre Comercial</th>
                                <th>Antireflejante</th>
                                <th>Costo</th>
                                <th>Servicio</th>
                                <th>Subtotal</th>
                                <th>Descuentos sistema</th>
                                <th>Descuento</th>
                                <th>Descuento Promoción</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Fecha de captura</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(cart,i) in orders" :key="i">
                                <td>{{ cart.rx }}</td>
                                <td>{{ cart.product ? cart.product.name : 'N/A' }}</td>
                                <td>{{ cart.client ? cart.client.name : 'CLIENTE ELIMINADO' }}</td>
                                <td>{{ cart.client ? cart.client.comertial_name : 'CLIENTE ELIMINADO' }}</td>
                                <td v-if="cart.extras[0]">
                                    {{ cart.extras.map(row =>{return row.name}).join(", ") }}
                                </td>
                                <td v-else>
                                    -
                                </td>
                                <td>${{ cart.price }}</td>
                                <td>${{ cart.service }}</td>
                                <td>${{ cart.total }}</td>
                                <td>{{ cart.discount | currency }}</td>
                                <td>{{ cart.discount_admin | currency }}</td>
                                <td>
                                    <span v-if="cart.promo_discount != null">$ {{ cart.promo_discount }}</span>
                                    <span v-else>N/A</span>
                                </td>
                                <td v-if="!cart.cancelled">${{ cart.total_real }}</td>
                                <td v-else>{{ 0 | currency }} </td>
                                <button class="btn" @click="selectOrder(cart, i)">{{ cart.status.replace("_"," ") }}</button>
                                <td>{{ cart.created_at }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div id="status_table">
                    <div class="row">
                        <div class="col-md-12">
                            <h3>Cambio de estatus:</h3>
                        </div>
                        <div class="col-md-6 col-md-offset-3">
                            <br>
                            <br>
                            <button class="btn btn-warning btn-block" @click="changeStatus('garantia')" v-show="order.status != 'en_proceso' && order.status != 'garantia'">Garantia</button>
                            <br>
                            <br>
                            <button class="btn btn-default btn-block" @click="changeStatus('entregado')" v-show="order.status =='terminado' || order.status == 'garantia'">Entregado</button>
                            <br>
                            <br>
                            <!-- <button class="btn btn-success btn-block" @click="changeStatus('pagado')" v-show="order.status=='terminado' || order.status=='entregado'">Pagado</button> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <sweet-modal ref="showRxModal" width="60%"  @close="handleModalClose">
            <form role="form" class="form-horizontal" v-if="indx_show != null">
                <div>
                    <div class="col-sm-6" style="text-align: left;">
                        <img src="https://sistema.augenlabs.com/public/images/logo.png" width="25%">
                        <div style="font-size: 25px;display: inline-block;padding-left: 10px;">|</div>
                    </div>
                    <div class="col-sm-3"></div>
                    <div class="col-sm-3" style="text-align: right;">
                        <h3><b>{{ orders[indx_show].branch.name }}</b></h3>
                        <h4><b>{{ orders[indx_show].branch.laboratory.name }}</b></h4>
                    </div>
                </div>
                <hr>
                <br>
                <p style="text-align: left;"><b>CAPTURA DE DATOS</b></p>
                <div class="form-group">
                    <div class="col-sm-3" style="text-align: left;">
                        <label style="font-weight:300">RX:</label>
                        <input v-model="orders[indx_show].rx_rx" class="form-control" id="rx" disabled>
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
                        <input v-model="orders[indx_show].rx_od_esfera" class="form-control" id="od_esfera">
                    </div>
                    <div class="col-sm-2" style="text-align: left;">
                        <label style="font-weight:300">OD CILINDRO</label>
                        <input v-model="orders[indx_show].rx_od_cilindro" class="form-control" id="od_cilindro">
                    </div>
                    <div class="col-sm-2" style="text-align: left;">
                        <label style="font-weight:300">OD EJE</label>
                        <input v-model="orders[indx_show].rx_od_eje" class="form-control" id="od_eje">
                    </div>
                    <div class="col-sm-2" style="text-align: left;">
                        <label style="font-weight:300">OD ADICION</label>
                        <input v-model="orders[indx_show].rx_od_adicion" class="form-control" id="od_adicion">
                    </div>
                    <div class="col-sm-2" style="text-align: left;">
                        <label style="font-weight:300">OD DIP</label>
                        <input v-model="orders[indx_show].rx_od_dip" class="form-control" id="od_dip">
                    </div>
                    <div class="col-sm-2" style="text-align: left;">
                        <label style="font-weight:300">OD ALTURA</label>
                        <input v-model="orders[indx_show].rx_od_altura" class="form-control" id="od_altura" >
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-2" style="text-align: left;">
                        <label style="font-weight:300">OI ESFERA</label>
                        <input v-model="orders[indx_show].rx_od_esfera_dos" class="form-control" id="od_esfera_dos">
                    </div>
                    <div class="col-sm-2" style="text-align: left;">
                        <label style="font-weight:300">OI CILINDRO</label>
                        <input v-model="orders[indx_show].rx_od_cilindro_dos" class="form-control" id="od_cilindro_dos">
                    </div>
                    <div class="col-sm-2" style="text-align: left;">
                        <label style="font-weight:300">OI EJE</label>
                        <input v-model="orders[indx_show].rx_od_eje_dos" class="form-control" id="od_eje_dos">
                    </div>
                    <div class="col-sm-2" style="text-align: left;">
                        <label style="font-weight:300">OI ADICION</label>
                        <input v-model="orders[indx_show].rx_od_adicion_dos" class="form-control" id="od_adicion_dos">
                    </div>
                    <div class="col-sm-2" style="text-align: left;">
                        <label style="font-weight:300">OI DIP</label>
                        <input v-model="orders[indx_show].rx_od_dip_dos" class="form-control" id="od_dip_dos">
                    </div>
                    <div class="col-sm-2" style="text-align: left;">
                        <label style="font-weight:300">OI ALTURA</label>
                        <input v-model="orders[indx_show].rx_od_altura_dos" class="form-control" id="od_altura_dos">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-6" style="text-align: left;">
                        <label style="font-weight:300">DISEÑO:</label>
                        <input v-model="orders[indx_show].rx_diseno" class="form-control" id="rx_diseno">
                    </div>
                    <div class="col-sm-6" style="text-align: left;">
                        <label style="font-weight:300">MATERIAL:</label>
                        <input v-model="orders[indx_show].rx_material" class="form-control" id="rx_diseno">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-6" style="text-align: left;">
                        <label style="font-weight:300">TIPO AR:</label>
                        <v-select v-model="orders[indx_show].rx_tipo_ar" :options="tipoarOpcs" label="label" index="value"/>
                    </div>
                    <div class="col-sm-6" style="text-align: left;">
                        <label style="font-weight:300">TALLADO:</label>
                        <v-select v-model="orders[indx_show].rx_tallado" :options="talladoOpcs" label="label" index="value"/>
                    </div>
                </div>
                <br>
                <p style="text-align: left;"><b>SERVICIOS</b></p>
                <div class="form-group">
                    <div class="col-sm-12" style="text-align: left;">
                        <input v-model="orders[indx_show].rx_servicios" class="form-control" id="rx_servicios">
                    </div>
                </div>
                <br>
                <p style="text-align: left;"><b>ARMAZÓN</b></p>
                <div class="form-group">
                    <div class="col-sm-4" style="text-align: left;">
                        <label style="font-weight:300;font-size: 13px;">TIPO DE ARMAZÓN:</label>
                        <v-select v-model="orders[indx_show].rx_tipo_armazon" :options="tipo_armazonOpcs" label="label" index="value"/>
                    </div>
                    <div class="col-sm-2" style="text-align: left;">
                        <label style="font-weight:300;font-size: 13px;">HORIZONTAL "A"</label>
                        <input v-model="orders[indx_show].rx_horizontal_a" class="form-control" id="rx_horizontal_a">
                    </div>
                    <div class="col-sm-2" style="text-align: left;">
                        <label style="font-weight:300;font-size: 13px;">VERTICAL "B"</label>
                        <input v-model="orders[indx_show].rx_vertical_b" class="form-control" id="vertical_b">
                    </div>
                    <div class="col-sm-2" style="text-align: left;">
                        <label style="font-weight:300;font-size: 13px;">DIAGONAL "ED"</label>
                        <input v-model="orders[indx_show].rx_diagonal_ed" class="form-control" id="diagonal_ed">
                    </div>
                    <div class="col-sm-2" style="text-align: left;">
                        <label style="font-weight:300;font-size: 13px;">PUENTE</label>
                        <input v-model="orders[indx_show].rx_puente" class="form-control" id="rx_puente">
                    </div>
                </div>
                <br>
                <p style="text-align: left;"><b>OBSERVACIONES</b></p>
                <div class="form-group">
                    <div class="col-sm-12" style="text-align: left;">
                        <textarea v-model="orders[indx_show].rx_observaciones" class="form-control" id="rx_rx_observaciones"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <!-- <button type="button" class="btn btn-default pull-right" @click="$refs.showRxModal.close()">Cerrar</button> -->
                    </div>
                </div>
            </form>
        </sweet-modal>
    </div>
</template>
<script>
export default {
    data(){
        return {
            client:"",
            rx:"",
            clients:[],
            orders:[],
            order:{},
            rxStatus: '',
            indx_show: null,
            idx: null,
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
    methods:{
        searchClient:function(){
            this.$parent.inPetition=true;
            axios.get(tools.url("/api/client/search/"+this.client)).then((response)=>{
                        let clients = response.data;
                        this.clients = clients;
                        this.$parent.inPetition=false;
                    })
                    .catch((err)=>{
                        this.$parent.inPetition=false;
                        this.$parent.handleErrors(err);
                    });
        },
        searchRX:function(){
            this.$parent.inPetition=true;
            this.orders = [];
            console.log('Orders clean');
            axios.get(tools.url("/api/order/search/"+this.rx)).then((response)=>{
                        let orders=response.data;
                        this.orders=orders;
                        alertify.rxDialog(document.getElementById("orders_modal"));
                        this.$parent.inPetition=false;
                    })
                    .catch((err)=>{
                        this.$parent.inPetition=false;
                        this.$parent.handleErrors(err);
                    });
        },
        selectClient:function(client_id){
            this.$parent.inPetition=true;
            axios.get(tools.url("/api/order/client/"+client_id)).then((response)=>{
                        let orders=response.data;
                        this.orders=orders;
                        alertify.rxDialog(document.getElementById("orders_modal"));
                        this.$parent.inPetition=false;
                    })
                    .catch((err)=>{
                        this.$parent.inPetition=false;
                        this.$parent.handleErrors(err);
                    });
        },
        selectOrder:function(order, idx){
            if(order.status == "en_proceso"){
                return false;
            }
            
            this.order=order;
            this.idx = idx;
            alertify.statusDialog(document.getElementById('status_table'));
        },
        setReasonAndChange(reason) {
            this.$parent.inPetition = true;
            let order = this.order;
            axios.post(tools.url("api/orders/statusWarranty/" + this.order.id), { reason, order }).then(result => {
                this.$parent.inPetition = false;
                this.$parent.showMessage(result.data.msg, "success");
                this.clients=[];
                alertify.closeAll();
            }).catch((err)=>{
                this.$parent.inPetition=false;
                this.$parent.handleErrors(err);
            });
        },
        changeStatus: function(status) {
            if(status == 'garantia') {
                this.rxStatus = '';
                let that = this;
                alertify.confirm().set('onshow', function() {
                    let $select = jQuery('.status-reason');
                    if($select.length) {
                        console.log($select);
                        $select.change(function() {
                            that.rxStatus = jQuery(this).val();
                        });
                    }
                });
                alertify.confirm(`
                        <h3>Confirma el motivo de la garantía</h3>
                        <select class="status-reason form-control">
                            <option selected disabled hidden>Selecciona una opción</option>
                            <option>ERROR DE POTENCIA (ESFERA/CILINDRO)</option>
                            <option>EJE INCORRECTO</option>
                            <option>ADICIÓN INCORRECTA</option>
                            <option>CENTROS OPTICOS</option>
                            <option>DISEÑO DE PROGRESIVO INCORRECTO</option>
                            <option>PRISMAS INCORRECTOS</option>
                            <option>BISEL (VENTANAS, MAL MONTADO)</option>
                            <option>RAYADO CLIENTE (APOYO)</option>
                            <option>SUCIO/RAYADO (LABORATORIO)</option>
                            <option>DIFERENTE MATERIAL</option>
                            <option>DESPRENDIEMIENTO DE CAPA</option>
                            <option>AR INCORRECTO</option>
                            <option>CAMBIO DE GRADUACIÓN (APOYO)</option>
                            <option>PROBLEMAS DE ADAPTACIÓN (APOYO)</option>
                            <option>ESTETICA (GROSOR)</option>
                            <option>ARMAZÓN (ROTO O DAÑADO)</option>
                            <option>ERROR DE CAPTURA</option>
                            <option>LÁSER INCORRECTO O NO COLOCADO</option>
                            <option>APOYO LIVERPOOL</option>
                        </select>
                        `, () => {
                            if(!this.order.have_data) {
                                // alert('Esta receta no tiene información adicional');

                                // pre-fill order
                                this.order.rx_rx = this.order.rx;
                                this.order.rx_cliente = this.order.client.name;
                                this.order.rx_fecha = this.order.created_at.split(' ')[0];
                            }

                            this.$refs.showRxModal.open();
                            this.indx_show = this.idx;
                        
                        }, () => {
                            this.rxStatus = '';
                        })
            } else {

                // if (this.order.status != 'garantia' && status == 'entregado' && this.order.client.status == 'Inactivo') {
                //     alert('No se puede cambiar el estatus, desbloque el cliente para poder continuar.');
                //     alertify.closeAll();
                //     return false;
                // }

                alertify.confirm('¿Seguro que deseas cambiar el estatus de este RX?', () => {
                    this.$parent.inPetition=true;
                    let params = {
                        status:status
                    };
                    axios.post(tools.url("api/orders/status/"+this.order.id),params)
                    .then((res)=>{
                        this.$parent.inPetition=false;
                        this.$parent.showMessage(res.data.msg,"success");
                        this.clients=[];
                        alertify.closeAll();
                    })
                    .catch((err)=>{
                        this.$parent.inPetition=false;
                        this.$parent.handleErrors(err);
                    });
                });
            }
        },
        handleModalClose() {
            this.setReasonAndChange(this.rxStatus);
            this.indx_show = null;
            this.rxStatus = '';
        }
    },
    mounted(){
        alertify.rxDialog || alertify.dialog('rxDialog',function(){
            return {
                main:function(content){
                    this.setContent(content);
                    this.resizeTo("80%","70%");
                },
                setup:function(){
                    return {
                        options:{
                            basic:true,
                            maximizable:false,
                            resizable:true,
                        }
                    };
                },
                settings:{
                    selector:undefined
                }
            };
        });
        alertify.statusDialog || alertify.dialog('statusDialog',function(){
            return {
                main:function(content){
                    this.setContent(content);
                },
                setup:function(){
                    return {
                        options:{
                            basic:true,
                            maximizable:false,
                            resizable:false,
                        }
                    };
                },
                settings:{
                    selector:undefined
                }
            };
        });
    }
}
</script>
