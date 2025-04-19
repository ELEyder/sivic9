<?php

namespace App\Http\Controllers;

use App\Models\InformationPage;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class InformationPageController extends Controller
{
    /**
     * Mostrar la informaci贸n.
     */
    public function index()
    {
        $info = InformationPage::first();
        return response()->json($info);
    }

    /**
     * Actualizar la informaci贸n.
     */
    public function update(Request $request)
    {
        ini_set('memory_limit', '128M');
        $info = InformationPage::first();
    
        if (!$info) {
            $info = new InformationPage();
        }
    
        $info->fill($request->except(['image_1', 'image_2', 'image_3']));
    
        // Manejo de imágenes si se suben
        foreach (['image_1', 'image_2', 'image_3'] as $index => $imageField) {
            if ($request->hasFile($imageField)) {
                $file = $request->file($imageField);
                $filename = 'image' . ($index + 1) . '.png';
    
                // Leer la imagen con Intervention y convertirla a PNG
                $image = Image::make($file)->encode('png');
    
                // Guardar la imagen en el disco 'public' en la carpeta 'information'
                Storage::disk('public')->put('information/' . $filename, (string) $image);
    
                // Asignar la ruta de la imagen convertida a la propiedad correspondiente
                $info->$imageField = "/storage/information/" . $filename;
            }
        }
    
        $info->save();
    
        return response()->json([
            'message' => 'Info actualizada correctamente.',
        ]);
    }
}
