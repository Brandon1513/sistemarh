<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CarruselImage;
use Illuminate\Support\Facades\Storage;

class CarruselController extends Controller
{
    public function index()
    {
        $carruselImages = CarruselImage::all();
        return view('carrusel.index', compact('carruselImages'));
    }

    public function create()
    {
        return view('carrusel.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'image' => 'required|image|mimes:jpeg,png,jpg,gif', //|max:2048
        'titulo' => 'nullable|string|max:255',
        'descripcion' => 'nullable|string',
    ]);

    $path = $request->file('image')->store('carrusel', 'public');

    CarruselImage::create([
        'image' => $path,
        'titulo' => $request->titulo,
        'descripcion' => $request->descripcion,
    ]);

    return redirect()->route('carrusel.index')->with('success', 'Imagen agregada al carrusel.');
}

public function destroy($id)
{
    $imagen = CarruselImage::findOrFail($id);

    // Verificar si la imagen realmente existe en el storage
    $rutaImagen = 'public/' . $imagen->image;

    if (Storage::exists($rutaImagen)) {
        Storage::delete($rutaImagen);
    } else {
        return response()->json(['success' => false, 'message' => 'Imagen no encontrada en el almacenamiento'], 404);
    }

    // Eliminar la imagen de la base de datos
    $imagen->delete();

    return response()->json(['success' => true, 'message' => 'Imagen eliminada correctamente']);
}

public function update(Request $request, $id)
{
    $request->validate([
        'titulo' => 'required|string|max:255',
        'descripcion' => 'nullable|string|max:500',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $image = CarruselImage::findOrFail($id);
    $image->titulo = $request->titulo;
    $image->descripcion = $request->descripcion;

    if ($request->hasFile('image')) {
        // Eliminar la imagen anterior si existe
        Storage::delete('public/' . $image->image);

        // Guardar la nueva imagen
        $path = $request->file('image')->store('carrusel', 'public');
        $image->image = $path;
    }

    $image->save();

    return redirect()->route('carrusel.index')->with('success', 'Imagen actualizada correctamente.');
}


}
