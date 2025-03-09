<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Routing\Controllers\Middleware;
use App\Infrastructures\Services\v1\RoleService;
use Illuminate\Routing\Controllers\HasMiddleware;
use App\Infrastructures\Services\v1\PermissionService;

class RoleController extends Controller implements HasMiddleware
{
  protected $roleService;
  protected $permissionService;

  public function __construct()
  {
    $this->roleService = new RoleService();
    $this->permissionService = new PermissionService();
  }

  public static function middleware(): array
  {
    return [
      new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('read role,web'), only: ['index']),
      new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('create role,web'), only: ['create', 'store']),
      new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('update role,web'), only: ['edit', 'update']),
      new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('delete role,web'), only: ['destroy']),
    ];
  }

  /**
   * Redirect to role-management view.
   *
   */
  public function index()
  {
    return view('content.role.index');
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
      2 => 'name',
      3 => 'created_at',
    ];

    $search = [];

    $totalData = Role::count();

    $totalFiltered = $totalData;

    $limit = $request->input('length');
    $start = $request->input('start');
    $order = $columns[$request->input('order.0.column')];
    $dir = $request->input('order.0.dir');

    if (empty($request->input('search.value'))) {
      $roles = Role::offset($start)
        ->limit($limit)
        ->orderBy($order, $dir)
        ->get();
    } else {
      $search = $request->input('search.value');

      $roles = Role::where('name', 'LIKE', "%{$search}%")
        ->offset($start)
        ->limit($limit)
        ->orderBy($order, $dir)
        ->get();

      $totalFiltered = Role::where('name', 'LIKE', "%{$search}%")
        ->count();
    }

    $data = [];

    if (!empty($roles)) {
      // providing a dummy id instead of database ids
      $ids = $start;

      foreach ($roles as $role) {
        $nestedData['id'] = $role->id;
        $nestedData['name'] = $role->name;
        $nestedData['created_at'] = Carbon::parse($role->created_at)->format('Y-m-d H:i:s');

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
    $permissions = $this->permissionService->getGroupedPermissions();
    $data['groupedPermissions'] = [];

    foreach ($permissions as $key => $permission) {
      $dump['group'] = $key;
      $dump['permissions'] = $permission;
      array_push($data['groupedPermissions'], (object) $dump);
    }

    return view('content.role.form', $data);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    try {
      DB::beginTransaction();
      $this->roleService->create($request->all());
      DB::commit();

      return redirect()->route('roles.index');
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
    $data['role'] = $this->roleService->getById($id);
    $permissions = $this->permissionService->getGroupedPermissions();
    $data['groupedPermissions'] = [];

    foreach ($permissions as $key => $permission) {
      $dump['group'] = $key;
      $dump['permissions'] = $permission;
      array_push($data['groupedPermissions'], (object) $dump);
    }

    return view('content.role.form', $data);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    try {
      DB::beginTransaction();
      $this->roleService->updateById($id, $request->all());
      DB::commit();

      return redirect()->route('roles.index');
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
      $this->roleService->deleteById($id);
      return response()->json('Deleted');
    } catch (Exception $e) {
      return response()->json($e->getMessage());
    }
  }
}
