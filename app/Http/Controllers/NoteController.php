<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Note;
use App\NoteRx;
use App\Client;
use App\Order;
use App\Mail\SendEmailNote;

use Mail;
use PDF;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rows=Note::all();
        foreach ($rows as $key => $value) {
            $rxs = NoteRx::where('notes_id',$value->id)->get();
            foreach ($rxs as $keyrx => $valuexr) {
                $order = Order::find($valuexr->orders_id);
                if ($order) {
                    $valuexr->rx = $order->rx;   
                }
                $valuexr->amount = '$ '.number_format($valuexr->amount,2);
            }
            $value->rxs = $rxs;
            $value->rxsbtn = '<button class="btn btn-info">'.sizeof($rxs).'</button>';
            $value->total = '$ '.number_format($value->total,2);
            $client = Client::find($value->clients_id);
            if ($client) {
                $value->client = $client->name;
            } 
            $value->editbtn = '<button class="btn btn-sss"><i class="fas fa-edit"></i> Editar</button>';
            $value->printbtn = '<button class="btn btn-sss"><i class="fas fa-print"></i> Imprimir</button>';
        }

        return response()->json($rows);
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
        $note = $request;
        /*$row = new Note();
        $row->account_status = $request->account_status;
        $row->wekeend = $request->wekeend;
        $row->clients_id = $request->clients_id;
        $row->observations = $request->observations;
        $row->total = $request->total;
        $row->save();
        if (isset($request->rxs)) {
            if (sizeof($request->rxs) > 0) {
                foreach ($request->rxs as $key => $value) {
                    $noterx = new NoteRx();
                    $noterx->date = $value['date'];
                    $noterx->orders_id = $value['orders_id'];
                    $noterx->description = $value['description'];
                    $noterx->amount = $value['amount'];
                    $noterx->notes_id = $row->id;
                    $noterx->save();
                }
            } 
        }*/
        $now = date('d-m-Y-H-i-s');
        $pdf = PDF::loadView('plantillas.printnote',['inputs' => $note])->setPaper('A5');
        $content = $pdf->download()->getOriginalContent();
        Storage::disk('public')->put('docs/note-'.$now.'.pdf',$content);
        $url = 'https://dev.augenlabs.com/storage/app/public/docs/note-'.$now.'.pdf';
        
        return response()->json($url);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $row = Note::find($id);
        $row->rxs = NoteRx::where('notes_id',$id)->get();
        $client_name = Client::find($row->clients_id);
        if ($client_name) {
            $row->client_name = $client_name->name;
        }
        return response()->json($row);
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
        $row=Note::find($id);
        $row->account_status = $request->account_status;
        $row->wekeend = $request->wekeend;
        $row->clients_id = $request->clients_id;
        $row->observations = $request->observations;
        $row->total = $request->total;
        $row->save();

        if (isset($request->rxs)) {
            if (sizeof($request->rxs) > 0) {
                NoteRx::where('notes_id',$row->id)->delete();
                foreach ($request->rxs as $key => $value) {
                    $noterx = new NoteRx();
                    $noterx->date = $value['date'];
                    $noterx->orders_id = $value['orders_id'];
                    $noterx->description = $value['description'];
                    $noterx->amount = $value['amount'];
                    $noterx->notes_id = $row->id;
                    $noterx->save();
                }
            }
            else{
                NoteRx::where('notes_id',$row->id)->delete();
            }
        }
        else{
            NoteRx::where('notes_id',$row->id)->delete();
        }

       

        return response()->json($row);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $row=Note::find($id);
        NoteRx::where('notes_id',$id)->delete();
        if($row->delete()){
            return response()->json(['msg'=>'Categoria con el ID '.$id.' eliminada correctamente.']);
        }else{
            return response()->json(['msg'=>'Ocurrio un error al eliminar.'],500);
        }
        
        return false;
    }

    public function destroyMultiple(Request $request)
    {
        foreach ($request->ids as $key => $value) {
            $status=$this->_deleteRow($value);
            if(!$status)
                break;
        }

        if ($status) {
            return response()->json(['msg'=>'Registros eliminados.']);
        }
        else{
            return response()->json(['msg'=>'Ocurrio un error al eliminar.'],500);
        }
    }

    private function _deleteRow($id)
    {
        $row=Note::find($id);
        NoteRx::where('notes_id',$id)->delete();
        if ($row->delete()) {
            return true;
        }
        else{
            return false;
        }
    }
    public function print(Request $request)
    {
        $data = [];
        foreach ($request->ids as $key => $value) {
            
            $note = Note::find($value);
            $rxs = NoteRx::where('notes_id',$value)->get();
            $client = Client::find($note->clients_id);
            if ($client) {
                $note->client = $client->name;
            } 
            $note->total = 0;
            foreach ($rxs as $keyrx => $valuerx) {
                $note->total += $valuerx->amount;
                $valuerx->amount = '$ '.number_format($valuerx->amount,2);
                $order = Order::find($valuerx->orders_id);
                if ($order) {
                    $valuerx->rx = $order->rx;   
                }
            }
            $note->rxs = $rxs;
            $note->total = number_format($note->total,2);
            ini_set('memory_limit', '-1');

            $pdf = PDF::loadView('plantillas.printnote',['inputs' => $note])->setPaper('A5');
            $content = $pdf->download()->getOriginalContent();
            Storage::disk('public')->put('docs/note-'.$note->id.'.pdf',$content);
            array_push($data,'https://dev.augenlabs.com/storage/app/public/docs/note-'.$note->id.'.pdf');
        }
        return $data;
    }
    public function sendEmail(Request $request)
    {
        /*$data = [];
        foreach ($request->ids as $key => $value) {
            
            $note = Note::find($value);
            $rxs = NoteRx::where('notes_id',$value)->get();
            $client = Client::find($note->clients_id);
            if ($client) {
                $note->client = $client->name;
            } 
            $note->total = 0;
            foreach ($rxs as $keyrx => $valuerx) {
                $note->total += $valuerx->amount;
                $valuerx->amount = '$ '.number_format($valuerx->amount,2);
                $order = Order::find($valuerx->orders_id);
                if ($order) {
                    $valuerx->rx = $order->rx;   
                }
            }
            $note->rxs = $rxs;
            $note->total = number_format($note->total,2);
            ini_set('memory_limit', '-1');

            $pdf = PDF::loadView('plantillas.printnote',['inputs' => $note])->setPaper('A5');
            $content = $pdf->download()->getOriginalContent();
            Storage::disk('public')->put('docs/note-'.$note->id.'.pdf',$content);
            array_push($data,'https://dev.augenlabs.com/storage/app/public/docs/note-'.$note->id.'.pdf');
        }
        $emailsdata = explode(',',$request->emails);
        
        foreach ($emailsdata as $key => $value) {
           
            Mail::to($value)->send(new SendEmailNote( $data));
            
        }       
        Mail::to('sistemas@augenlabs.com')->send(new SendEmailNote( $data));
        return ' ok';*/

        $note = $request;
        $data = [];
        $now = date('d-m-Y-H-i-s');
        $pdf = PDF::loadView('plantillas.printnote',['inputs' => $note])->setPaper('A5');
        $content = $pdf->download()->getOriginalContent();
        Storage::disk('public')->put('docs/note-'.$now.'.pdf',$content);
        $url = 'https://dev.augenlabs.com/storage/app/public/docs/note-'.$now.'.pdf';

        array_push($data,$url);

        $emailsdata = explode(',',$request->emails);
        
        foreach ($emailsdata as $key => $value) {
           
            Mail::to($value)->send(new SendEmailNote( $data));
            
        }       
        //Mail::to('sistemas@augenlabs.com')->send(new SendEmailNote( $data));
        return ' ok';
        
    }   
}
