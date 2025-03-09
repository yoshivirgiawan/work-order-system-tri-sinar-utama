<?php

namespace App\Http\Requests\WorkOrder;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProgressRequest extends FormRequest
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
      'quantity' => 'required|integer|min:1',
      'progress_note' => 'nullable|string',
    ];
  }

  public function messages()
  {
    return [
      'status.required' => 'Status is required',
      'status.in' => 'Status must be one of the following: Pending, In Progress, Completed, Canceled',
      'quantity.required' => 'Quantity is required',
      'quantity.integer' => 'Quantity must be an integer',
      'quantity.min' => 'Quantity must be at least 1',
    ];
  }
}
