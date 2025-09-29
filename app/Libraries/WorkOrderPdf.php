<?php

namespace App\Libraries;

require_once app_path('Libraries/fpdf/fpdf.php');


use FPDF;

class WorkOrderPdf extends FPDF
{
    protected $workOrder;
    protected $data;

    public function __construct($workOrder, $data)
    {
        parent::__construct();
        $this->workOrder = $workOrder;
        $this->data = $data;
    }

    // Header
    function Header()
    {
        $this->SetFont('Arial','B',12);
        $this->Cell(95,15,'WORK ORDER FORM',1,0,'L');
        // Buat Cell kosong dulu (border aja)
        $this->Cell(25, 15, '', 1, 0, 'L');

        // Ambil posisi cell yang baru dibuat
        $x = $this->GetX() - 25; // posisi awal cell (karena GetX() sudah maju setelah Cell)
        $y = $this->GetY();

        // Hitung supaya logo berada di tengah cell
        $cellWidth = 25;
        $cellHeight = 15;
        $imgWidth = 20;
        $imgHeight = 10;

        $imgX = $x + ($cellWidth - $imgWidth) / 2;
        $imgY = $y + ($cellHeight - $imgHeight) / 2;

        // Taruh gambar
        $this->Image(public_path('images/CITASys Logo.jpg'), $imgX, $imgY, $imgWidth, $imgHeight);
        $this->Cell(10,15,'NO',1,0,'C');
        $this->Cell(30,15,'WO-' . $this->workOrder->id,1,0,'L');
        $this->MultiCell(30, 5, "FRM-ENG-03\nRev. 01\n22-04-2013", 1, 'L');
    }

