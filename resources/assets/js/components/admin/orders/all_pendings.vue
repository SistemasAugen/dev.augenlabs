<template>
    <div class="row">
        <div class="col-md-12">
            <h3 style="display:inline-block;">RX pendientes</h3>
            <!-- <button @click="getOrders" class="btn pull-right"><i class="fas fa-sync"></i></button> -->
        </div>
        <div class="row" style="padding: 0px 20px;">
            <div class="col-md-2">
                <p>Desde:</p>
                <input type="date" class="form-control" v-model="filters.start">
            </div>
            <div class="col-md-2">
                <p>Hasta:</p>
                <input type="date" class="form-control" v-model="filters.end">
            </div>
            <div class="col-md-8">
                <div class="row"><div class="col-md-12"><p>Estado:</p></div></div>
                <div class="row" style="display: flex;">
                    <div class="col" style="flex: 1;">
                        En proceso<vSwitch name="en_proceso" :checked="true" v-model="filters.status.en_proceso"></vSwitch>
                    </div>
                    <div class="col" style="flex: 1;">
                        Terminado<vSwitch name="terminado" :checked="true" v-model="filters.status.terminado"></vSwitch>
                    </div>
                    <div class="col" style="flex: 1;">
                        Entregado<vSwitch name="entregado" :checked="true" v-model="filters.status.entregado"></vSwitch>
                    </div>
                    <div class="col" style="flex: 1;">
                        Pagado<vSwitch name="pagado" :checked="true" v-model="filters.status.pagado"></vSwitch>
                    </div>
                    <div class="col" style="flex: 1;">
                        Garantía<vSwitch name="pagado" :checked="true" v-model="filters.status.garantia"></vSwitch>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <p>RX:</p>
                <input type="text" name="" id="" class="form-control" v-model="filters.search" @keydown.enter="getOrders">
            </div>
            <div class="col-md-2">
                <button class="btn btn-block btn-success" @click="getOrders" style="margin-top: 30px;">Filtrar</button>
            </div>
            
            <div class="col-md-3">
                <button style="width:100px;display: none;" class="btn btn-block btn-info" @click="exportData" >Exportar</button>
            </div>
        </div>
        <div class="col-md-12">
            <table class="table responsive" id="rx-table" v-if="!isLoading"  >
                <thead>
                    <tr>
                        <th>RX</th>
                        <th>Cliente</th>
                        <th>Nombre Comercial</th>
                        <th>Laboratorio</th>
                        <th>Fecha</th>
                        <th>Lista de precios</th>
                        <th>Diseño</th>
                        <th>Material</th>
                        <th>Caracteristica</th>
                        <th>Antireflejante</th>
                        <th>Costo</th>
                        <th>Servicio</th>
                        <th>Subtotal</th>
                        <th>Descuento sistema</th>
                        <th>Descuento</th>
                        <th>Descuento promoción</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Acciones</th>
                        <!-- <th>Semanas Restantes</th> -->
                    </tr>
                </thead>
                <tbody v-if="orders.length > 0">
                    <tr v-for="(cart,i) in orders" :key="i" :data-id="cart.id">
                        <td>{{ cart.rx }}</td>
                        <td>{{ cart.client.name }}</td>
                        <td>{{ cart.client.comertial_name }}</td>
                        <td>{{ cart.branch.name }}</td>
                        <td>{{ cart.created_at }}</td>
                        <td>{{ cart.price_list }}</td>
                        <td>{{ cart.product.name }}</td>
                        <td>{{ cart.product.subcategory_name }}</td>
                        <td>{{ cart.product.type_name }}</td>
                        <td v-if="cart.extras[0]">
                            {{ cart.extras.map(row =>{return row.name}).join(", ") }}
                        </td>
                        <td v-else>
                            -
                        </td>
                        <td>${{ cart.price }}</td>
                        <td>${{ cart.service }}</td>
                        <td>{{ getTotal(cart.price, 0, 0,cart.service) | currency }}</td>
                        <td>${{ cart.discount }}</td>
                        <td><button class="btn cmd-changeDiscount" @click="selectDiscount(cart.id)">${{ cart.discount_admin }}</button></td>
                        <td>
                            <span v-if="cart.promo_discount != null">$ {{ cart.promo_discount }}</span>
                            <span v-else>N/A</span>
                        </td>
                        <td v-if="cart.status=='en_proceso'">
                            <button class="btn btn-success cmd-changeStatus" @click="selectOrder(cart)">{{ cart.status.replace("_"," ") }}</button>
                        </td>
                        <td v-else-if="cart.status == 'cancelado'">
                            <button class="btn btn-danger">{{ cart.status.replace("_"," ") }}</button>
                        </td>
                        <td v-else>
                            <button class="btn btn-warning cmd-changeStatus" @click="selectOrder(cart)">{{ cart.status.replace("_"," ") }}</button>
                        </td>
                        <td v-if="!cart.cancelled">{{ getTotal(cart.total,cart.discount,cart.discount_admin,cart.iva, cart.promo_discount) | currency }}</td>
                        <td v-else>{{ 0 | currency }} </td>
                        <td>
                            <div v-if="!cart.cancelled && !cart.payed" style="width: 120px;">
                                <!-- <button class="btn btn-primary" @click.prevent="applyCourtesy(cart.rx, cart.id, cart)"><i class="fas fa-gift"></i></button> -->
                                <button class="btn btn-danger cmd-cancelOrder" @click.prevent="applyCancelled(cart.rx, cart.id, cart)" v-if="!cart.cancelled"><i class="fas fa-times"></i></button>
                                <button class="btn btn-info cmd-payOrder" @click.prevent="applyPayment(cart.rx, cart.id, cart)" v-if="!cart.payed"><i class="fas fa-money-bill-alt"></i></button>
                                <button class="btn btn-info cmd-showOrder" @click.prevent="openOrder(cart)" v-if="cart.rx_rx != ''"><i class="fas fa-eye"></i></button>
                            </div>
                            <div v-else style="width: 120px; text-align: center;">
                                -
                            </div>
                        </td>
                        <!-- <td>{{ '0' }}</td> -->
                    </tr>
                </tbody>
            </table>

            <!-- <b-pagination-nav :link-gen="linkGen" :number-of-pages="pagination.total" use-router></b-pagination-nav> -->

        </div>
        <div style="display:none;">
            <div id="discount_table">
                <div class="row">
                    <div class="col-md-12">
                        <h3>Ingresa el nuevo descuento:</h3>
                    </div>
                    <form action="" @submit.prevent="setDiscount">
                        <div class="col-md-9">
                            $<input type="number" step=".01" class="" min="0" v-model="discount" >
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-success">Aplicar</button>
                        </div>
                    </form>
                </div>
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
                        <button class="btn btn-success btn-block" @click="changeStatus('terminado')" v-show="order.status == 'en_proceso'">Terminado</button>
                        <br>
                        <br>
                        <button class="btn btn-default btn-block" @click="changeStatus('entregado')" v-show="order.status =='terminado' || order.status == 'garantia'">Entregado</button>
                        <!-- <button class="btn btn-success btn-block" @click="changeStatus('pagado')" v-show="order.status=='terminado' || order.status=='entregado'">Pagado</button> -->
                    </div>
                </div>
            </div>
        </div>
        <sweet-modal ref="showRxModal" width="60%">
            <form role="form" class="form-horizontal" v-if="selectedOrder != null">
                <div>	
                    <div class="col-sm-6" style="text-align: left;">
                        <img src="https://sistema.augenlabs.com/public/images/logo.png" width="25%">
                        <div style="font-size: 25px;display: inline-block;padding-left: 10px;">|</div>
                    </div>
                    <div class="col-sm-3"></div>
                    <div class="col-sm-3" style="text-align: right;">
                        <h3><b>{{ selectedOrder.branch.name }}</b></h3>
                        <h4><b>{{ selectedOrder.branch.laboratory.name }}</b></h4>
                    </div>

                </div>
                <hr><br>

                <p style="text-align: left;"><b>CAPTURA DE DATOS</b></p>
                <div class="form-group">
                        
                    <div class="col-sm-3" style="text-align: left;">
                        <label style="font-weight:300">RX:</label>
                        <input v-model="selectedOrder.rx_rx" disabled class="form-control" id="rx">
                    </div>

                        
                    <div class="col-sm-3" style="text-align: left;">
                        <label style="font-weight:300">FECHA:</label>
                        <input v-model="selectedOrder.rx_fecha" class="form-control" id="fecha" type="date" disabled>
                    </div>

                        
                    <div class="col-sm-6" style="text-align: left;">
                        <label style="font-weight:300">CLIENTE:</label>
                        <input v-model="selectedOrder.rx_cliente" disabled class="form-control" id="fecha">
                </div>
                </div>


                <br>
                <p style="text-align: left;"><b>GRADUACION</b></p>
                
                <div class="form-group">
                                <div class="col-sm-2" style="text-align: left;">
                    <label style="font-weight:300">OD ESFERA</label>
                    <input v-model="selectedOrder.rx_od_esfera" class="form-control" id="od_esfera" disabled>
                                </div>
                    <div class="col-sm-2" style="text-align: left;">
                    <label style="font-weight:300">OD CILINDRO</label>
                    <input v-model="selectedOrder.rx_od_cilindro" class="form-control" id="od_cilindro" disabled>
                                </div>
                    <div class="col-sm-2" style="text-align: left;">
                    <label style="font-weight:300">OD EJE</label>
                    <input v-model="selectedOrder.rx_od_eje" class="form-control" id="od_eje" disabled>
                                </div>
                    <div class="col-sm-2" style="text-align: left;">
                    <label style="font-weight:300">OD ADICION</label>
                    <input v-model="selectedOrder.rx_od_adicion" class="form-control" id="od_adicion" disabled>
                                </div>
                    <div class="col-sm-2" style="text-align: left;">
                    <label style="font-weight:300">OD DIP</label>
                    <input v-model="selectedOrder.rx_od_dip" class="form-control" id="od_dip" disabled>
                                </div>
                    <div class="col-sm-2" style="text-align: left;">
                    <label style="font-weight:300">OD ALTURA</label>
                    <input v-model="selectedOrder.rx_od_altura" class="form-control" id="od_altura"  disabled>
                                </div>
                </div>

                <div class="form-group">
                                <div class="col-sm-2" style="text-align: left;">
                    <label style="font-weight:300">OI ESFERA</label>
                    <input v-model="selectedOrder.rx_od_esfera_dos" class="form-control" id="od_esfera_dos" disabled>
                                </div>
                    <div class="col-sm-2" style="text-align: left;">
                    <label style="font-weight:300">OI CILINDRO</label>
                    <input v-model="selectedOrder.rx_od_cilindro_dos" class="form-control" id="od_cilindro_dos" disabled>
                                </div>
                    <div class="col-sm-2" style="text-align: left;">
                    <label style="font-weight:300">OI EJE</label>
                    <input v-model="selectedOrder.rx_od_eje_dos" class="form-control" id="od_eje_dos" disabled>
                                </div>
                    <div class="col-sm-2" style="text-align: left;">
                    <label style="font-weight:300">OI ADICION</label>
                    <input v-model="selectedOrder.rx_od_adicion_dos" class="form-control" id="od_adicion_dos" disabled>
                                </div>
                    <div class="col-sm-2" style="text-align: left;">
                    <label style="font-weight:300">OI DIP</label>
                    <input v-model="selectedOrder.rx_od_dip_dos" class="form-control" id="od_dip_dos" disabled>
                                </div>
                    <div class="col-sm-2" style="text-align: left;">
                    <label style="font-weight:300">OI ALTURA</label>
                    <input v-model="selectedOrder.rx_od_altura_dos" class="form-control" id="od_altura_dos" disabled>
                                </div>
                </div>


                <div class="form-group">
                                <div class="col-sm-6" style="text-align: left;">
                    <label style="font-weight:300">DISEÑO:</label>
                    <input v-model="selectedOrder.rx_diseno" disabled class="form-control" id="rx_diseno">
                                </div>
                    <div class="col-sm-6" style="text-align: left;">
                    <label style="font-weight:300">MATERIAL:</label>
                    <input v-model="selectedOrder.rx_material" disabled class="form-control" id="rx_diseno">
                                </div>
                </div>

                <div class="form-group">
                                <div class="col-sm-6" style="text-align: left;">
                    <label style="font-weight:300">TIPO AR:</label>
                    <v-select v-model="selectedOrder.rx_tipo_ar" :options="tipoarOpcs" label="label" index="value" disabled/>
                                </div>
                    <div class="col-sm-6" style="text-align: left;">
                    <label style="font-weight:300">TALLADO:</label>
                    <v-select v-model="selectedOrder.rx_tallado" :options="talladoOpcs" label="label" index="value" disabled/>
                                </div>
                </div>
                <br>
                <p style="text-align: left;"><b>SERVICIOS</b></p>
                <div class="form-group">

                    <div class="col-sm-12" style="text-align: left;">
                    
                    <input v-model="selectedOrder.rx_servicios" class="form-control" id="rx_servicios" disabled>
                                </div>
                </div>

                <br>
                <p style="text-align: left;"><b>ARMAZÓN</b></p>
                <div class="form-group">

                <div class="col-sm-4" style="text-align: left;">
                    <label style="font-weight:300;font-size: 13px;">TIPO DE ARMAZÓN:</label>
                    <v-select v-model="selectedOrder.rx_tipo_armazon" :options="tipo_armazonOpcs" label="label" index="value" disabled/>
                </div>
                <div class="col-sm-2" style="text-align: left;">
                    <label style="font-weight:300;font-size: 13px;">HORIZONTAL "A"</label>
                    <input v-model="selectedOrder.rx_horizontal_a" class="form-control" id="rx_horizontal_a" disabled>
                </div>
                <div class="col-sm-2" style="text-align: left;">
                    <label style="font-weight:300;font-size: 13px;">VERTICAL "B"</label>
                    <input v-model="selectedOrder.rx_vertical_b" class="form-control" id="vertical_b" disabled>
                </div>
                <div class="col-sm-2" style="text-align: left;">
                    <label style="font-weight:300;font-size: 13px;">DIAGONAL "ED"</label>
                    <input v-model="selectedOrder.rx_diagonal_ed" class="form-control" id="diagonal_ed" disabled>
                </div>
                <div class="col-sm-2" style="text-align: left;">
                    <label style="font-weight:300;font-size: 13px;">PUENTE</label>
                    <input v-model="selectedOrder.rx_puente" class="form-control" id="rx_puente" disabled>
                </div>
                </div>


                <br>
                <p style="text-align: left;"><b>OBSERVACIONES</b></p>
                <div class="form-group">

                <div class="col-sm-12" style="text-align: left;">
                    <textarea v-model="selectedOrder.rx_observaciones" class="form-control" id="rx_rx_observaciones" disabled></textarea>
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
<script>
export default {
    data(){
        return {
            filters:{
                start: "",
                end: "",
                status:{
                    en_proceso:true,
                    terminado:true,
                    entregado:true,
                    pagado:true,
                    garantia: true
                },
                search: '',
            },
            orders:[],
            order: {},
            discount:0,
            order_id:"",
            isLoading: true,
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
                {value:'HD',label:'HD'},
            ],
            tipo_armazonOpcs:[
                {value:'Metálico',label:'Metálico'},
                {value:'Perforado',label:'Perforado'},
                {value:'Plástico',label:'Plástico'},
                {value:'Ranurado',label:'Ranurado'},
            ],
            selectedOrder: null
            /*pagination:{
                currentpage:1,
                total:1,
                number: 10,
                total_products:1,
            },*/
        }
    },
    /*watch:{
        '$route.query.page':function(val){
        if (val) {
            this.pagination.currentpage = val;

        }
        else{
            this.pagination.currentpage = 1;
        }
        this.getOrders();
      },
    },*/
    methods:{
        openOrder: function(order) {
            this.selectedOrder = order;
            this.$refs.showRxModal.open();
        },
        applyPayment(rx, id, order) {
            if(!confirm(`¿Estás seguro que deseas marcar la RX: ${ rx } como pagada?`))
                return;

            this.$parent.inPetition=true;
            axios.post('/api/orders/' + id + '/payed').then(result => {
                order.payed = 1;
                this.$parent.inPetition = false;
                this.$parent.showMessage(result.data.msg,"success");
            }).catch((err)=>{
                this.$parent.inPetition = false;
                this.$parent.handleErrors(err);
            });
        },
        applyCancelled(rx, id, order) {
            if(!confirm(`¿Estás seguro que deseas cancelar la RX: ${ rx }?`))
                return;

            this.$parent.inPetition=true;
            axios.post('/api/orders/' + id + '/cancel').then(result => {
                order.cancelled = 1;
                order.status = 'cancelado';
                this.$parent.inPetition = false;
                this.$parent.showMessage(result.data.msg,"success");
            }).catch((err)=>{
                this.$parent.inPetition = false;
                this.$parent.handleErrors(err);
            });
        },
        getTotal:function(subtotal,dis,dis1,iva, promo = null){
            //console.log(promo);
            if(promo != null)
                return promo;
            iva = parseFloat(iva);
            return Math.abs(parseFloat((parseFloat(subtotal) - this.getDiscount(dis,dis1)) + iva).toFixed(2));
        },
        getDiscount:function(dis,dis1){
            return parseFloat(dis || 0) + parseFloat(dis1 || 0);
        },
        getOrders:function(){
            this.orders = [];
            this.$parent.inPetition = true;

            jQuery('#rx-table').bootstrapTable('removeAll');
            jQuery('#rx-table').bootstrapTable('destroy');
            this.isLoading = true;

            //axios.post(tools.url("api/orders?page=" + this.pagination.currentpage), this.filters)
            axios.post(tools.url("api/orders"), this.filters)
            .then((res)=>{
                this.$parent.inPetition=false;
                this.orders=res.data;
                // console.log(this.orders.length);
                this.isLoading = false;

                /*this.orders=res.data.data;
                this.pagination.total = res.data.last_page;
                this.pagination.total_products = res.data.total;*/

                setTimeout(()=>{
                    this.mountTable();
                }, 1000);
            })
            .catch((err)=>{
                this.$parent.inPetition=false;
                this.$parent.handleErrors(err);
            });
        },
        selectDiscount:function(id){
            this.order_id=id;
            alertify.discountDialog(document.getElementById("discount_table"));
        },
        setDiscount:function(){
            this.$parent.inPetition=true;
            let params={
                discount:this.discount
            }
            axios.post(tools.url('api/orders/discount/'+this.order_id),params)
            .then((res)=>{
                this.$parent.inPetition=false;
                alertify.closeAll();
                this.$parent.showMessage(res.data.msg,"success");
                this.getOrders();
            })
            .catch((err)=>{
                this.$parent.inPetition=false;
                this.$parent.handleErrors(err);
            });
        },
        selectOrder:function(order){
            
            this.order=order;
            alertify.statusDialog(document.getElementById('status_table'));
        },
        changeStatus:function(status){
            // if(status == "entregado") {
            //     if (this.order.client.status == 'Inactivo') {
            //         alert('Esta acción no está permitida para clientes bloqueados');
            //         return;
            //     }
            // }
            alertify.confirm('¿Seguro que deseas cambiar el estatus de este RX?', ()=>{
                this.$parent.inPetition=true;
                let params={
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
        },
        mountTable(){
            jQuery('#rx-table').bootstrapTable('removeAll');
            jQuery('#rx-table').bootstrapTable('destroy');

            jQuery('#rx-table').bootstrapTable({
                //Boton de refrescar x
                exportTypes: ['excel'],
                exportDataType: 'all',
                showRefresh:true,
                showExport: true,
                showToggle: false,
                pagination: true,
                search: true,
                exportOptions: {
                    fileName: 'rx-pendientes' + this.filters.start + '_' + this.filters.end
                }
            });

            jQuery('#rx-table').on('refresh.bs.table',()=>{
                this.getOrders();
            });

            jQuery('#rx-table').on('page-change.bs.table', () => {
                jQuery('.cmd-changeDiscount').click(function() {
                    let $tr = $(this).parents('tr');
                    let id  = $tr.data('id');

                    let cart = that.orders.filter(o => o.id == id).shift();

                    that.selectDiscount(cart.id);
                });

                jQuery('.cmd-changeStatus').click(function() {
                    let $tr = $(this).parents('tr');
                    let id  = $tr.data('id');

                    let cart = that.orders.filter(o => o.id == id).shift();

                    that.selectOrder(cart);
                });

                jQuery('.cmd-cancelOrder').click(function() {
                    let $tr = $(this).parents('tr');
                    let id  = $tr.data('id');

                    let cart = that.orders.filter(o => o.id == id).shift();

                    that.applyCancelled(cart.rx, cart.id, cart);
                });

                jQuery('.cmd-payOrder').click(function() {
                    let $tr = $(this).parents('tr');
                    let id  = $tr.data('id');

                    let cart = that.orders.filter(o => o.id == id).shift();

                    that.applyPayment(cart.rx, cart.id, cart);
                });
                
                jQuery('.cmd-showOrder').click(function() {
                    let $tr = $(this).parents('tr');
                    let id  = $tr.data('id');

                    let cart = that.orders.filter(o => o.id == id).shift();

                    that.openOrder(cart);
                });
            });

            let that = this;

            jQuery('.cmd-changeDiscount').click(function() {
                let $tr = $(this).parents('tr');
                let id  = $tr.data('id');

                let cart = that.orders.filter(o => o.id == id).shift();

                that.selectDiscount(cart.id);
            });

            jQuery('.cmd-changeStatus').click(function() {
                let $tr = $(this).parents('tr');
                let id  = $tr.data('id');

                let cart = that.orders.filter(o => o.id == id).shift();

                that.selectOrder(cart);
            });

            jQuery('.cmd-cancelOrder').click(function() {
                let $tr = $(this).parents('tr');
                let id  = $tr.data('id');

                let cart = that.orders.filter(o => o.id == id).shift();

                that.applyCancelled(cart.rx, cart.id, cart);
            });

            jQuery('.cmd-payOrder').click(function() {
                let $tr = $(this).parents('tr');
                let id  = $tr.data('id');

                let cart = that.orders.filter(o => o.id == id).shift();

                that.applyPayment(cart.rx, cart.id, cart);
            });

            jQuery('.cmd-showOrder').click(function() {
                let $tr = $(this).parents('tr');
                let id  = $tr.data('id');

                let cart = that.orders.filter(o => o.id == id).shift();

                that.openOrder(cart);
            });
            // this.getClients();
        },
        linkGen(pageNum) {
            return pageNum === 1 ? '?' : `?page=${pageNum}`
        },
        exportData(){
            this.$parent.inPetition=true;
            axios.post(tools.url("/api/ordersExport"),{data:this.filters}).then((response)=>{
                this.$parent.inPetition=false;
                    window.open('https://sistema.augenlabs.com/storage/app/public/pedidos.xlsx','_blank')
            }).catch((error)=>{
                this.$parent.handleErrors(error);
                this.$parent.inPetition=false;
            });
        }
    },
    mounted() {
        let date = new Date();
            date.setTime(new Date().getTime() - (6 * 60 * 60 * 1000));
        this.filters.start = this.filters.end = date.toISOString().slice(0,10);

        this.getOrders();
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
        alertify.discountDialog || alertify.dialog('discountDialog',function(){
            return {
                main:function(content){
                    this.setContent(content);
                },
                setup:function(){
                    return {
                        options:{
                            basic:true,
                            maximizable:true,
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
<style lang="scss">
.col {
    label.switch {
        display: block;
    }
}
</style>
