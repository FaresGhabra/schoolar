<?php

namespace App\Http\Controllers\SchoolServices;

use App\Http\Controllers\Controller;
use App\Models\SchoolAccounts\User;
use App\Models\SchoolServices\Service;
use Illuminate\Http\Request;

class ServiceAdminController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $services = Service::searchable($request)->paginate($perPage);
        sendJson($services);
    }

    public function show($id)
    {
        $service = Service::find($id);
        if (!$service)
            sendMessageJson("Service not found", 404);
        $service->load('users');
        sendJson($service);
    }

    public function viewUserServices($user_id)
    {
        $user = User::find($user_id);
        if (!$user)
            sendMessageJson("User not found", 404);
        sendJson($user->services);
    }

    public function store(Request $request)
    {
        $validData = $request->validate([
            'name' => "required|string",
            'description' => "required|string",
            'price' => 'required|numeric'
        ]);
        $service = Service::create($validData);
        sendJson($service);
    }

    public function updateImages(Request $request, $id)
    {
        $service = Service::find($id);
        if (!$service)
            sendMessageJson("Service not found", 404);
        $validData = $request->validate([
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif',
        ]);
        if ($service->photos) {
            foreach ($service->photos as $photo) {
                if (file_exists(storage_path('app/public/'.$photo)))
                    unlink(storage_path('app/public/'.$photo));
            }
        }
        $arr = [];
        foreach ($validData['images'] as $image) {
            $path = $image->store('photos', 'public');
            array_push($arr, $path);
        }
        $service->photos = json_encode($arr);

        $service->save();
        sendJson($service);
    }

    public function update(Request $request, $id)
    {
        $service = Service::find($id);
        if (!$service)
            sendMessageJson("Service not found", 404);
        $validData = $request->validate([
            'name' => 'string',
            'description' => 'string',
            'photos' => 'json',
            'price' => 'numeric'
        ]);
        $service->update($validData);
        $service->save();
        sendJson($service);
    }

    public function delete($id)
    {
        $service = Service::find($id);
        if (!$service)
            sendMessageJson("Service not found", 404);
        $service->delete();
        sendMessageJson("Service had been deleted successfly");
    }



}