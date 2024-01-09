<template>
    <div class="row">
        <div class="col-md-12">
            <h3 style="display:inline-block;">RX pendientes.</h3>
            <!-- <button @click="getOrders" class="btn pull-right"><i class="fas fa-sync"></i></button> -->
        </div>
        <div class="col-md-12">
            <table
                class="table responsive"
                id="pendings-table"
                data-toggle="table"
                :data-height="heightTable"
                data-search="true"
                data-side-pagination="server"
                data-pagination="false"
                data-locale="es-ES">
                <thead>
                    <tr>
                        <th data-field="rx">RX</th>
                        <th data-field="client.name">Cliente</th>
                        <th data-field="branch.name">Origen</th>
                        <th data-field="created_at">Fecha</th>
                        <th data-field="product.name">Diseño</th>
                        <th data-field="product.subcategory_name">Material</th>
                        <th data-field="product.type_name">Caracteristica</th>
                        <th data-field="computedExtras">Antireflejante</th>
                        <th data-field="price">Costo</th>
                        <th data-field="service">Servicio</th>
                        <!-- <th data-field="">Descuento promoción</th> -->
                        <th data-field="realTotal">Total</th>
                        <th data-field="statusButton">Status</th>
                        <th data-field="moveButton">Mover</th>
                        <th data-field="showButton">Ver</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div style="display:none;">
            <div id="laboratories_table">
                <div class="row">
                    <div class="col-md-12">
                        <h3>Selecciona un laboratorio:</h3>
                    </div>
                    <div class="col-md-6 col-md-offset-3" v-for="laboratory in laboratories" :key="laboratory.id">
                        <br>
                        <br>
                        <button class="btn btn-default btn-block" @click="moveOrder(laboratory.id)">{{ laboratory.name }}</button>
                    </div>
                </div>
            </div>
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
                <hr><br>

                <p style="text-align: left;"><b>CAPTURA DE DATOS</b></p>
                <div class="form-group">
                        
                    <div class="col-sm-3" style="text-align: left;">
                        <label style="font-weight:300">RX:</label>
                        <input v-model="order.rx_rx" disabled class="form-control" id="rx">
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
                    <input v-model="order.rx_od_altura" class="form-control" id="od_altura"  disabled>
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
                    <input v-model="order.rx_diseno" disabled class="form-control" id="rx_diseno">
                                </div>
                    <div class="col-sm-6" style="text-align: left;">
                    <label style="font-weight:300">MATERIAL:</label>
                    <input v-model="order.rx_material" disabled class="form-control" id="rx_diseno">
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
            orders:[],
            laboratories:[],
            order_id:"",
            branches:[],
            heightTable: 900,
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
            order: null
        }
    },
    methods:{
        getTotal:function(subtotal,dis,dis1,iva, promo = null){
            console.log(promo);
            if(promo != null)
                return promo;
            iva = parseFloat(iva);
            return Math.abs(parseFloat((parseFloat(subtotal) - this.getDiscount(dis,dis1)) + iva).toFixed(2));
        },
        getDiscount:function(dis,dis1){
            return parseFloat(dis || 0) + parseFloat(dis1 || 0);
        },
        getOrders:function(){
            // this.$parent.inPetition=true;
            // axios.get(tools.url('api/orders/pending'))
            // .then((res)=>{
            //     this.$parent.inPetition=false;
            //     this.orders=res.data;
            // })
            // .catch((err)=>{
            //     this.$parent.inPetition=false;
            //     this.$parent.handleErrors(err);
            // });
            jQuery('#pendings-table').bootstrapTable('refresh')
        },
        getLaboratories:function(){
            axios.get(tools.url("api/laboratories"))
            .then((response)=>{
                this.laboratories=response.data;
            })
            .catch((err)=>{
                this.$parent.handleErrors(err);
            });
        },
        finishOrder:function(id){
            const order = this.orders.filter(o => o.id == id).shift();
            
            if (order.status == 'en_proceso' && order.client.status == 'Inactivo') {
                alert('Estás dando terminado a una rx de un cliente esta bloqueado.');
            }
            alertify.confirm('¿Deseas marcar como terminado este RX?', ()=>{
                this.$parent.inPetition=true;
                let params={
                    status:"terminado"
                }
                axios.post(tools.url("api/orders/status/"+id),params)
                .then((res)=>{
                    this.$parent.inPetition=false;
                    this.$parent.showMessage(res.data.msg,"success");
                    this.getOrders();
                })
                .catch((err)=>{
                    this.$parent.inPetition=false;
                    this.$parent.handleErrors(err);
                });
            });
        },
        selectLaboratory:function(id){
            this.order_id=id;
            alertify.laboratoriesDialog(document.getElementById("laboratories_table"));
        },
        selectOrder: function(id) {
            console.log(id, this.orders);
            const order = this.orders.filter(o => o.id == id).shift();
            this.order = order;
            this.$refs.showRxModal.open();
        },
        moveOrder:function(id){
            alertify.confirm('¿Seguro que deseas mover este RX?', ()=>{
                this.$parent.inPetition=true;
                let params={
                    laboratory_id:id
                }
                axios.post(tools.url("api/orders/laboratory/"+this.order_id),params)
                .then((res)=>{
                    this.$parent.inPetition=false;
                    this.$parent.showMessage(res.data.msg,"warning");
                    this.order_id="";
                    alertify.closeAll();
                    this.getOrders();
                })
                .catch((err)=>{
                    this.$parent.inPetition=false;
                    this.$parent.handleErrors(err);
                });
            });
        },
        getBranches(){
            this.$parent.inPetition=true;
            axios.get(tools.url("/api/branches")).then((response)=>{
                this.branches=response.data;
                this.branches.forEach((v,k)=>{
                    this.branches[k].laboratory=v.laboratory.name;
                    this.branches[k].state=v.state.name;
                    this.branches[k].town=v.town.name;
                });

                this.$parent.inPetition=false;
            }).catch((error)=>{
                this.$parent.handleErrors(error);
                this.$parent.inPetition=false;
            });
        },
        filterBranch:function(branch_id){
            let branch = this.branches.find(row=>{
                return row.id==branch_id;
            });

            return branch;
        },
        ajaxRequest(params) {
            axios.get('/api/orders/pending' + '?' + jQuery.param(params.data) + '&ajax=true').then(result => {
                // this.heightTable = 460;
                this.orders = result.data.rows;
                params.success(result.data);
                let that = this;
                jQuery('.btn-change').click(function() {
                    let id = jQuery(this).data('id');
                    that.finishOrder(id);
                });

                jQuery('.btn-move').click(function() {
                    let id = jQuery(this).data('id');
                    that.selectLaboratory(id);
                });
                
                $('.btn-show').click(function() {
                    let id = jQuery(this).data('id');
                    that.selectOrder(id);
                });
            });
        },
        mountTable() {
            jQuery('#pendings-table').bootstrapTable({
                ajax: this.ajaxRequest
            });
        }
    },
    mounted(){
        // this.getBranches();
        this.getLaboratories();
        this.mountTable();
        // setTimeout(()=>{
        //     this.getOrders();
        // },1000);
        alertify.laboratoriesDialog || alertify.dialog('laboratoriesDialog',function(){
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
