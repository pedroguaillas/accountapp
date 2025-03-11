<?php

namespace App\Http\Controllers\Landlord;

use App\Models\Landlord\PaymentRegim as LandlordPaymentRegim;
use App\Http\Controllers\Controller;
use App\Models\PaymentRegim;
use Illuminate\Http\Request;
use App\Models\Company;
use Inertia\Inertia;

class PaymentRegimController extends Controller
{
    // Método para mostrar los métodos de pago globales en la vista
    public function index()
    {
        $paymentRegims = PaymentRegim::all(); // Obtiene todos los métodos de pago
        $excludedCodes = $paymentRegims->pluck('region'); // Extrae solo los códigos de PayMethod

        $globalPaymentRegims = LandlordPaymentRegim::whereNotIn('region', $excludedCodes)->get(); // Filtra los que no están en PayMethod

        return Inertia::render('Business/General/PaymentRegim/Index', [
            'paymentRegims' => $paymentRegims,
            'globalPaymentRegims' => $globalPaymentRegims,
        ]);
    }

    // Método para guardar en el tenant los métodos seleccionados
    public function store(Request $request)
    {
        $company = Company::first();

        PaymentRegim::create(
            [
                'company_id' => $company->id,
                'region' => $request->region,
                'months_xiii' => $request->months_xiii,
                'months_xiv' => $request->months_xiv,
                'months_reserve_funds' => " "
            ]
        );
    }
}