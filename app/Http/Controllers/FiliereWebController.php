<?php

namespace App\Http\Controllers;

use App\Models\Filiere;
use Illuminate\Http\Request;

class FiliereWebController extends Controller
{
    public function index()
    {
        $filieres = Filiere::all();
        return view('filiere', compact('filieres'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code_filiere' => 'required|string|min:3|unique:filiere,code_filiere',
            'label_filiere' => 'required|string|min:3',
            'desc_filiere' => 'required|string|min:3',
        ]);

        Filiere::create($request->all());

        return redirect()->back()->with('success', 'Filière ajoutée avec succès !');
    }

    public function update(Request $request, $code_filiere)
    {
        $filiere = Filiere::findOrFail($code_filiere);

        $request->validate([
            'label_filiere' => 'required|string|min:3',
            'desc_filiere' => 'required|string|min:3',
        ]);

        $filiere->update([
            'label_filiere' => $request->label_filiere,
            'desc_filiere' => $request->desc_filiere,
        ]);

        return redirect()->back()->with('success', 'Filière mise à jour !');
    }

    public function destroy($code_filiere)
    {
        $filiere = Filiere::findOrFail($code_filiere);
        $filiere->delete();

        return redirect()->back()->with('success', 'Filière supprimée !');
    }
}
