<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class SPSanAnswer extends Model
{
  protected $table = 'sp_san_answer';
  protected $fillable = ['si_v2_id', 'twhpq1', 'twhpq2', 'twhpq3', 'twhpq4', 'twhpq5', 'twhpq6', 'twhpq7', 'twhpq8', 'twhpq9',
      'bhmpq1', 'bhmpq2', 'bhmpq3', 'bhmpq4', 'bhmpq5', 'bhmpq6', 'bhmpq7', 'bhmpq8', 'bhmpq9', 'bhmpq10', 'rwhq1', 'rwhq2', 'rwhq3',
      'rwhq4', 'rwhq5', 'rwhq6', 'rwhq7', 'rwhq8', 'rwhq9', 'rwhq10', 'rwhq11', 'rwhq12', 'rwhq13', 'rwhq14', 'rwhq15', 'rwhq16',
      'pwssq1', 'pwssq2', 'pwssq3', 'pwssq4', 'pwssq5', 'pwssq6', 'pwssq7', 'pwssq8', 'pwssq9', 'pwssq10', 'pwssq11',
      'pwsnq1', 'pwsnq2', 'pwsnq3', 'pwsnq4', 'pwsnq5', 'pwsnq6', 'pwsnq7', 'pwsnq8',
      'pwstq1', 'pwstq2', 'pwstq3', 'pwstq4', 'pwstq5'];
  protected $guarded = ['id'];

    public function SpSanInspectionV2()
    {
        return $this->belongsTo(SPSanInspectionV2::class, 'id', 'answer_id');
    }
}
