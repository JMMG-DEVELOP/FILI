<?php

namespace App\Models\Auth;

use CodeIgniter\Model;

class UsersSucursals extends Model
{
    protected $table = 'users_sucursals';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'user',
        'sucursal',
    ];
    public function getAll()
    {
        return $this->db->table('users_sucursals us')
            ->select('
      us.id,
      us.user,
      us.sucursal,

      u.name as user_name,
      u.user as username,
      u.category,

      uc.name as category_name,

      s.name as sucursal_name
    ')
            ->join(
                'users u',
                'u.id = us.user'
            )
            ->join(
                'users_category uc',
                'uc.id = u.category',
                'left'
            )
            ->join(
                'sucursals s',
                's.id = us.sucursal'
            )
            ->orderBy('s.id', 'ASC')
            ->orderBy('u.name', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function get_by_id($id)
    {
        if (empty($id)) {
            return null;
        }

        return $this->db->table('users_sucursals us')
            ->select('
        us.id,
        us.user,
        us.sucursal,

        u.name as user_name,
        u.user as username,

        s.name as sucursal_name
      ')
            ->join(
                'users u',
                'u.id = us.user'
            )
            ->join(
                'sucursals s',
                's.id = us.sucursal'
            )
            ->where('us.id', $id)
            ->get()
            ->getRowArray();
    }

    public function get_Users()
    {
        return $this->db->table('users')
            ->select('
      id,
      name,
      user,
      status
    ')
            ->where('status', 1)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function get_Sucursals()
    {
        return $this->db->table('sucursals')
            ->select('
      id,
      name
    ')
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();
    }
    public function add($values)
    {
        if (empty($values)) {
            return false;
        }

        $user = $values['user'] ?? null;
        $sucursal = $values['sucursal'] ?? null;

        if (empty($user) || empty($sucursal)) {
            $this->errors = [
                'user' => 'DEBE SELECCIONAR EL USUARIO Y LA SUCURSAL'
            ];

            return false;
        }

        $exists = $this->db->table($this->table)
            ->where('user', $user)
            ->where('sucursal', $sucursal)
            ->countAllResults();

        if ($exists > 0) {
            $this->errors = [
                'user' => 'ESTE USUARIO YA ESTA ASIGNADO A ESTA SUCURSAL'
            ];

            return false;
        }

        return $this->insert([
            'user' => $user,
            'sucursal' => $sucursal
        ]);
    }
    public function edit($values, $id)
    {
        if (empty($values) || empty($id)) {
            return false;
        }

        $user = $values['user'] ?? null;
        $sucursal = $values['sucursal'] ?? null;

        if (empty($user) || empty($sucursal)) {
            return false;
        }

        $exists = $this->db->table($this->table)
            ->where('user', $user)
            ->where('sucursal', $sucursal)
            ->where('id !=', $id)
            ->countAllResults();

        if ($exists > 0) {
            $this->errors = [
                'user' => 'ESTE USUARIO YA ESTA ASIGNADO A ESTA SUCURSAL'
            ];

            return false;
        }

        return $this->update($id, [
            'user' => $user,
            'sucursal' => $sucursal
        ]);
    }

    public function quit($id)
    {
        if (empty($id)) {
            return false;
        }

        try {
            $deleted = $this->delete($id);

            if (!$deleted) {
                $this->errors = [
                    'delete' => 'NO SE PUDO ELIMINAR LA ASIGNACION'
                ];

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            $message = $e->getMessage();

            if (
                stripos($message, 'foreign key constraint fails') !== false ||
                stripos($message, 'Cannot delete or update a parent row') !== false
            ) {
                $this->errors = [
                    'delete' => 'NO SE PUEDE ELIMINAR LA ASIGNACION PORQUE ESTA SIENDO UTILIZADA'
                ];

                return false;
            }

            $this->errors = [
                'delete' => 'ERROR AL ELIMINAR LA ASIGNACION'
            ];

            return false;
        }
    }
    public function details($user)
    {
        return $this->select([
            'users_sucursals.sucursal',
            'sucursals.name AS sucursal_name'
        ])
            ->join('sucursals', 'sucursals.id = users_sucursals.sucursal')
            ->where('users_sucursals.user', $user)
            ->findAll();
    }




}