<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BloodGroup;
use Illuminate\Http\Request;

class BloodGroupController extends Controller
{
    public function index(){
        $blood_groups = BloodGroup::all();

        return response([
            'status' => 'done',
            'blood_groups' => $blood_groups
        ]);
    }
}
