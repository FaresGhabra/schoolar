<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Models\SchoolCourses\CourseInvoice;
use App\Models\Invoices\ServiceInvoice;
use App\Models\SchoolServices\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class InvoiceController extends Controller
{
    // function to read all Services Invoices.. Admin Role
    public function InvServiceA(Request $request)
    {
        if ($request->has('start_date') && $request->has('end_date')) {
            $from = date($request->start_date);
            $to = date($request->end_date);
            sendJson(ServiceInvoice::whereBetween('created_at', [$from, $to])->get());
        } else {
            sendJson(ServiceInvoice::all());
        }
    }


    //function to read courses Invoices.. Admin Role
    public function InvCourseA(Request $request)
    {
        if ($request->has('start_date') && $request->has('end_date')) {
            $from = date($request->start_date);
            $to = date($request->end_date);
            sendJson(CourseInvoice::whereBetween('created_at', [$from, $to])->get());
        } else {
            sendJson(CourseInvoice::all());
        }
    }


    // function to read Services Invoices .. Parent Role
    public function InvServiceP(Request $request)
    {
        if ($request->has('start_date') && $request->has('end_date')) {
            $from = date($request->start_date);
            $to = date($request->end_date);
            sendJson(ServiceInvoice::join('users_services', 'users_services.id', '=', 'services_invoices.users_services_id')
                ->where('users_services.user_id', Auth::id())
                ->whereBetween('services_invoices.created_at', [$from, $to])
                ->select('users_services.service_id', 'services_invoices.amount', 'services_invoices.paid_online', 'services_invoices.created_at')
                ->get());
        } else {
            sendJson(ServiceInvoice::join('users_services', 'users_services.id', '=', 'services_invoices.users_services_id')
                ->where('users_services.user_id', Auth::id())
                ->select('users_services.service_id', 'services_invoices.amount', 'services_invoices.paid_online', 'services_invoices.created_at')
                ->get());
        }
    }

    //function to read Courses Invoices.. Parent Role
    public function InvCourseP(Request $request)
    {
        if ($request->has('start_date') && $request->has('end_date')) {
            $from = date($request->start_date);
            $to = date($request->end_date);
            sendJson(CourseInvoice::where('user_id', Auth::id())->whereBetween('created_at', [$from, $to])->get());
        } else {
            sendJson(CourseInvoice::where('user_id', Auth::id())->get());
        }
    }


    public function payService(Request $request)
    {
        if (Auth::user()->role_id == RoleEnum::ADMIN) {
            $userService = UserService::where('user_id', $request->user_id)->where('service_id', $request->service_id)->first();
        } else {
            $userService = UserService::where('user_id', Auth::user()->id)->where('service_id', $request->service_id)->first();
        }
        if ($userService) {
            $userService->total_paid_amount += $request->amount;
            $userService->save();

        } else {
            $userService = new UserService();
            if (Auth::user()->role_id == RoleEnum::ADMIN) {
                $userService->user_id = $request->user_id;
            } else {
                $userService->user_id = Auth::user()->id;
            }
            $userService->service_id = $request->service_id;
            $userService->total_paid_amount = $request->amount;
            $userService->status = 'activated';
            $userService->save();
        }

        $serviceInvoice = new ServiceInvoice();
        $serviceInvoice->users_services_id = $userService->id;
        $serviceInvoice->amount = $request->amount;
        if (Auth::user()->role_id == RoleEnum::ADMIN) {
            $serviceInvoice->paid_online = 0;
        } else {
            $serviceInvoice->paid_online = 1;
        }
        $serviceInvoice->save();

        sendJson([
            'user_service' => $userService,
            'service_invoice' => $serviceInvoice,
        ]);
    }
}