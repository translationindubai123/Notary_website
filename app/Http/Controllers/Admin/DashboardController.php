<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactInquiry;
use App\Models\Service;
use App\Models\ServiceCategory;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'categoryCount' => ServiceCategory::count(),
            'serviceCount' => Service::count(),
            'publishedCount' => Service::published()->count(),
            'inquiryCount' => ContactInquiry::count(),
        ]);
    }
}
