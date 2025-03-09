<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
  protected $fillable = [
    'reference',
    'product_name',
    'quantity',
    'due_date',
    'status',
    'operator',
  ];

  public function operatorUser()
  {
    return $this->belongsTo(User::class, 'operator');
  }

  public function progresses()
  {
    return $this->hasMany(WorkOrderProgress::class);
  }
}
