<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Helpers\CP;
use App\Client;
use App\Order;
use App\OrderHasExtra;
use App\Purchase;
use App\OrdersMove;
use App\Branch;
use App\Laboratory;
use App\Promo;
use App\User;
use JWTAuth;
use Mail;
use Excel;
use PDF;

use Illuminate\Support\Facades\Storage;
use App\Exports\OrdersExport;
use App\Mail\RequestRx;
use Carbon\Carbon;

class OrdersController extends Controller
{
    public function store(Request $request)
    {
       
        ini_set('memory_limit', '-1');
        $user=Auth::user();
        $user->branch;
        if(!isset($user->branch->laboratory)){
            return response()->json(['msg'=>'No puedes realizar pedidos, contacta al administrador.'],500);
        }
        // dd($user);
        $purchase=new Purchase(array(
            "user_id"=>$user->id,
            "client_id"=>$request->client_id,
            "total"=>$request->total,
            "description"=>$request->observation
        ));
        $purchase->save();

        $client = Client::find($request->client_id);
        foreach ($request->cart as $key => $value) {
            $order = new Order(array(
                "purchase_id"=>$purchase->id,
                "product_has_subcategory_id"=>$value['product_has_subcategory_id'],
                "price"=>$value['price'],
                "discount"=>$value['discount'],
                "discount_percent"=>$value['percent_discount'],
                "service"=>$value['service'],
                "total"=>$value['total'],
                "iva"=>$value['iva'],
                "rx"=>$value['rx'],
                "laboratory_id"=> isset($value['laboratory_id']) ? $value['laboratory_id'] : $user->branch->laboratory->id,
                "branch_id"=>$user->branch->id,
                "client_id"=>$request->client_id,

                "rx_rx" => isset($value['rx_data']['rx_rx']) ? $value['rx_data']['rx_rx'] : null,
                "rx_fecha" => isset($value['rx_data']['rx_fecha']) ? $value['rx_data']['rx_fecha'] : null,
                "rx_cliente" => isset($value['rx_data']['rx_cliente']) ? $value['rx_data']['rx_cliente'] : null,

                "rx_od_esfera" => isset($value['rx_data']['rx_od_esfera']) ? $value['rx_data']['rx_od_esfera'] : null,
                "rx_od_cilindro" => isset($value['rx_data']['rx_od_cilindro']) ? $value['rx_data']['rx_od_cilindro'] : null,
                "rx_od_eje" => isset($value['rx_data']['rx_od_eje']) ? $value['rx_data']['rx_od_eje'] : null,
                "rx_od_adicion" => isset($value['rx_data']['rx_od_adicion']) ? $value['rx_data']['rx_od_adicion'] : null,
                "rx_od_dip" => isset($value['rx_data']['rx_od_dip']) ? $value['rx_data']['rx_od_dip'] : null,
                "rx_od_altura" => isset($value['rx_data']['rx_od_altura']) ? $value['rx_data']['rx_od_altura'] : null,
                

                "rx_od_esfera_dos" => isset($value['rx_data']['rx_od_esfera_dos']) ? $value['rx_data']['rx_od_esfera_dos'] : null,
                "rx_od_cilindro_dos" => isset($value['rx_data']['rx_od_cilindro_dos']) ? $value['rx_data']['rx_od_cilindro_dos'] : null,
                "rx_od_eje_dos" => isset($value['rx_data']['rx_od_eje_dos']) ? $value['rx_data']['rx_od_eje_dos'] : null,
                "rx_od_adicion_dos" => isset($value['rx_data']['rx_od_adicion_dos']) ? $value['rx_data']['rx_od_adicion_dos'] : null,
                "rx_od_dip_dos" => isset($value['rx_data']['rx_od_dip_dos']) ? $value['rx_data']['rx_od_dip_dos'] : null,
                "rx_od_altura_dos" => isset($value['rx_data']['rx_od_altura_dos']) ? $value['rx_data']['rx_od_altura_dos'] : null,
            
                "rx_diseno" => isset($value['rx_data']['rx_diseno']) ? $value['rx_data']['rx_diseno'] : null,
                "rx_material" => isset($value['rx_data']['rx_material']) ? $value['rx_data']['rx_material'] : null,
                "rx_caracteristicas" => isset($value['rx_data']['rx_caracteristicas']) ? $value['rx_data']['rx_caracteristicas'] : null,
                "rx_tipo_ar" => isset($value['rx_data']['rx_tipo_ar']) ? $value['rx_data']['rx_tipo_ar'] : null,
                "rx_tallado" => isset($value['rx_data']['rx_tallado']) ? $value['rx_data']['rx_tallado'] : null,
                "rx_servicios" => isset($value['rx_data']['rx_servicios']) ? $value['rx_data']['rx_servicios'] : null,

                "rx_tipo_armazon" => isset($value['rx_data']['rx_tipo_armazon']) ? $value['rx_data']['rx_tipo_armazon'] : null,
                "rx_horizontal_a" => isset($value['rx_data']['rx_horizontal_a']) ? $value['rx_data']['rx_horizontal_a'] : null,
                "rx_vertical_b" => isset($value['rx_data']['rx_vertical_b']) ? $value['rx_data']['rx_vertical_b'] : null,
                "rx_diagonal_ed" => isset($value['rx_data']['rx_diagonal_ed']) ? $value['rx_data']['rx_diagonal_ed'] : null,
                "rx_puente" => isset($value['rx_data']['rx_puente']) ? $value['rx_data']['rx_puente'] : null,
                "rx_observaciones" => isset($value['rx_data']['rx_observaciones']) ? $value['rx_data']['rx_observaciones'] : null,
                
            ));

            $actualTotal = floatval($value['total']) - floatval($value['discount']);

            $order->iva = $this->_recalculateTax($actualTotal, $order->laboratory_id, $order->iva);
            $order->save();
            
            if($value['extras']) {
                foreach ($value['extras'] as $k => $v) {
                    OrderHasExtra::create(array(
                        "order_id"=>$order->id,
                        "extra_id"=>$v['extra_id'],
                        "price"=>$v['price'],
                    ));
                }
            }

            // if Order object is an Augen Mask it must be on ENTREGADO inmediatly
            if (strpos($order->rx, 'AM-') !== false) {
                $order->finish_date = date('Y-m-d');
                $order->delivered_date = date('Y-m-d');
                $order->status = 'entregado';
                $order->save();
            }

            if ($value['rx_data']) {
                $value['rx_data']['pvd'] = $user->branch->name;
                $value['rx_data']['laboratory'] = $user->branch->Laboratory->name;

                $pdf = PDF::loadView('plantillas.requestrx',['inputs' => $value['rx_data']])->setPaper('A5');
                $content = $pdf->download()->getOriginalContent();
                 // Crear el archivo y almacenarlo en el storage
                Storage::disk('public')->put('docs/order-'.$order->id.'.pdf',$content);
                
                $mapLabs = [
                    1 => 'americas',
                    2 => 'ens',
                    3 => 'chap',
                    4 => 'mty',
                    5 => 'pue',
                    6 => 'slp'
                ];
                
                if ($client) {
                    if($value['rx_data']['have_data']) {
                        if(in_array($value['laboratory_id'], [1, 2, 3, 4, 5, 6])) {
                            // $cc = 'captura' . $mapLabs[$value['laboratory_id']] . '@augenlabs.com';
                            Mail::to('sistemas@augenlabs.com')
                            // ->cc($cc)
                            ->send(new RequestRx($value['rx_data'], $content, 'CAPTURA'));
                        } else {
                            Mail::to('sistemas@augenlabs.com')->send(new RequestRx($value['rx_data'], $content, 'CAPTURA'));
                        }
                    }
                }
            }

        }

        if (isset($request->user['branch'])) {
            $data['pvd'] = $request->user['branch']['name'];
        }
        
        


        return response()->json(['msg'=>'Pedido generado correctamente!!']);
    }

    private function _recalculateTax($total, $laboratory_id, $actualTax) {
        // $client = Client::findOrFail($client_id);
        // $zipCode = $client->facturacion->postal_code;

        if(!isset($laboratory_id) || is_null($laboratory_id))
            return $actualTax;
        return in_array($laboratory_id, [2, 10]) ? (floatval($total) * 0.08) : $actualTax;
    }

