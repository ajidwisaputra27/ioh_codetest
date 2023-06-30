<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
  use AuthorizesRequests, ValidatesRequests;

  public function generateResponse($data = [], $message = '', $statusCode = '')
  {
    //response
    $response = [
      'status_code' => $statusCode,
      'message'     => $message,
      'result'      => $data
    ];

    return response()->json($response, $statusCode);
  }

  public function generateInvNumber()
  {
    $invcount = Invoice::whereDate('created_at', now())->count();
    $padded = str()->padLeft($invcount + 1, 4, '0');

    return "INV" . now()->format('Ymd') . "" . $padded;
  }
}
