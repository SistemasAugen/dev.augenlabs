<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<style>

		span{
			
			font-family: Arial, Helvetica, sans-serif;
			font-size: 9px;
		}
		body{
			
			font-family: Arial, Helvetica, sans-serif;
			font-size: 12px;
			background-color: white;
			
		}

		.form-group{
			margin-bottom:15px;
		}
		.col-sm-1{
			width:14.9%;
			position: relative;
			min-height: 1px;
			padding-right: 10px;
			float:left;
		}	
		.col-sm-2{
			width:18.3%;
			position: relative;
			min-height: 1px;
			padding-right: 10px;
			float:left;
		}	
		.col-sm-3{
			width:23%;
			position: relative;
			min-height: 1px;
			padding-right: 10px;
			float:left;
		}
		.col-sm-4{
			width:33.33333333%;
			position: relative;
			min-height: 1px;
			padding-right: 10px;
			float:left;
		}
		.col-sm-6{
			width:49%;
			position: relative;
			min-height: 1px;
			padding-right: 10px;
			float:left;
		}
		
		.col-sm-12{
			width:100.5%;
			position: relative;
			min-height: 1px;
			padding-right: 10px;
			float:left;
		}	
		

	
	</style>
</head>
<body >
	<div class="form-group">	
		<div class="col-sm-6" style="text-align: left;">
            <img src="https://dev.augenlabs.com/public/images/logo.png" width="45%">
            <div style="font-size: 25px;display: inline-block;padding-left: 10px;">|</div>
        </div>
        <div class="col-sm-3"></div>
        <div class="col-sm-3" style="text-align: right;">
            <h2><b>{{ $inputs['pvd'] }}</b></h2>
            <p><b>{{ $inputs['laboratory'] }}</b></p>
        </div>
    </div>
    <br><br><br><br><br><br><br><hr>

    <p style="text-align: left;"><b>CAPTURA DE DATOS</b></p>
	<div class="form-group">
						
		<div class="col-sm-3" style="">
            <span style="text-align: left;">RX:</span><br>
			<div style="border:1px solid;background-color:#f8f8f8;border-radius:3px;height:25px"><p style="margin:5px 0 5px 0;padding-left:5px">{{ $inputs['rx_rx'] }}</p></div>
		</div>
		<div class="col-sm-3" style="">
            <span style="text-align: left;">FECHA:</span><br>
			<div style="border:1px solid;background-color:#f8f8f8;border-radius:3px;height:25px"><p style="margin:5px 0 5px 0;padding-left:5px">{{ $inputs['rx_fecha'] }}</p></div>
		</div>
		<div class="col-sm-6" style="">
            <span style="text-align: left;">CLIENTE:</span><br>
			<div style="border:1px solid;background-color:#f8f8f8;border-radius:3px;height:25px"><p style="margin:5px 0 5px 0;padding-left:5px">{{ $inputs['rx_cliente'] }}</p></div>
		</div>

	</div>
    
    <br>
    <p style="text-align: left;"><b>GRADUACION</b></p>
        
    <div class="form-group">
		<div class="col-sm-1">
            <span style="text-align: left;">OD ESFERA:</span><br>
			<div style="border:1px solid;background-color:#f8f8f8;border-radius:3px;height:25px"><p style="margin:5px 0 5px 0;padding-left:5px">{{ $inputs['rx_od_esfera'] }}</p></div>
		</div>
		
		<div class="col-sm-1">
            <span style="text-align: left;">OD CILINDRO:</span><br>
			<div style="border:1px solid;background-color:#f8f8f8;border-radius:3px;height:25px"><p style="margin:5px 0 5px 0;padding-left:5px">{{ $inputs['rx_od_cilindro'] }}</p></div>
		</div>

		<div class="col-sm-1">
            <span style="text-align: left;">OD EJE:</span><br>
			<div style="border:1px solid;background-color:#f8f8f8;border-radius:3px;height:25px"><p style="margin:5px 0 5px 0;padding-left:5px">{{ $inputs['rx_od_eje'] }}</p></div>
		</div>

		
		<div class="col-sm-1">
            <span style="text-align: left;">OD ADICION:</span><br>
			<div style="border:1px solid;background-color:#f8f8f8;border-radius:3px;height:25px"><p style="margin:5px 0 5px 0;padding-left:5px">{{ $inputs['rx_od_adicion'] }}</p></div>
		</div>
		
		<div class="col-sm-1">
            <span style="text-align: left;">OD DIP:</span><br>
			<div style="border:1px solid;background-color:#f8f8f8;border-radius:3px;height:25px"><p style="margin:5px 0 5px 0;padding-left:5px">{{ $inputs['rx_od_dip'] }}</p></div>
		</div>
        
		<div class="col-sm-1">
            <span style="text-align: left;">OD ALTURA:</span><br>
			<div style="border:1px solid;background-color:#f8f8f8;border-radius:3px;height:25px"><p style="margin:5px 0 5px 0;padding-left:5px">{{ $inputs['rx_od_altura'] }}</p></div>
		</div>

    </div>
	<br><br><br><br>
    <div class="form-group">
		<div class="col-sm-1" style="">
            <span style="text-align: left;">OD ESFERA:</span><br>
			<div style="border:1px solid;background-color:#f8f8f8;border-radius:3px;height:25px"><p style="margin:5px 0 5px 0;padding-left:5px">{{ $inputs['rx_od_esfera_dos'] }}</p></div>
		</div>
		
		<div class="col-sm-1" style="">
            <span style="text-align: left;">OD CILINDRO:</span><br>
			<div style="border:1px solid;background-color:#f8f8f8;border-radius:3px;height:25px"><p style="margin:5px 0 5px 0;padding-left:5px">{{ $inputs['rx_od_cilindro_dos'] }}</p></div>
		</div>

		<div class="col-sm-1" style="">
            <span style="text-align: left;">OD EJE:</span><br>
			<div style="border:1px solid;background-color:#f8f8f8;border-radius:3px;height:25px"><p style="margin:5px 0 5px 0;padding-left:5px">{{ $inputs['rx_od_eje_dos'] }}</p></div>
		</div>

		
		<div class="col-sm-1" style="">
            <span style="text-align: left;">OD ADICION:</span><br>
			<div style="border:1px solid;background-color:#f8f8f8;border-radius:3px;height:25px"><p style="margin:5px 0 5px 0;padding-left:5px">{{ $inputs['rx_od_adicion_dos'] }}</p></div>
		</div>
		
		<div class="col-sm-1" style="">
            <span style="text-align: left;">OD DIP:</span><br>
			<div style="border:1px solid;background-color:#f8f8f8;border-radius:3px;height:25px"><p style="margin:5px 0 5px 0;padding-left:5px">{{ $inputs['rx_od_dip_dos'] }}</p></div>
		</div>
        
		<div class="col-sm-1" style="">
            <span style="text-align: left;">OD ALTURA:</span><br>
			<div style="border:1px solid;background-color:#f8f8f8;border-radius:3px;height:25px"><p style="margin:5px 0 5px 0;padding-left:5px">{{ $inputs['rx_od_altura_dos'] }}</p></div>
		</div>
    </div>
    <br><br><br>
    <div class="form-group">

		<div class="col-sm-6" style="">
            <span style="text-align: left;">DISEÑO:</span><br>
			<div style="border:1px solid;background-color:#f8f8f8;border-radius:3px;height:25px"><p style="margin:5px 0 5px 0;padding-left:5px">{{ $inputs['rx_diseno'] }}</p></div>
		</div>

		<div class="col-sm-6" style="">
            <span style="text-align: left;">MATERIAL:</span><br>
			<div style="border:1px solid;background-color:#f8f8f8;border-radius:3px;height:25px"><p style="margin:5px 0 5px 0;padding-left:5px">{{ $inputs['rx_material'] }}</p></div>
		</div>
    </div>
    <br><br><br><br>
    <div class="form-group">

		<div class="col-sm-6" style="">
            <span style="text-align: left;">TIPO AR:</span><br>
			<div style="border:1px solid;background-color:#f8f8f8;border-radius:3px;height:25px"><p style="margin:5px 0 5px 0;padding-left:5px">{{ $inputs['rx_tipo_ar'] }}</p></div>
		</div>

		<div class="col-sm-6" style="">
            <span style="text-align: left;">TALLADO:</span><br>
			<div style="border:1px solid;background-color:#f8f8f8;border-radius:3px;height:25px"><p style="margin:5px 0 5px 0;padding-left:5px">{{ $inputs['rx_tallado'] }}</p></div>
		</div>
    </div>
    
    <br><br>
    <p style="text-align: left;"><b>SERVICIOS</b></p>
    <div class="form-group">

		<div class="col-sm-12" style="">
			<div style="border:1px solid;background-color:#f8f8f8;border-radius:3px;height:25px"><p style="margin:5px 0 5px 0;padding-left:5px">{{ $inputs['rx_servicios'] }}</p></div>
		</div>

    </div>

        
 
	<br><br>
    <p style="text-align: left;"><b>ARMAZÓN</b></p>
    <div class="form-group">

		<div class="col-sm-2" >
            <span style="text-align: left;">TIPO DE ARMAZÓN:</span><br>
			<div style="border:1px solid;background-color:#f8f8f8;border-radius:3px;height:25px;margin-top:4px"><p style="margin:5px 0 5px 0;padding-left:5px">{{ $inputs['rx_tipo_armazon'] }}</p></div>
		</div>

		<div class="col-sm-2" style="">
            <span style="text-align: left;">HORIZONTAL "A"</span><br><br>
			<div style="border:1px solid;background-color:#f8f8f8;border-radius:3px;height:25px"><p style="margin:5px 0 5px 0;padding-left:5px">{{ $inputs['rx_horizontal_a'] }}</p></div>
		</div>

		<div class="col-sm-2" style="">
            <span style="text-align: left;">VERTICAL "B"</span><br><br>
			<div style="border:1px solid;background-color:#f8f8f8;border-radius:3px;height:25px"><p style="margin:5px 0 5px 0;padding-left:5px">{{ $inputs['rx_vertical_b'] }}</p></div>
		</div>

		<div class="col-sm-2" style="">
            <span style="text-align: left;">DIAGONAL "ED"</span><br><br>
			<div style="border:1px solid;background-color:#f8f8f8;border-radius:3px;height:25px"><p style="margin:5px 0 5px 0;padding-left:5px">{{ $inputs['rx_diagonal_ed'] }}</p></div>
		</div>

		<div class="col-sm-2" style="">
            <span style="text-align: left;">PUENTE</span><br><br>
			<div style="border:1px solid;background-color:#f8f8f8;border-radius:3px;height:25px"><p style="margin:5px 0 5px 0;padding-left:5px">{{ $inputs['rx_puente'] }}</p></div>
		</div>

    </div>


	<br><br><br><br>
    <p style="text-align: left;"><b>OBSERVACIONES</b></p>
    <div class="form-group">

		<div class="col-sm-12" style="">
			<div style="border:1px solid;background-color:#f8f8f8;border-radius:3px;height:25px"><p style="margin:5px 0 5px 0;padding-left:5px">{{ $inputs['rx_observaciones'] }}</p></div>
		</div>
    </div>

		
</body>

</html>
