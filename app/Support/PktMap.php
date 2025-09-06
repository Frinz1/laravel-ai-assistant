<?php
namespace App\Support;

final class PktMap {
    public static function pathFor(?string $canonicalTopic): ?string {
        $map = [
            'virus'           => 'malware/virus_simulation.pkt',
            'trojan'          => 'malware/trojan_simulation.pkt',
            'worms'           => 'malware/worms_simulation.pkt',
            'ransomware'      => 'malware/ransomware_simulation.pkt',
            'spyware'         => 'malware/spyware_simulation.pkt',
            'adware'          => 'malware/adware_simulation.pkt',
            'keylogger'       => 'malware/keylogger_simulation.pkt',
            'email phishing'  => 'phishing/email_phishing_simulation.pkt',
            'smishing'        => 'phishing/smishing_simulation.pkt',
            'clone phishing'  => 'phishing/clone_phishing_simulation.pkt',
            'spear phishing'  => 'phishing/spear_phishing_simulation.pkt',
        ];
        return $canonicalTopic && isset($map[$canonicalTopic]) ? $map[$canonicalTopic] : null;
    }
}