<table>
    <thead>
        <tr>
            <th>Empleado</th>
            <th>Departamento</th>
            <th>Fecha de Inicio</th>
            <th>Fecha de Fin</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($vacaciones as $vacacion)
            <tr>
                <td>{{ $vacacion->empleado->name }}</td>
                <td>{{ $vacacion->departamento->name }}</td>
                <td>{{ $vacacion->fecha_inicio }}</td>
                <td>{{ $vacacion->fecha_fin }}</td>
                <td>{{ ucfirst($vacacion->estado) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