    public function search($rx = null) {
        $user=Auth::user();

        if(is_null($rx))
        $orders = Order::with('client')
                       ->where('branch_id', $user->branch_id)
                       ->take(20)
                       ->get();
        else
            $orders = Order::where("rx",$rx)
                           ->where('branch_id', $user->branch_id)
                           ->with('client')
                           ->get();
        foreach ($orders as $key => $value) {
            $value->productHas;
            $value->extras;
            $value->branch;
            $value->branch->laboratory;

            if(is_null($orders[$key]->client)) {
                $orders[$key]->client = ['name'=> 'Cliente eliminado'];
            }

            if(is_null($orders[$key]->branch)) {
                $orders[$key]->branch = ['name'=> 'PDV Eliminado'];
            }

            if(isset($orders[$key]->promo_discount))
                $orders[$key]->total_real = $orders[$key]->promo_discount;//number_format(floatval($orders[$key]->total)  - floatval($orders[$key]->discount) - floatval($orders[$key]->discount_admin) + floatval($orders[$key]->iva), 2);
            else
                $orders[$key]->total_real = number_format(floatval($orders[$key]->total)  - floatval($orders[$key]->discount) - floatval($orders[$key]->discount_admin) + floatval($orders[$key]->iva), 2);
        
            
            $checkform = false;
            if ($value->rx_diagonal_ed != null && $value->rx_diagonal_ed != '') {$checkform = true;}
            if ($value->rx_horizontal_a != null && $value->rx_horizontal_a != '') {$checkform = true;}
            if ($value->rx_observaciones != null && $value->rx_observaciones  != '') {$checkform = true;}
            if ($value->rx_od_adicion != null && $value->rx_od_adicion != '') {$checkform = true;}
            if ($value->rx_od_adicion_dos != null && $value->rx_od_adicion_dos != '') {$checkform = true;}
            if ($value->rx_od_altura != null && $value->rx_od_altura != '') {$checkform = true;}
            if ($value->rx_od_altura_dos != null && $value->rx_od_altura_dos != '') {$checkform = true;}
            if ($value->rx_od_cilindro != null && $value->rx_od_cilindro != '') {$checkform = true;}
            if ($value->rx_od_cilindro_dos != null &&  $value->rx_od_cilindro_dos != '') {$checkform = true;}
            if ($value->rx_od_dip != null && $value->rx_od_dip != '') {$checkform = true;}
            if ($value->rx_od_dip_dos != null && $value->rx_od_dip_dos != '') {$checkform = true;}
            if ($value->rx_od_eje != null && $value->rx_od_eje != '') {$checkform = true;}
            if ($value->rx_od_eje_dos != null && $value->rx_od_eje_dos != '') {$checkform = true;}
            if ($value->rx_od_esfera != null && $value->rx_od_esfera != '') {$checkform = true;}
            if ($value->rx_od_esfera_dos != null && $value->rx_od_esfera_dos != '') {$checkform = true;}
            if ($value->rx_puente != null && $value->rx_puente != '') {$checkform = true;}
            if ($value->rx_servicios != null && $value->rx_servicios != '') {$checkform = true;}
            if ($value->rx_tallado != null && $value->rx_tallado != '') {$checkform = true;}
            if ($value->rx_tipo_ar != null && $value->rx_tipo_ar != '') {$checkform = true;}
            if ($value->rx_tipo_armazon != null && $value->rx_tipo_armazon != '') {$checkform = true;}
            if ($value->rx_vertical_b != null && $value->rx_vertical_b != '') {$checkform = true;}
            $orders[$key]->have_data = $checkform;
        }

        return response()->json($orders, 200, [], JSON_NUMERIC_CHECK);
    }

    public function client($id){

        $orders = Order::where("client_id",$id)->where(function($query){
            $query->where("status","en_proceso")
                ->orWhere("status","terminado")
                ->orWhere("status","entregado")
                ->orWhere("status","garantia");
        })->get();

        foreach ($orders as $key => $value) {
            $value->productHas;
            $value->extras;
        }

        return response()->json($orders);
    }

    public function laboratory(Request $request) {
        if($request->has('ajax')) {
            $search     = $request->query('search');
            $sort       = $request->query('sort');
            $order      = $request->query('order');
            $offset     = intval($request->query('offset'));
            $limit      = intval($request->query('limit'));

            $rows = [];
            $total = 0;
            $totalNotFiltered = 0;

            $user = Auth::user();
            $user->laboratory;

            if(!$user->laboratory) {
                return response()->json(compact('rows', 'total', 'totalNotFiltered'));
            }

            $rows = Order::with('branch')
                         ->where('laboratory_id', $user->laboratory->id)
                         ->whereIn('status', ['en_proceso', 'garantia']);

            if(!is_null($search) AND $search != 'TODO')
                $rows->where('rx', 'LIKE', '%' . $search . '%');

            // $rows->skip($offset)
            //       ->limit($limit);
            //

            if(!is_null($sort))
                $rows->orderBy($sort, $order);

            $rows = $rows->get();

            $totalNotFiltered = Order::where('laboratory_id', $user->laboratory->id)
                         ->whereIn('status', ['en_proceso', 'garantia'])
                         ->count();

            if(!is_null($search))
                $total = count($rows);
            else
                $total = $totalNotFiltered;

            foreach ($rows as $key => $value) {
                $value->productHas;
                $value->extras;
                $value->branch;
                $value->branch->laboratory;

                try {
                    if(count($value->extras) > 0)
                        $value->computedExtras = implode(', ', array_map(function($e) {
                            return $e->name;
                        }, $value->extras->toArray()));
                    else
                        $value->computedExtras = '-';
                } catch(\Exception $e) {
                    $value->computedExtras = '-';
                }

                // if($value->computedExtras == "")
                    // $value->computedExtras = 'N/A';

                $value->price   = '$' . number_format($value->price, 2);
                $value->service = '$' . number_format($value->service, 2);
                $value->realTotal = '$' . number_format(floatval($value->total)  - floatval($value->discount) - floatval($value->discount_admin) + floatval($value->iva), 2);

                if($value->status == 'en_proceso')
                    $value->statusButton = '<button class="btn btn-success btn-change" data-id="' . $value->id . '">En proceso</button>';
                else
                    $value->statusButton = '<button class="btn btn-warning btn-change" data-id="' . $value->id . '">Garantía</button>';
                $value->moveButton = '<button class="btn btn-move" data-id="' . $value->id . '"> <i class="fas fa-arrows-alt"></i> </button>';
                if(isset($value->rx_rx) && $value->rx_rx != '')
                    $value->showButton = '<button class="btn btn-show" data-id="' . $value->id . '"> <i class="fas fa-eye"></i> </button>';
                else
                    $value->showButton = '-';
            }

            return response()->json(compact('rows', 'total', 'totalNotFiltered'));
            exit;
        }
        $user=Auth::user();
        $user->laboratory;
        if(!$user->laboratory){
            return response()->json(['msg'=>'No estas registrado en ningun laboratorio.','data'=>$user->laboratory],404);
        }

        $orders= Order::where("laboratory_id",$user->laboratory->id)->where(function($query){
            $query->where("status","en_proceso")->orWhere("status","garantia");
        })->get();

        foreach ($orders as $key => $value) {
            $value->productHas;
            $value->extras;
            $value->client;
        }

        foreach ($orders as $key => $value) {
            $orders[$key] = $orders[$key]->toArray();
            if(is_null($orders[$key]['client'])){
                $orders[$key] = array_merge($orders[$key], ['client'=>['name'=> 'Cliente eliminado']]);
            }

            if(is_null($orders[$key]['branch'])) {
                $orders[$key] = array_merge($orders[$key], ['branch'=>['name'=> 'PDV Eliminado']]);
            }
        }

        // dd($orders[0]);
        return response()->json($orders);

    }

    public function pendings(Request $request) {
        
        
        ini_set('memory_limit',-1);
        $start = $request->start;
        $end = $request->end;
        $status = $request->status;

        $start = date("Y-m-d H:i:s", strtotime($start." 00:00:00"));
        $end = date("Y-m-d H:i:s", strtotime($end." 23:59:59"));

        if(!$status['en_proceso'] && !$status['terminado'] && !$status['entregado'] && !$status['pagado'] && !$status['garantia'])
            return [];

        $orders = Order::query();

        if($request->has('search') && $request->input('search') != '')
            // $orders = $orders->where('rx', 'LIKE', '%' . $request->input('search') . '%');
            $orders = $orders->where('rx', $request->input('search'))->orderBy('created_at', 'desc');
        else
            $orders = $orders->where("created_at", ">=" ,$start)->where("created_at", "<=", $end)->where(
                function($query) use ($status){
                    if($status['en_proceso']==true) $query->orWhere("status","like","en_proceso");
                    if($status['terminado']==true) $query->orWhere("status","like","terminado");
                    if($status['entregado']==true) $query->orWhere("status","like","entregado");
                    if($status['pagado']==true) $query->orWhere("status","like","pagado");
                    if($status['garantia']==true) $query->orWhere("status","like","garantia");
                }
            )->orderBy('created_at', 'desc');


        $orders = $orders->get();
        foreach ($orders as $key => $value) {
            $value->productHas;
            $value->extras;
            $value->client;
            $value->branch;
            $value->branch->laboratory;
        }

        foreach ($orders as $key => $value) {
            $orders[$key] = $orders[$key]->toArray();
            if(is_null($orders[$key]['client'])){
                $orders[$key] = array_merge($orders[$key], ['client'=>['name'=> 'Cliente eliminado']]);
            }

            if(is_null($orders[$key]['branch'])) {
                $orders[$key] = array_merge($orders[$key], ['branch'=>['name'=> 'PDV Eliminado']]);
            }
        }
        return response()->json($orders, 200, [], JSON_NUMERIC_CHECK);
    }

