<?php

namespace App\Models\Box;

use CodeIgniter\Model;

class DocumentSequenceModel extends Model
{
  protected $table = 'document_sequence';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'last_number',
    'expedition_point'
  ];

  public function update_last_number($id)
  {
    $row = $this->find($id);

    if (!$row)
      return false;

    $current = (int) $row['last_number'];
    $next = $current + 1;

    $formatted = str_pad($next, 7, '0', STR_PAD_LEFT);

    $this->update($id, [
      'last_number' => $formatted
    ]);

    return $formatted;
  }

}