<template>
    <div class="">
        <div class="row">
            <div class="col-md-12">
                <h1>Estado de cuenta</h1>
                <div class="table-responsive pdv-table">
                    <table class="table responsive table-striped response" v-if="branches.length > 0">
                        <thead>
                            <tr>
                                <th>PDV</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="branch in branches" :key="branch.id">
                                <td>{{ branch.name }}</td>
                                <td>
                                    <a :href="'/generate/account/' + branch.id + '?type=credit'" class="btn btn-info">Generar (plazo) <i class="fas fa-file-archive"></i></a>
                                    <a :href="'/generate/account/' + branch.id + '?type=cash'" class="btn btn-warning">Generar (contado) <i class="fas fa-file-archive"></i></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: "",
    data: () => ({
        branches: []
    }),
    methods: {
        getBranches() {
            this.$parent.inPetition = true;
            axios.get('/api/branches').then(result => {
                this.branches = result.data;
                this.$parent.inPetition = false;
            }).catch(error => {
                this.$parent.handleErrors(error);
                this.$parent.inPetition = false;
            });
        }
    },
    mounted() {
        this.getBranches();
    }
}
</script>
<style lang="scss" scoped>
.pdv-table {
    max-width: 600px;
    margin: 25px auto;
    border: 1px solid #ebebeb;
    -webkit-box-shadow: 0 3px 1px rgba(0, 0, 0, 0.04);
    -moz-box-shadow: 0 3px 1px rgba(0, 0, 0, 0.04);
    box-shadow: 0 3px 1px rgba(0, 0, 0, 0.04);
    background-clip: padding-box;
}
</style>
