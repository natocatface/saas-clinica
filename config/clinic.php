<?php

/*
|--------------------------------------------------------------------------
| Configuracion de modulos de la Clinica
|--------------------------------------------------------------------------
| Define los modulos del sistema. Se usa para construir el menu vertical
| y para resolver las paginas placeholder de cada modulo.
| 'route' (opcional) = nombre de ruta cuando el modulo ya tiene CRUD.
*/

return [
    'menu' => [
        'citas' => [
            'group' => 'Gestion Clinica',
            'label' => 'Citas y Agenda',
            'icon' => 'calendar',
            'roles' => ['recepcion', 'medico', 'enfermeria'],
            'route' => 'citas.index',
            'description' => 'Programacion, confirmacion y seguimiento de citas medicas.',
        ],
        'pacientes' => [
            'group' => 'Gestion Clinica',
            'label' => 'Pacientes',
            'icon' => 'users',
            'roles' => ['recepcion', 'medico', 'enfermeria'],
            'route' => 'pacientes.index',
            'description' => 'Registro y administracion de la base de pacientes.',
        ],
        'medicos' => [
            'group' => 'Gestion Clinica',
            'label' => 'Medicos',
            'icon' => 'stethoscope',
            'roles' => [],
            'route' => 'medicos.index',
            'description' => 'Gestion de profesionales, horarios y disponibilidad.',
        ],
        'historias' => [
            'group' => 'Gestion Clinica',
            'label' => 'Historias Clinicas',
            'icon' => 'clipboard',
            'roles' => ['medico', 'enfermeria'],
            'route' => 'historias.index',
            'description' => 'Expedientes clinicos electronicos de cada paciente.',
        ],
        'recetas' => [
            'group' => 'Gestion Clinica',
            'label' => 'Recetas',
            'icon' => 'pill',
            'roles' => ['medico'],
            'route' => 'recetas.index',
            'description' => 'Emision y control de recetas y prescripciones.',
        ],
        'especialidades' => [
            'group' => 'Gestion Clinica',
            'label' => 'Especialidades',
            'icon' => 'grid',
            'roles' => [],
            'route' => 'especialidades.index',
            'description' => 'Catalogo de especialidades y servicios de la clinica.',
        ],
        'laboratorio' => [
            'group' => 'Gestion Clinica',
            'label' => 'Laboratorio',
            'icon' => 'beaker',
            'roles' => ['medico', 'enfermeria'],
            'route' => 'laboratorio.index',
            'description' => 'Ordenes de laboratorio y resultados de examenes.',
        ],
        'farmacia' => [
            'group' => 'Administracion',
            'label' => 'Farmacia e Inventario',
            'icon' => 'box',
            'roles' => ['enfermeria'],
            'route' => 'productos.index',
            'description' => 'Control de stock de medicamentos e insumos.',
        ],
        'facturacion' => [
            'group' => 'Administracion',
            'label' => 'Facturacion y Pagos',
            'icon' => 'cash',
            'roles' => ['recepcion'],
            'route' => 'facturas.index',
            'description' => 'Facturas, cobros, seguros y estados de cuenta.',
        ],
        'facturacion_electronica' => [
            'group' => 'Administracion',
            'label' => 'Facturacion Electronica',
            'icon' => 'receipt',
            'roles' => [],
            'route' => 'facturacion.index',
            'description' => 'Emision de comprobantes electronicos ante SUNAT (Peru).',
        ],
        'comprobantes' => [
            'group' => 'Administracion',
            'label' => 'Comprobantes SUNAT',
            'icon' => 'check-badge',
            'roles' => ['recepcion'],
            'route' => 'facturacion.comprobantes',
            'description' => 'Listado y reporte de comprobantes electronicos emitidos.',
        ],
        'reportes' => [
            'group' => 'Administracion',
            'label' => 'Reportes',
            'icon' => 'chart',
            'roles' => [],
            'route' => 'reportes.index',
            'description' => 'Indicadores, estadisticas y reportes gerenciales.',
        ],
        'usuarios' => [
            'group' => 'Sistema',
            'label' => 'Usuarios y Roles',
            'icon' => 'shield',
            'roles' => [],
            'route' => 'usuarios.index',
            'description' => 'Administracion de usuarios, roles y permisos.',
        ],
        'configuracion' => [
            'group' => 'Sistema',
            'label' => 'Configuracion',
            'icon' => 'cog',
            'roles' => [],
            'route' => 'configuracion.index',
            'description' => 'Parametros generales de la clinica y del sistema.',
        ],
        'auditoria' => [
            'group' => 'Sistema',
            'label' => 'Auditoria',
            'icon' => 'shield',
            'route' => 'auditoria.index',
            'roles' => [],
            'description' => 'Bitacora de acciones del sistema.',
        ],
        'papelera' => [
            'group' => 'Sistema',
            'label' => 'Papelera',
            'icon' => 'box',
            'route' => 'papelera.index',
            'roles' => [],
            'description' => 'Registros eliminados (restaurar).',
        ],


    ],

    'groups_order' => ['Gestion Clinica', 'Administracion', 'Sistema'],
];
