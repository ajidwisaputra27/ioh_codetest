<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
  use HasFactory;

  protected $guarded = [];

  protected function serializeDate(\DateTimeInterface $date)
  {
    return $date->format('Y-m-d H:i:s');
  }

  public function invoice()
  {
    return $this->belongsTo(User::class);
  }
}
