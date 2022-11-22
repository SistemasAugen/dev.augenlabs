<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Branch;
// use App\Town;
// use App\State;
// use App\Laboratory;
use App\Http\Requests\BranchRequest;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $branches=Branch::all();
        
        foreach ($branches as $key => $value) {
            $value->town();
            $value->state();
            $value->laboratory();
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
            'address'=>$request->address
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
        $branch=Branch::find($id);
        $branch->state();
        $branch->town();
        $branch->laboratory();

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
        $branch->address=$request->addres;

        $branch->save();
        
        		
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
}
