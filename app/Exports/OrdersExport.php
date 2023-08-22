<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;

use App\Order;

class OrdersExport implements FromCollection 
{

    protected $data;
    function __construct($data) {
        $this->data = $data;
    }

    public function collection(){
        ini_set('memory_limit',-1);
        /*$datarows = [];
        ini_set('memory_limit',-1);
        $start = $this->data['start'];
        $end = $this->data['end'];
        $status = $this->data['status'];

        $start = date("Y-m-d H:i:s", strtotime($start." 00:00:00"));
        $end = date("Y-m-d H:i:s", strtotime($end." 23:59:59"));

        if(!$status['en_proceso'] && !$status['terminado'] && !$status['entregado'] && !$status['pagado'] && !$status['garantia'])
            return [];

        $orders = Order::query();

        if($this->data['search'] && $this->data['search'] != '')

            $orders = $orders->select('id','rx','created_at','price','service','discount','promo_discount','status','total','discount_admin','iva')->where('rx', $this->data['search'])->orderBy('created_at', 'desc');
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
        }*/
        
        
        return $this->data;
    }

}

?>