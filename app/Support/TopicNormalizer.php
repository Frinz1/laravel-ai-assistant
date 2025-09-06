<?php
namespace App\Support;

final class TopicNormalizer {
    private const CANON = [
        'virus' => 'virus',
        'trojan' => 'trojan',
        'worm' => 'worms',
        'worms' => 'worms',
        'adware' => 'adware',
        'keylogger' => 'keylogger',
        'spyware' => 'spyware',
        'ransomware' => 'ransomware',
        'email phishing' => 'email phishing',
        'email-phishing' => 'email phishing',
        'spear phishing' => 'spear phishing',
        'spear-phishing' => 'spear phishing',
        'smishing' => 'smishing',
        'clone phishing' => 'clone phishing',
        'clone-phishing' => 'clone phishing',
    ];

    public static function toCanonical(?string $t): ?string {
        if (!$t) return null;
        $k = strtolower(trim($t));
        $k = str_replace('_', '-', $k);
        $k2 = str_replace('-', ' ', $k);
        return self::CANON[$k] ?? self::CANON[$k2] ?? null;
    }
}