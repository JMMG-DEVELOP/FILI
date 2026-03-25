<?php

namespace App\Models\Box;

use CodeIgniter\Model;

class UserBoxModel extends Model
{
  protected $table = 'users_box';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'user',
    'expedition_point'
  ];

  public function getUserExpeditionData($userId)
  {
    return $this->db->table('users_box ub')
      ->select('
            ub.user,
            ep.id as expedition_point,
            ep.code,
            ep.sucursal,

            ds.last_number as document_last_number,
            isq.last_number as invoice_last_number
        ')
      ->join('expedition_point ep', 'ep.id = ub.expedition_point')
      ->join('document_sequence ds', 'ds.expedition_point = ep.id', 'left')
      ->join('invoice_sequence isq', 'isq.expedition_point = ep.id', 'left')
      ->where('ub.user', $userId)
      ->get()
      ->getResultArray();
  }

  public function getUserExpeditionGrouped($userId, $sucursal)
  {
    $data = $this->db->table('users_box ub')
      ->select('
            ep.sucursal,
            ep.id as expedition_point,
            ep.code,

            ds.id as document_sequence_id,
            ds.last_number as document_last_number,

            isq.id as invoice_sequence_id,
            isq.last_number as invoice_last_number
        ')
      ->join('expedition_point ep', 'ep.id = ub.expedition_point')
      ->join('document_sequence ds', 'ds.expedition_point = ep.id', 'left')
      ->join('invoice_sequence isq', 'isq.expedition_point = ep.id', 'left')
      ->where('ub.user', $userId)
      ->where('ub.sucursal', $sucursal)
      ->get()
      ->getResultArray();

    $result = [];

    foreach ($data as $row) {
      $sucursal = $row['sucursal'];

      $result[$sucursal] = [
        'sucursal' => $row['sucursal'],
        'expedition_point' => $row['expedition_point'],
        'expedition_code' => $row['code'],

        'document_sequence_id' => $row['document_sequence_id'],
        'document_last_number' => $row['document_last_number'],

        'invoice_sequence_id' => $row['invoice_sequence_id'],
        'invoice_last_number' => $row['invoice_last_number'],

        'document_number' => '00' . $row['sucursal'] . ' ' . $row['code'] . ' ' . $row['document_last_number'],
        'invoice_number' => '00' . $row['sucursal'] . ' ' . $row['code'] . ' ' . $row['invoice_last_number'],
      ];
    }

    return $result;
  }

  public function getUserExpedition($userId, $sucursal)
  {
    $row = $this->db->table('users_box ub')
      ->select('
            ep.sucursal,
            ep.id as expedition_point,
            ep.code,

            ds.id as document_sequence_id,
            ds.last_number as document_last_number,

            isq.id as invoice_sequence_id,
            isq.last_number as invoice_last_number
        ')
      ->join('expedition_point ep', 'ep.id = ub.expedition_point')
      ->join('document_sequence ds', 'ds.expedition_point = ep.id', 'left')
      ->join('invoice_sequence isq', 'isq.expedition_point = ep.id', 'left')
      ->where('ub.user', $userId)
      ->where('ep.sucursal', $sucursal) // 👈 CORREGIDO (no ub.sucursal)
      ->get()
      ->getRowArray(); // 👈 SOLO UNO

    if (!$row) {
      return null; // o []
    }

    return [
      'sucursal' => $row['sucursal'],
      'expedition_point' => $row['expedition_point'],
      'expedition_code' => $row['code'],

      'document_sequence_id' => $row['document_sequence_id'],
      'document_last_number' => $row['document_last_number'],

      'invoice_sequence_id' => $row['invoice_sequence_id'],
      'invoice_last_number' => $row['invoice_last_number'],

      // 🔥 FORMATO PRO
      'document_number' =>
        str_pad($row['sucursal'], 3, '0', STR_PAD_LEFT) . ' ' .
        str_pad($row['code'], 3, '0', STR_PAD_LEFT) . ' ' .
        str_pad($row['document_last_number'], 7, '0', STR_PAD_LEFT),

      'invoice_number' =>
        str_pad($row['sucursal'], 3, '0', STR_PAD_LEFT) . ' ' .
        str_pad($row['code'], 3, '0', STR_PAD_LEFT) . ' ' .
        str_pad($row['invoice_last_number'], 7, '0', STR_PAD_LEFT),
    ];
  }

}