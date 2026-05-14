<?php

namespace App\Services;

use Carbon\Carbon;
use Throwable;

class HermesClient
{
    protected function root(): string
    {
        return rtrim((string) config('hermes.data_root'), '/');
    }

    protected function readJson(string $relPath): ?array
    {
        $full = $this->root() . '/' . ltrim($relPath, '/');
        if (!is_readable($full)) {
            return null;
        }
        $raw = @file_get_contents($full);
        if ($raw === false) {
            return null;
        }
        $data = json_decode($raw, true);
        return is_array($data) ? $data : null;
    }

    public function status(): array
    {
        $state = $this->readJson('gateway_state.json');
        if (!$state) {
            return [
                'source'  => null,
                'data'    => ['status' => 'offline', 'reason' => 'gateway_state.json non leggibile'],
            ];
        }

        $updatedAt    = $state['updated_at'] ?? null;
        $staleAfter   = (int) config('hermes.gateway_stale_after');
        $isStale      = false;
        $ageSeconds   = null;

        if ($updatedAt) {
            try {
                $ageSeconds = (int) Carbon::parse($updatedAt)->diffInSeconds(Carbon::now());
                $isStale = $ageSeconds > $staleAfter;
            } catch (Throwable) {
                $isStale = false;
            }
        }

        $pidAlive = isset($state['pid']) ? $this->isPidAlive((int) $state['pid']) : false;

        $platforms = [];
        foreach (($state['platforms'] ?? []) as $name => $p) {
            $platforms[] = [
                'name'  => $name,
                'state' => $p['state'] ?? 'unknown',
                'error' => $p['error_message'] ?? null,
            ];
        }

        // Hermes scrive gateway_state.json solo agli eventi, non periodicamente,
        // quindi lo "stale" è puramente informativo. La verità è il PID + lo state field.
        $isOnline = ($state['gateway_state'] ?? null) === 'running' && $pidAlive;

        return [
            'source' => 'gateway_state.json',
            'data'   => [
                'status'        => $isOnline ? 'online' : 'offline',
                'gateway_state' => $state['gateway_state'] ?? null,
                'pid'           => $state['pid'] ?? null,
                'pid_alive'     => $pidAlive,
                'updated_at'    => $updatedAt,
                'age_seconds'   => $ageSeconds,
                'stale'         => $isStale,
                'active_agents' => $state['active_agents'] ?? 0,
                'platforms'     => $platforms,
            ],
        ];
    }

    public function logs(int $limit = null): array
    {
        $limit = $limit ?: (int) config('hermes.log_tail');
        $path  = $this->root() . '/logs/agent.log';

        if (!is_readable($path)) {
            return ['source' => null, 'lines' => []];
        }

        $lines = $this->tail($path, $limit);
        $parsed = array_map(function (string $raw) {
            $raw = rtrim($raw);
            if ($raw === '') {
                return null;
            }
            // Hermes log format: "YYYY-MM-DD HH:MM:SS LEVEL [logger] message"
            if (preg_match('/^(\d{4}-\d{2}-\d{2}[ T]\d{2}:\d{2}:\d{2})[\.,]?\d*\s+(\w+)\s+(.*)$/', $raw, $m)) {
                return ['ts' => $m[1], 'level' => $m[2], 'message' => trim($m[3])];
            }
            return ['ts' => null, 'level' => null, 'message' => $raw];
        }, $lines);

        return [
            'source' => 'logs/agent.log',
            'lines'  => array_values(array_filter($parsed)),
        ];
    }

    public function gatewayState(): array
    {
        return $this->readJson('gateway_state.json') ?? ['status' => 'offline'];
    }

    private function isPidAlive(int $pid): bool
    {
        if ($pid <= 0) return false;
        // posix_kill(pid, 0) returns true if process exists & we can signal it
        return function_exists('posix_kill') ? @posix_kill($pid, 0) : false;
    }

    /** Read last N lines without loading the whole file. */
    private function tail(string $path, int $lines, int $bufferSize = 4096): array
    {
        $fh = @fopen($path, 'rb');
        if (!$fh) return [];

        try {
            fseek($fh, 0, SEEK_END);
            $pos = ftell($fh);
            $output = '';
            $found = 0;

            while ($pos > 0 && $found <= $lines) {
                $read = min($bufferSize, $pos);
                $pos -= $read;
                fseek($fh, $pos, SEEK_SET);
                $chunk  = fread($fh, $read);
                $output = $chunk . $output;
                $found  = substr_count($output, "\n");
            }
            $all = explode("\n", $output);
            return array_slice($all, -$lines - 1, $lines);
        } finally {
            fclose($fh);
        }
    }
}
