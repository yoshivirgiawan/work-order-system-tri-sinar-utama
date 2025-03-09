<?php

namespace App\Http\Requests\WorkOrder;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkOrderRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    return [
      'status' => 'required|in:pending,in_progress,completed,canceled',
      'operator' => 'nullable|exists:users,id',
    ];
  }

  public function messages()
  {
    return [
      'status.required' => 'Status is required',
      'status.in' => 'Status must be one of the following: Pending, In Progress, Completed, Canceled',
      'operator.exists' => 'Operator does not exist',
    ];
  }
}
