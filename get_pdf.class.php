<?php
//header('Content-Type: application/json');
require_once('fpdf/fpdf.php'); //incluimos la libreria
class PDF extends FPDF{
  // Cabecera de página
  function Header(){
    //Encabezado
    $this->Image('fpdf/encabezado2.jpg',50,2,200);  //mover izq,subir,tamaño
    // Salto de línea
    $this->Ln(2);}

  function tabla($header,$ancho,$data)  {  
    //Colores, ancho de línea y fuente en negrita de CABECERA  
    $this->SetFillColor(0,112,190);   // fondo de celda  
    $this->SetTextColor(255,255,255);       // color del texto  
    $this->SetDrawColor(3,3,3);   // color de linea  
    $this->SetLineWidth(.3);        // ancho de linea  
    $this->SetFont('','B');         // negrita  
    $acum_rows=12;
        for($i=0;$i<count($header);$i++)  
        if ($i==0) {
            $this->Cell(12,7,$header[$i],1,0,'C',1);
        }else{
            $this->Cell($ancho[($i-1)],7,$header[$i],1,0,'C',1); //por cada encabezado existente, crea una celda
            $acum_rows=$acum_rows+$ancho[($i-1)];
        }  
    $this->Ln();  
    //Colores, ancho de línea y fuente en negrita de CONTENIDO  
    $this->SetFillColor(224,235,255); //  
    $this->SetTextColor(0);  
    $this->SetFont('helvetica');  
    //Datos  
    $fill=false; // variable para alternar relleno 
    //$ancho=50;
    for ($i = 0; $i<count($data); $i++) {
        $this->Cell(12,6,($i+1),'LR',0,'C',$fill);
        for ($j = 0; $j<count($data[$i]); $j++) {
            $this->Cell($ancho[$j],6,($data[$i][$j]),'LR',0,'C',$fill);  
            //$ancho=50;          
        }
        $this->Ln();
        $fill=!$fill;
    }

    /*foreach($data as $row) {  
        $columna = explode(";",$row); //separar los datos en posiciones de arreglo  
        $this->Cell($w[0],4,$columna[0],'LR',0,'C',$fill); //celda(ancho,alto,salto de linea,border,alineacion,relleno)  
        $this->Cell($w[1],4,$columna[1],'LR',0,'C',$fill);  

        $this->Ln();  
        $fill=!$fill; //se alterna el valor del boolean $fill para cambiar relleno  
    }  */
    $this->Cell($acum_rows,0,'','T');   
  }
  // Pie de página
  function Footer(){
    //Pie
    $this->Image('fpdf/pie2.jpg',90,190,236); //mover izq,subir,tamaño
    // Posición: a 1,5 cm del final 12=cm
    $this->SetY(-18);
    // Arial italic 8
    $this->SetFont('Arial','I',10);
     // Número de página
    $this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
  }
}

class pdfModelo{
    public $_pdf;
    public function __construct(){
     $this->_pdf = new PDF('L','mm','A4');//tamanios Legal,Letter,A3,A4,A5
    }
    public function get_pdf($datos,$titulos){
       //   DECLARACION DE LA HOJA
    //$pdf=new PDF('L','mm','Legal');//tamanios Legal,Letter,A3,A4,A5
    $ancho_row=$titulos[2];
    $acum_rows=12;
    for ($i = 0; $i<count($ancho_row); $i++) {
        $acum_rows=$acum_rows+$ancho_row[$i];
    }
    $v_300=300-$acum_rows;
    $entre2=$v_300/2;
    $this->_pdf->SetMargins($entre2,60,$entre2); //left,top,right
    $this->_pdf->Ln(4);
    $this->_pdf->AliasNbPages(); //funcion que calcula el numero de paginas 
    $head1=$titulos[1];//$head1 = array_unshift($titulos[1], "#");
    $arr=["#"];
    $head1 = array_merge($arr, $head1);
    //$head = array("#","Apellido","Identidad","Direccion EMC","Cargo", "Monto Solicitado","Fecha Inicial Cuota", "Cuota","Estado"); // cabecera  
    //print_r($head1);
    //print_r($head);
    $this->_pdf->AddPage(); //crear documento  
    $this->_pdf->Cell(40);  
    $this->_pdf->SetFont('Arial','',12);  
    $this->_pdf->Ln(-10);  
    $this->_pdf->SetFont('Arial','',10); //ACA SE CAMBIA LA FUENTE Y TAMAÑO AL ENCABEZADO DE CELDAS 
    //$this->_pdf->Cell(50,1,"REPORTE DE SOLICITUDES DE PRESTAMOS",0,0,'L'); 
    $this->_pdf->Ln(4);
    //$this->_pdf->Cell($acum_rows,5,'Reporte generado fecha: '.date("d-m-Y").', por el usuario: USER',1,1,'C');
    $this->_pdf->Cell($acum_rows,5,'Reporte generado fecha: '.date("d-m-Y"),1,1,'C');  
    //$this->_pdf->Ln(10); 
    //$this->_pdf->Cell(50,1,"PERSONAS ACTIVAS EN EL SISTEMA",0,0,'L');  
    //$this->_pdf->Ln(5); 
    $this->_pdf->tabla($head1,$ancho_row,$datos);  
    //$a=$pdf->Output("Reporte_Gral_Prestamos.pdf","S"); //el resto es historia 
    $file_contents = $this->_pdf->Output("","S");
    //echo utf8_encode($a);
    return 'data:application/pdf;base64,'.base64_encode($file_contents); 
       
    //return (print_r($datos));

    }
}
?>