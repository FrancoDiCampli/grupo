<?php

namespace App\Traits;

use App\Contacto;

trait ContactosTrait
{
    public static function contactos($entidad)
    {
        $clase = class_basename($entidad);

        switch ($clase) {
            case 'Cliente':
                $referencia = 'CL' . $entidad->id;
                return Contacto::where('referencia', $referencia)->get();
                break;

            case 'Supplier':
                $referencia = 'PR' . $entidad->id;
                return Contacto::where('referencia', $referencia)->get();
                break;

            default:
                return [];
                break;
        }
    }

    public static function crearContactos($entidad, $request)
    {
        $clase = class_basename($entidad);

        switch ($clase) {
            case 'Cliente':
                $referencia = 'CL' . $entidad->id;
                break;

            case 'Supplier':
                $referencia = 'PR' . $entidad->id;
                break;

            default:
                $referencia = '';
                break;
        }

        if ($request['contactos']) {
            foreach ($request['contactos'] as $tel) {
                Contacto::create([
                    'nombre' => $tel['nombre'],
                    'numero' => $tel['numero'],
                    'email' => $tel['email'],
                    'cargo' => $tel['cargo'],
                    'referencia' => $referencia,
                ]);
            }
        }
    }

    public static function editarContactos($entidad, $request)
    {
        $clase = class_basename($entidad);

        switch ($clase) {
            case 'Cliente':
                $referencia = 'CL' . $entidad->id;
                break;

            case 'Supplier':
                $referencia = 'PR' . $entidad->id;
                break;

            default:
                $referencia = '';
                break;
        }

        $nuevosContactos = $request['contactos'];
        $contactosEliminados = [];
        if ($request['eliminados']) {
            $contactosEliminados = $request['eliminados'];
        }

        // Agregar nuevos
        foreach ($nuevosContactos as $tel) {
            if (!array_key_exists('id', $tel)) {
                Contacto::create([
                    'nombre' => $tel['nombre'],
                    'numero' => $tel['numero'],
                    'email' => $tel['email'],
                    'cargo' => $tel['cargo'],
                    'referencia' => $referencia,
                ]);
            }
        }

        // Eliminar
        if (count($contactosEliminados) > 0) {
            foreach ($contactosEliminados as $tel) {
                Contacto::destroy($tel['id']);
            }
        }
    }
}
