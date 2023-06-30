<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    try {
      $invoices = Invoice::with(['user', 'details'])->latest()
        ->when(request()->q != '', function ($q) {
          $q->where('inv_number', 'LIKE', '%' . request()->q . '%');
        })
        ->where('user_id', auth()->id())
        ->paginate(25);

      return $this->generateResponse($invoices, '', 200);
    } catch (\Exception $e) {
      return $this->generateResponse([], 'failed get data invoices', 500);
    }
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    // set validation rule
    $rules = [
      'due_date' => 'required',
    ];
    $rulesdetails = [
      'details.*.item_name' => 'required',
      'details.*.qty'       => 'required',
      'details.*.price'     => 'required',
    ];

    $validation = $request->validate($rules);
    $validationdetails = $request->validate($rulesdetails);

    try {
      DB::beginTransaction();

      $validation['user_id'] = auth()->id();
      $validation['inv_number'] = $this->generateInvNumber();
      $inv = Invoice::with('details')->create($validation);

      foreach ($validationdetails['details'] as $vdet) {
        $vdet['price_subtotal'] = $vdet['qty'] * $vdet['price'];
        $inv->details()->create($vdet);
      }

      $inv->refresh();
      $inv->update(['total' => $inv->details->sum('price_subtotal')]);

      DB::commit();
      return $this->generateResponse([], 'invoice successfully created', 200);
    } catch (\Exception $th) {
      DB::rollBack();
      return $this->generateResponse([], 'failed get data invoices', 500);
    }
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    try {
      $invoice = Invoice::with(['user', 'details'])
        ->where('user_id', auth()->id())
        ->find($id);

      // if not found data
      if (!$invoice) {
        return $this->generateResponse([], 'failed get data invoice', 500);
      }

      return $this->generateResponse($invoice, '', 200);
    } catch (\Exception $e) {
      return $this->generateResponse([], 'failed get data invoice', 500);
    }
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(string $id)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, string $id)
  {
    // set validation rule
    $rules = [
      'due_date' => 'nullable',
    ];
    $rulesdetails = [
      'details.*.id'        => 'nullable',
      'details.*.item_name' => 'required',
      'details.*.qty'       => 'required',
      'details.*.price'     => 'required',
    ];

    $validation = $request->validate($rules);
    $validationdetails = $request->validate($rulesdetails);
    try {
      DB::beginTransaction();

      $inv = Invoice::with('details')->find($id);
      if (!$inv) {
        return $this->generateResponse([], 'failed get data invoice', 500);
      }

      foreach ($validationdetails['details'] as $vdet) {
        $vdet['price_subtotal'] = $vdet['qty'] * $vdet['price'];
        if (empty($vdet['id'])) {
          $inv->details()->create($vdet);
        } else {
          $invdet = InvoiceDetail::where('invoice_id', $inv->id)->where('id', $vdet['id'])->first();
          if (!$invdet) {
            $inv->details()->create($vdet);
          } else {
            $invdet->update($vdet);
          }
        }
      }

      $inv->refresh();
      $validation['total'] = $inv->details->sum('price_subtotal');
      $inv->update($validation);

      DB::commit();
      return $this->generateResponse([], 'invoice successfully updated', 200);
    } catch (\Exception $th) {
      DB::rollBack();
      return $this->generateResponse([], 'failed get data invoice', 500);
    }
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    try {
      InvoiceDetail::where('invoice_id', $id)->delete();
      $inv = Invoice::find($id);
      $inv->delete();
      return $this->generateResponse([], 'invoice successfully deleted!', 200);
    } catch (\Exception $e) {
      return $this->generateResponse([], 'failed destroy invoice', 500);
    }
  }
}
