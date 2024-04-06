<?php

namespace App\Http\Controllers;

use App\Models\BusSupervisor;
use App\Models\SchoolAccounts\Student;
use Illuminate\Http\Request;
use App\Services\TelegramService;

class TelegramController extends Controller
{

    // CRUD for bus supervisor
    public function store(Request $request)
    {
        $supervisor = new BusSupervisor();
        $supervisor->full_name = $request->full_name;
        $supervisor->phone_number = $request->phone_number;
        $random_code = random_int(10000000,99999999);
        while(!empty(BusSupervisor::where('code',$random_code)->get()->toArray()))
        {
            $random_code = random_int(10000000,99999999);
        }
        $supervisor->code = $random_code;
        $supervisor->gender = $request->gender;
        $supervisor->bus_number = $request->bus_number;
        $supervisor->save();

        sendJson($supervisor);
    }
    public function index()
    {
        $busSupervisors = BusSupervisor::all();

        return response()->json(['data' => $busSupervisors]);
    }

    public function show($id)
    {
        $busSupervisor = BusSupervisor::findOrFail($id);

        return response()->json(['data' => $busSupervisor]);
    }

    public function update(Request $request, $id)
    {
        $busSupervisor = BusSupervisor::findOrFail($id);
        $busSupervisor->fill($request->only(['full_name', 'gender', 'phone_number', 'bus_number']));
        $busSupervisor->save();

        return response()->json(['data' => $busSupervisor]);
    }

    public function destroy($id)
    {
        BusSupervisor::destroy($id);

        return response()->json(['message' => 'Bus Supervisor deleted successfully']);
    }

    public function set_chat_id(Request $request)
    {
        $supervisor = BusSupervisor::where('phone_number',$request->phone_number)->get()->toArray();
        if($supervisor == null){
            return response()->json(['message'=>'supervisor not found']);
        }

       // dd($supervisor);
        $apiUrl = "https://api.telegram.org/bot5999727844:AAHTu1WCI3JxFvdkHr4rUjxiBenqNkAO8JQ/getUpdates";
        $response = file_get_contents($apiUrl);
        $updates = json_decode($response, true);
        if ($updates['ok']) {
            $result = $updates['result'];

            foreach($result as $update){
                if($update['message']['text'] == $supervisor[0]['code']){
                  //  $supervisor[0]['chat_id'] = $update['message']['chat']['id'];
                    $super = BusSupervisor::find($supervisor[0]['id']);
                    $super->chat_id = $update['message']['chat']['id'];
                    $super->save();

                    $succes_message = new TelegramService();
                    $succes_message -> sendMessage($super->chat_id,"Thank you ".$super->full_name);
                    
                    sendMessageJson("Succes");
                }
            }
            sendMessageJson("Error.. No message match the code");
        }
    }

    public function send_absence_message(Request $request)
    {
        //$chatId = 912005272;
        $student = Student::find($request->student_id);

        $pre_name = 'Mr.';
        if($student->bus_supervisor->gender == 'F')
        {$pre_name ='Ms.' ;}

        $messageText = 'Hello'.$pre_name.$student->bus_supervisor->full_name.', my son'.$student->user->fullname."won't come tomorrow. thank you" ;

        $telegram = new TelegramService();
        $telegram->sendMessage($student->bus_supervisor->chat_id, $messageText);
    }
}
