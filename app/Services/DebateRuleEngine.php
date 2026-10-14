<?php

namespace App\Services;

use InvalidArgumentException;

class DebateRuleEngine
{
    /**
     * Role configurations for British Parliamentary.
     */
    public const BP_ROLES = [
        'PM' => [
            'name' => 'Prime Minister',
            'team' => 'Opening Government (OG)',
            'side' => 'Government',
            'order' => 1,
            'brief' => 'Tetapkan konteks masalah, model/mekanisme kebijakan, beban pembuktian (burden of proof), dan 2 argumen konstruktif utama.',
        ],
        'LO' => [
            'name' => 'Leader of Opposition',
            'team' => 'Opening Opposition (OO)',
            'side' => 'Opposition',
            'order' => 2,
            'brief' => 'Berikan direct clash langsung ke premis pembuka pemerintah, usulkan counter-model atau status quo yang lebih baik, dan bawa argumen oposisi.',
        ],
        'DPM' => [
            'name' => 'Deputy Prime Minister',
            'team' => 'Opening Government (OG)',
            'side' => 'Government',
            'order' => 3,
            'brief' => 'Pertahankan argumen PM dari sanggahan LO, bangun kembali kausalitas, dan perdalam dampak kebijakan tim pemerintah.',
        ],
        'DLO' => [
            'name' => 'Deputy Leader of Opposition',
            'team' => 'Opening Opposition (OO)',
            'side' => 'Opposition',
            'order' => 4,
            'brief' => 'Pertahankan kasus OO, hancurkan bantahan DPM, dan kunci dominasi meja pembuka oposisi.',
        ],
        'MG' => [
            'name' => 'Member of Government',
            'team' => 'Closing Government (CG)',
            'side' => 'Government',
            'order' => 5,
            'brief' => 'Bawa ekstensi (extension) baru yang substansial: sudut pandang filosofis, aktor baru, atau mekanisme alternatif yang membedakan CG dari OG.',
        ],
        'MO' => [
            'name' => 'Member of Opposition',
            'team' => 'Closing Opposition (CO)',
            'side' => 'Opposition',
            'order' => 6,
            'brief' => 'Bawa ekstensi oposisi baru, serang ekstensi CG, dan berikan alasan unik mengapa CO harus menang atas seluruh ruangan.',
        ],
        'GW' => [
            'name' => 'Government Whip',
            'team' => 'Closing Government (CG)',
            'side' => 'Government',
            'order' => 7,
            'brief' => 'Petakan clash utama debat, timbang dampak (comparative weighing), jelaskan kemenangan CG atas 3 tim lain. DILARANG membawa argumen baru.',
        ],
        'OW' => [
            'name' => 'Opposition Whip',
            'team' => 'Closing Opposition (CO)',
            'side' => 'Opposition',
            'order' => 8,
            'brief' => 'Pidato penutup seluruh debat! Pertahankan ekstensi MO, simpulkan kontribusi CO, dan tunjukkan mengapa oposisi memenangkan dunia debat.',
        ],
    ];

    /**
     * Role configurations for Asian Parliamentary.
     */
    public const AP_ROLES = [
        'PM' => [
            'name' => 'Prime Minister',
            'team' => 'Government Team',
            'side' => 'Government',
            'order' => 1,
            'brief' => 'Definisikan mosi, tentukan parameter perdebatan, garis besar kasus tim, dan bawakan 2 poin substantif pertama.',
        ],
        'LO' => [
            'name' => 'Leader of Opposition',
            'team' => 'Opposition Team',
            'side' => 'Opposition',
            'order' => 2,
            'brief' => 'Tanggapi kasus pemerintah, klarifikasi sikap oposisi, dan sampaikan 2 poin substantif pertama pihak oposisi.',
        ],
        'DPM' => [
            'name' => 'Deputy Prime Minister',
            'team' => 'Government Team',
            'side' => 'Government',
            'order' => 3,
            'brief' => 'Bantah serangan LO, bangun kembali argumen PM, dan sampaikan poin substantif terakhir pemerintah.',
        ],
        'DLO' => [
            'name' => 'Deputy Leader of Opposition',
            'team' => 'Opposition Team',
            'side' => 'Opposition',
            'order' => 4,
            'brief' => 'Bantah argumen DPM, perkuat kasus oposisi, dan sampaikan poin substantif terakhir pihak oposisi.',
        ],
        'GW' => [
            'name' => 'Government Whip',
            'team' => 'Government Team',
            'side' => 'Government',
            'order' => 5,
            'brief' => 'Bantah kasus menyeluruh oposisi, ringkas poin pertarungan utama, dan pertahankan integritas kasus pemerintah.',
        ],
        'OW' => [
            'name' => 'Opposition Whip',
            'team' => 'Opposition Team',
            'side' => 'Opposition',
            'order' => 6,
            'brief' => 'Bantah seluruh kasus pemerintah, ringkas titik benturan, dan tegaskan keunggulan posisi oposisi.',
        ],
        'OR' => [
            'name' => 'Opposition Reply',
            'team' => 'Opposition Team',
            'side' => 'Opposition',
            'order' => 7,
            'is_reply' => true,
            'brief' => 'Pidato evaluasi 4 menit dari sudut pandang juri yang objektif-kritis mengenai mengapa oposisi memenangkan perdebatan.',
        ],
        'GR' => [
            'name' => 'Government Reply',
            'team' => 'Government Team',
            'side' => 'Government',
            'order' => 8,
            'is_reply' => true,
            'brief' => 'Pidato penutup resmi 4 menit yang merangkum dua pertanyaan kunci debat dan membuktikan kemenangan pemerintah.',
        ],
    ];

    /**
     * Check whether a role is valid for the given format.
     */
    public function isValidRole(string $format, string $role): bool
    {
        return match (strtoupper($format)) {
            'BP' => array_key_exists($role, self::BP_ROLES),
            'AP' => array_key_exists($role, self::AP_ROLES),
            default => false,
        };
    }

    /**
     * Get available roles metadata for format.
     *
     * @return array<string, array<string, mixed>>
     */
    public function getRoles(string $format): array
    {
        return match (strtoupper($format)) {
            'BP' => self::BP_ROLES,
            'AP' => self::AP_ROLES,
            default => throw new InvalidArgumentException("Format debat tidak didukung: {$format}"),
        };
    }

    /**
     * Calculate timing and POI rules for speech.
     *
     * @return array{
     *     duration_seconds: int,
     *     poi_allowed: bool,
     *     poi_start_second: int,
     *     poi_end_second: int,
     *     role_name: string,
     *     team: string,
     *     side: string,
     *     brief: string
     * }
     */
    public function calculateSpeechRules(string $format, string $role): array
    {
        $roles = $this->getRoles($format);

        if (! array_key_exists($role, $roles)) {
            throw new InvalidArgumentException("Posisi {$role} tidak valid untuk format {$format}.");
        }

        $meta = $roles[$role];
        $isReply = ! empty($meta['is_reply']);

        return [
            'duration_seconds' => $isReply ? 240 : 420,
            'poi_allowed' => ! $isReply,
            'poi_start_second' => $isReply ? 0 : 60,
            'poi_end_second' => $isReply ? 0 : 360,
            'role_name' => $meta['name'],
            'team' => $meta['team'],
            'side' => $meta['side'],
            'brief' => $meta['brief'],
        ];
    }
}
