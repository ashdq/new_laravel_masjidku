<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Donasi;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class DonasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // Di method index()
    public function index(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            
            // Semua role bisa melihat semua donasi
            $donasi = Donasi::with(['user', 'donatur'])->get();
            
            return response()->json([
                'status' => 'success',
                'data' => $donasi
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat data donasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            
            // Only warga can create donations
            if ($user->roles !== 'warga') {
                return response()->json(['message' => 'Unauthorized. Only warga can create donations.'], 403);
            }

            $request->validate([
                'nama' => 'required|string|max:255',
                'jumlah_donasi' => 'required|numeric|min:0',
                'date' => 'required|date',
                'note' => 'nullable|string',
            ]);

            $donasi = Donasi::create([
                'nama' => $request->nama,
                'jumlah_donasi' => $request->jumlah_donasi,
                'date' => $request->date,
                'note' => $request->note,
                'user_id' => $user->id,
                'donatur_id' => $user->id
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $donasi
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membuat donasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Donasi $donasi): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            
            // Admin and Takmir can see any donation
            if (in_array($user->roles, ['admin', 'takmir'])) {
                return response()->json([
                    'status' => 'success',
                    'data' => $donasi->load(['user', 'donatur'])
                ]);
            }
            
            // Warga can only see their own donations
            if ($donasi->donatur_id === $user->id) {
                return response()->json([
                    'status' => 'success',
                    'data' => $donasi->load(['user', 'donatur'])
                ]);
            }
            
            return response()->json(['message' => 'Unauthorized'], 403);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat detail donasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get total donations and statistics
     */
    public function getStatistics(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            // Get total donations
            $totalDonasi = Donasi::sum('jumlah_donasi');

            // Get monthly statistics for the current year
            $monthlyStats = Donasi::select(
                DB::raw('MONTH(date) as month'),
                DB::raw('SUM(jumlah_donasi) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->whereYear('date', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

            // Get recent donations (5 terbaru)
            $recentDonations = Donasi::with(['donatur:id,name'])
                ->orderBy('date', 'desc')
                ->limit(5)
                ->get();

            // Get top donors
            $topDonors = Donasi::select(
                'donatur_id',
                DB::raw('SUM(jumlah_donasi) as total_donasi'),
                DB::raw('COUNT(*) as jumlah_donasi')
            )
            ->with('donatur:id,name')
            ->groupBy('donatur_id')
            ->orderByDesc('total_donasi')
            ->limit(5)
            ->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'total_donasi' => $totalDonasi,
                    'monthly_statistics' => $monthlyStats,
                    'recent_donations' => $recentDonations,
                    'top_donors' => $topDonors
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat statistik donasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get list of all donors
     */
    public function getDonors(): JsonResponse
    {
        try {
            $donors = Donasi::select(
                'donatur_id',
                DB::raw('SUM(jumlah_donasi) as total_donasi'),
                DB::raw('COUNT(*) as jumlah_donasi'),
                DB::raw('MAX(date) as last_donation')
            )
            ->with('donatur:id,name,email')
            ->groupBy('donatur_id')
            ->orderByDesc('total_donasi')
            ->get();

            return response()->json([
                'status' => 'success',
                'data' => $donors
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat daftar donatur',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's donation history with statistics
     */
    public function getMyDonations(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            
            $donations = Donasi::where('donatur_id', $user->id)
                ->orderBy('date', 'desc')
                ->get();

            $totalDonasi = $donations->sum('jumlah_donasi');
            $jumlahDonasi = $donations->count();

            // Get monthly statistics for the current year
            $monthlyStats = Donasi::where('donatur_id', $user->id)
                ->select(
                    DB::raw('MONTH(date) as month'),
                    DB::raw('SUM(jumlah_donasi) as total'),
                    DB::raw('COUNT(*) as count')
                )
                ->whereYear('date', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email
                    ],
                    'total_donasi' => $totalDonasi,
                    'jumlah_donasi' => $jumlahDonasi,
                    'monthly_statistics' => $monthlyStats,
                    'riwayat_donasi' => $donations
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat riwayat donasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
