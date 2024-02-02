<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Response;
use Illuminate\Support\Facades\DB;
use JWTAuth;

use App\Client;
use App\Order;
use App\Product;
use App\Type;
use App\ProductHasSubcategory;
use App\Subcategory;
use App\Branch;
use App\Purchase;
use App\Notification;
use App\User;
use Carbon\Carbon;

class AppController extends Controller {
    protected $ttl = 60;
    public function __construct() {
        \Config::set('auth.providers.users.model', \App\Client::class);
    }

    public function index() {
        $author = 'Edgar Sandoval <sistemas@augenlabs.com>';
        $name   = 'Customer API Service';
        $version = '0.1';
        $releaseDate = '2021-10-27';

        return Response::set(true, null, compact('name', 'author', 'version', 'releaseDate'));
    }

    public function login(Request $request) {
        try {
            $credentials = $request->all();
            $client = Client::where('email', $credentials['email'])
                            ->orWhere('id', $credentials['email'])
                            ->first();

            if(is_null($client))
                return response()->json(Response::set(false, 'Usuario y/o contraseña incorrecto'), 401);

            if(!\Hash::check($credentials['password'], $client->password))
                return response()->json(Response::set(false, 'Usuario y/o contraseña incorrecto'), 401);

            if($client->status != 'Activo')
                return response()->json(Response::set(false, 'Usuario Inactivo'), 401);

            try {
                if(!$token = JWTAuth::fromUser($client)) {
                    return response()->json([
                        'status'    => false,
                        'message'   => 'Credenciales invalidas',
                        'data'      => null
                    ], 401);
                }
            } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

                return response()->json([
                    'status'    => false,
                    'message'   => 'Token expirado',
                    'data'      => null
                ], 500);

            } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

                return response()->json([
                    'status'    => false,
                    'message'   => 'Token invalido',
                    'data'      => null
                ], 500);

            } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {

                return response()->json([
                    'status'    => false,
                    'message'   => 'Token ausente: ' .  $e->getMessage(),
                    'data'      => null
                ], 500);

            }

