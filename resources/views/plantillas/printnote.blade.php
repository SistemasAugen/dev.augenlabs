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
			font-size: 9px;
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
		.col-sm-7{
			width:58.33333333%;
			position: relative;
			min-height: 1px;
			padding-left: 15px;
			padding-right: 15px;
			float:left;
		}
		.col-sm-8{
			width:66.66666667%;
			position: relative;
			min-height: 1px;
			padding-left: 15px;
			padding-right: 15px;
			float:left;
		}
		.col-sm-9{
			width:75%;
			position: relative;
			min-height: 1px;
			padding-left: 15px;
			padding-right: 15px;
			float:left;
		}
		.col-sm-12{
			width:100%;
			position: relative;
			min-height: 1px;
			padding-left: 15px;
			padding-right: 15px;
			float:left;
		}
		
	
		.row{
			margin-left: -10px;
    		margin-right: -10px;
		}
		
		#tablemodels{
			font-family: arial, sans-serif;
			border-collapse: separate !important;
			border-spacing:20px;

			width: 100%;
		}

		#tablemodels td {
			border-bottom: 1px solid;
			text-align: left;
			padding: 1px 5px 1px 5px;
			text-align: center;
		}
		#tablemodels th {
			text-align: center;
			border: 1px solid;
			padding: 5px 5px 5px 5px;
			/* background-color: #dddddd; */
			border-radius: 10px 10px 0 0;
		}
	
	</style>
</head>
<body>
	
	<div class="row">
		<div class="col-sm-7" style="">
			<img src="https://sistema.augenlabs.com/public/images/logo.png" width="150">
		</div>
		
							
				
		<div class="col-sm-4">
			<span><b>Estado de cuenta N° </b></span> <u > {{ $inputs['account_status'] }}</u><br>
			
			<span><b>Semana </b></span> <u > {{ $inputs['wekeend'] }}</u>
		
		</div>
	</div>
	<br><br><br><br><br>

	<div class="row">
		<div class="col-sm-12">
			<label>Doctor / Óptica:</label>
			<div style="border:1px solid;border-radius:8px;text-align: center;"><p>{{ $inputs['customer_name'] }}</p></div>	
			
		</div>
	</div>

	<div class="row" style="padding-top:50px">
		<table id="tablemodels" style="">
			<tr>
				<th style="width: 20%;">Fecha</th>
				<th style="width: 20%;">RX´S</th>
				<th style="width: 45%;">Descripción</th>
				<th style="width: 35%;">Importe</th>
			</tr>
			@foreach ($inputs['rxs'] as $rx)
				<tr>
					<td><p>{{ $rx['rx_fecha'] }}</p></td>
					<td><p>{{ $rx['rx'] }}</p></td>
					<td><p>{{ $rx['description'] }}</p></td>
					<td><p>$ {{ number_format($rx['total'],2) }}</p></td>
				</tr>
			@endforeach
			<tr>
				<td colspan="3" style="border:none;text-align: left;"><p style="padding-top: 4px;text-align: right;"><b>Total: $</b></p></td>
				
				<th style="border-radius: 13px 13px 13px 13px" >$  {{ number_format($inputs['total'],2)}}</th>
			</tr>
		</table>
	</div>
				
	
	<div class="row">
		<div class="col-sm-12">
			<label>OBSERVACIONES:</label>
			@if($inputs['observations'] != null)
				<div style="border:1px solid;border-radius:8px;text-align: center;"><p>{{ $inputs['observations'] }}</p></div>	
			@else
				<div style="border:1px solid;border-radius:8px;text-align: center;height:20px"></div>
			@endif
		</div>
	</div>
	
	<br><br><br><br><br>
    <div class="row">
		<div class="col-sm-12" style="text-align:right">
			<img src="https://sistema.augenlabs.com/public/images/logo.png" width="100">
		</div>
	</div>
		
</body>

</html>
