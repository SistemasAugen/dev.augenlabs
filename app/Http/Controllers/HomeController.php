<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Exports\BalanceExport;
use App\Exports\DebtorsExport;
use App\Exports\ClientsExport;
use App\Exports\StatusExport;
use App\Imports\ClientsImport;
use App\Branch;
use App\Order;
use App\Client;
use App\User;
use App\Category;
use App\Product;
use App\Purchase;
use App\Notification;
use App\ProductHasSubcategory;
use Zipper;
use Excel;
use PDF;

/**
*
*/
class HomeController extends Controller {
	public static $unwantedArray = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
                            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
                            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
                            'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y',
                            '&#225;'=>'a', '&#233;'=>'e', '&#237;'=>'i', '&#243;'=>'o', '&#250;'=>'u',
                            '&#193;'=>'A', '&#201;'=>'E', '&#205;'=>'I', '&#211;'=>'O', '&#218;'=>'U',
                            '&#209;'=>'N', '&#241;'=>'n', '.' => '', ' ' => '_');
	public function home(){
		return view('page');
	}

	public function admin(Request $request){
		ini_set('memory_limit',-1);
		if($request->query('action') == 'all_ok') {
			@file_put_contents('./input.txt', json_encode($request->query()), FILE_APPEND);
		} else {
			return view('admin');
		}
	}

	public function account($id, Request $request) {
		$typeOfReport = $request->input('type');
		ini_set('memory_limit',-1);
		// Clear all export folder
		foreach (glob(public_path('/documents/*')) as $file) {
			if(file_exists($file)){
			   //Use the unlink function to delete the file.
			   unlink($file);
   			}
		}
		$documentstoAdd = $this->_generateReports($id, $typeOfReport);

		$headers = ["Content-Type" => "application/zip"];
		$branch = Branch::findOrFail($id);
		$branchName = $branch->name;
		$branchName = str_replace('PDV ', '', $branchName);
		if($typeOfReport == 'credit')
			$fileName = 'EdoCuenta_PLAZO_' . $branchName . date('Y-m-d') . '.zip';
		else
			$fileName = 'EdoCuenta_CONTADO_' . $branchName . date('Y-m-d') . '.zip';

		Zipper::make(public_path('/documents/' . $fileName))
					->add($documentstoAdd)
					->close();

		return response()->download(public_path('/documents/'.$fileName),$fileName, $headers);
	}

	private function _generateReports($id, $typeOfReport) {
		ini_set('memory_limit',-1);
		$branch = Branch::findOrFail($id);

		// Remote old existing reports
		$folder = storage_path('app/documents/branch/' . $id);
		// First we check if the folder already exists
		if(!is_dir($folder)) {
			mkdir($folder, 0777);
		}

		foreach (glob($folder . '/*') as $file) {
			if(is_file($file)){
			   //Use the unlink function to delete the file.
			   unlink($file);
   			}
		}

		foreach ($branch->clients as $client) {
			$clientName = $client->name;
			$clientName = $this->clearString($clientName);

			$fileName = $client->id . '-' . $clientName . '.xlsx';

			// $handle = fopen($folder . '/' . $fileName, 'w') or die('Cannot open file:  '. $fileName); //implicitly creates file
			// $data = DB::table('orders');
			// dd($data);
			$data = [];
			if($typeOfReport == 'credit' && intval($client->payment_plan) == 0) continue;
			if($typeOfReport == 'cash' && intval($client->payment_plan) != 0) continue;
			$payments = Order::where("status","entregado")
                            ->where("payed", false)
							->where('client_id', $client->id)
							// ->whereRaw(sprintf('ROUND(DATEDIFF(DATE(NOW()), IFNULL(delivered_date, created_at))/7, 0) <= 0'))
							->get();

            // $consult = $consult->orderByRaw('cast(rx as unsigned)');
			if(count($payments) == 0) continue;

            foreach ($payments as $order) {
				$order->productHas;
				$orderExtras = [];
				foreach ($order->extras as $extra) {
					$orderExtras[] = $extra->name;
				}

				if(isset($order->promo_discount))
					$total = floatval($order->promo_discount);
				else
					$total = floatval($order->total) - floatval($order->discount) - floatval($order->discount_admin) + floatval($order->iva);

				$taxToApply = (in_array($order->laboratory_id, [2, 10]) ? 0.08 : 0.16);
				$partialTax = ($total / (1 + $taxToApply)) * $taxToApply;

				$data[] = [
		            'rx' => $order->rx,
		        	'fecha' => $order->created_at,
		        	'semanas_restantes' => $order->passed,
		        	'diseno' => isset($order->product['name']) ? $order->product['name'] : 'NO DISPONIBLE',
					'material' => isset($order->product['subcategory_name']) ? $order->product['subcategory_name'] : 'NO DISPONIBLE',
					'caracteristica' => isset($order->product['type_name']) ? $order->product['type_name'] : 'NO DISPONIBLE',
		        	'antireflejante' => implode(', ', $orderExtras),
		        	'plazo' => intval(@$order->payment_plan ?: '0'),
		        	'subtotal' => $order->total,
					'iva' => $partialTax,
		        	'descuentos_sistema' => $order->discount,
		        	'descuento' =>  floatval($order->discount_admin) / (in_array($order->laboratory_id, [2, 10]) ? 1.08 : 1.16),
		        	'total' => $total,
		        ];
            }

			usort($data, function($aRx, $bRx) {
				if($aRx['semanas_restantes'] < $bRx['semanas_restantes'])
					return -1;
				if($aRx['semanas_restantes'] > $bRx['semanas_restantes'])
					return 1;

				return 0;
			});

			Excel::store(new BalanceExport($data, $client), 'documents/branch/' . $id . '/' . $fileName);
			// return Excel::download(new BalanceExport($data, $client), 'test.xlsx');
		}
		return glob($folder . '/*');
	}

	private function clearString($str) {
		return strtr($str, self::$unwantedArray);
	}

	public function income(Request $request) {
		ini_set('memory_limit',-1);
		$typeOf 	= $request->query('type');
		$startDate 	= $request->query('start_date');
		$endDate 	= $request->query('end_date');

		$branches = Branch::all();

		foreach($branches as $branch) {
			// if($branch->id != 5) continue;
			$partialAmount = 0.0;

			$begin = new \DateTime($startDate);
			$end = new \DateTime($endDate);

			for($i = $begin; $i <= $end; $i->modify('+1 day')) {
				$date = $i->format("Y-m-d");

				$orders = $branch->orders()
								 ->where('status', 'pagado')
							     ->whereRaw("DATE(payment_date) = '$date'")
								 ->get();

				dd($orders);

				foreach ($orders as $order) {
					$client = $order->client;
					$weekPassed = $this->calcFrom($order, $date, $client->payment_plan);
					if($weekPassed == 0 && $client->payment_plan > 0) {
						$partialAmount += round(floatval($order->total) - floatval($order->discount) - floatval($order->discount_admin) + floatval($order->iva), 2);
						echo $client->name . ' ' . number_format($partialAmount, 2) . ' ' . $order->delivered_date  . ' ' . $client->payment_plan  .   PHP_EOL;
					}
				}
			}
		}
		die;

		if($typeOf == 'CONTADO')
			$clients = Client::where('payment_plan', 0)->orderBy('branch_id', 'ASC')->get();
		else
			$clients = Client::where('payment_plan', '>', 0)->orderBy('branch_id', 'ASC')->get();

		$data = [];
		$totalAmount = 0.0;

		foreach ($clients as $client) {
			$branchName = $client->branch->name;
			$branchName = str_replace('PDV ', '', $branchName);

			$clientId 	= $client->id;
			$clientName = $client->name;
			$partialAmount = 0.0;

			$begin = new \DateTime($startDate);
			$end = new \DateTime($endDate);

			for($i = $begin; $i <= $end; $i->modify('+1 day')) {
				$date = $i->format("Y-m-d");
				$orders = $client->orders()
								 ->where('payment_date', $date)
								 ->get();

				if(count($orders) == 0) continue;

				switch ($typeOf) {
					case 'EDO_CUENTA':
					case 'CONTADO':
						foreach ($orders as $order) {
							$weekPassed = $this->calcFrom($order, $date, $client->payment_plan);
							if($weekPassed == 0) {
								$partialAmount += round(floatval($order->total) - floatval($order->discount) - floatval($order->discount_admin) + floatval($order->iva), 2);
							}
						}
						break;
					case 'COBRANZA':
						// $query = DB::table('orders')
						// 			 ->select(DB::raw('SUM(total) AS total, SUM(discount) AS discount, SUM(discount_admin) AS discount_admin, SUM(iva) AS iva'))
						// 			 ->where('client_id', $clientId)
						// 			 ->whereRaw(sprintf('ROUND(DATEDIFF(DATE(NOW()), IFNULL(delivered_date, created_at))/7, 0) IN (-1, -2, -3)'))
						// 			 ->whereBetween('payment_date', [ $startDate, $endDate ])
						// 			 ->groupBy('client_id')
						// 			 ->first();
						if(count($orders) > 0) {
							foreach ($orders as $order) {
								if(in_array($order->passed, [-1, -2, -3])) {
									$partialAmount += round(floatval($order->total) - floatval($order->discount) - floatval($order->discount_admin) + floatval($order->iva), 2);
								}
							}
						}
						break;
					case 'VENCIDO':
						// $query = DB::table('orders')
						// 			 ->select(DB::raw('SUM(total) AS total, SUM(discount) AS discount, SUM(discount_admin) AS discount_admin, SUM(iva) AS iva'))
						// 			 ->where('client_id', $clientId)
						// 			 ->whereRaw(sprintf('ROUND(DATEDIFF(DATE(NOW()), IFNULL(delivered_date, created_at))/7, 0) <= -4'))
						// 			 ->whereBetween('payment_date', [ $startDate, $endDate ])
						// 			 ->groupBy('client_id')
						// 			 ->first();
						if(count($orders) > 0) {
							foreach ($orders as $order) {
								if($order->passed <= -4) {
									$partialAmount += round(floatval($order->total) - floatval($order->discount) - floatval($order->discount_admin) + floatval($order->iva), 2);
								}
							}
						}
						break;
					// case 'CONTADO':
					// 	// $query = DB::table('orders')
					// 	// 			 ->select(DB::raw('SUM(total) AS total, SUM(discount) AS discount, SUM(discount_admin) AS discount_admin, SUM(iva) AS iva'))
					// 	// 			 ->where('client_id', $clientId)
					// 	// 			 ->whereRaw(sprintf('ROUND(DATEDIFF(DATE(NOW()), IFNULL(delivered_date, created_at))/7, 0) = 0'))
					// 	// 			 ->whereBetween('payment_date', [ $startDate, $endDate ])
					// 	// 			 ->groupBy('client_id')
					// 	// 			 ->first();
					// 	if(count($orders) > 0) {
					// 		foreach ($orders as $order) {
					// 			if($order->passed == 0) {
					// 				$partialAmount += round(floatval($order->total) - floatval($order->discount) - floatval($order->discount_admin) + floatval($order->iva), 2);
					// 			}
					// 		}
					// 	}
					// 	break;
					case 'ANTICIPO':
						// $query = DB::table('orders')
						// 			 ->select(DB::raw('SUM(total) AS total, SUM(discount) AS discount, SUM(discount_admin) AS discount_admin, SUM(iva) AS iva'))
						// 			 ->where('client_id', $clientId)
						// 			 ->whereRaw(sprintf('ROUND(DATEDIFF(DATE(NOW()), IFNULL(delivered_date, created_at))/7, 0) > 0'))
						// 			 ->whereBetween('payment_date', [ $startDate, $endDate ])
						// 			 ->groupBy('client_id')
						// 			 ->first();
						if(count($orders) > 0) {
							foreach ($orders as $order) {
								if($order->passed > 0) {
									$partialAmount += round(floatval($order->total) - floatval($order->discount) - floatval($order->discount_admin) + floatval($order->iva), 2);
								}
							}
						}
						break;
				}

				if($partialAmount != 0.0) {
					$data[] = [
						'laboratory' 	=> $branchName,
						'client'		=> $clientName,
						'amount'		=> $partialAmount
					];

					$totalAmount += $partialAmount;
				}
				// if(!is_null($query)) {
				// 	$totalAmount = round(floatval($query->total) - floatval($query->discount) - floatval($query->discount_admin) + floatval($query->iva), 2);
				// 	if($totalAmount > 0) {
				// 		$data[] = [
				// 			'laboratory' 	=> $branchName,
				// 			'client'		=> $clientName,
				// 			'amount'		=> $totalAmount
				// 		];
				// 	}
				// }
			}
		}

		if(count($data) > 0) {
			echo '<table>';
				echo '
					<tr>
						<th>Laboratorio</th>
						<th>Clinte</th>
						<th>Cantidad</th>
					</tr>
				';
			foreach($data as $row) {
				echo '<tr>';
				echo '<td>' . $row['laboratory'] . '</td>';
				echo '<td>' . $row['client'] . '</td>';
				echo '<td>' . '$ ' . number_format($row['amount'], 2) . '</td>';
				echo '</tr>';
			}

			echo '<tfoot>
				<tr>
					<td colspan="2"> Total </td>
					<td> ' . number_format($totalAmount, 2)  . ' </td>
				</tr>
			</tfoot>';
			echo '</table>';
		}
	}

	public function debtors($branch_id = null) {
		ini_set('memory_limit',-1);
		$actualDate = date('Y-m-d');
		$numberOfWeek = date('N', strtotime($actualDate));
		$weekFirstDay = date('Y-m-d', strtotime($actualDate . " - " . ($numberOfWeek - 1) . " days"));
		$weekLastDay = date('Y-m-d', strtotime($actualDate . " + " . (7 - $numberOfWeek) . " days"));
		$isWholeClients = is_null($branch_id);

		$data = [];
		$data['EDO_CUENTA'] = [];
		$data['COBRANZA_1'] = [];
		$data['COBRANZA_2'] = [];
		$data['COBRANZA_3'] = [];
		$data['VENCIDO'] = [];
		$data['CONTADO'] = [];

		define('BASE_DATE', '2019-02-11');

		if(!$isWholeClients) {
			$branch = Branch::findOrFail($branch_id);
			$branchName = $branch->name;
			$branchName = str_replace('PDV ', '', $branchName);
			$branchs = [ $branch ];
		} else {
			$branchName = 'CONCENTRADO';
			$branchs = Branch::all();
		}

		// Now we'll use each POS BRANCH for iterate through ther customers
		foreach ($branchs as $branch) {
			// If we need headers
			if($isWholeClients) {
				$data['EDO_CUENTA'][] = str_replace('PDV ', '', $branch->name);
				$data['COBRANZA_1'][] = str_replace('PDV ', '', $branch->name);
				$data['COBRANZA_2'][] = str_replace('PDV ', '', $branch->name);
				$data['COBRANZA_3'][] = str_replace('PDV ', '', $branch->name);
				$data['VENCIDO'][] = str_replace('PDV ', '', $branch->name);
				$data['CONTADO'][] = str_replace('PDV ', '', $branch->name);
			}

			// We'll use this foreach cycle to iterate through all customers with more time
			// in ther payment_plan

			$clients = $branch->clients()
							  ->where('payment_plan', '>', 0)
						  	  ->where('category', 'DOCTORES')
						  	  ->orderBy('name', 'ASC')
							  ->get();

			foreach ($clients as $client) {
				$orders = $client->orders()
								->whereNull('payment_date')
								->whereNotNull('delivered_date')
								->where('payed', 0)
								->where('created_at', '>', BASE_DATE)
								->where('status', 'entregado')
								->get();

				if(count($orders) > 0) {
					$clientData = [];
					$clientData['EDO_CUENTA'] = 0.0;
					$clientData['COBRANZA_1'] = 0.0;
					$clientData['COBRANZA_2'] = 0.0;
					$clientData['COBRANZA_3'] = 0.0;
					$clientData['VENCIDO'] = 0.0;

					// if($client->id == 33) {
					// 	array_map(function($a) {
					// 		echo $a['passed'] . ' - ' . $a['id'] . PHP_EOL;
					// 	}, $orders->toArray());
					// 	die;
					// }

					foreach ($orders as $order) {
						$weekPassed = $order->passed;
						$partialAmount = round(floatval($order->total) - floatval($order->discount) - floatval($order->discount_admin) + floatval($order->iva), 2);

						if($weekPassed >= 1) continue;
						if($weekPassed == 0) {
							$clientData['EDO_CUENTA'] += $partialAmount;
						} else if($weekPassed == -1) {
							$clientData['COBRANZA_1'] += $partialAmount;
						} else if($weekPassed == -2) {
							$clientData['COBRANZA_2'] += $partialAmount;
						} else if($weekPassed == -3) {
							$clientData['COBRANZA_3'] += $partialAmount;
						} else {
							$clientData['VENCIDO'] += $partialAmount;
						}
					}

					if($clientData['EDO_CUENTA'] > 0) {
						$data['EDO_CUENTA'][] = [
							'client' 	=> $client->name,
							'amount'	=> $clientData['EDO_CUENTA'],
						];
					}

					if($clientData['COBRANZA_1'] > 0) {
						$data['COBRANZA_1'][] = [
							'client' 	=> $client->name,
							'amount'	=> $clientData['COBRANZA_1'],
						];
					}

					if($clientData['COBRANZA_2'] > 0) {
						$data['COBRANZA_2'][] = [
							'client' 	=> $client->name,
							'amount'	=> $clientData['COBRANZA_2'],
						];
					}

					if($clientData['COBRANZA_3'] > 0) {
						$data['COBRANZA_3'][] = [
							'client' 	=> $client->name,
							'amount'	=> $clientData['COBRANZA_3'],
						];
					}

					if($clientData['VENCIDO'] > 0) {
						$data['VENCIDO'][] = [
							'client' 	=> $client->name,
							'amount'	=> $clientData['VENCIDO'],
						];
					}
				}
			}

			$clients = $branch->clients()
							  ->where('payment_plan', 0)
							  ->where('category', 'DOCTORES')
							  ->orderBy('name', 'ASC')
							  ->get();
			// And now for all customer with no payment_plan
			foreach ($clients as $client) {
				$orders = $client->orders()
								->whereNull('payment_date')
								->whereNotNull('delivered_date')
								->where('created_at', '>', BASE_DATE)
								->where('status', 'entregado')
								->get();

				if(count($orders) > 0) {
					$totalAmount = 0.0;

					foreach ($orders as $order) {
						$partialAmount = round(floatval($order->total) - floatval($order->discount) - floatval($order->discount_admin) + floatval($order->iva), 2);
						$totalAmount += $partialAmount;
					}

					if($totalAmount > 0) {
						$data['CONTADO'][] = [
							'client' 	=> $client->name,
							'amount'	=> $totalAmount,
						];
					}
				}
			}
		}

		$data4Sheet = [
			'sheets' => [
				[ $data['EDO_CUENTA'] ],
				[ $data['COBRANZA_1'], $data['COBRANZA_2'], $data['COBRANZA_3'] ],
				[ $data['VENCIDO'] ],
				[ $data['CONTADO'] ]
			],
			'range' 	=> sprintf('AL %s', $weekFirstDay),
			'branch'	=> $branchName,
		];

		return (new DebtorsExport($data4Sheet))->download(date('Y-m-d') . $branchName . '_FORMATO_DE_INGRESOS.xlsx');
	}

	public function pdf(Request $request) {
		ini_set('memory_limit',-1);
		$id         = $request->input('client_id');
		$startDate  = $request->input('start_date') . ' 00:00:00';
		$endDate    = $request->input('end_date') . ' 23:59:59';

		$orders = Order::where('client_id', $id)
					   ->whereBetween('created_at', [ $startDate, $endDate ])
					   ->orderBy('created_at', 'ASC')
					   ->get();

	   foreach ($orders as $key => &$value) {
		   $value->productHas;
		   $value->extras;

		   $value->computedTotal = !is_null($value->promo) ? $value->promo : floatval($value->total) - floatval($value->discount) - floatval($value->discount_admin) + floatval($value->iva);
	   }

	   $selectedClient = Client::findOrFail($id);
	   $selectedClient->monofocalDiscount       = $selectedClient->discounts()->where('type_id', 1)->first()->discount;
	   $selectedClient->bifocalDiscount         = $selectedClient->discounts()->where('type_id', 2)->first()->discount;
	   $selectedClient->progresiveDiscount      = $selectedClient->discounts()->where('type_id', 3)->first()->discount;
	   $selectedClient->packagesDiscount        = $selectedClient->discounts()->where('type_id', 5)->first()->discount;
	   $selectedClient->finishedDiscount        = $selectedClient->discounts()->where('type_id', 4)->first()->discount;

	   $pdf = PDF::loadView('pdf.invoice', compact('orders', 'selectedClient'));
	   return $pdf->setPaper('a4', 'landscape')->stream();
	}

	public function calcFrom(Order $order, $date, $paymentPlan) {
		ini_set('memory_limit',-1);
		$startDate  = isset($order->delivered_date) ? date_create($order->delivered_date) : date_create($order->created_at);
        @$weeks  = $paymentPlan ?: 0;
        date_add($startDate, date_interval_create_from_date_string($weeks . ' weeks'));
        $endDate    = date('Y-m-d', strtotime($date));

        $startDate = date_format($startDate, 'Y-m-d');

        $first = \DateTime::createFromFormat('Y-m-d', $startDate);
        $second = \DateTime::createFromFormat('Y-m-d', $endDate);
        if($first > $second)
            return intval(ceil($first->diff($second)->days/7));
        else
            return -intval(ceil($first->diff($second)->days/7)) + 1;
	}

	public function accountClient($id, Request $request) {
		ini_set('memory_limit',-1);
		$client = Client::findOrFail($id);
		$clientName = $client->name;
		$clientName = $this->clearString($clientName);

		$fileName = $client->id . '-' . $clientName . '.xlsx';
		$data = [];


		if($request->has('ids')) {
			$idsParam = $request->query('ids');
			$ids = explode(',', $idsParam);

			$payments = Order::whereIn('id', $ids)
							 ->get();
		} else {
			$payments = Order::where("status","entregado")
							->where("payed", false)
							->where('client_id', $client->id)
							->get();
		}

		foreach ($payments as $order) {
			$order->productHas;
			$orderExtras = [];
			foreach ($order->extras as $extra) {
				$orderExtras[] = $extra->name;
			}

			if(isset($order->promo_discount))
				$total = floatval($order->promo_discount);
			else
				$total = floatval($order->total) - floatval($order->discount) - floatval($order->discount_admin) + floatval($order->iva);

			$taxToApply = (in_array($order->laboratory_id, [2, 10]) ? 0.08 : 0.16);
			$partialTax = ($total / (1 + $taxToApply)) * $taxToApply;

			$data[] = [
				'rx' => $order->rx,
				'fecha' => $order->created_at,
				'semanas_restantes' => $order->passed,
				'diseno' => isset($order->product['name']) ? $order->product['name'] : 'NO DISPONIBLE',
				'material' => isset($order->product['subcategory_name']) ? $order->product['subcategory_name'] : 'NO DISPONIBLE',
				'caracteristica' => isset($order->product['type_name']) ? $order->product['type_name'] : 'NO DISPONIBLE',
				'antireflejante' => implode(', ', $orderExtras),
				'plazo' => @$order->payment_plan ?: '0',
				'subtotal' => $order->total,
				'iva' => $partialTax,
				'descuentos_sistema' => $order->discount,
				'descuento' =>  floatval($order->discount_admin) / (in_array($order->laboratory_id, [2, 10]) ? 1.08 : 1.16),
				'total' => $total,
			];
		}

		// Excel::store(new BalanceExport($data, $client), 'documents/branch/' . $id . '/' . $fileName);
		return Excel::download(new BalanceExport($data, $client), $fileName);
	}

	public function setLog(Request $request) {
		ini_set('memory_limit',-1);
		try {
			$user = Auth::user();
			$data = $request->all();
			// if is first attempt today
			if(DB::table('login_logs')->where('email', $user->email)->whereBetween('login_date', [date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')])->count() == 0) {
				$data['name']		= $user->name;
				$data['email']		= $user->email;

				DB::table('login_logs')->insert($data);

				return response()->json(['msg' => 'Login registrado']);
			}
			return response()->json(['msg' => 'Login NO registrado']);
		} catch(\Exception $e) {
			return response()->json(['msg' => $e->getMessage()]);
		}
	}

	public function getLogs(Request $request) {
		ini_set('memory_limit',-1);
		$logs = DB::table('login_logs')->whereRaw(sprintf('DATE(login_date) = "%s"', $request->input('date')))->get();

		foreach ($logs as &$log) {
			$log->login_time = date('h:i:s a', strtotime($log->login_date));
		}

		$maxAssistance 	= User::whereHas("roles", function($q){ $q->where("name", "Ejecutivo"); })->count();

		return response()->json(compact('logs', 'maxAssistance'));
	}

	public function excelFebe() {
		ini_set('memory_limit',-1);
		$clients = Client::with('branch', 'state', 'town')
						 ->orderBy('id', 'ASC')->get();

		foreach ($clients as &$client) {
			$client->monofocalDiscount       = @$client->discounts()->where('type_id', 1)->first()->discount;
			$client->bifocalDiscount         = @$client->discounts()->where('type_id', 2)->first()->discount;
			$client->progresiveDiscount      = @$client->discounts()->where('type_id', 3)->first()->discount;
			$client->packagesDiscount        = @$client->discounts()->where('type_id', 5)->first()->discount;
			$client->finishedDiscount        = @$client->discounts()->where('type_id', 4)->first()->discount;
		}

		try {
			return Excel::download(new ClientsExport($clients), date('Ymd') . '_CLIENTES.xlsx');
		} catch(\Exception $e) {
			echo $e->getMessage();
		}

	}

	public function excelStatus(Request $request) {
		ini_set('memory_limit',-1);
		set_time_limit(0);
		$start      = $request->input('start') . ' 00:00:00';
		$end        = $request->input('end') . ' 23:59:59';

		$user = User::findOrFail($request->input('user_id'));

		if($request->has('q')) {
			$field  = $request->query('q');

			$title = '';
			switch ($request->input('q')) {
				case 'finish_date': $title = 'PROCESO_TERMINADO'; break;
				case 'delivered_date': $title = 'TERMINADO_ENTREGADO'; break;
				case 'payment_date': $title = 'ENTREGADO_PAGADO'; break;
				case 'delivered_date2': $title = 'GARANTIA_ENTREGADO'; break;
			}

			if($user->hasRole('punto de ventas') || $user->hasRole('Ejecutivo')) {
				if($field == 'delivered_date')
					$orders =  Order::where('branch_id', $user->branch_id)->whereBetween($field, [$start, $end])->whereNull('warranty_date')->orderBy($field, 'ASC')->orderByRaw('cast(rx as unsigned)')->get();
				else
					$orders =  Order::where('branch_id', $user->branch_id)->whereBetween($field, [$start, $end])->orderBy($field, 'ASC')->orderByRaw('cast(rx as unsigned)')->get();
			}
			else {
				if($field == 'delivered_date')
					$orders =  Order::whereBetween($field, [$start, $end])->whereNull('warranty_date')->orderBy($field, 'ASC')->orderByRaw('cast(rx as unsigned)')->get();
				else
					$orders =  Order::whereBetween($field, [$start, $end])->orderBy($field, 'ASC')->orderByRaw('cast(rx as unsigned)')->get();
			}

			foreach ($orders as $key => $value) {
				$value->productHas;
				$value->extras;
				$value->client;
				$value->branch;
				$value->laboratory;
				$value->branch;
				// $value->client->facturacion;

				$value->computedTotal = !is_null($value->promo) ? $value->promo : floatval($value->total) - floatval($value->discount) - floatval($value->discount_admin) + floatval($value->iva);
			}
            
			foreach ($orders as $key => $value) {
				$orders[$key] = $orders[$key]->toArray();
				if(is_null($orders[$key]['client'])){
					$orders[$key] = array_merge($orders[$key], ['client'=>['name'=> 'Cliente eliminado']]);
				}
			}
			libxml_use_internal_errors(true);
			return Excel::download(new StatusExport($orders), date('Ymd') . '_' . $title  . '_STATUS.xlsx');
		} else
			die('Request cannot be processed');
	}
	public function excelStatusTest(Request $request) {
		
		ini_set('memory_limit',-1);
		set_time_limit(0);
		$start      = $request->input('start') . ' 00:00:00';
		$end        = $request->input('end') . ' 23:59:59';

		$user = User::findOrFail($request->input('user_id'));

		if($request->has('q')) {
			$field  = $request->query('q');

			$title = '';
			switch ($request->input('q')) {
				case 'finish_date': $title = 'PROCESO_TERMINADO'; break;
				case 'delivered_date': $title = 'TERMINADO_ENTREGADO'; break;
				case 'payment_date': $title = 'ENTREGADO_PAGADO'; break;
				case 'delivered_date2': $title = 'GARANTIA_ENTREGADO'; break;
			}

			if($user->hasRole('punto de ventas') || $user->hasRole('Ejecutivo')) {
				if($field == 'delivered_date')
					$orders =  Order::select('orders.*','clients.name as client_name','branches.name as branch_name','laboratories.name as laboratory_name')
								->leftJoin('clients', 'orders.client_id', '=', 'clients.id')
								->leftJoin('branches', 'orders.branch_id', '=', 'branches.id')
								->leftJoin('laboratories', 'orders.laboratory_id', '=', 'laboratories.id')
								->where('orders.branch_id', $user->branch_id)
								->whereBetween('orders.'.$field, [$start, $end])
								->whereNull('warranty_date')
								->orderBy('orders.'.$field, 'ASC')
								->orderByRaw('cast(orders.rx as unsigned)')
								->get();
				else
					$orders =  Order::select('orders.*','clients.name as client_name','branches.name as branch_name','laboratories.name as laboratory_name')
								->leftJoin('clients', 'orders.client_id', '=', 'clients.id')
								->leftJoin('branches', 'orders.branch_id', '=', 'branches.id')
								->leftJoin('laboratories', 'orders.laboratory_id', '=', 'laboratories.id')
								->where('orders.branch_id', $user->branch_id)
								->whereBetween('orders.'.$field, [$start, $end])
								->orderBy('orders.'.$field, 'ASC')
								->orderByRaw('cast(orders.rx as unsigned)')
								->get();
			}
			else {
				if($field == 'delivered_date')
					$orders =  Order::select('orders.*','clients.name as client_name','branches.name as branch_name','laboratories.name as laboratory_name')
								
								->leftJoin('clients', 'orders.client_id', '=', 'clients.id')
								->leftJoin('branches', 'orders.branch_id', '=', 'branches.id')
								->leftJoin('laboratories', 'orders.laboratory_id', '=', 'laboratories.id')
								->whereBetween('orders.'.$field, [$start, $end])
								->whereNull('warranty_date')
								->orderBy('orders.'.$field, 'ASC')
								->orderByRaw('cast(orders.rx as unsigned)')
								->get();
				else
					$orders =  Order::select('orders.*','clients.name as client_name','branches.name as branch_name','laboratories.name as laboratory_name')
							
								->leftJoin('clients', 'orders.client_id', '=', 'clients.id')
								->leftJoin('branches', 'orders.branch_id', '=', 'branches.id')
								->leftJoin('laboratories', 'orders.laboratory_id', '=', 'laboratories.id')
								->whereBetween('orders.'.$field, [$start, $end])
								->orderBy('orders.'.$field, 'ASC')
								->orderByRaw('cast(orders.rx as unsigned)')
								->get();
			}
			

			

			foreach ($orders as $key => $value) {
				
				$value->productHas;
				$value->extras;
				//$value->client;
				//$value->branch;
				//$value->laboratory;
				//$value->branch;
				// $value->client->facturacion;

				$value->computedTotal = !is_null($value->promo) ? $value->promo : floatval($value->total) - floatval($value->discount) - floatval($value->discount_admin) + floatval($value->iva);
				$orders[$key] = $orders[$key]->toArray();
			}
			
			/*foreach ($orders as $key => $value) {
				$orders[$key] = $orders[$key]->toArray();
				if(is_null($orders[$key]['client'])){
					$orders[$key] = array_merge($orders[$key], ['client'=>['name'=> 'Cliente eliminado']]);
				}
			}*/
			libxml_use_internal_errors(true);
			return Excel::download(new StatusExport($orders), date('Ymd') . '_' . $title  . '_STATUS.xlsx');
		} else
			die('Request cannot be processed');
	}

	public function updateClients(Request $request) {
		ini_set('memory_limit',-1);
		// if($request->has('file')) {
		// 	$filename = $request->file('file')->getRealPath();
	    //     $lines = [];
		// 	if (($handle = fopen($filename, "r")) !== false) {
		// 		while (($data = fgetcsv($handle, 8192, ",")) !== false) {
		// 			$data = array_map(function($item) {
		// 				return utf8_encode($item);
		// 			}, $data);
		//
		// 			$coords = explode(',', $data[11]);
		// 			if(count($coords) > 1) {
		// 				$data[11] = $coords[0];
		// 				$data[12] = $coords[1];
		//
		// 				$data = array_map(function($c) {
		// 					return "'$c'";
		// 				}, $data);
		// 				$fieldsValues = 'null, ' . implode(',', array_values($data));
		//
		// 				$response = sprintf("INSERT INTO re VALUES (%s)", $fieldsValues);
		// 				try {
		// 					DB::insert($response);
		// 				} catch(\Exception $e) {
		// 					echo $response . PHP_EOL;
		// 				}
		// 			}
		// 		}
		// 	}
		// }
		$branches = Branch::all();

		if($request->has('file')) {
			ClientsImport::$updatedClients = 0;
			Excel::import(new ClientsImport, $request->file('file'));

			$updatedClients = ClientsImport::$updatedClients;

			// return redirect('/import')->with(compact('updateClients'));

		}

		if(isset($updatedClients))
			echo $updatedClients . 'clientes modificados';
		echo '<br/>';
		echo '
			<form enctype="multipart/form-data" method="POST">
				' . csrf_field() .'
				<input type="file" name="file"/>
				<select name="branch_id">
					<option selected disable hidden>Selecciona Laboratorio</option>';
				foreach ($branches as $branch) {
					echo sprintf('<option value="%s">%s</option>', $branch->id, $branch->name);
				}
		echo '
				</select>
				<button type="submit">Enviar</button>
			</form>
		';
	}

	public function re(Request $request) {
		ini_set('memory_limit',-1);
		header("Access-Control-Allow-Origin: *");
		$machines = [];

        $lat        = $request->input('lat');
        $lng        = $request->input('lng');
        $distance   = 50;

		// $results = DB::table('re')->get();
		$results = DB::select(DB::raw('SELECT *, ( 3959 * acos( cos( radians(' . $lat . ') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(' . $lng . ') ) + sin( radians(' . $lat .') ) * sin( radians(latitude) ) ) ) AS distance FROM re HAVING distance < ' . $distance . ' ORDER BY distance') );

		return response()->json($results);
	}

	public function wsSisavi(Request $request) {
		if($request->has('wsdl')) {
			header('Content-Type: application/xml');
			echo <<<XML
			<?xml version="1.0" encoding="UTF-8"?>
				<definitions name="GlassesService"
							targetNamespace="https://dev.augenlabs.com/api/ws/sisavi"
							xmlns:tns="https://dev.augenlabs.com/api/ws/sisavi"
							xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/"
							xmlns:xsd="http://www.w3.org/2001/XMLSchema"
							xmlns:soapenc="http://schemas.xmlsoap.org/soap/encoding/"
							xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/"
							xmlns="http://schemas.xmlsoap.org/wsdl/">

					<types>
						<xsd:schema targetNamespace="https://dev.augenlabs.com/api/ws/sisavi">
							<xsd:element name="Envelope" type="tns:Envelope"/>
							<xsd:element name="EnvelopeResponse" type="tns:EnvelopeResponse"/>
							<xsd:complexType name="Envelope">
								<xsd:sequence>
									<xsd:element name="rx" type="xsd:int"/>
									<xsd:element name="fecha" type="xsd:date"/>
									<xsd:element name="cliente" type="xsd:string"/>
									<!-- Add other elements as per your request structure -->
								</xsd:sequence>
							</xsd:complexType>
							<xsd:complexType name="EnvelopeResponse">
								<xsd:sequence>
									<xsd:element name="order_id" type="xsd:int"/>
								</xsd:sequence>
							</xsd:complexType>
						</xsd:schema>
					</types>

					<message name="GlassesRequest">
						<part name="parameters" element="tns:Envelope"/>
					</message>

					<message name="GlassesResponse">
						<part name="parameters" element="tns:EnvelopeResponse"/>
					</message>

					<portType name="GlassesPortType">
						<operation name="ProcessGlasses">
							<input message="tns:GlassesRequest"/>
							<output message="tns:GlassesResponse"/>
						</operation>
					</portType>

					<binding name="GlassesBinding" type="tns:GlassesPortType">
						<soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/>
						<operation name="ProcessGlasses">
							<soap:operation soapAction="https://dev.augenlabs.com/api/ws/sisavi#ProcessGlasses" style="rpc"/>
							<input>
								<soap:body use="encoded" namespace="https://dev.augenlabs.com/api/ws/sisavi" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>
							</input>
							<output>
								<soap:body use="encoded" namespace="https://dev.augenlabs.com/api/ws/sisavi" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>
							</output>
						</operation>
					</binding>

					<service name="GlassesService">
						<port name="GlassesPort" binding="tns:GlassesBinding">
							<soap:address location="https://dev.augenlabs.com/api/ws/sisavi"/>
						</port>
					</service>
				</definitions>

			XML;
			die;
		}

		if ($request->isMethod('get')) {
			abort(404);
			die;
		}
		$xmlString = $request->getContent();
				// Create a SimpleXMLElement from the XML string
		$xmlObject = new \SimpleXMLElement($xmlString);

		// Convert SimpleXMLElement to stdClass
		$phpObject = json_decode(json_encode($xmlObject));

		// Access properties of the PHP object
		// echo $phpObject->rx; // Example access to the 'rx' property
		// Set the content type to XML
		header('Content-Type: application/xml');

		try {

			$rxFields = [
				'rx_rx' => 'rx',
				'rx_fecha' => 'fecha',
				'client_id' => 'id_cliente',
				'rx_cliente' => 'cliente',
				'rx_od_esfera' => 'od_esfera',
				'rx_od_cilindro' => 'od_cilindro',
				'rx_od_eje' => 'od_eje',
				'rx_od_adicion' => 'od_adicion',
				'rx_od_dip' => 'od_dip',
				'rx_od_altura' => 'od_altura',
				'rx_od_esfera_dos' => 'oi_esfera',
				'rx_od_cilindro_dos' => 'oi_cilindro',
				'rx_od_eje_dos' => 'oi_eje',
				'rx_od_adicion_dos' => 'oi_adicion',
				'rx_od_dip_dos' => 'oi_dip',
				'rx_od_altura_dos' => 'oi_altura',
				'rx_diseno' => 'diseno',
				'rx_material' => 'material',
				'rx_caracteristicas' => 'caracteristicas',
				'rx_tipo_ar' => 'tipo_ar',
				'rx_tallado' => 'tallado',
				'rx_servicios' => 'servicios',
				'rx_tipo_armazon' => 'tipo_armazon',
				'rx_horizontal_a' => 'horizontal_a',
				'rx_vertical_b' => 'vertical_b',
				'rx_diagonal_ed' => 'diagonal_ed',
				'rx_puente' => 'puente',
				'rx_observaciones' => 'observaciones'
			];


			// fields validation
			if(!isset($phpObject->tipo_armazon)) {
				throw new \Exception("Error en el tipo de armazon", 19);
			}

			foreach(['od_esfera', 'od_cilindro', 'od_eje', 'od_adicion', 'od_dip', 'od_altura'] as $field) {
				if(!isset($phpObject->{ $field })) {
					throw new \Exception("Error indicador mica derecha", 14);
				}
			}

			foreach(['oi_esfera', 'oi_cilindro', 'oi_eje', 'oi_adicion', 'oi_dip', 'oi_altura'] as $field) {
				if(!isset($phpObject->{ $field })) {
					throw new \Exception("Error indicador mica izquierda", 15);
				}
			}

			if(!isset($phpObject->tipo_ar)) {
				throw new \Exception("Error indicador de tinte", 17);
			}

			$order = new Order();
			foreach($rxFields as $orderAttr => $xmlAttr) {
				if(is_string($phpObject->{ $xmlAttr }))
					$order->{ $orderAttr } = $phpObject->{ $xmlAttr };
				else
					$order->{ $orderAttr } = '';
			}

			$metadata = [
				'oi_posicion_prisma' => is_string($phpObject->oi_posicion_prisma) ? $phpObject->oi_posicion_prisma : '',
				'od_posicion_prisma' => is_string($phpObject->od_posicion_prisma) ? $phpObject->od_posicion_prisma : '',
				'oi_grado_prisma' => is_string($phpObject->oi_grado_prisma) ? $phpObject->oi_grado_prisma : '',
				'od_grado_prisma' => is_string($phpObject->od_grado_prisma) ? $phpObject->od_grado_prisma : '',
				'b_mica_derecha' => is_string($phpObject->b_mica_derecha) ? $phpObject->b_mica_derecha : '',
				'b_mica_izquierda' => is_string($phpObject->b_mica_izquierda) ? $phpObject->b_mica_izquierda : '',
			];

			$order->metadata = json_encode($metadata);

			$client = Client::findOrFail($order->client_id);
			// $clientId = $client->id;
		
			// $order->client_id = $clientId;
			$order->rx = $phpObject->rx;

			if(Order::where('rx', $order->rx)->exists()) {
				throw new \Exception("Orden ya existe", 19);
			}

			define('TYPE_ID', 15);
			$category = $this->_mapMaterial($xmlObject->material->__toString());
			if(is_null($category))
				throw new \Exception("El material enviado no pudo ser encontrado", 99);

			$product = $this->_mapDiseno($xmlObject->diseno->__toString(), TYPE_ID);
			if(is_null($product))
				throw new \Exception("El diseño enviado no pudo ser encontrado", 99);
			
			$characteristicName = $this->_mapExtra($xmlObject->diseno->__toString(), $xmlObject->material->__toString());
			if(is_null($characteristicName))
				throw new \Exception("Valores inválidos", 99);

			$subcaregory = $category->subcategories->where('name', $characteristicName)->first();
			$phs = ProductHasSubcategory::where('product_id', $product->id)
										->where('subcategory_id', $subcaregory->id)
										->where('category_id', $category->id)
										->where('type_id', TYPE_ID)
										// ->where('cost', '>', 0)
										->where('price', '>', 0)
										->orderBy('created_at', 'DESC')
										->first();

			if(is_null($phs))
				throw new \Exception("Valores inválidos", 99);
			
			$purchase = new Purchase(array(
				"user_id" => 0,
				"client_id" => $order->client_id,
				"total" => $phs->price,
				"description"=> 'Order generada via WS /// SISAVI'
			));
			$purchase->save();

			$order->purchase_id = $purchase->id;
			$order->product_has_subcategory_id = $phs->id;
			$order->price = $phs->price;
			$order->service = 0;
			$order->total = $phs->price;
			$order->iva = round($phs->price * 0.16, 2);
			$order->branch_id = $client->branch_id;
			$order->laboratory_id = $client->branch->laboratory->id;

			// $order->save();

			$order->branch;
			$order->branch->laboratory;
			$order->productHas;

			$notification = new Notification();
        	$notification->text = 'RX ' . $order->rx . ' - Solicitud desde WS';
        	$notification->type = 'rx';
			$notification->icon = 'fa fa-eye';

			$user = User::where('laboratory_id', $order->branch->laboratory_id)
                    ->where('email', 'LIKE', 'coordinador%')
                    ->first();

			$notification->user_id = $user->id;
			$notification->url = '';
			$notification->metadata = json_encode($order->toArray());
			$notification->save();

			$orderId = 'TBD'; // $order->id;

			$response = <<<XML
			<?xml version="1.0" encoding="UTF-8" ?>
			<Envelope>
				<order_id>$orderId</order_id>
				<result>10</result>
			</Envelope>
			XML;

		} catch(\Exception $e) {
			$code = $e->getCode();
			$message = $e->getMessage();

			if(!in_array($code, [10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 70])) {
				$code = 99;
				// $message = 'Error no definido';
			}

			$response = <<<XML
			<?xml version="1.0" encoding="UTF-8" ?>
			<Envelope>
				<result>$code</result>
				<message>$message</message>
			</Envelope>
			XML;
		}

	

		echo $response;

	}

	private function _mapDiseno($param, $type_id) {
		$disenoMap = [
			"Trinity DG" => "PROGRESIVO AL CENTURY",
			"Trinity FF" => "PROGRESIVO FF CENTURY",
			"V.S. Digital - Au" => "MONOFOCAL CENTURY",
			"V.S. Digital + AR - Au" => "MONOFOCAL CENTURY",
			"Flat Top 28 Digital - Au" => "FLAT TOP CENTURY",
			"Flat Top 28 Digital + AR - Au" => "FLAT TOP CENTURY",
			"Invisible - Au" => "INVISIBLE CENTURY",
		];

		if(!isset($disenoMap[$param]))
			return null;

		$name = $disenoMap[$param];
		$product = Product::where('name', $name)
						 ->where('type_id', $type_id)
						 ->first();

		return $product;
	}

	private function _mapMaterial($param) {
		$materialsMap = [
			"Cr-39 Digital" => "RESINA ÓPTICA CR-39",
			"Alto Indice 1.56 Digital" => "ALTO ÍNDICE 1.56",
			"Cr-39 Parasol Digital" => "PARASOL FOTOCROMÁTICO",
			"Trivex Digital" => "TRIVEX",
			"B-block" => "B-BLOCK",
			"Cr-39 Free Form" => "RESINA ÓPTICA CR-39",
			"Cr-39 Polarizado Cafe Free Form" => "POLARIZADO",
			"Cr-39 Polarizado Verde Free Form" => "POLARIZADO",
			"Cr-39 Parasol Free Form" => "PARASOL FOTOCROMÁTICO",
			"Cr-39 Polarizado Gris Free Form" => "POLARIZADO",
			"Trivex Free Form" => "TRIVEX",
			"Trivex Parasol Free Form" => "TRIVEX PARASOL",
			"Trivex 1.60 Free Form" => "TRIVEX 1.60",
			"MR10" => "ALTAS GRADUACIONES",
			"Alto Indice 1.56 Free Form" => "ALTO ÍNDICE 1.56",
			"Cr-39 Digital + AR" => "RESINA ÓPTICA CR-39",
			"Cr-39 Polarizado Gris Digital" => "POLARIZADO",
			"Cr-39 Polarizado cafe Digital" => "POLARIZADO",
			"Trivex Digital + AR" => "TRIVEX",
			"Trivex Parasol Digital" => "TRIVEX PARASOL",
			"Trivex 1.60 Digital" => "TRIVEX 1.60",
			"Alto Indice 1.56 Digital + AR" => "ALTO ÍNDICE 1.56",
			"Cr-39 Parasol Digital + AR" => "PARASOL FOTOCROMÁTICO",
			"MR10 + AR" => "ALTAS GRADUACIONES",
			"B-block digital + AR Azul" => "B-BLOCK",
			"Trivex Digital 1.60 + AR" => "TRIVEX 1.60",
			"Trivex Parasol Digital + AR" => "TRIVEX PARASOL",
			"Cr-39 Polarizado Digital + AR" => "POLARIZADO",
		];

		if(!isset($materialsMap[$param]))
			return null;

		$name = $materialsMap[$param];
		$category = Category::where('name', $name)->first();

		return $category;
	}

	private function _mapExtra($diseno, $material) {
		$caracteristicasMap = [
			"Trinity DG" => [
				"Cr-39 Digital" => "Blanco",
				"Alto Indice 1.56 Digital" => "Blanco",
				"Cr-39 Parasol Digital" => "Blanco",
				"Trivex Digital" => "Blanco",
			],
			"Trinity FF" => [
				"B-block" => "Free Form + Ar Azul",
				"Cr-39 Free Form" => "Free Form + AR",
				"Cr-39 Polarizado Cafe Free Form" => "Free Form Blanco",
				"Cr-39 Polarizado Verde Free Form" => "Free Form Blanco",
				"Cr-39 Parasol Free Form" => "Free Form Blanco",
				"Cr-39 Polarizado Gris Free Form" => "Free Form Blanco",
				"Trivex Free Form" => "Free Form Blanco",
				"Trivex Parasol Free Form" => "Free Form Blanco",
				"Trivex 1.60 Free Form" => "Free Form Blanco",
				"MR10" => "Free Form Blanco",
				"Alto Indice 1.56 Free Form" => "Free Form Blanco",
			],
			"V.S. Digital - Au" => [
				"Cr-39 Digital" => "Blanco",
				"MR10" => "",
				"Cr-39 Parasol Digital" => "Blanco",
				"Cr-39 Polarizado Gris Digital" => "Blanco",
				"Cr-39 Polarizado cafe Digital" => "Blanco",
				"Trivex Digital" => "Blanco",
				"Trivex Parasol Digital" => "Blanco",
				"Trivex 1.60 Digital" => "Blanco",
				"Alto Indice 1.56 Digital" => "Blanco",
			],
			"V.S. Digital + AR - Au" => [
				"Cr-39 Digital + AR" => "HD + AR",
				"Trivex Digital + AR" => "Augen Lens + AR",
				"Alto Indice 1.56 Digital + AR" => "HD + AR",
				"B-block digital + AR Azul" => "Augen Lens + AR Azul",
				"Cr-39 Parasol Digital + AR" => "Augen Lens + AR",
				"Trivex Digital 1.60 + AR" => "Augen Lens + AR",
				"Trivex Parasol Digital + AR" => "Augen Lens + AR",
				"Cr-39 Polarizado Digital + AR" => "Augen Lens + AR",
				"MR10 + AR" => "Augen Lens + AR",
			],
			"Flat Top 28 Digital - Au" => [
				"Cr-39 Digital" => "Blanco",
				"Cr-39 Parasol Digital" => "Blanco",
				"Trivex Digital" => "Blanco",
				"Alto Indice 1.56 Digital" => "Blanco",
				"MR10" => "Blanco",
			],
			"Flat Top 28 Digital + AR - Au" => [
				"Cr-39 Parasol Digital + AR" => "Augen Lens + AR",
				"MR10 + AR" => "Augen Lens + AR",
				"Trivex Digital + AR" => "Augen Lens + AR",
				"Cr-39 Digital + AR" => "HD + AR",
				"Alto Indice 1.56 Digital + AR" => "HD + AR",
			],
			"Invisible - Au" => [
				"Cr-39 Digital" => "",
				"Cr-39 Digital + AR" => "HD + AR",
			],
		];

		$caracteristica = $caracteristicasMap[$diseno][$material] ?? null;

		return $caracteristica;
	}

	public function rxGenerationPDF() {
		$pdf = PDF::loadView('pdf.recipe', []);
		
		return $pdf->setPaper('A5')->stream();
		// return view('pdf.recipe');
	}
}

 ?>
