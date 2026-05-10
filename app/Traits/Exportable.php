<?php

namespace App\Traits;

use App\Libraries\SimpleXLSXGen;

trait Exportable
{
    /**
     * Export data ke Excel (.xlsx)
     */
    protected function exportToExcel($data, $filename = 'export-data')
    {
        ob_end_clean();
        $xlsx = SimpleXLSXGen::fromArray($data);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        
        $xlsx->downloadAs($filename . '.xlsx');
        exit();
    }

    /**
     * Export data ke Word (.doc)
     */
    protected function exportToWord($data, $title = 'Data Export', $filename = 'export-data')
    {
        header("Content-type: application/vnd.ms-word");
        header("Content-Disposition: attachment; filename=" . $filename . ".doc");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo "<html>";
        echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">";
        echo "<body>";
        echo "<h2 style='text-align:center'>$title</h2>";
        echo "<table border='1' style='width:100%; border-collapse:collapse;'>";
        
        // Header
        if (!empty($data)) {
            echo "<tr>";
            foreach (array_keys($data[0]) as $header) {
                echo "<th style='background:#f4f4f4; padding:5px;'>" . ucwords(str_replace('_', ' ', $header)) . "</th>";
            }
            echo "</tr>";
            
            // Body
            foreach ($data as $row) {
                echo "<tr>";
                foreach ($row as $cell) {
                    echo "<td style='padding:5px;'>$cell</td>";
                }
                echo "</tr>";
            }
        }
        
        echo "</table>";
        echo "</body>";
        echo "</html>";
        exit();
    }

    /**
     * Export data ke PDF (Print Friendly HTML)
     */
    protected function exportToPdf($data, $title = 'Data Export')
    {
        // Untuk PDF sederhana kita gunakan view HTML khusus cetak (Print Friendly)
        return view('layout/print_template', [
            'title' => $title,
            'data'  => $data
        ]);
    }
}
