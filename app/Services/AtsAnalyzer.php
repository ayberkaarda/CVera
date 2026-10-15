<?php

namespace App\Services;

final class AtsAnalyzer
{
    private function normalize(string $text): string
    {
        return mb_strtolower(strtr($text, ['İ' => 'i', 'I' => 'i', 'ı' => 'i', 'ş' => 's', 'Ş' => 's', 'ğ' => 'g', 'Ğ' => 'g', 'ü' => 'u', 'Ü' => 'u', 'ö' => 'o', 'Ö' => 'o', 'ç' => 'c', 'Ç' => 'c']), 'UTF-8');
    }

    public function analyze(string $cv, ?string $job = null): array
    {
        $text = $this->normalize($cv);
        $words = preg_split('/\s+/u', trim($cv), -1, PREG_SPLIT_NO_EMPTY);
        $wordCount = count($words);
        $checks = [
            ['label' => 'İletişim bilgileri', 'max' => 15, 'points' => (preg_match('/[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}/i', $cv) ? 8 : 0) + (preg_match('/(?:\+?\d[\s().-]*){10,13}/', $cv) ? 7 : 0), 'tip' => 'Güncel e-posta adresini ve telefon numaranı metin olarak ekle.'],
            ['label' => 'Deneyim bölümü', 'max' => 15, 'points' => preg_match('/deneyim|experience|staj|internship|projeler|projects/u', $text) ? 15 : 0, 'tip' => '“Deneyim” veya “Projeler” başlığı altında görevlerini ve tarihlerini belirt.'],
            ['label' => 'Eğitim bilgileri', 'max' => 10, 'points' => preg_match('/egitim|education|universite|university|lisans|bachelor/u', $text) ? 10 : 0, 'tip' => '“Eğitim” başlığı altında okul, bölüm ve mezuniyet tarihini ekle.'],
            ['label' => 'Beceriler bölümü', 'max' => 15, 'points' => preg_match('/beceri|yetenek|skills|yetkinlik|teknolojiler|technologies/u', $text) ? 15 : 0, 'tip' => 'Gerçekten kullandığın araç ve teknolojileri “Beceriler” başlığı altında açıkça listele.'],
            ['label' => 'Profesyonel özet', 'max' => 10, 'points' => preg_match('/ozet|summary|profil|profile|hakkimda|objective/u', $text) ? 10 : 0, 'tip' => 'Uzmanlığını ve hedef rolünü anlatan kısa bir “Profesyonel Özet” ekle.'],
            ['label' => 'Ölçülebilir başarılar', 'max' => 15, 'points' => min(15, preg_match_all('/(?:%\s*\d+|\d+\s*%|\d+\s*(?:kisi|kullanici|musteri|proje|users|customers|projects|yuzde|percent))/u', $text) * 5), 'tip' => 'Başarılarını gerçek sayılarla anlat: geliştirdiğin süreç, sağladığın tasarruf veya ulaştığın kullanıcı sayısı gibi.'],
            ['label' => 'İçerik kapsamı', 'max' => 10, 'points' => $wordCount >= 150 && $wordCount <= 1200 ? 10 : ($wordCount >= 70 && $wordCount <= 1600 ? 5 : 0), 'tip' => $wordCount < 150 ? 'CV kısa görünüyor. Görev, proje ve sonuçlarını daha açık anlat. 150–1200 kelime aralığı hedeflenebilir.' : 'İçeriği sadeleştir; tekrarları çıkar ve hedef rolle ilgili deneyimlere odaklan.'],
            ['label' => 'Eylem odaklı anlatım', 'max' => 10, 'points' => min(10, preg_match_all('/gelistir|yonet|tasarla|iyilestir|olustur|analiz|artir|azalt|build|built|develop|led|managed|improv|design|deliver|implement/u', $text) * 2), 'tip' => '“Geliştirdim”, “yönettim”, “iyileştirdim” gibi somut eylemler kullan. Sonuçlarını kendi gerçek verilerinle destekle.'],
        ];
        $recommendations = [];
        foreach ($checks as $check) {
            if ($check['points'] < $check['max']) {
                $recommendations[] = ['title' => $check['label'], 'description' => $check['tip'], 'potential' => $check['max'] - $check['points']];
            }
        }
        $baseScore = array_sum(array_column($checks, 'points'));
        $matched = $missing = [];
        $matchScore = null;
        if ($job !== null && trim($job) !== '') {
            $jobText = $this->normalize($job);
            $dictionary = ['PHP' => ['php'], 'Laravel' => ['laravel'], 'JavaScript' => ['javascript', ' java script'], 'TypeScript' => ['typescript'], 'React' => ['react'], 'Vue' => ['vue'], 'Angular' => ['angular'], 'Node.js' => ['node.js', 'nodejs'], 'Python' => ['python'], 'Java' => ['java'], 'C#' => ['c#', 'csharp'], 'C++' => ['c++'], '.NET' => ['.net', 'dotnet'], 'SQL' => ['sql'], 'MySQL' => ['mysql'], 'PostgreSQL' => ['postgresql', 'postgres'], 'MongoDB' => ['mongodb'], 'Redis' => ['redis'], 'Docker' => ['docker'], 'Kubernetes' => ['kubernetes', 'k8s'], 'AWS' => ['aws', 'amazon web services'], 'Azure' => ['azure'], 'Git' => ['git'], 'REST API' => ['rest api', 'restful'], 'GraphQL' => ['graphql'], 'HTML' => ['html'], 'CSS' => ['css'], 'Tailwind' => ['tailwind'], 'Figma' => ['figma'], 'UX' => ['ux', 'user experience', 'kullanici deneyimi'], 'UI' => ['ui', 'user interface', 'arayuz tasarimi'], 'Excel' => ['excel'], 'Power BI' => ['power bi', 'powerbi'], 'Tableau' => ['tableau'], 'SAP' => ['sap'], 'ERP' => ['erp'], 'CRM' => ['crm'], 'SEO' => ['seo'], 'Google Analytics' => ['google analytics', 'ga4'], 'Google Ads' => ['google ads', 'adwords'], 'Meta Ads' => ['meta ads', 'facebook ads'], 'Dijital pazarlama' => ['dijital pazarlama', 'digital marketing'], 'Sosyal medya' => ['sosyal medya', 'social media'], 'İçerik üretimi' => ['icerik uretimi', 'content creation'], 'Satış' => ['satis', 'sales'], 'Muhasebe' => ['muhasebe', 'accounting'], 'Finans' => ['finans', 'finance'], 'Bütçe yönetimi' => ['butce', 'budget'], 'Proje yönetimi' => ['proje yonetimi', 'project management'], 'Agile' => ['agile'], 'Scrum' => ['scrum'], 'Jira' => ['jira'], 'Veri analizi' => ['veri analizi', 'data analysis', 'data analytics'], 'Makine öğrenmesi' => ['makine ogrenmesi', 'machine learning'], 'İngilizce' => ['ingilizce', 'english'], 'Almanca' => ['almanca', 'german'], 'İletişim' => ['iletisim beceri', 'communication'], 'Ekip çalışması' => ['ekip calismasi', 'takim calismasi', 'teamwork', 'team player'], 'Problem çözme' => ['problem cozme', 'problem solving'], 'Liderlik' => ['liderlik', 'leadership'], 'Müşteri ilişkileri' => ['musteri iliskileri', 'customer relations'], 'Raporlama' => ['raporlama', 'reporting'], 'Tedarik zinciri' => ['tedarik zinciri', 'supply chain'], 'Lojistik' => ['lojistik', 'logistics'], 'AutoCAD' => ['autocad'], 'SolidWorks' => ['solidworks'], 'Kalite kontrol' => ['kalite kontrol', 'quality control'], 'İnsan kaynakları' => ['insan kaynaklari', 'human resources'], 'İşe alım' => ['ise alim', 'recruitment']];
            $contains = function (string $haystack, array $aliases): bool {
                foreach ($aliases as $alias) {
                    if (preg_match('/(?<![a-z0-9])'.preg_quote($alias, '/').'(?![a-z0-9])/u', $haystack)) {
                        return true;
                    }
                }

                return false;
            };
            foreach ($dictionary as $keyword => $aliases) {
                if ($contains($jobText, $aliases)) {
                    if ($contains($text, $aliases)) {
                        $matched[] = $keyword;
                    } else {
                        $missing[] = $keyword;
                    }
                }
            }
            $total = count($matched) + count($missing);
            if ($total > 0) {
                $matchScore = (int) round(count($matched) / $total * 100);
                $score = (int) round($baseScore * .65 + $matchScore * .35);
                if ($missing) {
                    $recommendations[] = ['title' => 'İlanla ilgili eksik ifadeler', 'description' => 'CV’nde bulunamayan ifadeler: '.implode(', ', $missing).'. Yalnızca gerçekten sahip olduğun becerileri ilgili deneyimin içinde örnekleriyle belirt.', 'potential' => null];
                }
            } else {
                $score = $baseScore;
                $recommendations[] = ['title' => 'İlanı ayrıntılı incele', 'description' => 'Bu ilanda desteklenen beceri sözlüğüyle eşleşen ifade bulunamadı. Puan yalnızca genel CV ölçütlerine dayanır; ilan uyumu hesaplanamadı. Gereksinimleri kendin kontrol et.', 'potential' => null];
            }
        } else {
            $score = $baseScore;
        }

        return ['score' => $score, 'baseScore' => $baseScore, 'matchScore' => $matchScore, 'wordCount' => $wordCount, 'checks' => array_map(fn ($c) => ['label' => $c['label'], 'points' => $c['points'], 'max' => $c['max']], $checks), 'recommendations' => $recommendations, 'matched' => $matched, 'missing' => $missing, 'method' => 'rule-based-v1', 'note' => 'Bu puan metin tabanlı kurallarla hesaplanan bir rehberdir. Görsel düzen, tablo, sütun ve dosyanın gerçek ATS tarafından ayrıştırılabilirliği ölçülmez. İşveren sistemlerinin puanı değildir.'];
    }
}