            $ttl = $this->ttl;
            return Response::set(true, 'Bienvenido ' . $client->name, compact('token', 'ttl', 'client'));
        } catch(\Exception $e) {
            return  Response::handle($e);
        }
    }

    public function rxActives() {
        $firstDayOfMonth = date('2023-08-01') . ' 00:00:00'; // hard-coded '01' for first day
        $lastDayOfMonth  = date('Y-m-t') . ' 23:59:59';

        $client = \Auth::user();
        $orders = $client->orders()
                         ->whereBetween('created_at', [ $firstDayOfMonth, $lastDayOfMonth ])
                         ->get();

         foreach ($orders as &$nOrder) {
             // $nOrder->client;
             $nOrder->productHas;
             $nOrder->extras;
             $nOrder->branch;
             $nOrder->branch->laboratory;

             $nOrder->description = '';
             if (isset($nOrder->product['id']))
                 $nOrder->description .= $nOrder->product['name'] . ', ' . $nOrder->product['subcategory_name'] . ', ' .  $nOrder->product['type_name'];
             if (is_array($nOrder->extras) && count($nOrder->extras) > 0) {
                 $nOrder->description .= @implode(', ', array_map(function($e) {
                     return $e->name;
                 }, $nOrder->extras));
             }

             $nOrder->totalPrice = floatval($nOrder->total)  - floatval($nOrder->discount) - floatval($nOrder->discount_admin) + floatval($nOrder->iva);
         }

        return Response::set(true, null, compact('orders'));
    }

    public function rxPendings() {
        $client = \Auth::user();
        $orders = $client->orders()
                         ->where('status', 'entregado')
                         ->whereNull('payment_date')
                         ->get();

         foreach ($orders as &$nOrder) {
             // $nOrder->client;
             $nOrder->productHas;
             $nOrder->extras;

             $nOrder->description = '';
             if (isset($nOrder->product['id']))
                 $nOrder->description .= $nOrder->product['name'] . ', ' . $nOrder->product['subcategory_name'] . ', ' .  $nOrder->product['type_name'];
             if (is_array($nOrder->extras) && count($nOrder->extras) > 0) {
                 $nOrder->description .= @implode(', ', array_map(function($e) {
                     return $e->name;
                 }, $nOrder->extras));
             }

             $nOrder->totalPrice = floatval($nOrder->total)  - floatval($nOrder->discount) - floatval($nOrder->discount_admin) + floatval($nOrder->iva);
         }

        return Response::set(true, null, compact('orders'));
    }

    public function sendOtp(Request $request) {
        $logoUrl = 'https://augenlabs.com/_next/static/media/Logo_Menu_Raiz.1861f2f9.svg';

        // Generate a random number between 0 and 9999
        $randomNumber = mt_rand(0, 999999);

        // Pad with zeros to ensure it's a 4-digit number
        $loginCode = str_pad($randomNumber, 6, '0', STR_PAD_LEFT);


        \Mail::send('emails.login_code', ['logoUrl' => $logoUrl, 'loginCode' => $loginCode], function ($message) {
            $message->to('sistemas@augenlabs.com')->subject('Tu código de acceso');
        });

        return Response::set(true, 'Correo OTP enviado');
    }

    public function products() {
        $products = Product::all();

        return Response::set(true, null, compact('products'));
    }
    
    public function types() {
        $types = Type::all();
        
        return Response::set(true, null, compact('types'));
    }
    
    public function subcategories($id) {
        $subcategories = DB::select("SELECT DISTINCT phc.*, c.name FROM product_has_subcategories phc INNER JOIN categories c ON phc.category_id = c.id WHERE phc.type_id = $id AND YEAR(phc.created_at) = 2023 GROUP BY subcategory_id;");
        return Response::set(true, null, compact('subcategories'));
    }

    public function rxRequest(Request $request) {
        $client = \Auth::user();
        $phs = ProductHasSubcategory::find($request->input('rx_material'));
        $purchase=new Purchase(array(
            "user_id"=>0,
            "client_id"=>$client,
            "total"=>$phs->price,
            "description"=> 'Order generada por el portal'
        ));
        $purchase->save();

        $subcategory = Subcategory::find($phs->subcategory_id);
        $branch = Branch::find($client->branch_id);

        $value = $request->all();
        $value['product_has_subcategory_id'] = $phs->id;
        $value['rx_material'] = $subcategory->name;
        $value['discount'] = 0;
        $value['service'] = 0;
        $value['total'] = $phs->price;
        $value['iva'] = $phs->price / 1.16;
        $value['rx'] = $value['rx_rx'];

        $order = new Order(array(
            "purchase_id"=>$purchase->id,
            "product_has_subcategory_id"=>$value['product_has_subcategory_id'],
            "price"=>$phs->price,
            "discount"=>$value['discount'],
            "discount_percent"=>0,
            "service"=>$value['service'],
            "total"=>$value['total'],
            "iva"=>$value['iva'],
            "rx"=>$value['rx'],
            "laboratory_id"=> $branch->laboratory->id,
            "branch_id"=>$branch->id,
            "client_id"=>$client->id,

            "rx_rx" => isset($value['rx_rx']) ? $value['rx_rx'] : null,
            "rx_fecha" => isset($value['rx_fecha']) ? $value['rx_fecha'] : null,
            "rx_cliente" => isset($value['rx_cliente']) ? $value['rx_cliente'] : null,

            "rx_od_esfera" => isset($value['rx_od_esfera']) ? $value['rx_od_esfera'] : null,
            "rx_od_cilindro" => isset($value['rx_od_cilindro']) ? $value['rx_od_cilindro'] : null,
            "rx_od_eje" => isset($value['rx_od_eje']) ? $value['rx_od_eje'] : null,
            "rx_od_adicion" => isset($value['rx_od_adicion']) ? $value['rx_od_adicion'] : null,
            "rx_od_dip" => isset($value['rx_od_dip']) ? $value['rx_od_dip'] : null,
            "rx_od_altura" => isset($value['rx_od_altura']) ? $value['rx_od_altura'] : null,
            

            "rx_od_esfera_dos" => isset($value['rx_od_esfera_dos']) ? $value['rx_od_esfera_dos'] : null,
            "rx_od_cilindro_dos" => isset($value['rx_od_cilindro_dos']) ? $value['rx_od_cilindro_dos'] : null,
            "rx_od_eje_dos" => isset($value['rx_od_eje_dos']) ? $value['rx_od_eje_dos'] : null,
            "rx_od_adicion_dos" => isset($value['rx_od_adicion_dos']) ? $value['rx_od_adicion_dos'] : null,
            "rx_od_dip_dos" => isset($value['rx_od_dip_dos']) ? $value['rx_od_dip_dos'] : null,
            "rx_od_altura_dos" => isset($value['rx_od_altura_dos']) ? $value['rx_od_altura_dos'] : null,
        
            "rx_diseno" => isset($value['rx_diseno']) ? $value['rx_diseno'] : null,
            "rx_material" => isset($value['rx_material']) ? $value['rx_material'] : null,
            "rx_caracteristicas" => isset($value['rx_caracteristicas']) ? $value['rx_caracteristicas'] : null,
            "rx_tipo_ar" => isset($value['rx_tipo_ar']) ? $value['rx_tipo_ar'] : null,
            "rx_tallado" => isset($value['rx_tallado']) ? $value['rx_tallado'] : null,
            "rx_servicios" => isset($value['rx_servicios']) ? $value['rx_servicios'] : null,

            "rx_tipo_armazon" => isset($value['rx_tipo_armazon']) ? $value['rx_tipo_armazon'] : null,
            "rx_horizontal_a" => isset($value['rx_horizontal_a']) ? $value['rx_horizontal_a'] : null,
            "rx_vertical_b" => isset($value['rx_vertical_b']) ? $value['rx_vertical_b'] : null,
            "rx_diagonal_ed" => isset($value['rx_diagonal_ed']) ? $value['rx_diagonal_ed'] : null,
            "rx_puente" => isset($value['rx_puente']) ? $value['rx_puente'] : null,
            "rx_observaciones" => isset($value['rx_observaciones']) ? $value['rx_observaciones'] : null,
            
        ));

        // $order->save();

        $notification = new Notification();
        $notification->text = 'Una RX receta ha sido solicitada';
        $notification->type = 'primary';
        // $notification->icon = 'primary';
        
        $user = User::where('laboratory_id', $branch->laboratory_id)
                    ->where('email', 'LIKE', 'coordinador%')
                    ->first();

        $notification->user_id = $user->id;
        $notification->url = '';
        $notification->metadata = json_encode($order->toArray());
        $notification->save();

        return Response::set(true, 'RX Creada correctamente', compact('order'));
    }

    public function getPeriods() {
        $firstDayOfMonth = date('2023-08-01') . ' 00:00:00'; // hard-coded '01' for first day
        $lastDayOfMonth  = date('Y-m-t') . ' 23:59:59';

        $client = \Auth::user();
        $orders = $client->orders()
                         ->whereBetween('created_at', [ $firstDayOfMonth, $lastDayOfMonth ])
                         ->get();

        foreach ($orders as &$nOrder) {
             // $nOrder->client;
             $nOrder->productHas;
             $nOrder->extras;

             $nOrder->description = '';
             if (isset($nOrder->product['id']))
                 $nOrder->description .= $nOrder->product['name'] . ', ' . $nOrder->product['subcategory_name'] . ', ' .  $nOrder->product['type_name'];
             if (is_array($nOrder->extras) && count($nOrder->extras) > 0) {
                 $nOrder->description .= @implode(', ', array_map(function($e) {
                     return $e->name;
                 }, $nOrder->extras));
             }

             $nOrder->totalPrice = floatval($nOrder->total)  - floatval($nOrder->discount) - floatval($nOrder->discount_admin) + floatval($nOrder->iva);
        }

        $orders = $orders->toArray();

        $groupedOrders = collect($orders)->map(function ($order) {
            $date = Carbon::parse($order['created_at']);
            $weekStart = $date->startOfWeek()->format('Y-m-d'); // Monday
            $weekEnd = $date->endOfWeek()->subDays(1)->format('Y-m-d'); // Saturday
            $weekNumber = $date->weekOfYear;
            $year = $date->year;
        
            return [
                'id' => uniqid(),
                'name' => "Semana {$weekNumber} {$year}",
                'start_date' => $weekStart,
                'end_date' => $weekEnd,
                // 'order' => $order
            ];
        })->unique('name'); // Ensure only one record per week
        
        $result = $groupedOrders->values()->all();

        return Response::set(true, 'RX Creada correctamente', $result);
    }

    public function getPeriod(Request $request) {
        $client = \Auth::user();
        $orders = $client->orders()
                         ->whereBetween('created_at', [ $request->input('start_date'), $request->input('end_date') ])
                         ->get();

        foreach ($orders as &$nOrder) {
             // $nOrder->client;
             $nOrder->productHas;
             $nOrder->extras;

             $nOrder->description = '';
             if (isset($nOrder->product['id']))
                 $nOrder->description .= $nOrder->product['name'] . ', ' . $nOrder->product['subcategory_name'] . ', ' .  $nOrder->product['type_name'];
             if (is_array($nOrder->extras) && count($nOrder->extras) > 0) {
                 $nOrder->description .= @implode(', ', array_map(function($e) {
                     return $e->name;
                 }, $nOrder->extras));
             }

             $nOrder->totalPrice = floatval($nOrder->total)  - floatval($nOrder->discount) - floatval($nOrder->discount_admin) + floatval($nOrder->iva);
        }

        return Response::set(true, null, compact('orders'));
    }

    public function notFound() {
        return response()->json(Response::set(false, 'Method could not be found'), 404);
    }
}
