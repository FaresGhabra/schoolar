<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class MessageController extends Controller
{
    public function postMessage(Request $request)
    {
        $request->validate([
            'content' => 'string',
           // 'type' => Rule::in(['','']),
        ]);

        $message = new Message();
        $message->user_id = $request->user()->id;
        if($request->has('class_id')){
            $message->class_id = $request->class_id;
        }
        else if($request->has('section_id')){
            $message->section_id = $request->section_id;
        }
        elseif ($request->has('parent_id')) {
            $message->parent_id = $request->parent_id;
        }
        else{
            $message->all = 1;
        }
        $message->content = $request->content;
        $message->type = $request->type;
        $message->save();

        sendJson($message);

    }

    public function getMessages()
    {

        $messages = Message::where('all',1)->orWhere('parent_id',Auth::user()->student_parent->id);

        foreach (Auth::user()->student_parent->students as $student){
            foreach($student->student_records as $record)
            {
                dd($record->section);
                $messages = $messages->orWhere('section_id',$record->section_id)
                                     ->orWhere('class_id',$record->section->school_class->id);
            }
        }

        sendJson($messages);
    }
}
