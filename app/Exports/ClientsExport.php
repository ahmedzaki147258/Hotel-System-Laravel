<?php

namespace App\Exports;

use App\Http\Resources\ClientExcelResource;
use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClientsExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $clients = Client::get();
        return ClientExcelResource::collection($clients);
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function headings(): array
    {
        return ["ID", "Name", "Email", "Country", "Gender", "Mobile", "Approved By"];
    }
}
