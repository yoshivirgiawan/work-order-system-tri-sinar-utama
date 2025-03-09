<?php

namespace App\Exports;

use App\Models\WorkOrder;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class WorkOrderReportExport implements FromCollection, WithHeadings
{
  /**
   * @return \Illuminate\Support\Collection
   */
  public function collection()
  {
    $workOrderQuery = WorkOrder::selectRaw("
        product_name,
        SUM(CASE WHEN status = 'pending' THEN quantity ELSE 0 END) AS total_pending,
        SUM(CASE WHEN status = 'in_progress' THEN quantity ELSE 0 END) AS total_in_progress,
        SUM(CASE WHEN status = 'completed' THEN quantity ELSE 0 END) AS total_completed,
        SUM(CASE WHEN status = 'canceled' THEN quantity ELSE 0 END) AS total_canceled
    ")->groupBy('product_name');

    if (Auth::user()->role == 'operator') {
      $workOrderQuery->where('operator', Auth::user()->id);
    }

    return $workOrderQuery->get();
  }

  public function headings(): array
  {
    return ['Nama Barang', 'Total Pending', 'Total In Progress', 'Total Completed', 'Total Canceled'];
  }
}
