<template>
  <div>
    <form-wizard
      @on-complete="onComplete"
      ref="form"
      :start-index="index"
      color="gray"
      shape="circle"
      title="Nuevo pedido"
      subtitle="Sigue los pasos marcados"
      nextButtonText="Siguiente"
      backButtonText="Atras"
      finishButtonText="Finalizar"
    >
      <tab-content
        title="Cliente"
        icon="fa fa-user"
        :beforeChange="saveClient"
        ref="client"
      >
        <div class="panel panel-primary" data-collapsed="0">
          <div class="panel-heading">
            <div class="panel-title">
              <i class="fab fa-odnoklassniki"></i> Cliente
            </div>
          </div>

          <div class="panel-body">
            <form role="form" class="form-horizontal">
              <div class="row">
                <div class="col-md-12">
                  <button
                    type="button"
                    class="btn btn-secondary pull-right"
                    style="margin-bottom: 10px"
                    @click="cleanClient"
                  >
                    <i></i> Limpiar
                  </button>
                  <button
                    type="button"
                    class="btn btn-info pull-right"
                    style="margin-bottom: 10px"
                    @click="searchClient"
                  >
                    <i class="fa fa-search"></i> Buscar cliente
                  </button>
                </div>
              </div>
              <hr />
              <div class="col-md-12">
                <div class="tabs-vertical-env">
                  <ul class="nav tabs-vertical">
                    <li class="active">
                      <a href="#v-data" data-toggle="tab" aria-expanded="true"
                        >Datos</a
                      >
                    </li>
                    <li class="">
                      <a
                        href="#v-contact"
                        data-toggle="tab"
                        aria-expanded="false"
                        >Contacto</a
                      >
                    </li>
                    <li class="">
                      <a
                        href="#v-facturacion"
                        data-toggle="tab"
                        aria-expanded="false"
                        >Facturacion</a
                      >
                    </li>
                  </ul>
                  <div class="tab-content">
                    <div class="tab-pane active" id="v-data">
                      <input-form
                        name="nombre"
                        :disabled="true"
                        text="Nombre"
                        :data.sync="client.name"
                        validate="required"
                      ></input-form>
                      <input-form
                        name="email"
                        :disabled="true"
                        text="Email"
                        :data.sync="client.email"
                        validate="required"
                      ></input-form>
                      <input-form
                        name="telefono"
                        :disabled="true"
                        text="Telefono"
                        :data.sync="client.phone"
                        validate="digits:8"
                      ></input-form>
                      <input-form
                        name="celular"
                        :disabled="true"
                        text="Celular"
                        :data.sync="client.celphone"
                        validate="digits:10"
                      ></input-form>
                      <input-form
                        name="nombre_comercial"
                        :disabled="true"
                        text="Nombre comercial"
                        :data.sync="client.comertial_name"
                      ></input-form>
                      <!-- <input-form name="RFC" :disabled="true" text="R.F.C." :data.sync="client.rfc"></input-form> -->
                      <select-form
                        text="Estado"
                        name="state"
                        :options="states"
                        :data.sync="client.state"
                      ></select-form>
                      <select-form
                        text="Ciudad"
                        name="town"
                        :options="towns"
                        :data.sync="client.town"
                      ></select-form>
                      <input-form
                        name="codigo_postal"
                        :disabled="true"
                        text="Codigo postal"
                        :data.sync="client.postal_code"
                        validate="required|digits:5"
                      ></input-form>
                      <input-form
                        name="direccion"
                        :disabled="true"
                        text="Direccion"
                        :data.sync="client.address"
                        validate="required"
                      ></input-form>
                      <input-form
                        name="colonia"
                        :disabled="true"
                        text="Colonia"
                        :data.sync="client.suburb"
                        validate="required"
                      ></input-form>
                    </div>
                    <div class="tab-pane" id="v-contact">
                      <input-form
                        name="contacto_nombre"
                        :disabled="true"
                        text="Contacto nombre"
                        :data.sync="client.contact_name"
                        validate=""
                      ></input-form>
                      <input-form
                        name="contacto_email"
                        :disabled="true"
                        text="Contacto email"
                        :data.sync="client.contact_email"
                        validate="email"
                      ></input-form>
                      <input-form
                        name="contacto_telefono"
                        :disabled="true"
                        text="Contacto telefono"
                        :data.sync="client.contact_phone"
                        validate="digits:8"
                      ></input-form>
                      <input-form
                        name="contacto_celular"
                        :disabled="true"
                        text="Contacto celular"
                        :data.sync="client.contact_celphone"
                        validate="digits:10"
                      ></input-form>
                    </div>
                    <div class="tab-pane" id="v-facturacion">
                      <input-form
                        name="nombre"
                        :disabled="true"
                        text="Nombre"
                        :data.sync="client.facturacion.name"
                      ></input-form>
                      <input-form
                        name="telefono"
                        :disabled="true"
                        text="Telefono"
                        :data.sync="client.facturacion.phone"
                        validate="digits:8"
                      ></input-form>
                      <input-form
                        name="celular"
                        :disabled="true"
                        text="Celular"
                        :data.sync="client.facturacion.celphone"
                        validate="digits:10"
                      ></input-form>
                      <input-form
                        name="RFC"
                        :disabled="true"
                        text="R.F.C."
                        :data.sync="client.facturacion.rfc"
                      ></input-form>
                      <input-form
                        name="direccion"
                        :disabled="true"
                        text="Direccion"
                        :data.sync="client.facturacion.address"
                      ></input-form>
                      <input-form
                        name="colonia"
                        :disabled="true"
                        text="Colonia"
                        :data.sync="client.facturacion.suburb"
                      ></input-form>
                      <select-form
                        text="Estado"
                        name="state"
                        :options="states"
                        :data.sync="client.facturacion.state"
                      ></select-form>
                      <select-form
                        text="Ciudad"
                        name="town"
                        :options="towns"
                        :data.sync="client.facturacion.town"
                      ></select-form>
                      <input-form
                        name="codigo_postal"
                        :disabled="true"
                        text="Codigo postal"
                        :data.sync="client.facturacion.postal_code"
                      ></input-form>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </tab-content>
      <tab-content title="Pedido" icon="fas fa-shopping-cart">
        <div class="panel panel-primary" data-collapsed="0">
          <div class="panel-heading">
            <div class="panel-title">
              <i class="fas fa-shopping-cart"></i> Pedido
            </div>
          </div>

          <div class="panel-body">
            <div class="col-md-10">
              <table class="table responsive">
                <thead>
                  <tr>
                    <th>RX</th>
                    <th>Diseño</th>
                    <th>Material</th>
                    <th>Caracteristica</th>
                    <th v-if="!isAugenLabs">Extras</th>
                    <th v-else>Cantidad</th>
                    <th>Costo</th>
                    <th v-if="!isAugenLabs">Servicio</th>
                    <th>Subtotal</th>
                    <th>RX</th>
                    <th>Eliminar</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(cart, i) in $parent.sale.cart" :key="i">
                    <td>{{ cart.rx }}</td>
                    <td>{{ cart.product.name }}</td>
                    <td>{{ cart.product.category_name }}</td>
                    <td>{{ cart.product.type_name }}</td>
                    <td v-if="!isAugenLabs">
                      {{
                        cart.extras
                          .map((row) => {
                            return row.name;
                          })
                          .join(", ")
                      }}
                      <button
                        class="btn btn-sm btn-warning"
                        @click="selectOrder(cart)"
                      >
                        <i class="fa fa-plus"></i>
                      </button>
                    </td>
                    <td v-else>
                      <input
                        
                        class="form-control"
                        v-model="cart.service"
                        @change="getDiscounts"
                        
                        @keypress="isNumber($event)"
                      />
                    </td>
                    <td>${{ cart.price }}</td>
                    <td v-if="!isAugenLabs">
                      <input
                        
                        v-model="cart.service"
                        @change="getDiscounts"
                        
                        @keypress="isNumber($event)"
                        width="50px"
                        id="aaaa"
                      />
                    </td>
                    <td>${{ cart.total }}</td>
                    <td>
                      <button v-if="cart.rx_data.have_data" class="btn btn-success btn-sm" @click="openModalRx(i)">RX</button>
                      <button v-else class="btn btn-info btn-sm" @click="openModalRx(i)">RX</button>
                    </td>

                    <td>
                      <button
                        class="btn btn-danger btn-sm"
                        @click="deleteProduct(i)"
                      >
                        Borrar
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="col-md-2">
              <button class="btn btn-success" @click="selectType">
                <i class="fa fa-search"></i> Buscar producto
              </button>
            </div>
          </div>
        </div>
      </tab-content>
      <tab-content title="Checkout" icon="fas fa-check">
        <div class="row">
          <div class="col-md-12">
            <div class="panel panel-primary" data-collapsed="0">
              <div class="panel-heading">
                <div class="panel-title">
                  <i class="fab fa-odnoklassniki"></i> Cliente
                </div>
              </div>

              <div class="panel-body">
                <div class="row">
                  <div class="col-md-4">Nombre</div>
                  <div class="col-md-8">{{ client.name }}</div>
                </div>
                <div class="row">
                  <div class="col-md-4">Email:</div>
                  <div class="col-md-8">{{ client.email }}</div>
                </div>
                <div class="row">
                  <div class="col-md-4">Celular:</div>
                  <div class="col-md-8">{{ client.celphone }}</div>
                </div>
                <div class="row">
                  <div class="col-md-4">Nombre comercial:</div>
                  <div class="col-md-8">{{ client.comertial_name }}</div>
                </div>
                <div class="row">
                  <div class="col-md-4">Domicilio:</div>
                  <div class="col-md-8">{{ client.address }}</div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-121">
            <div class="panel panel-primary" data-collapsed="0">
              <div class="panel-heading">
                <div class="panel-title">
                  <i class="fas fa-shopping-cart"></i> Pedido
                </div>
              </div>

              <div class="panel-body">
                <table class="table responsive">
                  <thead>
                    <tr>
                      <th>RX</th>
                      <th>Diseño</th>
                      <th>Material</th>
                      <th>Caracteristica</th>
                      <th>Antireflejante</th>
                      <th>Costo</th>
                      <th v-if="!isAugenLabs">Servicio</th>
                      <th v-else>Cantidad</th>
                      <th>Descuento</th>
                      <th>Subtotal</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(cart, i) in $parent.sale.cart" :key="i">
                      <td>{{ cart.rx }}</td>
                      <td>{{ cart.product.name }}</td>
                      <td>{{ cart.product.category_name }}</td>
                      <td>{{ cart.product.type_name }}</td>
                      <td v-if="cart.extras[0]">
                        {{
                          cart.extras
                            .map((row) => {
                              return row.name;
                            })
                            .join(", ")
                        }}
                      </td>
                      <td v-else>-</td>
                      <td>${{ cart.price }}</td>
                      <td v-if="!isAugenLabs">${{ cart.service }}</td>
                      <td v-else>{{ cart.service }}</td>
                      <td>{{ cart.percent_discount }}%</td>
                      <td>${{ parseFloat(cart.total - cart.discount) }}</td>
                    </tr>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td colspan="8" class="text-right">Descuentos:</td>
                      <td>${{ $parent.sale.discounts }}</td>
                    </tr>
                    <tr>
                      <td colspan="8" class="text-right">IVA:</td>
                      <td>${{ $parent.sale.ivas }}</td>
                    </tr>
                    <tr>
                      <td
                        colspan="8"
                        class="text-right"
                        style="font-size: 27px; letter-spacing: 2px"
                      >
                        Total:
                      </td>
                      <td
                        style="font-size: 28px;position: absolute;font-weight: bold;margin-left: -12px;"
                        v-if="isAugenLabs"
                      >
                        ${{
                          Math.ceil(
                            $parent.sale.total -
                              $parent.sale.discounts +
                              parseFloat($parent.sale.ivas)
                          ).toFixed(2)
                        }}
                      </td>
                      <td
                        style="font-size: 28px;position: absolute;font-weight: bold; margin-left: -12px;"
                        v-else
                      >
                        ${{
                          (
                            $parent.sale.total -
                            $parent.sale.discounts +
                            parseFloat($parent.sale.ivas)
                          ).toFixed(2)
                        }}
                      </td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
            <h4>Observaciones</h4>
            <textarea
              name=""
              id=""
              cols="30"
              rows="6"
              class="form-control"
              v-model="$parent.sale.observation"
            ></textarea>
          </div>
        </div>
      </tab-content>
    </form-wizard>
    <div style="display: none">
      <table class="table responsive" id="clients_table">
        <thead>
          <tr>
            <th>#</th>
            <th>Nombres</th>
            <th>Nombre comercial</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="client in clients"
            :key="client.id"
            @click="selectClient(client.id)"
          >
            <td>{{ client.id }}</td>
            <td>{{ client.name }}</td>
            <td>{{ client.comertial_name }}</td>
            <td>
              <button class="btn btn-sm"><i class="fa fa-plus"></i></button>
            </td>
          </tr>
        </tbody>
      </table>
      <div id="types_table">
        <div class="row" v-if="hasLists">
          <div class="col-md-12">
            <h3 v-if="selectedListId == null">Selecciona una lista de precios</h3>
            <h3 v-else>Elige un tipo de producto:</h3>
          </div>
          <template v-if="selectedListId == null">
            <div class="col-md-6 col-md-offset-3" v-for="(list, indx) in lists" :key="list.id">
              <button
              class="btn btn-default btn-block"
              @click="selectedListId = list.id; getTypes();"
              v-if="canViewList(list.id)"
            >
              {{ list.name }}
              </button>
            </div>
          </template>
          <template v-else>
            <div class="col-md-6 col-md-offset-3" v-for="(type,indx) in types" :key="type.id">
              <button
                class="btn btn-default btn-block"
                @click="selectProduct(type)"
                v-if="type.canSee"
              >
              {{ type.name }}
              </button>
            </div>
            <div class="col-md-6 col-md-offset-3">
              <button class="btn btn-warning btn-block" @click="selectedListId = null">Regresar</button>
            </div>
          </template>
        </div>
        <div class="row" v-else>
          <div class="col-md-12">
            <h3>Elige un tipo de producto:</h3>
          </div>
          <div
            class="col-md-6 col-md-offset-3"
            v-for="(type,indx) in types" :key="type.id">
            <!-- <br> -->
            <!-- <br> -->
            <button
              class="btn btn-default btn-block"
              @click="selectProduct(type)"
              v-if="type.canSee"
            >
              {{ type.name }}
            </button>
          </div>
        </div>
      </div>
      <div id="extras_table">
        <div class="row">
          <div class="col-md-12">
            <h3>Seleccionar extra:</h3>
          </div>
          <div
            class="col-md-6 col-md-offset-3"
            v-for="extra in extras"
            :key="extra.id"
          >
            <br />
            <br />
            <button class="btn btn-default btn-block" @click="addExtra(extra)">
              {{ extra.name }} - ${{ extra.price }}
            </button>
          </div>
          <div class="col-md-6 col-md-offset-3">
            <br />
            <br />
            <button class="btn btn-warning btn-block" @click="addExtra()">
              Omitir
            </button>
          </div>
        </div>
      </div>
      <div id="products_table">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th rowspan="2" width="15%" style="color: black">
                {{ type.name }}
              </th>
              <th
                v-for="category in categories"
                :key="category.id"
                :colspan="category.subcategories.length"
                :style="{ color: 'black', backgroundColor: category.color }"
              >
                {{ category.name }}
              </th>
            </tr>
            <tr>
              <template v-for="category in categories">
                <th
                  v-for="subcategory in category.subcategories"
                  :key="subcategory.id + category.name"
                  :style="{ color: 'black', backgroundColor: category.color }"
                  :title="subcategory.description"
                >
                  {{ subcategory.name }}
                </th>
              </template>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(product, i) in products" :key="product.id + i">
              <td>{{ product.name }}</td>
              <template v-for="category in categories">
                <td
                  v-for="subcategory in category.subcategories"
                  :key="subcategory.id + category.name"
                  :title="subcategory.description"
                >
                  <button
                    class="btn btn-sm btn-block"
                    v-if="
                      filterProduct(product, category.id, subcategory.id)
                        .price != 0
                    "
                    @click="
                      addProduct(
                        filterProduct(product, category.id, subcategory.id),
                        category,
                        subcategory
                      )
                    "
                  >
                    ${{
                      filterProduct(product, category.id, subcategory.id).price
                    }}
                  </button>
                  <p v-else>-</p>
                </td>
              </template>
            </tr>
          </tbody>
        </table>
        <small>*Precios sin I.V.A.</small>
      </div>

      <div id="new_products_table">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th></th>
                    <th v-for="currentMaterial in type.materials" :style="'width: 100px;text-align: center;' + currentMaterial.color == '#ffffff' ? 'color: #000;' : 'color: #fff;background-color: ' + currentMaterial.color + ';'" :colspan="Math.max(1, currentMaterial.material.characteristics.length)">
                        {{ currentMaterial.material.name }}
                    </th>
                </tr>
            </thead>
            <tbody>
                  <tr>
                    <td style="background-color: #f5f5f6; border: 0 !important;"></td>
                    <template v-for="(currentMaterial, idj) in type.materials">
                      <template v-for="(currentCharacteristic, idx) in currentMaterial.material.characteristics">
                        <td :style="'width: 100px;' + currentMaterial.color == '#ffffff' ? 'color: #000;' : 'color: #fff;background-color: ' + currentMaterial.color + ';'" >
                          <span>{{ currentCharacteristic.characteristic.name }}</span>
                        </td>
                      </template>
                     
                    </template>
                  </tr>
                  <tr>
                    <td>{{ type.name }}</td>
                    <template v-for="currentMaterial in type.materials">
                      <template v-for="currentCharacteristic in currentMaterial.material.characteristics">
                        <td style="width: 100px;">
                            <button class="btn btn-sm btn-block" @click="_addProduct(type, currentMaterial.material, currentCharacteristic.characteristic, currentCharacteristic.price, currentCharacteristic.cost)" v-if="currentCharacteristic.price > 0">
                              {{  currentCharacteristic.price | currency }}
                            </button>
                            <span v-else>-</span>

                            <!-- addProduct(
                        filterProduct(product, category.id, subcategory.id),
                        category,
                        subcategory
                      ) -->
                        </td>
                      </template>
                    </template>
                  </tr>
            </tbody>
          </table>
      </div>
    </div>

   
    <sweet-modal ref="modalRx" width="60%">

      <form role="form" :class="'form-horizontal' + (isSafilo ? ' is-safilo': '')" @submit.prevent="newBranch($event.target)" v-if="index_prod != null">
        <div class="form-group">
            <div class="col-sm-6" style="text-align: left;">
                <img src="https://sistema.augenlabs.com/public/images/safilo_rx_color.png" style="width: 300px; height: 50px;" v-if="isSafilo">
                <img src="https://sistema.augenlabs.com/public/images/logo.png" style="height: 50px;" v-else>
                <div style="display: block;" v-if="client.is_safilo == 1">
                    Receta SAFILO <vSwitch name="is_safilo" :checked="isSafilo" v-model="isSafilo"></vSwitch>
                </div>
            </div>
            <div class="col-sm-6" style="text-align: right;">
                <h3><b>{{ client.branch.name }}</b></h3>
                <div v-if="[1, 2, 3, 4, 5, 6].includes(client.branch.laboratory_id) && 'laboratory_id' && $parent.sale.cart[index_prod]">
                  <label>Laboratorio: </label>
                  <v-select v-model="$parent.sale.cart[index_prod].laboratory_id" :options="[
                    { id: 1, name: 'LABORATORIO GUADALAJARA'},
                    { id: 2, name: 'LABORATORIO ENSENADA'},
                    { id: 3, name: 'LABORATORIO CHAPULTEPEC'},
                    { id: 4, name: 'LABORATORIO MONTERREY'},
                    { id: 5, name: 'LABORATORIO PUEBLA'},
                    { id: 6, name: 'LABORATORIO SAN LUIS POTOSI'},
                  ]" label="name" index="id"></v-select>
                </div>
                <h4 v-else><b>{{ client.branch.laboratory.name }}</b></h4>
            </div>
            <div class="col-sm-12">
                <p v-if="isSafilo" style="margin: 0; font-weight: bold; text-decoration: underline; color: rgb(177, 78, 79);">* Estás capturando una <b>receta SAFILO</b>, favor de incluir un <b>armazón SAFILO</b></p>
            </div>
        </div>
        <hr><br>
        <p style="text-align: left;"><b>CAPTURA DE DATOS</b></p>
        <div class="form-group">
						
						<div class="col-sm-3" style="text-align: left;">
              <label style="font-weight:300">RX:</label>
              <input v-model="$parent.sale.cart[index_prod].rx_data.rx_rx" disabled class="form-control" id="rx">
						</div>

            
						<div class="col-sm-3" style="text-align: left;">
              <label style="font-weight:300">FECHA:</label>
              <input v-model="$parent.sale.cart[index_prod].rx_data.rx_fecha" class="form-control" id="fecha" type="date" disabled>
						</div>

            
						<div class="col-sm-6" style="text-align: left;">
              <label style="font-weight:300">CLIENTE:</label>
              <input v-model="$parent.sale.cart[index_prod].rx_data.rx_cliente" disabled class="form-control" id="fecha">
						</div>
				</div>

        <!-- <input-form name="rx" text="RX" :data.sync="$parent.sale.cart[index_prod].rx_data.rx_rx" disabled></input-form>
        <input-form name="fecha" text="Fecha" :data.sync="$parent.sale.cart[index_prod].rx_data.rx_fecha" type="date"></input-form>
        <input-form name="cliente" text="Cliente" :data.sync="$parent.sale.cart[index_prod].rx_data.rx_cliente" disabled></input-form> -->
        <br>
        <p style="text-align: left;"><b>GRADUACION</b></p>
        
        <div class="form-group">
						<div class="col-sm-2" style="text-align: left;">
              <label style="font-weight:300">OD ESFERA</label>
              <input v-model="$parent.sale.cart[index_prod].rx_data.rx_od_esfera" class="form-control" id="od_esfera" @change="changeRXdata(index_prod)">
						</div>
            <div class="col-sm-2" style="text-align: left;">
              <label style="font-weight:300">OD CILINDRO</label>
              <input v-model="$parent.sale.cart[index_prod].rx_data.rx_od_cilindro" class="form-control" id="od_cilindro" @change="changeRXdata(index_prod)">
						</div>
            <div class="col-sm-2" style="text-align: left;">
              <label style="font-weight:300">OD EJE</label>
              <input v-model="$parent.sale.cart[index_prod].rx_data.rx_od_eje" class="form-control" id="od_eje" @change="changeRXdata(index_prod)">
						</div>
            <div class="col-sm-2" style="text-align: left;">
              <label style="font-weight:300">OD ADICION</label>
              <input v-model="$parent.sale.cart[index_prod].rx_data.rx_od_adicion" class="form-control" id="od_adicion" @change="changeRXdata(index_prod)">
						</div>
            <div class="col-sm-2" style="text-align: left;">
              <label style="font-weight:300">OD DIP</label>
              <input v-model="$parent.sale.cart[index_prod].rx_data.rx_od_dip" class="form-control" id="od_dip" @change="changeRXdata(index_prod)">
						</div>
            <div class="col-sm-2" style="text-align: left;">
              <label style="font-weight:300">OD ALTURA</label>
              <input v-model="$parent.sale.cart[index_prod].rx_data.rx_od_altura" class="form-control" id="od_altura" @change="changeRXdata(index_prod)">
						</div>
        </div>

        <div class="form-group">
						<div class="col-sm-2" style="text-align: left;">
              <label style="font-weight:300">OI ESFERA</label>
              <input v-model="$parent.sale.cart[index_prod].rx_data.rx_od_esfera_dos" class="form-control" id="od_esfera_dos" @change="changeRXdata(index_prod)">
						</div>
            <div class="col-sm-2" style="text-align: left;">
              <label style="font-weight:300">OI CILINDRO</label>
              <input v-model="$parent.sale.cart[index_prod].rx_data.rx_od_cilindro_dos" class="form-control" id="od_cilindro_dos" @change="changeRXdata(index_prod)">
						</div>
            <div class="col-sm-2" style="text-align: left;">
              <label style="font-weight:300">OI EJE</label>
              <input v-model="$parent.sale.cart[index_prod].rx_data.rx_od_eje_dos" class="form-control" id="od_eje_dos" @change="changeRXdata(index_prod)">
						</div>
            <div class="col-sm-2" style="text-align: left;">
              <label style="font-weight:300">OI ADICION</label>
              <input v-model="$parent.sale.cart[index_prod].rx_data.rx_od_adicion_dos" class="form-control" id="od_adicion_dos" @change="changeRXdata(index_prod)">
						</div>
            <div class="col-sm-2" style="text-align: left;">
              <label style="font-weight:300">OI DIP</label>
              <input v-model="$parent.sale.cart[index_prod].rx_data.rx_od_dip_dos" class="form-control" id="od_dip_dos" @change="changeRXdata(index_prod)">
						</div>
            <div class="col-sm-2" style="text-align: left;">
              <label style="font-weight:300">OI ALTURA</label>
              <input v-model="$parent.sale.cart[index_prod].rx_data.rx_od_altura_dos" class="form-control" id="od_altura_dos" @change="changeRXdata(index_prod)">
						</div>
        </div>

        <!-- <input-form name="od_esfera" text="OD Esfera" :data.sync="$parent.sale.cart[index_prod].rx_data.rx_od_esfera" ></input-form>
        <input-form name="od_cilindro" text="OD Clilindro" :data.sync="$parent.sale.cart[index_prod].rx_data.rx_od_cilindro" ></input-form>
        <input-form name="od_eje" text="OD Eje" :data.sync="$parent.sale.cart[index_prod].rx_data.rx_od_eje" ></input-form>
        <input-form name="od_adicion" text="OD Adición" :data.sync="$parent.sale.cart[index_prod].rx_data.rx_od_adicion" ></input-form>
        <input-form name="od_dip" text="OD Dip" :data.sync="$parent.sale.cart[index_prod].rx_data.rx_od_dip" ></input-form>
        <input-form name="od_altura" text="OD Altura" :data.sync="$parent.sale.cart[index_prod].rx_data.rx_od_altura" ></input-form>
        <hr>
        <input-form name="od_esfera" text="OD Esfera" :data.sync="$parent.sale.cart[index_prod].rx_data.rx_od_esfera_dos" ></input-form>
        <input-form name="od_cilindro" text="OD Clilindro" :data.sync="$parent.sale.cart[index_prod].rx_data.rx_od_cilindro_dos" ></input-form>
        <input-form name="od_eje" text="OD Eje" :data.sync="$parent.sale.cart[index_prod].rx_data.rx_od_eje_dos" ></input-form>
        <input-form name="od_adicion" text="OD Adición" :data.sync="$parent.sale.cart[index_prod].rx_data.rx_od_adicion_dos" ></input-form>
        <input-form name="od_dip" text="OD Dip" :data.sync="$parent.sale.cart[index_prod].rx_data.rx_od_dip_dos" ></input-form>
        <input-form name="od_altura" text="OD Altura" :data.sync="$parent.sale.cart[index_prod].rx_data.rx_od_altura_dos" ></input-form>
       -->
        <!-- <div class="form-group">
						<label class="col-sm-3 control-label">Diseño:</label>
						<div class="col-sm-7">
								<v-select v-model="$parent.sale.cart[index_prod].rx_data.rx_diseno" :options="disenoOpcs" label="label" index="value"/>
						</div>
				</div> 
        <input-form name="rx_diseno" text="Diseño" :data.sync="$parent.sale.cart[index_prod].rx_data.rx_diseno" disabled></input-form>
        -->

        <!-- <div class="form-group">
						<label class="col-sm-3 control-label">Material:</label>
						<div class="col-sm-7">
								<v-select v-model="$parent.sale.cart[index_prod].rx_data.rx_material" :options="materialOpcs" label="label" index="value"/>
						</div>
				</div> -
        <input-form name="rx_material" text="Material" :data.sync="$parent.sale.cart[index_prod].rx_data.rx_material" disabled></input-form>
      -->
        <div class="form-group">
						<div class="col-sm-6" style="text-align: left;">
              <label style="font-weight:300">DISEÑO:</label>
              <input v-model="$parent.sale.cart[index_prod].rx_data.rx_diseno" disabled class="form-control" id="rx_diseno">
						</div>
            <div class="col-sm-6" style="text-align: left;">
              <label style="font-weight:300">MATERIAL:</label>
              <input v-model="$parent.sale.cart[index_prod].rx_data.rx_material" disabled class="form-control" id="rx_material">
						</div>
        </div>

        <div class="form-group">
						<div class="col-sm-6" style="text-align: left;">
              <label style="font-weight:300">TIPO AR:</label>
              <v-select v-model="$parent.sale.cart[index_prod].rx_data.rx_tipo_ar" :options="tipoarOpcs" label="label" index="value" @change="changeRXdata(index_prod)" />
						</div>
            <div class="col-sm-6" style="text-align: left;">
              <label style="font-weight:300">TALLADO:</label>
              <v-select v-model="$parent.sale.cart[index_prod].rx_data.rx_tallado" :options="talladoOpcs" label="label" index="value" @change="changeRXdata(index_prod)"/>
						</div>
        </div>
        <br>
        <p style="text-align: left;"><b>SERVICIOS</b></p>
        <div class="form-group">

            <div class="col-sm-12" style="text-align: left;">
            
              <input v-model="$parent.sale.cart[index_prod].rx_data.rx_servicios" class="form-control" id="rx_servicios" @change="changeRXdata(index_prod)">
						</div>
        </div>

        <!-- <div class="form-group">
						<label class="col-sm-3 control-label">Tipo AR:</label>
						<div class="col-sm-7">
								<v-select v-model="$parent.sale.cart[index_prod].rx_data.rx_tipo_ar" :options="tipoarOpcs" label="label" index="value"/>
						</div>
				</div>
        

        <div class="form-group">
						<label class="col-sm-3 control-label">Tallado:</label>
						<div class="col-sm-7">
								<v-select v-model="$parent.sale.cart[index_prod].rx_data.rx_tallado" :options="talladoOpcs" label="label" index="value"/>
						</div>
				</div> -->

 
        <br>
        <p style="text-align: left;"><b>ARMAZÓN</b></p>
        <div class="form-group">

          <template v-if="isSafilo">
            <div class="col-sm-2" style="text-align: left;">
              <label style="font-weight:300;font-size: 11.5px;">TIPO DE ARMAZÓN:</label>
              <v-select v-model="$parent.sale.cart[index_prod].rx_data.rx_tipo_armazon" :options="tipo_armazonOpcs" label="label" index="value" @change="changeRXdata(index_prod)"/>
            </div>
            <div class="col-sm-2" style="text-align: left;">
              <label style="font-weight:300;font-size: 13px;">MARCA:</label>
              <v-select v-model="$parent.sale.cart[index_prod].rx_data.rx_marca" :options="tipo_marcaOpcs" label="label" index="value" @change="changeRXdata(index_prod)"/>
            </div>
          </template>
          <template v-else>
            <div class="col-sm-4" style="text-align: left;">
              <label style="font-weight:300;font-size: 13px;">TIPO DE ARMAZÓN:</label>
              <v-select v-model="$parent.sale.cart[index_prod].rx_data.rx_tipo_armazon" :options="tipo_armazonOpcs" label="label" index="value" @change="changeRXdata(index_prod)"/>
            </div>
          </template>
          
          <div class="col-sm-2" style="text-align: left;">
            <label style="font-weight:300;font-size: 13px;">HORIZONTAL"A"</label>
            <input v-model="$parent.sale.cart[index_prod].rx_data.rx_horizontal_a" class="form-control" id="rx_horizontal_a" @change="changeRXdata(index_prod)">
          </div>
          <div class="col-sm-2" style="text-align: left;">
            <label style="font-weight:300;font-size: 13px;">VERTICAL"B"</label>
            <input v-model="$parent.sale.cart[index_prod].rx_data.rx_vertical_b" class="form-control" id="vertical_b" @change="changeRXdata(index_prod)">
          </div>
          <div class="col-sm-2" style="text-align: left;">
            <label style="font-weight:300;font-size: 13px;">DIAGONAL"ED"</label>
            <input v-model="$parent.sale.cart[index_prod].rx_data.rx_diagonal_ed" class="form-control" id="diagonal_ed" @change="changeRXdata(index_prod)">
          </div>
          <div class="col-sm-2" style="text-align: left;">
            <label style="font-weight:300;font-size: 13px;">PUENTE</label>
            <input v-model="$parent.sale.cart[index_prod].rx_data.rx_puente" class="form-control" id="rx_puente" @change="changeRXdata(index_prod)">
          </div>
        </div>

        <!-- <div class="form-group">
						<label class="col-sm-3 control-label">Tipo de armazón:</label>
						<div class="col-sm-7">
								<v-select v-model="$parent.sale.cart[index_prod].rx_data.rx_tipo_armazon" :options="tipo_armazonOpcs" label="label" index="value"/>
						</div>
				</div>


        <input-form text='Horizontal "A"' name="horizontal_a" :data.sync="$parent.sale.cart[index_prod].rx_data.rx_horizontal_a" ></input-form>
        <input-form text='Vertical "B"' name="vertical_b" :data.sync="$parent.sale.cart[index_prod].rx_data.rx_vertical_b" ></input-form>
        <input-form text='Diagonal "ED" ' name="diagonal_ed" :data.sync="$parent.sale.cart[index_prod].rx_data.rx_diagonal_ed" ></input-form>
        <input-form text="Puente" name="puente" :data.sync="$parent.sale.cart[index_prod].rx_data.rx_puente" ></input-form> -->
        <br>
        <p style="text-align: left;"><b>OBSERVACIONES</b></p>
        <div class="form-group">

          <div class="col-sm-12" style="text-align: left;">
            <textarea v-model="$parent.sale.cart[index_prod].rx_data.rx_observaciones" class="form-control" id="rx_rx_observaciones" @change="changeRXdata(index_prod)"></textarea>
          </div>
        </div>

        <!-- <text-form name="observaciones" text="Observaciones" :data.sync="$parent.sale.cart[index_prod].rx_data.rx_observaciones" ></text-form> -->

        <!-- <input-form name="estado_cuenta" text="Estado de cuenta" :data.sync="$parent.sale.cart[index_prod].note.estado_cuenta" ></input-form>
        <input-form name="semana" text="Semana" :data.sync="$parent.sale.cart[index_prod].note.semana" ></input-form>
        <input-form name="optica" text="Doctor/Óptica" :data.sync="$parent.sale.cart[index_prod].note.optica" ></input-form>

        <input-form name="fecha" text="Fecha" :data.sync="$parent.sale.cart[index_prod].note.fecha" type="date"></input-form>
        <input-form name="rx" text="RX´S" :data.sync="$parent.sale.cart[index_prod].note.rx" ></input-form>
        <input-form name="descripcion" text="Descripcion" :data.sync="$parent.sale.cart[index_prod].note.descripcion" ></input-form>
        <input-form name="importe" text="Importe" :data.sync="$parent.sale.cart[index_prod].note.importe" ></input-form>

        <input-form name="total" text="Total" :data.sync="$parent.sale.cart[index_prod].note.total" ></input-form>

        <hr>
        <text-form name="observaciones" text="Observaciones" :data.sync="$parent.sale.cart[index_prod].note.observaciones" ></text-form> -->
          
          <div class="form-group">
						<div class="col-sm-12">
							
              <!-- <button type="button" class="btn btn-dark pull-left" style="background-color: black;color: white;" @click="requestRx(index_prod)">Solicitar RX</button>
               
							<button type="submit" class="btn btn-success pull-right" @click="$refs.modalRx.close()"><i class="far fa-save"></i> Guardar</button> -->
							<!-- <button type="button" class="btn btn-default pull-right" @click="$refs.modalRx.close()">Cancelar</button> -->
						</div>
					</div>

      </form>
        
        
      </sweet-modal>

      <sweet-modal :icon="modal.icon" :blocking="modal.block" :hide-close-button="modal.block"  ref="modalalert">
        <div class="fa-3x" v-if="modal.icon== ''"><i class="fas fa-spinner fa-pulse"></i></div><br/>
        <div v-html="modal.msg"></div>
       
      </sweet-modal>

  </div>
