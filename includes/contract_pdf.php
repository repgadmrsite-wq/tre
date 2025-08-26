<?php
function output_contract_pdf(array $data): void {
    $font = __DIR__ . '/../fonts/DejaVuSans.ttf';
    $lines = [
        'قرارداد اجاره موتور',
        'نام مشتری: ' . $data['user_name'],
        'مدل موتور: ' . $data['model'] . ' پلاک ' . $data['plate'],
        'تاریخ شروع: ' . $data['start_date'],
        'تاریخ پایان: ' . $data['end_date'],
        'مبلغ کل: ' . number_format($data['amount']) . ' تومان',
        '',
        'شرایط اجاره:',
        '1- اجاره‌کننده موظف است موتور را سالم در زمان مقرر بازگرداند.',
        '2- هرگونه خسارت وارده بر عهده مشتری خواهد بود.',
        '3- استفاده از کلاه ایمنی در طول مدت اجاره الزامی است.'
    ];
    $w = 595; $h = 842; // A4 at 72dpi
    $im = imagecreatetruecolor($w, $h);
    $white = imagecolorallocate($im, 255, 255, 255);
    $black = imagecolorallocate($im, 0, 0, 0);
    imagefill($im, 0, 0, $white);
    $y = 60;
    foreach ($lines as $line) {
        $line = rtl($line);
        imagettftext($im, 14, 0, $w-40, $y, $black, $font, $line);
        $y += 24;
    }
    ob_start();
    imagepng($im);
    $png = ob_get_clean();
    imagedestroy($im);
    $pdf = "%PDF-1.3\n";
    $objs = [];
    $objs[] = "1 0 obj<< /Type /Catalog /Pages 2 0 R>>endobj\n";
    $objs[] = "2 0 obj<< /Type /Pages /Kids [3 0 R] /Count 1>>endobj\n";
    $objs[] = "3 0 obj<< /Type /Page /Parent 2 0 R /MediaBox [0 0 $w $h] /Contents 4 0 R /Resources << /XObject << /I1 5 0 R >> >> >>endobj\n";
    $content = "q\n$w 0 0 $h 0 0 cm\n/I1 Do\nQ";
    $objs[] = "4 0 obj<< /Length " . strlen($content) . " >>stream\n$content\nendstream\nendobj\n";
    $objs[] = "5 0 obj<< /Type /XObject /Subtype /Image /Width $w /Height $h /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /FlateDecode /Length " . strlen($png) . " >>stream\n$png\nendstream\nendobj\n";
    $offsets = [];
    $off = strlen($pdf);
    foreach ($objs as $obj) { $offsets[] = $off; $pdf .= $obj; $off += strlen($obj); }
    $xref = $off;
    $pdf .= "xref\n0 " . (count($objs) + 1) . "\n0000000000 65535 f \n";
    foreach ($offsets as $o) { $pdf .= sprintf("%010d 00000 n \n", $o); }
    $pdf .= "trailer<< /Size " . (count($objs) + 1) . " /Root 1 0 R >>\nstartxref\n" . $xref . "\n%%EOF";
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="contract_' . $data['id'] . '.pdf"');
    echo $pdf;
}
function rtl(string $text): string {
    $chars = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);
    return implode('', array_reverse($chars));
}
?>
