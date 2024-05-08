<template>
    <div class="container table-responsive">
        <h1>Lista de precios</h1>
        <br/>
        <div class="pull-right" style="margin-bottom: 15px;">
            <button class="btn btn-warning" @click="$router.push('/lista-de-precios/')"><i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;Regresar a listado</button>
        </div>
        <div v-if="priceList != null">
          <div class="form-group">
            <label for="">Nombre de la lista: </label>
            <input type="text" class="form-control" disabled v-model="priceList.name">
          </div>
        </div>
        <button class="btn btn-info" @click="addDesignSlot()" v-if="isEditing">Añadir diseño</button>
        <button class="btn btn-primary" @click="togglePreciosOrCostos = !togglePreciosOrCostos; reload();" v-if="!togglePreciosOrCostos">Ver costos</button>
        <button class="btn btn-primary" @click="togglePreciosOrCostos = !togglePreciosOrCostos; reload();" v-else>Ver precios</button>
        
        <div class="pull-right" style="margin-bottom: 15px;">
            <button class="btn btn-success" @click="isEditing = true" v-if="!isEditing && $parent.userCan('editar_catalogo')" >Editar lista</button>
            <button class="btn btn-info" @click="cancel()" v-if="isEditing">Cancelar</button>
            <button class="btn btn-success" @click="save()" v-if="isEditing">Guardar cambios</button>
        </div>

        <div class="alert alert-info" style="margin-top: 15px;" v-if="isEditing">
            <strong>Atención</strong> Estás editando <b><u>{{ togglePreciosOrCostos ? 'COSTOS' : 'PRECIOS' }}</u></b>.
        </div>

        <div v-for="(currentDesign, number) in priceList.designs">
          <table class="table table-bordered" style="margin: 0;width: inherit; max-width: inherit;">
            <thead :style="number > 0 ? 'display: none;' : ''">
                <tr>
                    <th></th>
                    <th v-for="currentMaterial in currentDesign.materials" :style="'text-align: center;' + currentMaterial.color == '#ffffff' ? 'color: #000;' : 'color: #fff;background-color: ' + currentMaterial.color + ';'" :colspan="Math.max(1, currentMaterial.material.characteristics.length)">
                        {{ currentMaterial.material.name }}
                        <input type="color" v-model="currentMaterial.color" v-if="isEditing" style="display: inline-block;">
                        <button class="btn btn-warning" @click="addNewCharacteristic(currentMaterial)" v-if="isEditing"><i class="fa fa-plus"></i></button>
                        <button class="btn btn-danger" @click="removeMaterial(currentMaterial.material_id)" v-if="isEditing"><i class="fa fa-trash"></i></button>
                    </th>
                    <th v-if="isCreatingNewOne && selectedTable == number">
                        <select class="form-control" @change="addNewMaterial(currentDesign, $event)">
                            <option hidden selected disabled>Selecciona un material</option>
                            <option v-for="material in materials" :value="material.id" :disabled="validateIfAvailableM(material.id)">{{  material.name }}</option>
                        </select>
                    </th>
                    <th v-if="isEditing">
                        <button class="btn btn-success" @click="addNewRow(number)"><i class="fa fa-plus"></i></button>
                    </th>
                </tr>
            </thead>
            <tbody>
                    <!-- <td v-for="currentMaterial in currentDesign.materials">
                        <table class="table table-bordered">
                            <tr>
                                <td v-for="currentCharacteristic in currentMaterial.characteristics">
                                    {{ currentCharacteristic.name }} <br/>
                                    <input class="form-control" type="numner" v-model="treatement.price" v-if="!togglePreciosOrCostos">
                                    <input class="form-control" type="numner" v-model="treatement.cost" v-else>
                                </td>
                                <td rowspan="2" v-if="isCreatingNewTreatement && selectedIndex == index && selectedTable == number">
                                    <select class="form-control" @change="addNewTreatement(material, $event)">
                                        <option hidden selected disabled>Selecciona una característica</option>
                                        <option v-for="treatment in treatments" :value="treatment.title">{{  treatment.title }}</option>
                                    </select>
                                </td>
                                <td rowspan="2">
                                    <button class="btn btn-warning" @click="cerateNewTratement(index, number)">Añadir característica</button>
                                </td>
                            </tr>
                        </table>
                    </td> -->
                  <tr :style="number > 0 ? 'display: none;' : ''">
                    <td style="background-color: #f5f5f6; border: 0 !important;"></td>
                    <template v-for="(currentMaterial, idj) in currentDesign.materials">
                      <template v-for="(currentCharacteristic, idx) in currentMaterial.material.characteristics">
                        <td :style="'width: 100px;' + currentMaterial.color == '#ffffff' ? 'color: #000;' : 'color: #fff;background-color: ' + currentMaterial.color + ';'">
                          <select class="form-control" @change="handleAddChar(currentMaterial, idx, $event)" v-if="!currentCharacteristic.characteristic_id" style="width: 150px;">
                              <option hidden selected disabled>Selecciona una característica</option>
                              <option v-for="characteristic in characteristics" :value="characteristic.id">{{ characteristic.name }}</option>
                          </select>
                          <span v-else>{{ currentCharacteristic.characteristic.name }} <button class="btn btn-danger" @click="removeChar(currentMaterial, idx)" v-if="isEditing"><i class="fa fa-trash"></i></button></span>
                        </td>
                      </template>
                     
                    </template>
                  </tr>
                  <tr>
                    <td v-if="isEditing">
                      <select class="form-control" v-model="currentDesign.id" style="width: 140px; display: inline;">
                        <option value="0" selected hiddden disabled>SELECCIONA UN DISEÑO</option>
                        <option v-for="design in designs" :value="design.id" :disabled="validateIfAvailable(design.id)">{{ design.name }}</option>
                      </select>
                      <button class="btn btn-danger" @click="removeDesign(number)" v-if="isEditing" style="display: inline;"><i class="fa fa-trash"></i></button>
                    </td>
                    <td v-else>{{ currentDesign.name }}</td>
                    <template v-for="currentMaterial in currentDesign.materials">
                      <template v-for="currentCharacteristic in currentMaterial.material.characteristics">
                        <td style="width: 100px;" v-if="isEditing">
                            <input style="width: 100px;" class="form-control" type="number" v-model="currentCharacteristic.price" v-if="!togglePreciosOrCostos">
                            <input style="width: 100px;" class="form-control" type="number" v-model="currentCharacteristic.cost" v-else>
                        </td>
                        <td style="width: 100px;" v-else>
                            <span style="width: 100px;" v-if="!togglePreciosOrCostos">{{  currentCharacteristic.price | currency }}</span>
                            <span style="width: 100px;" v-else>{{  currentCharacteristic.cost | currency }}</span>
                        </td>
                      </template>
                    </template>
                  </tr>
            </tbody>
          </table>
        </div>
        <hr/>
        <h2>Extras</h2>
        <div class="extras">
          <button class="btn btn-info" @click="addExtraSlot()" v-if="isEditing">Añadir extra</button>
          <br/>
          <table class="table table-bordered" style="margin: 0;width: inherit; max-width: inherit; margin-top: 15px;" v-if="priceList.extras.length > 0">
            <tr>
                <th style="color: #fff; background-color: #212529; border: 1px solid #32383e; font-weight: bold; padding: 8px; line-height: 1.6;">Nombre</th> <!-- Static first cell for "Name" label -->
                <td v-for="extra in priceList.extras" :key="'name-'+extra.id" style="border: 1px solid #ebebeb; border: 1px solid #ebebeb; padding: 8px; line-height: 1.6;">
                    <template v-if="isEditing">
                        <input class="form-control" type="text" v-model="extra.name">
                    </template>
                    <template v-else>
                        {{ extra.name }}
                    </template>
                </td>
            </tr>

            <tr v-if="!togglePreciosOrCostos">
                <th style="color: #fff; background-color: #212529; border: 1px solid #32383e; font-weight: bold; padding: 8px; line-height: 1.6;">Precio</th> <!-- Static first cell for "Price" label -->
                <td v-for="extra in priceList.extras" :key="'price-'+extra.id" style="border: 1px solid #ebebeb; border: 1px solid #ebebeb; padding: 8px; line-height: 1.6;">
                    <template v-if="isEditing">
                        <input class="form-control" type="number" v-model="extra.price">
                    </template>
                    <template v-else>
                        {{ extra.price | currency }}
                    </template>
                </td>
            </tr>
            <tr v-else>
                <th style="color: #fff; background-color: #212529; border: 1px solid #32383e; font-weight: bold; padding: 8px; line-height: 1.6;">Costo</th> <!-- Static first cell for "Price" label -->
                <td v-for="extra in priceList.extras" :key="'cost-'+extra.id" style="border: 1px solid #ebebeb; border: 1px solid #ebebeb; padding: 8px; line-height: 1.6;">
                    <template v-if="isEditing">
                        <input class="form-control" type="number" v-model="extra.cost">
                    </template>
                    <template v-else>
                        {{ extra.cost | currency }}
                    </template>
                </td>
            </tr>
          </table>
        </div>
    </div>
  </template>
  
  <script>

  export default {
    data() {
      return {
        togglePreciosOrCostos: false,
        numberOfLists: 0,
        isCreatingNewOne: false,
        isCreatingNewTreatement: false,
        currentMaterials: [],
        currentTreatments: [],
        currentColors: [],
        selectedIndex: null,
        selectedTable: null,

        _materials: [
          { title: 'CR-39' },
          { title: 'ALTO INDICE 1.56' },
          { title: 'PARASOL' },
          { title: 'TRIVEX' },
          { title: 'TRIVEX 1.60' },
          { title: 'TRIVEX PARASOL' },
          { title: 'TRIVEX B/BLOCK' },
          { title: 'ALTAS GRADUACIONES' },
        ],
        _treatments: [
          { title: 'Blanco'}, 
          { title: 'HD | AR'}, 
          { title: 'AR'}, 
          { title: 'AL | AR'}
        ],
        selectedMaterial: { title: '' },
        selectedTreatment: { title: '' },
        selectedTreatments: [],
        totalCost: 0,
        id: null,
        priceList: null,
        designs: [],
        materials: [],
        characteristics: [],
        isEditing: false,
      };
    },
    filters: {
      currency(value) {
        return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value);
      },
    },
    methods: {
        cancel() {
          if(confirm('¿Estás seguro? Se perderán todos los cambios')) {
            this.getList();
            this.isEditing = false;
          }
        },
        save() {

            this.$parent.inPetition = true;

            // price / cost Validation
            for(const d of this.priceList.designs) {
                for(const m of d.materials) {
                    const material = m.material;
                    for(const c of material.characteristics) {
                        const characteristic = c.characteristic;
                        const price = parseInt(c.price);
                        const cost = parseInt(c.cost);

                        // if(price != 0 && cost == 0) {
                        //     this.$parent.showMessage(`<b>${ material.name }</b> en ${ characteristic.name} tiene un precio de $0`, 'error');
                        //     this.$parent.inPetition = false;
                        //     return
                        // }

                        if(isNaN(price)) {
                            this.$parent.showMessage(`<b>${ material.name }</b> en ${ characteristic.name} no tiene un precio válido, no puede estar vacío`, 'warning');
                            this.$parent.inPetition = false;
                            return;
                        }

                        if(isNaN(cost)) {
                            this.$parent.showMessage(`<b>${ material.name }</b> en ${ characteristic.name} no tiene un costo válido, no puede estar vacío`, 'warning');
                            this.$parent.inPetition = false;
                            return;
                        }
                        
                        if(price != 0 && cost == 0) {
                            this.$parent.showMessage(`<b>${ material.name }</b> en ${ characteristic.name} tiene un costo de $0`, 'warning');
                            this.$parent.inPetition = false;
                            return;
                        }
                    }
                }
            }

            axios.put('https://apiv2.augenlabs.com/v1/lists/' + this.id, this.priceList).then(result => {
                console.log(result);
                this.$parent.showMessage('Cambios en lista de precios aplicados correctamente',"success");
					    	
                this.isEditing = false;
                this.$parent.inPetition = false;
            }).catch(error => {
                this.$parent.inPetition = false;
                console.log(error);
            });
        },
        filterTreats(mat, number) {
            if(!Array.isArray(this.currentTreatments[number]))
              return [];
            return this.currentTreatments[number].filter(item => item.material == mat);
        },
        cerateNewTratement(index, number) {
            this.selectedIndex = index;
            this.selectedTable = number;
            this.isCreatingNewTreatement = true;

        },
        addNewTreatement(material, $event) {
            // console.log(material, $event);
            let value = $event.target.value;
            
            if(!Array.isArray(this.currentTreatments[this.selectedTable]))
              this.currentTreatments[this.selectedTable] = []
            if(value.includes('AR')) {
              this.currentTreatments[this.selectedTable].push({ material: material, value: value + ' Azul', price: 0.0, cost: 0.0 });
              this.currentTreatments[this.selectedTable].push({ material: material, value: value + ' Verde', price: 0.0, cost: 0.0 });
              this.currentTreatments[this.selectedTable].push({ material: material, value: value + ' Gold', price: 0.0, cost: 0.0 });
            } else {
              this.currentTreatments[this.selectedTable].push({ material: material, value: value, price: 0.0, cost: 0.0 });
            }
            this.isCreatingNewTreatement = false;
        },
      addNewRow(number) {
        this.selectedTable = number;
        this.isCreatingNewOne = true;
      },
      addNewMaterial(currentDesign, $event) {
        let value = $event.target.value;
        let selectedMaterial = this.materials.find(m => m.id == value);
        
        selectedMaterial.characteristics = [];
        
        currentDesign.materials.push({
          material_id: value, 
          material: selectedMaterial,
          color: selectedMaterial.color,
        });

        console.log(this.priceList);
        this.reload();

        this.isCreatingNewOne = false;
      },
      fetchPrices() {
        // Placeholder for fetching and updating treatment costs based on the selected material
        // For now, you'll need to define how costs are determined and update the `cost` property of each treatment in `selectedTreatments`
        this.calculateTotalCost();
      },
      addTreatment() {
        if (this.selectedTreatment && !this.selectedTreatments.find(t => t.title === this.selectedTreatment.title)) {
          this.selectedTreatments.push({...this.selectedTreatment, cost: 0}); // Add the selected treatment with a default cost
          this.fetchPrices(); // Update prices whenever a new treatment is added
        }
      },
      removeTreatment(index) {
        this.selectedTreatments.splice(index, 1);
        this.calculateTotalCost();
      },
      calculateTotalCost() {
        this.totalCost = this.selectedTreatments.reduce((acc, treatment) => acc + treatment.cost, 0);
      },
      getList() {
        this.$parent.inPetition = true;
        axios.get('https://apiv2.augenlabs.com/v1/lists/' + this.id).then(result => {
          console.log(result);
          this.priceList = result.data;
          this.$parent.inPetition = false;
        });
      },
      getDesigns() {
        this.$parent.inPetition = true;
        axios.get('https://apiv2.augenlabs.com/v1/designs').then(result => {
          this.designs = result.data;
          this.$parent.inPetition = false;
        });
      },
      getMaterials() {
        this.$parent.inPetition = true;
        axios.get('https://apiv2.augenlabs.com/v1/materials').then(result => {
          this.materials = result.data;
          this.$parent.inPetition = false;
        });
      },
      getCharacteristics() {
        this.$parent.inPetition = true;
        axios.get('https://apiv2.augenlabs.com/v1/characteristics').then(result => {
          this.characteristics = result.data;
          this.$parent.inPetition = false;
        });
      },
      addDesignSlot() {
        if(this.priceList.designs.length == 0) {
          this.priceList.designs.push({
            id: 0,
            materials: [] 
          });
        } else {
          this.priceList.designs.push({
            id: 0,
            materials: JSON.parse(JSON.stringify(this.priceList.designs[0].materials)) 
          });

          this.priceList.designs[this.priceList.designs.length - 1].materials.forEach(m => {
            m.material.characteristics.forEach(c => {
              c.price = 0;
              c.cost = 0;
            });
          });

          console.log(this.priceList.designs[this.priceList.designs.length - 1]);
        }

        this.reload();
      },
      addNewCharacteristic(material) {
        material.material.characteristics.push({});
        this.reload();
        console.log(material);
      },
      handleAddChar(material, idx, $event) {
        let value = $event.target.value;
        let selectedCharacteristic = this.characteristics.find(c => c.id == value);

        if(selectedCharacteristic.name == 'AR' || selectedCharacteristic.name == 'AL + AR' || selectedCharacteristic.name == 'HD + AR' || selectedCharacteristic.name == 'FREE FORM + AR') {
          let ARBlue =  this.characteristics.find(c => c.name == selectedCharacteristic.name + ' Azul');
          let ARGreen =  this.characteristics.find(c => c.name == selectedCharacteristic.name + ' Verde');
          let ARGold =  this.characteristics.find(c => c.name == selectedCharacteristic.name + ' Gold');

          material.material.characteristics[idx] = {
            characteristic_id: ARBlue.id,
            characteristic: ARBlue,
            price: 0,
            cost: 0
          };

          material.material.characteristics.push({
            characteristic_id: ARGreen.id,
            characteristic: ARGreen,
            price: 0,
            cost: 0
          });

          material.material.characteristics.push({
            characteristic_id: ARGold.id,
            characteristic: ARGold,
            price: 0,
            cost: 0
          });

        } else {

          material.material.characteristics[idx] = {
            characteristic_id: value,
            characteristic: selectedCharacteristic,
            price: 0,
            cost: 0
          };
        }

        this.reload();
      },
      removeChar(material, idx) {
        console.log(material, idx);
        let materialId = material.material_id;
        this.priceList.designs.forEach(d => {
          d.materials.forEach(m => {
            if(m.material_id == materialId) {
              m.material.characteristics.splice(idx, 1);
            }
          })
        });
        this.reload();
      },
      reload() {
        let priceListData = this.priceList;
        this.priceList = null;
        this.priceList = priceListData;
      },
      removeDesign(idx) {
        this.priceList.designs.splice(idx, 1);
        this.reload();
      },
      removeMaterial(materialId) {
        console.log(materialId);
        this.priceList.designs.forEach(d => {
          console.log(d.materials.filter(m => m.material_id != materialId));
          d.materials = d.materials.filter(m => m.material_id != materialId)
        });

        this.reload();
      },
      validateIfAvailable(designId) {
        const currentDesigns = this.priceList.designs.map(d => d.id);
        return currentDesigns.includes(designId);
      },
      validateIfAvailableM(materialId) {
        const currentMaterials = this.priceList.designs[0].materials.map(m => parseInt(m.material_id));
        return currentMaterials.includes(materialId);
      },
      addExtraSlot() {
        if(this.priceList !== null) {
            if(!Array.isArray(this.priceList.extras)) {
                this.priceList.extras = [];
            }

            this.priceList.extras.push({
                id: Date.now(),
                name: '',
                price: 0,
                cost: 0
            })
        }
      }
    },
    mounted() {
      this.id = this.$route.params.id;
      this.getList();
      this.getDesigns();
      this.getMaterials(); 
      this.getCharacteristics();
    },
  };
  </script>
  
  <style lang="scss" scoped>
  table th,td {
    width: 200px;
    text-align: center;
    max-width: 200px !important;
    min-width: 200px !important;
    vertical-align: middle !important;
  }
  </style>
  