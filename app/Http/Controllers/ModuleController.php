<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\View;

class ModuleController extends Controller
{
    public function show(string $module)
    {
        $menu = config('clinic.menu');
        $meta = $menu[$module] ?? null;

        abort_if($meta === null, 404);

        return view('modules.placeholder', [
            'moduleKey' => $module,
            'title' => $meta['label'],
            'description' => $meta['description'] ?? '',
            'icon' => $meta['icon'] ?? 'dot',
        ]);
    }
}
