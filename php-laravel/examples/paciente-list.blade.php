<x-layouts.app>
    <x-slot:title>Pacientes</x-slot:title>
    <center>
        <div
            style="width: 100%; max-width: 900px; display: flex; flex-direction: column; align-items: center; margin: 0 auto;">

            <h1 style="margin-top: 15px;">Pacientes</h1><br>

            {{-- Paginación superior --}}
            <div style="margin-bottom: 20px;">
                @if ($results->count() > 0)
                    {{ $results->links('partials.paginacion') }}
                @endif
            </div>

            {{-- 1. TABLA VISIBLE GENERAL DE idS --}}
            @if ($results->count() > 0)
                <div
                    style="width: 100%; overflow-x: auto; display: block; clear: both; -webkit-overflow-scrolling: touch;">
                    <table class="tablavisible"
                        style="width:100%; max-width:650px; margin: 0 auto; border-collapse: collapse;">
                        <thead>
                            <tr>
                                <th style="text-align:center; padding: 10px; width: 80px;">Cédula</th>
                                <th style="text-align:center; padding: 10px;">Nombre</th>
                                @if (session('tipo') === 'veterinario')
                                    <th style="text-align:center; padding: 10px; width: 100px;">Raza</th>
                                @endif
                                <th style="width:120px; text-align:center; padding: 10px;" colspan="3">Teléfono</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($results as $r)
                                @php
                                    // Dirección limpia para redirección RESTful de Laravel

                                    // Regla de Negocio Veterinaria: apellido = Mascota, nombre = Raza
                                    if (session('tipo') === 'veterinario') {
                                        $urlEnlace = route('paciente.mascota.show', [
                                            'id' => urlencode($r->id),
                                        ]);
                                        $nombreCompleto = $r->apellido;
                                    } else {
                                        $urlEnlace = route('paciente.personal.show', [
                                            'id' => urlencode($r->id),
                                        ]);
                                        $nombreCompleto = $r->nombre . ' ' . $r->apellido;
                                    }
                                @endphp
                                <tr style="border-bottom: 1px solid #ddd;">
                                    <td style="text-align:center;">
                                        <a href="{{ $urlEnlace }}"><b>{{ $r->cedula }}</b></a>
                                    </td>
                                    <td style="text-align:left; padding-left:10px;">
                                        <a href="{{ $urlEnlace }}"><b>{{ $nombreCompleto }}</b></a>
                                    </td>
                                    @if (session('tipo') === 'veterinario')
                                        <td style="text-align:center;">
                                            <a href="{{ $urlEnlace }}"><b>{{ $r->nombre }}</b></a>
                                        </td>
                                    @endif

                                    {{-- Botonera de Telefonía Móvil --}}
                                    <td style="text-align: center; width: 40px;">
                                        <a href="tel:+58{{ $r->telefono_limpio }}">
                                            <img alt="Call" src="/assets/images/call.png"
                                                style="width:30px; height:30px;">
                                        </a>
                                    </td>
                                    <td style="text-align: center; width: 40px;">
                                        <a href="sms:+58{{ $r->telefono_limpio }}">
                                            <img alt="SMS" src="/assets/images/sms.png"
                                                style="width:30px; height:30px;">
                                        </a>
                                    </td>
                                    <td style="text-align: center; width: 40px;">
                                        <a href="https://wa.me{{ $r->telefono_limpio }}?text=" target="_blank">
                                            <img alt="WA" src="/assets/images/whatsapp.png"
                                                style="width:30px; height:30px;">
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="text-align: center; padding: 20px; color: #666;">
                    <h2>No se encontraron pacientes registrados.</h2>
                </div>
            @endif

            <br><br>

            {{-- 2. FORMULARIO DE BÚSQUEDA INTEGRADO --}}
            <div
                style="height:70px; width:300px; color: white; border-radius:20px; background-color:rgba(255,255,255,0.4); display: flex; align-items: center; justify-content: center;">
                <form method="POST" action="{{ route('pacientes.index') }}">
                    @csrf
                    <table class="tablainvisible" style="margin: 0 auto;">
                        <tr>
                            <td>
                                <input type="text"
                                    style="border:none; outline:none; color:#000; font-size:16px; height:40px; font-weight:bold; padding: 0 8px; border-radius: 4px;"
                                    name="search" value="{{ $search }}" placeholder="C.I. o teléfono"
                                    required />
                            </td>
                            <td>
                                <div class="tooltip">
                                    <button type="submit" class="botonicon">
                                        <img alt="Buscar" src="/assets/images/lupa.png" class="imgicon">
                                    </button>
                                    <span class="tooltiptext">Buscar</span>
                                </div>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
            <br>
            {{-- 3. COMPONENTE DE ABECEDARIO COMPARTIDO --}}
            <div style="width: 100%;">
                <x-abecedario />
            </div>

            {{-- 4. BOTÓN NUEVO PACIENTE (Acondicionado por privilegios de tipo de negocio) --}}
            @if (session('tipo') !== 'veterinario')
                <div style="margin-top: 25px;">
                    <div class="tooltip">
                        <a href="{{ route('paciente.personal.create') }}" class="botonicon button-link"
                            id="nuevopaciente"
                            style="background-color: #1e7e34; background-image: linear-gradient(-180deg, #74E0B8 10%, rgba(12, 129, 2) 70%); width:50px; height:50px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; border-radius: 50%;">
                            <img alt="Agregar" src="{{ asset('assets/images/add.png') }}"
                                style="width:20px; height:20px;">
                        </a>
                        <span class="tooltiptext">Nuevo Paciente</span>
                    </div>
                </div>
            @endif

            {{-- 5. VENTANA MODAL FLOTANTE (FILTRADO POR ABECEDARIO) --}}
            @if (!empty($pacientesFiltrados) && $pacientesFiltrados->count() > 0)
                <div id="informacion_filtro_paciente" class="pantallaoscura"
                    style="display: flex; position: fixed; z-index: 3000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.6); align-items: center; justify-content: center;">
                    <div id="dialogo-ventana-filtro" class="listado">



                        <h1><span style="color:green; text-shadow:none">Pacientes con Letra
                                "{{ strtoupper($letra) }}"</span></h1><br>

                        <table class="tablavisible" style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th style="padding: 8px; text-align: center;">Cédula</th>
                                    <th style="padding: 8px; text-align: center;">Nombre</th>
                                    @if (session('tipo') === 'veterinario')
                                        <th style="padding: 8px; text-align: center;">Raza</th>
                                    @endif
                                    <th colspan="3" style="text-align: center; padding: 8px;">Teléfono</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pacientesFiltrados as $pf)
                                    @php

                                        if (session('tipo') === 'veterinario') {
                                            $urlEnlaceModal = route('paciente.mascota.show', [
                                                'id' => urlencode($pf->id),
                                            ]);
                                            $nombreModal = $pf->apellido;
                                        } else {
                                            $urlEnlaceModal = route('paciente.personal.show', [
                                                'id' => urlencode($pf->id),
                                            ]);
                                            $nombreModal = $pf->nombre . ' ' . $pf->apellido;
                                        }
                                    @endphp
                                    <tr style="border-bottom: 1px solid #ddd;">
                                        <td style="text-align:center; padding: 8px;">
                                            <a href="{{ $urlEnlaceModal }}"><b>{{ $pf->cedula }}</b></a>
                                        </td>
                                        <td style="padding: 8px;">
                                            <a href="{{ $urlEnlaceModal }}"><b>{{ $nombreModal }}</b></a>
                                        </td>
                                        @if (session('tipo') === 'veterinario')
                                            <td style="text-align:center; padding: 8px;">
                                                <a href="{{ $urlEnlaceModal }}"><b>{{ $pf->nombre }}</b></a>
                                            </td>
                                        @endif
                                        <td style="text-align: center; width: 40px;">
                                            <a href="tel:+58{{ $pf->telefono_limpio }}">
                                                <img alt="Call" src="{{ asset('assets/images/call.png') }}"
                                                    style="width:30px;">
                                            </a>
                                        </td>
                                        <td style="text-align: center; width: 40px;">
                                            <a href="sms:+58{{ $pf->telefono_limpio }}">
                                                <img alt="SMS" src="{{ asset('assets/images/sms.png') }}"
                                                    style="width:30px;">
                                            </a>
                                        </td>
                                        <td style="text-align: center; width: 40px;">
                                            <a href="https://wa.me{{ $pf->telefono_limpio }}?text=" target="_blank">
                                                <img alt="WA" src="{{ asset('assets/images/whatsapp.png') }}"
                                                    style="width:30px;">
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </center>

    {{-- --- CONTROLADOR DE INTERFAZ SEGURO CON NONCE CSP --- --}}
    <script nonce="{{ session('nonce') }}">
        document.addEventListener('DOMContentLoaded', function() {
            const modalFiltro = document.getElementById('informacion_filtro_paciente');
            const btnCerrar = document.getElementById('btnCerrarFiltroPaciente');

            // 1. Manejador para cerrar el modal al presionar la '✕'
            if (btnCerrar && modalFiltro) {
                btnCerrar.addEventListener('click', function(e) {
                    e.preventDefault();
                    modalFiltro.style.display = 'none';
                    // Regresa limpiamente a la URL base sin el parámetro query ?go=
                    window.location.href = "{{ route('pacientes.index') }}";
                });
            }

            // 2. Cerrar el modal de forma segura al hacer clic en el fondo oscuro exterior
            if (modalFiltro) {
                modalFiltro.addEventListener('click', function(e) {
                    if (e.target === modalFiltro) {
                        window.location.href = "{{ route('pacientes.index') }}";
                    }
                });
            }
        });
    </script>

    <x-modales-sistema />
</x-layouts.app>
