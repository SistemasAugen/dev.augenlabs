<template>
    <div id="customer-query">
        <div class="form-horizontal">
            <div class="form-group">
                <label class="control-label col-sm-4">Nombre del cliente:</label>
                <div class="col-sm-4" style="padding-right: 0px; ">
                    <autocomplete
                        :url="'/api/clients/search/' + fieldToSearch"
                        :anchor="fieldToSearch"
                        label="branch.name"
                        :classes="{ input: 'form-control' }"
                        :customHeaders="{ Authorization: $parent.token }"
                        :on-select="getData">
                    </autocomplete>
                </div>
                <div class="col-sm-4" style="padding-left: 0px;">
                    <select class="form-control custom-select" v-model="fieldToSearch">
                        <option value="name">Nombre</option>
                        <option value="comertial_name">Nombre Comercial</option>
                        <option value="contact_name">Nombre de Contacto</option>
                        <option value="business_name">Razón Social</option>
                    </select>
                </div>
            </div>
        </div>
        <hr/>
        <div class="container" v-if="selectedClient != null">
            <div class="row">
                <table class="table table-client">
                    <tbody>
                        <tr>
                            <td>{{ selectedClient.name }}</td>
                            <td>{{ selectedClient.phone }}</td>
                            <td>MON: {{ selectedClient.monofocalDiscount + ' %' }}</td>
                            <td>PAQ: {{ selectedClient.packagesDiscount + ' %' }}</td>
                        </tr>
                        <tr>
                            <td>{{ selectedClient.comertial_name }}</td>
                            <td>{{ selectedClient.email }}</td>
                            <td>BI: {{ selectedClient.bifocalDiscount + ' %' }}</td>
                            <td>TER: {{ selectedClient.finishedDiscount + ' %' }}</td>
                        </tr>
                        <tr>
                            <td>{{ selectedClient.branch.name }}</td>
                            <td>PLAZO: {{ selectedClient.payment_plan }} SEMANAS</td>
                            <td>PRO: {{ selectedClient.progresiveDiscount + ' %' }}</td>
                            <td>
                                <a :href="'/generate/account/' + selectedClient.id + '/client'" class="btn btn-success">ADEDUO <i class="fas fa-file-excel"></i></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table id="orders"></table>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: "",
    data: () => ({
        selectedClient: null,
        orders: [],
        fieldToSearch: 'name',
        isDataLoaded: false,
    }),
    methods: {
        getData(obj) {
            this.selectedClient = null;
            this.orders = [];
            this.$parent.inPetition = true;

            this.selectedClient = obj;
            this.selectedClient.monofocalDiscount       = this.selectedClient.discounts.filter(d => d.type_id == 1).shift().discount;
            this.selectedClient.bifocalDiscount         = this.selectedClient.discounts.filter(d => d.type_id == 2).shift().discount;
            this.selectedClient.progresiveDiscount      = this.selectedClient.discounts.filter(d => d.type_id == 3).shift().discount;
            this.selectedClient.packagesDiscount        = this.selectedClient.discounts.filter(d => d.type_id == 5).shift().discount;
            this.selectedClient.finishedDiscount        = this.selectedClient.discounts.filter(d => d.type_id == 4).shift().discount;

            setTimeout(this.mountTable, 1000);
        },
        mountTable() {
            jQuery('#orders').bootstrapTable({
                columns: [
                    {
                        field: 'rx',
                        title: 'RX',
                        sortable:true,
                        switchable:true,

                    },{
                        field: 'created_at',
                        title: 'Fecha de captura',
                        sortable: true,
                        switchable: true,

                    },{
                        field: 'delivered_date',
                        title: 'Fecha de entrega',
                        sortable: true,
                        switchable: true,

                    },{
                        field: 'passed',
                        title: 'Semanas restantes',
                        sortable: true,
                        switchable: true,
                    },{
                        field: 'product.name',
                        title: 'Diseño',
                        sortable: true,
                        switchable: true,
                    },{
                        field: 'product.subcategory_name',
                        title: 'Material',
                        sortable: true,
                        switchable: true,
                    },{
                        field: 'product.type_name',
                        title: 'Caracteristicas',
                        sortable: true,
                        switchable: true,
                    },{
                        field: 'total',
                        title: 'Subtotal',
                        sortable: true,
                        switchable: true,
                    },{
                        field: 'discount',
                        title: 'Descuento',
                        sortable: true,
                        switchable: true,
                    },{
                        field: 'discount_admin',
                        title: 'Descuento Admin',
                        sortable: true,
                        switchable: true,
                    },{
                        field: 'subtotal',
                        title: 'Total',
                        sortable: true,
                        switchable: true,
                    },{
                        field: 'status',
                        title: 'Estado',
                        sortable: true,
                        switchable: true,
                    }
                ],
                pagination: true,
                search: true
            });

            this.getOrders();
        },
        getOrders() {
            axios.get('/api/clients/' + this.selectedClient.id + '/data').then(result => {
                this.orders = result.data.orders;

                jQuery('#orders').bootstrapTable('removeAll');
                jQuery('#orders').bootstrapTable('append', this.orders);

                this.$parent.inPetition = false;
            }).catch(error => {
                this.$parent.inPetition = false;
                this.$parent.handleErrors(error);
            });
        }
    },
    mounted() {
    }
}
</script>
<style lang="scss" scoped>
.custom-select {
    color: #000;
    background-color: silver;
    border: 1px solid;
    border-top-left-radius: 0px;
    border-bottom-left-radius: 0px;
}
</style>
