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
        $start = date("Y-m-d H:i:s", strtotime("2022-01-01 00:00:00"));
        $end = date("Y-m-d H:i:s", strtotime("2023-01-01 23:59:59"));

       
        $orders = Order::query();

       
        $orders = $orders->where("created_at", ">=" ,$start)->where("created_at", "<=", $end)->orderBy('created_at', 'desc');


        $orders = $orders->get();
      

        foreach ($orders as $key => $value) {
            $value->productHas;
            $value->extras;
            $value->client;
            $value->branch;

            $orders[$key] = $orders[$key]->toArray();
            if(is_null($orders[$key]['client'])){
                $orders[$key] = array_merge($orders[$key], ['client'=>['name'=> 'Cliente eliminado']]);
            }

            $aux = [
                'rx' => $value['rx'],
                'name' => $value['client']['name'],
                'comertial_name' => $value['client']['comertial_name'],
                'branch_name' => $value['branch']['name'],
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
        }

        Excel::store(new OrdersExport($datarows), 'pedidos.xlsx','public');
        
        return $datarows;
    }
}
