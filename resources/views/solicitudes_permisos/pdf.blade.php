<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de Permiso</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }

        .header-table, .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .header-table td, .info-table td {
            border: 1px solid black;
            padding: 8px;
            
            vertical-align: top;
        }
        table.info-table th {
        border: 1px solid black;
        padding: 8px;
        text-align: left;
        vertical-align: top;
    }

        .header-table {
            margin-bottom: 20px;
            max-width: 100%;
        }

        .logo {
            width: 80px;
            
            
        }

        .header-title {
            text-align: center;
            font-weight: bold;
        }

        .signature {
            margin-top: 30px;
        }

        .signature-line {
            border-top: 1px solid black;
            width: 200px;
            text-align: center;
            margin-top: 5px;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-weight: bold;
        }
        
            
    </style>
</head>
<body>

    <!-- Header con logo y detalles del documento -->
    <table class="header-table">
        <tr>
            <td rowspan="2" style="width: 10%; border: 1px solid black; text-align: center;">
                <img src="{{ public_path('images/logo.png') }}" class="logo" alt="Logo" style="max-width: 100px;" >
            </td>
            <td colspan="3" class="header-title" style="width: 90%; border: 1px solid black; text-align: center;" ><strong>Nombre del documento:</strong><br>
                Permiso de Pase de Salida 
            </td>
        </tr>
        <tr>
            <td class="header-title"> Fecha de Emisión:<br>
                17-jun-2024
            </td>
             <td class="header-title">Revisión:<br>
                05
            </td>    
                <td class="header-title">
                    Código: <br>
                    F-REH-02
                </td>
        </tr>
    </table>

    <!-- Información del permiso -->
    <table class="info-table">
        <tr>
            <td>Pase de Entrada: @if($permission->entry_exit_type == 'entrada') (X) @else ( ) @endif</td>
            <td colspan="3">Pase de Salida: @if($permission->entry_exit_type == 'salida') (X) @else ( ) @endif</td>
         </tr>
        <tr>
            <td>Nombre:</td>
            <td colspan="3">{{ $permission->user->name }}</td>
        </tr>
        <tr>
            <td>Puesto:</td>
            <td colspan="3">{{ $permission->position }}</td>
        </tr>
        <tr>
            <td>Departamento:</td>
            <td colspan="3">{{ $permission->department->name }}</td>
        </tr>
        <tr>
            <td>Horario Oficial:</td>
            <td colspan="3">{{ \Carbon\Carbon::parse($permission->official_schedule)->format('h:i A') }} </td>
        </tr>
        <tr>
            <td>Hora de Entrada/Salida:</td>
            <td  colspan="3">{{ \Carbon\Carbon::parse($permission->entry_exit_time)->format('h:i A') }} </td>
        </tr>
        <tr>
            <td>Fecha del Permiso:</td>
            <td colspan="3">{{ $permission->date }}</td>
        </tr>
        <tr>
            <td>Motivo:</td>
            <td colspan="3">{{ $permission->reason }}</td>
        </tr>
    </table>

    <!-- Firmas -->
    <table style="width: 100%; margin-top: 50px;">
        <tr>
            <td style="text-align: center;">
                ____________________________
            </td>
        </tr>
        <tr>
            <td style="text-align: center;">
                Firma del Empleado
            </td>
        </tr>
    </table>
    
    <table style="width: 100%; margin-top: 100px;">
        <tr>
            <td style="text-align: center; width: 50%;">
                @if($permission->status == 'pendiente')
                    Pendiente
                @elseif($permission->status == 'aprobado')
                    Aprobado
                @else
                    Rechazado
                @endif
            </td>
            <td style="text-align: center; width: 50%;">
            </td>
        </tr>
        <tr>
            <td style="text-align: center; width: 50%;">
                ____________________________
            </td>
            <td style="text-align: center; width: 50%;">
                ____________________________
            </td>
        </tr>
        <tr>
            <td style="text-align: center;">
                Firma del Jefe Inmediato
            </td>
            <td style="text-align: center;">
                Firma de Recursos Humanos
            </td>
        </tr>
    </table>
    

</body>
</html>