    public function discount(Request $request,$id)
    {
        $order = Order::find($id);
        $order->discount_admin=$request->discount;
        if(($order->total + $order->iva) <= ($order->discount+$order->discount_admin)){
            $order->payed=true;
            if($order->status == "entregado") {
                // $order->delivered_date = date('Y-m-d');
                $order->payment_date = date('Y-m-d');
                $order->status = "pagado";
            }
        }
        $order->save();

        return response()->json(['msg'=>"Descuento hecho por: $".$request->discount]);
    }

    public function today(Request $request, $branch_id = null) {
        $startDate  = $request->query('from') . ' 00:00:00';
        $endDate    = $request->query('to') . ' 23:59:59';

        $user = Auth::user();
        $branch_id =  $user->branch ? $user->branch->id : $branch_id;

        $orders= Order::whereBetween('created_at', [ $startDate, $endDate ])
                        ->where('status', '<>', 'cancelado')
                        ->where("branch_id", $branch_id)
                        ->get();

        foreach ($orders as $key => $value) {
            $value->productHas;
            $value->extras;
            $value->purchase;
            $value->branch;
            $value->branch->laboratory;

            $checkform = false;
                if ($value->rx_diagonal_ed != null && $value->rx_diagonal_ed != '') {$checkform = true;}
                if ($value->rx_horizontal_a != null && $value->rx_horizontal_a != '') {$checkform = true;}
                if ($value->rx_observaciones != null && $value->rx_observaciones  != '') {$checkform = true;}
                if ($value->rx_od_adicion != null && $value->rx_od_adicion != '') {$checkform = true;}
                if ($value->rx_od_adicion_dos != null && $value->rx_od_adicion_dos != '') {$checkform = true;}
                if ($value->rx_od_altura != null && $value->rx_od_altura != '') {$checkform = true;}
                if ($value->rx_od_altura_dos != null && $value->rx_od_altura_dos != '') {$checkform = true;}
                if ($value->rx_od_cilindro != null && $value->rx_od_cilindro != '') {$checkform = true;}
                if ($value->rx_od_cilindro_dos != null &&  $value->rx_od_cilindro_dos != '') {$checkform = true;}
                if ($value->rx_od_dip != null && $value->rx_od_dip != '') {$checkform = true;}
                if ($value->rx_od_dip_dos != null && $value->rx_od_dip_dos != '') {$checkform = true;}
                if ($value->rx_od_eje != null && $value->rx_od_eje != '') {$checkform = true;}
                if ($value->rx_od_eje_dos != null && $value->rx_od_eje_dos != '') {$checkform = true;}
                if ($value->rx_od_esfera != null && $value->rx_od_esfera != '') {$checkform = true;}
                if ($value->rx_od_esfera_dos != null && $value->rx_od_esfera_dos != '') {$checkform = true;}
                if ($value->rx_puente != null && $value->rx_puente != '') {$checkform = true;}
                if ($value->rx_servicios != null && $value->rx_servicios != '') {$checkform = true;}
                if ($value->rx_tallado != null && $value->rx_tallado != '') {$checkform = true;}
                if ($value->rx_tipo_ar != null && $value->rx_tipo_ar != '') {$checkform = true;}
                if ($value->rx_tipo_armazon != null && $value->rx_tipo_armazon != '') {$checkform = true;}
                if ($value->rx_vertical_b != null && $value->rx_vertical_b != '') {$checkform = true;}
                $value->have_data = $checkform;
        }
        return response()->json($orders);
    }

    public function changeStatus(Request $request,$id) {
        $order  = Order::find($id);
        $status = $request->input('status');
        $clientName = $order->client->name;

        define('EN_PROCESO', 'en_proceso');
        define('TERMINADO', 'terminado');
        define('ENTREGADO', 'entregado');
        define('PAGADO', 'pagado');
        define('GARANTIA', 'garantia');
        define('CANCELADO', 'cancelado');

        /*
         *  Order $order lifecycle
         *  EN PROCESO
         *  TERMINADO
         *  ENTREGADO
         *  PAGADO
         *  En cualquiero momento la orden puede pasar a
         *  CANCELADO
         *  Solo si la orden no está en EN PROCESO ó CANCELADO puede pasar a
         *  GARANTÍA
         */

         try {
             $prevStatus = $order->status;
             $order->status = $status;
             // dd($prevStatus, $status);
             switch ($prevStatus) {
                 case CANCELADO:
                    if($order->cancelled == 1) throw new \Exception("Error: Rx cancelada", 1);
                    break;
                 case EN_PROCESO:
                     if($status == TERMINADO)
                         $order->finish_date = date('Y-m-d');
                     else throw new \Exception("Error: flujo incorrecto", 1);
                     break;
                 case TERMINADO:
                    if($status == ENTREGADO) {
                        if (is_null($order->warranty_date)) {
                            $order->delivered_date = date('Y-m-d');
                        } else {
                            $order->delivered_date2 = date('Y-m-d');
                        }
                        // Si la Rx fue pagada con anerioridad pasa a pagado y cierre el flujo
                        if($order->payed == 1) {
                            $order->payment_date = date('Y-m-d');
                            $order->status = PAGADO;
                        }
                        // Si el cliente es de CORTESÍA la Rx pasa a pagado y cierra el flujo.
                        if (strpos($clientName, 'CORTESIA') !== false) {
                            $order->payment_date = date('Y-m-d');
                            $order->status = PAGADO;
                            $order->payed = 1;
                        }
                    } else if($status == GARANTIA)
                        $order->warranty_date = date('Y-m-d');
                    else throw new \Exception("Error: flujo incorrecto", 1);
                    break;
                 case ENTREGADO:
                    if($status == PAGADO) {
                        $order->payment_date = date('Y-m-d');
                        $order->payed = 1;
                    }
                    else if($status == GARANTIA)
                        $order->warranty_date = date('Y-m-d');
                    else throw new \Exception("Error: flujo incorrecto", 1);
                    break;
                 case PAGADO:
                    if($status == GARANTIA)
                        $order->warranty_date = date('Y-m-d');
                    else throw new \Exception("Error: flujo incorrecto", 1);
                    break;
                 case GARANTIA:
                    if($status == TERMINADO)
                        $status = ENTREGADO;
                    if($status == ENTREGADO) {
                         $order->delivered_date2 = date('Y-m-d');
                         // Si la Rx fue pagada con anerioridad pasa a pagado y cierre el flujo
                         if($order->payed == 1) {
                             $order->payment_date = date('Y-m-d');
                             $order->status = PAGADO;
                         }
                         // Si el cliente es de CORTESÍA la Rx pasa a pagado y cierra el flujo.
                         if (strpos($clientName, 'CORTESIA') !== false) {
                             $order->payment_date = date('Y-m-d');
                             $order->status = PAGADO;
                             $order->payed = 1;
                         }
                    } else throw new \Exception("Error: flujo incorrecto", 1);
                    break;
                 default:
                     throw new \Exception("Error: flujo incorrecto", 1);
                     break;
             }

             $order->save();

             return response()->json(['msg'=>"Estatus de la orden se cambio a " . $order->status ]);
         } catch(\Exception $e) {
             return response()->json(['msg'=> $e->getMessage()]);
         }
    }

