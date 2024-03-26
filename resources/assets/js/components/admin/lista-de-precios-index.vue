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
                    <button class="btn btn-info" @click="showForm = false">Cancelar</button>
                    <button type="submit" class="btn btn-success">Enviar</button>
                </div>
            </form>
        </div>

        <div v-else>
            <div>
                <button class="btn btn-sucess" @click="addNew">Agregar nuevo</button>
            </div>
            <table class="table table-bordered" style="margin-top: 15px;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripcion</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="list in lists">
                        <td>{{ list.id }}</td>
                        <td>{{ list.name }}</td>
                        <td>{{ list.description }}</td>
                        <td><a :href="'/lista-de-precios/' + list.id" class="btn btn-info">Editar</a></td>
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
            });
        },
        addNew() {
            this.priceList = {
                name: '',
                description: '',
            };
            this.showForm = true;
        },
        handleSubmit(event) {
            event.preventDefault();
            this.$parent.inPetition = true;
            axios.post('https://apiv2.augenlabs.com/v1/lists', this.priceList).then(result => {
                console.log(result);
                this.showForm = false;
                this.getLists();
                this.$parent.inPetition = false;
            });
        }
    },
    mounted() {
        this.getLists();            
    }
}
</script>

<style lang="scss" scoped>

</style>