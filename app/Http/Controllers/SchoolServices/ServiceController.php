<?php

namespace App\Http\Controllers\SchoolServices;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Models\SchoolServices\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $services = Service::searchable($request)->get();
        sendJson($services);
    }

    public function show($id)
    {
        $service = Service::find($id);
        if (!$service)
            sendMessageJson("Service not found", 404);
        sendJson($service);
    }

    public function viewMySerivces(Request $request)
    {
        $user = $request->user();
        sendJson($user->services);
    }
}