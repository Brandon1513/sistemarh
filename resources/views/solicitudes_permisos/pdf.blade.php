<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de ausencias del personal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid black;
            padding: 5px;
            text-align: center;
        }

        .header {
            background-color: #D3A1D1;
        }

        .logo {
            max-width: 100px;
        }

        .info {
            background-color: #F0E0F6;
            font-weight: bold;
        }

        .signature {
            border-top: 1px solid black;
            width: 200px;
            margin: 20px auto 0;
            text-align: center;
        }

        .section-title {
            background-color: #D3A1D1;
            font-weight: bold;
            text-align: left;
            padding-left: 10px;
        }

        .checkbox {
            width: 10px;
            height: 10px;
            border: 1px solid black;
            display: inline-block;
        }

    </style>
</head>
<body>

    <!-- Header con logo y detalles del documento -->
    <table>
        <tr>
            <td rowspan="2" class="header" style="width: 15%; text-align: center;">
                <img src="{{ asset('images/logo.png') }}" class="logo" alt="Logo">

            </td>
            <td colspan="3" class="header" style="text-align: center;">
                <strong>Nombre del documento:</strong><br>Control de Ausencias del Personal
            </td>
        </tr>
        <tr>
            <td class="header">Fecha de Emisión:<br>17-jun-2024</td>
            <td class="header">Revisión:<br>03</td>
            <td class="header">Código:<br>F-REH-36</td>
        </tr>
    </table>

    <!-- Información del empleado -->
    <table>
        <tr>
            <td class=" section-title" colspan="3">Fecha</td>
            <td class="section-title" colspan="2">Nombre Completo (Apellido Paterno, Materno, Nombre(s))</td>
            <td class="section-title">Departamento</td>
        </tr>
        <tr>
            <td colspan="3">{{ date('d', strtotime($permiso->fecha_inicio)) }} {{ date('M', strtotime($permiso->fecha_inicio)) }} {{ date('Y', strtotime($permiso->fecha_inicio)) }}</td>
            <td colspan="2">{{ $permiso->empleado->name }}</td>
            <td>{{ $permiso->departamento->name }}</td>
        </tr>
    </table>

    <!-- Detalles del permiso -->
    <table>
        <tr>
            <td class="section-title" colspan="2">Permiso</td>
            <td class="section-title" colspan="2">Comisión</td>
            <td class="section-title" colspan="2">Suspensión</td>
        </tr>
        <tr>
            <td colspan="2">
                @if ($permiso->tipo == 'Permiso')
                    <div class="checkbox" style="background-color: black;"></div>
                @else
                    <div class="checkbox"></div>
                @endif
            </td>
            <td colspan="2">
                @if ($permiso->tipo == 'Comisión')
                    <div class="checkbox" style="background-color: black;"></div>
                @else
                    <div class="checkbox"></div>
                @endif
            </td>
            <td colspan="2">
                @if ($permiso->tipo == 'Suspensión')
                    <div class="checkbox" style="background-color: black;"></div>
                @else
                    <div class="checkbox"></div>
                @endif
            </td>
        </tr>
    </table>

    <!-- Tipo de permiso -->
    <table>
        <tr>
            <td class="section-title" colspan="3">Con Goce de Sueldo</td>
            <td class="section-title" colspan="3">Sin Goce de Sueldo</td>
        </tr>
        <tr>
            <td colspan="3">
                @if ($permiso->tipo_permiso == 'Con Goce de Sueldo')
                    <div class="checkbox" style="background-color: black;"></div>
                @else
                    <div class="checkbox"></div>
                @endif
            </td>
            <td colspan="3">
                @if ($permiso->tipo_permiso == 'Sin Goce de Sueldo')
                    <div class="checkbox" style="background-color: black;"></div>
                @else
                    <div class="checkbox"></div>
                @endif
            </td>
        </tr>
    </table>

    <!-- Fechas de inicio y término -->
    <table>
        <tr>
            <td class="section-title">Fecha de Inicio</td>
            <td class="section-title">Fecha de Término</td>
            <td class="section-title">Regresa a Laborar</td>
        </tr>
        <tr>
            <td>{{ date('d', strtotime($permiso->fecha_inicio)) }} {{ date('M', strtotime($permiso->fecha_inicio)) }} {{ date('Y', strtotime($permiso->fecha_inicio)) }}</td>
            <td>{{ date('d', strtotime($permiso->fecha_termino)) }} {{ date('M', strtotime($permiso->fecha_termino)) }} {{ date('Y', strtotime($permiso->fecha_termino)) }}</td>
            <td>{{ date('d', strtotime($permiso->fecha_regreso_laborar)) }} {{ date('M', strtotime($permiso->fecha_regreso_laborar)) }} {{ date('Y', strtotime($permiso->fecha_regreso_laborar)) }}</td>
        </tr>
    </table>

    <!-- Día de descanso -->
    <table>
        <tr>
            <td class="section-title">Día de Descanso</td>
        </tr>
        <tr>
            <td>{{ $permiso->dia_descanso }}</td>
        </tr>
    </table>

    <!-- Observaciones -->
    <table>
        <tr>
            <td class="section-title">Observaciones</td>
        </tr>
        <tr>
            <td>{{ $permiso->motivo ?? 'N/A' }}</td>
        </tr>
    </table>

   <!-- Firmas -->
<table>
    <tr>
        <td class="section-title">Empleado (Nombre y Firma)</td>
        <td class="section-title">Jefe Inmediato</td>
        <td class="section-title">Recursos Humanos</td>
    </tr>
    <tr>
        <!-- Nombre del empleado -->
        <td>{{ $permiso->empleado->name  }}</td>

        <!-- Estado de aprobación/rechazo del jefe inmediato -->
        <td>
            @if($permiso->estado == 'aprobado')
                <span class="badge bg-success">Aprobado</span>
            @elseif($permiso->estado == 'rechazado')
                <span class="badge bg-danger">Rechazado</span>
            @else
                <span class="badge bg-warning">Pendiente</span>
            @endif
        </td>

        <!-- Espacio para Recursos Humanos (puedes añadir más lógica si es necesario) -->
        <td><br></td>
    </tr>
</table>


</body>
</html>
