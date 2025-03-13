<?php

namespace App\Http\Controllers;

use App\Models\Noticia;
use Illuminate\Http\Request;
use App\Models\CarruselImage;
use Illuminate\Support\Facades\Storage;

class NoticiaController extends Controller
{
    public function index()
    {
        $noticias = Noticia::orderBy('fecha', 'desc')->paginate(10);
        return view('noticias.index', compact('noticias'));
    }

    public function create()
    {
        return view('noticias.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'contenido' => 'required',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'multimedia' => 'nullable|url',
            'autor' => 'required|string|max:255',
            'fecha' => 'required|date',
            'activo' => 'required|boolean',
        ]);
    
        $noticia = new Noticia();
        $noticia->titulo = $request->titulo;
        $noticia->contenido = $request->contenido;
        $noticia->autor = $request->autor;
        $noticia->fecha = $request->fecha;
        $noticia->activo = $request->activo;
    
        // Almacenar imagen si se sube un archivo
        if ($request->hasFile('imagen')) {
            $noticia->imagen = $request->file('imagen')->store('noticias', 'public');
        }
    
        // Almacenar enlace de video si se proporciona
        if ($request->multimedia) {
            $noticia->multimedia = $request->multimedia;
        }
    
        $noticia->save();
    
        return redirect()->route('noticias.index')->with('success', 'Noticia creada exitosamente.');
    }
    

    public function edit(Noticia $noticia)
    {
        return view('noticias.edit', compact('noticia'));
    }

    public function update(Request $request, Noticia $noticia)
{
    $validatedData = $request->validate([
        'titulo' => 'required|string|max:255',
        'contenido' => 'required',
        'imagen' => 'nullable|image|max:2048',
        'multimedia' => 'nullable|url',
        'autor' => 'required|string|max:255',
        'fecha' => 'required|date',
        'activo' => 'required|in:0,1', // 🔹 Acepta 0 o 1
    ]);

    // Manejo de imagen
    if ($request->hasFile('imagen')) {
        if ($noticia->imagen && Storage::exists('public/' . $noticia->imagen)) {
            Storage::delete('public/' . $noticia->imagen);
        }
        $path = $request->file('imagen')->store('noticias', 'public');
        $validatedData['imagen'] = $path;
    }

    $noticia->update($validatedData);

    return redirect()->route('noticias.index')->with('success', 'Noticia actualizada con éxito.');
}


    public function destroy(Noticia $noticia)
    {
        if ($noticia->imagen && Storage::exists('public/' . $noticia->imagen)) {
            Storage::delete('public/' . $noticia->imagen);
        }

        $noticia->delete();
        return redirect()->route('noticias.index')->with('success', 'Noticia eliminada.');
    }

    public function toggle(Noticia $noticia)
{
    $noticia->activo = !$noticia->activo; // Cambia el estado
    $noticia->save();

    return redirect()->route('noticias.index')->with('success', 'Estado de la noticia actualizado.');
}

public function publicIndex()
{
    $noticias = Noticia::where('activo', 1)->orderBy('fecha', 'desc')->paginate(8);
    $carruselImages = CarruselImage::all(); // Obtener imágenes del carrusel

    return view('noticias.public', compact('noticias', 'carruselImages'));
}


}
