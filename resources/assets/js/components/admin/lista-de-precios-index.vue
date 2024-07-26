<template>
    <div>
        <div v-if="showForm">
            <form @submit="handleSubmit" method="POST">
                <div class="form-group">
                    <label for="">Nombre:</label>
                    <input type="text" name="name" class="form-control" v-model="priceList.name">
                </div>
                <div class="form-group">
                    <label for="">Descripcion:</label>
                    <textarea class="form-control" name="description" v-model="priceList.description"></textarea>
                </div>
                <div class="form-group">
                    <label for="">Activo:</label>
                    <select class="form-control" name="active" v-model="priceList.active">
                        <option value="1">Sí</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <div class="form-group">
                    <button class="btn btn-info" @click="showForm = false">Cancelar</button>
                    <button type="submit" class="btn btn-success">Enviar</button>
                </div>
            </form>
        </div>

        <div v-else>
            <div>
                <button class="btn btn-sucess" @click="addNew" v-if="$parent.userCan('editar_catalogo')">Agregar nuevo</button>
            </div>
            <table class="table table-bordered" style="margin-top: 15px;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripcion</th>
                        <th style="width: 20px">Activo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="list in lists">
                        <td>{{ list.id }}</td>
                        <td>{{ list.name }}</td>
                        <td>{{ list.description }}</td>
                        <td style="width: 20px">{{ list.active ? 'Sí' : 'No' }}</td>
                        <td>
                            <button class="btn btn-info btn-sm" v-if="$parent.userCan('editar_catalogo')" @click="selectList(list)">
                                Editar
                            </button>
                            <router-link :to="'/lista-de-precios/' + list.id">
                                <button class="btn btn-warning btn-sm">
                                    <i class="fa fa-pencil"></i> Ver contenido
                                </button>
                            </router-link>
                            <router-link :to="'/lista-de-precios/' + list.id + '/assignacion'">
                                <button class="btn btn-success btn-sm">
                                    <i class="fa fa-eye"></i> Asignación
                                </button>
                            </router-link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            lists: [],
            showForm: false,
            priceList: {
                name: '',
                description: '',
            }
        }
    },
    methods: {
        getLists() {
            this.$parent.inPetition = true;
            axios.get('https://apiv2.augenlabs.com/v1/lists').then(result => {
                console.log(result);
                this.lists = result.data;
                this.$parent.inPetition = false;
                window.scrollTo(0, 0);
            });
        },
        addNew() {
            this.priceList = {
                name: '',
                description: '',
            };
            this.showForm = true;
            window.scrollTo(0, 0);
        },
        handleSubmit(event) {
            event.preventDefault();
            this.$parent.inPetition = true;
            
            if(this.priceList.id) {
                axios.put('https://apiv2.augenlabs.com/v1/lists/' + this.priceList.id, this.priceList).then(result => {
                    console.log(result);
                    this.showForm = false;
                    this.getLists();
                    this.$parent.showMessage('Cambios aplicados correctamente',"success");
                    this.$parent.inPetition = false;
                });
            } else {
                axios.post('https://apiv2.augenlabs.com/v1/lists', this.priceList).then(result => {
                    console.log(result);
                    this.showForm = false;
                    this.getLists();
                    this.$parent.showMessage('Lista de precios creada correctamente',"success");
                    this.$parent.inPetition = false;
                });
            }
        },
        selectList(list) {
            this.priceList = list;
            this.showForm = true;
            window.scrollTo(0, 0);
        }
    },
    mounted() {
        this.getLists();            
    }
}
</script>

<style lang="scss" scoped>

</style>