    public function changeLaboratory(Request $request,$id)
    {
        $order = Order::find($id);
        $branch = Branch::find($order->branch_id);
        
        OrdersMove::create(array(
            "from"=> $order->laboratory_id,
            "to"=> $request->laboratory_id
        ));

        $checkform = false;
        if ($order->rx_diagonal_ed != null && $order->rx_diagonal_ed != '') {$checkform = true;}
        if ($order->rx_horizontal_a != null && $order->rx_horizontal_a != '') {$checkform = true;}
        if ($order->rx_observaciones != null && $order->rx_observaciones  != '') {$checkform = true;}
        if ($order->rx_od_adicion != null && $order->rx_od_adicion != '') {$checkform = true;}
        if ($order->rx_od_adicion_dos != null && $order->rx_od_adicion_dos != '') {$checkform = true;}
        if ($order->rx_od_altura != null && $order->rx_od_altura != '') {$checkform = true;}
        if ($order->rx_od_altura_dos != null && $order->rx_od_altura_dos != '') {$checkform = true;}
        if ($order->rx_od_cilindro != null && $order->rx_od_cilindro != '') {$checkform = true;}
        if ($order->rx_od_cilindro_dos != null &&  $order->rx_od_cilindro_dos != '') {$checkform = true;}
        if ($order->rx_od_dip != null && $order->rx_od_dip != '') {$checkform = true;}
        if ($order->rx_od_dip_dos != null && $order->rx_od_dip_dos != '') {$checkform = true;}
        if ($order->rx_od_eje != null && $order->rx_od_eje != '') {$checkform = true;}
        if ($order->rx_od_eje_dos != null && $order->rx_od_eje_dos != '') {$checkform = true;}
        if ($order->rx_od_esfera != null && $order->rx_od_esfera != '') {$checkform = true;}
        if ($order->rx_od_esfera_dos != null && $order->rx_od_esfera_dos != '') {$checkform = true;}
        if ($order->rx_puente != null && $order->rx_puente != '') {$checkform = true;}
        if ($order->rx_servicios != null && $order->rx_servicios != '') {$checkform = true;}
        if ($order->rx_tallado != null && $order->rx_tallado != '') {$checkform = true;}
        if ($order->rx_tipo_ar != null && $order->rx_tipo_ar != '') {$checkform = true;}
        if ($order->rx_tipo_armazon != null && $order->rx_tipo_armazon != '') {$checkform = true;}
        if ($order->rx_vertical_b != null && $order->rx_vertical_b != '') {$checkform = true;}

        if($checkform) {
            $laboratoryId = $request->laboratory_id;
            $laboratory = Laboratory::findOrFaiL($laboratoryId);
            $data = $order->toArray();
            $data['pvd'] = $branch->name;
            $data['laboratory'] = $laboratory->name;
            $pdf = PDF::loadView('plantillas.requestrx',['inputs' => $data])->setPaper('A5');
            $content = $pdf->download()->getOriginalContent();
            Storage::disk('public')->put('docs/order-'.$order->id.'.pdf',$content);

            $mapLabs = [
                1 => 'americas',
                2 => 'ens',
                3 => 'chap',
                4 => 'mty',
                5 => 'pue',
                6 => 'slp'
            ];
    
            // if(in_array($laboratoryId, [1, 2, 3, 4, 5, 6])) {
            //     $mailTo = 'captura' . $mapLabs[$laboratoryId] . '@augenlabs.com';
            //     $title = sprintf('RX MOVIDA DE %s A %s', $branch->laboratory->name, $laboratory->name);
            //     Mail::to($mailTo)
            //         ->send(new RequestRx($data, $content, $title));
            // }
        }

        $order->laboratory_id=$request->laboratory_id;
        $order->save();
        return response()->json(['msg'=>"RX fue movido a otro laboratorio con exito."]);
    }

    public function paymentsPDV(Request $request, $branch_id) {
        ini_set('memory_limit',-1);
        $orders = Order::where("branch_id", $branch_id)
                       // ->where("created_at",">=",$start)
                       // ->where("created_at","<=",$end)
                       ->where("status","like","entregado")
                       ->where("payed",false)
                       ->orderByRaw('cast(rx as unsigned)');

        $isLiverpool = $request->has('liverpool');
       if($isLiverpool) {
           $clients = Client::where('name', 'LIKE', 'LIVERPOOL%')
                            ->select('id')
                            ->get();
           $clientsId = [];
           foreach ($clients as $client)
               $clientsId[] = $client->id;

           $orders->whereIn('client_id', $clientsId);
       }

        $orders = $orders->get();



        foreach ($orders as $key => $value) {
            $value->productHas;
            $value->extras;
            $value->client;
            $value->branch;
            if(isset($value->client))
                $value->client->facturacion;
        }



        foreach ($orders as $key => $value) {
            $orders[$key] = $orders[$key]->toArray();
            if(is_null($orders[$key]['client'])){
                $orders[$key] = array_merge($orders[$key], ['client'=>['name'=> 'Cliente eliminado']]);
            }
        }

        return response()->json($orders, 200, [], JSON_NUMERIC_CHECK);

    }

    public function payments(Request $request, $branch_id = null)
    {
        ini_set('memory_limit',-1);
        $user = Auth::user();
        $email = $user->email;

        if ($email != 'cobranzacorp@augenlabs.com' && $email != 'jcsosa@augenlabs.com') {
            return response()->json(['error' => 'No tienes acceso a este módulo'], 403);
        }

        // $start=$request->start;
        // $end=$request->end;
        // $start=date("Y-m-d H:i:s",strtotime($start." 00:00:00"));
        // $end=date("Y-m-d H:i:s",strtotime($end." 23:59:59"));

        $isLiverpool = $request->has('liverpool');

        $branch_id =  $user->branch ? $user->branch->id : $branch_id;

        if($user->hasPermissionTo('cobranza_admin') && $branch_id == null) {
            $branchs = Branch::all();
            foreach ($branchs as $key => $value) {
                // $value->clients;
                $orders = Order::where("branch_id",$value->id)
                               // ->where("created_at",">=",$start)
                               // ->where("created_at","<=",$end)
                               ->where("status","like","entregado")
                               ->where("status","like","terminado")
                               ->where("payed",false);
                if($isLiverpool) {
                    $clients = Client::where('name', 'LIKE', 'LIVERPOOL%')
                                     ->select('id')
                                     ->get();
                    $clientsId = [];
                    foreach ($clients as $client)
                        $clientsId[] = $client->id;

                    $orders->whereIn('client_id', $clientsId);
                }
                else if($request->has('client')) {
                    $orders = $orders->whereHas("client", function($query) use ($request) {
                        $query->where("name","like","%".$request->client."%")->orWhere("comertial_name","like","%".$request->client."%");
                    });
                }

                if(!empty($request->rx)){
                    $orders = $orders->where("rx",$request->rx);
                }


                // $orders = $orders->groupBy("orders.client_id");

                $orders = $orders->get();

                // foreach ($orders as $order) {
                //     $order->productHas;
                //     $order->extras;
                //     $order->client;
                //     $order->branch;
                //     $order->client->facturacion;
                // }

                $value->orders=$orders;
            }

            return response()->json($branchs);
        } else {

            $consult = Order::selectRaw("orders.client_id")
                            ->where("status","entregado")
                            // ->where("created_at",">=",$start)
                            // ->where("created_at","<=",$end)
                            ->where("payed",false)
                            ->where("branch_id", $branch_id);
            if($isLiverpool) {
                $clients = Client::where('name', 'LIKE', 'LIVERPOOL%')
                                 ->select('id')
                                 ->get();
                $clientsId = [];
                foreach ($clients as $client)
                    $clientsId[] = $client->id;

                $consult->whereIn('client_id', $clientsId);
            }

            if(!empty($request->rx)){
                $consult=$consult->where("rx",$request->rx);
            }

            $consult=$consult->groupBy("orders.client_id");
            // $consult = $consult->orderByRaw('cast(rx as unsigned)');
            $payments=$consult->get();
            // dd($payments);
            $clients=[];
            foreach ($payments as $key => $value) {
                $client=Client::where("id",$value->client_id);
                if(!empty($request->client)){
                    $client = $client->where(function($query) use ($request){
                        $query->where("name","like","%".$request->client."%")->orWhere("comertial_name","like","%".$request->client."%");
                    });
                }

                $client=$client->first();
                if(is_null($client))
                    continue;

                $client->total = 0.0;
                $client->discounts = 0.0;
                $client->discounts_admin = 0.0;
                $client->ivas = 0.0;

                $filteredOrders = $client->orders()
                                         ->with('extras', 'client')
                                         ->where("status","entregado")
                                         // ->where("created_at",">=",$start)
                                         // ->where("created_at","<=",$end)
                                         ->where("payed",false)
                                         ->where("branch_id", $branch_id)
                                         ->orderByRaw('cast(rx as unsigned)')
                                         ->get();

                foreach ($filteredOrders as &$order) {
                    $order->productHas;
                    if($order->passed <= 0) {
                        $client->total += $order->total;
                        $client->discounts += $order->discount;
                        $client->discounts_admin += $order->discount_admin;
                        $client->ivas += $order->iva;
                    }
                }

                $client->orders = $filteredOrders;
                array_push($clients,$client);
            }
            return response()->json($clients, 200, [], JSON_NUMERIC_CHECK);
        }

    }

