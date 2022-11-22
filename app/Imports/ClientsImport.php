<?php

namespace App\Imports;

use App\Client;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;

class ClientsImport implements ToModel {
    public static $updatedClients = 0;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row) {
        if(true) {
            // try {
                $coords = explode(',', $row[11]);
				if(count($coords) > 1) {
					$row[11] = $coords[0];
					$row[12] = $coords[1];

					$row = array_map(function($c) {
                        return "'" . str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $c) . "'";
					}, $row);
					$fieldsValues = 'null, ' . implode(',', array_values($row));

					$response = sprintf("INSERT INTO re VALUES (%s)", $fieldsValues);
					if(!DB::insert($response)) {
                        var_dump($response);
                    }
                    self::$updatedClients++;
                    return null;
                }
            // } catch(\Exception $e) {
            //     var_dump($response);
            //     die;
            //     return null;
            // }
        }
        return null;

        if($row[0] == '#') return null;
        $client = Client::find($row[0]);
        if($client) {
            try {
                $client->celphone = (string) $row[4];
                $client->notification_mail = (string) $row[5];
                $client->contact_name = (string) $row[6];
                $client->contact_phone = (string) $row[7];
                $client->facturacion->email = (string) $row[8];
                $client->push();
                self::$updatedClients++;
                return $client;
            } catch(\Exception $e) {
                return null;
            }
        }
    }
}
