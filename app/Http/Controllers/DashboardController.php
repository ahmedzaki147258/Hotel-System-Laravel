<?php

namespace App\Http\Controllers;

use App\Exports\ClientsExport;
use App\Models\Client;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function export()
    {
        return Excel::download(new ClientsExport, 'clients.xlsx');
    }

    public function statistics(Request $request)
    {
        $year = $request->input('year', now()->year);

        // Total male and female clients
        $totalMaleClients = Client::whereYear('created_at', $year)->where('gender', 'male')->count();
        $totalFemaleClients = Client::whereYear('created_at', $year)->where('gender', 'female')->count();

        // Get monthly revenue
        $monthlyRevenue = Reservation::whereYear('created_at', $year)
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(paid_price_in_cents) as total')
            )
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month');

        $revenueByMonth = [];
        for ($i = 1; $i <= 12; $i++) {
            $revenueByMonth[] = round($monthlyRevenue->get($i, 0), 2);
        }

        // Reservations by country
        $reservationsByCountry = Reservation::select(DB::raw('lct.name as country_name'), DB::raw('COUNT(*) as total'))
            ->whereYear('reservations.created_at', $year)
            ->join('clients', 'clients.id', '=', 'reservations.client_id')
            ->join('lc_countries as lc', 'lc.id', '=', 'clients.country_id')
            ->join('lc_countries_translations as lct', 'lct.lc_country_id', '=', 'lc.id')
            ->groupBy('lct.name')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) {
                return [
                    'country' => $item->country_name,
                    'reservations' => $item->total,
                ];
            });

        // Get top clients
        $topClients = Reservation::select('client_id', DB::raw('COUNT(*) as reservations_count'))
            ->whereYear('created_at', $year)
            ->groupBy('client_id')
            ->orderByDesc('reservations_count')
            ->with('client:id,name')
            ->limit(10)
            ->get()
            ->map(function ($reservation) {
                return [
                    'name' => $reservation->client->name,
                    'reservations' => $reservation->reservations_count,
                ];
            });

        return response()->json([
            'totalClients' => [
                'totalMales' => $totalMaleClients,
                'totalFemales' => $totalFemaleClients,
                'total' => $totalMaleClients + $totalFemaleClients,
            ],
            'monthlyRevenue' => $revenueByMonth,
            'reservationsByCountry' => $reservationsByCountry,
            'topClients' => $topClients,
        ]);
    }
}