    public function ofClient(Request $request, $client_id, $branch_id = null) {
        // $startDate = $request->query('from');
        // $endDate = $request->query('to');

        $user=Auth::user();
        // $user->branch;
        $branch_id =  $user->branch ? $user->branch->id : $branch_id;

        $orders=Order::where("branch_id", $branch_id)
                     // ->whereBetween('created_at', [ $startDate, $endDate ])
                     ->where("status","entregado")
                     ->where("payed",false)
                     ->where("client_id",$client_id)
                     ->get();

        foreach ($orders as $key => $value) {
            $value->productHas;
            $value->extras;
            $value->client;
            $value->branch;
            $value->client->facturacion;
        }

        foreach ($orders as $key => $value) {
            $orders[$key] = $orders[$key]->toArray();
            if(is_null($orders[$key]['client'])){
                $orders[$key] = array_merge($orders[$key], ['client'=>['name'=> 'Cliente eliminado']]);
            }
        }

        return response()->json($orders);
    }

    public function ofBranch(Request $request, $branch_id) {
        $startDate = $request->query('from');
        $endDate = $request->query('to');

        $orders=Order::where("branch_id", $branch_id)
                     ->whereBetween('created_at', [ $startDate, $endDate ])
                     ->where("status","entregado")
                     ->where("payed",false)
                     ->get();

        foreach ($orders as $key => $value) {
            $value->productHas;
            $value->extras;
            $value->client;
            $value->branch;
            $value->client->facturacion;
        }

        foreach ($orders as $key => $value) {
            $orders[$key] = $orders[$key]->toArray();
            if(is_null($orders[$key]['client'])){
                $orders[$key] = array_merge($orders[$key], ['client'=>['name'=> 'Cliente eliminado']]);
            }
        }

        return response()->json($orders);
    }

    private function _payOrder($id){
        $order=Order::find($id);
        $order->status="pagado";
        $order->payed=true;
        $order->payment_date = date('Y-m-d');
        $order->save();
    }

    public function pay(Request $request,$id)
    {
        $this->_payOrder($id);

        return response()->json(['msg'=>'La RX fue cobrada.']);
    }

    public function payMany(Request $request){
        $ids=$request->ids;
        // dd($ids);
        foreach ($ids as $key => $id) {
            $this->_payOrder($id);
        }

        return response()->json(['msg'=>'Todos los RX fueron cobrados.']);
    }

    public function statusTable(Request $request) {
        ini_set('max_execution_time', 0);
        ini_set("memory_limit","-1");
        // $branch_id  = $request->input('lab');
        $start      = $request->input('start') . ' 00:00:00';
        $end        = $request->input('end') . ' 23:59:59';
        // $status     = $request->input('status');
        // $date = $request->input('date');
        $rx = $request->input('rx');

        $user = Auth::user();

        if(isset($rx) && $rx != '') {
            if($user->hasRole('punto de ventas') ||
               $user->hasRole('Ejecutivo') ||
               $user->hasRole('laboratorio')) {
                $orders = Order::where('branch_id', $user->branch_id)->where('status', '<>', 'cancelado')->where('rx', $rx)->get();
            } else {
                $orders = Order::where('rx', $rx)->where('status', '<>', 'cancelado')->get();
            }

            foreach ($orders as $key => $value) {
                $value->productHas;
                $value->extras;
                $value->client;
                $value->branch;
                $value->laboratory;
                $value->branch;
                $value->client->facturacion;
            }

            foreach ($orders as $key => $value) {
                $orders[$key] = $orders[$key]->toArray();
                if(is_null($orders[$key]['client'])){
                    $orders[$key] = array_merge($orders[$key], ['client'=>['name'=> 'Cliente eliminado']]);
                }
            }

            return response()->json($orders);
        } else {
            if($request->has('q')) {
                $field  = $request->query('q');

                if($user->hasRole('punto de ventas') ||
                   $user->hasRole('Ejecutivo') ||
                   $user->hasRole('laboratorio')) {
                    if($field == 'delivered_date')
                        $orders =  Order::where('branch_id', $user->branch_id)->where('status', '<>', 'cancelado')->whereBetween($field, [$start, $end])->whereNull('warranty_date')->orderBy($field, 'ASC')->orderByRaw('cast(rx as unsigned)')->get();
                    else
                        $orders =  Order::where('branch_id', $user->branch_id)->where('status', '<>', 'cancelado')->whereBetween($field, [$start, $end])->orderBy($field, 'ASC')->orderByRaw('cast(rx as unsigned)')->get();
                }
                else {
                    if($field == 'delivered_date')
                        $orders =  Order::whereBetween($field, [$start, $end])->where('status', '<>', 'cancelado')->where(function($query) {
                                                $query->whereNull('warranty_date')
                                                      ->orWhereRaw('warranty_date > delivered_date');
                        })        
                                                      ->orderBy($field, 'ASC')->orderByRaw('cast(rx as unsigned)')->get();
                    else
                        $orders =  Order::whereBetween($field, [$start, $end])->where('status', '<>', 'cancelado')->orderBy($field, 'ASC')->orderByRaw('cast(rx as unsigned)')->get();
                }

                foreach ($orders as $key => $value) {
                    $value->productHas;
                    $value->extras;
                    $value->client;
                    $value->branch;
                    $value->laboratory;
                    $value->branch;
                    // $value->client->facturacion;
                }

                foreach ($orders as $key => $value) {
                    $orders[$key] = $orders[$key]->toArray();
                    if(is_null($orders[$key]['client'])){
                        $orders[$key] = array_merge($orders[$key], ['client'=>['name'=> 'Cliente eliminado']]);
                    }
                }

                return response()->json($orders);
            } else {
                if($user->hasRole('punto de ventas') ||
                   $user->hasRole('Ejecutivo') ||
                   $user->hasRole('laboratorio')) {
                    $data = [
                        'terminado' => Order::where('branch_id', $user->branch_id)->whereBetween('finish_date', [$start, $end])->where('status', '<>', 'cancelado')->count(),
                        'entregado' => Order::where('branch_id', $user->branch_id)->whereBetween('delivered_date', [$start, $end])
                                            ->where(function($query) {
                                                $query->whereNull('warranty_date')
                                                      ->orWhere('warranty_date', '>', 'delivered_date');
                                            })->where('status', '<>', 'cancelado')->count(),
                        'pagado'    => Order::where('branch_id', $user->branch_id)->whereBetween('payment_date', [$start, $end])->where('status', '<>', 'cancelado')->count(),
                        'garantia'    => Order::where('branch_id', $user->branch_id)->whereBetween('delivered_date2', [$start, $end])->where('status', '<>', 'cancelado')->count()//->where('warranty_date', '<=', $date)->count()
                    ];
                    $data['money'] = [
                        'terminado' => $this->generateTotal(Order::where('branch_id', $user->branch_id)->whereBetween('finish_date', [$start, $end])->where('status', '<>', 'cancelado')->orderBy('finish_date', 'ASC')->orderByRaw('cast(rx as unsigned)')->get()),
                        'entregado' => $this->generateTotal(
                            Order::where('branch_id', $user->branch_id)
                                    ->whereBetween('delivered_date', [$start, $end])
                                    ->where(function($query) {
                                        $query->whereNull('warranty_date')
                                              ->orWhere('warranty_date', '>', 'delivered_date');
                                    })->where('status', '<>', 'cancelado')
                                    ->orderBy('delivered_date', 'ASC')
                                    ->orderByRaw('cast(rx as unsigned)')
                                    ->get()
                        ),
                        'pagado'    => $this->generateTotal(Order::where('branch_id', $user->branch_id)->whereBetween('payment_date', [$start, $end])->where('status', '<>', 'cancelado')->orderBy('payment_date', 'ASC')->orderByRaw('cast(rx as unsigned)')->get()),
                        'garantia'    => $this->generateTotal(Order::where('branch_id', $user->branch_id)->whereBetween('delivered_date2', [$start, $end])->where('status', '<>', 'cancelado')->orderBy('delivered_date2', 'ASC')->orderByRaw('cast(rx as unsigned)')->get())//->where('warranty_date', '<=', $date)->count()
                    ];
                } else {
                    
                    $data = [
                        'terminado' => Order::whereBetween('finish_date', [$start, $end])->where('status', '<>', 'cancelado')->count(),
                        'entregado' => Order::whereBetween('delivered_date', [$start, $end])
                                            ->where(function($query) {
                                                $query->whereNull('warranty_date')
                                                      ->orWhereRaw('warranty_date > delivered_date');
                                            })->where('status', '<>', 'cancelado')->count(),
                        'pagado'    => Order::whereBetween('payment_date', [$start, $end])->where('status', '<>', 'cancelado')->count(),
                        'garantia'    => Order::whereBetween('delivered_date2', [$start, $end])->where('status', '<>', 'cancelado')->count()//->where('warranty_date', '<=', $date)->count()
                    ];
                    $data['money'] = [
                        'terminado' => $this->generateTotal(Order::whereBetween('finish_date', [$start, $end])->where('status', '<>', 'cancelado')->orderBy('finish_date', 'ASC')->orderByRaw('cast(rx as unsigned)')->get()),
                        'entregado' => $this->generateTotal(
                            Order::whereBetween('delivered_date', [$start, $end])
                                    ->where(function($query) {
                                        $query->whereNull('warranty_date')
                                              ->orWhere('warranty_date', '>', 'delivered_date');
                                    })->where('status', '<>', 'cancelado')
                                    ->orderBy('delivered_date', 'ASC')
                                    ->orderByRaw('cast(rx as unsigned)')
                                    ->get()
                        ),
                        'pagado'    => $this->generateTotal(Order::whereBetween('payment_date', [$start, $end])->where('status', '<>', 'cancelado')->orderBy('payment_date', 'ASC')->orderByRaw('cast(rx as unsigned)')->get()),
                        'garantia'    => $this->generateTotal(Order::whereBetween('delivered_date2', [$start, $end])->where('status', '<>', 'cancelado')->orderBy('delivered_date2', 'ASC')->orderByRaw('cast(rx as unsigned)')->get())//->where('warranty_date', '<=', $date)->count()
                    ];
                }

                // $orders = Order::with('laboratory', 'client')
                //                 ->where('branch_id', $branch_id)
                //                 ->whereBetween('created_at', [$start, $end])
                //                 ->where(
                //                     function($query) use ($status){
                //                         if($status['en_proceso'] == true) $query->orWhere("status","like","en_proceso");
                //                         if($status['terminado'] == true) $query->orWhere("status","like","terminado");
                //                         if($status['entregado'] == true) $query->orWhere("status","like","entregado");
                //                         if($status['pagado'] == true) $query->orWhere("status","like","pagado");
                //                     }
                //                 )->get();

                // foreach ($orders as $order) {
                //     $order->productHas;
                // }

                return response()->json($data);
            }
        }
    }

