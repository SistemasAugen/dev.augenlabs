<template>
	<div class="sidebar-menu">

		<div class="sidebar-menu-inner">
			
			<header class="logo-env">

				<!-- logo -->
				<div class="logo">
					<router-link to="/home">
						<img :src="logo()" width="120" alt="" />
					</router-link>
				</div>

				<!-- logo collapse icon -->
				<div class="sidebar-collapse">
					<a href="#" class="sidebar-collapse-icon"><!-- add class "with-animation" if you want sidebar to have animation during expanding/collapsing transition -->
						<i class="entypo-menu"></i>
					</a>
				</div>

								
				<!-- open/close menu icon (do not remove if you want to enable menu on mobile devices) -->
				<div class="sidebar-mobile-menu visible-xs">
					<a href="#" class="with-animation"><!-- add class "with-animation" to support animation -->
						<i class="entypo-menu"></i>
					</a>
				</div>

			</header>
			
									
			<ul id="main-menu" class="main-menu">
				<!-- add class "multiple-expanded" to allow multiple submenus to open -->
				<!-- class "auto-inherit-active-class" will automatically add "active" class for parent elements who are marked already with class "active" -->
				<li v-for="item in menu" :class="{'has-sub':item.sub_menus.length>0}">
					<a v-if="item.sub_menus.length>0">
						<i :class="item.icon"></i>
						<span class="title">{{item.name}}</span>
					</a>
					<router-link :to="item.route" v-else>
						<i :class="item.icon"></i>
						<span class="title">{{item.name}}</span>
					</router-link>
					<ul v-if="item.sub_menus.length>0">
						<li v-for="sub in item.sub_menus">
							<router-link :to="sub.route">
								<span class="title">{{sub.name}}</span>
							</router-link>
						</li>						
					</ul>
				</li>
				
			</ul>
			
		</div>
	</div>
</template>

<script type="text/javascript">
	export default {
		data(){
			return {
				menu:{}
			}
		},
		methods:{
			mountMenu(){
				window.public_vars.$body	 	 	= $("body");
				window.public_vars.$pageContainer  = public_vars.$body.find(".page-container");
				window.public_vars.$chat 			= public_vars.$pageContainer.find('#chat');
				window.public_vars.$horizontalMenu = public_vars.$pageContainer.find('header.navbar');
				window.public_vars.$sidebarMenu	= public_vars.$pageContainer.find('.sidebar-menu');
				window.public_vars.$mainMenu	    = public_vars.$sidebarMenu.find('#main-menu');
				window.public_vars.$mainContent	= public_vars.$pageContainer.find('.main-content');
				window.public_vars.$sidebarUserEnv = public_vars.$sidebarMenu.find('.sidebar-user-info');
				window.public_vars.$sidebarUser 	= public_vars.$sidebarUserEnv.find('.user-link');

				setup_sidebar_menu();
				// Horizontal Menu Setup
				setup_horizontal_menu();
				//Funciones extras de Neon
				runExtras();
			},
			getMenus(){
				axios.get(tools.url("/api/menu")).then((response)=>{
			    	this.menu=response.data;
			    	this.menu.sort((a,b)=>{
			    		return a.position-b.position;
			    	});
					this.menu.forEach((v,k) => {
						this.menu[k].sub_menus.sort((a,b)=>{
							return a.position-b.position;
						});
					});
			    	setTimeout(()=>{
			    		this.mountMenu();
			    	},2000);
			    	
			    }).catch((error)=>{
			        this.error=error.response.data.error;			        
			        this.inPetition=false;
			    });
				
			},
			logo(){
				return window.tools.url('/public/images/logo.png');
			}
		},
        mounted() {
        	this.getMenus();
            console.log('Menu mounted.');
        }
    }
</script>
<style>
.sidebar-menu{
	background-color:silver !important;
	border-radius: 0px 50px 50px 0px;
}
.page-container .sidebar-menu #main-menu li a{
	color:black;
}
.page-container .sidebar-menu #main-menu li ul > li > a{
	background-color:darkgrey !important;
}
</style>
