<?php

namespace App\Http\Controllers;

use App\Models\SchoolInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;


class SchoolInfoController extends Controller
{
    public function index()
    {
        return SchoolInfo::latest()->first();
    }

    public function edit(Request $request)
    {
        $schoolInfo = SchoolInfo::latest()->first();
        $validData = $request->validate([
            'name' => 'required|string',
            'beginning_time' => 'required|integer',
            'address' => 'required|string',
            'description' => 'required|string',
            'logo_file' => 'required|image'
        ]);

        $path = $request->logo_file->store('school_logo', 'public');
        $validData['logo'] = $path;
        if ($schoolInfo) {
            $schoolInfo->update($validData);
        } else {
            $schoolInfo = SchoolInfo::create($validData);
        }
        sendJson($schoolInfo);
    }

    public function udpateImages(Request $request) {
        $schoolInfo = SchoolInfo::latest()->first();

        $validData = $request->validate([
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif',
        ]);
        if ($schoolInfo->photos) {
            foreach ($schoolInfo->photos as $photo) {
                if (file_exists(storage_path('app/school_logo/'.$photo)))
                    unlink(storage_path('app/school_logo/'.$photo));
            }
        }
        $arr = [];
        foreach ($validData['images'] as $image) {
            $path = $image->store('photos', 'public');
            array_push($arr, $path);
        }
        $schoolInfo->photos = json_encode($arr);

        $schoolInfo->save();
        sendJson($schoolInfo);
    }
}