    public function generateTotal($orders) {
        $response = 0.0;
        foreach ($orders as $order)
            $response += (floatval($order->total)  - floatval($order->discount) - floatval($order->discount_admin) + floatval($order->iva));
        return $response;
    }

    public function recalcutateOrdersSinceDate() {
        set_time_limit(0);
        ini_set("memory_limit", "-1");
        ini_set('max_execution_time', 0);


        $datetime = '2019-02-01 00:00:00';
        $ordersModified = 0;
        $orders = Order::where('created_at', '>=', $datetime)->get();
        // $ids = [62645, 62646, 62647, 62648];
        // $orders = Order::whereIn('id', $ids)->get();

        try {
            foreach($orders as $order) {
                $zipCode = $order->client->facturacion->postal_code;

                if(!isset($zipCode) || is_null($zipCode))
                    continue;

                if(in_array($order->laboratory_id, [2, 10])) {
                // if(CP::searchZipCode($zipCode)) {
                    $actualTotal = floatval($order->total) - floatval($order->discount);
                    $order->iva = $actualTotal * 0.08;
                    $order->save();
                    $ordersModified++;
                } else {
                    $actualTotal = floatval($order->total) - floatval($order->discount);
                    $order->iva = $actualTotal * 0.16;
                    $order->save();
                    $ordersModified++;
                }
            }
        } catch(\Exception $e) {
            dd($e->getMessage());
        }

        return response()->json([
            'status'    => true,
            'message'   => sprintf('(%s) ordenes modificadas', $ordersModified),
            'data'      => null
        ]);
    }


    public function screen(Request $request) {
        date_default_timezone_set('America/Mexico_City');
        $lab = $request->query('lab');

        if($lab == 'vrdvrd') {
            $id = 0;
        } else {
            $laboratories = Laboratory::all();
            $id = null;
            foreach ($laboratories as $laboratory) {
                if(substr(md5($laboratory->id), 0, 5) == $lab) {
                    $id = $laboratory->id;
                    break;
                }
            }
        }

        if(is_null($id)) return response()->json([]);

        $pages = [];
        $pageSize = $id == 3 ? 25 : 20;
        for($i = 0; $i < 4; $i++) {
            if($id == 0) {
                $orders = Order::with('branch')
                                ->where('client_id', '566')
                                ->where(function($query) {
                                    $query->where('status', 'en_proceso')
                                          ->orWhere(function($q){
                                             $q->where('status', 'garantia')
                                               ->whereNotNull('delivered_date');
                                          });
                                })
                                // ->whereIn('status', ['en_proceso', 'garantia'])
                                ->skip($i * $pageSize)
                                ->take($pageSize)
                                ->orderBy('created_at', 'ASC')
                                ->get();
            } else {
                $orders = Order::with('branch')
                                ->where('laboratory_id', $id)
                                ->where(function($query) {
                                    $query->where('status', 'en_proceso')
                                          ->orWhere(function($q){
                                             $q->where('status', 'garantia')
                                               ->whereNotNull('delivered_date');
                                          });
                                })
                                // ->whereIn('status', ['en_proceso', 'garantia'])
                                ->skip($i * $pageSize)
                                ->take($pageSize)
                                ->orderBy('created_at', 'ASC')
                                ->get();
            }



            foreach($orders as &$order) {
                $start = new \DateTime($order->created_at);
                $end = new \DateTime(date('Y-m-d H:i:s'));
                $days = $start->diff($end, true)->days;
                $sundays = intval($days / 7) + ($start->format('N') + $days % 7 >= 7);
                $order->days_diff = $days - $sundays;

                $order->formatted_date = date('d-M', strtotime($order->created_at));


            }

            $pages[] = $orders;
        }

        return response()->json(compact('pages'));
    }

    public function sendPromotionalEmail($id = null) {
        if(is_null($id)) {
            // TEST MAIL
            // $order = Order::inRandomOrder()->first();
            $order = Order::findOrFail(41083);

            $order->client;
            $order->productHas;
            $order->extras;

            if(!is_null($order->client)) {
                // Prepare test view

                $orders = [ $order, $order ];
                // if(true) {
                //     Mail::send('emails.order_ready', compact('orders'), function ($m) {
                //         $m->from('no-reply@augenlabs.com', 'Augen Labs');
                //
                //         // $m->to('mkt@dlo.com.mx', 'Yorch')->subject('Augen TEST');
                //         // $m->bcc('pgarcia@dlo.com.mx')->subject('Augen TEST');
                //         $m->to('sistemas@augenlabs.com')->subject('Augen TEST');
                //     });
                // }


                return view('emails.order_ready', compact('orders'));

                return response()->json(['msg' => 'Test mail sent']);
            }
        } else {
            $order = Order::findOrFail($id);
            return true;
        }
    }

    public function refreshCourtesy() {
        $orders = Order::whereHas('client', function($query) {
            $query->where('name', 'LIKE', '%CORTESIA%');
        })->where('status', 'entregado')
        ->with('client')
        ->update([
            'status' => 'pagado',
            'payment_date' => date('Y-m-d'),
            'payed' =>  1,
        ]);

        return response()->json(['msg' => 'Orders refreshed']);
    }

    public function buenFin() {
        $readingHeaders = true;
        // echo '<table>';
        if (($manager = fopen(base_path('import.csv'), "r")) !== FALSE) {
            while (($row = fgetcsv($manager, 1000, ",")) !== FALSE) {
                if($readingHeaders) {
                    $readingHeaders = false;
                    continue;
                }

                $promo = Promo::findOrFail(1);

                // echo '<tr>';

                if($row[1] == 'SLP')
                    $branch = Branch::findOrFail(10);
                else if($row[1] == 'MTY')
                    $branch = Branch::findOrFail(8);
                else
                    $branch = Branch::where('name', 'LIKE', '%' .  $row[1] . '%')->first();

                // if(is_null($branch))
                //     dd($row);
                // dd($row);
                DB::enableQueryLog();
                $order = Order::whereIn('rx', [ str_replace("O", "0", $row[2]), '0' . $row[2], str_replace("*", "", $row[2]) ])
                              ->where('branch_id', $branch->id)
                              ->whereBetween('created_at', [ $promo->starts_at, $promo->ends_at])
                              ->first();

              if(is_null($order)) {

                  echo $row[2] . '<br/>';
                  continue;
                  // dd(DB::getQueryLog());
              }
              continue;


                if(!is_null($order)) {
                    // $order->discount_admin = 0.0;
                    // $orde
                    if($order->discount_admin == 0.0) {
                        // $partialAmount = round(floatval($order->total) - floatval($order->discount) - floatval($order->discount_admin) + floatval($order->iva), 2);
                        $finalPrice = floatval($order->total) / 2;

                        $order->iva = $this->_recalculateTax($finalPrice, $order->laboratory_id, $finalPrice * 0.16);
                        $order->promo_discount = $finalPrice + $order->iva;

                        $order->promos()->sync([1]);
                        $order->save();
                        // dd($order->iva, $finalPrice);

                        // dd($partialAmount, $finalPrice, $order->iva);
                    }
                }

                // if(is_null($order)) {
                //
                //     echo $row[2] . '<br/>';
                //     continue;
                //     // dd(DB::getQueryLog());
                // }
                // else
                //     continue;
                    // DB::disableQueryLog();
                              // ->whereRaw('DATE(created_at) >= "' . vsprintf('%3$s-%2$s-%1$s', explode('/', $row[0])) . '"')



                // if(count($order) > 1 || count($order) == 0) {
                //     // dd($row);$order = Order::where('rx', str_replace("O", "0", $row[2]))
                //     $order = Order::where('rx', '0' . $row[2])
                //                   ->where('branch_id', $branch->id)
                //
                //                   // ->whereRaw('DATE(created_at) >= "' . vsprintf('%3$s-%2$s-%1$s', explode('/', $row[0])) . '"')
                // $order->promos()->sync([1]);
                //                   ->get();
                // }
                // if(count($order) > 1 || count($order) == 0) {
                //     echo $row[2] . ' - ' . $row[0] . '<br/>';
                // }
                // else {
                //     DB::disableQueryLog();
                //     continue;
                //     $order = $order[0];
                //     $promoId = 1;
                //
                //     // dd($order);
                //
                //     echo '
                //         <td> ' . $order->subtotal .'</td>
                //         <td> ' . floatval($order->subtotal / 2)  .'</td>
                //     ';
                // }

                // echo '</tr>';
            }
            fclose($manager);
            return response()->json(['msg' => 'Orders with promo :)']);
        } else {
            return response()->json(['msg' => 'No se encontró el archivo']);
        }
        // echo '</table>';

    }

