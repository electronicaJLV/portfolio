<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/*
 * Dynamic patient search and pagination
 *
 * Searches patients by:
 * - ID number
 * - Phone
 * - First name
 * - Last name
 *
 * Results are paginated to improve performance and
 * usability on small screens.
 */

$search = $request->input('search');

$query = DB::table('paciente')
    ->whereNull('deleted_at');

if (!empty($search)) {
    $query->where(function ($q) use ($search) {
        $q->where('cedula', 'LIKE', "%{$search}%")
          ->orWhere('telefono', 'LIKE', "%{$search}%")
          ->orWhere('nombre', 'LIKE', "%{$search}%")
          ->orWhere('apellido', 'LIKE', "%{$search}%");
    });
}

$results = $query
    ->orderBy('id', 'DESC')
    ->paginate(15);