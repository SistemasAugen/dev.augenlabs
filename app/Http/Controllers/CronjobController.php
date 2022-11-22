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

                if($client->state_id == 14) { // JUST GDL
                    if($mailCounter == 0) {
                        // TESTING TO SEE
                        Mail::send('emails.all_ok', compact('orders'), function ($m) use ($client) {
                            $m->from('contacto@augenlabs.com', 'Augen Labs');
                            $m->to('sistemas@augenlabs.com')->subject('Recibimos tus recetas y ¡TODO OK!');
                            // $m->to('sleal@augenlabs.com')->subject('Recibimos tus recetas y ¡TODO OK!');
                        });
                    }
                    Mail::send('emails.all_ok', compact('orders'), function ($m) use ($client) {
                        $m->from('contacto@augenlabs.com', 'Augen Labs');
                        $emailsToSend = explode(';', $client->notification_mail);

                        foreach ($emailsToSend as $email) {
                            $m->to($email)->subject('Recibimos tus recetas y ¡TODO OK!');
                        }
                    });

                    $mailCounter++;
                }
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

                if($client->state_id == 14) { // JUST GDL
                    if($mailCounter == 0) {
                        // TESTING TO SEE
                        Mail::send('emails.order_ready', compact('orders'), function ($m) use ($client) {
                            $m->from('contacto@augenlabs.com', 'Augen Labs');
                            $m->to('sistemas@augenlabs.com')->subject('Tus recetas ¡ESTÁN LISTAS!');
                        });
                    }
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
                }
            }
        }

        return Response::set(true, sprintf('( %s ) Mails sent', $mailCounter));
    }

    public function warrantyReport() {
        $filename   = "GARANTIAS SEMANA " . date('W') . " " . date('Y') . ".xlsx";
        $attachment = Excel::raw(new WarrantyExport, BaseExcel::XLSX);
        $title      = "GARANTIAS SEMANA " . date('W') . " " . date('Y');

        Mail::send('emails.warranty_report', compact('title'), function($m) use ($filename, $attachment) {
            $m->from('contacto@augenlabs.com', 'Augen Labs');

            $m->to('sleal@augenlabs.com')->subject('Reporte de garantías');
            $m->to('operaciones@augenlabs.com')->subject('Reporte de garantías');
            $m->to('sistemas@augenlabs.com')->subject('Reporte de garantías');
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
