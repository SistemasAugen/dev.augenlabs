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
