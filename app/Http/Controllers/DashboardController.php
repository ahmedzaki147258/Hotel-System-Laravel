<?php

namespace App\Http\Controllers;

use App\Exports\ClientsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function export()
    {
        return Excel::download(new ClientsExport, 'clients.xlsx');
    }
}
