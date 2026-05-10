<?php

namespace App\Libraries;

class ExportService
{
    /**
     * Export ke format XLSX (Biner Asli)
     */
    public function excel_xlsx($filename, $headers, $data)
    {
        // Bersihkan buffer sebelum output biner (Sesuai permintaan)
        if (ob_get_level() > 0) ob_end_clean();

        $rows = array_merge([$headers], $data);
        $xlsx = \App\Libraries\SimpleXLSXGen::fromArray($rows);
        $xlsx->downloadAs($filename);
    }

    public function word_content($title, $content_html)
    {
        $html = "<html>";
        $html .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">";
        $html .= "<style>
            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
            .header-title { text-align: center; color: #4e73df; font-size: 24pt; font-weight: bold; margin-bottom: 5pt; }
            .subtitle { text-align: center; font-size: 10pt; color: #666; margin-bottom: 20pt; border-bottom: 2pt solid #4e73df; padding-bottom: 10pt; }
            table { width: 100%; border-collapse: collapse; margin-top: 10pt; }
            th { background-color: #f8f9fc; color: #4e73df; font-weight: bold; padding: 8pt; border: 1pt solid #e3e6f0; text-align: left; }
            td { padding: 8pt; border: 1pt solid #e3e6f0; font-size: 10pt; }
        </style>";
        $html .= "<body>";
        $html .= "<div class='header-title'>$title</div>";
        $html .= "<div class='subtitle'>Laporan Sistem SuperApp - Dicetak pada ".date('d M Y H:i')."</div>";
        $html .= $content_html;
        $html .= "</body>";
        $html .= "</html>";
        
        return $html;
    }

    public function pdf_print()
    {
        return "<script>window.onload = function() { window.print(); }</script>";
    }
}
