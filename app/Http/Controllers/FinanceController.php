<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class FinanceController extends Controller
{
    private function resolveContextView(string $defaultView): string
    {
        $routeName = request()->route()?->getName() ?? '';
        $bankView = 'bank.' . $defaultView;

        if (str_starts_with($routeName, 'bank.') && view()->exists($bankView)) {
            return $bankView;
        }

        return $defaultView;
    }

    public function feeDue(Request $request)
    {
        if (!Schema::hasTable('apara_invoices')) {
            $invoices = collect();
            $feeDueRouteName = Route::has('bank.fee_due') && str_starts_with((string) request()->route()?->getName(), 'bank.')
                ? 'bank.fee_due'
                : 'fee_due';

            return view($this->resolveContextView('fee_due'), compact('invoices', 'feeDueRouteName'));
        }

        $query = DB::table('apara_invoices')->where('status', 'due');
        $this->applyBankDataScope($query, 'apara_invoices');
        $this->applyInvoiceFilters($query, $request, 'invoiced_at');

        $invoices = $query->orderByDesc('invoiced_at')->get();
        $feeDueRouteName = Route::has('bank.fee_due') && str_starts_with((string) request()->route()?->getName(), 'bank.')
            ? 'bank.fee_due'
            : 'fee_due';

        return view($this->resolveContextView('fee_due'), compact('invoices', 'feeDueRouteName'));
    }

    public function invoice(Request $request)
    {
        if (!Schema::hasTable('apara_invoices')) {
            $invoices = collect();
            $invoiceRouteName = Route::has('bank.invoice') && str_starts_with((string) request()->route()?->getName(), 'bank.')
                ? 'bank.invoice'
                : 'invoice';

            return view($this->resolveContextView('invoice'), compact('invoices', 'invoiceRouteName'));
        }

        $query = DB::table('apara_invoices')->where('status', 'paid');
        $this->applyBankDataScope($query, 'apara_invoices');
        $this->applyInvoiceFilters($query, $request, 'paid_at');

        $invoices = $query->orderByDesc('paid_at')->get();
        $invoiceRouteName = Route::has('bank.invoice') && str_starts_with((string) request()->route()?->getName(), 'bank.')
            ? 'bank.invoice'
            : 'invoice';

        return view($this->resolveContextView('invoice'), compact('invoices', 'invoiceRouteName'));
    }

    public function receipt(Request $request)
    {
        if (!Schema::hasTable('apara_invoices')) {
            $invoices = collect();
            $receiptRouteName = Route::has('bank.receipt') && str_starts_with((string) request()->route()?->getName(), 'bank.')
                ? 'bank.receipt'
                : 'receipt';

            return view($this->resolveContextView('receipt'), compact('invoices', 'receiptRouteName'));
        }

        $query = DB::table('apara_invoices');
        $this->applyBankDataScope($query, 'apara_invoices');
        $this->applyInvoiceFilters($query, $request, 'invoiced_at');

        $invoices = $query->orderByDesc('invoiced_at')->get();
        $receiptRouteName = Route::has('bank.receipt') && str_starts_with((string) request()->route()?->getName(), 'bank.')
            ? 'bank.receipt'
            : 'receipt';

        return view($this->resolveContextView('receipt'), compact('invoices', 'receiptRouteName'));
    }

    public function history(Request $request)
    {
        if (!Schema::hasTable('apara_payment_histories')) {
            $payments = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            $historyRouteName = str_starts_with((string) request()->route()?->getName(), 'bank.')
                ? 'bank.history'
                : 'history';

            return view($this->resolveContextView('history'), compact('payments', 'historyRouteName'));
        }

        $query = DB::table('apara_payment_histories');
        $this->applyBankDataScope($query, 'apara_payment_histories');

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

        $historyRouteName = str_starts_with((string) request()->route()?->getName(), 'bank.')
            ? 'bank.history'
            : 'history';

        return view($this->resolveContextView('history'), compact('payments', 'historyRouteName'));
    }

    public function payments(Request $request)
    {
        if (!Schema::hasTable('apara_payment_histories')) {
            $payments = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            $paymentSummary = [
                'total_records' => 0,
                'paid_records' => 0,
                'pending_records' => 0,
                'failed_records' => 0,
                'total_amount' => 0,
            ];
            $paymentsRouteName = str_starts_with((string) request()->route()?->getName(), 'bank.')
                ? 'bank.payments'
                : 'payments';

            return view($this->resolveContextView('payments'), compact('payments', 'paymentSummary', 'paymentsRouteName'));
        }

        $query = DB::table('apara_payment_histories');
        $this->applyBankDataScope($query, 'apara_payment_histories');

        if ($request->filled('from_date')) {
            $query->whereDate('payment_date', '>=', $request->input('from_date'));
        }

        if ($request->filled('to_date')) {
            $query->whereDate('payment_date', '<=', $request->input('to_date'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->input('payment_type'));
        }

        if ($request->filled('payment_ref')) {
            $query->where('payment_ref', 'like', '%' . $request->input('payment_ref') . '%');
        }

        $summaryQuery = clone $query;
        $payments = $query->orderByDesc('payment_date')->paginate(15)->withQueryString();
        $paymentSummary = [
            'total_records' => (clone $summaryQuery)->count(),
            'paid_records' => (clone $summaryQuery)->where('status', 'paid')->count(),
            'pending_records' => (clone $summaryQuery)->where('status', 'pending')->count(),
            'failed_records' => (clone $summaryQuery)->where('status', 'failed')->count(),
            'total_amount' => (float) ((clone $summaryQuery)->sum('total_amount') ?? 0),
        ];

        $paymentsRouteName = str_starts_with((string) request()->route()?->getName(), 'bank.')
            ? 'bank.payments'
            : 'payments';

        return view($this->resolveContextView('payments'), compact('payments', 'paymentSummary', 'paymentsRouteName'));
    }

    private function applyBankDataScope($query, string $table): void
    {
        $bankProfile = session('bank_profile');

        if (!is_array($bankProfile) || empty($bankProfile) || !Schema::hasTable($table)) {
            return;
        }

        $query->where(function ($scopedQuery) use ($bankProfile, $table) {
            $hasScopedCondition = false;

            foreach (['for_branch_bank_id', 'branch_id', 'bank_branch_id'] as $column) {
                if (Schema::hasColumn($table, $column) && !empty($bankProfile['id'])) {
                    $scopedQuery->where($column, $bankProfile['id']);
                    $hasScopedCondition = true;
                    break;
                }
            }

            foreach (['selected_bank_name', 'bank_name'] as $column) {
                if (Schema::hasColumn($table, $column) && !empty($bankProfile['bank_name'])) {
                    if ($hasScopedCondition) {
                        $scopedQuery->orWhere($column, $bankProfile['bank_name']);
                    } else {
                        $scopedQuery->where($column, $bankProfile['bank_name']);
                        $hasScopedCondition = true;
                    }
                }
            }

            if (!$hasScopedCondition) {
                $scopedQuery->whereRaw('1 = 0');
            }
        });
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
