<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use JWTAuth;

use App\Client;
use App\Order;
use App\Product;
use App\Type;
use App\ProductHasSubcategory;
use App\Subcategory;
use App\Branch;
use App\Laboratory;
use App\Purchase;
use App\Notification;
use App\User;
use Carbon\Carbon;
use Mail;
use PDF;


use Illuminate\Support\Facades\Storage;
use App\Mail\RequestRx;

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
                            ->first();

            if(is_null($client))
                return response()->json(Response::set(false, 'Usuario y/o contraseña incorrecto'), 401);

            // if(!\Hash::check($credentials['password'], $client->password))
            //     return response()->json(Response::set(false, 'Usuario y/o contraseña incorrecto'), 401);

            if($client->status != 'Activo')
                return response()->json(Response::set(false, 'Usuario Inactivo'), 401);
        
            $cachedOtp = Cache::get('otp_' . $credentials['email']);

            if (!$cachedOtp || $cachedOtp != $request->code) {
                return response()->json([
                    'status'    => false,
                    'message'   => 'El código proporcionado es inválido o ya expiró',
                    'data'      => null
                ], 403);
            }

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

            Cache::forget('otp_' . $request->email);

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
                         ->orderBy('created_at', 'DESC')
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
        $email = $request->input('email');
        $logoUrl = 'https://augenlabs.com/_next/static/media/Logo_Menu_Raiz.1861f2f9.svg';

        // Generate a random number between 0 and 9999
        $randomNumber = mt_rand(0, 999999);

        // Pad with zeros to ensure it's a 4-digit number
        $loginCode = str_pad($randomNumber, 6, '0', STR_PAD_LEFT);

        // Store OTP in cache. Here, 'email' is used as part of the key to uniquely identify the OTP for the user.
        Cache::put('otp_'. $email, $loginCode, now()->addMinutes(5)); // OTP expires in 5 minutes

        /* \Mail::send('emails.login_code', ['logoUrl' => $logoUrl, 'loginCode' => $loginCode], function ($message) {
            $message->to(['edgar.desarrollo@gmail.com', 'sistemas@augenlabs.com'])->subject('Tu código de acceso');
        }); */

        return Response::set(true, 'Correo OTP enviado', compact('loginCode'));
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
        $value['rx_rx'] = str_replace("_", "", $value['rx_rx']); // Remove all undescores for the front mask
        $value['product_has_subcategory_id'] = $phs->id;
        $value['rx_material'] = $subcategory->name;
        $value['discount'] = 0;
        $value['service'] = 0;
        $value['total'] = $phs->price;
        $value['iva'] = number_format($phs->price / 1.16, 2);
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
            "rx_observaciones" => isset($value['rx_observaciones']) ? $value['rx_observaciones'] : '',
            
        ));

        // $order->save();

        $order->branch;
        $order->branch->laboratory;
        $order->productHas;

        $notification = new Notification();
        $notification->text = 'RX ' . $order->rx . ' - Solicitud desde el portal';
        $notification->type = 'rx';
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
        // $firstDayOfMonth = date('2023-08-01') . ' 00:00:00'; // hard-coded '01' for first day
        // $lastDayOfMonth  = date('Y-m-t') . ' 23:59:59';

        // $client = \Auth::user();
        // $orders = $client->orders()
        //                  ->whereBetween('created_at', [ $firstDayOfMonth, $lastDayOfMonth ])
        //                  ->get();

        // foreach ($orders as &$nOrder) {
        //      // $nOrder->client;
        //      $nOrder->productHas;
        //      $nOrder->extras;

        //      $nOrder->description = '';
        //      if (isset($nOrder->product['id']))
        //          $nOrder->description .= $nOrder->product['name'] . ', ' . $nOrder->product['subcategory_name'] . ', ' .  $nOrder->product['type_name'];
        //      if (is_array($nOrder->extras) && count($nOrder->extras) > 0) {
        //          $nOrder->description .= @implode(', ', array_map(function($e) {
        //              return $e->name;
        //          }, $nOrder->extras));
        //      }

        //      $nOrder->totalPrice = floatval($nOrder->total)  - floatval($nOrder->discount) - floatval($nOrder->discount_admin) + floatval($nOrder->iva);
        // }

        // $orders = $orders->toArray();

        // $groupedOrders = collect($orders)->map(function ($order) {
        //     $date = Carbon::parse($order['created_at']);
        //     $weekStart = $date->startOfWeek()->format('Y-m-d'); // Monday
        //     $weekEnd = $date->endOfWeek()->subDays(1)->format('Y-m-d'); // Saturday
        //     $weekNumber = $date->weekOfYear;
        //     $year = $date->year;
        
        //     return [
        //         'id' => uniqid(),
        //         'name' => "Semana {$weekNumber} {$year}",
        //         'start_date' => $weekStart,
        //         'end_date' => $weekEnd,
        //         // 'order' => $order
        //     ];
        // })->unique('name'); // Ensure only one record per week
            
        // Set Carbon's locale to Spanish to use for day and month names
        setlocale(LC_TIME, 'es_ES.UTF-8', 'Spanish_Spain', 'Spanish');

        $date = Carbon::now();

        // Using format() with %d (day of the month), %B (full month name)
        $weekStart = strftime('%d de %B', $date->startOfWeek()->timestamp); // e.g., "02 de Junio"
        $weekEnd = strftime('%d de %B', $date->endOfWeek()->subDays(1)->timestamp); // e.g., "09 de Junio"
        $weekNumber = $date->weekOfYear;
        $year = $date->year;

        $result = [
            [
                'id' => uniqid(),
                'name' => "Semana {$weekNumber} {$year}",
                'start_date' => $weekStart,
                'end_date' => $weekEnd
            ]
        ];

        return Response::set(true, 'RX Creada correctamente', $result);
    }

    public function getPeriod(Request $request) {
        $firstDayOfMonth = date('2023-08-01') . ' 00:00:00'; // hard-coded '01' for first day
        $lastDayOfMonth  = date('Y-m-t') . ' 23:59:59';

        $client = \Auth::user();
        $orders = $client->orders()
                         ->whereBetween('created_at', [  $firstDayOfMonth, $lastDayOfMonth ])
                         ->orderBy('created_at', 'DESC')
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

             if($nOrder->passed < 0) {
                $nOrder->selected = 1;
             }
             $nOrder->totalPrice = floatval($nOrder->total)  - floatval($nOrder->discount) - floatval($nOrder->discount_admin) + floatval($nOrder->iva);
        }

        return Response::set(true, null, compact('orders'));
    }

    public function rxApprove($id) {
        $notification = Notification::findOrFail($id);

        $data = $notification->metadata;
        $data = json_decode($data, true);

        $order = new Order();
        foreach($data as $attr => $value) {
            if(in_array($attr, ['passed', 'cont_dias']))
                continue;
            if(is_string($value) || is_numeric($value) ) {
                $order->{ $attr } = $value;
            }
        }

        $order->save();
        $notification->delete();

        $branch = Branch::find($order->branch_id);
        $laboratory = Laboratory::findOrFail($order->laboratory_id);
        
        $data = $order->toArray();
        $data['pvd'] = $branch->name;
        $data['laboratory'] = $laboratory->name;

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
            ->send(new RequestRx($data, $content, 'CAPTURA'));
        } else {
            Mail::to('sistemas@augenlabs.com')->send(new RequestRx($data, $content, 'CAPTURA'));
        }

        return Response::set(true, 'RX Creada correctamente', compact('order'));
    }

    public function getSafiloNumber() {
        $prefix = 'SF-';

        $number = Order::where('rx', 'LIKE', $prefix . '%')->count() + 1;
        $rx = $prefix . str_pad($number, 5, '0', STR_PAD_LEFT);
        
        return response()->json(Response::set(true, null, compact('rx')));
    }

    public function notFound() {
        return response()->json(Response::set(false, 'Method could not be found'), 404);
    }
}
