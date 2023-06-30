<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
  use HasFactory;

  protected $guarded = [];

  protected function serializeDate(\DateTimeInterface $date)
  {
    return $date->format('Y-m-d H:i:s');
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function details()
  {
    return $this->hasMany(InvoiceDetail::class);
  }
}
