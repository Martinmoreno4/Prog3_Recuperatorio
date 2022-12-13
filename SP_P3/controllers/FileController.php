<?php


use Fpdf\Fpdf;
require_once './models/Sale.php';

class FileController extends Sale{
    
    public function CreatePDF($request, $response, $args) 
    {
        $sales = Sale::getAllEntities();
        var_dump($sales);
        if ($sales) {
            $pdf = new FPDF();
            $pdf->AddPage();

            //tipo de letra del pdf 'arial black'
            $pdf->SetFont('Arial', 'B', 25);

            // titulo principal del pdf
            $pdf->Cell(160, 15, 'SPP3', 1, 3, 'L');
            $pdf->Ln(3);

            $pdf->SetFont('Arial', '', 15);

            // titulo secundario del pdf
            $pdf->Cell(60, 4, 'Facundo Falcone', 0, 1, 'L');
            $pdf->Cell(20, 0, '', 'T');
            $pdf->Ln(3);
            
            // titulo de la tabla
            $pdf->Cell(60, 4, 'Facundo Falcone', 0, 1, 'L');
            $pdf->Cell(15, 0, '', 'T');
            $pdf->Ln(5);

            // Columnas de la clase venta
            $header = array('ID', 'DATE', 'CRYPTO_NAME', 'AMOUNT', 'CUSTOMER', 'USER', 'IMAGE');
            
            // colores RGB del fondo de las filas de la tabla del pdf
            $pdf->SetFillColor(125, 0, 0);
            $pdf->SetTextColor(125);
            $pdf->SetDrawColor(50, 0, 0);
            $pdf->SetLineWidth(.3);
            $pdf->SetFont('Arial', 'B', 8);
            $w = array(20, 30, 30, 30, 40, 30);
            for ($i = 0; $i < count($header); $i++) {
                $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
            }
            $pdf->Ln();

            // Colores de los bordes de las filas de la tabla del pdf en rgb
            $pdf->SetFillColor(215, 209, 235);
            $pdf->SetTextColor(0);
            $pdf->SetFont('');
            // Data
            $fill = false;

            // Header
            foreach ($sales as $sale) { // cada una de las columnas de la clase venta
                $pdf->Cell($w[0], 6, $sale->getId(), 'LR', 0, 'C', $fill);
                $pdf->Cell($w[1], 6, $sale->getdate(), 'LR', 0, 'C', $fill);
                $pdf->Cell($w[2], 6, $sale->getCryptoName(), 'LR', 0, 'C', $fill);
                $pdf->Cell($w[3], 6, $sale->getAmount(), 'LR', 0, 'C', $fill);
                $pdf->Cell($w[4], 6, $sale->getCustomer(), 'LR', 0, 'C', $fill);
                $pdf->Cell($w[5], 6, $sale->getUser(), 'LR', 0, 'C', $fill);
                $pdf->Cell($w[6], 6, $sale->getImage(), 'LR', 0, 'C', $fill);
                
                $pdf->Ln();
                $fill = !$fill;
            }
            $pdf->Cell(array_sum($w), 0, '', 'T');

            // ruta del pdf, hay que crear el directorio manualmente
            $pdf->Output('F', './MyFiles/' . $sale->getCustomer() .'.pdf', 'I');

            // ruta del pdf, hay que crear el directorio manualmente
            $payload = json_encode(array("message" => 'pdf created ./MyFiles/' . $sale->getCustomer() .'.pdf'));
        }
        else
        {
            $payload = json_encode(array("error" => 'error getting data'));
        }
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    

    
}
?>