<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\WorkOrder;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\Middleware;
use App\Infrastructures\Services\v1\UserService;
use Illuminate\Routing\Controllers\HasMiddleware;
use App\Infrastructures\Services\v1\WorkOrderService;
use App\Http\Requests\WorkOrder\CreateWorkOrderRequest;
use App\Http\Requests\WorkOrder\UpdateWorkOrderRequest;

class WorkOrderController extends Controller implements HasMiddleware
{
  protected $userService;
  protected $workOrderService;

  public function __construct()
  {
    $this->userService = new UserService();
    $this->workOrderService = new WorkOrderService();
  }

  public static function middleware(): array
  {
    return [
      new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('read work order,web'), only: ['index']),
      new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('create work order,web'), only: ['create', 'store']),
      new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('update work order,web'), only: ['edit', 'update']),
      new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('update own work order,web'), only: ['editProgress', 'updateProgress']),
      new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('delete work order,web'), only: ['destroy']),
    ];
  }

  /**
   * Redirect to work order-management view.
   *
   */
  public function index()
  {
    return view('content.work-order.index');
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function data(Request $request)
  {
    $columns = [
      1 => 'id',
      2 => 'reference',
      3 => 'product_name',
      4 => 'quantity',
      5 => 'due_date',
      6 => 'status',
      7 => 'operator',
      8 => 'created_at',
    ];

    $search = [];

    $totalData = WorkOrder::count();

    $totalFiltered = $totalData;

    $limit = $request->input('length');
    $start = $request->input('start');
    $order = $columns[$request->input('order.0.column')];
    $dir = $request->input('order.0.dir');


    $workOrderQuery = WorkOrder::with('operatorUser');

    if (!empty($request->input('search.value'))) {
      $search = $request->input('search.value');

      $workOrderQuery = $workOrderQuery->where('reference', 'LIKE', "%{$search}%")
        ->orWhere('product_name', 'LIKE', "%{$search}%")
        ->orWhere('quantity', 'LIKE', "%{$search}%");
    }

    if (!empty($request->input('status'))) {
      $workOrderQuery = $workOrderQuery->where('status', $request->input('status'));
    }

    if (!empty($request->input('created_at'))) {
      $workOrderQuery = $workOrderQuery->whereDate('created_at', $request->input('created_at'));
    }

    if (Auth::user()->role == 'operator') {
      $workOrderQuery = $workOrderQuery->where('operator', Auth::user()->id);
    }

    $totalFiltered = $workOrderQuery->count();

    $workOrders = $workOrderQuery->offset($start)
      ->limit($limit)
      ->orderBy($order, $dir)
      ->get();

    $data = [];

    if (!empty($workOrders)) {
      foreach ($workOrders as $workOrder) {
        $nestedData['id'] = $workOrder->id;
        $nestedData['reference'] = $workOrder->reference;
        $nestedData['product_name'] = $workOrder->product_name;
        $nestedData['quantity'] = $workOrder->quantity;
        $nestedData['due_date'] = Carbon::parse($workOrder->due_date)->format('Y-m-d');
        $nestedData['status'] = Str::ucfirst(Str::replace('_', ' ', $workOrder->status));
        $nestedData['operator'] = $workOrder->operatorUser->name;
        $nestedData['created_at'] = Carbon::parse($workOrder->created_at)->format('Y-m-d H:i:s');

        $data[] = $nestedData;
      }
    }

    if ($data) {
      return response()->json([
        'draw' => intval($request->input('draw')),
        'recordsTotal' => intval($totalData),
        'recordsFiltered' => intval($totalFiltered),
        'code' => 200,
        'data' => $data,
      ]);
    } else {
      return response()->json([
        'message' => 'Internal Server Error',
        'code' => 500,
        'data' => [],
      ]);
    }
  }

  public function create()
  {
    $data['operators'] = $this->userService->getOperators();

    return view('content.work-order.form', $data);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(CreateWorkOrderRequest $request)
  {
    try {
      DB::beginTransaction();
      $user = Auth::user();
      $this->workOrderService->create($user, $request->all());
      DB::commit();

      return redirect()->route('work-orders.index');
    } catch (Exception $e) {
      DB::rollBack();

      return redirect()->back()->withErrors($e->getMessage());
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    //
  }

  public function edit($id)
  {
    $data['operators'] = $this->userService->getOperators();
    $data['workOrder'] = $this->workOrderService->getById($id);

    return view('content.work-order.form', $data);
  }

  public function editProgress($id)
  {
    $data['workOrder'] = $this->workOrderService->getById($id);

    return view('content.work-order.detail', $data);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(UpdateWorkOrderRequest $request, $id)
  {
    try {
      DB::beginTransaction();
      $this->workOrderService->updateById($id, $request->all());
      DB::commit();

      return redirect()->route('work-orders.index');
    } catch (Exception $e) {
      DB::rollBack();

      return redirect()->back()->withErrors($e->getMessage());
    }
  }

  public function updateProgress(Request $request, $id)
  {
    try {
      DB::beginTransaction();
      $user = Auth::user();
      $this->workOrderService->updateProgress($id, $user->id, $request->all());
      DB::commit();

      return redirect()->route('work-orders.edit-progress', $id);
    } catch (Exception $e) {
      DB::rollBack();

      return redirect()->back()->withErrors($e->getMessage());
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    try {
      $this->workOrderService->deleteById($id);
      return response()->json('Deleted');
    } catch (Exception $e) {
      return response()->json($e->getMessage());
    }
  }
}
