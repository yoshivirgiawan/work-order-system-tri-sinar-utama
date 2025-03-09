<?php

namespace App\Exports;

use App\Models\WorkOrder;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class OperatorReportExport implements FromCollection, WithHeadings
{
  /**
   * @return \Illuminate\Support\Collection
   */
  public function collection()
  {
    $workOrderQuery = WorkOrder::selectRaw("
        users.name AS operator,
        product_name,
        SUM(CASE WHEN status = 'Completed' THEN quantity ELSE 0 END) AS total_completed
    ")->join('users', 'work_orders.operator', '=', 'users.id')->groupBy('operator', 'product_name');

    if (Auth::user()->role == 'operator') {
      $workOrderQuery->where('operator', Auth::user()->id);
    }

    return $workOrderQuery->get();
  }

  public function headings(): array
  {
    return ['Operator', 'Nama Barang', 'Total Completed'];
  }
}
