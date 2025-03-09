<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrderProgress extends Model
{
  protected $fillable = [
    'work_order_id',
    'operator',
    'status',
    'quantity',
    'progress_note',
  ];

  public function workOrder()
  {
    return $this->belongsTo(WorkOrder::class);
  }

  public function operator()
  {
    return $this->belongsTo(User::class, 'operator');
  }
}
