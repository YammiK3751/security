<?php

namespace App\Service\Export;

use App\Entity\Document;
use App\Entity\SecurityApartment;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\PhpWord;

class DocumentBuilder
{
    /**
     * @param Document $document
     * @return PhpWord
     */
    public function build(Document $document)
    {
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(12);

        $properties = $phpWord->getDocInfo();
        $properties->setCreator($document->getOwner());
        $properties->setTitle('Выгрузка');
        $properties->setDescription('Договор');
        $properties->setSubject('Договор');

        $sectionStyle = [
            'orientation' => 'landscape',
            'marginLeft' => 1150,
            'marginRight' => 575,
            'marginTop' => 690,
            'marginBottom' => 700,
        ];

        $cellHLeft = ['align' => 'left'];
        $cellHCentered = ['align' => 'center'];
        $cellVCentered = ['valign' => 'center'];

        $section = $phpWord->addSection($sectionStyle);

        $tableHeader = $section->addTable([
            'borderSize' => 6,
            'borderColor' => 'ffffff'
        ]);

        $tableHeader->addRow();
        $tableHeader->addCell(10000, $cellVCentered)->addText('');
        $tableHeader->addCell(5000, $cellVCentered)->addText(join("\n", ['Утверждаю: ', 'Генеральный директор', 'ОВО «Щит»', '________________ К.Л.Прыкин', '«____» _________________ 20__ г.']), [], $cellHLeft);

        $section->addTextBreak();

        $title = 'Документ № ' . $document->getCode();
        $section->addText(htmlspecialchars_decode(trim(strip_tags($title))), [] ,['align' => 'center']);

        $section->addTextBreak();

        $section->addText(htmlspecialchars_decode(trim(strip_tags('Дата вступления договора в силу: ' . $document->getStartAt()->format('d.m.Y')))), [], $cellHLeft);
        $section->addText(htmlspecialchars_decode(trim(strip_tags('Дата окончания договора: ' . $document->getEndAt()->format('d.m.Y')))), [], $cellHLeft);
        $section->addText(htmlspecialchars_decode(trim(strip_tags('Дата составления договора: ' . $document->getCreatedAt()->format('d.m.Y')))), [], $cellHLeft);

        $section->addTextBreak();

        $styleTable = [
            'borderSize' => 6,
            'borderColor' => '999999',
        ];

        $phpWord->addTableStyle('Colspan Rowspan', $styleTable);
        $table = $section->addTable('Colspan Rowspan');

        $table->addRow();
        $table->addCell(650, $cellVCentered)->addText('№ п/п', [], $cellHCentered);
        $table->addCell(9000, $cellVCentered)->addText('Квартира, адрес', [], $cellHCentered);
        $table->addCell(5750, $cellVCentered)->addText('Сумма компенсации, руб', [], $cellHCentered);

        $apartments = $document->getSecurityApartments();

        if (!empty($apartments)) {
            $currentItem = 1;

            /** @var SecurityApartment $apartment */
            foreach ($apartments as $apartment) {
                $table->addRow();
                $table->addCell(650, $cellVCentered)->addText($currentItem++, [], $cellHCentered);
                $table->addCell(9000, $cellVCentered)->addText($apartment->getApartment()->getAddress(), [], $cellHCentered);
                $table->addCell(5750, $cellVCentered)->addText($apartment->getCompensation(), [], $cellHCentered);
            }
        } else {
            $table->addRow();
            $table->addCell(650, $cellVCentered);
            $table->addCell(9000, $cellVCentered);
            $table->addCell(5750, $cellVCentered);
        }

        $section->addTextBreak();

        $tableFooter = $section->addTable([
            'borderSize' => 6,
            'borderColor' => 'ffffff',
            'layout' => \PhpOffice\PhpWord\Style\Table::LAYOUT_AUTO,
        ]);

        $text = $document->getOwner()->getFullName();
        $this->generateFooterRow($tableFooter, $text, 'Составил: ');

        $text = '______________________________________________';
        $this->generateFooterRow($tableFooter, $text, 'Проверил: ');

        return $phpWord;
    }

    /**
     * @param Table $tableFooter
     * @param $text
     * @param string $title
     */
    protected function generateFooterRow(Table $tableFooter, $text, $title = '')
    {
        $cellHLeft = ['align' => 'left'];
        $cellHCentered = ['align' => 'center'];
        $cellVCentered = ['valign' => 'center'];

        $tableFooter->addRow();
        $tableFooter->addCell(1500, $cellVCentered)->addText($title, [], $cellHLeft);
        $tableFooter->addCell(6000, $cellVCentered)->addText($text, [], $cellHLeft);
        $tableFooter->addCell(4500, $cellVCentered)->addText('_______________ «__»_____20__ г.', [], $cellHLeft);

        $tableFooter->addRow();
        $tableFooter->addCell(1500, $cellVCentered);
        $tableFooter->addCell(6000, $cellVCentered)->addText('должность, Фамилия И.О.', ['size' => 9], $cellHCentered);
        $tableFooter->addCell(4500, $cellVCentered)->addText('             подпись', ['size' => 9], $cellHLeft);
    }
}