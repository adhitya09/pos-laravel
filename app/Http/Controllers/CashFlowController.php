<?php

namespace App\Http\Controllers;

use App\Models\CashboxFlow;
use App\Models\CashFlowSource;
use App\Models\CashFlow;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashFlowController extends Controller
{
    /**
     * Get base query with applied filters
     */
    private function getFilteredQuery(Request $request)
    {
        $query = CashboxFlow::with('source');

        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }
        if ($request->filled('type') && in_array($request->type, ['in', 'out'])) {
            $query->where('type', $request->type);
        }
        if ($request->filled('source_id')) {
            $query->where('source_id', $request->source_id);
        }

        return $query;
    }

    /**
     * Calculate summary totals with filters
     */
    private function calculateSummary(Request $request)
    {
        $query = $this->getFilteredQuery($request);

        // Cashbox flows (primary table)
        $totalModalCashbox = (clone $query)
            ->whereHas('source', function ($q) {
                $q->whereIn('name', ['Modal Awal', 'Modal Tambahan']);
            })
            ->sum('amount');

        $totalInflowCashbox = (clone $query)->where('type', 'in')->sum('amount');
        $totalOutflowCashbox = (clone $query)->where('type', 'out')->sum('amount');

        // Legacy cash_flows table (if used elsewhere in the app)
        $legacyQuery = CashFlow::query();
        if ($request->filled('from_date')) {
            $legacyQuery->whereDate('flow_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $legacyQuery->whereDate('flow_date', '<=', $request->to_date);
        }
        if ($request->filled('type') && in_array(strtolower($request->type), ['in', 'out'])) {
            $legacyQuery->where('type', strtoupper($request->type));
        }

        $totalModalLegacy = (clone $legacyQuery)
            ->whereIn('source', ['Modal Awal', 'Modal Tambahan'])
            ->sum('amount');

        $totalInflowLegacy = (clone $legacyQuery)->where('type', 'IN')->sum('amount');
        $totalOutflowLegacy = (clone $legacyQuery)->where('type', 'OUT')->sum('amount');

        // Transactions (cash sales) - treat as inflow
        $txQuery = Transaction::query()->whereHas('paymentMethod', function ($q) {
            $q->where('is_cash', true);
        })->where('status', 'completed');

        if ($request->filled('from_date')) {
            $txQuery->whereDate('transaction_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $txQuery->whereDate('transaction_date', '<=', $request->to_date);
        }

        $totalInflowTransactions = $txQuery->sum('total_amount');

        // Combine totals
        $totalModal = $totalModalCashbox + $totalModalLegacy;
        $totalInflow = $totalInflowCashbox + $totalInflowLegacy + $totalInflowTransactions;
        $totalOutflow = $totalOutflowCashbox + $totalOutflowLegacy;
        $totalToko = $totalInflow - $totalOutflow;

        return compact('totalModal', 'totalInflow', 'totalOutflow', 'totalToko');
    }

    /**
     * Display a listing of cash flows.
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);

        $cashFlows = $this->getFilteredQuery($request)
            ->latest('date')
            ->paginate($perPage)
            ->withQueryString();

        $summary = $this->calculateSummary($request);

        // Get sources for dropdowns
        $inSources = CashFlowSource::where('type', 'in')
            ->orWhere('type', 'both')
            ->orderBy('sort_order')
            ->get();

        $outSources = CashFlowSource::where('type', 'out')
            ->orWhere('type', 'both')
            ->orderBy('sort_order')
            ->get();

        $allSources = CashFlowSource::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('pages.cashflow.index', [
            'cashFlows' => $cashFlows,
            'inSources' => $inSources,
            'outSources' => $outSources,
            'allSources' => $allSources,
            ...$summary
        ]);
    }

    /**
     * Store a newly created cash flow.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:in,out',
            'source_id' => 'required|exists:cash_flow_sources,id',
            'amount' => 'required|integer|min:1',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $source = CashFlowSource::find($validated['source_id']);

        // Validate source type matches cash flow type
        if ($source->type !== 'both' && $source->type !== $validated['type']) {
            return response()->json([
                'message' => 'Sumber yang dipilih tidak sesuai dengan tipe cash flow',
            ], 422);
        }

        // Add manual cash flow data
        $validated['is_auto'] = false;
        $validated['reference_type'] = null;
        $validated['reference_id'] = null;

        $cashFlow = CashboxFlow::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cash flow berhasil ditambahkan',
            'data' => $cashFlow->load('source'),
        ], 201);
    }

    /**
     * Delete a cash flow (only manual entries).
     */
    public function destroy(CashboxFlow $cashFlow)
    {
        // Prevent deletion of auto-generated entries
        if ($cashFlow->is_auto) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus cash flow otomatis dari transaksi');
        }

        $cashFlow->delete();

        return redirect()->back()
            ->with('success', 'Cash flow berhasil dihapus');
    }
}
