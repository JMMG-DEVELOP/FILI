<?php

namespace App\Models\Printers;

use CodeIgniter\Model;

class PrintersModel extends Model
{
  protected $table = 'printers';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'name',
    'system_name',
    'auto_cut',
    'charset',
    'driver',
    'paper',

  ];
  public function quit($id)
  {
    if (empty($id)) {
      return false;
    }

    return $this->delete($id);
  }
  public function edit($values, $id)
  {
    if (empty($id)) {
      return false;
    }

    return $this->update($id, $values);
  }
  public function get_PrintersById($id)
  {
    $builder = $this->db->table('printers p');

    $builder->select('
        p.id,
        p.name,
        p.system_name,
        p.auto_cut,
        p.driver,
        pd.name AS driver_name,
        p.paper,
        pp.name AS paper_name,
        p.charset,
        pc.name AS charset_name,
        pc.code AS charset_code
    ');

    $builder->join(
      'printers_drivers pd',
      'pd.id = p.driver',
      'left'
    );

    $builder->join(
      'printers_paper pp',
      'pp.id = p.paper',
      'left'
    );

    $builder->join(
      'printers_charsets pc',
      'pc.id = p.charset',
      'left'
    );

    $builder->where('p.id', $id);

    return $builder->get()->getRowArray();
  }


  public function get_Printers()
  {
    $builder = $this->db->table('printers p');

    $builder->select('
        p.id,
        p.name,
        p.system_name,
        p.auto_cut,

        p.driver,
        pd.name AS driver_name,

        p.paper,
        pp.name AS paper_name,

        p.charset,
        pc.name AS charset_name,
        pc.code AS charset_code
    ');

    $builder->join(
      'printers_drivers pd',
      'pd.id = p.driver',
      'left'
    );

    $builder->join(
      'printers_paper pp',
      'pp.id = p.paper',
      'left'
    );

    $builder->join(
      'printers_charsets pc',
      'pc.id = p.charset',
      'left'
    );

    $builder->orderBy('p.id', 'DESC');

    return $builder->get()->getResultArray();
  }

  public function add($values)
  {
    return $this->insert($values);
  }

}