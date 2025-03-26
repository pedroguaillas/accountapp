<?php

namespace Database\Seeders;

use App\Models\MovementType;
use App\Models\PaymentRegim;
use App\Models\Tenant;
use App\Models\Landlord\PayMethod;
use App\Models\Landlord\Iva;
use App\Models\Landlord\Iess;
use App\Models\Landlord\Ice;
use App\Models\Landlord\Withholding;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User Admin
        User::factory()->create([
            'name' => 'Pedro Guaillas',
            'username' => 'peters',
            'email' => 'peters@example.com',
        ]);

        // User Tenant example Ariel
        $user = User::factory()->create([
            'name' => 'Ariel Torres',
            'username' => 'ariels',
            'email' => 'ariel4@example.com',
        ]);

        // Tenant example ariel
        $tenant = Tenant::create(['id' => 'ariels']);

        // Many to Many User with Tenant 
        $user->tenants()->attach($tenant, ['is_owner' => true]); //relacion 

        //dominio
        $tenant->domains()->create(['domain' => 'ariels.example.test']);

        PayMethod::create(['name' => 'SIN UTILIZACION DEL SISTEMA FINANCIERO', 'code' => 1, 'max' => 500]);
        PayMethod::create(['name' => 'COMPENSACIÓN DE DEUDAS', 'code' => 15]);
        PayMethod::create(['name' => 'TARJETA DE DÉBITO', 'code' => 16]);
        PayMethod::create(['name' => 'DINERO ELECTRÓNICO', 'code' => 17]);
        PayMethod::create(['name' => 'TARJETA PREPAGO', 'code' => 18]);
        PayMethod::create(['name' => 'TARJETA DE CRÉDITO', 'code' => 19]);
        PayMethod::create(['name' => 'OTROS CON UTILIZACIÓN DEL SISTEMA FINANCIERO', 'code' => 20]);
        PayMethod::create(['name' => 'ENDOSO DE TÍTULOS', 'code' => 21]);

        Iva::create(['code' => 0, 'name' => '0%', 'percentage' => 0.00]);
        Iva::create(['code' => 2, 'name' => '12%', 'percentage' => 0.12]);
        Iva::create(['code' => 3, 'name' => '14%', 'percentage' => 0.14]);
        Iva::create(['code' => 4, 'name' => '15%', 'percentage' => 0.15]);
        Iva::create(['code' => 5, 'name' => '5%', 'percentage' => 0.05]);
        Iva::create(['code' => 8, 'name' => 'IVA diferenciado', 'percentage' => 0.08]);
        Iva::create(['code' => 6, 'name' => 'No objeto de impuesto', 'percentage' => 0]);

        Iess::create(['code' => 'epe', 'type' => 'empleado', 'name' => 'personal', 'percentage' => 9.54,]);
        Iess::create(['code' => 'spa', 'type' => 'socio', 'name' => 'patronal', 'percentage' => 17.6,]);
        Iess::create(['code' => 'sac', 'type' => 'ambos', 'name' => 'conyuge', 'percentage' => 3.41,]);
        Iess::create(['code' => 'epa', 'type' => 'empleado', 'name' => 'patronal', 'percentage' => 12.15,]);

        PaymentRegim::create(['region' => 'Sierra', 'months_xiii' => "Diciembre", 'months_xiv' => "Agosto", 'months_reserve_funds' => ""]);
        PaymentRegim::create(['region' => 'Costa', 'months_xiii' => "Diciembre", 'months_xiv' => "Marzo", 'months_reserve_funds' => ""]);

        //NOTA ELIMINAR CUANDO SE VAYA A PRODUCCION
        Ice::create(['code' => 3011, 'name' => 'ICE Cigarrillos Rubios', 'percentage' => 0.17]);
        Ice::create(['code' => 3021, 'name' => 'ICE Cigarrillos Negros', 'percentage' => 0.17]);
        Ice::create(['code' => 3031, 'name' => 'ICE Bebidas Alcohólicas', 'percentage' => 0.75]);

        Withholding::create(['code' => '9', 'name' => '10%', 'percentage' => '0.10', 'type' => 'IVA']);
        Withholding::create(['code' => '303', 'name' => 'Honorarios profesionales y demás pagos por servicios relacionados con el título profesional', 'percentage' => '0.10', 'type' => 'RENTA']);
        Withholding::create(['code' => '303A', 'name' => 'Servicios profesionales prestados por sociedades residentes', 'percentage' => '0.03', 'type' => 'RENTA']);
        Withholding::create(['code' => '304', 'name' => 'Servicios predomina el intelecto no relacionados con el título profesional', 'percentage' => '0.10', 'type' => 'RENTA']);
        Withholding::create(['code' => '304A', 'name' => 'Comisiones y demás pagos por servicios predomina intelecto no relacionados con el título profesional', 'percentage' => '0.10', 'type' => 'RENTA']);
        Withholding::create(['code' => '304B', 'name' => 'Pagos a notarios y registradores de la propiedad y mercantil por sus actividades ejercidas como tales', 'percentage' => '0.10', 'type' => 'RENTA']);
        Withholding::create(['code' => '304C', 'name' => 'Pagos a deportistas, entrenadores, árbitros, miembros del cuerpo técnico por sus actividades ejercidas como tales', 'percentage' => '0.08', 'type' => 'RENTA']);
        Withholding::create(['code' => '304D', 'name' => 'Pagos a artistas por sus actividades ejercidas como tales', 'percentage' => '0.08', 'type' => 'RENTA']);
        Withholding::create(['code' => '304E', 'name' => 'Honorarios y demás pagos por servicios de docencia', 'percentage' => '0.10', 'type' => 'RENTA']);
        Withholding::create(['code' => '307', 'name' => 'Servicios predomina la mano de obra', 'percentage' => '0.02', 'type' => 'RENTA']);
        Withholding::create(['code' => '308', 'name' => 'Utilización o aprovechamiento de la imagen o renombre (personas naturales, sociedades, influencers)', 'percentage' => '0.10', 'type' => 'RENTA']);
        Withholding::create(['code' => '309', 'name' => 'Servicios prestados por medios de comunicación y agencias de publicidad', 'percentage' => '0.0275', 'type' => 'RENTA']);
        Withholding::create(['code' => '310', 'name' => 'Servicio de transporte privado de pasajeros o transporte público o privado de carga', 'percentage' => '0.01', 'type' => 'RENTA']);
        Withholding::create(['code' => '311', 'name' => 'Pagos a través de liquidación de compra (nivel cultural o rusticidad)', 'percentage' => '0.02', 'type' => 'RENTA']);
        Withholding::create(['code' => '312', 'name' => 'Transferencia de bienes muebles de naturaleza corporal', 'percentage' => '0.0175', 'type' => 'RENTA']);
        Withholding::create(['code' => '312A', 'name' => 'COMPRAS AL PRODUCTOR: de bienes de origen bioacuático, forestal y los descritos en el art.27.1 de LRTI', 'percentage' => '0.01', 'type' => 'RENTA']);
        Withholding::create(['code' => '312C', 'name' => 'COMPRAS AL COMERCIALIZADOR: de bienes de origen bioacuático, forestal y los descritos en el art.27.1 de LRTI', 'percentage' => '0.0175', 'type' => 'RENTA']);
        Withholding::create(['code' => '314A', 'name' => 'Regalías por concepto de franquicias de acuerdo al Código INGENIOS (COESCCI) - pago a personas naturales', 'percentage' => '0.10', 'type' => 'RENTA']);
        Withholding::create(['code' => '314B', 'name' => 'Cánones, derechos de autor, marcas, patentes y similares de acuerdo al Código INGENIOS (COESCCI) – pago a personas naturales', 'percentage' => '0.10', 'type' => 'RENTA']);
        Withholding::create(['code' => '314C', 'name' => 'Regalías por concepto de franquicias de acuerdo al Código INGENIOS (COESCCI) - pago a sociedades', 'percentage' => '0.10', 'type' => 'RENTA']);
        Withholding::create(['code' => '314D', 'name' => 'Cánones, derechos de autor, marcas, patentes y similares de acuerdo al Código INGENIOS (COESCCI)', 'percentage' => '0.10', 'type' => 'RENTA']);
        Withholding::create(['code' => '319', 'name' => 'Cuotas de arrendamiento mercantil (prestado por sociedades), inclusive la de opción de compra', 'percentage' => '0.02', 'type' => 'RENTA']);
        Withholding::create(['code' => '320', 'name' => 'Arrendamiento bienes inmuebles', 'percentage' => '0.10', 'type' => 'RENTA']);
        Withholding::create(['code' => '322', 'name' => 'Seguros y reaseguros (primas y cesiones)', 'percentage' => '0.01', 'type' => 'RENTA']);
        Withholding::create(['code' => '323', 'name' => 'Rendimientos financieros pagados a naturales y sociedades (No a IFIs)', 'percentage' => '0.02', 'type' => 'RENTA']);
        Withholding::create(['code' => '323A', 'name' => 'Rendimientos financieros depósitos Cta. Corriente', 'percentage' => '0.02', 'type' => 'RENTA']);
        Withholding::create(['code' => '323B1', 'name' => 'Rendimientos financieros depósitos Cta. Ahorros Sociedades', 'percentage' => '0.02', 'type' => 'RENTA']);
        Withholding::create(['code' => '323E', 'name' => 'Rendimientos financieros depósito a plazo fijo gravados', 'percentage' => '0.02', 'type' => 'RENTA']);
        Withholding::create(['code' => '323E2', 'name' => 'Rendimientos financieros depósito a plazo fijo exentos', 'percentage' => '0.00', 'type' => 'RENTA']);
        Withholding::create(['code' => '323F', 'name' => 'Rendimientos financieros operaciones de reporto - repos', 'percentage' => '0.02', 'type' => 'RENTA']);
        Withholding::create(['code' => '323G', 'name' => 'Inversiones (captaciones) rendimientos distintos de aquellos pagados a IFIs', 'percentage' => '0.02', 'type' => 'RENTA']);
        Withholding::create(['code' => '323H', 'name' => 'Rendimientos financieros obligaciones', 'percentage' => '0.02', 'type' => 'RENTA']);
        Withholding::create(['code' => '323I', 'name' => 'Rendimientos financieros bonos convertible en acciones', 'percentage' => '0.02', 'type' => 'RENTA']);
        Withholding::create(['code' => '323M', 'name' => 'Rendimientos financieros: Inversiones en títulos valores en renta fija gravados', 'percentage' => '0.02', 'type' => 'RENTA']);
        Withholding::create(['code' => '323N', 'name' => 'Rendimientos financieros: Inversiones en títulos valores en renta fija exentos', 'percentage' => '0.00', 'type' => 'RENTA']);
        Withholding::create(['code' => '323O', 'name' => 'Intereses y demás rendimientos financieros pagados a bancos y otras entidades sometidas al control de la Superintendencia de Bancos y de la Economía Popular y Solidaria', 'percentage' => '0.00', 'type' => 'RENTA']);
        Withholding::create(['code' => '323P', 'name' => 'Intereses pagados por entidades del sector público a favor de sujetos pasivos', 'percentage' => '0.02', 'type' => 'RENTA']);
        Withholding::create(['code' => '323Q', 'name' => 'Otros intereses y rendimientos financieros gravados', 'percentage' => '0.02', 'type' => 'RENTA']);
        Withholding::create(['code' => '323R', 'name' => 'Otros intereses y rendimientos financieros exentos', 'percentage' => '0.00', 'type' => 'RENTA']);
        Withholding::create(['code' => '323S', 'name' => 'Pagos y créditos en cuenta efectuados por el BCE y los depósitos centralizados de valores, en calidad de intermediarios, a instituciones del sistema financiero por cuenta de otras personas naturales y sociedades', 'percentage' => '0.02', 'type' => 'RENTA']);
        Withholding::create(['code' => '323T', 'name' => 'Rendimientos financieros originados en la deuda pública ecuatoriana', 'percentage' => '0.00', 'type' => 'RENTA']);
        Withholding::create(['code' => '323U', 'name' => 'Rendimientos financieros originados en títulos valores de obligaciones de 360 días o más para el financiamiento de proyectos públicos en asociación público-privada', 'percentage' => '0.00', 'type' => 'RENTA']);
        Withholding::create(['code' => '324A', 'name' => 'Intereses en operaciones de crédito entre instituciones del sistema financiero y entidades economía popular y solidaria.', 'percentage' => '0.01', 'type' => 'RENTA']);
        Withholding::create(['code' => '324B', 'name' => 'Inversiones entre instituciones del sistema financiero y entidades economía popular y solidaria', 'percentage' => '0.01', 'type' => 'RENTA']);
        Withholding::create(['code' => '324C', 'name' => 'Pagos y créditos en cuenta efectuados por el BCE y los depósitos centralizados de valores, en calidad de intermediarios, a instituciones del sistema financiero por cuenta de otras instituciones del sistema financiero', 'percentage' => '0.01', 'type' => 'RENTA']);
        Withholding::create(['code' => '325', 'name' => 'Anticipo dividendos', 'percentage' => '0.22', 'type' => 'RENTA']);
        Withholding::create(['code' => '325A', 'name' => 'Préstamos accionistas, beneficiarios o partícipes residentes o establecidos en el Ecuador', 'percentage' => '0.22', 'type' => 'RENTA']);
        Withholding::create(['code' => '326', 'name' => 'Dividendos distribuidos que correspondan al impuesto a la renta único establecido en el art. 27 de la LRTI', 'percentage' => '0.25', 'type' => 'RENTA']);
        Withholding::create(['code' => '327', 'name' => 'Dividendos distribuidos a personas naturales residentes', 'percentage' => '0.25', 'type' => 'RENTA']);
        Withholding::create(['code' => '328', 'name' => 'Dividendos distribuidos a sociedades residentes', 'percentage' => '0.00', 'type' => 'RENTA']);
        Withholding::create(['code' => '329', 'name' => 'Dividendos distribuidos a fideicomisos residentes', 'percentage' => '0.00', 'type' => 'RENTA']);
        Withholding::create(['code' => '331', 'name' => 'Dividendos en acciones (capitalización de utilidades)', 'percentage' => '0.00', 'type' => 'RENTA']);
        Withholding::create(['code' => '332', 'name' => 'Otras compras de bienes y servicios no sujetas a retención (incluye régimen RIMPE - Negocios Populares, para este caso aplica con cualquier forma de pago inclusive los pagos que deban realizar las tarjetas de crédito/débito)', 'percentage' => '0.00', 'type' => 'RENTA']);
        Withholding::create(['code' => '332B', 'name' => 'Compra de bienes inmuebles', 'percentage' => '0.00', 'type' => 'RENTA']);
        Withholding::create(['code' => '332C', 'name' => 'Transporte público de pasajeros', 'percentage' => '0.00', 'type' => 'RENTA']);
        Withholding::create(['code' => '332D', 'name' => 'Pagos en el país por transporte de pasajeros o transporte internacional de carga, a compañías nacionales o extranjeras de aviación o marítimas', 'percentage' => '0.00', 'type' => 'RENTA']);
        Withholding::create(['code' => '332E', 'name' => 'Valores entregados por las cooperativas de transporte a sus socios', 'percentage' => '0.00', 'type' => 'RENTA']);
        Withholding::create(['code' => '332F', 'name' => 'Compraventa de divisas distintas al dólar de los Estados Unidos de América', 'percentage' => '0.00', 'type' => 'RENTA']);
        Withholding::create(['code' => '332G', 'name' => 'Pagos con tarjeta de crédito', 'percentage' => '0.00', 'type' => 'RENTA']);
        Withholding::create(['code' => '332H', 'name' => 'Pago al exterior tarjeta de crédito reportada por la Emisora de tarjeta de crédito, solo recap', 'percentage' => '0.00', 'type' => 'RENTA']);
        Withholding::create(['code' => '332I', 'name' => 'Pago a través de convenio de débito (Clientes IFIs)', 'percentage' => '0.00', 'type' => 'RENTA']);
        Withholding::create(['code' => '333', 'name' => 'Ganancia en la enajenación de derechos representativos de capital u otros derechos que permitan la exploración, explotación, concesión o similares de sociedades, que se coticen en bolsa de valores del Ecuador', 'percentage' => '10.00', 'type' => 'RENTA']);
        Withholding::create(['code' => '334', 'name' => 'Contraprestación producida por la enajenación de derechos representativos de capital u otros derechos que permitan la exploración, explotación, concesión o similares de sociedades, no cotizados en bolsa de valores del Ecuador', 'percentage' => '1.00', 'type' => 'RENTA']);
        Withholding::create(['code' => '335', 'name' => 'Loterías, rifas, pronósticos deportivos, apuestas y similares', 'percentage' => '15.00', 'type' => 'RENTA']);
        Withholding::create(['code' => '336', 'name' => 'Venta de combustibles a comercializadoras', 'percentage' => '0.20', 'type' => 'RENTA']); // 2/mil = 0.20%
        Withholding::create(['code' => '337', 'name' => 'Venta de combustibles a distribuidores', 'percentage' => '0.30', 'type' => 'RENTA']); // 3/mil = 0.30%
        Withholding::create(['code' => '338', 'name' => 'Producción y venta local de banano producido o no por el mismo sujeto pasivo', 'percentage' => '1.00', 'type' => 'RENTA']); // Rango de 1 a 2, se puede ajustar si es necesario
        Withholding::create(['code' => '340', 'name' => 'Impuesto único a la exportación de banano', 'percentage' => '3.00', 'type' => 'RENTA']);
        Withholding::create(['code' => '343', 'name' => 'Otras retenciones aplicables el 1% (incluye régimen RIMPE - Emprendedores, para este caso aplica con cualquier forma de pago inclusive los pagos que deban realizar las tarjetas de crédito/débito)', 'percentage' => '1.00', 'type' => 'RENTA']);
        Withholding::create(['code' => '343A', 'name' => 'Energía eléctrica', 'percentage' => '1.00', 'type' => 'RENTA']);
        Withholding::create(['code' => '343B', 'name' => 'Actividades de construcción de obra material inmueble, urbanización, lotización o actividades similares', 'percentage' => '1.75', 'type' => 'RENTA']);
        Withholding::create(['code' => '343C', 'name' => 'Recepción de botellas plásticas no retornables de PET', 'percentage' => '2.00', 'type' => 'RENTA']);
        Withholding::create(['code' => '3440', 'name' => 'Otras retenciones aplicables el 2,75%', 'percentage' => '2.75', 'type' => 'RENTA']);
        Withholding::create(['code' => '344A', 'name' => 'Pago local tarjeta de crédito /débito reportada por la Emisora de tarjeta de crédito / entidades del sistema financiero', 'percentage' => '2.00', 'type' => 'RENTA']);
        Withholding::create(['code' => '344B', 'name' => 'Adquisición de sustancias minerales dentro del territorio nacional', 'percentage' => '2.00', 'type' => 'RENTA']);
        Withholding::create(['code' => '346', 'name' => 'Otras retenciones aplicables a otros porcentajes', 'percentage' => 'Varios', 'type' => 'RENTA']);
        Withholding::create(['code' => '346A', 'name' => 'Otras ganancias de capital distintas de enajenación de derechos representativos de capital', 'percentage' => 'Varios', 'type' => 'RENTA']);
        Withholding::create(['code' => '346B', 'name' => 'Donaciones en dinero -Impuesto a las donaciones', 'percentage' => 'Conforme Art 36 LRTI literal d)', 'type' => 'RENTA']);
        Withholding::create(['code' => '346C', 'name' => 'Retención a cargo del propio sujeto pasivo por la producción y/o comercialización de minerales y otros bienes', 'percentage' => '0.00 o 10.00', 'type' => 'RENTA']);
        Withholding::create(['code' => '346D', 'name' => 'Retención a cargo del propio sujeto pasivo por la comercialización de productos forestales', 'percentage' => '0.00 o 10.00', 'type' => 'RENTA']);
        Withholding::create(['code' => '350', 'name' => 'Otras autorretenciones (inciso 1 y 2 Art.92.1 RLRTI)', 'percentage' => '1.50 o 1.75', 'type' => 'RENTA']);
        Withholding::create(['code' => '3480', 'name' => 'Impuesto a la renta único sobre los ingresos percibidos por los operadores de pronósticos deportivos', 'percentage' => '15.00', 'type' => 'RENTA']);
        Withholding::create(['code' => '3481', 'name' => 'Autorretenciones Sociedades Grandes Contribuyentes', 'percentage' => 'Conforme Nro. NAC-DGERCGC24-00000003, del 12 de enero de 2024', 'type' => 'RENTA']);
        Withholding::create(['code' => '3482', 'name' => 'Comisiones a sociedades, nacionales o extranjeras residentes y establecimientos permanentes domiciliados en el país', 'percentage' => '3.00', 'type' => 'RENTA']);
        Withholding::create(['code' => '500', 'name' => 'Pago a no residentes - Rentas Inmobiliarias', 'percentage' => '25 o 37', 'type' => 'RENTA']);
        Withholding::create(['code' => '501', 'name' => 'Pago a no residentes - Beneficios/Servicios Empresariales', 'percentage' => '25 o 37', 'type' => 'RENTA']);
        Withholding::create(['code' => '501A', 'name' => 'Pago a no residentes - Servicios técnicos, administrativos o de consultoría y regalías', 'percentage' => '25 o 37', 'type' => 'RENTA']);
        Withholding::create(['code' => '503', 'name' => 'Pago a no residentes- Navegación Marítima y/o aérea', 'percentage' => '0 o 25 o 37', 'type' => 'RENTA']);
        Withholding::create(['code' => '504', 'name' => 'Pago a no residentes- Dividendos distribuidos a personas naturales (domiciliados o no en paraíso fiscal) o a sociedades sin beneficiario efectivo persona natural residente en Ecuador', 'percentage' => '25', 'type' => 'RENTA']);
        Withholding::create(['code' => '504A', 'name' => 'Dividendos a sociedades con beneficiario efectivo persona natural residente en el Ecuador', 'percentage' => 'Hasta 25 y conforme la Resolución NAC-DGERCGC20-000000013', 'type' => 'RENTA']);
        Withholding::create(['code' => '504B', 'name' => 'Dividendos a no residentes incumpliendo el deber de informar la composición societaria', 'percentage' => '37', 'type' => 'RENTA']);
        Withholding::create(['code' => '504C', 'name' => 'Dividendos a residentes o establecidos en paraísos fiscales o regímenes de menor imposición (con beneficiario Persona Natural residente en Ecuador)', 'percentage' => 'Hasta 25 y conforme la Resolución NAC-DGERCGC20-000000013', 'type' => 'RENTA']);
        Withholding::create(['code' => '504D', 'name' => 'Dividendos a fideicomisos o establecidos en paraísos fiscales o regímenes de menor imposición (con beneficiario Persona Natural residente en Ecuador)', 'percentage' => 'Hasta 25 y conforme la Resolución NAC-DGERCGC20-000000013', 'type' => 'RENTA']);
        Withholding::create(['code' => '504E', 'name' => 'Pago a no residentes - Anticipo dividendos (no domiciliada en paraísos fiscales o regímenes de menor imposición)', 'percentage' => '22 o 25', 'type' => 'RENTA']);
        Withholding::create(['code' => '504F', 'name' => 'Pago a no residentes - Anticipo dividendos (domiciliadas en paraísos fiscales o regímenes de menor imposición)', 'percentage' => '22 o 25 o 28', 'type' => 'RENTA']);
        Withholding::create(['code' => '504G', 'name' => 'Pago a no residentes - Préstamos accionistas, beneficiarios o partícipes (no domiciliadas en paraísos fiscales o regímenes de menor imposición)', 'percentage' => '22 o 25', 'type' => 'RENTA']);
        Withholding::create(['code' => '504H', 'name' => 'Pago a no residentes - Préstamos accionistas, beneficiarios o partícipes (domiciliadas en paraísos fiscales o regímenes de menor imposición)', 'percentage' => '22 o 25 o 28', 'type' => 'RENTA']);
        Withholding::create(['code' => '504I', 'name' => 'Pago a no residentes - Préstamos no comerciales a partes relacionadas (no domiciliadas en paraísos fiscales o regímenes de menor imposición)', 'percentage' => '22 o 25', 'type' => 'RENTA']);
        Withholding::create(['code' => '504J', 'name' => 'Pago a no residentes - Préstamos no comerciales a partes relacionadas (domiciliadas en paraísos fiscales o regímenes de menor imposición)', 'percentage' => '22 o 25 o 28', 'type' => 'RENTA']);
        Withholding::create(['code' => '505', 'name' => 'Pago a no residentes - Rendimientos financieros', 'percentage' => '25 o 37', 'type' => 'RENTA']);
        Withholding::create(['code' => '505A', 'name' => 'Pago a no residentes – Intereses de créditos de Instituciones Financieras del exterior', 'percentage' => '0 o 25', 'type' => 'RENTA']);
        Withholding::create(['code' => '505B', 'name' => 'Pago a no residentes – Intereses de créditos de gobierno a gobierno', 'percentage' => '0 o 25', 'type' => 'RENTA']);
        Withholding::create(['code' => '505C', 'name' => 'Pago a no residentes – Intereses de créditos de organismos multilaterales', 'percentage' => '0 o 25', 'type' => 'RENTA']);
        Withholding::create(['code' => '505D', 'name' => 'Pago a no residentes - Intereses por financiamiento de proveedores externos', 'percentage' => '25', 'type' => 'RENTA']);
        Withholding::create(['code' => '505E', 'name' => 'Pago a no residentes - Intereses de otros créditos externos', 'percentage' => '25', 'type' => 'RENTA']);
        Withholding::create(['code' => '505F', 'name' => 'Pago a no residentes - Otros Intereses y Rendimientos Financieros', 'percentage' => '25 o 37', 'type' => 'RENTA']);
        Withholding::create(['code' => '509', 'name' => 'Pago a no residentes- Cánones, derechos de autor, marcas, patentes y similares', 'percentage' => '25 o 37', 'type' => 'RENTA']);
        Withholding::create(['code' => '509A', 'name' => 'Pago a no residentes - Regalías por concepto de franquicias', 'percentage' => '25 o 37', 'type' => 'RENTA']);
        Withholding::create(['code' => '510', 'name' => 'Pago a no residentes - Otras ganancias de capital distintas de enajenación de derechos representativos de capital', 'percentage' => '5, 25, 37', 'type' => 'RENTA']);
        Withholding::create(['code' => '511', 'name' => 'Pago a no residentes - Servicios profesionales independientes', 'percentage' => '25 o 37', 'type' => 'RENTA']);
        Withholding::create(['code' => '512', 'name' => 'Pago a no residentes - Servicios profesionales dependientes', 'percentage' => '25 o 37', 'type' => 'RENTA']);
        Withholding::create(['code' => '513A', 'name' => 'Pago a no residentes - Deportistas', 'percentage' => '25 o 37', 'type' => 'RENTA']);
        Withholding::create(['code' => '514', 'name' => 'Pago a no residentes - Participación de consejeros', 'percentage' => '25 o 37', 'type' => 'RENTA']);
        Withholding::create(['code' => '515', 'name' => 'Pago a no residentes- Artistas y relacionados a organización, producción y espectáculos artísticos y culturales en Ecuador', 'percentage' => '0 al 15; según Ar. 39.3 LRTI / 0 al 25; o 37 según la Resolución NAC-DGERCGC24-00000008', 'type' => 'RENTA']);
        Withholding::create(['code' => '516', 'name' => 'Pago a no residentes - Pensiones', 'percentage' => '25 o 37', 'type' => 'RENTA']);
        Withholding::create(['code' => '517', 'name' => 'Pago a no residentes- Reembolso de Gastos', 'percentage' => '25 o 37', 'type' => 'RENTA']);
        Withholding::create(['code' => '518', 'name' => 'Pago a no residentes- Funciones Públicas', 'percentage' => '25 o 37', 'type' => 'RENTA']);
        Withholding::create(['code' => '519', 'name' => 'Pago a no residentes - Estudiantes', 'percentage' => '25 o 37', 'type' => 'RENTA']);
        Withholding::create(['code' => '520A', 'name' => 'Pago a no residentes - Pago a proveedores de servicios hoteleros y turísticos en el exterior', 'percentage' => '25 o 37', 'type' => 'RENTA']);
        Withholding::create(['code' => '520B', 'name' => 'Pago a no residentes - Arrendamientos mercantil internacional', 'percentage' => '0, 25, 37', 'type' => 'RENTA']);
        Withholding::create(['code' => '520D', 'name' => 'Pago a no residentes - Comisiones por exportaciones y por promoción de turismo receptivo', 'percentage' => '0, 25, 37', 'type' => 'RENTA']);
        Withholding::create(['code' => '520E', 'name' => 'Pago a no residentes - Por las empresas de transporte marítimo o aéreo y por empresas pesqueras de alta mar, por su actividad.', 'percentage' => '0', 'type' => 'RENTA']);
        Withholding::create(['code' => '520F', 'name' => 'Pago a no residentes - Por las agencias internacionales de prensa', 'percentage' => '0, 25, 37', 'type' => 'RENTA']);
        Withholding::create(['code' => '520G', 'name' => 'Pago a no residentes - Contratos de fletamento de naves para empresas de transporte aéreo o marítimo internacional', 'percentage' => '0, 25, 37', 'type' => 'RENTA']);
        Withholding::create(['code' => '521', 'name' => 'Pago a no residentes - Enajenación de derechos representativos de capital u otros derechos que permitan la exploración, explotación, concesión o similares de sociedades', 'percentage' => '1 o 10', 'type' => 'RENTA']);
        Withholding::create(['code' => '523A', 'name' => 'Pago a no residentes - Seguros y reaseguros (primas y cesiones)', 'percentage' => '0,22,37', 'type' => 'RENTA']);
        Withholding::create(['code' => '525', 'name' => 'Pago a no residentes- Donaciones en dinero -Impuesto a las donaciones', 'percentage' => 'Según art 36 LRTI literal d)', 'type' => 'RENTA']);

        MovementType::create(['code' => 'P', 'name' => "Préstamo", 'type' => "Ingreso", 'venue' => 'ambos']);
        MovementType::create(['code' => 'AP', 'name' => "Anticipo proveedor", 'type' => "Egreso", 'venue' => 'ambos']);
        MovementType::create(['code' => 'AC', 'name' => "Anticipo cliente", 'type' => "Ingreso", 'venue' => 'ambos']);
        MovementType::create(['code' => 'OF', 'name' => "Operación de Financiamiento", 'type' => "Ingreso", 'venue' => 'ambos']);
        MovementType::create(['code' => 'RC', 'name' => "Retiro", 'type' => "Egreso", 'venue' => 'bancos']);
        MovementType::create(['code' => 'DB', 'name' => "Deposito", 'type' => "Egreso", 'venue' => 'caja']);
        MovementType::create(['code' => 'PP', 'name' => "Pago préstamo", 'type' => "Egreso", 'venue' => 'bancos']);
        MovementType::create(['code' => 'GND', 'name' => "Gastos no deducibles", 'type' => "Egreso", 'venue' => 'ambos']);
        MovementType::create(['code' => 'GP', 'name' => "Gasto personal", 'type' => "Egreso", 'venue' => 'ambos']);
        MovementType::create(['code' => 'FC', 'name' => "Faltante de Caja", 'type' => "Egreso", 'venue' => 'caja']);
        MovementType::create(['code' => 'SC', 'name' => "Sobrante de Caja", 'type' => "Ingreso", 'venue' => 'caja']);
        MovementType::create(['code' => 'AES', 'name' => "Anticipo empleado sueldo", 'type' => "Ingreso", 'venue' => 'ambos']);
        //Movimientos entre cajas
        MovementType::create(['code' => 'RCC', 'name' => "Reposición", 'type' => "Egreso", 'venue' => 'caja']);
        MovementType::create(['code' => 'PF', 'name' => "Provisición de fondos", 'type' => "Egreso", 'venue' => 'caja']);
        MovementType::create(['code' => 'DCG', 'name' => "Deposito a caja general", 'type' => "Egreso", 'venue' => 'caja']);
        MovementType::create(['code' => 'RFA', 'name' => "Reintegro de fondos sobrantes", 'type' => "Egreso", 'venue' => 'caja']);

    }
}