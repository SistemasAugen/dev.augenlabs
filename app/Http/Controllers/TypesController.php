<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Type;
use App\Client;
use app\Http\Requests\TypesRequest;
use Illuminate\Support\Facades\Auth;

class TypesController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $id = Auth::id();
        
        if($request->has('client_id')) {
            $clientId = $request->input('client_id');
            $client = Client::findOrFail($clientId);

            if($client->price_list_id !== 0) {
                $priceListId = $client->price_list_id;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://apiv2.augenlabs.com/v1/lists/' . $priceListId); 
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
                curl_setopt($ch, CURLOPT_HEADER, 0); 
                $data = curl_exec($ch); 
                curl_close($ch); 

                $priceList = json_decode($data);

                $types = $priceList->designs;
                foreach($types as &$type) {
                    $type->isNew  = true;
                }
            } else {
                $types = Type::all();
            }
        } else {
            if($id == 106) {
                $types = Type::whereIn('id', [1,2,3])->get();
            } else {
                $types = Type::all();
            }
            foreach ($types as $key => $value) {
                $value->categories;
            }
        }

        return response()->json($types);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $type=new Type(array(
            'name'=>$request->name,
            'description'=>$request->description
        ));

        $type->save();

        return response()->json($type);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $type=Type::find($id);

        return response()->json($type);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $type=Type::find($id);
        $type->categories();

        return response()->json($type);
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
        $type=Type::find($id);
        
        $type->name=$request->name;
        $type->description=$request->description;
        
        $type->save();

        return response()->json($type);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $type=Type::find($id);

        if ($type->delete()) {
            return response()->json(['msg'=>'Tipo con el ID '.$id.' eliminado correctamente.']);
        }else{
            return response()->json(['msg'=>'Ocurrio un error al eliminar.'],500);
        }
        
        return false;
    }
}
