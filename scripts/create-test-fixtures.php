<?php

$root = dirname(__DIR__);
@mkdir($root.'/e2e/fixtures', 0777, true);
$zip = new ZipArchive;
$zip->open($root.'/e2e/fixtures/cv.docx', ZipArchive::CREATE | ZipArchive::OVERWRITE);
$zip->addFromString('[Content_Types].xml', '<?xml version="1.0"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/></Types>');
$zip->addFromString('_rels/.rels', '<?xml version="1.0"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/></Relationships>');
$text = 'Deniz Yılmaz deniz@example.com +90 555 123 45 67. Profesyonel Özet: React geliştirici. Deneyim: 12 proje geliştirdim, hızı %35 artırdım. Eğitim: Bilgisayar Mühendisliği Lisans. Beceriler: React, SQL, Git.';
$zip->addFromString('word/document.xml', '<?xml version="1.0" encoding="UTF-8"?><w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"><w:body><w:p><w:r><w:t>'.htmlspecialchars($text, ENT_XML1, 'UTF-8').'</w:t></w:r></w:p></w:body></w:document>');
$zip->close();
echo "DOCX test fixture created.\n";
