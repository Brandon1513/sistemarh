<!DOCTYPE html>
<html>
<head>
    <title>Solicitud de Vacaciones</title>
</head>
<body>
    <h1>Solicitud de Vacaciones</h1>
    <p><strong>Empleado:</strong> {{ $vacation->empleado->name }}</p>
    <p><strong>Departamento:</strong> {{ $vacation->departamento->name }}</p>
    <p><strong>Fecha de Inicio:</strong> {{ $vacation->fecha_inicio }}</p>
    <p><strong>Fecha de Fin:</strong> {{ $vacation->fecha_fin }}</p>
    <p><strong>Estado:</strong> {{ ucfirst($vacation->estado) }}</p>
</body>
</html>
