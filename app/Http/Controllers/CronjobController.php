<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as BaseExcel;
use Illuminate\Http\Request;
use App\Exports\WarrantyExport;
use App\Exports\PaymentExport;
use App\Helpers\Response;
use App\Order;
use App\Client;
use Mail;

class CronjobController extends Controller {
    public function allOk() {
        $mailCounter = 0;

        $ordersReceivedToday = Order::whereRaw('DATE(created_at) = "' . date('Y-m-d') .'"' )
                                    ->get();
        $clientsAlreadySent = [];


        foreach ($ordersReceivedToday as $order) {
            $client = $order->client;
            if(!in_array($client->id, $clientsAlreadySent)) {
                $clientsAlreadySent[] = $client->id;
                $orders = $client->orders()->whereRaw('DATE(created_at) = "' . date('Y-m-d') .'"')->get();

                foreach ($orders as &$nOrder) {
                    // $nOrder->client;
                    $nOrder->productHas;
                    $nOrder->extras;
                }

                // if($client->state_id == 14) { // JUST GDL
                    // if($mailCounter == 0) {
                    //     // TESTING TO SEE
                    //     // return view('emails.all_ok', compact('orders'));die;
                    //     Mail::send('emails.all_ok', compact('orders'), function ($m) use ($client) {
                    //         $m->from('contacto@augenlabs.com', 'Augen Labs');
                            
                    //         $m->to('edgar.desarrollo@gmail.com')->subject('Recibimos tus recetas y ¡TODO OK!');
                    //         // $m->to('sistemas@augenlabs.com')->subject('Recibimos tus recetas y ¡TODO OK!');
                            
                    //         $m->getHeaders()->addTextHeader('Content-Type', 'text/html; charset=UTF-8');
                    //     });
                    //     echo 'Mail sent';die;
                    // }
                    Mail::send('emails.all_ok', compact('orders'), function ($m) use ($client) {
                        $m->from('contacto@augenlabs.com', 'Augen Labs');
                        $emailsToSend = explode(';', $client->notification_mail);

                        foreach ($emailsToSend as $email) {
                            $m->to($email)->subject('Recibimos tus recetas y ¡TODO OK!');
                        }
                    });

                    $mailCounter++;
                // }
            }
        }

        return Response::set(true, sprintf('( %s ) Mails sent', $mailCounter));
    }

    public function orderReady() {
        $mailCounter = 0;

        $ordersFinishedToday = Order::whereRaw('DATE(finish_date) = "' . date('Y-m-d') .'"' )
                                    ->get();
        $clientsAlreadySent = [];

        foreach ($ordersFinishedToday as $order) {
            $client = $order->client;
            if(!in_array($client->id, $clientsAlreadySent)) {
                $clientsAlreadySent[] = $client->id;
                $orders = $client->orders()->whereRaw('DATE(finish_date) = "' . date('Y-m-d') .'"')->get();

                foreach ($orders as &$nOrder) {
                    // $nOrder->client;
                    $nOrder->productHas;
                    $nOrder->extras;
                }

                // if($client->state_id == 14) { // JUST GDL
                    // if($mailCounter == 0) {
                    //     // TESTING TO SEE
                    //     // return view('emails.order_ready', compact('orders'));
                    //     Mail::send('emails.order_ready', compact('orders'), function ($m) use ($client) {
                    //         $m->from('contacto@augenlabs.com', 'Augen Labs');
                            
                    //         $m->to('edgar.desarrollo@gmail.com')->subject('Recibimos tus recetas y ¡TODO OK!');
                    //         //$m->to('sistemas@augenlabs.com')->subject('Tus recetas ¡ESTÁN LISTAS!');
                    //         $m->getHeaders()->addTextHeader('Content-Type', 'text/html; charset=UTF-8');
                    //     });
                    //      echo 'Mail sent';die;
                    // }
                    try {
                            Mail::send('emails.order_ready', compact('orders'), function ($m) use ($client) {
                            $m->from('contacto@augenlabs.com', 'Augen Labs');
                            $emailsToSend = explode(';', $client->notification_mail);
    
                            foreach ($emailsToSend as $email) {
                                $m->to($email)->subject('Tus recetas ¡ESTÁN LISTAS!');
                            }
                        });
                    } catch(\Exception $e) {}
                    $mailCounter++;
                // }
            }
        }

        return Response::set(true, sprintf('( %s ) Mails sent', $mailCounter));
    }

    public function warrantyReport() {
          // Check if today is Monday
        if (date('N') == 1) {
            $filename = "GARANTIAS SEMANA " . date('W') . " " . date('Y') . ".xlsx";
            $title = "GARANTIAS SEMANA " . date('W') . " " . date('Y');
        } else {
            // For days other than Monday, use the date of the day before
            $yesterday = date('dmY', strtotime('-1 day'));
            $filename = "GARANTIAS " . $yesterday . ".xlsx";
            $title = "GARANTIAS " . $yesterday;
        }

        $attachment = Excel::raw(new WarrantyExport, BaseExcel::XLSX);
        
        Mail::send('emails.warranty_report', compact('title'), function($m) use ($filename, $attachment) {
            $m->from('contacto@augenlabs.com', 'Augen Labs');

            $m->to('sleal@augenlabs.com')->subject('Reporte de garantías');
            $m->to('operaciones@augenlabs.com')->subject('Reporte de garantías');
            $m->to('sistemas@augenlabs.com')->subject('Reporte de garantías');
            
            $m->to('procesos@augenlabs.com')->subject('Reporte de garantías');
            
            $m->attachData($attachment, $filename);
        });

        return Response::set(true, 'Mail sent');
    }

    public function profitabilityCalculate() {
        $branchId = 4;

        $banch = Branch::findOrFail($branchId);
    }

    public function paymentReport() {
        $year   = '2022';
        $month  = '03';
        // $attachment = Excel::raw(new PaymentExport($year, $month), BaseExcel::XLSX);

        return Excel::download(new PaymentExport($year, $month), 'PagadoMar2022_v2.xlsx');
    }
}
