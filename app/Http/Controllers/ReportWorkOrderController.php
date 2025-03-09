<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\WorkOrderReportExport;

class ReportWorkOrderController extends Controller
{
  public function index()
  {
    return view('content.report.work-orders');
  }

  public function data(Request $request)
  {
    $columns = [
      1 => 'product_name',
      2 => 'total_pending',
      3 => 'total_in_progress',
      4 => 'total_completed',
      5 => 'total_canceled',
    ];

    $search = $request->input('search.value');
    $limit = $request->input('length');
    $start = $request->input('start');
    $order = $columns[$request->input('order.0.column')] ?? 'product_name';
    $dir = $request->input('order.0.dir') ?? 'asc';

    $workOrderQuery = WorkOrder::selectRaw("
            product_name,
            SUM(CASE WHEN status = 'pending' THEN quantity ELSE 0 END) AS total_pending,
            SUM(CASE WHEN status = 'in_progress' THEN quantity ELSE 0 END) AS total_in_progress,
            SUM(CASE WHEN status = 'completed' THEN quantity ELSE 0 END) AS total_completed,
            SUM(CASE WHEN status = 'canceled' THEN quantity ELSE 0 END) AS total_canceled
        ")->groupBy('product_name');

    if (!empty($search)) {
      $workOrderQuery->where('product_name', 'LIKE', "%{$search}%");
    }

    if (Auth::user()->role == 'operator') {
      $workOrderQuery->where('operator', Auth::user()->id);
    }

    $totalData = WorkOrder::count();
    $totalFiltered = $workOrderQuery->count();

    $workOrders = $workOrderQuery
      ->offset($start)
      ->limit($limit)
      ->orderBy($order, $dir)
      ->get();

    $data = [];
    foreach ($workOrders as $workOrder) {
      $nestedData['product_name'] = $workOrder->product_name;
      $nestedData['total_pending'] = $workOrder->total_pending;
      $nestedData['total_in_progress'] = $workOrder->total_in_progress;
      $nestedData['total_completed'] = $workOrder->total_completed;
      $nestedData['total_canceled'] = $workOrder->total_canceled;

      $data[] = $nestedData;
    }

    return response()->json([
      'draw' => intval($request->input('draw')),
      'recordsTotal' => intval($totalData),
      'recordsFiltered' => intval($totalFiltered),
      'code' => 200,
      'data' => $data,
    ]);
  }

  public function export()
  {
    return Excel::download(new WorkOrderReportExport, 'work_order_report.xlsx');
  }
}
