<?php

namespace App\Http\Controllers;
  
use App\Models\User;
use App\Models\Slecic_employee;
use App\Models\Bank_employee;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AuthController extends Controller
{   
    // Show forms
    public function showLoginForm() {
        return view('login');
    }

    public function showBankLoginForm(?string $bank = null) {
        $bankBrand = $this->resolveBankBrand($bank);
        $bankOptions = $this->availableBankOptions();
        $selectedBankId = $bankOptions
            ->first(function (array $bankOption) use ($bankBrand) {
                $identifier = strtoupper(trim(($bankOption['bank_name'] ?? '') . ' ' . ($bankOption['bank_code'] ?? '')));

                return !empty($bankBrand['slug'])
                    && str_contains($identifier, strtoupper($bankBrand['slug']));
            })['id'] ?? null;

        return view('bank.login', compact('bankBrand', 'bankOptions', 'selectedBankId'));
    }
    public function showRegisterForm() {}

    // Actions 
    public function login(Request $request) {
        $email = $request->input('email');
        $password = $request->input('password');
        $portal = $request->input('portal');
        $bankBrandSlug = $request->input('bank_brand');
        $selectedBankId = $request->input('bank_id');
        $bankBrand = $this->resolveBankBrand($bankBrandSlug);

        if ($portal === 'bank') {
            $request->validate([
                'bank_id' => 'required',
                'email' => 'required|email',
                'password' => 'required',
            ]);
        }

        $selectedBank = $portal === 'bank' ? $this->findBankById((int) $selectedBankId) : null;

        if ($portal === 'bank' && !$selectedBank) {
            return back()->withErrors([
                'bank_id' => 'Please select a valid registered bank.',
            ])->withInput($request->except('password'));
        }

        $credentials = [
            'email' => $email,
            'password' => $password
        ];

        try {
            if (Auth::attempt($credentials)) {
                // Authentication passed...
                $user = User::where('email', $email)->first();
                $isBankLinkedUser = false;
                $isSlecicLinkedUser = false;
                $userType = (int) ($user->type ?? -1);

                if ($user && Schema::hasTable('bank_employees')) {
                    $isBankLinkedUser = Bank_employee::where('user_id', $user->id)->exists();
                }

                if ($user && Schema::hasTable('slecic_employees')) {
                    $isSlecicLinkedUser = Slecic_employee::where('user_id', $user->id)->exists();
                }

                if ($portal === 'bank' && (int) ($user->type ?? -1) !== 1 && !$isBankLinkedUser) {
                    Auth::logout();

                    return back()->withErrors([
                        'email' => 'This login page is for bank users only.',
                    ]);
                }

                if ($portal === 'bank' && ($userType === 1 || $isBankLinkedUser)) {
                    $employee = $user && Schema::hasTable('bank_employees')
                        ? Bank_employee::where('user_id', $user->id)->first()
                        : null;

                    if ($selectedBank && (int) data_get($employee, 'branch_id') !== (int) data_get($selectedBank, 'id')) {
                        Auth::logout();

                        return back()->withErrors([
                            'email' => 'Those credentials do not belong to the selected bank.',
                        ])->withInput($request->except('password'));
                    }

                    session(['employee' => $employee]);
                    session(['bank_profile' => $selectedBank ? $this->resolveBankProfile($selectedBank) : null]);
                    session(['bank_brand' => $selectedBank ? $this->resolveBankBrandFromBank($selectedBank) : $this->resolveBankBrand($bankBrandSlug)]);
                    session(['portal' => 'bank']);

                    return redirect()->route('bank.dashboard');
                }

                if ($userType === 0 || $isSlecicLinkedUser) {
                    $employee = Slecic_employee::where('user_id', $user->id)->first();
                    session(['employee' => $employee]);
                    session()->forget(['bank_profile', 'bank_brand']);
                    session(['portal' => 'slecic']);
                    return redirect()->route('dashboard');    
                
                } elseif ($userType === 1 || $isBankLinkedUser) {
                    $employee = Bank_employee::where('user_id', $user->id)->first();
                    session(['employee' => $employee]);
                    $linkedBank = $this->findBankById((int) data_get($employee, 'branch_id'));
                    session(['bank_profile' => $linkedBank ? $this->resolveBankProfile($linkedBank) : null]);
                    session(['bank_brand' => $linkedBank ? $this->resolveBankBrandFromBank($linkedBank) : $this->resolveBankBrand($bankBrandSlug)]);
                    session(['portal' => 'bank']);
                    return redirect()->route('bank.dashboard');
                }

                Auth::logout();
            }
        } catch (QueryException $exception) {
            report($exception);

            return back()->withErrors([
                'email' => 'Login is temporarily unavailable because the database connection failed. Please check the database settings and try again.',
            ])->withInput($request->except('password'));
        }
        
        if ($portal === 'bank' && !empty($bankBrand['slug'])) {
            return back()->withErrors([
                'email' => "No {$bankBrand['name']} bank account matches those credentials.",
            ])->withInput($request->except('password'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->except('password'));
    }
    public function register(Request $request) {
        $user = new User();
        // $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->save();

        return redirect()->route('login.form');    
    }
    public function logout(Request $request) {
        $bankProfile = session('bank_profile');
        $bankBrand = session('bank_brand');
        $logoutTarget = !empty($bankProfile)
            ? (!empty($bankBrand['slug']) ? route('bank.login.brand', ['bank' => $bankBrand['slug']]) : route('bank.login.form'))
            : route('login.form');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect($logoutTarget);
    }

    // Password
    public function showForgotPasswordForm() {}
    public function sendResetLinkEmail(Request $request) {}
    public function showResetPasswordForm($token) {}
    public function resetPassword(Request $request) {}

    // Optional
    public function verifyEmail($token) {}

    private function resolveBankBrand(?string $bank): array
    {
        $slug = Str::slug((string) $bank);

        $brands = [
            'boc' => [
                'slug' => 'boc',
                'name' => 'BOC',
                'display_name' => 'BOC Bank',
                'logo' => asset('images/boc.png'),
            ],
            'dfcc' => [
                'slug' => 'dfcc',
                'name' => 'DFCC',
                'display_name' => 'DFCC Bank',
                'logo' => asset('images/dfcc-logo.jpg'),
            ],
            'lolc' => [
                'slug' => 'lolc',
                'name' => 'LOLC',
                'display_name' => 'LOLC Bank',
                'logo' => asset('images/lolc-logo.jpg'),
            ],
        ];

        return $brands[$slug] ?? [
            'slug' => null,
            'name' => 'Bank',
            'display_name' => 'Bank Portal',
            'logo' => asset('images/logo.png'),
        ];
    }

    private function availableBankOptions()
    {
        try {
            if (!Schema::hasTable('banks') || !Schema::hasTable('branches')) {
                return collect();
            }

            return DB::table('branches')
                ->join('banks', 'banks.id', '=', 'branches.bank_id')
                ->leftJoin('districts', 'districts.id', '=', 'branches.district_id')
                ->leftJoin('provinces', 'provinces.id', '=', 'districts.province_id')
                ->select(
                    'branches.id',
                    'branches.name as branch_name',
                    'branches.grade as branch_grade',
                    'branches.email',
                    'branches.tel_no as tel',
                    'banks.name as bank_name',
                    'banks.code as bank_code',
                    'provinces.name as province'
                )
                ->orderBy('banks.name')
                ->orderBy('branches.name')
                ->get()
                ->map(function ($bank) {
                    return [
                        'id' => $bank->id,
                        'bank_name' => trim((string) ($bank->bank_name ?? $bank->name ?? 'Bank')),
                        'bank_code' => trim((string) ($bank->bank_code ?? $bank->code ?? '')),
                        'branch_name' => trim((string) ($bank->branch_name ?? '')),
                    ];
                })
                ->filter(fn (array $bank) => !empty($bank['bank_name']))
                ->values();
        } catch (QueryException $exception) {
            report($exception);

            return collect();
        }
    }

    private function findBankById(int $bankId): ?object
    {
        try {
            if ($bankId <= 0 || !Schema::hasTable('banks') || !Schema::hasTable('branches')) {
                return null;
            }

            return DB::table('branches')
                ->join('banks', 'banks.id', '=', 'branches.bank_id')
                ->leftJoin('districts', 'districts.id', '=', 'branches.district_id')
                ->leftJoin('provinces', 'provinces.id', '=', 'districts.province_id')
                ->select(
                    'branches.id',
                    'branches.name as branch_name',
                    'branches.grade as branch_grade',
                    'branches.email',
                    'branches.tel_no as tel',
                    'banks.name as bank_name',
                    'banks.code as bank_code',
                    'provinces.name as province'
                )
                ->where('branches.id', $bankId)
                ->first();
        } catch (QueryException $exception) {
            report($exception);

            return null;
        }
    }

    private function resolveBankBrandFromBank(object $bank): array
    {
        $bankName = trim((string) ($bank->bank_name ?? $bank->name ?? 'Bank'));
        $bankCode = trim((string) ($bank->bank_code ?? $bank->code ?? $bank->branch_code ?? ''));
        $identifier = strtoupper(trim($bankName . ' ' . $bankCode));

        if (str_contains($identifier, 'BOC')) {
            return $this->resolveBankBrand('boc');
        }

        if (str_contains($identifier, 'DFCC')) {
            return $this->resolveBankBrand('dfcc');
        }

        if (str_contains($identifier, 'LOLC')) {
            return $this->resolveBankBrand('lolc');
        }

        return [
            'slug' => Str::slug($bankName),
            'name' => $bankName,
            'display_name' => $bankName . ' Portal',
            'logo' => asset('images/logo.png'),
        ];
    }

    private function resolveBankProfile(object $bank): array
    {
        return [
            'id' => $bank->id ?? null,
            'bank_name' => trim((string) ($bank->bank_name ?? $bank->name ?? 'Bank')),
            'bank_code' => trim((string) ($bank->bank_code ?? $bank->code ?? '')),
            'branch_name' => trim((string) ($bank->branch_name ?? '')),
            'branch_grade' => trim((string) ($bank->branch_grade ?? $bank->grade ?? '')),
            'province' => trim((string) ($bank->province ?? '')),
            'email' => trim((string) ($bank->email ?? '')),
            'tel' => trim((string) ($bank->tel ?? '')),
        ];
    }
}
