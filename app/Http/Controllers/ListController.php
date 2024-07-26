<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Branch;
use App\Client;
use App\Group;

class ListController extends Controller {
    public function getListAssignment($list_id) {
        $assignments = DB::table('lists_models')
                         ->where('list_id', $list_id)
                         ->get();

        $assignmentsDetails = $assignments->map(function ($assignment) {
            return $this->getAssignmentDetails($assignment);
        });

        return response()->json($assignmentsDetails);
    }

    private function getAssignmentDetails($assignment) {
         switch ($assignment->model_type) {
            case \App\Client::class:
                $model = DB::table('clients')->find($assignment->model_id);
                return [
                    'type' => 'Client',
                    'model' => $model,
                ];
            case \App\Branch::class:
                $model = DB::table('branches')->find($assignment->model_id);
                return [
                    'type' => 'Branch',
                    'model' => $model,
                ];
            case \App\Group::class:
                $model = DB::table('groups')->find($assignment->model_id);
                return [
                    'type' => 'Group',
                    'model' => $model,
                ];
            default:
                return null;
        }
    }
}
