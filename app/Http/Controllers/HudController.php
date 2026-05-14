<?php

namespace App\Http\Controllers;

use App\Services\HermesClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class HudController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Hud/Dashboard', [
            'user' => Auth::user()?->only('name', 'email'),
            'hermes_base_url' => config('hermes.base_url'),
        ]);
    }

    public function system(): JsonResponse
    {
        $load = function_exists('sys_getloadavg') ? sys_getloadavg() : [0.0, 0.0, 0.0];
        $cores = (int) shell_exec('sysctl -n hw.ncpu 2>/dev/null') ?: 1;

        $cpuPct = min(100, max(0, ($load[0] / max(1, $cores)) * 100));

        $ramPct = $this->macosRamUsagePercent();

        return response()->json([
            'cpu'   => round($cpuPct, 1),
            'ram'   => round($ramPct, 1),
            'load'  => $load,
            'cores' => $cores,
            'ts'    => now()->toIso8601String(),
        ]);
    }

    public function hermesStatus(HermesClient $hermes): JsonResponse
    {
        return response()->json($hermes->status());
    }

    public function hermesLogs(HermesClient $hermes): JsonResponse
    {
        return response()->json($hermes->logs(40));
    }

    public function hermesGateway(HermesClient $hermes): JsonResponse
    {
        return response()->json($hermes->gatewayState());
    }

    private function macosRamUsagePercent(): float
    {
        $vm = @shell_exec('vm_stat 2>/dev/null');
        if (!$vm) {
            return mt_rand(280, 620) / 10;
        }
        $pageSize = 4096;
        if (preg_match('/page size of (\d+) bytes/', $vm, $m)) {
            $pageSize = (int) $m[1];
        }
        $get = function (string $key) use ($vm): int {
            if (preg_match('/' . preg_quote($key, '/') . ':\s+(\d+)/', $vm, $m)) {
                return (int) $m[1];
            }
            return 0;
        };
        $free        = $get('Pages free');
        $active      = $get('Pages active');
        $inactive    = $get('Pages inactive');
        $speculative = $get('Pages speculative');
        $wired       = $get('Pages wired down');
        $compressed  = $get('Pages occupied by compressor');

        $total = $free + $active + $inactive + $speculative + $wired + $compressed;
        if ($total === 0) {
            return 0.0;
        }
        $used = $active + $wired + $compressed;
        return ($used / $total) * 100;
    }
}
