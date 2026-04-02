<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function feeDue(Request $request)
    {
        $query = DB::table('apara_invoices')->where('status', 'due');
        $this->applyInvoiceFilters($query, $request, 'invoiced_at');

        $invoices = $query->orderByDesc('invoiced_at')->get();

        return view('fee_due', compact('invoices'));
    }

    public function invoice(Request $request)
    {
        $query = DB::table('apara_invoices')->where('status', 'paid');
        $this->applyInvoiceFilters($query, $request, 'paid_at');

        $invoices = $query->orderByDesc('paid_at')->get();

        return view('invoice', compact('invoices'));
    }

    public function receipt(Request $request)
    {
        $query = DB::table('apara_invoices');
        $this->applyInvoiceFilters($query, $request, 'invoiced_at');

        $invoices = $query->orderByDesc('invoiced_at')->get();

        return view('receipt', compact('invoices'));
    }

    public function history(Request $request)
    {
        $query = DB::table('apara_payment_histories');

        if ($request->filled('from_date')) {
            $query->whereDate('payment_date', '>=', $request->input('from_date'));
        }

        if ($request->filled('to_date')) {
            $query->whereDate('payment_date', '<=', $request->input('to_date'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('payment_ref')) {
            $query->where('payment_ref', 'like', '%' . $request->input('payment_ref') . '%');
        }

        $payments = $query->orderByDesc('payment_date')->paginate(15)->withQueryString();

        return view('history', compact('payments'));
    }

    private function applyInvoiceFilters($query, Request $request, string $dateColumn): void
    {
        if ($request->filled('id_no')) {
            $query->where('id_no', 'like', '%' . $request->input('id_no') . '%');
        }

        if ($request->filled('from_date')) {
            $query->whereDate($dateColumn, '>=', $request->input('from_date'));
        }

        if ($request->filled('to_date')) {
            $query->whereDate($dateColumn, '<=', $request->input('to_date'));
        }
    }
}
