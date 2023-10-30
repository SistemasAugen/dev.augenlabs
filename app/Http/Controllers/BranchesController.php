<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Branch;
use App\Order;
// use App\Town;
// use App\State;
// use App\Laboratory;
use App\Http\Requests\BranchRequest;
use Illuminate\Support\Facades\DB;
use App\ProductHasSubcategory;
use App\Profitability;
use App\Helpers\Response;

class BranchesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $branches=Branch::all();

        foreach ($branches as $key => &$value) {
            $value->town;
            $value->state;
            $value->laboratory;
        }

        return response()->json($branches);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BranchRequest $request)
    {
        $branch=new Branch(array(
            'name'=>$request->name,
            'state_id'=>$request->state_id,
            'town_id'=>$request->town_id,
            'laboratory_id'=>$request->laboratory_id,
            'address'=>$request->address,
            'timezone' => $request->timezone
        ));

        $branch->save();

        return response()->json($branch);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $branch = Branch::find($id);
        $branch->state;
        $branch->town;
        $branch->laboratory;

        date_default_timezone_set($branch->timezone);
        $branch->actualDateTime = date('h:i:s a');

        return response()->json($branch);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $branch=Branch::find($id);

        $branch->name=$request->name;
        $branch->state_id=$request->state_id;
        $branch->town_id=$request->town_id;
        $branch->laboratory_id=$request->laboratory_id;
        $branch->address=$request->address;
        $branch->timezone = $request->timezone;
        $branch->base = $request->base;

        $branch->save();

        // Edgar Sandoval hotfix

        $firstDayOfMonth = date('Y-m-01') . ' 00:00:00'; // hard-coded '01' for first day
        $lastDayOfMonth  = date('Y-m-t') . ' 23:59:59';

        DB::table('profitabilities')
          ->whereBetween('estimated_at', [$firstDayOfMonth, $lastDayOfMonth])
          ->where('branch_id', $id)
          ->update([
              'base' => $branch->base
          ]);

        //

        return response()->json($branch);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if($this->_destroy($id)){
            return response()->json(['msg'=>'Sucursal con ID '.$id.' eliminado.']);
        }
        else{
            return response()->json(['msg'=>'Ocurrio un error al eliminar.'],500);
        }
    }

    private function _destroy($id)
    {
        $branch=Branch::find($id);
        if($branch->delete()){
            return true;
        }
        else{
            return false;
        }

    }

    public function today(Request $request) {
        $startDate  = $request->query('from') . ' 00:00:00';
        $endDate    = $request->query('to') . ' 23:59:59';
        // $branches   = [];
        $subtotal   = 0.0;
        $discounts = 0.0;
        $total      = 0.0;
        $ordersCount = 0;

        $branches = Branch::all();
        foreach ($branches as &$branch) {
            $data = DB::table(with(new Order)->getTable())
                      ->selectRaw('SUM(total) AS q1, SUM(discount) as q2, SUM(discount_admin) AS q3,  SUM(iva) AS q4, count( DISTINCT(client_id)) AS q5')
                      ->whereBetween('created_at', [ $startDate, $endDate ])
                      ->where('status', '<>', 'cancelado')
                      ->where('branch_id', $branch->id)
                      // ->groupBy('client_id')
                      ->get();

            $data = $data[0];

            $branch->subtotal = floatval($data->q1) + floatval($data->q4);
            $branch->descuentos = floatval($data->q2);
            $branch->descuentos_admin = floatval($data->q3);
            $branch->total = $branch->subtotal - $branch->descuentos - $branch->descuentos_admin;

            $subtotal   += $branch->subtotal;
            $discounts += $branch->descuentos + $branch->descuentos_admin;

            $branch->clients_orders = $data->q5;
            $branch->orders = Order::whereBetween('created_at', [ $startDate, $endDate ])
                            ->where("branch_id", $branch->id)
                            ->where('status', '<>', 'cancelado')
                            ->count();

            $ordersCount += $branch->orders;
        }

        $total = $subtotal - $discounts;

        $avg = $total / $ordersCount;

        return response()->json(compact('branches', 'subtotal', 'discounts', 'total', 'ordersCount', 'avg'));
    }

    public function profitability(Request $request, $id) {
        $dateToStart = strtotime($request->input('period') . '-01');

        if ($dateToStart < strtotime('2021-08-01')) {
            $dataset = [];
            $base = 0;
            $topLimit = $base;
            return Response::set(true, null, compact('dataset', 'base', 'topLimit'));
            exit;
        }

        if($id == 0) {
            // Request for a general amount
            $branches = Branch::where('base', '>', 0)->get();

            $firstDayOfMonth = date('Y-m-01', $dateToStart) . ' 00:00:00'; // hard-coded '01' for first day
            $lastDayOfMonth  = date('Y-m-t', $dateToStart) . ' 23:59:59';

            $dataset = [];


            $base = 0.0;

            foreach($branches as $branch) {
                $previousBase = DB::table('profitabilities')
                                  ->where('branch_id', $branch->id)
                                  ->where('estimated_at', '<=', $lastDayOfMonth)
                                  ->orderBy('estimated_at', 'DESC')
                                  ->first();
                if(isset($previousBase))
                    $base += $previousBase->base;
                else
                    $base += $branch->base;
                $subtotal = 0.0;

                $begin = new \DateTime($firstDayOfMonth);
                $end = new \DateTime($lastDayOfMonth);

                for($i = $begin; $i <= $end; $i->modify('+1 day')) {
                    $date = $i->format("Y-m-d");

                    if($date > date('Y-m-d')) {
                        $dataset[$date] = 0;
                        continue;
                    } else if($date < date('Y-m-d')) {
                        $computedProfitability = Profitability::where('branch_id', $branch->id)
                                                        ->where('estimated_at', $date)
                                                        ->first();


                        if(isset($computedProfitability)) {
                            $subtotal = floatval($computedProfitability->amount);
                        } else {
                            $orders = $branch->orders()
                                             ->whereRaw("DATE(delivered_date) = '$date'")
                                             ->get();
                            $partialAmount = 0.0;
                            foreach ($orders as $order) {
                                $partialOrder = floatval($order->total)  - floatval($order->discount) - floatval($order->discount_admin) + floatval($order->iva);
                                $partialCost = $this->_getCost($order);
                                $partialAmount += ($partialOrder - $partialCost);
                            }

                            $subtotal += $partialAmount;

                            // Profitability::create([
                            //     'branch_id' => $branch->id,
                            //     'estimated_at' => $date,
                            //     'base'  => $base,
                            //     'amount' => round($subtotal, 2)
                            // ]);
                        }


                        if(isset($dataset[$date])) {
                            $dataset[$date] += $subtotal;
                        }
                        else
                            $dataset[$date] = $subtotal;
                    } else {
                        $orders = $branch->orders()
                                         ->whereRaw("DATE(delivered_date) = '$date'")
                                         ->get();

                        $partialAmount = 0.0;
                        foreach ($orders as $order) {
                            $partialOrder = floatval($order->total)  - floatval($order->discount) - floatval($order->discount_admin) + floatval($order->iva);
                            $partialCost = $this->_getCost($order);
                            $partialAmount += ($partialOrder - $partialCost);
                        }

                        $subtotal += $partialAmount;
                        if(isset($dataset[$date]))
                            $dataset[$date] += $subtotal;
                        else
                            $dataset[$date] = $subtotal;
                    }
                }
            }

            $topLimit = $base + (120000 * count($branches));
            return Response::set(true, null, compact('dataset', 'base', 'topLimit'));
            exit;
        }
        $branch = Branch::findOrFail($id);

        $firstDayOfMonth = date('Y-m-01', $dateToStart) . ' 00:00:00'; // hard-coded '01' for first day
        $lastDayOfMonth  = date('Y-m-t', $dateToStart) . ' 23:59:59';

        $dataset = [];

        $begin = new \DateTime($firstDayOfMonth);
        $end = new \DateTime($lastDayOfMonth);
        $subtotal = 0.0;

        $previousBase = DB::table('profitabilities')
                          ->where('branch_id', $branch->id)
                          ->where('estimated_at', '<=', $lastDayOfMonth)
                          ->orderBy('estimated_at', 'DESC')
                          ->first();

        if(isset($previousBase))
            $base = $previousBase->base;
        else
            $base = $branch->base;
        for($i = $begin; $i <= $end; $i->modify('+1 day')) {
            $date = $i->format("Y-m-d");

            if($date > date('Y-m-d')) {
                $dataset[$date] = 0;
                continue;
            } else if($date < date('Y-m-d')) {
                $computedProfitability = Profitability::where('branch_id', $id)
                                                ->where('estimated_at', $date)
                                                ->first();
                if(isset($computedProfitability)) {
                    $subtotal = floatval($computedProfitability->amount);
                    $dataset[$date] = round($subtotal);
                } else {
                    $orders = $branch->orders()
                                     ->whereRaw("DATE(delivered_date) = '$date'")
                                     ->get();
                    $partialAmount = 0.0;
                    foreach ($orders as $order) {
                        $partialOrder = floatval($order->total)  - floatval($order->discount) - floatval($order->discount_admin);// + floatval($order->iva);
                        $partialCost = $this->_getCost($order);
                        $partialAmount += ($partialOrder - $partialCost);
                    }

                    $subtotal += $partialAmount;
                    $dataset[$date] = round($subtotal, 2);

                    Profitability::create([
                        'branch_id' => $id,
                        'estimated_at' => $date,
                        'base'  => $base,
                        'amount' => round($subtotal, 2)
                    ]);
                    //
                    // $computedProfitability = Profitability::where('branch_id', $branch->id)
                    //                                       ->where('estimated_at', $date)
                    //                                       ->first();
                    // if(isset($computedProfitability)) {
                    //     $computedProfitability->base = $base;
                    //     $computedProfitability->amount = round($subtotal, 2);
                    //     $computedProfitability->save();
                    // }
                }
            } else {
                $orders = $branch->orders()
                                 ->whereRaw("DATE(delivered_date) = '$date'")
                                 ->get();
                $partialAmount = 0.0;
                foreach ($orders as $order) {
                    $partialOrder = floatval($order->total)  - floatval($order->discount) - floatval($order->discount_admin);// + floatval($order->iva);
                    $partialCost = $this->_getCost($order);
                    $partialAmount += ($partialOrder - $partialCost);
                }

                $subtotal += $partialAmount;
                $dataset[$date] = round($subtotal, 2);
            }

            // Profitability::create([
            //     'branch_id' => $id,
            //     'estimated_at' => $date,
            //     'base'  => $base,
            //     'amount' => round($subtotal, 2)
            // ]);
        }

        $topLimit = $base + 120000;

        foreach ($dataset as &$dt) {
            $dt = min($topLimit, $dt);
        }

        return Response::set(true, null, compact('dataset', 'base', 'topLimit'));
    }

    private function _getCost($order) {
        $phs    = ProductHasSubcategory::findOrFail($order->product_has_subcategory_id);
        /* $phsNew = ProductHasSubcategory::where('product_id', $phs->product_id)
                                       ->where('subcategory_id', $phs->subcategory_id)
                                       ->where('category_id', $phs->category_id)
                                       ->where('cost', '<>', '0')
                                       ->latest()
                                       ->first(); */
                                       

        try {
            $cost = $phs->cost;
        } catch (\Exception $e) {
            $cost = 0;
        }


        return $cost;// * (in_array($order->laboratory_id, [2, 10]) ? 1.08 : 1.16);
    }
}
