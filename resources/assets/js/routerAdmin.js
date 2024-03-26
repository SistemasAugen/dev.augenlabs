import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

const page="./components/admin/";

const MyRouter = new VueRouter({
    mode: 'history',
    base: '/',
    fallback: true,
  	routes:[
	    { path: '/', redirect:"/login"},
	    { path: '/login', component: require(page+ 'login.vue').default, meta:{title:"Login"}},
	    { path: '/home', component: require(page+ 'home.vue').default, meta:{title:"Home"}},
	    { path: '/cover', component: require(page+ 'cover.vue').default, meta:{title:"Carátula Ejecutiva"}},
	    { path: '/asistencia', component: require(page+ 'asistencia.vue').default, meta:{title:"Firma de asistencia"}},
	    { path: '/profile', component: require(page+'me.vue').default, meta:{title:"Mi perfil"}},
	    //Usuarios
	    { path: '/users', component: require(page+'users/index.vue').default, meta:{title:"Usuarios"}},
	    { path: '/users/edit', component: require(page+'users/edit.vue').default, meta:{title:"Nuevo"}},//Cuando no envian parametro
	    { path: '/users/edit/:id', component: require(page+'users/edit.vue').default, meta:{title:"Editar"}},//Con parametro
	    //Roles
	    { path: '/roles', component: require(page+'configuration/roles.vue').default, meta:{title:"Roles"}},
		{ path: '/roles/edit/:id', component: require(page+'configuration/permissions.vue').default, meta:{title:"Editar"}},
		//Rutas de administrador
		{ path: '/branches', component: require(page+'branches/index.vue').default, meta:{title:"Sucursales"}},
		{ path: '/branches/edit', component: require(page+'branches/edit.vue').default, meta:{title:"Nuevo"}},
		{ path: '/branches/edit/:id', component: require(page+'branches/edit.vue').default, meta:{title:"Editar"}},
		{ path: '/laboratories', component: require(page+'laboratories/index.vue').default, meta:{title:"Laboratorios"}},
		{ path: '/laboratories/edit', component: require(page+'laboratories/edit.vue').default, meta:{title:"Nuevo"}},
		{ path: '/laboratories/edit/:id', component: require(page+'laboratories/edit.vue').default, meta:{title:"Editar"}},
		{ path: '/types', component: require(page+'types/index.vue').default, meta:{title:"Diseños"}},
		{ path: '/types/edit', component: require(page+'types/edit.vue').default, meta:{title:"Nuevo"}},
		{ path: '/types/edit/:id', component: require(page+'types/edit.vue').default, meta:{title:"Editar"}},
		{ path: '/categories', component: require(page+'categories/index.vue').default, meta:{title:"Material"}},
		{ path: '/categories/edit', component: require(page+'categories/edit.vue').default, meta:{title:"Nuevo"}},
		{ path: '/categories/edit/:id', component: require(page+'categories/edit.vue').default, meta:{title:"Editar"}},
		{ path: '/subcategories', component: require(page+'subcategories/index.vue').default, meta:{title:"Caracteristicas"}},
		{ path: '/subcategories/edit', component: require(page+'subcategories/edit.vue').default, meta:{title:"Nuevo"}},
		{ path: '/subcategories/edit/:id', component: require(page+'subcategories/edit.vue').default, meta:{title:"Editar"}},
		{ path: '/products', component: require(page+'products/index.vue').default, meta:{title:"Precios"}},
		{ path: '/products/edit', component: require(page+'products/edit.vue').default, meta:{title:"Nuevo"}},
		{ path: '/products/edit/:id', component: require(page+'products/edit.vue').default, meta:{title:"Editar"}},
		{ path: '/clients', component: require(page+'clients/index.vue').default, meta:{title:"Clientes"}},
		{ path: '/clients/edit', component: require(page+'clients/edit.vue').default, meta:{title:"Nuevo"}},
		{ path: '/clients/edit/:id', component: require(page+'clients/edit.vue').default, meta:{title:"Editar"}},
		{ path: '/clientes-nuevos', component: require(page+'clients/news.vue').default, meta:{title:"Clientes nuevos"}},
		{ path: '/extras', component: require(page+'extras/index.vue').default, meta:{title:"Antireflejantes"}},
		{ path: '/extras/edit', component: require(page+'extras/edit.vue').default, meta:{title:"Nuevo"}},
		{ path: '/extras/edit/:id', component: require(page+'extras/edit.vue').default, meta:{title:"Editar"}},
		{ path: '/orders/new', component: require(page+'orders/new.vue').default, meta:{title:"Nuevo pedido"}},
		{ path: '/search', component: require(page+'search.vue').default, meta:{title:"Buscador"}},
		{ path: '/catalog', component: require(page+'orders/catalog.vue').default, meta:{title:"Catalogo"}},
		{ path: '/sales', component: require(page+'orders/sales.vue').default, meta:{title:"Ventas"}},
		{ path: '/orders/pending', component: require(page+'orders/pendings.vue').default, meta:{title:"Pendientes"}},
		{ path: '/orders', component: require(page+'orders/all_pendings.vue').default, meta:{title:"Pendientes"}},
		{ path: '/orders/payments', component: require(page+'orders/payments.vue').default, meta:{title:"Cobranza"}},
		{ path: '/reports', component: require(page+'reports2.vue').default, meta:{title:"Reportes"}},
        { path: '/status', component: require(page+'status.vue').default, meta:{title:"Status"}},

        //  Reportes -> Cobranza

        { path: '/orders/estado-cuenta', component: require(page+'orders/account.vue').default, meta:{title:"Estado de cuenta"}},
        { path: '/orders/ingresos', component: require(page+'orders/income.vue').default, meta:{title:"Ingresos"}},
        { path: '/orders/deudores', component: require(page+'orders/debtors.vue').default, meta:{title:"Deudores"}},

        { path: '/promociones', component: require(page+'promo.vue').default, meta:{title:"Promociones"}},
        { path: '/consulta', component: require(page + 'customer-query.vue').default, meta: { title: "Consulta de Clientes"}},
        { path: '/registro-de-rx', component: require(page + 'orders-log.vue').default, meta: { title: "Registro de RX"}},
        { path: '/rentabilidad', component: require(page + 'profitability.vue').default, meta: { title: "Rentabilidad"}},

		{ path: '/entradas_salidas', component: require(page+'orders/entradas_salidas.vue').default, meta:{title:"Entradas y salidas"}},

		//Notas
	    { path: '/notes', component: require(page+'notes/index.vue').default, meta:{title:"Notas"}},
	    { path: '/notes/edit', component: require(page+'notes/edit.vue').default, meta:{title:"Nuevo"}},//Cuando no envian parametro
	    { path: '/notes/edit/:id', component: require(page+'notes/edit.vue').default, meta:{title:"Editar"}},//Con parametro
		
		{ path: '/lista-de-precios', component: require(page+ 'lista-de-precios-index.vue').default, meta: { title: "Lista de precios"}},//Con parametro
		{ path: '/lista-de-precios/:id', component: require(page+ 'lista-de-precios.vue').default, meta: { title: "Editar Lista de precio"}},//Con parametro
	]

});

//Titulos del website
import VueDocumentTitlePlugin from "vue-document-title-plugin";
Vue.use(VueDocumentTitlePlugin, MyRouter,
	{ defTitle: "Augen", filter: (title)=>{ return title+" - Augen"; } }
);

// export {routes};
export default MyRouter;
