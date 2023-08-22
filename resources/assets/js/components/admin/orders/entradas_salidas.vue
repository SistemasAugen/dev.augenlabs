<template>
    <div class="row">
        <div class="col-md-12">
            <h3 style="display:inline-block;">RX pendientes</h3>
            <!-- <button @click="getOrders" class="btn pull-right"><i class="fas fa-sync"></i></button> -->
        </div>
        <div class="row" style="padding: 0px 20px;">
            <div class="col-md-3">
                <p>Desde:</p>
                <input type="date" class="form-control" v-model="filters.start">
            </div>
            <div class="col-md-3">
                <p>Hasta:</p>
                <input type="date" class="form-control" v-model="filters.end">
            </div>
            <div class="col-md-2">
                <button class="btn btn-block btn-success" @click="getOrders" style="margin-top: 30px;">Filtrar</button>
            </div>
        </div>
        <div class="col-md-12">
            <table class="table responsive" id="rx-table" v-if="!isLoading"  >
                <thead>
                    <tr>
                        <th>RX</th>
                        <th>Status</th>
                        <th>Cliente</th>
                        <th>Nombre Comercial</th>
                        <th>Laboratorio</th>
                        <th>Fecha</th>
                        <th>Diseño</th>
                        <th>Material</th>
                        <th>Caracteristica</th>
                        <th>Antireflejante</th>
                        <th>Costo</th>
                        <th>Servicio</th>
                        <th>Subtotal</th>
                        <th>Descuento sistema</th>
                        
                        <th>Descuento promoción</th>
                        
                        <th>Total</th>
                              
                    </tr>
                </thead>
                <tbody v-if="orders.length > 0">
                    <tr v-for="(cart,i) in orders" :key="i" :data-id="cart.id">
                        <td>{{ cart.rx }}</td>

                        <td v-if="cart.status == 'pagado'">
                            <button class="btn btn-success">Pagado</button>
                        </td>
                   
                        <td v-else>
                            <button class="btn btn-danger">Pediente de pago</button>
                        </td>

                        <td>{{ cart.client_name }}</td>
                        <td>{{ cart.comertial_name }}</td>
                        <td>{{ cart.branch_name }}</td>
                        <td>{{ cart.created_at }}</td>
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
                    
                        <td>
                            <span v-if="cart.promo_discount != null">$ {{ cart.promo_discount }}</span>
                            <span v-else>N/A</span>
                        </td>
                        
                        <td v-if="!cart.cancelled">{{ getTotal(cart.total,cart.discount,cart.discount_admin,cart.iva, cart.promo_discount) | currency }}</td>
                        <td v-else>{{ 0 | currency }} </td>
                      
                    </tr>
                </tbody>
            </table>

            <!-- <b-pagination-nav :link-gen="linkGen" :number-of-pages="pagination.total" use-router></b-pagination-nav> -->

        </div>

    </div>
</template>
<script>
export default {
    data(){
        return {
            filters:{
                start: "",
                end: "",
            },
            orders:[],
            order: {},
            isLoading: true,
        }
    },

    methods:{
        getOrders:function(){
            this.orders = [];
            this.$parent.inPetition = true;

            jQuery('#rx-table').bootstrapTable('removeAll');
            jQuery('#rx-table').bootstrapTable('destroy');
            this.isLoading = true;

            //axios.post(tools.url("api/orders?page=" + this.pagination.currentpage), this.filters)
            axios.post(tools.url("api/ordersEntradasSalidas"), this.filters)
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
    },
    mounted() {
        let date = new Date();
            date.setTime(new Date().getTime() - (6 * 60 * 60 * 1000));
        this.filters.start = this.filters.end = date.toISOString().slice(0,10);

        this.getOrders();
        
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
