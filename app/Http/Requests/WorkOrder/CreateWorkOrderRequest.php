<?php

namespace App\Http\Requests\WorkOrder;

use Illuminate\Foundation\Http\FormRequest;

class CreateWorkOrderRequest extends FormRequest
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
      'product_name' => 'required|string',
      'quantity' => 'required|integer|min:1',
      'due_date' => 'required|date',
      'operator' => 'required|exists:users,id'
    ];
  }

  public function messages()
  {
    return [
      'product_name.required' => 'Product name is required',
      'quantity.required' => 'Quantity is required',
      'quantity.integer' => 'Quantity must be an integer',
      'quantity.min' => 'Quantity must be at least 1',
      'due_date.required' => 'Due date is required',
      'due_date.date' => 'Due date must be a valid date',
      'operator.required' => 'Operator is required',
      'operator.exists' => 'Operator does not exist'
    ];
  }
}
