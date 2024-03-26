<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<style>
			/* Container */
			.container {
				width: 100%;
				max-width: 1200px; /* Adjust this value as needed */
				margin: 0 auto; /* Center the container */
			}

			/* Row */
			.row {
				margin-right: -15px;
				margin-left: -15px;
				/*overflow: hidden;*/ /* Clearfix to contain floats */
			}

			/* Columns */
			[class*="col-"] {
				float: left;
				padding-right: 15px;
				padding-left: 15px;
				min-height: 1px; /* Ensure the column is always visible */
			}

			/* Example column sizes - Adjust the percentages based on the number of columns */
			.col-1 { width: 8.33%; }
			.col-2 { width: 16.66%; }
			.col-3 { width: 25%; }
			.col-4 { width: 33.33%; }
			.col-5 { width: 41.66%; }
			.col-6 { width: 50%; }
			.col-7 { width: 58.33%; }
			.col-8 { width: 66.66%; }
			.col-9 { width: 75%; }
			.col-10 { width: 83.33%; }
			.col-11 { width: 91.66%; }
			.col-12 { width: 100%; }

			/* Clearfix for the row (Optional but recommended) */
			.row::after {
				content: "";
				display: table;
				clear: both;
			}

			table span {
				font-size: 7.5px !important;
			}

			span {
				font-family: Arial, Helvetica, sans-serif;
				font-size: 10px;
				line-height: 0px;
			}

			body{
				font-family: Arial, Helvetica, sans-serif;
				font-size: 12px;
				background-color: white;
			}

			.form-group{
				margin-bottom: 15px;
			}

			.no-gutters {
				margin-right: 0;
				margin-left: 0;
			}
			.no-gutters > .col,
			.no-gutters > [class*=col-] {
				padding-right: 0;
				padding-left: 0;
			}

			.clearfix::after {
				content: ""; 
				clear: both;
				display: table;
			} 
			
		</style>
	</head>
	<body>
		<div class="form-group" style="border-bottom: 2px solid rgb(177, 78, 79);">
			<div class="row no-gutters">
				<div class="col-6">
					<img src="https://dev.augenlabs.com/public/images/logo.png" width="100%">
				</div>
				<div class="col-6" style="text-align: right;">
					<h2><b>PDV Veracruz</b></h2>
					<p><b>LABORATORIO<br/>GUADALAJARA</b></p>
				</div>
			</div>
			<p style="margin: 0; color: rgb(177, 78, 79);">* Estás capturando una <b>receta SAFILO</b>, favor de incluir un <b>armazón SAFILO</b></p>
                        
		</div>
		<p style="text-align: left; margin: 0px;"><b style="color: rgb(177, 78, 79)">CAPTURA DE DATOS</b></p>
		<div class="form-group" style="font-size: 10px;">
			<div class="row no-gutters">
				<div class="col-2" style="">
					<span style="text-align: left;">RX</span><br/>
					<div style="border:1px solid;border-radius:10px;height:25px; text-align: center; margin-top: 5px; margin-right: 5px;"><p style="margin:5px 0 5px 0;padding-left:5px">860914</p></div>
				</div>
				<div class="col-2" style=""> 
					<span style="text-align: left;">FECHA</span><br/>
					<div style="border:1px solid;border-radius:10px;height:25px; text-align: center; margin-top: 5px; margin-right: 5px;"><p style="margin:5px 0 5px 0;padding-left:5px">2023/12/01</p></div>
				</div>
				<div class="col-2" style="">
					<span style="text-align: left;">ID</span><br/>
					<div style="border:1px solid;border-radius:10px;height:25px; text-align: center; margin-top: 5px; margin-right: 5px;"><p style="margin:5px 0 5px 0;padding-left:5px">1525</p></div>
				</div>
				<div class="col-6" style="">
					<span style="text-align: left;">CLIENTE</span><br/>
					<div style="border:1px solid;border-radius:10px;height:25px; text-align: left; margin-top: 5px; margin-right: 5px;"><p style="margin:5px 0 5px 0;padding-left:5px">ELISA FABIANO RUFO</p></div>
				</div>
			</div>
		</div>
		<br/>
		<div class="form-group" style="font-size: 10px;  display: block;">
			<div class="row no-gutters">
				<div class="col-6" style="">
					<span style="text-align: left;">DISEÑO</span><br/>
					<div style="border:1px solid;border-radius:10px;height:25px; text-align: left; margin-top: 5px; margin-right: 5px;"><p style="margin:5px 0 5px 0;padding-left:5px">TRINITY 13-17 HYPERSOFT</p></div>
				</div>
				
				<div class="col-6" style="">
					<span style="text-align: left;">MATERIAL</span><br/>
					<div style="border:1px solid;border-radius:10px;height:25px; text-align: left; margin-top: 5px; margin-right: 5px;"><p style="margin:5px 0 5px 0;padding-left:5px">CR-39</p></div>
				</div>
			</div>
		</div>
		<div class="form-group" style="font-size: 10px; display: block;">
			<div class="row no-gutters">
				<div class="col-6" style="">
					<span style="text-align: left;">TIPO AR</span><br/>
					<div style="border:1px solid;border-radius:10px;height:25px; text-align: left; margin-top: 5px; margin-right: 5px;"><p style="margin:5px 0 5px 0;padding-left:5px">AZUL</p></div>
				</div>
				
				<div class="col-6" style="">
					<span style="text-align: left;">TALLADO</span><br/>
					<div style="border:1px solid;border-radius:10px;height:25px; text-align: left; margin-top: 5px; margin-right: 5px;"><p style="margin:5px 0 5px 0;padding-left:5px">FREE FORM</p></div>
				</div>
			</div>
		</div>
		<div class="form-group" style="font-size: 10px; display: block;">
			<div class="row no-gutters">
				<div class="col-12" style="">
					<span style="text-align: left;">SERVICIO</span><br/>
					<div style="border:1px solid;border-radius:10px;height:25px; text-align: left; margin-top: 5px; margin-right: 5px;"><p style="margin:5px 0 5px 0;padding-left:5px">CORTE Y MONTE</p></div>
				</div>
			</div>
		</div>
		<p style="text-align: left; margin: 0px;"><b>ARMAZÓN</b></p>
		<div class="container" style="display: block;">
			<div class="row no-gutters">
				<div class="col-6" style="padding: 5px;">
					<div style="border: 2px solid #000; border-radius: 10px;">
						<div style="background-color:#f8f8f8;">
							<div class="position: relative;  display: inline; width: 100%;">
								<table style="width: 100%; border: 0px;">
									<tr>
										<td style="text-align: left; width: 60%;">
											<span style="text-align: left;">TIPO DE ARMAZÓN</span><br/>
											<div style="border:1px solid; background-color:#f8f8f8; border-radius:10px; height:25px; background-color: #fff; margin-top: 5px;"><p style="margin-top: 5px;"></p></div>
										</td>
										<td style="text-align: left; width: 40%;">
											<span style="text-align: left;">HORIZONTAL "A"</span><br/>
											<div style="border:1px solid; background-color:#f8f8f8; border-radius:10px; height:25px; background-color: #fff; margin-top: 5px;"><p style="margin-top: 5px;"></p></div>
										</td>
									</tr>
								</table>
								<table style="width: 100%; border: 0px;">
									<tr>
										<td style="text-align: left; width: 33.33%;">
											<span style="text-align: left;">VERICAL "B"</span><br/>
											<div style="border:1px solid; background-color:#f8f8f8; border-radius:10px; height:25px; background-color: #fff; margin-top: 5px;"><p style="margin-top: 5px;"></p></div>
										</td>
										<td style="text-align: left; width: 33.33%;">
											<span style="text-align: left;">DIAGONAL "ED"</span><br/>
											<div style="border:1px solid; background-color:#f8f8f8; border-radius:10px; height:25px; background-color: #fff; margin-top: 5px;"><p style="margin-top: 5px;"></p></div>
										</td>
										<td style="text-align: left; width: 33.33%;">
											<span style="text-align: left;">PUENTE</span><br/>
											<div style="border:1px solid; background-color:#f8f8f8; border-radius:10px; height:25px; background-color: #fff; margin-top: 5px;"><p style="margin-top: 5px;"></p></div>
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div>
				<div class="col-6" style="padding: 5px;">
					<div style="border-radius: 10px;">
						<div>
							<div class="position: relative;  display: inline; width: 100%;">
								<table style="width: 100%; border: 0px;">
									<tr>
										<td style="text-align: left; width: 50%;">
											<span style="text-align: left;">ÁNGULO FACIL</span><br/>
											<div style="border:1px solid; border-radius:10px; height:25px; background-color: #fff; margin-top: 5px;"><p style="margin-top: 5px;"></p></div>
										</td>
										<td style="text-align: left; width: 50%;">
											<span style="text-align: left;">ÁNGULO PANTOSCÓPICO</span><br/>
											<div style="border:1px solid; background-color:#f8f8f8; border-radius:10px; height:25px; background-color: #fff; margin-top: 5px;"><p style="margin-top: 5px;"></p></div>
										</td>
									</tr>
								</table>
								<table style="width: 100%; border: 0px;">
									<tr>
										<td style="text-align: left; width: 20%;">
											<span style="text-align: left;">VÉRTICE</span><br/>
											<div style="border:1px solid; background-color:#f8f8f8; border-radius:10px; height:25px; background-color: #fff; margin-top: 5px;"><p style="margin-top: 5px;"></p></div>
										</td>
										<td style="text-align: left; width: 40%;">
											<span style="text-align: left;">B MICA IZQUIERDA</span><br/>
											<div style="border:1px solid; background-color:#f8f8f8; border-radius:10px; height:25px; background-color: #fff; margin-top: 5px;"><p style="margin-top: 5px;"></p></div>
										</td>
										<td style="text-align: left; width: 40%;">
											<span style="text-align: left;">B MICA DERECHA</span><br/>
											<div style="border:1px solid; background-color:#f8f8f8; border-radius:10px; height:25px; background-color: #fff; margin-top: 5px;"><p style="margin-top: 5px;"></p></div>
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
