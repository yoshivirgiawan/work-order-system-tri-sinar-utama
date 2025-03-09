<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controllers\Middleware;
use App\Infrastructures\Services\v1\UserService;
use Illuminate\Routing\Controllers\HasMiddleware;

class UserController extends Controller implements HasMiddleware
{
  protected $userService;

  public function __construct()
  {
    $this->userService = new UserService();
  }

  public static function middleware(): array
  {
    return [
      new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('read user,web'), only: ['index']),
      new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('create user,web'), only: ['create', 'store']),
      new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('update user,web'), only: ['edit', 'update']),
      new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('delete user,web'), only: ['destroy']),
    ];
  }

  /**
   * Redirect to user-management view.
   *
   */
  public function index()
  {
    $users = User::all();
    $userCount = $users->count();
    $superAdminCount = User::where('role', 'super admin')->get()->count();
    $productionManagerCount = User::where('role', 'production manager')->get()->count();
    $operatorCount = User::where('role', 'operator')->get()->count();

    return view('content.user.index', [
      'totalUser' => $userCount,
      'superAdminCount' => $superAdminCount,
      'productionManagerCount' => $productionManagerCount,
      'operatorCount' => $operatorCount
    ]);
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
      3 => 'email',
      4 => 'role',
      5 => 'created_at',
    ];

    $search = [];

    $totalData = User::count();

    $totalFiltered = $totalData;

    $limit = $request->input('length');
    $start = $request->input('start');
    $order = $columns[$request->input('order.0.column')];
    $dir = $request->input('order.0.dir');

    if (empty($request->input('search.value'))) {
      $users = User::offset($start)
        ->limit($limit)
        ->orderBy($order, $dir)
        ->get();
    } else {
      $search = $request->input('search.value');

      $users = User::where('name', 'LIKE', "%{$search}%")
        ->orWhere('email', 'LIKE', "%{$search}%")
        ->offset($start)
        ->limit($limit)
        ->orderBy($order, $dir)
        ->get();

      $totalFiltered = User::where('name', 'LIKE', "%{$search}%")
        ->orWhere('email', 'LIKE', "%{$search}%")
        ->count();
    }

    $data = [];

    if (!empty($users)) {
      // providing a dummy id instead of database ids
      $ids = $start;

      foreach ($users as $user) {
        $nestedData['id'] = $user->id;
        $nestedData['fake_id'] = ++$ids;
        $nestedData['name'] = $user->name;
        $nestedData['email'] = $user->email;
        $nestedData['role'] = $user->role;
        $nestedData['created_at'] = Carbon::parse($user->created_at)->format('Y-m-d H:i:s');

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

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
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
      $this->userService->create($request->all());
      DB::commit();
      return response()->json('Updated');
    } catch (Exception $e) {
      DB::rollBack();
      return response()->json($e->getMessage());
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

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id): JsonResponse
  {
    $user = $this->userService->getById($id);
    return response()->json($user);
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
      $this->userService->updateById($id, $request->all());
      DB::commit();
      return response()->json('Updated');
    } catch (Exception $e) {
      DB::rollBack();
      return response()->json($e->getMessage());
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
      $this->userService->deleteById($id);
      return response()->json('Deleted');
    } catch (Exception $e) {
      return response()->json($e->getMessage());
    }
  }
}
