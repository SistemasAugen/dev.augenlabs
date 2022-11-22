<template>
	<div>
		<div class="row">
			<div class="col-md-12">
				<div id="toolbar">
			        <router-link to="/clients/edit">
			        	<button class="btn btn-success btn-sm">
				            <i class="fa fa-plus"></i> Nuevo
				        </button>
			        </router-link>

			    </div>
				<table
					class="table responsive"
                	id="clients-table"
					data-toggle="table"
					data-height="460"
	                data-search="true"
					data-side-pagination="server"
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
<script type="text/javascript">
	export default {
		data(){
			return { }
		},
		methods: {
			ajaxRequest(params) {
	            axios.get('/api/clients' + '?' + jQuery.param(params.data) + '&ajax=true').then(result => {
	                params.success(result.data);
	                let that = this;

					jQuery('#clients-table').on('click-row.bs.table', (row, data) => {
						this.$router.push('/clients/edit/'+data.id);
					});
	            });
	        },
			mountTable() {
				jQuery('#clients-table').bootstrapTable({
	                ajax: this.ajaxRequest,
					exportTypes: ['excel'],
					exportDataType: 'all',
					showRefresh:true,
					showExport: true,
					showToggle: true,
					pagination: true,
					search: true,
					exportOptions: {
						fileName: 'clientes'
					}
	            });
			}
		},
        mounted() {
			this.mountTable();
        }
    }
</script>
