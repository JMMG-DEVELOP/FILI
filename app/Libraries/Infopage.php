<?php

namespace App\Libraries;
use App\Models\Auth\UsersModel;


class Infopage
{
  public function infopage(array $info): array
  {
    $usersModel = new UsersModel();
    $user = $usersModel->details(session()->get('id'));

    // Base obligatoria del layout
    $page = [
      'title' => $info['title'] ?? '',
      'category_name' => $user['category_name'] ?? null,
      'category_id' => $user['category'] ?? null,
      'user_id' => $user['id'] ?? null,
      'user_name' => $user['name'] ?? null,
      'user_user' => $user['user'] ?? null,
    ];

    // 🔥 MERGE: mantiene todo lo que venga del controlador
    return array_merge($page, $info);
  }

  public function redirect()
  {
    $category = session('category');
    return match ($category) {

      '1' => 'dashboard/index',
      '2' => 'dashboard/index',
      '3' => 'box',
      '4' => 'box',

      default => redirect()->to('/login')->with('error', 'Rol no autorizado'),
    };
  }
}