    // Footer
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
    }

    public function build()
    {
        $this->AddPage();
        $this->SetFont('Arial','',10);

        $this->SetFont('Arial','',10);

        // Bagian atas tabel
        $this->Cell(25,6,'Issued Date',1,0);
        $this->Cell(70,6,'',1,0);
        $this->Cell(25,6,'Project No',1,0);
        $this->Cell(70,6,'',1,1);

        $this->Cell(25,6,'Client',1,0);
        $this->Cell(70,6,'',1,0);
        $this->Cell(25,6,'Project Name',1,0);
        $this->Cell(70,6,'',1,1);

        $this->Cell(25,6,'Location',1,0);
        $this->Cell(70,6,'',1,0);
        $this->Cell(25,6,'Purpose',1,0);
        $this->Cell(70,6,'Meeting / Survey / Install / Test / Other',1,1);

        $this->Cell(25,6,'Vehicle No',1,0);
        $this->Cell(25,6,'',1,0);
        $this->Cell(15,6,'Driver',1,0);
        $this->Cell(30,6,'',1,0);
        $this->Cell(25,6,'No of Person',1,0);
        $this->Cell(70,6,'',1,1);

        // Nama & posisi
        $this->SetFont('Arial','B',10);
        $this->Cell(10,5,'No',1,0,'C');
        $this->Cell(43,5,'Name',1,0,'C');
        $this->Cell(42,5,'Position',1,0,'C');
        $this->Cell(10,5,'No',1,0,'C');
        $this->Cell(43,5,'Name',1,0,'C');
        $this->Cell(42,5,'Position',1,1,'C');

        for ($i=0; $i<5; $i++) {
            $this->Cell(10,6,'',1,0,'C');
            $this->Cell(43,6,'',1,0,'C');
            $this->Cell(42,6,'',1,0,'C');
            $this->Cell(10,6,'',1,0,'C');
            $this->Cell(43,6,'',1,0,'C');
            $this->Cell(42,6,'',1,1,'C');
        }

        // Work description & result
        $this->SetFont('Arial','B',10);
        $this->Cell(10,8,'No.',1,0,'C');
        $this->Cell(90,8,'Work Description',1,0,'C');
        $this->Cell(90,8,'Result',1,1,'C');

        $this->SetFont('Arial','',10);

        // tinggi baris tetap
        $lineHeight = 5;
        $rowHeight  = 22;
        $colNo = 10;
        $colDesc = 90;
        $colResult = 90;

        foreach ($this->data as $i => $row) {
            $x = $this->GetX();
            $y = $this->GetY();

            // Gambar border luar
            $this->Cell($colNo, $rowHeight, '', 1, 0);
            $this->Cell($colDesc, $rowHeight, '', 1, 0);
            $this->Cell($colResult, $rowHeight, '', 1, 1);

            // Isi teks tanpa border
            $this->SetXY($x, $y);
            $this->MultiCell($colNo, $lineHeight, ($i+1).".", 0, 'C');

            $this->SetXY($x + $colNo, $y);
            $this->MultiCell($colDesc, $lineHeight, $row['desc'], 0, 'L');

            $this->SetXY($x + $colNo + $colDesc, $y);
            $this->MultiCell($colResult, $lineHeight, $row['result'], 0, 'L');

            // pindah ke baris berikutnya
            $this->SetXY($x, $y + $rowHeight);
        }

        $this->Cell(35,5,'Start Work Time',1,0,'C');
        $this->Cell(65,5,' ',1,0,'C');
        $this->Cell(20,10,'Continue on',1,0,'C');
        $this->Cell(10,5,'Date',1,0,'C');
        $this->Cell(60,5,' ',1,1,'C');

        $this->Cell(35,5,'Stop Work Time',1,0,'C');
        $this->Cell(65,5,' ',1,0,'C');
        $this->Cell(20,5,'',0,0,'C');
        $this->Cell(10,5,'Time',1,0,'C');
        $this->Cell(60,5,' ',1,1,'C');

        $this->Cell(0, 15, "Client Note:", 1, 'L');


        // Signature
        $this->Cell(49,5,'Requested by',1,0,'C');
        $this->Cell(47,5,'Approved by',1,0,'C');
        $this->Cell(47,5,'Accepted by',1,0,'C');
        $this->Cell(47,5,'Client',1,1,'C');

        $this->Cell(49,15,'',1,0);
        $this->Cell(47,15,'',1,0);
        $this->Cell(47,15,'',1,0);
        $this->Cell(47,15,'',1,1);

        $this->Cell(10,5,'Dept',1,0,'C');
        $this->Cell(39,5,'',1,0,'C');
        $this->Cell(47,5,'',1,0,'C');
        $this->Cell(47,5,'',1,0,'C');
        $this->Cell(47,5,'',1,1,'C');

        $this->SetFont('Arial','B',12);
        $this->Cell(55,5,'OVERNIGHT WORK / JOB',1,0,'L');
        $this->Cell(135,5,'[ ] Yes   [ ] No',1,1,'L');

        $this->Cell(30,10,'Scheduled',1,0,'C');
        $this->Cell(23,5,'Start Date ',1,0,'C');
        $this->Cell(43,5,'',1,0,'C');
        $this->Cell(30,10,'Actual',1,0,'C');
        $this->Cell(23,5,'Start Date ',1,0,'C');
        $this->Cell(41,5,' ',1,1,'C');


        $this->Cell(30,10,'',0,0,'C');
        $this->Cell(23,5,'End Date ',1,0,'C');
        $this->Cell(43,5,'',1,0,'C');
        $this->Cell(30,10,'',0,0,'C');
        $this->Cell(23,5,'End Date ',1,0,'C');
        $this->Cell(41,5,'',1,1,'C');

        $this->Cell(40,5,'Accommodation',1,0,'C');
        $this->Cell(150,5,'',1,1,'C');

        // simpan posisi awal
        $x = $this->GetX();
        $y = $this->GetY();

        // gambar kotak border saja
        $this->Cell(140,25,'',1,0);

        // isi teks di pojok kiri atas (tanpa border)
        $this->SetXY($x+2, $y+2); // kasih padding 2 biar tidak mepet border
        $this->MultiCell(136,5,'Tools / Material Required',0,'L');

        // pindah kursor ke kanan setelah cell
        $this->SetXY($x+140, $y);

        $this->SetFont('Arial','',9);

        $x = $this->GetX();
        $y = $this->GetY();

        // Lebar 50, tinggi baris dibuat lebih rapat, misalnya 4 (default 5)
        $this->MultiCell(50,3.5,
        "Position Description:
        PM = Project Manager
        SM = Site Manager
        SP = Site/Project Supervisor
        EN = Engineer/Marketing
        AD = Admin
        TE = Technician",1,'L');

        $this->SetXY($x+50, $y);

    }
}
