<template>
    <div>
        <div v-if="showForm">
            <form @submit="handleSubmit" method="POST">
                <div class="form-group">
                    <label for="">Nombre:</label>
                    <input type="text" name="name" class="form-control" v-model="extra.name">
                </div>
                <div class="form-group">
                    <label for="">Precio:</label>
                    <input class="form-control" type="number" name="price" v-model="extra.price">
                </div>
                <div class="form-group">
                    <label for="">Precio:</label>
                    <input class="form-control" type="number" name="price" v-model="extra.cost">
                </div>
                <div class="form-group">
                    <label for="">Activo:</label>
                    <select class="form-control" name="active" v-model="extra.active">
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
                        <th>Precio</th>
                        <th>Costo</th>
                        <th style="width: 20px">Activo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="extra in extras">
                        <td>{{ extra.id }}</td>
                        <td>{{ extra.name }}</td>
                        <td>{{ extra.price | currency }}</td>
                        <td>{{ extra.cost | currency }}</td>
                        <td style="width: 20px">{{ extra.active ? 'Sí' : 'No' }}</td>
                        <td>
                            <button class="btn btn-info btn-sm" v-if="$parent.userCan('editar_catalogo')" @click="selectextra(extra)">
                                Editar
                            </button>
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
            extras: [],
            showForm: false,
            extra: {
                name: '',
                description: '',
            }
        }
    },
    methods: {
        getExtras() {
            this.$parent.inPetition = true;
            axios.get('https://apiv2.augenlabs.com/v1/extras').then(result => {
                console.log(result);
                this.extras = result.data;
                this.$parent.inPetition = false;
                window.scrollTo(0, 0);
            });
        },
        addNew() {
            this.extra = {
                name: '',
                description: '',
            };
            this.showForm = true;
            window.scrollTo(0, 0);
        },
        handleSubmit(event) {
            event.preventDefault();
            this.$parent.inPetition = true;
            
            if(this.extra.id) {
                axios.put('https://apiv2.augenlabs.com/v1/extras/' + this.extra.id, this.extra).then(result => {
                    console.log(result);
                    this.showForm = false;
                    this.getExtras();
                    this.$parent.showMessage('Cambios aplicados correctamente',"success");
                    this.$parent.inPetition = false;
                });
            } else {
                axios.post('https://apiv2.augenlabs.com/v1/extras', this.extra).then(result => {
                    console.log(result);
                    this.showForm = false;
                    this.getExtras();
                    this.$parent.showMessage('extraa de precios creada correctamente',"success");
                    this.$parent.inPetition = false;
                });
            }
        },
        selectextra(extra) {
            this.extra = extra;
            this.showForm = true;
            window.scrollTo(0, 0);
        }
    },
    mounted() {
        this.getExtras();            
    }
}
</script>

<style lang="scss" scoped>

</style>