<?php

/*
 * Clinical Data — Vaccination Bitmask
 *
 * Stores the vaccination status of up to 50 items
 * in a compact binary string.
 *
 * Each position represents one vaccination record:
 *
 *   0 = not selected
 *   1 = selected
 *
 * Example:
 *
 *   101001...
 *
 * This approach reduces the amount of data required to
 * represent multiple boolean values in a database field.
 */

// Number of vaccination items supported by the application.
$totalVacunas = 50;

// Initialize all vaccination states as inactive.
$stringBits = str_repeat('0', $totalVacunas);

// Process submitted vaccination data.
for ($posicion = 0; $posicion < $totalVacunas; $posicion++) {

    if ($request->has("vacuna_$posicion")) {
        $stringBits[$posicion] = '1';
    }
}

// Store the resulting bit string.
$datosClinicos = $stringBits;

/*
 * Example result:
 *
 * 000100010001000...
 *
 * Each character represents the state of one
 * vaccination item.
 */