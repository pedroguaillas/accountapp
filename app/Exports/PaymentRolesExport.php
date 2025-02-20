<?php
namespace App\Exports;

use App\Models\PaymentRole;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use App\Models\Company;

class PaymentRolesExport implements FromCollection, WithHeadings, WithStyles,WithEvents
{

    protected $search;
    protected $year;
    protected $month;

    // Constructor para recibir los filtros
    public function __construct($search = null, $year = null, $month = null)
    {
        $this->search = $search;
        $this->year = $year;
        $this->month = $month;
    }

    public function collection()
    {
        $company = Company::first();

        // Consulta principal con relaciones
        $paymentroles = PaymentRole::with(['employee', 'paymentroleingresses.roleIngress', 'paymentroleegresses.roleEgress'])
            ->when($this->search, function ($query, $search) {
                $query->whereHas('employee', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%");
                })
                    ->whereNull('deleted_at');
            })
            ->where('company_id', $company->id) // Filtrar por compañía si es necesario
            ->whereRaw("EXTRACT(YEAR FROM \"date\") = ?", [$this->year]) // Filtrar por año
            ->whereRaw("EXTRACT(MONTH FROM \"date\") = ?", [$this->month]) // Filtrar por mes
            ->whereNull('deleted_at')
            ->get(); // Obtén los resultados como una colección

        // Mapea los resultados para transformarlos
        $transformed = $paymentroles->map(function ($paymentRole) {

            // Mapear los ingresos
            $ingressData = $paymentRole->paymentroleingresses->mapWithKeys(function ($ingress) {
                return [$ingress->roleIngress->name => $ingress->value ];
            });

            $egressData = $paymentRole->paymentroleegresses->mapWithKeys(function ($egress) {
                return [$egress->roleEgress->name => $egress->value ];
            });

            // Combinar los valores fijos con los ingresos dinámicos
            return array_merge([
                'cuit' => $paymentRole->employee->cuit,
                'name' => $paymentRole->employee->name,
                'position' => $paymentRole->employee->position,
                'sector_code' => $paymentRole->employee->sector_code,
                'days' => $paymentRole->employee->days,
                'salary' => $paymentRole->employee->salary,
            ], $ingressData->toArray(), $egressData->toArray(), ['salary_receive' => $paymentRole->salary_receive,]); // Agregar los ingresos mapeados
        });
        return collect($transformed); // Retorna la colección transformada
    }

    public function headings(): array
    {
        // Encabezados fijos
        $fixedHeadings = [
            'Cédula',
            'Nombre',
            'Departamento/Cargo',
            'Actividad sectorial',
            'Días',
            'Salario',
        ];

        // Encabezados dinámicos de ingresos
        $dynamicHeadings = PaymentRole::join('payment_role_ingresses', 'payment_roles.id', '=', 'payment_role_ingresses.payment_role_id')
            ->join('role_ingresses', 'payment_role_ingresses.role_ingress_id', '=', 'role_ingresses.id')
            ->whereColumn('role_ingresses.created_at', '<=', 'payment_roles.created_at')
            ->whereRaw("EXTRACT(YEAR FROM \"date\") = ?", [$this->year]) // Filtrar por año
            ->whereRaw("EXTRACT(MONTH FROM \"date\") = ?", [$this->month]) // Filtrar por mes
            ->pluck('role_ingresses.name')
            ->unique()
            ->toArray();

        // Encabezados dinámicos de egresos

        $dynamicHeadingss = PaymentRole::join('payment_role_egresses', 'payment_roles.id', '=', 'payment_role_egresses.payment_role_id')
            ->join('role_egresses', 'payment_role_egresses.role_egress_id', '=', 'role_egresses.id')
            ->whereColumn('role_egresses.created_at', '<=', 'payment_roles.created_at')
            ->whereRaw("EXTRACT(YEAR FROM \"date\") = ?", [$this->year]) // Filtrar por año
            ->whereRaw("EXTRACT(MONTH FROM \"date\") = ?", [$this->month]) // Filtrar por mes
            ->pluck('role_egresses.name')
            ->unique()
            ->toArray();

        $fixedHeadingss = [
            'Sueldo a recibir',
        ];

        return array_merge($fixedHeadings, $dynamicHeadings, $dynamicHeadingss, $fixedHeadingss);
    }
    public function styles(Worksheet $sheet)
    {
        $company = Company::first();

        //vector meses para la seleccion del mes que se utiliza en la parte de abajo
        $meses = [
            'ENERO',
            'FEBRERO',
            'MARZO',
            'ABRIL',
            'MAYO',
            'JUNIO',
            'JULIO',
            'AGOSTO',
            'SEPTIEMBRE',
            'OCTUBRE',
            'NOVIEMBRE',
            'DICIEMBRE'
        ];

        // Inserta una fila antes de los encabezados
        $sheet->insertNewRowBefore(1, 2); // Inserta dos filas al principio (para el nuevo título y el encabezado)

        // Establece el nuevo título en la celda A1
        $sheet->setCellValue('A1', $company->company); // Reemplaza por el título que desees

        // Establece el título del rol de pagos en la celda A2
        $sheet->setCellValue('A2', 'ROL DE PAGOS ' . $meses[$this->month - 1] . ' ' . $this->year);

        // Combina las celdas de la fila de título
        $highestColumn = $sheet->getHighestColumn();
        $sheet->mergeCells("A1:{$highestColumn}1");
        $sheet->mergeCells("A2:{$highestColumn}2");

        // Estilo para la fila del nuevo título
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16, // Ajusta el tamaño según lo necesites
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
        ]);

        // Estilo para la fila de "ROL DE PAGOS"
        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
        ]);

        // Aplica estilos a todas las celdas de la tabla
        $highestRow = $sheet->getHighestRow();
        $range = "A3:{$highestColumn}{$highestRow}";

        $sheet->getStyle($range)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'], // Color negro para los bordes
                ],
            ],
            'alignment' => [
                'vertical' => 'center', // Centrado vertical
                'wrapText' => true, // Ajustar texto
            ],
        ]);

        // Estilo para los encabezados (ahora en la fila 3)
        $sheet->getStyle("A3:{$highestColumn}3")->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => 'center', // Centrado horizontal
                'vertical' => 'center',  // Centrado vertical
            ],
        ]);

        return [];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                // Convertir la columna más a la derecha (letra) a índice numérico
                $highestColumnIndex = Coordinate::columnIndexFromString($sheet->getHighestColumn());

                // Suponiendo que los datos comienzan en la fila 4 (después de dos filas de título y una de encabezados)
                for ($row = 4; $row <= $highestRow; $row++) {
                    for ($col = 1; $col <= $highestColumnIndex; $col++) {
                        $cell = $sheet->getCellByColumnAndRow($col, $row);
                        $value = $cell->getValue();
                        if ($value === null || $value === '') {
                            $cell->setValue(0);
                        }
                    }
                }
            }
        ];
    }
}