<template>
  <div id="orders-log">
      <div class="form-horizontal">
          <div class="form-group">
              <label class="control-label col-sm-4">RX:</label>
              <div class="col-sm-4" style="padding-right: 0px; ">
                  <input type="text" class="form-control" v-model="rxInput" debounce="1000"/>
              </div>
          </div>
      </div>
      <hr/>
      <div class="table-container">
          <table id="rx-table" class="table" v-if="orders.length != 0">
              <thead>
                  <tr>
                      <th>Folio</th>
                      <th>Producto</th>
                      <th>Cliente</th>
                      <th>Nombre Comercial</th>
                      <th>Antireflejante</th>
                      <th>Costo</th>
                      <th>Servicio</th>
                      <th>Subtotal</th>
                      <th>Descuentos sistema</th>
                      <th>Descuento</th>
                      <!-- <th>Descuento Promoción</th> -->
                      <th>Precio final</th>
                      <th>Status</th>
                      <th>Fecha de captura</th>
                  </tr>
              </thead>
             <tbody>
                 <tr v-for="(order, i) in orders" :key="i">
                     <td>{{ order.id }}</td>
                     <td>{{ order.product ? order.product.name : 'N/A' }}</td>
                     <td>{{ order.client.name }}</td>
                     <td>{{ order.client.comertial_name }}</td>
                     <td v-if="order.extras[0]">
                         {{ order.extras.map(row =>{return row.name}).join(", ") }}
                     </td>
                     <td v-else>
                         -
                     </td>
                     <td>${{ order.price }}</td>
                     <td>${{ order.service }}</td>
                     <td>${{ order.total }}</td>
                     <td>{{ order.discount | currency }}</td>
                     <td>{{ order.discount_admin | currency }}</td>
                     <!-- <td>
                         <span v-if="order.promo_discount != null">$ {{ order.promo_discount }}</span>
                         <span v-else>N/A</span>
                     </td> -->
                     <td>{{ order.total_real | currency  }}</td>
                     <td>{{ order.status.replace("_"," ") }}</td>
                     <td>{{ order.created_at }}</td>
                 </tr>
             </tbody>
          </table>
      </div>
  </div>
</template>

<script>
function debounce (fn, delay) {
    var timeoutID = null;
    return function () {
        clearTimeout(timeoutID)
        var args = arguments
        var that = this
        timeoutID = setTimeout(function () {
            fn.apply(that, args)
        }, delay)
    }
}

export default {
    data: () => ({
        rxInput: '',
        rxToSearch: '',
        isInputFunctionRunning: false,
        orders: [],
        logData: []
    }),
    watch: {
        rxInput: debounce(function() {
            if (this.isInputFunctionRunning) {
                return;
            }

            this.isInputFunctionRunning = true;
            this.rxToSearch = this.rxInput;
            this.isInputFunctionRunning = false;
        }, 1000),
        rxToSearch(val) {
            if(val != '')
                this.searchRx();
        }
    },
    methods: {
        searchRx() {
            this.$parent.inPetition = true;
            this.orders = [];

            jQuery('#rx-table').bootstrapTable('removeAll');
            jQuery('#rx-table').bootstrapTable('destroy');

            axios.get('/api/orders/tableSearch?rx=' + this.rxToSearch).then(result => {
                this.orders = result.data;

                setTimeout(() => {
                    this.mountTable();
                    this.$parent.inPetition = false;
                }, 1000);
            }).catch(error => {
                this.$parent.inPetition = false;
                this.$parent.handleErrors(error);
            });
        },
        mountTable(){
            jQuery('#rx-table').bootstrapTable('removeAll');
            jQuery('#rx-table').bootstrapTable('destroy');

            jQuery('#rx-table').bootstrapTable({
                //Boton de refrescar x
                exportDataType: 'all',
                showRefresh: false,
                showToggle: false,
                pagination: true,
                search: true,
            });


            jQuery('#rx-table').on('click-row.bs.table', (row, data) => {
                let id = data['0'];

                axios.get('/api/orders/log/' + id).then(result => {
                    this.logData = result.data;
                });
            });
        },
    }
}
</script>

<style lang="scss" scoped>
#rx-table tbody td {
    cursor: pointer;
}
</style>