    public function cancelOrder($id) {
        $order = Order::findOrFail($id);
        if($order->payed == 1)
            return response()->json(['msg' => 'RX no se puede cancelar, ya se encuentra pagada']);

        $order->cancelled = 1;
        $order->status = 'cancelado';
        $order->user_id = Auth::user()->id;

        $order->save();

        return response()->json(['msg' => 'RX cancelada correctamente']);
    }

    public function partalPayOrder($id) {
        $order = Order::findOrFail($id);
        $order->payed = 1;
        $order->user_id = Auth::user()->id;

        if($order->status == 'entregado') {
            $order->payment_date = date('Y-m-d');
            $order->status = 'pagado';
        }

        $order->save();

        return response()->json(['msg' => 'Rx reportada como pagada correctamente']);
    }

    public function applyCourtesy($id) {
        $order = Order::findOrFail($id);
        $order->courtesy = 1;
        $order->status = 'pagado';
        $order->user_id = Auth::user()->id;

        $order->save();

        return response()->json(['msg' => 'Cortesía aplicada correctamente']);
    }

    public function generateRx(Request $request) {
        if($request->has('paqueteria')) {
            $rx = 'PAQ-';
            $actualSold = Order::where('rx', 'LIKE', '%PAQ-%')->count();
            $actualSold++;

            $rx .= str_pad($actualSold, 5, '0', STR_PAD_LEFT);

            return $rx;
        } else {
            $rx = 'AM-';
            $actualSold = Order::where('rx', 'LIKE', '%AM-%')->count();
            $actualSold++;

            $rx .= str_pad($actualSold, 5, '0', STR_PAD_LEFT);

            return $rx;
        }
    }

    public function checkDuplicate($rx) {
        $today = date('Y-m-d');
        $order = Order::where('rx', $rx)
                      ->whereRaw("DATE(created_at) = '$today'")
                      ->first();

        if(!is_null($order))
            return 'true';
        return 'false';
    }

    public function tableSearch(Request $request) {
        $rx = $request->query('rx');

        $orders = Order::where('rx', $rx)->get();

        foreach ($orders as &$order) {
            $order->productHas;
            $order->extras;
            $order->client;
            $order->branch;

            if(isset($order->promo_discount))
                $order->total_real = $order->promo_discount;//number_format(floatval($order->total)  - floatval($order->discount) - floatval($order->discount_admin) + floatval($order->iva), 2);
            else
                $order->total_real = floatval(number_format(floatval($order->total)  - floatval($order->discount) - floatval($order->discount_admin) + floatval($order->iva), 2));

        }

        return response()->json($orders);
    }

    public function logTable($id) {
        $data = DB::table('orders_log')
                  ->where('order_id', $id)
                  ->get();

        foreach ($data as $i => $row) {
            $row[$i]->user = User::findOrFail($row->user_id);
        }

        return response()->json($data);
    }

    public function changeStatusWarranty($id, Request $request) {
        $user = Auth::user();
        $order = Order::findOrFail($id);
        $branch = Branch::find($order->branch_id);
        $order->status = 'garantia';
        $order->warranty_date = date('Y-m-d H:i:s');

        $orderData = $request->input('order');

        $checkform = false;
        if ($orderData['rx_diagonal_ed'] != null && $orderData['rx_diagonal_ed'] != '') {$checkform = true;}
        if ($orderData['rx_horizontal_a'] != null && $orderData['rx_horizontal_a'] != '') {$checkform = true;}
        if ($orderData['rx_observaciones'] != null && $orderData['rx_observaciones']  != '') {$checkform = true;}
        if ($orderData['rx_od_adicion'] != null && $orderData['rx_od_adicion'] != '') {$checkform = true;}
        if ($orderData['rx_od_adicion_dos'] != null && $orderData['rx_od_adicion_dos'] != '') {$checkform = true;}
        if ($orderData['rx_od_altura'] != null && $orderData['rx_od_altura'] != '') {$checkform = true;}
        if ($orderData['rx_od_altura_dos'] != null && $orderData['rx_od_altura_dos'] != '') {$checkform = true;}
        if ($orderData['rx_od_cilindro'] != null && $orderData['rx_od_cilindro'] != '') {$checkform = true;}
        if ($orderData['rx_od_cilindro_dos'] != null &&  $orderData['rx_od_cilindro_dos'] != '') {$checkform = true;}
        if ($orderData['rx_od_dip'] != null && $orderData['rx_od_dip'] != '') {$checkform = true;}
        if ($orderData['rx_od_dip_dos'] != null && $orderData['rx_od_dip_dos'] != '') {$checkform = true;}
        if ($orderData['rx_od_eje'] != null && $orderData['rx_od_eje'] != '') {$checkform = true;}
        if ($orderData['rx_od_eje_dos'] != null && $orderData['rx_od_eje_dos'] != '') {$checkform = true;}
        if ($orderData['rx_od_esfera'] != null && $orderData['rx_od_esfera'] != '') {$checkform = true;}
        if ($orderData['rx_od_esfera_dos'] != null && $orderData['rx_od_esfera_dos'] != '') {$checkform = true;}
        if ($orderData['rx_puente'] != null && $orderData['rx_puente'] != '') {$checkform = true;}
        if ($orderData['rx_servicios'] != null && $orderData['rx_servicios'] != '') {$checkform = true;}
        if ($orderData['rx_tallado'] != null && $orderData['rx_tallado'] != '') {$checkform = true;}
        if ($orderData['rx_tipo_ar'] != null && $orderData['rx_tipo_ar'] != '') {$checkform = true;}
        if ($orderData['rx_tipo_armazon'] != null && $orderData['rx_tipo_armazon'] != '') {$checkform = true;}
        if ($orderData['rx_vertical_b'] != null && $orderData['rx_vertical_b'] != '') {$checkform = true;}

        if($checkform) {
            $rxFields = [
                'rx_rx',
                'rx_fecha',
                'rx_cliente',
                'rx_od_esfera',
                'rx_od_cilindro',
                'rx_od_eje',
                'rx_od_adicion',
                'rx_od_dip',
                'rx_od_altura',
                'rx_od_esfera_dos',
                'rx_od_cilindro_dos',
                'rx_od_eje_dos',
                'rx_od_adicion_dos',
                'rx_od_dip_dos',
                'rx_od_altura_dos',
                'rx_diseno',
                'rx_material',
                'rx_caracteristicas',
                'rx_tipo_ar',
                'rx_tallado',
                'rx_servicios',
                'rx_tipo_armazon',
                'rx_horizontal_a',
                'rx_vertical_b',
                'rx_diagonal_ed',
                'rx_puente',
                'rx_observaciones',
            ];

            foreach($rxFields as $attr) {
                $order->{$attr} = $orderData[$attr];
            }
            $data = $order->toArray();
            $data['pvd'] = $branch->name;
            $data['laboratory'] = $branch->Laboratory->name;
            $pdf = PDF::loadView('plantillas.requestrx',['inputs' => $data])->setPaper('A5');
            $content = $pdf->download()->getOriginalContent();
            // Crear el archivo y almacenarlo en el storage
            Storage::disk('public')->put('docs/order-'.$order->id.'.pdf',$content);
            
            $mapLabs = [
                1 => 'americas',
                2 => 'ens',
                3 => 'chap',
                4 => 'mty',
                5 => 'pue',
                6 => 'slp'
            ];
            
            if(in_array($data['laboratory_id'], [1, 2, 3, 4, 5, 6])) {
                // $cc = 'captura' . $mapLabs[$data['laboratory_id']] . '@augenlabs.com';
                Mail::to('sistemas@augenlabs.com')
                // ->cc($cc)
                ->send(new RequestRx($data, $content, 'GARANTÍA'));
            } else {
                Mail::to('sistemas@augenlabs.com')->send(new RequestRx($data, $content, 'GARANTÍA'));
            }
        }
            
        $order->save();
        $reason = $request->input('reason');

        DB::table('orders_log')
          ->insert([
              'order_id' => $id,
              'user_id' => Auth::user()->id,
              'reason'  => $reason,
              'logged_at' => date('Y-m-d H:i:s')
        ]);

        return response()->json(['msg' => 'Estatus de la orden se cambio a ' . $order->status]);
    }