</template>
<script>
export default {
  data() {
    return {
      index: 0,
      client: {
        name: "",
        emal: "",
        phone: "",
        celphone: "",
        comertial_name: "",
        // rfc:"",
        address: "",
        suburb: "",
        state_id: "",
        state: { value: "", label: "Selecciona.." },
        town_id: "",
        town: { value: "", label: "Selecciona.." },
        postal_code: "",
        discounts: [],
        branch_id: "",
        branch: { value: "", label: "Selecciona.." },
        payment_plan: "",
        facturacion: {
          name: "",
          phone: "",
          celphone: "",
          rfc: "",
          address: "",
          suburb: "",
          state_id: "",
          state: { value: "", label: "Selecciona.." },
          town_id: "",
          town: { value: "", label: "Selecciona.." },
          postal_code: "",
        },
      },
      type: {},
      states: [],
      towns: [],
      clients: [],
      types: [],
      categories: [],
      products: [],
      extras: [],
      rx: "",
      order: {},
      orderInProcess: false,


      index_prod:null,
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
      tipo_marcaOpcs: [
        {value:'Carrera',label:'Carrera'},
        {value:'Polaroid',label:'Polaroid'},
      ],
      modal:{
        icon:'',
        block:false,
        msg:null
      },
      selectedListId: null,
      hasLists: false,
      lists: [],
      isSafilo: false
    };
  },
  computed: {
    isAugenLabs: function () {
      return this.client.id == 1862;
    },
    isPaqueteria: function () {
      return this.client.id == 1901;
    },
    
  },
  watch: {
    "client.state.value": {
      handler: function (v) {
        this.client.state_id = v;
        if (v != "" && !isNaN(v)) {
          this.getTowns();
        }
      },
      deep: true,
    },
    "client.town.value": {
      handler: function (v) {
        this.client.town_id = v;
      },
      deep: true,
    },
    "$parent.sale.cart": {
      handler: function (v) {
        this.getDiscounts();
      },
      deep: false,
    },
    "client.discounts": {
      handler: function () {
        this.getDiscounts();
      },
      deep: true,
    },
    isSafilo: function(newValue, oldValue) {
        const index_prod = this.index_prod;
        if (newValue) {
            // Prepend 'SF-' if it's not already there
            this.$parent.sale.cart[index_prod].rx = this.$parent.sale.cart[index_prod].rx.startsWith('SF-') ? this.$parent.sale.cart[index_prod].rx : 'SF-' + this.$parent.sale.cart[index_prod].rx;
            this.$parent.sale.cart[index_prod].rx_data.rx_rx = this.$parent.sale.cart[index_prod].rx_data.rx_rx.startsWith('SF-') ? this.$parent.sale.cart[index_prod].rx_data.rx_rx : 'SF-' + this.$parent.sale.cart[index_prod].rx_data.rx_rx;
        } else {
            // Remove 'SF-' if it exists
            this.$parent.sale.cart[index_prod].rx = this.$parent.sale.cart[index_prod].rx.startsWith('SF-') ? this.$parent.sale.cart[index_prod].rx.substring(3) : this.$parent.sale.cart[index_prod].rx;
            this.$parent.sale.cart[index_prod].rx_data.rx_rx = this.$parent.sale.cart[index_prod].rx_data.rx_rx.startsWith('SF-') ? this.$parent.sale.cart[index_prod].rx_data.rx_rx.substring(3) : this.$parent.sale.cart[index_prod].rx_data.rx_rx;
        }
    }
  },
  methods: {
    reset: function () {
      this.cleanClient();
      this.$parent.sale = {
        cart: [],
        client_id: "",
        total: 0,
        observation: "",
        discounts: 0,
      };
    },
    onComplete: function () {
      if (!this.orderInProcess) {
        this.orderInProcess = true;
       
        alertify.confirm("¿Seguro que deseas realizar esta venta?", () => {
          this.$parent.inPetition = true;
          let params = window._.cloneDeep(this.$parent.sale);

          params.cart = params.cart.map((row) => {
            let extras = row.extras.map((r) => {
              return { extra_id: r.id, price: r.price };
            });
            return {
              product_has_subcategory_id: row.product_has_subcategory_id,
              price: row.price,
              cost: row.cost,
              discount: row.discount,
              service: row.service,
              total: row.total,
              iva: row.iva,
              rx: row.rx,
              percent_discount: row.percent_discount,
              extras: extras,
              rx_data: row.rx_data,
              laboratory_id: row.laboratory_id,
              price_list: ('price_list' in row.metadata) ? row.metadata.price_list : 'SIN DEFINIR',
              metadata: row.metadata != undefined ? row.metadata : []
            };
          });
          axios
            .post(tools.url("/api/order"), params)
            .then((response) => {
              this.$refs.form.changeTab(2, 0);
              this.reset();
              this.$parent.inPetition = false;
              this.$parent.showMessage(
                "Pedido realizado correctamente.",
                "success"
              );
              this.orderInProcess = false;
            })
            .catch((err) => {
              this.$parent.inPetition = false;
              this.$parent.handleErrors(err);
            });
        });
      }
      else{
        console.log('next');
      }
    },
    getStates: function () {
      this.$parent.inPetition = true;
      axios
        .get(tools.url("/api/states"))
        .then((response) => {
          this.states = response.data;
          this.states = jQuery.map(this.states, (row) => {
            return { value: row.id, label: row.name };
          });
          this.$parent.inPetition = false;
        })
        .catch((error) => {
          this.$parent.handleErrors(error);
          this.$parent.inPetition = false;
        });
    },
    getTowns: function () {
      this.$parent.inPetition = true;
      axios
        .get(tools.url("/api/towns/" + this.client.state_id))
        .then((response) => {
          this.towns = response.data;
          this.towns = jQuery.map(this.towns, (row) => {
            return { value: row.id, label: row.name };
          });
          this.$parent.inPetition = false;
        })
        .catch((error) => {
          this.$parent.handleErrors(error);
          this.$parent.inPetition = false;
        });
    },
    getTypes: function () {
      this.$parent.inPetition = true;
      axios
        .get(tools.url("/api/types") + ('id' in this.client ? '?client_id=' + this.client.id : '') + (this.selectedListId != null ? '&list_id=' + this.selectedListId : ''))
        .then((response) => {
          let types = response.data;
          const CHAPULTEPEC_LAB = 46;
          let isPDV = false;
          for (let role of this.$parent.user.roles)
            if (role.name == "punto de ventas") {
              isPDV = true;
              break;
            }

          for (let i = 0; i < types.length; i++) {
            if (!isPDV) {
              types[i].canSee = true;
              continue;
            }
            if ([25, 34, 46].includes(this.$parent.user.id)) {
              types[i].canSee = true;
            } else {
              if (types[i].id == 10) types[i].canSee = false;
              else types[i].canSee = true;
            }
          }

          this.types = types;
          this.$parent.inPetition = false;
        })
        .catch((error) => {
          this.$parent.handleErrors(error);
          this.$parent.inPetition = false;
        });
    },
    getSubcategories: function (v) {
      this.categories = [];
      this.$parent.inPetition = true;
      axios
        .get(tools.url("/api/subcategories/type/" + v))
        .then((response) => {
          let subcategories = response.data;
          let categories = {};

          subcategories.forEach((sub, key) => {
            sub.categories.forEach((cat, k) => {
              if (categories[cat.name]) {
                categories[cat.name].subcategories.push(sub);
              } else {
                categories[cat.name] = cat;

                categories[cat.name].subcategories = [sub];
              }
            });
          });

          jQuery.each(categories, (k, v) => {
            v.subcategories.sort((a, b) => {
              return a.id - b.id;
            });
            this.categories.push(v);
          });

          this.categories.sort((a, b) => {
            return a.id - b.id;
          });

          this.$parent.inPetition = false;
        })
        .catch((error) => {
          this.$parent.handleErrors(error);
          this.$parent.inPetition = false;
        });
    },
    getProducts: function (type_id) {
      axios.get(tools.url("/api/product/type/" + type_id)).then((response) => {
        this.products = response.data;
        this.products.forEach((v, k) => {
          this.products[k].prices.sort((a, b) => {
            return a.id - b.id;
          });
        });
      });
    },
    searchClient: function () {
      alertify
        .prompt(
          "Augen",
          "Escribe algun nombre de cliente.",
          "",
          (evt, value) => {
            axios
              .get(tools.url("/api/client/search/" + value))
              .then((response) => {
                let clients = response.data;
                this.clients = clients;
                alertify.clientsDialog(
                  document.getElementById("clients_table")
                );
              });
          },
          function () {}
        )
        .set("labels", { ok: "Aceptar", cancel: "Cancelar" });
    },
    selectClient: function (client_id) {
      const client = this.clients.filter(client => client.id == client_id).shift();
      this.hasLists = client.lists.length > 0; 
      this.selectedListId = null;

      console.log('hasList', this.hasLists);
      if(this.hasLists) {
			  this.getLists();
      }

      if(this.hasLists == false) {
        alert('Si ves esta alerta, reportalo por favor a sistemas, esto es un error');
        alertify.closeAll();
        return false;
      }

      
      if(client.status == "Inactivo") {
        alert('Esta acción no está permitida para clientes bloqueados');
        alertify.closeAll();
        return false;

      }
      this.$parent.sale.client_id = client_id;
      this.getClient();
      alertify.clientsDialog().close();
    },
    selectType: async function () {
      // COVID-19 Augen Mask patch
      if (this.isAugenLabs) {
        // Augen Labs Client
        this.rx = (await axios.get("/api/generateRx")).data;

        for (let i = 0; i < this.types.length; i++) {
          this.types[i].canSee = this.types[i].id == 12;
        }
        alertify.typesDialog(document.getElementById("types_table"));
      } else if (this.isPaqueteria) {
        this.rx = (await axios.get("/api/generateRx?paqueteria")).data;

        for (let i = 0; i < this.types.length; i++) {
          this.types[i].canSee = this.types[i].id == 7;
        }
        alertify.typesDialog(document.getElementById("types_table"));
      } else {
        this.getTypes();
        alertify
          .prompt(
            "Augen",
            "Escribe el RX.",
            "",
            async (evt, value) => {
              this.rx = value;
              this.$parent.inPetition = true;
              let alreadyExist = (
                await axios.get(`/api/orders/check_duplicates/${value}`)
              ).data;
              this.$parent.inPetition = false;

              console.log(alreadyExist);

              if (alreadyExist) {
                alertify
                  .confirm(
                    "Augen",
                    "Ya hay una receta ingresada con ese folio, ¿quieres continuar?",
                    () => {
                      alertify.typesDialog(
                        document.getElementById("types_table")
                      );
                    },
                    () => {
                      this.rx = null;
                    }
                  )
                  .set("labels", { ok: "Sí", cancel: "No" });
              } else {
                alertify.typesDialog(document.getElementById("types_table"));
              }
            },
            function () {}
          )
          .set("labels", { ok: "Aceptar", cancel: "Cancelar" });
      }
    },
    selectProduct: function (type) {

      if('isNew' in type && type.isNew) {
        this.type = type;
        alertify.productsDialog(document.getElementById("new_products_table"));
      } else {
        this.getSubcategories(type.id);
        this.getProducts(type.id);
        this.type = type;
        alertify.productsDialog(document.getElementById("products_table"));
      }
    },
    getDiscounts: function (event) {
      // if('target' in event) {
      //     let value = parseFloat(event.target.value);
      //     if(value < 0) {
      //
      //     }
      // }
      let total = 0;
      let discounts = 0;
      let ivas = 0;

      if (this.isAugenLabs) {
        // In this case v.service will be used as qty option.
        this.$parent.sale.cart.forEach((v, k) => {
          this.$parent.sale.cart[k].percent_discount = 0;
          this.$parent.sale.cart[k].total = (
            window.parseFloat(v.price) * window.parseFloat(v.service)
          ).toFixed(2);
          this.$parent.sale.cart[k].discount = 0;
          this.$parent.sale.cart[k].iva = window.parseFloat(
            this.$parent.sale.cart[k].total * 0.16
          );
        });
      } else {
        this.$parent.sale.cart.forEach((v, k) => {
          const isDiscountByList = 'list_id' in v.product;
          var discount = null;

          if(isDiscountByList) {
            // new method for discount
            const listData = this.client.lists.find(l => l.value == v.product.list_id);
            const listName = listName.name;

            this.$parent.sale.cart[k].metadata.price_list = listName;

            if(listData.discount > 0) {
              discount = {
                discount: listData.discount
              };
            }
            
          } else { 
            discount = this.client.discounts.find((row) => {
              return row.type_id == v.product.type_id;
            });
          }

          if (discount) {
            this.$parent.sale.cart[k].percent_discount = discount.discount;
            this.$parent.sale.cart[k].total = (
              window.parseFloat(v.price) + window.parseFloat(v.service)
            ).toFixed(2);
            this.$parent.sale.cart[k].discount = window.parseFloat(
              (discount.discount / 100) * this.$parent.sale.cart[k].total
            );
            this.$parent.sale.cart[k].iva = window.parseFloat(
              (this.$parent.sale.cart[k].total -
                this.$parent.sale.cart[k].discount) *
                0.16
            );
          } else {
            this.$parent.sale.cart[k].percent_discount = 0;
            this.$parent.sale.cart[k].total = (
              window.parseFloat(v.price) + window.parseFloat(v.service)
            ).toFixed(2);
            this.$parent.sale.cart[k].discount = window.parseFloat(
              (0 / 100) * this.$parent.sale.cart[k].total
            );
            this.$parent.sale.cart[k].iva = window.parseFloat(
              (this.$parent.sale.cart[k].total -
                this.$parent.sale.cart[k].discount) *
                0.16
            );
          }
        });
      }

      this.$parent.sale.cart.forEach((v, k) => {
        total += window.parseFloat(v.total);
        discounts += window.parseFloat(v.discount);
        ivas += window.parseFloat(v.iva);
      });

      this.$parent.sale.total = total.toFixed(2);
      this.$parent.sale.discounts = discounts.toFixed(2);
      this.$parent.sale.ivas = ivas.toFixed(2);
    },
    addProduct: function (price, category, subcategory) {
      var no_AR = 0;
      var ar = 0;
      let product = this.products.find((r) => {
        if (r.id == price.product_id) return true;
      });
      product.category_name = category.name;
      product.type_name = subcategory.name;
      let order = {
        product_has_subcategory_id: price.id,
        price: price.price,
        discount: 0,
        total: price.price,
        service: 0,
        extras: [],
        product: product,
        rx: this.rx,
        not_ar: 0,
        rx_data:{
          rx_rx:null,
          rx_fecha:null,
          rx_cliente:null,

          rx_od_esfera:null,
          rx_od_cilindro:null,
          rx_od_eje:null,
          rx_od_adicion:null,
          rx_od_dip:null,
          rx_od_altura:null,
          rx_od_esfera:null,

          rx_od_esfera_dos:null,
          rx_od_cilindro_dos:null,
          rx_od_eje_dos:null,
          rx_od_adicion_dos:null,
          rx_od_dip_dos:null,
          rx_od_altura_dos:null,
          rx_od_esfera_dos:null,
          
          rx_diseno:null,
          rx_material:null,
          rx_tipo_ar:null,
          rx_tallado:null,
          rx_tipo_armazon:null,
          rx_horizontal_a:null,
          rx_vertical_b:null,
          rx_diagonal_ed:null,
          rx_puente:null,
          rx_observaciones:null,
          rx_servicios:null,
          rx_marca:null,
          have_data:false
        }
      };

      if (this.isAugenLabs) order.service = 1;

      product.subcategories.forEach((subcategory) => {
        if (subcategory.id == price.subcategory_id) {
          no_AR = subcategory.no_antireflective;
          ar = subcategory.antireflective;
        }
      });

      order.not_ar = no_AR;
      order.ar = ar;
      if(ar == 1){
          order.rx_data.rx_tipo_ar = 'Matiz-e';
      }
      if (product.extras.length == 0 || (no_AR == 1 && ar == 1)) {
        alertify.closeAll();
        this.$parent.sale.cart.push(order);
        this.getDiscounts();
        this.rx = "";
      } else if (product.extras.length != 0 && ar == 1) {
        this.order = order;
        this.extras = product.extras.filter((row) => {
          return row.free_form == 1;
        });
        alertify.extrasDialog(document.getElementById("extras_table"));
      } else if (product.extras.length != 0 && no_AR == 1) {
        this.order = order;
        this.extras = product.extras.filter((row) => {
          return row.free_form == 1;
        });
        alertify.extrasDialog(document.getElementById("extras_table"));
      } else {
        this.order = order;
        this.extras = product.extras;
        alertify.extrasDialog(document.getElementById("extras_table"));
      }

     
    },
    addExtra: function (extra) {
      var flag = 1;

      if (extra) {
        this.order.extras.forEach((tempExtra) => {
          if (extra.id == tempExtra.id) {
            flag = 0;
          }
          if (extra.free_form == tempExtra.free_form) {
            flag = 0;
          }
        });
        if (flag == 1) {
          this.order.extras.push(extra);
          this.order.price =
            window.parseFloat(this.order.price) +
            window.parseFloat(extra.price);

          this.order.price = this.order.price.toFixed(2);
        } else {
          this.$parent.showMessage(
            "No se puede agregar otro extra del mismo tipo.",
            "warning"
          );
        }
      }
      this.$parent.sale.cart.push(this.order);
      this.order = {};
      alertify.closeAll();
    },
    selectOrder: function (cart) {
      let product = cart.product;
      let no_AR = cart.not_ar;
      let ar = cart.ar;
      let indexProduct = this.$parent.sale.cart.findIndex((row) => {
        if (row.id == cart.id)
          return true;
      });
      let order = this.$parent.sale.cart[indexProduct];
      this.$parent.sale.cart.splice(indexProduct, 1);

      console.log(indexProduct, order, product);

      this.order = order;
      this.extras = product.extras;
      alertify.extrasDialog(document.getElementById("extras_table"));

      return;

      if (product.extras.length == 0 || (no_AR == 1 && ar == 1)) {
        alertify.closeAll();
        this.$parent.sale.cart.push(order);
        this.getDiscounts();
        this.rx = "";
      } else if (product.extras.length != 0 && ar == 1) {
        this.order = order;
        this.extras = product.extras.filter((row) => {
          return row.free_form == 1;
        });
        alertify.extrasDialog(document.getElementById("extras_table"));
      } else if (product.extras.length != 0 && no_AR == 1) {
        this.order = order;
        this.extras = product.extras.filter((row) => {
          return row.free_form == 1;
        });
        alertify.extrasDialog(document.getElementById("extras_table"));
      } else {
        this.order = order;
        this.extras = product.extras;
        alertify.extrasDialog(document.getElementById("extras_table"));
      }
      // if(cart.product.extras.length==0){
      //     this.$parent.showMessage("No puedes agregar antireflejante a este diseño.");
      // }
      // else{
      //     let indexProduct = this.$parent.sale.cart.findIndex(row=>{
      //         if(row.product_has_subcategory_id==cart.product_has_subcategory_id)
      //             return true;
      //     });
      //     this.order = this.$parent.sale.cart[indexProduct];
      //     this.$parent.sale.cart.splice(indexProduct,1);
      //     this.extras=cart.product.extras;
      //     alertify.extrasDialog(document.getElementById('extras_table'));
      // }
    },
    deleteProduct: function (index) {
      this.$parent.sale.cart.splice(index, 1);
    },
    saveClient: function () {
      //this.$parent.inPetition=true;
      this.$parent.validateAll(
        () => {
          var data = this.client;
          if (this.$parent.sale.client_id != "") {
            // axios.post(tools.url("/api/client/"+this.client.id),data)
            // .then((response)=>{
            //     this.$parent.sale.client_id=response.data.id;
            //     this.$parent.inPetition=false;
            //     this.$refs.form.changeTab(0,1);
            // }).catch((error)=>{
            //     this.$parent.handleErrors(error);
            //     this.$parent.inPetition=false;
            // });
            this.$refs.form.changeTab(0, 1);
            return true;
          } else {
            // axios.post(tools.url("/api/client"),data)
            // .then((response)=>{
            //     this.$parent.sale.client_id=response.data.id;
            //     this.$parent.inPetition=false;
            //     this.$refs.form.changeTab(0,1);
            // }).catch((error)=>{
            //     this.$parent.handleErrors(error);
            //     this.$parent.inPetition=false;
            // });
            this.$parent.showMessage("Primero busca un cliente.");
            return false;
          }
        },
        (e) => {
          console.log(e);
          this.$parent.inPetition = false;
          return false;
        },
        this.$refs.client
      );
    },
    getClient() {
      this.$parent.inPetition = true;

      axios
        .get(tools.url("/api/client/" + this.$parent.sale.client_id))
        .then((response) => {
          this.client = response.data;

          this.client.state = {
            value: this.client.state.id,
            label: this.client.state.name,
          };
          this.client.town = {
            value: this.client.town.id,
            label: this.client.town.name,
          };
          if (this.client.facturacion.state != null) {
            this.client.facturacion.state = {
              value: this.client.facturacion.state.id,
              label: this.client.facturacion.state.name,
            };
          }
          if (this.client.facturacion.town != null) {
            this.client.facturacion.town = {
              value: this.client.facturacion.town.id,
              label: this.client.facturacion.town.name,
            };
          }
          this.$parent.inPetition = false;
        })
        .catch((error) => {
          this.$parent.handleErrors(error);
          this.$parent.inPetition = false;
        });
    },
    cleanClient: function () {
      this.client = {
        name: "",
        emal: "",
        phone: "",
        celphone: "",
        comertial_name: "",
        address: "",
        suburb: "",
        state_id: "",
        state: { value: "", label: "Selecciona.." },
        town_id: "",
        town: { value: "", label: "Selecciona.." },
        postal_code: "",
        discounts: [],
        branch_id: "",
        branch: { value: "", label: "Selecciona.." },
        payment_plan: "",
        facturacion: {
          name: "",
          phone: "",
          celphone: "",
          rfc: "",
          address: "",
          suburb: "",
          state_id: "",
          state: { value: "", label: "Selecciona.." },
          town_id: "",
          town: { value: "", label: "Selecciona.." },
          postal_code: "",
        },
      };
      this.$parent.sale.client_id = "";
    },
    filterProduct: function (product, category_id, subcategory_id) {
      let data = product.prices.find((r) => {
        if (
          r.category_id == category_id &&
          r.subcategory_id == subcategory_id
        ) {
          return true;
        }
      });

      if (data) return data;
      else return { price: 0 };
    },
    openModalRx(indx){
        this.index_prod = indx;

        var fechaActual = new Date();
          var dia = fechaActual.getDate();
          var mes = fechaActual.getMonth() + 1; // Los meses comienzan desde 0, por lo que sumamos 1
          var año = fechaActual.getFullYear();

          // Asegurarse de que el día y el mes tienen siempre dos dígitos
          if (dia < 10) {
            dia = '0' + dia;
          }
          if (mes < 10) {
            mes = '0' + mes;
          }

          var fechaFormateada = año + '-' + mes + '-' + dia;

        this.$parent.sale.cart[indx].rx_data.rx_rx = this.$parent.sale.cart[indx].rx;
        this.$parent.sale.cart[indx].rx_data.rx_fecha = fechaFormateada;
        this.$parent.sale.cart[indx].rx_data.rx_cliente = this.client.name;
        this.$parent.sale.cart[indx].rx_data.rx_diseno = this.$parent.sale.cart[indx].product.name;
        this.$parent.sale.cart[indx].rx_data.rx_material = this.$parent.sale.cart[indx].product.category_name;
        this.$parent.sale.cart[indx].rx_data.rx_caracteristicas = this.$parent.sale.cart[indx].product.type_name;

        
        if([1, 2, 3, 4, 5, 6].includes(this.client.branch.laboratory_id)) {
          this.$parent.sale.cart[indx].laboratory_id = this.client.branch.laboratory_id;
        }
        
        this.$refs.modalRx.open();
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
    requestRx(indx){
        
        this.$parent.inPetition=true;
        var dataform = {
          'rx':this.$parent.sale.cart[indx].rx_data,
          'user':this.client
        };
        axios.post(tools.url("/api/orders/requestrx"),dataform).then((response)=>{
               
            this.$parent.inPetition=false;
            this.modal.block = false;
            this.modal.icon = 'success';
            this.modal.msg = 'RX eviada correctamente...';
            this.$refs.modalalert.open();
        }).catch((error)=>{
            this.$parent.handleErrors(error);
            this.$parent.inPetition=false;
            this.modal.block = false;
            this.modal.icon = 'error';
            this.modal.msg = error.data.msg;
            this.$refs.modalalert.open();
           
        });
    },
    changeRXdata(indx){


      // for (let x = 0; x < this.$parent.sale.cart.length; x++) {
          var checkform = false;
          if (this.$parent.sale.cart[indx]['rx_data']['rx_diagonal_ed'] != null && this.$parent.sale.cart[indx]['rx_data']['rx_diagonal_ed'] != '') {checkform = true;}
          if (this.$parent.sale.cart[indx]['rx_data']['rx_horizontal_a'] != null && this.$parent.sale.cart[indx]['rx_data']['rx_horizontal_a'] != '') {checkform = true;}
          if (this.$parent.sale.cart[indx]['rx_data']['rx_observaciones'] != null && this.$parent.sale.cart[indx]['rx_data']['rx_observaciones']  != '') {checkform = true;}
          if (this.$parent.sale.cart[indx]['rx_data']['rx_od_adicion'] != null && this.$parent.sale.cart[indx]['rx_data']['rx_od_adicion'] != '') {checkform = true;}
          if (this.$parent.sale.cart[indx]['rx_data']['rx_od_adicion_dos'] != null && this.$parent.sale.cart[indx]['rx_data']['rx_od_adicion_dos'] != '') {checkform = true;}
          if (this.$parent.sale.cart[indx]['rx_data']['rx_od_altura'] != null && this.$parent.sale.cart[indx]['rx_data']['rx_od_altura'] != '') {checkform = true;}
          if (this.$parent.sale.cart[indx]['rx_data']['rx_od_altura_dos'] != null && this.$parent.sale.cart[indx]['rx_data']['rx_od_altura_dos'] != '') {checkform = true;}
          if (this.$parent.sale.cart[indx]['rx_data']['rx_od_cilindro'] != null && this.$parent.sale.cart[indx]['rx_data']['rx_od_cilindro'] != '') {checkform = true;}
          if (this.$parent.sale.cart[indx]['rx_data']['rx_od_cilindro_dos'] != null &&  this.$parent.sale.cart[indx]['rx_data']['rx_od_cilindro_dos'] != '') {checkform = true;}
          if (this.$parent.sale.cart[indx]['rx_data']['rx_od_dip'] != null && this.$parent.sale.cart[indx]['rx_data']['rx_od_dip'] != '') {checkform = true;}
          if (this.$parent.sale.cart[indx]['rx_data']['rx_od_dip_dos'] != null && this.$parent.sale.cart[indx]['rx_data']['rx_od_dip_dos'] != '') {checkform = true;}
          if (this.$parent.sale.cart[indx]['rx_data']['rx_od_eje'] != null && this.$parent.sale.cart[indx]['rx_data']['rx_od_eje'] != '') {checkform = true;}
          if (this.$parent.sale.cart[indx]['rx_data']['rx_od_eje_dos'] != null && this.$parent.sale.cart[indx]['rx_data']['rx_od_eje_dos'] != '') {checkform = true;}
          if (this.$parent.sale.cart[indx]['rx_data']['rx_od_esfera'] != null && this.$parent.sale.cart[indx]['rx_data']['rx_od_esfera'] != '') {checkform = true;}
          if (this.$parent.sale.cart[indx]['rx_data']['rx_od_esfera_dos'] != null && this.$parent.sale.cart[indx]['rx_data']['rx_od_esfera_dos'] != '') {checkform = true;}
          if (this.$parent.sale.cart[indx]['rx_data']['rx_puente'] != null && this.$parent.sale.cart[indx]['rx_data']['rx_puente'] != '') {checkform = true;}
          if (this.$parent.sale.cart[indx]['rx_data']['rx_servicios'] != null && this.$parent.sale.cart[indx]['rx_data']['rx_servicios'] != '') {checkform = true;}
          if (this.$parent.sale.cart[indx]['rx_data']['rx_tallado'] != null && this.$parent.sale.cart[indx]['rx_data']['rx_tallado'] != '') {checkform = true;}
          if (this.$parent.sale.cart[indx]['rx_data']['rx_tipo_ar'] != null && this.$parent.sale.cart[indx]['rx_data']['rx_tipo_ar'] != '') {checkform = true;}
          if (this.$parent.sale.cart[indx]['rx_data']['rx_tipo_armazon'] != null && this.$parent.sale.cart[indx]['rx_data']['rx_tipo_armazon'] != '') {checkform = true;}
          if (this.$parent.sale.cart[indx]['rx_data']['rx_vertical_b'] != null && this.$parent.sale.cart[indx]['rx_data']['rx_vertical_b'] != '') {checkform = true;}
          this.$parent.sale.cart[indx]['rx_data']['have_data'] = checkform;
        // }
    },
    getLists() {
      // this.$parent.inPetition = true;
      axios.get('https://apiv2.augenlabs.com/v1/lists').then(result => {
        console.log(result);
        this.lists = result.data;
        console.log('lists loaded', this.lists)
      });
    },
    canViewList(list_id) {
      const availableLists = this.clients.list.filter(l => l.active);
      const listsIds = availableLists.map(l => l.value);
      return listsIds.includes(list_id);
    },
    _addProduct(design, material, characteristic, price, cost) {
        var order = {
            id: Date.now(),
            product_has_subcategory_id: null,
            price: price,
            cost: cost,
            discount: 0,
            total: price,
            service: 0,
            extras: [],
            product: {
                name: design.name,
                category_name: material.name,
                type_name: characteristic.name,
                list_id: design.pivot.list_id, // list_id for map later if there's any discount.
                extras: design.extras, // extras if needed. 
            },
            rx: this.rx,
            not_ar: 0,
            rx_data: {
                rx_rx:null,
                rx_fecha:null,
                rx_cliente:null,
                rx_od_esfera:null,
                rx_od_cilindro:null,
                rx_od_eje:null,
                rx_od_adicion:null,
                rx_od_dip:null,
                rx_od_altura:null,
                rx_od_esfera:null,

                rx_od_esfera_dos:null,
                rx_od_cilindro_dos:null,
                rx_od_eje_dos:null,
                rx_od_adicion_dos:null,
                rx_od_dip_dos:null,
                rx_od_altura_dos:null,
                rx_od_esfera_dos:null,
                
                rx_diseno:null,
                rx_material:null,
                rx_tipo_ar:null,
                rx_tallado:null,
                rx_tipo_armazon:null,
                rx_horizontal_a:null,
                rx_vertical_b:null,
                rx_diagonal_ed:null,
                rx_puente:null,
                rx_observaciones:null,
                rx_servicios:null,
                have_data:false
            },
            metadata: {
                diseno: design.name,
                material: material.name,
                characteristic: characteristic.name,
                list_id: design.pivot.list_id
            }
        };

        order.not_ar = 1;
        order.ar = 1;

        alertify.closeAll();
        this.$parent.sale.cart.push(order);
        this.getDiscounts();
        this.rx = "";
    }
  },
  mounted() {
    this.getStates();
    this.getTypes();
    this.selectedListId = null;
    if (this.$parent.sale.client_id != "") this.getClient();

    alertify.clientsDialog ||
      alertify.dialog("clientsDialog", function () {
        return {
          main: function (content) {
            this.setContent(content);
          },
          setup: function () {
            return {
              options: {
                basic: true,
                maximizable: true,
                resizable: false,
              },
            };
          },
          settings: {
            selector: undefined,
          },
        };
      });

    alertify.typesDialog ||
      alertify.dialog("typesDialog", function () {
        return {
          main: function (content) {
            this.setContent(content);
          },
          setup: function () {
            return {
              options: {
                basic: true,
                maximizable: true,
                resizable: false,
              },
            };
          },
          settings: {
            selector: undefined,
          },
        };
      });

    alertify.productsDialog ||
      alertify.dialog("productsDialog", function () {
        return {
          main: function (content) {
            this.setContent(content);
            this.resizeTo("80%", "70%");
          },
          setup: function () {
            return {
              options: {
                basic: true,
                maximizable: true,
                resizable: true,
              },
            };
          },
          settings: {
            selector: undefined,
          },
        };
      });

    alertify.extrasDialog ||
      alertify.dialog("extrasDialog", function () {
        return {
          main: function (content) {
            this.setContent(content);
          },
          setup: function () {
            return {
              options: {
                basic: true,
                maximizable: false,
                resizable: false,
                closable: false,
              },
            };
          },
          settings: {
            selector: undefined,
          },
        };
      });
  },
};
</script>
<style media="screen" lang="scss" scoped>
div#types_table button {
  margin-top: 35px;
}


.is-safilo {
    b {
        color: rgb(177, 78, 79) !important;
    }
}

</style>
