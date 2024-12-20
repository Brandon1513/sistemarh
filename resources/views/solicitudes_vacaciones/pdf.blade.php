
<!-- resources/views/vacaciones/pdf.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitud de Vacaciones</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .signature {
            text-align: center;
            margin-top: 30px;
        }
        .purple {
            background-color: #D9A0CC;
        }
        .section-title {
            background-color: #D3A1D1;
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
                <strong>Nombre del documento:</strong><br> Solicitud de vacaciones por parte del empleado
            </td>
        </tr>
        <tr>
            <td class="header">Fecha de Emisión:<br>17-jun-2024</td>
            <td class="header">Revisión:<br>07</td>
            <td class="header">Código:<br>F-REH-14</td>
        </tr>
    </table>
    <table>
        <tr class="purple">
            <th class=" section-title">Nombre del Empleado (Apellido Paterno, Materno, Nombre)</th>
            <th class=" section-title">Clave de Empleado</th>
            <th class=" section-title">Fecha de Ingreso</th>
        </tr>
        <tr>
            <td>{{ $vacation->empleado->name }}</td>
            <td>{{ $vacation->empleado->clave_empleado }}</td>
            <td>{{ $vacation->empleado->fecha_ingreso  }}</td>
        </tr>
    </table>
    <table>
        <tr class="purple">
            <th class=" section-title">Puesto del empleado</th>
            <th class=" section-title">Departamento</th>
        </tr>
        <tr>
            <td>{{ $vacation->empleado->puesto_empleado }}</td>
            <td>{{ $vacation->departamento->name }}</td>
        </tr>
    </table>
    <table>
        <thead>
            <tr class="purple">
                <th class=" section-title">Antigüedad</th>
                <th class=" section-title">Días de vacaciones</th>
                <th class=" section-title">Fecha de solicitud por parte del empleado</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1 año</td>
                <td>12 días</td>
                <td rowspan="1">{{$vacation->created_at}}</td>
            </tr>
            <tr>
                <td>2 años</td>
                <td>14 días</td>
                <th class="section-title">Días que corresponden</th>
            </tr>
            <tr>
                <td>3 años</td>
                <td>16 días</td>
                <td rowspan="1">{{$vacation->dias_corresponden}}</td>
            </tr>
            <tr>
                <td>4 años</td>
                <td>18 días</td>
                <th class="section-title">Días Solicitados</th>
            </tr>
            <tr>
                <td>5 años</td>
                <td>20 días</td>
                <td rowspan="2">{{ $vacation->dias_solicitados }}</td>
            </tr>
            <tr>
                <td>6 – 10 años</td>
                <td>22 días</td>
            </tr>
            <tr>
                <td>11 – 15 años</td>
                <td>24 días</td>
                <th class="section-title">Días Pendientes por Disfrutar</th>
            </tr>
            <tr>
                <td>16 – 20 años</td>
                <td>26 días</td>
                <td rowspan="1">{{ $vacation->dias_pendientes }}</td>
            </tr>
            <tr>
                <td>21 – 25 años</td>
                <td>28 días</td>
                <th class="section-title">Periodo que corresponde</th>
            </tr>
            <tr>
                <td>26 – 30 años</td>
                <td>30 días</td>
                <td rowspan="2">{{ $vacation->periodo_correspondiente }}</td>
            </tr>
            <tr>
                <td>31 – 35 años</td>
                <td>32 días</td>
            </tr>
        </tbody>
    </table>
    
    <table>
       
        <tr>
            <th class="section-title">Inicio de Vacaciones</th>
            <td>{{ $vacation->fecha_inicio }}</td>
        </tr>
        <tr>
            <th class="section-title">Término de Vacaciones</th>
            <td>{{ $vacation->fecha_fin }}</td>
        </tr>
        <tr>
            <th class="section-title">Fecha para Presentarse a Trabajar</th>
            <td>{{ $vacation->fecha_reincorporacion }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <td class="section-title">Empleado (Nombre y Firma)</td>
            <td class="section-title">Jefe Inmediato</td>
            <td class="section-title">Recursos Humanos</td>
        </tr>
        <tr>
            <!-- Nombre del empleado -->
            <td>{{ $vacation->empleado->name  }}</td>
    
            <!-- Estado de aprobación/rechazo del jefe inmediato -->
            <td>
                @if($vacation->estado == 'aprobado')
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