    public function export(Request $request)
    {
        
        try {
            Excel::store(new OrdersExport($request->data), 'pedidos.xlsx','public');
        }catch (\Exception $e) {
            report($e);
            return response()->json($e,500);
        }
       
        return 'ok';// Excel::download(new OrdersExport($request->data), 'pedidos.xlsx');
    }
    
    public function indexEntradasSalidas(Request $request)
    {
        ini_set('memory_limit',-1);
        $start = $request->start;
        $end = $request->end;
       

        $start = date("Y-m-d H:i:s", strtotime($start." 00:00:00"));
        $end = date("Y-m-d H:i:s", strtotime($end." 23:59:59"));

        $orders = Order::select('orders.*','clients.name as client_name','clients.comertial_name as comertial_name','branches.name as branch_name')
								
                    ->leftJoin('clients', 'orders.client_id', '=', 'clients.id')
                    ->leftJoin('branches', 'orders.branch_id', '=', 'branches.id')
                 
                    ->where("orders.created_at", ">=" ,$start)
                    ->where("orders.created_at", "<=", $end)
                    ->orderBy('orders.created_at', 'desc')
                    ->get();

        foreach ($orders as $key => $value) {
            $value->productHas;
            $value->extras;
            //$value->client;
            //$value->branch;
        }

        foreach ($orders as $key => $value) {
            $orders[$key] = $orders[$key]->toArray();
            if(is_null($orders[$key]['client'])){
                $orders[$key] = array_merge($orders[$key], ['client'=>['name'=> 'Cliente eliminado']]);
            }
        }
        return response()->json($orders, 200, [], JSON_NUMERIC_CHECK);
    }
    public function requestRx($id)
    {
        $order = Order::find($id);
        $branch = Branch::find($order->branch_id);
        $order->pvd = $branch->name;
        $order->laboratory = $branch->Laboratory->name;
    
        $order->date = date('d-m-Y');
        if ($order && $branch) {

            ini_set('memory_limit', '-1');

            $pdf = PDF::loadView('plantillas.requestrx',['inputs' => $order])->setPaper('A5');
            $content = $pdf->download()->getOriginalContent();

             // Crear el archivo y almacenarlo en el storage
            Storage::disk('public')->put('docs/order-'.$order->id.'.pdf',$content);

            $user = User::find($order->client_id);
            if ($user) {
                //Mail::to('sistemas@augenlabs.com')->send(new ContractComplete( $inputs,$content));
            }
        }
        return 'https://dev.augenlabs.com/storage/app/public/docs/order-'.$order->id.'.pdf';
    }
    public function requestRxSave(Request $request)
    {
        

        $data = $request->rx;
        
        if (isset($request->user['branch'])) {
            $data['pdv'] = $request->user['branch']['name'];
        }
        
        if ($data) {
            
            ini_set('memory_limit', '-1');

            $pdf = PDF::loadView('plantillas.requestrx',['inputs' => $data])->setPaper('A5');
            $content = $pdf->download()->getOriginalContent();

             // Crear el archivo y almacenarlo en el storage
            Storage::disk('public')->put('docs/order-aaaa.pdf',$content);

            

           
            if (isset($request->user['email'])) {
                if($data['rx_rx'] !== '') {
                    Mail::to($request->user['email'])->send(new RequestRx( $data,$content));
                }
            }
        }
        return 'ok';
    }

    public function getOrdersOpcs($id)
    {
        $currentDate = Carbon::now();
        $oneMonthAgo = $currentDate->subMonth()->format('Y-m-d'); // Calculate the date one month ago

        $orders = Order::where('client_id',$id)
                        ->whereIn('status',['entregado', 'garantia'])
                        ->where(function($query) use ($oneMonthAgo) {
                            $query->where('delivered_date', '>=', $oneMonthAgo)
                                  ->orWhere('delivered_date2', '>=', $oneMonthAgo);
                        })
                        ->get();
        
        foreach ($orders as $key => $value) {
            $value->total =  floatval($value->total);
            $value->subtotal = floatval($value->total) / (1 + (in_array($value->laboratory_id, [2, 10]) ? 0.08 : 0.16));
            $value->total = floatval($value->total) - floatval($value->discount) - floatval($value->discount_admin) + floatval($value->iva);
            
            if($value->status == 'garantia')
                $value->total_custom = '- $ '.number_format($value->total,2);
            else
                $value->total_custom = '$ '.number_format($value->total,2);
            $value->rxBtn = '<button class="btn btn-secondary"><i class="fas fa-eye"></i> </button>';
            $value->actions = '<button class="btn btn-danger btn-discount" data-total="' . $value->total . '"><i class="fa fa-percent" aria-hidden="true"></i> </button>';
            $value->branch->laboratory;
            $value->productHas;
            
        }
        return response()->json($orders);
    }

    public function resendRx(Request $request) {
        try {
            $order = Order::findOrFail($request->input('order_id'));
            $checkform = false;
            if ($order->rx_diagonal_ed != null && $order->rx_diagonal_ed != '') {$checkform = true;}
            if ($order->rx_horizontal_a != null && $order->rx_horizontal_a != '') {$checkform = true;}
            if ($order->rx_observaciones != null && $order->rx_observaciones  != '') {$checkform = true;}
            if ($order->rx_od_adicion != null && $order->rx_od_adicion != '') {$checkform = true;}
            if ($order->rx_od_adicion_dos != null && $order->rx_od_adicion_dos != '') {$checkform = true;}
            if ($order->rx_od_altura != null && $order->rx_od_altura != '') {$checkform = true;}
            if ($order->rx_od_altura_dos != null && $order->rx_od_altura_dos != '') {$checkform = true;}
            if ($order->rx_od_cilindro != null && $order->rx_od_cilindro != '') {$checkform = true;}
            if ($order->rx_od_cilindro_dos != null &&  $order->rx_od_cilindro_dos != '') {$checkform = true;}
            if ($order->rx_od_dip != null && $order->rx_od_dip != '') {$checkform = true;}
            if ($order->rx_od_dip_dos != null && $order->rx_od_dip_dos != '') {$checkform = true;}
            if ($order->rx_od_eje != null && $order->rx_od_eje != '') {$checkform = true;}
            if ($order->rx_od_eje_dos != null && $order->rx_od_eje_dos != '') {$checkform = true;}
            if ($order->rx_od_esfera != null && $order->rx_od_esfera != '') {$checkform = true;}
            if ($order->rx_od_esfera_dos != null && $order->rx_od_esfera_dos != '') {$checkform = true;}
            if ($order->rx_puente != null && $order->rx_puente != '') {$checkform = true;}
            if ($order->rx_servicios != null && $order->rx_servicios != '') {$checkform = true;}
            if ($order->rx_tallado != null && $order->rx_tallado != '') {$checkform = true;}
            if ($order->rx_tipo_ar != null && $order->rx_tipo_ar != '') {$checkform = true;}
            if ($order->rx_tipo_armazon != null && $order->rx_tipo_armazon != '') {$checkform = true;}
            if ($order->rx_vertical_b != null && $order->rx_vertical_b != '') {$checkform = true;}
    
            if($checkform) {
                $branch = Branch::find($order->branch_id);
                $data = $order->toArray();
                $data['pvd'] = $branch->name;
                $data['laboratory'] = $branch->laboratory->name;
                $pdf = PDF::loadView('plantillas.requestrx',['inputs' => $data])->setPaper('A5');
                $content = $pdf->download()->getOriginalContent();
                Storage::disk('public')->put('docs/order-'.$order->id.'.pdf',$content);
        
                $mailTo = $request->input('email');
                $title = 'REENVÍO';
                Mail::to($mailTo)->send(new RequestRx($data, $content, $title));
                return response()->json(['status' => true, 'message' => 'RX Sent']);
            } else {
                return response()->json(['status' => false, 'message' => 'No info to send']);
            }
        } catch(\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}
