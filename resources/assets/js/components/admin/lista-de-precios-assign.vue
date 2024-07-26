<template>
    <div v-if="priceList != null">
        <div class="alert alert-info" style="margin-top: 15px;" v-if="isEditing">
            <strong>Atención</strong> Estás editando la asignación de la lista de precios <b><u>{{ priceList.name }}</u></b>.
        </div>
        <h1>Lista de precios: <b>{{ priceList.name }}</b></h1>
        <br/>
        <div class="pull-right" style="margin-bottom: 15px;">
            <button class="btn btn-warning" @click="$router.push('/lista-de-precios/')"><i class="fa fa-arrow-left" aria-hidden="true"></i>&nbsp;Regresar a listado</button>
            <button class="btn btn-success" @click="isEditing = true" v-if="!isEditing && $parent.userCan('editar_catalogo')" >Editar lista</button>
            <button class="btn btn-info" @click="cancel" v-if="isEditing">Cancelar</button>
        </div>

        <table class="table table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th style="background-color: #000;color: #fff;font-weight: bold;">Tipo</th>
                    <th style="background-color: #000;color: #fff;font-weight: bold;">Nombre</th>
                    <th v-if="isEditing" style="background-color: #000;color: #fff;font-weight: bold;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in assignments">
                    <td>{{ item.type }}</td>
                    <td>{{ item.name }}</td>
                    <td v-if="isEditing">
                        <button @click="remove(item)" class="btn btn-danger"><i class="fa fa-trash"></i> Eliminar</button>
                    </td>
                </tr>
            </tbody>
            <tfoot v-if="isEditing">
                <tr>
                    <td>
                        <select class="form-control" @change="computeUrl" v-model="currentModel">
                            <option selected disabled hidden>Seleciona un tipo para asignar</option>
                            <option v-for="option in modelsAvailable" :value="option.key">{{ option.name }}</option>
                        </select>
                    </td>
                    <td v-if="isLoadingAutocomplete || currentModel == ''">Espera un momento por favor...</td>
                    <td v-if="!isLoadingAutocomplete && currentModel != ''">
                        <div v-if="currentModel === 'client'">
                            <autocomplete
                                :url="url"
                                anchor="name"
                                label="name"
                                :classes="{ input: 'form-control' }"
                                :customHeaders="{ Authorization: $parent.token }"
                                :on-select="getData">
                            </autocomplete>
                        </div>
                        <div v-else-if="currentModel === 'branch'">
                            <select class="form-control" v-model="selectedModelId">
                                <option selected disabled hidden>Seleciona un PDV</option>
                                <option v-for="branch in branches" :value="branch.id">{{ branch.name }}</option>
                            </select>
                        </div>
                        <div v-else-if="currentModel === 'group'">
                            <select class="form-control" v-model="selectedModelId">
                                <option selected disabled hidden>Seleciona un Grupo</option>
                                <option v-for="group in groups" :value="group.id">{{ group.name }}</option>
                            </select>
                        </div>
                    </td>
                    <td><button class="btn btn-success" :disabled="!dataToSend.is_valid" style="width: 100%;" @click="submit()">Enviar asignación</button></td>
                </tr>
            </tfoot>
        </table>
        <div style="text-align: right;">
            <button @click="showClients" class="btn btn-info">Ver Clientes&nbsp;<i class="fas fa-users"></i></button>
            <a @click.prevent="downloadExcel" class="btn btn-success">Descargar Excel&nbsp;<i class="fas fa-file-excel"></i></a>
        </div>

        <div style="display:none;">
            <div id="clients_modal">
                <h4>Clientes asignados:</h4>
                <table
                    class="table responsive"
                    id="clients-table"
                    data-height="460"
                    data-search="true"
                    data-pagination="false"
                    data-locale="es-ES">
                    <thead>
                        <tr>
                            <th data-field="id">ID</th>
                            <th data-field="name">Nombre</th>
                            <th data-field="comertial_name">Nombre comercial</th>
                            <th data-field="branch.name">Sucursal</th>
                            <th data-field="email">Email</th>
                            <th data-field="celphone">Telefono</th>
                            <th data-field="state.name">Estado</th>
                            <th data-field="town.name">Ciudad</th>
                            <th data-field="status">Estatus</th>
                            <th data-field="postal_code">CP</th>
                            <th data-field="facturacion.postal_code">CP Fiscal</th>
                            <th data-field="contact_celphone">Origen del cliente</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            id: null, 
            priceList: null,
            isEditing: false,
            assignments: [],
            modelsAvailable: [
                { key: 'branch', name: 'PDV', class: 'App\\Branch' },
                { key: 'client', name: 'Cliente', class: 'App\\Client' },
                { key: 'group', name: 'Grupo', class: 'App\\Group' },
            ],
            url: '',
            currentModel: '',
            isLoadingAutocomplete: false,
            dataToSend: {
                is_valid: false
            },
            branches: [],
            groups: [],
            selectedModelId: ''
        }
    },
    methods: {
        getList() {
            this.$parent.inPetition = true;
            axios.get('https://apiv2.augenlabs.com/v1/lists/' + this.id).then(result => {
                console.log(result);
                this.priceList = result.data;
                this.getCurrentList();
                this.$parent.inPetition = false;
            });
        },
        getCurrentList() {
            this.$parent.inPetition = true;
            axios.get(tools.url('/api/lists/' + this.id + '/assignment')).then(result => {
                this.assignments = result.data;
                this.$parent.inPetition = false;
            });
        },
        computeUrl() {
            this.isLoadingAutocomplete = true;
            this.url = '/api/lists/search/' + this.currentModel;

            if (this.currentModel === 'branch') {
                axios.get('/api/branches').then(response => {
                    this.branches = response.data;
                    this.isLoadingAutocomplete = false;
                });
            } else if (this.currentModel === 'group') {
                axios.get('/api/groups').then(response => {
                    this.groups = response.data;
                    this.isLoadingAutocomplete = false;
                });
            } else {
                this.isLoadingAutocomplete = false;
            }
        },
        getData(obj) {
            let model_type;
            if (this.currentModel === 'client') {
                model_type = this.modelsAvailable.find(m => m.key == this.currentModel).class;
                this.dataToSend = {
                    list_id: this.id,
                    model_id: obj.id,
                    model_type,
                    is_valid: true
                };
            } else if (this.currentModel === 'branch' || this.currentModel === 'group') {
                model_type = this.modelsAvailable.find(m => m.key == this.currentModel).class;
                this.dataToSend = {
                    list_id: this.id,
                    model_id: this.selectedModelId,
                    model_type,
                    is_valid: !!this.selectedModelId
                };
            }
        },
        cancel() {
            if(confirm('¿Estás seguro que deseas cancelar la asignación?')) {
               this.resetValues();
            }
        },
        resetValues() {
            this.isEditing = false;
            this.dataToSend = {
                is_valid: false
            }
            this.currentModel = '';
            this.url = '';
        },
        submit() {
            this.$parent.inPetition = true;
            axios.post(tools.url('/api/lists/assignment'), this.dataToSend).then(result => {
                this.getCurrentList();
                this.resetValues();
                this.$parent.showMessage('Cambios en lista de precios aplicados correctamente', "success");
                this.$parent.inPetition = false;
            });
        },
        remove(item) {
            if(confirm('¿Estás seguro que deseas eliminar de esta lista a ' + item.name + '?')) {
                const model_type = this.modelsAvailable.find(m => m.name == item.type).class;
                const data = {
                    list_id: this.id,
                    model_id: item.model.id,
                    model_type
                };

                this.$parent.inPetition = true;
                axios.post(tools.url('/api/lists/remove_assignment'), data).then(result => {
                    this.getCurrentList();
                    this.resetValues();
                    this.$parent.showMessage('Cambios en lista de precios aplicados correctamente',"success");
                    this.$parent.inPetition = false;
                });
            }
        },
        downloadExcel() {
            const name = this.priceList.name;
            let url = `/v1/list_assignment?id=${ this.id }&name=${ name }`;

            var element = document.createElement('a');
            element.setAttribute('href', url);
            // element.setAttribute('target', '_blank');
            // element.setAttribute('download', filename);

            element.style.display = 'none';
            document.body.appendChild(element);

            element.click();

            document.body.removeChild(element);
        },
        showClients() {
            this.$parent.inPetition = true;
            axios.get(tools.url(`/api/lists/${ this.id }/client_assignment`)).then(result => {
                this.clients = result.data;
                alertify.clientDialog(document.getElementById("clients_modal"));
                setTimeout(() => {
                    jQuery('#clients-table').bootstrapTable({
                        data: this.clients, // Directly use the fetched data
                        showRefresh: true,
                        showToggle: true,
                        pagination: true,
                        search: true,
                    });
                }, 500);
                this.$parent.inPetition = false;
            });
        }
    },
    mounted() {
        alertify.clientDialog || alertify.dialog('clientDialog',function(){
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
        this.id = this.$route.params.id;
        this.getList();
    }
}
</script>

<style lang="scss" scoped>

</style>