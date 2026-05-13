<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);

        $payment_methods = PaymentMethod::withTrashed()
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('pages.payment-method.index', compact('payment_methods'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.payment-method.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:payment_methods,name',
            'description' => 'nullable|string',
            'is_cash' => 'boolean',
            'is_active' => 'boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $payment_method = new PaymentMethod();
        $payment_method->fill($validated);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('payment-methods', 'public');
            $payment_method->logo = $path;
        }

        $payment_method->save();

        return redirect()->route('payment-method.index')
            ->with('success', 'Metode pembayaran berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(PaymentMethod $payment_method)
    {
        return view('pages.payment-method.show', compact('payment_method'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PaymentMethod $payment_method)
    {
        return view('pages.payment-method.edit', compact('payment_method'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaymentMethod $payment_method)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:payment_methods,name,' . $payment_method->id,
            'description' => 'nullable|string',
            'is_cash' => 'boolean',
            'is_active' => 'boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $payment_method->fill($validated);

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($payment_method->logo && \Storage::disk('public')->exists($payment_method->logo)) {
                \Storage::disk('public')->delete($payment_method->logo);
            }

            $path = $request->file('logo')->store('payment-methods', 'public');
            $payment_method->logo = $path;
        }

        $payment_method->save();

        return redirect()->route('payment-method.index')
            ->with('success', 'Metode pembayaran berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentMethod $payment_method)
    {
        $payment_method->delete();

        return redirect()->route('payment-method.index')
            ->with('success', 'Metode pembayaran berhasil dihapus');
    }

    /**
     * Restore a soft-deleted resource
     */
    public function restore($id)
    {
        $payment_method = PaymentMethod::withTrashed()->findOrFail($id);
        $payment_method->restore();

        return redirect()->route('payment-method.index')
            ->with('success', 'Metode pembayaran berhasil dipulihkan');
    }
}
