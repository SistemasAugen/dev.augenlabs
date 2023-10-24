<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {    
            $table->string('rx_rx');	
            $table->string('rx_fecha');	
            $table->string('rx_cliente');	
            $table->string('rx_od_esfera');	
            $table->string('rx_od_cilindro');	
            $table->string('rx_od_eje');	
            $table->string('rx_od_adicion');	
            $table->string('rx_od_dip');	
            $table->string('rx_od_altura');	
            $table->string('rx_od_esfera_dos');	
            $table->string('rx_od_cilindro_dos');	
            $table->string('rx_od_eje_dos');	
            $table->string('rx_od_adicion_dos');	
            $table->string('rx_od_dip_dos');	
            $table->string('rx_od_altura_dos');	
            $table->string('rx_diseno');	
            $table->string('rx_material');	
            $table->string('rx_caracteristicas');	
            $table->string('rx_tipo_ar');	
            $table->string('rx_tallado');	
            $table->string('rx_servicios');	
            $table->string('rx_tipo_armazon');	
            $table->string('rx_horizontal_a');	
            $table->string('rx_vertical_b');	
            $table->string('rx_diagonal_ed');	
            $table->string('rx_puente');	
            $table->string('rx_observaciones');	
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('rx_rx');	
            $table->dropColumn('rx_fecha');	
            $table->dropColumn('rx_cliente');	
            $table->dropColumn('rx_od_esfera');	
            $table->dropColumn('rx_od_cilindro');	
            $table->dropColumn('rx_od_eje');	
            $table->dropColumn('rx_od_adicion');	
            $table->dropColumn('rx_od_dip');	
            $table->dropColumn('rx_od_altura');	
            $table->dropColumn('rx_od_esfera_dos');	
            $table->dropColumn('rx_od_cilindro_dos');	
            $table->dropColumn('rx_od_eje_dos');	
            $table->dropColumn('rx_od_adicion_dos');	
            $table->dropColumn('rx_od_dip_dos');	
            $table->dropColumn('rx_od_altura_dos');	
            $table->dropColumn('rx_diseno');	
            $table->dropColumn('rx_material');	
            $table->dropColumn('rx_caracteristicas');	
            $table->dropColumn('rx_tipo_ar');	
            $table->dropColumn('rx_tallado');	
            $table->dropColumn('rx_servicios');	
            $table->dropColumn('rx_tipo_armazon');	
            $table->dropColumn('rx_horizontal_a');	
            $table->dropColumn('rx_vertical_b');	
            $table->dropColumn('rx_diagonal_ed');	
            $table->dropColumn('rx_puente');	
            $table->dropColumn('rx_observaciones');
        });
    }
}
