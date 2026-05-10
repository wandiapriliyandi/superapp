<?php

namespace App\Libraries;

/**
 * SimpleXLSXGen - Library untuk membuat file XLSX (Excel) asli tanpa Composer.
 * Sangat ringan dan menghasilkan file biner (.xlsx).
 */
class SimpleXLSXGen
{
    protected $sheets = [];
    protected $template = [
        '_rels/.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>',
        'docProps/app.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties"><TotalTime>0</TotalTime></Properties>',
        'docProps/core.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:dcmitype="http://purl.org/dc/dcmitype/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><dc:creator>SuperApp</dc:creator><cp:lastModifiedBy>SuperApp</cp:lastModifiedBy><dcterms:created xsi:type="dcterms:W3CDTF">{DATE}</dcterms:created><dcterms:modified xsi:type="dcterms:W3CDTF">{DATE}</dcterms:modified></cp:coreProperties>',
        'xl/_rels/workbook.xml.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/></Relationships>',
        'xl/worksheets/sheet1.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>{ROWS}</sheetData></worksheet>',
        'xl/workbook.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheets><sheet name="Sheet1" sheetId="1" r:id="rId1" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"/></sheets></workbook>',
        '[Content_Types].xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/><Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/></Types>'
    ];

    public static function fromArray(array $rows)
    {
        $xlsx = new self();
        $xlsx->sheets = $rows;
        return $xlsx;
    }

    public function __toString()
    {
        $rows_xml = "";
        foreach ($this->sheets as $r => $row) {
            $rows_xml .= '<row r="'.($r+1).'">';
            foreach ($row as $c => $val) {
                $type = is_numeric($val) ? 'n' : 'inlineStr';
                $rows_xml .= '<c t="'.$type.'">';
                if ($type === 'inlineStr') {
                    $rows_xml .= '<is><t>'.htmlspecialchars($val).'</t></is>';
                } else {
                    $rows_xml .= '<v>'.$val.'</v>';
                }
                $rows_xml .= '</c>';
            }
            $rows_xml .= '</row>';
        }

        $zip = new \ZipArchive();
        $temp_file = tempnam(sys_get_temp_dir(), 'xlsx');
        $zip->open($temp_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        foreach ($this->template as $path => $content) {
            $content = str_replace(['{ROWS}', '{DATE}'], [$rows_xml, date('Y-m-d\TH:i:s\Z')], $content);
            $zip->addFromString($path, $content);
        }
        $zip->close();

        $res = file_get_contents($temp_file);
        unlink($temp_file);
        return $res;
    }

    public function downloadAs($filename)
    {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');
        echo $this->__toString();
        exit;
    }
}
