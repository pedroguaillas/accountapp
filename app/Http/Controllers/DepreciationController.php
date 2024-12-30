<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessDepreciation;
use App\Models\Depreciation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepreciationController extends Controller
{
    public function index()
    {
        $process = new ProcessDepreciation;
        $process->handle();

        $depreciations = Depreciation::all();

        return Inertia::render('Depreciation/Index', [
            'depreciations' => $depreciations,
        ]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function edit(Depreciation $depreciation)
    {
        //
    }

    public function update(Request $request, Depreciation $depreciation)
    {
        //
    }
}
