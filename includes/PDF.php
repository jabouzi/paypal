<?php

require_once 'tcpdf/tcpdf.php';
require_once 'fpdi/fpdi.php' ;
require_once 'Date.php';

class PDF extends FPDI {
    
    private $language = '';
    private $guid = '';
    private $amount = 0;
    private $printedDate = null;
    
    private $fontFamily = array();
    private $textColor = array();
    private $moneyFormat = '';


    public function __construct($language, $amount, $guid, $printedDate = null)
    {
        
        $this->language = $language;
        $this->guid = $guid;
        $this->amount = $amount;
        $this->printedDate = SimpleDate::display(is_null($printedDate) ? time() : $printedDate, SimpleDate::LONG_FORMAT, $language, false);
        parent::__construct('P', 'pt', array(288, 540), true, 'UTF-8', false);
        
        $this->setLanguage();
        $this->init();
    }

    public function setDocumentInformation(array $information) {

        if (!isset($information['creator'])) {
            $this->SetCreator(PDF_CREATOR);
        } else {
            $this->SetCreator($information['creator']);
        }

        if (isset($information['author'])) {
            $this->SetAuthor($information['author']);
        }

        if (isset($information['title'])) {
            $this->SetTitle($information['title']);
        }

        if (isset($information['subject'])) {
            $this->SetSubject($information['subject']);
        }

        if (isset($information['keywords'])) {
            $this->SetKeywords($information['keywords']);
        }
    }

    public function distiller()
    {
        global $lang;

        $this->AddPage();
        $path = ROOT . 'templates' . '/' . $lang  . '/certificate.pdf';

        if (file_exists($path)) {
            $this->setSourceFile($path);
            $this->useTemplate($this->importPage(1));
        }
        
        $this->SetTextColorArray($this->textColor);
        
        $this->applyFont('regular', 12);
        $this->SetY(15);
        $this->Cell(0, 0,  $this->guid, 0, 0);
        
        if ($this->language == 'fr') {
            $this->applyFont('medium', 44);
            $this->SetY(108);
            $this->Cell(0, 0,$this->amount , 0, 0);
            
            $offset = $this->GetStringWidth($this->amount);
            
            $this->applyFont('medium', 20);
            $this->SetY(115);
            $this->SetX($this->GetX() + $offset);
            $this->Cell(0, 0, '$', 0, 0);
        } else {
            $this->applyFont('medium', 20);
            $this->SetY(113);
            $this->Cell(0, 0, '$', 0, 0);
            
            $this->applyFont('medium', 44);
            $this->SetY(105);
            $this->SetX(40);
            $this->Cell(0, 0,$this->amount , 0, 0);
        }

        $this->applyFont('regular', 7.5);
        $this->SetTextColor(188,177,166);
        $this->SetY(390);
        $this->Cell(0, 0, mb_strtoupper($this->printedDate, 'UTF-8'), 0, 0);
    }

    public function applyFont($type, $size) {
        if (array_key_exists($type, $this->fontFamily)) {
            $this->SetFont($this->fontFamily[$type]['name'], $this->fontFamily[$type]['style'], $size);
        } else {
            $this->SetFont($this->fontFamily['default']['name'], $this->fontFamily['default']['style'], $size);
        }
    }

    public function Header() {
    }

    public function Footer() {
        
    }

    private function init()
    {
        $this->SetAutoPageBreak(false);
        $this->SetMargins(25, 0, 0);
        $this->SetHeaderMargin(0);
        $this->SetFooterMargin(0);
        $this->setPrintHeader(false);
        $this->setPrintFooter(false);
        $this->setFontFamily();
        $this->setDocumentInformation(array('author' => 'SkySpa',
                                           'title' => 'Gift certificate'));
        
        if ($this->language == 'fr') {
            $this->textColor = array('R' => 255, 'G' => 255, 'B' => 255);
            $this->moneyFormat = "%s$";
        } else {
            $this->textColor = array('R' => 255, 'G' => 255, 'B' => 255);
            $this->moneyFormat = "$%s";
        }
    }

    private function setLanguage() {

        $l = array();
        $l['a_meta_charset'] = 'UTF-8';
        $l['a_meta_dir'] = 'ltr';
        $l['a_meta_language'] = $this->language;
        $l['w_page']          = 'page';

        parent::setLanguageArray($l);
    }

    private function setFontFamily() {
        $this->fontFamily['regular']    = array('name' => 'dinmittelschriftstd',
                                             'style' => '');
        $this->fontFamily['medium']    = array('name' => 'din-medium',
                                             'style' => '');
        $this->fontFamily['default'] = array('name' => 'times',
                                             'style' => '');
    }

}

