<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OperatorReportExport;

class ReportOperatorController extends Controller
{
  public function index()
  {
    return view('content.report.operators');
  }

  public function data(Request $request)
  {
    $columns = [
      1 => 'operator',
      2 => 'product_name',
      3 => 'total_completed',
    ];

    $search = $request->input('search.value');
    $limit = $request->input('length');
    $start = $request->input('start');
    $order = $columns[$request->input('order.0.column')] ?? 'operator';
    $dir = $request->input('order.0.dir') ?? 'asc';

    $workOrderQuery = WorkOrder::selectRaw("
      users.name AS operator,
      product_name,
      SUM(CASE WHEN status = 'Completed' THEN quantity ELSE 0 END) AS total_completed
  ")->join('users', 'work_orders.operator', '=', 'users.id')->groupBy('operator', 'product_name');

    if (!empty($search)) {
      $workOrderQuery->where('operator', 'LIKE', "%{$search}%")
        ->orWhere('product_name', 'LIKE', "%{$search}%");
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
      $nestedData['operator'] = $workOrder->operator;
      $nestedData['product_name'] = $workOrder->product_name;
      $nestedData['total_completed'] = $workOrder->total_completed;

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
    return Excel::download(new OperatorReportExport, 'work_order_operator_report.xlsx');
  }
}
