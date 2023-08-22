<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Order;
use Excel;
use App\Exports\OrdersExport;
class ExcelOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:excel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ini_set('memory_limit',-1);
        $datarows = [];
        $start = date("Y-m-d H:i:s", strtotime("2021-01-01 00:00:00"));
        $end = date("Y-m-d H:i:s", strtotime("2021-12-31 23:59:59"));

       
        $datarows = Order::select('orders.id','orders.rx','clients.name as comertial_name','branches.name as branch_name','orders.created_at','orders.product_has_subcategory_id','orders.id as product_subcategory_name','orders.id as product_type_name','orders.id as extras_string','orders.price','orders.service','orders.total as subtotal','orders.discount','orders.promo_discount','orders.status','orders.total','orders.service','orders.discount_admin','orders.iva')
                        ->leftJoin('clients', 'orders.client_id', '=', 'clients.id')
                        ->leftJoin('branches', 'orders.branch_id', '=', 'branches.id');
                        //->leftJoin('order_has_extras', 'orders.id', '=', 'order_has_extras.order_id')
                        //->leftJoin('extras', 'order_has_extras.extra_id', '=', 'extras.id');
                    

       
        $datarows = $datarows->where("orders.created_at", ">=" ,$start)->where("orders.created_at", "<=", $end)->orderBy('orders.created_at', 'desc');


        $datarows = $datarows->get();
       

        foreach ($datarows as $key => $value) {
            $value->productHas;
            
            
            $value->product_has_subcategory_id = null;
            if (isset($value['product']['name'])) {
                $value->product_has_subcategory_id = $value['product']['name'];
            }

            $value->product_subcategory_name = null;
            if (isset($value['product']['subcategory_name'])) {
                $value->product_subcategory_name = $value['product']['subcategory_name'];
            }

            $value->product_type_name = null;
            if (isset($value['product']['type_name'])) {
                $value->product_type_name = $value['product']['type_name'];
            }
            
            
            $value->subtotal = $value['price'] + $value['service'];
            $value->total = ( $value['total'] + $value['discount'] + $value['discount_admin'] + $value['iva'] + $value['promo_discount']);

            //consulta los extras y los convierte a STRING
            $value->extras_string = implode(',',$value->extras->pluck('name')->toArray());

            //$value->subtotal = $value['price'] + $value['service'];
            //$value->total = ( $value['total'] + $value['discount'] + $value['discount_admin'] + $value['iva'] + $value['promo_discount']);
            /*$value->extras;
            $value->client;
            $value->branch;

            $orders[$key] = $orders[$key]->toArray();
            if(is_null($orders[$key]['client'])){
                $orders[$key] = array_merge($orders[$key], ['client'=>['name'=> 'Cliente eliminado']]);
            }

            $aux = [
                'rx' => $value['rx'],
                'name' => $value['client'] ? $value['client']['name'] : null,
                'comertial_name' => $value['client'] ? $value['client']['comertial_name'] : null,
                'branch_name' => $value['branch'] ? $value['branch']['name'] : null,
                'created_at' => $value['created_at'],
                
                'product_name' => $value['product']['name'],
                'product_subcategory_name' => $value['product']['subcategory_name'],
                'product_type_name' => $value['product']['type_name'],
                
                'extras' => null,
                'price' => $value['price'],
                'service' => $value['service'],
                'subtotal' => $value['price'] + $value['service'],
                'discount' => $value['discount'],
                'promo_discount' => $value['promo_discount'],
                'status' => $value['status'],
                'total' => ( $value['total'] + $value['discount'] + $value['discount_admin'] + $value['iva'] + $value['promo_discount']),

            ];



            array_push($datarows,$aux);
            dd($datarows);*/
        }
        
        Excel::store(new OrdersExport($datarows), '2021.xlsx','public');
        
        return $datarows;
    }
}
