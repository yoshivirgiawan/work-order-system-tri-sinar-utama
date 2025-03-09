<?php

namespace App\Infrastructures\Services\v1;

use App\Infrastructures\Repositories\WorkOrderProgressRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Infrastructures\Repositories\WorkOrderRepository;

class WorkOrderService
{
  protected $workOrderRepository;
  protected $workOrderProgressRepository;

  public function __construct()
  {
    $this->workOrderRepository = new WorkOrderRepository();
    $this->workOrderProgressRepository = new WorkOrderProgressRepository();
  }

  public function getAll()
  {
    return $this->workOrderRepository->all();
  }

  public function getById($id)
  {
    return $this->workOrderRepository->findById($id, ['progresses']);
  }

  public function create($user, array $dataRequest)
  {
    $date = Carbon::now()->format('Ymd');
    $lastOrder = $this->workOrderRepository->getLastOrder();
    $newOrderNumber = str_pad($lastOrder + 1, 3, '0', STR_PAD_LEFT);
    $reference = "WO-{$date}-{$newOrderNumber}";
    $dataRequest['reference'] = $reference;
    $dataRequest['status'] = 'pending';

    $workOrder = $this->workOrderRepository->create($dataRequest);

    $dataProgress['work_order_id'] = $workOrder->id;
    $dataProgress['operator'] = $dataRequest['operator'];
    $dataProgress['status'] = $workOrder->status;
    $dataProgress['quantity'] = $dataRequest['quantity'];
    $dataProgress['progress_note'] = 'Work order assigned by ' . $user->name;
    $this->workOrderProgressRepository->create($dataProgress);

    return $workOrder;
  }

  public function updateById($id, array $dataRequest)
  {
    return $this->workOrderRepository->updateById($id, $dataRequest);
  }

  public function updateProgress($id, $userId, array $dataRequest)
  {
    $dataRequest['operator'] = $userId;
    $dataRequest['work_order_id'] = $id;

    $this->workOrderProgressRepository->create($dataRequest);
    $this->workOrderRepository->updateById($id, ['status' => $dataRequest['status']]);

    return $this->workOrderRepository->findById($id);
  }

  public function deleteById($id)
  {
    return $this->workOrderRepository->deleteById($id);
  }
}
