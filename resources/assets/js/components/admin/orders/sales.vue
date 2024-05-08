<template>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default panel-shadow" data-collapsed="0">
                <div class="panel-heading">
                    <div class="panel-title">Filtros</div>
                </div> <!-- panel body -->
                <div class="panel-body">
                    <div class="row filters">
                        <div class="col-md-6">
                            <p>Desde:</p>
                            <input type="date" class="form-control" v-model="filters.start">
                            <!-- <p v-if="isAdmin">PDV:</p>
                            <select class="form-control" v-model="branchId" v-if="isAdmin">
                                <option :value="branch.id" v-for="branch in branches">{{ branch.name }}</option>
                            </select> -->
                            <button class="btn btn-block btn-danger" @click="clients = []; branchId = null" v-if="branchId != null && isAdmin" style="font-size: 11px;">Deseleccionar sucursal</button>
                        </div>
                        <div class="col-md-6">
                            <p>Hasta:</p>
                            <input type="date" class="form-control" v-model="filters.end">
                            <button class="btn btn-block btn-success" @click="makeSearch()">Buscar</button>
                        </div>
                        <!-- <div class="col-md-6"> -->
                        <!-- </div> -->
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <table class="table responsive" v-if="isAdmin && !branchId">
                <thead>
                    <tr>
                        <th>PDV</th>
                        <th>Clientes</th>
                        <th>Subtotal</th>
                        <th>Descuento sistema</th>
                        <th>Descuento adicional</th>
                        <th>Total</th>
                        <th>RX</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="branch in data.branches" :key="branch.id">
                        <td>{{ branch.name }}</td>
                        <td><button class="btn" @click="getClients(branch.id)">{{ branch.clients_orders }}</button></td>
                        <td>{{ branch.subtotal | currency }}</td>
                        <td>{{ branch.descuentos | currency }}</td>
                        <td>{{ branch.descuentos_admin | currency }}</td>
                        <td>{{ branch.total | currency }}</td>
                        <td><button class="btn" @click="selectPdv(branch.id)">({{ branch.orders }})</button></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6"><b class="pull-right">Subtotal:</b></td>
                        <td>{{ data.subtotal | currency }}</td>
                    </tr>
                    <tr>
                        <td colspan="6"><b class="pull-right">Descuentos:</b></td>
                        <td>{{ data.discounts | currency }}</td>
                    </tr>
                    <tr>
                        <td colspan="6"><b class="pull-right">Total:</b></td>
                        <td>{{ data.total | currency }}</td>
                    </tr>
                    <tr>
                        <td colspan="6"><b class="pull-right">Ordenes totales:</b></td>
                        <td>{{ data.ordersCount }}</td>
                    </tr>
                    <tr>
                        <td colspan="6"><b class="pull-right">Ticket promedio:</b></td>
                        <td>{{ data.avg | currency }}</td>
                    </tr>
                </tfoot>
            </table>
            <table class="table responsive" v-else-if="(isAdmin && branchId && clientId) || isPDV">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Nombre Comercial</th>
                        <th>Celular</th>
                        <th>Condicion de pago</th>
                        <th>RX's</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="client in clients" :key="client.id">
                        <td>{{ client.name }}</td>
                        <td>{{ client.comertial_name }}</td>
                        <td>{{ client.celphone }}</td>
                        <td>{{ client.payment_plan }} semanas.</td>
                        <td><button class="btn" @click="selectBranch(client.orders)">({{ client.orders.length }})</button></td>
                        <td>{{ mgetTotal(client.total, client.discounts, client.discounts_admin, client.ivas) | currency }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5"><b class="pull-right">Subtotal:</b></td>
                        <td>{{ dataBranch.subtotal | currency }}</td>
                    </tr>
                    <tr>
                        <td colspan="5"><b class="pull-right">IVA:</b></td>
                        <td>{{ dataBranch.ivas | currency }}</td>
                    </tr>
                    <tr>
                        <td colspan="5"><b class="pull-right">Descuentos:</b></td>
                        <td>{{ dataBranch.discounts | currency }}</td>
                    </tr>
                    <tr>
                        <td colspan="5"><b class="pull-right">Total:</b></td>
                        <td>{{ dataBranch.total | currency }}</td>
                    </tr>
                    <tr>
                        <td colspan="5"><b class="pull-right">Ordenes totales:</b></td>
                        <td>{{ dataBranch.ordersCount }}</td>
                    </tr>
                </tfoot>
            </table>
            <table class="table responsive" v-else>
                <thead>
                    <tr>
                        <th>RX</th>
                        <th>Cliente</th>
                        <th>Nombre Comercial</th>
                        <th>Fecha</th>
                        <th>Diseño</th>
                        <th>Material</th>
                        <th>Caracteristica</th>
                        <th>Antireflejante</th>
                        <th>Costo</th>
                        <th>Servicio</th>
                        <th>Subtotal</th>
                        <th>Descuentos Sistema</th>
                        <th>Descuento</th>
                        <th>Descuento promoción</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Observaciones</th>
                        <th>RX</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(cart,i) in orders" :key="i">
                        <td>{{ cart.rx }}</td>
                        <td>{{ cart.client.name }}</td>
                        <td>{{ cart.client.comertial_name }}</td>
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
                        <td>${{ cart.total }}</td>
                        <td>{{ cart.discount | currency }}</td>
                        <td>{{ cart.discount_admin | currency }}</td>
                        <td>
                            <span v-if="cart.promo_discount != null">$ {{ cart.promo_discount }}</span>
                            <span v-else>N/A</span>
                        </td>
                        <td>{{ mgetTotal(cart.total, cart.discount, cart.discount_admin, cart.iva) | currency }}</td>
                        <td>{{ cart.status.charAt(0).toUpperCase() + cart.status.replace('_', ' ').slice(1) }}</td>
                        <td v-if="cart.purchase.description != null">{{ cart.purchase.description }}</td>
                        <td v-else>{{ 'N/A' }}</td>
                        <td><button v-if="cart.have_data" class="btn btn-success" @click="showNote(i)">RX</button>
                            <button v-else class="btn btn-info" >RX</button>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="14"><b class="pull-right">Subtotal:</b></td>
                        <td>${{ getSubtotal }}</td>
                    </tr>
                    <tr>
                        <td colspan="14"><b class="pull-right">Descuentos:</b></td>
                        <td>${{ getDiscounts }}</td>
                    </tr>
                    <tr>
                        <td colspan="14"><b class="pull-right">IVA:</b></td>
                        <td>${{ getIva }}</td>
                    </tr>
                    <tr>
                        <td colspan="14"><b class="pull-right">Total:</b></td>
                        <td>${{ getTotal }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div style="display:none;">
            <div id="modal_rx">
                <div class="row">
                    <div class="col-md-12">
                        <h4>RX</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table responsive">
                            <thead>
                                <tr>
                                    <th>RX</th>
                                    <th>Cliente</th>
                                    <th>Fecha</th>
                                    <th>Semanas restantes</th>
                                    <th>Diseño</th>
                                    <th>Material</th>
                                    <th>Caracteristica</th>
                                    <th>Antireflejante</th>
                                    <th>Subtotal</th>
                                    <th>Descuentos sistema</th>
                                    <th>Descuento</th>
                                    <th>Descuento promoción</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Observaciones</th>
                                    <th>RX</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(cart, i) in orders" :key="i">
                                    <td>{{ cart.rx }}</td>
                                    <td>{{ cart.client.name }}</td>
                                    <td>{{ cart.created_at }}</td>
                                    <td>
                                        <p style="background-color:green;" v-if="cart.status=='pagado'">0</p>
                                        <p style="background-color:red;" v-else-if="cart.passed<0">{{ cart.passed }}</p>
                                        <p style="background-color:orange;" v-else>{{ cart.passed }}</p>
                                    </td>
                                    <td>{{ cart.product ? cart.product.name : 'NO DISPONIBLE'  }}</td>
                                    <td>{{ cart.product ? cart.product.subcategory_name : 'NO DISPONIBLE' }}</td>
                                    <td>{{ cart.product ? cart.product.type_name : 'NO DISPONIBLE' }}</td>
                                    <td v-if="cart.extras[0]">
                                        {{ cart.extras.map(row =>{return row.name}).join(", ") }}
                                    </td>
                                    <td v-else>
                                        -
                                    </td>
                                    <td>{{ cart.total | currency table}}</td>
                                    <td>{{ cart.discount | currency }}</td>
                                    <td>{{ cart.discount_admin | currency }}</td>
                                    <td>
                                        <span v-if="cart.promo_discount != null">$ {{ cart.promo_discount }}</span>
                                        <span v-else>N/A</span>
                                    </td>
                                    <td>{{ mgetTotal(cart.total,cart.discount,cart.discount_admin,cart.iva, cart.promo_discount) | currency }}</td>
                                    <td>
                                        <button class="btn btn-warning">{{ cart.status.replace("_"," ") }}</button>
                                    </td>
                                    <td v-if="cart.purchase.description != null">{{ cart.purchase.description }}</td>
                                    <td v-else>{{ 'N/A' }}</td>
                                    <td><button v-if="cart.have_data" class="btn btn-success" @click="showNote(i)">RX</button>
                                        <button v-else class="btn btn-info" >RX</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <sweet-modal ref="showRxModal" width="60%">

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
                    <label style="font-weight:300">OI ESFERA</label>
                    <input v-model="orders[indx_show].rx_od_esfera_dos" class="form-control" id="od_esfera_dos" disabled>
                                </div>
                    <div class="col-sm-2" style="text-align: left;">
                    <label style="font-weight:300">OI CILINDRO</label>
                    <input v-model="orders[indx_show].rx_od_cilindro_dos" class="form-control" id="od_cilindro_dos" disabled>
                                </div>
                    <div class="col-sm-2" style="text-align: left;">
                    <label style="font-weight:300">OI EJE</label>
                    <input v-model="orders[indx_show].rx_od_eje_dos" class="form-control" id="od_eje_dos" disabled>
                                </div>
                    <div class="col-sm-2" style="text-align: left;">
                    <label style="font-weight:300">OI ADICION</label>
                    <input v-model="orders[indx_show].rx_od_adicion_dos" class="form-control" id="od_adicion_dos" disabled>
                                </div>
                    <div class="col-sm-2" style="text-align: left;">
                    <label style="font-weight:300">OI DIP</label>
                    <input v-model="orders[indx_show].rx_od_dip_dos" class="form-control" id="od_dip_dos" disabled>
                                </div>
                    <div class="col-sm-2" style="text-align: left;">
                    <label style="font-weight:300">OI ALTURA</label>
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

                <!-- <div class="form-group">
                    <div class="col-sm-12">
                       
                        <button type="button" class="btn btn-default pull-right" @click="$refs.showRxModal.close()">Cerrar</button>
                        <button type="button" class="btn btn-dark pull-left" style="background-color: black;color: white;" @click="requestRx(indx_show)">Solicitar RX</button>
                    </div>
                </div> -->

            </form>

            <!-- <form role="form" class="form-horizontal" v-if="indx_show != null">
                <p><b>Captura de datos</b></p>
                <input-form name="rx" text="RX" :data.sync="orders[indx_show].rx_rx" disabled></input-form>
                <input-form name="fecha" text="Fecha" :data.sync="orders[indx_show].rx_fecha" type="date" disabled></input-form>
                <input-form name="cliente" text="Cliente" :data.sync="orders[indx_show].rx_cliente" disabled></input-form>

                <p><b>Graduación</b></p>
                <input-form name="od_esfera" text="OD Esfera" :data.sync="orders[indx_show].rx_od_esfera" disabled></input-form>
                <input-form name="od_cilindro" text="OD Clilindro" :data.sync="orders[indx_show].rx_od_cilindro" disabled></input-form>
                <input-form name="od_eje" text="OD Eje" :data.sync="orders[indx_show].rx_od_eje" disabled></input-form>
                <input-form name="od_adicion" text="OD Adición" :data.sync="orders[indx_show].rx_od_adicion" disabled></input-form>
                <input-form name="od_dip" text="OD Dip" :data.sync="orders[indx_show].rx_od_dip" disabled></input-form>
                <input-form name="od_altura" text="OD Altura" :data.sync="orders[indx_show].rx_od_altura" disabled></input-form>
                <hr>
                <input-form name="od_esfera" text="OD Esfera" :data.sync="orders[indx_show].rx_od_esfera_dos" disabled></input-form>
                <input-form name="od_cilindro" text="OD Clilindro" :data.sync="orders[indx_show].rx_od_cilindro_dos" disabled></input-form>
                <input-form name="od_eje" text="OD Eje" :data.sync="orders[indx_show].rx_od_eje_dos" disabled></input-form>
                <input-form name="od_adicion" text="OD Adición" :data.sync="orders[indx_show].rx_od_adicion_dos" disabled></input-form>
                <input-form name="od_dip" text="OD Dip" :data.sync="orders[indx_show].rx_od_dip_dos" disabled></input-form>
                <input-form name="od_altura" text="OD Altura" :data.sync="orders[indx_show].rx_od_altura_dos" disabled></input-form>
            
                <div class="form-group">
                                <label class="col-sm-3 control-label">Diseño:</label>
                                <div class="col-sm-7">
                                        <v-select v-model="orders[indx_show].rx_diseno" :options="disenoOpcs" label="label" index="value" disabled/>
                                </div>
                        </div>

                <div class="form-group">
                                <label class="col-sm-3 control-label">Material:</label>
                                <div class="col-sm-7">
                                        <v-select v-model="orders[indx_show].rx_material" :options="materialOpcs" label="label" index="value" disabled/>
                                </div>
                        </div>

                <div class="form-group">
                                <label class="col-sm-3 control-label">Tipo AR:</label>
                                <div class="col-sm-7">
                                        <v-select v-model="orders[indx_show].rx_tipo_ar" :options="tipoarOpcs" label="label" index="value" disabled/>
                                </div>
                        </div>

                <div class="form-group">
                                <label class="col-sm-3 control-label">Tallado:</label>
                                <div class="col-sm-7">
                                        <v-select v-model="orders[indx_show].rx_tallado" :options="talladoOpcs" label="label" index="value" disabled/>
                                </div>
                        </div>

        
                <p><b>Armazón</b></p>
                <div class="form-group">
                                <label class="col-sm-3 control-label">Tipo de armazón:</label>
                                <div class="col-sm-7">
                                        <v-select v-model="orders[indx_show].rx_tipo_armazon" :options="tipo_armazonOpcs" label="label" index="value" disabled/>
                                </div>
                        </div>


                <input-form text='Horizontal "A"' name="horizontal_a" :data.sync="orders[indx_show].rx_horizontal_a" disabled></input-form>
                <input-form text='Vertical "B"' name="vertical_b" :data.sync="orders[indx_show].rx_vertical_b" disabled></input-form>
                <input-form text='Diagonal "ED" ' name="diagonal_ed" :data.sync="orders[indx_show].rx_diagonal_ed" disabled></input-form>
                <input-form text="Puente" name="puente" :data.sync="orders[indx_show].rx_puente" disabled></input-form>
                    
                <div class="form-group">
                    <label class="col-sm-3 control-label">Observaciones:</label>
                    <div class="col-sm-7">
                        <textarea v-model="orders[indx_show].rx_observaciones" disabled style="width:100%"></textarea>
                    </div>
                </div>

             
                <div class="form-group">
                    <div class="col-sm-12">
                       
                        <button type="button" class="btn btn-default pull-right" @click="$refs.showRxModal.close()">Cerrar</button>
                        <button type="button" class="btn btn-dark pull-left" style="background-color: black;color: white;" @click="requestRx(indx_show)">Solicitar RX</button>
                    </div>
                </div>

            </form> -->

          
        </sweet-modal>
        <sweet-modal ref="modalloading">
            <div class="fa-3x"><i class="fas fa-spinner fa-pulse"></i></div><br>
            <p>Cargando...</p>
          </sweet-modal>
    </div>
</template>
<script>
export default {
    data(){
        return {
            branchId: null,
            clientId: null,
            clients: [],
            data: {
                branches: [],
                subtotal: 0.0,
                discounts: 0.0,
                total: 0.0
            },
            dataBranch: {
                subtotal: 0.0,
                discounts: 0.0,
                total: 0.0
            },
            orders:[],
            filters: {
                start: '',
                end: '',
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
                {value:'HD',label:'HD'},
            ],
            tipo_armazonOpcs:[
                {value:'Metálico',label:'Metálico'},
                {value:'Perforado',label:'Perforado'},
                {value:'Plástico',label:'Plástico'},
                {value:'Ranurado',label:'Ranurado'},
            ],
        }
    },
    computed:{
        isAdmin: function() {
            let roles = this.$parent.user.roles;
            for(let role of roles)
                if(role.permissions.filter(p => p.name == 'cobranza_admin').length != 0)
                    return true;
            return false;
        },
        isPDV: function() {
            let roles = this.$parent.user.roles;
            for(let role of roles)
                if(role.name == 'punto de ventas' || role.name == 'Ejecutivo')
                    return true;
            return false;
        },
        getSubtotal:function(){
            let subtotal=0;
            this.orders.forEach((v)=>{
                subtotal+=parseFloat(v.total);
            });
            return parseFloat(subtotal.toFixed(2));
        },
        getIva:function(){
            let iva=0;
            this.orders.forEach((v)=>{
                iva+=parseFloat(v.iva);
            });
            return iva.toFixed(2);
        },
        getDiscounts:function(){
            let discounts=0;
            this.orders.forEach((v)=>{
                discounts += parseFloat(v.discount) + parseFloat(v.discount_admin);
            });
            return parseFloat(discounts.toFixed(2));
        },
        getTotal:function(){
            let total=0;
            this.orders.forEach((v)=>{
                total += parseFloat(v.total) - parseFloat(v.discount) - parseFloat(v.discount_admin) + parseFloat(v.iva);
            });
            return parseFloat(total.toFixed(2));
        }
    },
    methods:{
        mgetTotal:function(subtotal,dis,dis1,iva, promo = null){
            console.log(promo);
            if(promo != null)
                return promo;
            iva = parseFloat(iva);
            return Math.abs(parseFloat((parseFloat(subtotal) - this.mgetDiscount(dis,dis1)) + iva).toFixed(2));
        },
        mgetDiscount:function(dis,dis1){
            return parseFloat(dis || 0) + parseFloat(dis1 || 0);
        },
        makeSearch() {
            if(this.isAdmin) {
                if(this.branchId != null)
                    this.getClients(this.branchId);
                else
                    this.getBranches();
            } else if(this.isPDV) {
                this.getClients(this.$parent.user.branch_id);
            } else {
                this.getOrders();
            }
        },
        getBranches(){
            this.$parent.inPetition=true;
            axios.get(tools.url("/api/branches/today"  + `?from=${ this.filters.start }&to=${ this.filters.end }`)).then((response)=>{
                this.data = response.data;
                // this.branches.forEach((v,k)=>{
                    // this.branches[k].laboratory=v.laboratory.name;
                    // this.branches[k].state=v.state.name;
                    // this.branches[k].town=v.town.name;
                // });


                jQuery('#branches').bootstrapTable('removeAll');
                jQuery('#branches').bootstrapTable('append',this.branches);
                //
                //
                // this.branchId = this.branches[0].id;
                // this.getOrders();

                this.$parent.inPetition=false;
            }).catch((error)=>{
                this.$parent.handleErrors(error);
                this.$parent.inPetition=false;
            });
        },
        getOrders:function(){
            this.orders = [];
            this.$parent.inPetition=true;
            axios.get(tools.url('api/orders/today' + (this.branchId != null ? `/${ this.branchId }` : '') + `?from=${ this.filters.start }&to=${ this.filters.end }`))
            .then((res)=>{
                this.$parent.inPetition=false;
                this.orders=res.data;
            })
            .catch((err)=>{
                this.$parent.inPetition=false;
                this.$parent.handleErrors(err);
            });
        },
        getClients(branch_id = null) {
            this.$parent.inPetition=true;

            this.branchId = branch_id;
            this.dataBranch = {
                subtotal: 0.0,
                discounts: 0.0,
                ivas: 0.0,
                total: 0.0,
                ordersCount: 0,
            };

            this.clientId = true;
            this.clients = [];

            axios.get(tools.url("api/clients/today" + (branch_id ? `/${ branch_id }`: '') + `?from=${ this.filters.start }&to=${ this.filters.end }`)).then((res)=>{
                this.clients=res.data;
                this.clients.forEach(c => {
                    this.dataBranch.subtotal += parseFloat(c.total);
                    this.dataBranch.discounts += parseFloat(c.discounts_admin) + parseFloat(c.discounts);
                    this.dataBranch.ivas += parseFloat(c.ivas);
                    this.dataBranch.ordersCount += parseInt(c.orders.length);
                });

                this.dataBranch.total = this.dataBranch.subtotal + this.dataBranch.ivas - this.dataBranch.discounts;
                this.$parent.inPetition=false;
            })
            .catch((err)=>{
                this.$parent.handleErrors(err);
                this.$parent.inPetition=false;
            });
        },
        selectPdv(branch_id) {
            this.clientId = null;
            this.branchId = branch_id;
            this.getOrders();
        },
        selectBranch:function(orders) {
            this.orders=orders;
            alertify.rxDialog(document.getElementById('modal_rx'));
        },
        showNote(indx){
            this.indx_show = indx;
            this.$refs.showRxModal.open();
        },
        requestRx(indx){
            this.$refs.modalloading.open();
            this.$parent.inPetition=true;
            axios.get(tools.url("/api/orders/requestrx/"+this.orders[indx]['id'])).then((response)=>{
               
                this.$parent.inPetition=false;
                this.$refs.modalloading.close();
                window.open(response.data,'_blank');
            }).catch((error)=>{
                this.$parent.handleErrors(error);
                this.$parent.inPetition=false;
                this.$refs.modalloading.close();
            });
        }
    },
    mounted(){
        let date = new Date();
            date.setTime(new Date().getTime() - (6 * 60 * 60 * 1000));
        this.filters.start = this.filters.end = date.toISOString().slice(0,10);
        if(this.isAdmin)
            this.getBranches();
        else if(this.isPDV)
            this.getClients(this.$parent.user.branch_id);
        else
            this.getOrders();

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
                            maximizable:true,
                            resizable:true,
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
<style lang="scss" scoped>

.filters {
    div {
        margin-bottom: 15px;
    }

    button {
        margin-top: 30px;
    }
}

</style>
