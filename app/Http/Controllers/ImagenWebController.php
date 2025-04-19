<?php

namespace App\Http\Controllers;

use App\Models\ImagenWeb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImagenWebController extends Controller
{
    public function index()
    {
        $imagenes = ImagenWeb::all(['key', 'path']);

        $result = $imagenes->mapWithKeys(function ($item) {
            return [$item->key => $item->path];
        });

        return response()->json($result);
    }

    public function store(Request $request)
    {
        $contacto = ImagenWeb::create([
            'key' => $request->key,
            'path' => $request->path,
        ]);

        return response()->json([
            'message' => 'Imagen registrada exitosamente',
            'data' => $contacto
        ], 201);
    }

    public function show($key)
    {
        $imagen = ImagenWeb::where('key', $key);
        if (!$imagen) {
            return response()->json(['message' => 'Imagen no encontrada'], 404);
        }
        return response()->json($imagen);
    }

    public function update(Request $request)
    {
        ini_set('memory_limit', '128M');
        $tipos = ['logo', 'carrusel1', 'carrusel2', 'carrusel3'];
        $response = [];
    
        foreach ($tipos as $tipo) {
            if ($request->hasFile($tipo)) {
                $file = $request->file($tipo);
                $filename = $tipo . '.png';
    
                // Leer la imagen con Intervention y convertirla a PNG
                $image = Image::make($file)->encode('png');
    
                // Guardar en el disco 'public' en la carpeta imagenes_web
                Storage::disk('public')->put('imagenes_web/' . $filename, $image);
    
            } else {
                $response[] = $tipo . " no actualizado";
            }
        }
    
        return response()->json([
            'message' => 'Imágenes actualizadas correctamente.',
            "response" => $response
        ]);
    }

    public function destroy($id)
    {
        $contacto = ImagenWeb::find($id);
        if (!$contacto) {
            return response()->json(['message' => 'Consulta no encontrada'], 404);
        }

        $contacto->delete();

        return response()->json(['message' => 'Consulta eliminada exitosamente']);
    }
}
