<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Response;
use JWTAuth;

use App\Client;
use App\Order;

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
        $firstDayOfMonth = date('Y-08-01') . ' 00:00:00'; // hard-coded '01' for first day
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

    public function notFound() {
        return response()->json(Response::set(false, 'Method could not be found'), 404);
    }
}
