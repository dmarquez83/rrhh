<?php

use App\Models\Province;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Db;

class ProvinceSeeder extends Seeder{

  public function run(){
    Db::collection('Province')->delete();

    Province::create(array(
      'code' => '01', 
      'name' => 'Azuay', 
      'cantons' => [
        ['code' => '01','name' => 'Cuenca', 'parishes' => [
                                            ['code' => '01','name' => 'Bellavista'],
                                            ['code' => '02','name' => 'Cañaribamba'],
                                            ['code' => '03','name' => 'El Batán'],
                                            ['code' => '04','name' => 'El Sagrario'],
                                            ['code' => '05','name' => 'El Vecino'],
                                            ['code' => '06','name' => 'Gil Ramírez Dávalos'],
                                            ['code' => '07','name' => 'Huaynacápac'],
                                            ['code' => '08','name' => 'Machángara'],
                                            ['code' => '09','name' => 'Monay'],
                                            ['code' => '10','name' => 'San Blas'],
                                            ['code' => '11','name' => 'San Sebastián'],
                                            ['code' => '12','name' => 'Sucre'],
                                            ['code' => '13','name' => 'Totoracocha'],
                                            ['code' => '14','name' => 'Yanuncay'],
                                            ['code' => '15','name' => 'Hermano Miguel'],
                                            ['code' => '50','name' => 'Cuenca'],
                                            ['code' => '51','name' => 'Baños'],
                                            ['code' => '52','name' => 'Cumbe'],
                                            ['code' => '53','name' => 'Chaucha'],
                                            ['code' => '54','name' => 'Checa (Jidcay)'],
                                            ['code' => '55','name' => 'Chiquintad'],
                                            ['code' => '56','name' => 'Llacao'],
                                            ['code' => '57','name' => 'Molleturo'],
                                            ['code' => '58','name' => 'Nulti'],
                                            ['code' => '59','name' => 'Octavio Cordero Palacios (Santa Rosa)'],
                                            ['code' => '60','name' => 'Paccha'],
                                            ['code' => '61','name' => 'Quingeo'],
                                            ['code' => '62','name' => 'Ricaurte'],
                                            ['code' => '63','name' => 'San Joaquín'],
                                            ['code' => '64','name' => 'Santa Ana'],
                                            ['code' => '65','name' => 'Sayausí'],
                                            ['code' => '66','name' => 'Sidcay'],
                                            ['code' => '67','name' => 'Sinincay'],
                                            ['code' => '68','name' => 'Tarqui'],
                                            ['code' => '69','name' => 'Turi'],
                                            ['code' => '70','name' => 'Valle'],
                                            ['code' => '71','name' => 'Victoria Del Portete (Irquis)']]],

        ['code' => '02','name' => 'Girón', 'parishes' => [
                                            ['code' => '50','name' => 'Girón'],
                                            ['code' => '51','name' => 'Asunción'],
                                            ['code' => '52','name' => 'San Gerardo']]],

        ['code' => '03','name' => 'Gualaceo', 'parishes' => [
                                            ['code' => '50','name' => 'Gualaceo'],
                                            ['code' => '51','name' => 'Chordeleg'],
                                            ['code' => '52','name' => 'Daniel Córdova Toral (El Oriente)'],
                                            ['code' => '53','name' => 'Jadán'],
                                            ['code' => '54','name' => 'Mariano Moreno'],
                                            ['code' => '55','name' => 'Principal'],
                                            ['code' => '56','name' => 'Remigio Crespo Toral (Gúlag)'],
                                            ['code' => '57','name' => 'San Juan'],
                                            ['code' => '58','name' => 'Zhidmad'],
                                            ['code' => '59','name' => 'Luis Cordero Vega'],
                                            ['code' => '60','name' => 'Simón Bolívar (Cab. En Gañanzol)']]],

        ['code' => '04','name' => 'Nabón', 'parishes' => [
                                            ['code' => '50','name' => 'Nabón'],
                                            ['code' => '51','name' => 'Cochapata'],
                                            ['code' => '52','name' => 'El Progreso (Cab.En Zhota)'],
                                            ['code' => '53','name' => 'Las Nieves (Chaya)'],
                                            ['code' => '54','name' => 'Oña']]],

        ['code' => '05','name' => 'Paute', 'parishes' => [
                                            ['code' => '50','name' => 'Paute'],
                                            ['code' => '51','name' => 'Amaluza'],
                                            ['code' => '52','name' => 'Bulán (José Víctor Izquierdo)'],
                                            ['code' => '53','name' => 'Chicán (Guillermo Ortega)'],
                                            ['code' => '54','name' => 'El Cabo'],
                                            ['code' => '55','name' => 'Guachapala'],
                                            ['code' => '56','name' => 'Guarainag'],
                                            ['code' => '57','name' => 'Palmas'],
                                            ['code' => '58','name' => 'Pan'],
                                            ['code' => '59','name' => 'San Cristóbal (Carlos Ordóñez Lazo)'],
                                            ['code' => '60','name' => 'Sevilla De Oro'],
                                            ['code' => '61','name' => 'Tomebamba'],
                                            ['code' => '62','name' => 'Dug Dug']]],

        ['code' => '06','name' => 'Pucará', 'parishes' => [
                                            ['code' => '50','name' => 'Pucará'],
                                            ['code' => '51','name' => 'Camilo Ponce Enríquez (Cab. En Río 7 De Mollepongo)'],
                                            ['code' => '52','name' => 'San Rafael De Sharug']]],

        ['code' => '07','name' => 'San Fernando', 'parishes' => [
                                            ['code' => '50','name' => 'San Fernando'],
                                            ['code' => '51','name' => 'Chumblín']]],

        ['code' => '08','name' => 'Santa Isabel', 'parishes' => [
                                            ['code' => '50','name' => 'Santa Isabel (Chaguarurco)'],
                                            ['code' => '51','name' => 'Abdón Calderón (La Unión)'],
                                            ['code' => '52','name' => 'El Carmen De Pijilí'],
                                            ['code' => '53','name' => 'Zhaglli (Shaglli)'],
                                            ['code' => '54','name' => 'San Salvador De Cañaribamba']]],

        ['code' => '09','name' => 'Sigsig', 'parishes' => [
                                            ['code' => '50','name' => 'Sigsig'],
                                            ['code' => '51','name' => 'Cuchil (Cutchil)'],
                                            ['code' => '52','name' => 'Gima'],
                                            ['code' => '53','name' => 'Guel'],
                                            ['code' => '54','name' => 'Ludo'],
                                            ['code' => '55','name' => 'San Bartolomé'],
                                            ['code' => '56','name' => 'San José De Raranga']]],

        ['code' => '10','name' => 'Oña', 'parishes' => [
                                            ['code' => '50','name' => 'San Felipe De Oña Cabecera Cantonal'],
                                            ['code' => '51','name' => 'Susudel']]],

        ['code' => '11','name' => 'Chordeleg', 'parishes' => [
                                            ['code' => '50','name' => 'Chordeleg'],
                                            ['code' => '51','name' => 'Principal'],
                                            ['code' => '52','name' => 'La Unión'],
                                            ['code' => '53','name' => 'Luis Galarza Orellana (Cab.En Delegsol)'],
                                            ['code' => '54','name' => 'San Martín De Puzhio']]],

        ['code' => '12','name' => 'El Pan', 'parishes' => [
                                            ['code' => '50','name' => 'El Pan'],
                                            ['code' => '51','name' => 'Amaluza'],
                                            ['code' => '52','name' => 'Palmas'],
                                            ['code' => '53','name' => 'San Vicente']]],

        ['code' => '13','name' => 'Sevilla de oro', 'parishes' => [
                                            ['code' => '50','name' => 'Sevilla De Oro'],
                                            ['code' => '51','name' => 'Amaluza'],
                                            ['code' => '52','name' => 'Palmas']]],

        ['code' => '14','name' => 'Guachapala', 'parishes' => [
                                            ['code' => '50','name' => 'Guachapala']]],

        ['code' => '15','name' => 'Camilo Poce Enríquez', 'parishes' => [
                                            ['code' => '50','name' => 'Camilo Ponce Enríquez'],
                                            ['code' => '51','name' => 'El Carmen De Pijilí']]],
      ]
    ));


    Province::create(array(
      'code' => '02', 
      'name' => 'Bolivar', 
      'cantons' => [
        ['code' => '01','name' => 'Guaranda', 'parishes' => [
                                            ['code' => '01','name' => 'Ángel Polibio Cháves'],
                                            ['code' => '02','name' => 'Gabriel Ignacio Veintimilla'],
                                            ['code' => '03','name' => 'Guanujo'],
                                            ['code' => '50','name' => 'Guaranda'],
                                            ['code' => '51','name' => 'Facundo Vela'],
                                            ['code' => '52','name' => 'Guanujo'],
                                            ['code' => '53','name' => 'Julio E. Moreno (Catanahuán Grande)'],
                                            ['code' => '54','name' => 'Las Naves'],
                                            ['code' => '55','name' => 'Salinas'],
                                            ['code' => '56','name' => 'San Lorenzo'],
                                            ['code' => '57','name' => 'San Simón (Yacoto)'],
                                            ['code' => '58','name' => 'Santa Fé (Santa Fé)'],
                                            ['code' => '59','name' => 'Simiátug'],
                                            ['code' => '60','name' => 'San Luis De Pambil']]],

        ['code' => '02','name' => 'Chillanes', 'parishes' => [
                                            ['code' => '50','name' => 'Chillanes'],
                                            ['code' => '51','name' => 'San José Del Tambo (Tambopamba)']]],

        ['code' => '03','name' => 'Chimbo', 'parishes' => [
                                            ['code' => '50','name' => 'San José De Chimbo'],
                                            ['code' => '51','name' => 'Asunción (Asancoto)'],
                                            ['code' => '52','name' => 'Caluma'],
                                            ['code' => '53','name' => 'Magdalena (Chapacoto)'],
                                            ['code' => '54','name' => 'San Sebastián'],
                                            ['code' => '55','name' => 'Telimbela']]],

        ['code' => '04','name' => 'Echeandía', 'parishes' => [
                                            ['code' => '50','name' => 'Echeandía']]],

        ['code' => '05','name' => 'San Miguel', 'parishes' => [
                                            ['code' => '50','name' => 'San Miguel'],
                                            ['code' => '51','name' => 'Balsapamba'],
                                            ['code' => '52','name' => 'Bilován'],
                                            ['code' => '53','name' => 'Régulo De Mora'],
                                            ['code' => '54','name' => 'San Pablo (San Pablo De Atenas)'],
                                            ['code' => '55','name' => 'Santiago'],
                                            ['code' => '56','name' => 'San Vicente']]],

        ['code' => '06','name' => 'Caluma', 'parishes' => [
                                            ['code' => '50','name' => 'Caluma']]],

        ['code' => '07','name' => 'Las Naves', 'parishes' => [
                                            ['code' => '01','name' => 'Las Mercedes'],
                                            ['code' => '02','name' => 'Las Naves']]],
      ]
    ));


    Province::create(array(
      'code' => '03', 
      'name' => 'Cañar', 
      'cantons' => [
        ['code' => '01','name' => 'Azogues', 'parishes' => [
                                            ['code' => '01','name' => 'Aurelio Bayas Martínez'],
                                            ['code' => '02','name' => 'Azogues'],
                                            ['code' => '03','name' => 'Borrero'],
                                            ['code' => '04','name' => 'San Francisco'],
                                            ['code' => '50','name' => 'Azogues'],
                                            ['code' => '51','name' => 'Cojitambo'],
                                            ['code' => '52','name' => 'Déleg'],
                                            ['code' => '53','name' => 'Guapán'],
                                            ['code' => '54','name' => 'Javier Loyola (Chuquipata)'],
                                            ['code' => '55','name' => 'Luis Cordero'],
                                            ['code' => '56','name' => 'Pindilig'],
                                            ['code' => '57','name' => 'Rivera'],
                                            ['code' => '58','name' => 'San Miguel'],
                                            ['code' => '59','name' => 'Solano'],
                                            ['code' => '60','name' => 'Taday']]],

        ['code' => '02','name' => 'Biblián', 'parishes' => [
                                            ['code' => '50','name' => 'Biblián'],
                                            ['code' => '51','name' => 'Nazón (Cab. En Pampa De Domínguez)'],
                                            ['code' => '52','name' => 'San Francisco De Sageo'],
                                            ['code' => '53','name' => 'Turupamba'],
                                            ['code' => '54','name' => 'Jerusalén']]],

        ['code' => '03','name' => 'Cañar', 'parishes' => [
                                            ['code' => '50','name' => 'Cañar'],
                                            ['code' => '51','name' => 'Chontamarca'],
                                            ['code' => '52','name' => 'Chorocopte'],
                                            ['code' => '53','name' => 'General Morales (Socarte)'],
                                            ['code' => '54','name' => 'Gualleturo'],
                                            ['code' => '55','name' => 'Honorato Vásquez (Tambo Viejo)'],
                                            ['code' => '56','name' => 'Ingapirca'],
                                            ['code' => '57','name' => 'Juncal'],
                                            ['code' => '58','name' => 'San Antonio'],
                                            ['code' => '59','name' => 'Suscal'],
                                            ['code' => '60','name' => 'Tambo'],
                                            ['code' => '61','name' => 'Zhud'],
                                            ['code' => '62','name' => 'Ventura'],
                                            ['code' => '63','name' => 'Ducur']]],

        ['code' => '04','name' => 'La Troncal', 'parishes' => [
                                            ['code' => '50','name' => 'La Troncal'],
                                            ['code' => '51','name' => 'Manuel J. Calle'],
                                            ['code' => '52','name' => 'Pancho Negro']]],

        ['code' => '05','name' => 'El Tambo', 'parishes' => [
                                            ['code' => '50','name' => 'El Tambo']]],

        ['code' => '06','name' => 'Déeleg', 'parishes' => [
                                            ['code' => '50','name' => 'Déleg'],
                                            ['code' => '51','name' => 'Solano']]],

        ['code' => '07','name' => 'Suscal', 'parishes' => [
                                            ['code' => '50','name' => 'Suscal']]],
        ]
     ));


    Province::create(array(
      'code' => '04', 
      'name' => 'Carchi', 
      'cantons' => [
        ['code' => '01','name' => 'Tulcán', 'parishes' => [
                                            ['code' => '01','name' => 'González Suárez'],
                                            ['code' => '02','name' => 'Tulcán'],
                                            ['code' => '50','name' => 'Tulcán'],
                                            ['code' => '51','name' => 'El Carmelo (El Pun)'],
                                            ['code' => '52','name' => 'Huaca'],
                                            ['code' => '53','name' => 'Julio Andrade (Orejuela)'],
                                            ['code' => '54','name' => 'Maldonado'],
                                            ['code' => '55','name' => 'Pioter'],
                                            ['code' => '56','name' => 'Tobar Donoso (La Bocana De Camumbí)'],
                                            ['code' => '57','name' => 'Tufiño'],
                                            ['code' => '58','name' => 'Urbina (Taya)'],
                                            ['code' => '59','name' => 'El Chical'],
                                            ['code' => '60','name' => 'Mariscal Sucre'],
                                            ['code' => '61','name' => 'Santa Martha De Cuba']]],

        ['code' => '02','name' => 'Bolívar', 'parishes' => [
                                            ['code' => '50','name' => 'Bolívar'],
                                            ['code' => '51','name' => 'García Moreno'],
                                            ['code' => '52','name' => 'Los Andes'],
                                            ['code' => '53','name' => 'Monte Olivo'],
                                            ['code' => '54','name' => 'San Vicente De Pusir'],
                                            ['code' => '55','name' => 'San Rafael']]],

        ['code' => '03','name' => 'Espejo', 'parishes' => [
                                            ['code' => '01','name' => 'El Ángel'],
                                            ['code' => '02','name' => '27 De Septiembre'],
                                            ['code' => '50','name' => 'El Angel'],
                                            ['code' => '51','name' => 'El Goaltal'],
                                            ['code' => '52','name' => 'La Libertad (Alizo)'],
                                            ['code' => '53','name' => 'San Isidro']]],

        ['code' => '04','name' => 'Mira', 'parishes' => [
                                            ['code' => '50','name' => 'Mira (Chontahuasi)'],
                                            ['code' => '51','name' => 'Concepción'],
                                            ['code' => '52','name' => 'Jijón Y Caamaño (Cab. En Río Blanco)'],
                                            ['code' => '53','name' => 'Juan Montalvo (San Ignacio De Quil)']]],

        ['code' => '05','name' => 'Montúfar', 'parishes' => [
                                            ['code' => '01','name' => 'González Suárez'],
                                            ['code' => '02','name' => 'San José'],
                                            ['code' => '50','name' => 'San Gabriel'],
                                            ['code' => '51','name' => 'Cristóbal Colón'],
                                            ['code' => '52','name' => 'Chitán De Navarrete'],
                                            ['code' => '53','name' => 'Fernández Salvador'],
                                            ['code' => '54','name' => 'La Paz'],
                                            ['code' => '55','name' => 'Piartal']]],

        ['code' => '06','name' => 'San Pedro De Huaca', 'parishes' => [
                                            ['code' => '50','name' => 'Huaca'],
                                            ['code' => '51','name' => 'Mariscal Sucre']]],
        ]
     ));


    Province::create(array(
      'code' => '05', 
      'name' => 'Cotopaxi', 
      'cantons' => [
        ['code' => '01','name' => 'Latacunga', 'parishes' => [
                                            ['code' => '01','name' => 'Eloy Alfaro (San Felipe)'],
                                            ['code' => '02','name' => 'Ignacio Flores (Parque Flores)'],
                                            ['code' => '03','name' => 'Juan Montalvo (San Sebastián)'],
                                            ['code' => '04','name' => 'La Matriz'],
                                            ['code' => '05','name' => 'San Buenaventura'],
                                            ['code' => '50','name' => 'Latacunga'],
                                            ['code' => '51','name' => 'Alaques (Aláquez)'],
                                            ['code' => '52','name' => 'Belisario Quevedo (Guanailín)'],
                                            ['code' => '53','name' => 'Guaitacama (Guaytacama)'],
                                            ['code' => '54','name' => 'Joseguango Bajo'],
                                            ['code' => '55','name' => 'Las Pampas'],
                                            ['code' => '56','name' => 'Mulaló'],
                                            ['code' => '57','name' => '11 De Noviembre (Ilinchisi)'],
                                            ['code' => '58','name' => 'Poaló'],
                                            ['code' => '59','name' => 'San Juan De Pastocalle'],
                                            ['code' => '60','name' => 'Sigchos'],
                                            ['code' => '61','name' => 'Tanicuchí'],
                                            ['code' => '62','name' => 'Toacaso'],
                                            ['code' => '63','name' => 'Palo Quemado']]],


        ['code' => '02','name' => 'La Maná', 'parishes' => [
                                            ['code' => '01','name' => 'El Carmen'],
                                            ['code' => '02','name' => 'La Maná'],
                                            ['code' => '03','name' => 'El Triunfo'],
                                            ['code' => '50','name' => 'La Maná'],
                                            ['code' => '51','name' => 'Guasaganda (Cab.En Guasaganda'],
                                            ['code' => '52','name' => 'Pucayacu']]],

        ['code' => '03','name' => 'Pangua', 'parishes' => [
                                            ['code' => '50','name' => 'El Corazón'],
                                            ['code' => '51','name' => 'Moraspungo'],
                                            ['code' => '52','name' => 'Pinllopata'],
                                            ['code' => '53','name' => 'Ramón Campaña']]],

        ['code' => '04','name' => 'Pujilí', 'parishes' => [
                                            ['code' => '50','name' => 'Pujilí'],
                                            ['code' => '51','name' => 'Angamarca'],
                                            ['code' => '52','name' => 'Chucchilán (Chugchilán)'],
                                            ['code' => '53','name' => 'Guangaje'],
                                            ['code' => '54','name' => 'Isinlibí (Isinliví)'],
                                            ['code' => '55','name' => 'La Victoria'],
                                            ['code' => '56','name' => 'Pilaló'],
                                            ['code' => '57','name' => 'Tingo'],
                                            ['code' => '58','name' => 'Zumbahua']]],

        ['code' => '05','name' => 'Salcedo', 'parishes' => [
                                            ['code' => '50','name' => 'San Miguel'],
                                            ['code' => '51','name' => 'Antonio José Holguín (Santa Lucía)'],
                                            ['code' => '52','name' => 'Cusubamba'],
                                            ['code' => '53','name' => 'Mulalillo'],
                                            ['code' => '54','name' => 'Mulliquindil (Santa Ana)'],
                                            ['code' => '55','name' => 'Pansaleo']]],

        ['code' => '06','name' => 'Saquisilí', 'parishes' => [
                                            ['code' => '50','name' => 'Saquisilí'],
                                            ['code' => '51','name' => 'Canchagua'],
                                            ['code' => '52','name' => 'Chantilín'],
                                            ['code' => '53','name' => 'Cochapamba']]],

        ['code' => '07','name' => 'Sigchos', 'parishes' => [
                                            ['code' => '50','name' => 'Sigchos'],
                                            ['code' => '51','name' => 'Chugchillán'],
                                            ['code' => '52','name' => 'Isinliví'],
                                            ['code' => '53','name' => 'Las Pampas'],
                                            ['code' => '54','name' => 'Palo Quemado']]],
        ]
     ));


    Province::create(array(
      'code' => '06', 
      'name' => 'Chimborazo', 
      'cantons' => [
        ['code' => '01','name' => 'Riobamba', 'parishes' => [
                                            ['code' => '01','name' => 'Lizarzaburu'],
                                            ['code' => '02','name' => 'Maldonado'],
                                            ['code' => '03','name' => 'Velasco'],
                                            ['code' => '04','name' => 'Veloz'],
                                            ['code' => '05','name' => 'Yaruquíes'],
                                            ['code' => '50','name' => 'Riobamba'],
                                            ['code' => '51','name' => 'Cacha (Cab. En Machángara)'],
                                            ['code' => '52','name' => 'Calpi'],
                                            ['code' => '53','name' => 'Cubijíes'],
                                            ['code' => '54','name' => 'Flores'],
                                            ['code' => '55','name' => 'Licán'],
                                            ['code' => '56','name' => 'Licto'],
                                            ['code' => '57','name' => 'Pungalá'],
                                            ['code' => '58','name' => 'Punín'],
                                            ['code' => '59','name' => 'Quimiag'],
                                            ['code' => '60','name' => 'San Juan'],
                                            ['code' => '61','name' => 'San Luis']]],

        ['code' => '02','name' => 'Alausí', 'parishes' => [
                                            ['code' => '50','name' => 'Alausí'],
                                            ['code' => '51','name' => 'Achupallas'],
                                            ['code' => '52','name' => 'Cumandá'],
                                            ['code' => '53','name' => 'Guasuntos'],
                                            ['code' => '54','name' => 'Huigra'],
                                            ['code' => '55','name' => 'Multitud'],
                                            ['code' => '56','name' => 'Pistishí (Nariz Del Diablo)'],
                                            ['code' => '57','name' => 'Pumallacta'],
                                            ['code' => '58','name' => 'Sevilla'],
                                            ['code' => '59','name' => 'Sibambe'],
                                            ['code' => '60','name' => 'Tixán']]],

        ['code' => '03','name' => 'Colta', 'parishes' => [
                                            ['code' => '01','name' => 'Cajabamba'],
                                            ['code' => '02','name' => 'Sicalpa'],
                                            ['code' => '50','name' => 'Villa La Unión (Cajabamba)'],
                                            ['code' => '51','name' => 'Cañi'],
                                            ['code' => '52','name' => 'Columbe'],
                                            ['code' => '53','name' => 'Juan De Velasco (Pangor)'],
                                            ['code' => '54','name' => 'Santiago De Quito (Cab. En San Antonio De Quito)']]],

        ['code' => '04','name' => 'Chambo', 'parishes' => [
                                            ['code' => '50','name' => 'Chambo']]],

        ['code' => '05','name' => 'Chunchi', 'parishes' => [
                                            ['code' => '50','name' => 'Chunchi'],
                                            ['code' => '51','name' => 'Capzol'],
                                            ['code' => '52','name' => 'Compud'],
                                            ['code' => '53','name' => 'Gonzol'],
                                            ['code' => '54','name' => 'Llagos']]],

        ['code' => '06','name' => 'Guamote', 'parishes' => [
                                            ['code' => '50','name' => 'Guamote'],
                                            ['code' => '51','name' => 'Cebadas'],
                                            ['code' => '52','name' => 'Palmira']]],

        ['code' => '07','name' => 'Guano', 'parishes' => [
                                            ['code' => '01','name' => 'El Rosario'],
                                            ['code' => '02','name' => 'La Matriz'],
                                            ['code' => '50','name' => 'Guano'],
                                            ['code' => '51','name' => 'Guanando'],
                                            ['code' => '52','name' => 'Ilapo'],
                                            ['code' => '53','name' => 'La Providencia'],
                                            ['code' => '54','name' => 'San Andrés'],
                                            ['code' => '55','name' => 'San Gerardo De Pacaicaguán'],
                                            ['code' => '56','name' => 'San Isidro De Patulú'],
                                            ['code' => '57','name' => 'San José Del Chazo'],
                                            ['code' => '58','name' => 'Santa Fé De Galán'],
                                            ['code' => '59','name' => 'Valparaíso']]],

        ['code' => '08','name' => 'Pallatanga', 'parishes' => [
                                            ['code' => '50','name' => 'Pallatanga']]],

        ['code' => '09','name' => 'Penipe', 'parishes' => [
                                            ['code' => '50','name' => 'Penipe'],
                                            ['code' => '51','name' => 'El Altar'],
                                            ['code' => '52','name' => 'Matus'],
                                            ['code' => '53','name' => 'Puela'],
                                            ['code' => '54','name' => 'San Antonio De Bayushig'],
                                            ['code' => '55','name' => 'La Candelaria'],
                                            ['code' => '56','name' => 'Bilbao (Cab.En Quilluyacu)']]],

        ['code' => '10','name' => 'Cumandá', 'parishes' => [
                                            ['code' => '50','name' => 'Cumandá']]],
    ]
     ));


    Province::create(array(
      'code' => '07', 
      'name' => 'El Oro', 
      'cantons' => [
        ['code' => '01','name' => 'Machala', 'parishes' => [
                                            ['code' => '01','name' => 'La Providencia'],
                                            ['code' => '02','name' => 'Machala'],
                                            ['code' => '03','name' => 'Puerto Bolívar'],
                                            ['code' => '04','name' => 'Nueve De Mayo'],
                                            ['code' => '05','name' => 'El Cambio'],
                                            ['code' => '50','name' => 'Machala'],
                                            ['code' => '51','name' => 'El Cambio'],
                                            ['code' => '52','name' => 'El Retiro']]],

        ['code' => '02','name' => 'Arenillas', 'parishes' => [
                                            ['code' => '50','name' => 'Arenillas'],
                                            ['code' => '51','name' => 'Chacras'],
                                            ['code' => '52','name' => 'La Libertad'],
                                            ['code' => '53','name' => 'Las Lajas (Cab. En La Victoria)'],
                                            ['code' => '54','name' => 'Palmales'],
                                            ['code' => '55','name' => 'Carcabón']]],

        ['code' => '03','name' => 'Atahualpa', 'parishes' => [
                                            ['code' => '50','name' => 'Paccha'],
                                            ['code' => '51','name' => 'Ayapamba'],
                                            ['code' => '52','name' => 'Cordoncillo'],
                                            ['code' => '53','name' => 'Milagro'],
                                            ['code' => '54','name' => 'San José'],
                                            ['code' => '55','name' => 'San Juan De Cerro Azul']]],

        ['code' => '04','name' => 'Balsas', 'parishes' => [
                                            ['code' => '50','name' => 'Balsas'],
                                            ['code' => '51','name' => 'Bellamaría']]],

        ['code' => '05','name' => 'Chilla', 'parishes' => [
                                            ['code' => '50','name' => 'Chilla']]],

        ['code' => '06','name' => 'El Guabo', 'parishes' => [
                                            ['code' => '50','name' => 'El Guabo'],
                                            ['code' => '51','name' => 'Barbones (Sucre)'],
                                            ['code' => '52','name' => 'La Iberia'],
                                            ['code' => '53','name' => 'Tendales (Cab.En Puerto Tendales)'],
                                            ['code' => '54','name' => 'Río Bonito']]],

        ['code' => '07','name' => 'Huaquillas', 'parishes' => [
                                            ['code' => '01','name' => 'Ecuador'],
                                            ['code' => '02','name' => 'El Paraíso'],
                                            ['code' => '03','name' => 'Hualtaco'],
                                            ['code' => '04','name' => 'Milton Reyes'],
                                            ['code' => '05','name' => 'Unión Lojana'],
                                            ['code' => '50','name' => 'Huaquillas']]],

        ['code' => '08','name' => 'Marcabelí', 'parishes' => [
                                            ['code' => '50','name' => 'Marcabelí'],
                                            ['code' => '51','name' => 'El Ingenio']]],

        ['code' => '09','name' => 'Pasaje', 'parishes' => [
                                            ['code' => '01','name' => 'Bolívar'],
                                            ['code' => '02','name' => 'Loma De Franco'],
                                            ['code' => '03','name' => 'Ochoa León (Matriz)'],
                                            ['code' => '04','name' => 'Tres Cerritos'],
                                            ['code' => '50','name' => 'Pasaje'],
                                            ['code' => '51','name' => 'Buenavista'],
                                            ['code' => '52','name' => 'Casacay'],
                                            ['code' => '53','name' => 'La Peaña'],
                                            ['code' => '54','name' => 'Progreso'],
                                            ['code' => '55','name' => 'Uzhcurrumi'],
                                            ['code' => '56','name' => 'Cañaquemada']]],

        ['code' => '10','name' => 'Piñas', 'parishes' => [
                                            ['code' => '01','name' => 'La Matriz'],
                                            ['code' => '02','name' => 'La Susaya'],
                                            ['code' => '03','name' => 'Piñas Grande'],
                                            ['code' => '50','name' => 'Piñas'],
                                            ['code' => '51','name' => 'Capiro (Cab. En La Capilla De Capiro)'],
                                            ['code' => '52','name' => 'La Bocana'],
                                            ['code' => '53','name' => 'Moromoro (Cab. En El Vado)'],
                                            ['code' => '54','name' => 'Piedras'],
                                            ['code' => '55','name' => 'San Roque (Ambrosio Maldonado)'],
                                            ['code' => '56','name' => 'Saracay']]],


        ['code' => '11','name' => 'Portovelo', 'parishes' => [
                                            ['code' => '50','name' => 'Portovelo'],
                                            ['code' => '51','name' => 'Curtincapa'],
                                            ['code' => '52','name' => 'Morales'],
                                            ['code' => '53','name' => 'Salatí']]],

        ['code' => '12','name' => 'Santa Rosa', 'parishes' => [
                                            ['code' => '01','name' => 'Santa Rosa'],
                                            ['code' => '02','name' => 'Puerto Jelí'],
                                            ['code' => '03','name' => 'Balneario Jambelí (Satélite)'],
                                            ['code' => '04','name' => 'Jumón (Satélite)'],
                                            ['code' => '05','name' => 'Nuevo Santa Rosa'],
                                            ['code' => '50','name' => 'Santa Rosa'],
                                            ['code' => '51','name' => 'Bellavista'],
                                            ['code' => '52','name' => 'Jambelí'],
                                            ['code' => '53','name' => 'La Avanzada'],
                                            ['code' => '54','name' => 'San Antonio'],
                                            ['code' => '55','name' => 'Torata'],
                                            ['code' => '56','name' => 'Victoria'],
                                            ['code' => '57','name' => 'Bellamaría']]],

        ['code' => '13','name' => 'Zaruma', 'parishes' => [
                                            ['code' => '50','name' => 'Zaruma'],
                                            ['code' => '51','name' => 'Abañín'],
                                            ['code' => '52','name' => 'Arcapamba'],
                                            ['code' => '53','name' => 'Guanazán'],
                                            ['code' => '54','name' => 'Guizhaguiña'],
                                            ['code' => '55','name' => 'Huertas'],
                                            ['code' => '56','name' => 'Malvas'],
                                            ['code' => '57','name' => 'Muluncay Grande'],
                                            ['code' => '58','name' => 'Sinsao'],
                                            ['code' => '59','name' => 'Salvias']]],

        ['code' => '14','name' => 'Las Lajas', 'parishes' => [
                                            ['code' => '01','name' => 'La Victoria'],
                                            ['code' => '02','name' => 'Platanillos'],
                                            ['code' => '03','name' => 'Valle Hermoso'],
                                            ['code' => '50','name' => 'La Victoria'],
                                            ['code' => '51','name' => 'La Libertad'],
                                            ['code' => '52','name' => 'El Paraíso'],
                                            ['code' => '53','name' => 'San Isidro']]],
        ]
     ));


    Province::create(array(
      'code' => '08', 
      'name' => 'Esmeraldas', 
      'cantons' => [
        ['code' => '01','name' => 'Esmeraldas', 'parishes' => [
                                            ['code' => '01','name' => 'Bartolomé Ruiz (César Franco Carrión)'],
                                            ['code' => '02','name' => '5 De Agosto'],
                                            ['code' => '03','name' => 'Esmeraldas'],
                                            ['code' => '04','name' => 'Luis Tello (Las Palmas)'],
                                            ['code' => '05','name' => 'Simón Plata Torres'],
                                            ['code' => '50','name' => 'Esmeraldas'],
                                            ['code' => '51','name' => 'Atacames'],
                                            ['code' => '52','name' => 'Camarones (Cab. En San Vicente)'],
                                            ['code' => '53','name' => 'Crnel. Carlos Concha Torres (Cab.En Huele)'],
                                            ['code' => '54','name' => 'Chinca'],
                                            ['code' => '55','name' => 'Chontaduro'],
                                            ['code' => '56','name' => 'Chumundé'],
                                            ['code' => '57','name' => 'Lagarto'],
                                            ['code' => '58','name' => 'La Unión'],
                                            ['code' => '59','name' => 'Majua'],
                                            ['code' => '60','name' => 'Montalvo (Cab. En Horqueta)'],
                                            ['code' => '61','name' => 'Río Verde'],
                                            ['code' => '62','name' => 'Rocafuerte'],
                                            ['code' => '63','name' => 'San Mateo'],
                                            ['code' => '64','name' => 'Súa (Cab. En La Bocana)'],
                                            ['code' => '65','name' => 'Tabiazo'],
                                            ['code' => '66','name' => 'Tachina'],
                                            ['code' => '67','name' => 'TonchigÜE'],
                                            ['code' => '68','name' => 'Vuelta Larga']]],

        ['code' => '02','name' => 'Eloy Alfaro', 'parishes' => [
                                            ['code' => '50','name' => 'Valdez (Limones)'],
                                            ['code' => '51','name' => 'Anchayacu'],
                                            ['code' => '52','name' => 'Atahualpa (Cab. En Camarones)'],
                                            ['code' => '53','name' => 'Borbón'],
                                            ['code' => '54','name' => 'La Tola'],
                                            ['code' => '55','name' => 'Luis Vargas Torres (Cab. En Playa De Oro)'],
                                            ['code' => '56','name' => 'Maldonado'],
                                            ['code' => '57','name' => 'Pampanal De Bolívar'],
                                            ['code' => '58','name' => 'San Francisco De Onzole'],
                                            ['code' => '59','name' => 'Santo Domingo De Onzole'],
                                            ['code' => '60','name' => 'Selva Alegre'],
                                            ['code' => '61','name' => 'Telembí'],
                                            ['code' => '62','name' => 'Colón Eloy Del María'],
                                            ['code' => '63','name' => 'San José De Cayapas'],
                                            ['code' => '64','name' => 'Timbiré']]],

        ['code' => '03','name' => 'Muisne', 'parishes' => [
                                            ['code' => '50','name' => 'Muisne'],
                                            ['code' => '51','name' => 'Bolívar'],
                                            ['code' => '52','name' => 'Daule'],
                                            ['code' => '53','name' => 'Galera'],
                                            ['code' => '54','name' => 'Quingue (Olmedo Perdomo Franco)'],
                                            ['code' => '55','name' => 'Salima'],
                                            ['code' => '56','name' => 'San Francisco'],
                                            ['code' => '57','name' => 'San Gregorio'],
                                            ['code' => '58','name' => 'San José De Chamanga (Cab.En Chamanga)']]],

        ['code' => '04','name' => 'Quinindé', 'parishes' => [
                                            ['code' => '50','name' => 'Rosa Zárate (Quinindé)'],
                                            ['code' => '51','name' => 'Cube'],
                                            ['code' => '52','name' => 'Chura (Chancama) (Cab. En El Yerbero)'],
                                            ['code' => '53','name' => 'Malimpia'],
                                            ['code' => '54','name' => 'Viche'],
                                            ['code' => '55','name' => 'La Unión']]],

        ['code' => '05','name' => 'San Lorenzo', 'parishes' => [
                                            ['code' => '50','name' => 'San Lorenzo'],
                                            ['code' => '51','name' => 'Alto Tambo (Cab. En Guadual)'],
                                            ['code' => '52','name' => 'Ancón (Pichangal) (Cab. En Palma Real)'],
                                            ['code' => '53','name' => 'Calderón'],
                                            ['code' => '54','name' => 'Carondelet'],
                                            ['code' => '55','name' => '5 De Junio (Cab. En Uimbi)'],
                                            ['code' => '56','name' => 'Concepción'],
                                            ['code' => '57','name' => 'Mataje (Cab. En Santander)'],
                                            ['code' => '58','name' => 'San Javier De Cachaví (Cab. En San Javier)'],
                                            ['code' => '59','name' => 'Santa Rita'],
                                            ['code' => '60','name' => 'Tambillo'],
                                            ['code' => '61','name' => 'Tululbí (Cab. En Ricaurte)'],
                                            ['code' => '62','name' => 'Urbina']]],

        ['code' => '06','name' => 'Atacames', 'parishes' => [
                                            ['code' => '50','name' => 'Atacames'],
                                            ['code' => '51','name' => 'La Unión'],
                                            ['code' => '52','name' => 'Súa (Cab. En La Bocana)'],
                                            ['code' => '53','name' => 'TonchigÜE'],
                                            ['code' => '54','name' => 'Tonsupa']]],

        ['code' => '07','name' => 'Rioverde', 'parishes' => [
                                            ['code' => '50','name' => 'Rioverde'],
                                            ['code' => '51','name' => 'Chontaduro'],
                                            ['code' => '52','name' => 'Chumundé'],
                                            ['code' => '53','name' => 'Lagarto'],
                                            ['code' => '54','name' => 'Montalvo (Cab. En Horqueta)'],
                                            ['code' => '55','name' => 'Rocafuerte']]],

        ['code' => '08','name' => 'La Concordia', 'parishes' => [
                                            ['code' => '50','name' => 'La Concordia'],
                                            ['code' => '51','name' => 'Monterrey'],
                                            ['code' => '52','name' => 'La Villegas'],
                                            ['code' => '53','name' => 'Plan Piloto']]],
        ]
     ));


    Province::create(array(
      'code' => '09', 
      'name' => 'Guayas', 
      'cantons' => [
        ['code' => '01','name' => 'Guayaquil', 'parishes' => [
                                            ['code' => '01','name' => 'Ayacucho'],
                                            ['code' => '02','name' => 'Bolívar (Sagrario)'],
                                            ['code' => '03','name' => 'Carbo (Concepción)'],
                                            ['code' => '04','name' => 'Febres Cordero'],
                                            ['code' => '05','name' => 'García Moreno'],
                                            ['code' => '06','name' => 'Letamendi'],
                                            ['code' => '07','name' => 'Nueve De Octubre'],
                                            ['code' => '08','name' => 'Olmedo (San Alejo)'],
                                            ['code' => '09','name' => 'Roca'],
                                            ['code' => '10','name' => 'Rocafuerte'],
                                            ['code' => '11','name' => 'Sucre'],
                                            ['code' => '12','name' => 'Tarqui'],
                                            ['code' => '13','name' => 'Urdaneta'],
                                            ['code' => '14','name' => 'Ximena'],
                                            ['code' => '15','name' => 'Pascuales'],
                                            ['code' => '50','name' => 'Guayaquil'],
                                            ['code' => '51','name' => 'Chongón'],
                                            ['code' => '52','name' => 'Juan Gómez Rendón (Progreso)'],
                                            ['code' => '53','name' => 'Morro'],
                                            ['code' => '54','name' => 'Pascuales'],
                                            ['code' => '55','name' => 'Playas (Gral. Villamil)'],
                                            ['code' => '56','name' => 'Posorja'],
                                            ['code' => '57','name' => 'Puná'],
                                            ['code' => '58','name' => 'Tenguel']]],

        ['code' => '02','name' => 'Alfredo Baquerizo (Juján)', 'parishes' => [
                                            ['code' => '50','name' => 'Alfredo Baquerizo Moreno (Juján)']]],

        ['code' => '03','name' => 'Balao', 'parishes' => [
                                            ['code' => '50','name' => 'Balao']]],

        ['code' => '04','name' => 'Balzar', 'parishes' => [
                                            ['code' => '50','name' => 'Balzar']]],

        ['code' => '05','name' => 'Colimes', 'parishes' => [
                                            ['code' => '50','name' => 'Colimes'],
                                            ['code' => '51','name' => 'San Jacinto']]],

        ['code' => '06','name' => 'Daule', 'parishes' => [
                                            ['code' => '01','name' => 'Daule'],
                                            ['code' => '02','name' => 'La Aurora (Satélite)'],
                                            ['code' => '03','name' => 'Banife'],
                                            ['code' => '04','name' => 'Emiliano Caicedo Marcos'],
                                            ['code' => '05','name' => 'Magro'],
                                            ['code' => '06','name' => 'Padre Juan Bautista Aguirre'],
                                            ['code' => '07','name' => 'Santa Clara'],
                                            ['code' => '08','name' => 'Vicente Piedrahita'],
                                            ['code' => '50','name' => 'Daule'],
                                            ['code' => '51','name' => 'Isidro Ayora (Soledad)'],
                                            ['code' => '52','name' => 'Juan Bautista Aguirre (Los Tintos)'],
                                            ['code' => '53','name' => 'Laurel'],
                                            ['code' => '54','name' => 'Limonal'],
                                            ['code' => '55','name' => 'Lomas De Sargentillo'],
                                            ['code' => '56','name' => 'Los Lojas (Enrique Baquerizo Moreno)'],
                                            ['code' => '57','name' => 'Piedrahita (Nobol)']]],

        ['code' => '07','name' => 'Durán', 'parishes' => [
                                            ['code' => '01','name' => 'Eloy Alfaro (Durán)'],
                                            ['code' => '02','name' => 'El Recreo'],
                                            ['code' => '50','name' => 'Eloy Alfaro (Durán)']]],

        ['code' => '08','name' => 'El Empalme', 'parishes' => [
                                            ['code' => '50','name' => 'Velasco Ibarra (El Empalme)'],
                                            ['code' => '51','name' => 'Guayas (Pueblo Nuevo)'],
                                            ['code' => '52','name' => 'El Rosario']]],

        ['code' => '09','name' => 'El Triunfo', 'parishes' => [
                                            ['code' => '50','name' => 'El Triunfo']]],

        ['code' => '10','name' => 'Milagro', 'parishes' => [
                                            ['code' => '50','name' => 'Milagro'],
                                            ['code' => '51','name' => 'Chobo'],
                                            ['code' => '52','name' => 'General Elizalde (Bucay)'],
                                            ['code' => '53','name' => 'Mariscal Sucre (Huaques)'],
                                            ['code' => '54','name' => 'Roberto Astudillo (Cab. En Cruce De Venecia)']]],

        ['code' => '11','name' => 'Naranjal', 'parishes' => [
                                            ['code' => '50','name' => 'Naranjal'],
                                            ['code' => '51','name' => 'Jesús María'],
                                            ['code' => '52','name' => 'San Carlos'],
                                            ['code' => '53','name' => 'Santa Rosa De Flandes'],
                                            ['code' => '54','name' => 'Taura']]],

        ['code' => '12','name' => 'Naranjito', 'parishes' => [
                                            ['code' => '50','name' => 'Naranjito']]],

        ['code' => '13','name' => 'Palestina', 'parishes' => [
                                            ['code' => '50','name' => 'Palestina']]],

        ['code' => '14','name' => 'Pedro Carbo', 'parishes' => [
                                            ['code' => '50','name' => 'Pedro Carbo'],
                                            ['code' => '51','name' => 'Valle De La Virgen'],
                                            ['code' => '52','name' => 'Sabanilla']]],

        ['code' => '16','name' => 'Samborondón', 'parishes' => [
                                            ['code' => '01','name' => 'Samborondón'],
                                            ['code' => '02','name' => 'La Puntilla (Satélite)'],
                                            ['code' => '50','name' => 'Samborondón'],
                                            ['code' => '51','name' => 'Tarifa']]],

        ['code' => '18','name' => 'Santa Lucía', 'parishes' => [
                                            ['code' => '50','name' => 'Santa Lucía']]],

        ['code' => '19','name' => 'Salitre (Urbina Jado)', 'parishes' => [
                                            ['code' => '01','name' => 'Bocana'],
                                            ['code' => '02','name' => 'Candilejos'],
                                            ['code' => '03','name' => 'Central'],
                                            ['code' => '04','name' => 'Paraíso'],
                                            ['code' => '05','name' => 'San Mateo'],
                                            ['code' => '50','name' => 'El Salitre (Las Ramas)'],
                                            ['code' => '51','name' => 'Gral. Vernaza (Dos Esteros)'],
                                            ['code' => '52','name' => 'La Victoria (ñauza)'],
                                            ['code' => '53','name' => 'Junquillal']]],

        ['code' => '20','name' => 'San Jacinto De Yaguachi', 'parishes' => [
                                            ['code' => '50','name' => 'San Jacinto De Yaguachi'],
                                            ['code' => '51','name' => 'Crnel. Lorenzo De Garaicoa (Pedregal)'],
                                            ['code' => '52','name' => 'Crnel. Marcelino Maridueña (San Carlos)'],
                                            ['code' => '53','name' => 'Gral. Pedro J. Montero (Boliche)'],
                                            ['code' => '54','name' => 'Simón Bolívar'],
                                            ['code' => '55','name' => 'Yaguachi Viejo (Cone)'],
                                            ['code' => '56','name' => 'Virgen De Fátima']]],

        ['code' => '21','name' => 'Playas', 'parishes' => [
                                            ['code' => '50','name' => 'General Villamil (Playas)']]],

        ['code' => '22','name' => 'Simón Bolívar', 'parishes' => [
                                            ['code' => '50','name' => 'Simón Bolívar'],
                                            ['code' => '51','name' => 'Crnel.Lorenzo De Garaicoa (Pedregal)']]],

        ['code' => '23','name' => 'Coronel Marcelino Maridueña', 'parishes' => [
                                            ['code' => '50','name' => 'Coronel Marcelino Maridueña (San Carlos)']]],

        ['code' => '24','name' => 'Lomas De Sargentillo', 'parishes' => [
                                            ['code' => '50','name' => 'Lomas De Sargentillo'],
                                            ['code' => '51','name' => 'Isidro Ayora (Soledad)']]],

        ['code' => '25','name' => 'Nobol', 'parishes' => [
                                            ['code' => '50','name' => 'Narcisa De Jesús']]],

        ['code' => '27','name' => 'General Antonio Elizalde (Bucay)', 'parishes' => [
                                            ['code' => '50','name' => 'General Antonio Elizalde (Bucay)']]],

        ['code' => '28','name' => 'Isidro Ayora Moreno', 'parishes' => [
                                            ['code' => '50','name' => 'Isidro Ayora']]],
        ]

     ));


    Province::create(array(
      'code' => '10', 
      'name' => 'Imbabura', 
      'cantons' => [
        ['code' => '01','name' => 'Ibarra', 'parishes' => [
                                            ['code' => '01','name' => 'Caranqui'],
                                            ['code' => '02','name' => 'Guayaquil De Alpachaca'],
                                            ['code' => '03','name' => 'Sagrario'],
                                            ['code' => '04','name' => 'San Francisco'],
                                            ['code' => '05','name' => 'La Dolorosa Del Priorato'],
                                            ['code' => '50','name' => 'San Miguel De Ibarra'],
                                            ['code' => '51','name' => 'Ambuquí'],
                                            ['code' => '52','name' => 'Angochagua'],
                                            ['code' => '53','name' => 'Carolina'],
                                            ['code' => '54','name' => 'La Esperanza'],
                                            ['code' => '55','name' => 'Lita'],
                                            ['code' => '56','name' => 'Salinas'],
                                            ['code' => '57','name' => 'San Antonio']]],

        ['code' => '02','name' => 'Antonio Ante', 'parishes' => [
                                            ['code' => '01','name' => 'Andrade Marín (Lourdes)'],
                                            ['code' => '02','name' => 'Atuntaqui'],
                                            ['code' => '50','name' => 'Atuntaqui'],
                                            ['code' => '51','name' => 'Imbaya (San Luis De Cobuendo)'],
                                            ['code' => '52','name' => 'San Francisco De Natabuela'],
                                            ['code' => '53','name' => 'San José De Chaltura'],
                                            ['code' => '54','name' => 'San Roque']]],

        ['code' => '03','name' => 'Cotacachi', 'parishes' => [
                                            ['code' => '01','name' => 'Sagrario'],
                                            ['code' => '02','name' => 'San Francisco'],
                                            ['code' => '50','name' => 'Cotacachi'],
                                            ['code' => '51','name' => 'Apuela'],
                                            ['code' => '52','name' => 'García Moreno (Llurimagua)'],
                                            ['code' => '53','name' => 'Imantag'],
                                            ['code' => '54','name' => 'Peñaherrera'],
                                            ['code' => '55','name' => 'Plaza Gutiérrez (Calvario)'],
                                            ['code' => '56','name' => 'Quiroga'],
                                            ['code' => '57','name' => '6 De Julio De Cuellaje (Cab. En Cuellaje)'],
                                            ['code' => '58','name' => 'Vacas Galindo (El Churo) (Cab.En San Miguel Alto']]],

        ['code' => '04','name' => 'Otavalo', 'parishes' => [
                                            ['code' => '01','name' => 'Jordán'],
                                            ['code' => '02','name' => 'San Luis'],
                                            ['code' => '50','name' => 'Otavalo'],
                                            ['code' => '51','name' => 'Dr. Miguel Egas Cabezas (Peguche)'],
                                            ['code' => '52','name' => 'Eugenio Espejo (Calpaquí)'],
                                            ['code' => '53','name' => 'González Suárez'],
                                            ['code' => '54','name' => 'Pataquí'],
                                            ['code' => '55','name' => 'San José De Quichinche'],
                                            ['code' => '56','name' => 'San Juan De Ilumán'],
                                            ['code' => '57','name' => 'San Pablo'],
                                            ['code' => '58','name' => 'San Rafael'],
                                            ['code' => '59','name' => 'Selva Alegre (Cab.En San Miguel De Pamplona)']]],

        ['code' => '05','name' => 'Pimampiro', 'parishes' => [
                                            ['code' => '50','name' => 'Pimampiro'],
                                            ['code' => '51','name' => 'Chugá'],
                                            ['code' => '52','name' => 'Mariano Acosta'],
                                            ['code' => '53','name' => 'San Francisco De Sigsipamba']]],

        ['code' => '06','name' => 'San Miguel De Urcuquí', 'parishes' => [
                                            ['code' => '50','name' => 'Urcuquí Cabecera Cantonal'],
                                            ['code' => '51','name' => 'Cahuasquí'],
                                            ['code' => '52','name' => 'La Merced De Buenos Aires'],
                                            ['code' => '53','name' => 'Pablo Arenas'],
                                            ['code' => '54','name' => 'San Blas'],
                                            ['code' => '55','name' => 'Tumbabiro']]],
        ]
     ));


    Province::create(array(
      'code' => '11', 
      'name' => 'Loja', 
      'cantons' => [
        ['code' => '01','name' => 'Loja', 'parishes' => [
                                            ['code' => '01','name' => 'El Sagrario'],
                                            ['code' => '02','name' => 'San Sebastián'],
                                            ['code' => '03','name' => 'Sucre'],
                                            ['code' => '04','name' => 'Valle'],
                                            ['code' => '50','name' => 'Loja'],
                                            ['code' => '51','name' => 'Chantaco'],
                                            ['code' => '52','name' => 'Chuquiribamba'],
                                            ['code' => '53','name' => 'El Cisne'],
                                            ['code' => '54','name' => 'Gualel'],
                                            ['code' => '55','name' => 'Jimbilla'],
                                            ['code' => '56','name' => 'Malacatos (Valladolid)'],
                                            ['code' => '57','name' => 'San Lucas'],
                                            ['code' => '58','name' => 'San Pedro De Vilcabamba'],
                                            ['code' => '59','name' => 'Santiago'],
                                            ['code' => '60','name' => 'Taquil (Miguel Riofrío)'],
                                            ['code' => '61','name' => 'Vilcabamba (Victoria)'],
                                            ['code' => '62','name' => 'Yangana (Arsenio Castillo)'],
                                            ['code' => '63','name' => 'Quinara']]],

        ['code' => '02','name' => 'Calvas', 'parishes' => [
                                            ['code' => '01','name' => 'Cariamanga'],
                                            ['code' => '02','name' => 'Chile'],
                                            ['code' => '03','name' => 'San Vicente'],
                                            ['code' => '50','name' => 'Cariamanga'],
                                            ['code' => '51','name' => 'Colaisaca'],
                                            ['code' => '52','name' => 'El Lucero'],
                                            ['code' => '53','name' => 'Utuana'],
                                            ['code' => '54','name' => 'Sanguillín']]],

        ['code' => '03','name' => 'Catamayo', 'parishes' => [
                                            ['code' => '01','name' => 'Catamayo'],
                                            ['code' => '02','name' => 'San José'],
                                            ['code' => '50','name' => 'Catamayo (La Toma)'],
                                            ['code' => '51','name' => 'El Tambo'],
                                            ['code' => '52','name' => 'Guayquichuma'],
                                            ['code' => '53','name' => 'San Pedro De La Bendita'],
                                            ['code' => '54','name' => 'Zambi']]],

        ['code' => '04','name' => 'Celica', 'parishes' => [
                                            ['code' => '50','name' => 'Celica'],
                                            ['code' => '51','name' => 'Cruzpamba (Cab. En Carlos Bustamante)'],
                                            ['code' => '52','name' => 'Chaquinal'],
                                            ['code' => '53','name' => '12 De Diciembre (Cab. En Achiotes)'],
                                            ['code' => '54','name' => 'Pindal (Federico Páez)'],
                                            ['code' => '55','name' => 'Pozul (San Juan De Pozul)'],
                                            ['code' => '56','name' => 'Sabanilla'],
                                            ['code' => '57','name' => 'Tnte. Maximiliano Rodríguez Loaiza']]],

        ['code' => '05','name' => 'Chaguarpamba', 'parishes' => [
                                            ['code' => '50','name' => 'Chaguarpamba'],
                                            ['code' => '51','name' => 'Buenavista'],
                                            ['code' => '52','name' => 'El Rosario'],
                                            ['code' => '53','name' => 'Santa Rufina'],
                                            ['code' => '54','name' => 'Amarillos']]],

        ['code' => '06','name' => 'Espíndola', 'parishes' => [
                                            ['code' => '50','name' => 'Amaluza'],
                                            ['code' => '51','name' => 'Bellavista'],
                                            ['code' => '52','name' => 'Jimbura'],
                                            ['code' => '53','name' => 'Santa Teresita'],
                                            ['code' => '54','name' => '27 De Abril (Cab. En La Naranja)'],
                                            ['code' => '55','name' => 'El Ingenio'],
                                            ['code' => '56','name' => 'El Airo']]],

        ['code' => '07','name' => 'Gonzanamá', 'parishes' => [
                                            ['code' => '50','name' => 'Gonzanamá'],
                                            ['code' => '51','name' => 'Changaimina (La Libertad)'],
                                            ['code' => '52','name' => 'Fundochamba'],
                                            ['code' => '53','name' => 'Nambacola'],
                                            ['code' => '54','name' => 'Purunuma (Eguiguren)'],
                                            ['code' => '55','name' => 'Quilanga (La Paz)'],
                                            ['code' => '56','name' => 'Sacapalca'],
                                            ['code' => '57','name' => 'San Antonio De Las Aradas (Cab. En Las Aradas)']]],

        ['code' => '08','name' => 'Macará', 'parishes' => [
                                            ['code' => '01','name' => 'General Eloy Alfaro (San Sebastián)'],
                                            ['code' => '02','name' => 'Macará (Manuel Enrique Rengel Suquilanda)'],
                                            ['code' => '50','name' => 'Macará'],
                                            ['code' => '51','name' => 'Larama'],
                                            ['code' => '52','name' => 'La Victoria'],
                                            ['code' => '53','name' => 'Sabiango (La Capilla)']]],

        ['code' => '09','name' => 'Paltas', 'parishes' => [
                                            ['code' => '01','name' => 'Catacocha'],
                                            ['code' => '02','name' => 'Lourdes'],
                                            ['code' => '50','name' => 'Catacocha'],
                                            ['code' => '51','name' => 'Cangonamá'],
                                            ['code' => '52','name' => 'Guachanamá'],
                                            ['code' => '53','name' => 'La Tingue'],
                                            ['code' => '54','name' => 'Lauro Guerrero'],
                                            ['code' => '55','name' => 'Olmedo (Santa Bárbara)'],
                                            ['code' => '56','name' => 'Orianga'],
                                            ['code' => '57','name' => 'San Antonio'],
                                            ['code' => '58','name' => 'Casanga'],
                                            ['code' => '59','name' => 'Yamana']]],

        ['code' => '10','name' => 'Puyango', 'parishes' => [
                                            ['code' => '50','name' => 'Alamor'],
                                            ['code' => '51','name' => 'Ciano'],
                                            ['code' => '52','name' => 'El Arenal'],
                                            ['code' => '53','name' => 'El Limo (Mariana De Jesús)'],
                                            ['code' => '54','name' => 'Mercadillo'],
                                            ['code' => '55','name' => 'Vicentino']]],

        ['code' => '11','name' => 'Saraguro', 'parishes' => [
                                            ['code' => '50','name' => 'Saraguro'],
                                            ['code' => '51','name' => 'El Paraíso De Celén'],
                                            ['code' => '52','name' => 'El Tablón'],
                                            ['code' => '53','name' => 'Lluzhapa'],
                                            ['code' => '54','name' => 'Manú'],
                                            ['code' => '55','name' => 'San Antonio De Qumbe (Cumbe)'],
                                            ['code' => '56','name' => 'San Pablo De Tenta'],
                                            ['code' => '57','name' => 'San Sebastián De Yúluc'],
                                            ['code' => '58','name' => 'Selva Alegre'],
                                            ['code' => '59','name' => 'Urdaneta (Paquishapa)'],
                                            ['code' => '60','name' => 'Sumaypamba']]],

        ['code' => '12','name' => 'Sozoranga', 'parishes' => [
                                            ['code' => '50','name' => 'Sozoranga'],
                                            ['code' => '51','name' => 'Nueva Fátima'],
                                            ['code' => '52','name' => 'Tacamoros']]],

        ['code' => '13','name' => 'Zapotillo', 'parishes' => [
                                            ['code' => '50','name' => 'Zapotillo'],
                                            ['code' => '51','name' => 'Mangahurco (Cazaderos)'],
                                            ['code' => '52','name' => 'Garzareal'],
                                            ['code' => '53','name' => 'Limones'],
                                            ['code' => '54','name' => 'Paletillas'],
                                            ['code' => '55','name' => 'Bolaspamba']]],

        ['code' => '14','name' => 'Pindal', 'parishes' => [
                                            ['code' => '50','name' => 'Pindal'],
                                            ['code' => '51','name' => 'Chaquinal'],
                                            ['code' => '52','name' => '12 De Diciembre (Cab.En Achiotes)'],
                                            ['code' => '53','name' => 'Milagros']]],

        ['code' => '15','name' => 'Quilanga', 'parishes' => [
                                            ['code' => '50','name' => 'Quilanga'],
                                            ['code' => '51','name' => 'Fundochamba'],
                                            ['code' => '52','name' => 'San Antonio De Las Aradas (Cab. En Las Aradas)']]],

        ['code' => '16','name' => 'Olmedo', 'parishes' => [
                                            ['code' => '50','name' => 'Olmedo'],
                                            ['code' => '51','name' => 'La Tingue']]],
        ]
     ));


    Province::create(array(
      'code' => '12', 
      'name' => 'Los Ríos', 
      'cantons' => [
        ['code' => '01','name' => 'Babahoyo', 'parishes' => [
                                            ['code' => '01','name' => 'Clemente Baquerizo'],
                                            ['code' => '02','name' => 'Dr. Camilo Ponce'],
                                            ['code' => '03','name' => 'Barreiro'],
                                            ['code' => '04','name' => 'El Salto'],
                                            ['code' => '50','name' => 'Babahoyo'],
                                            ['code' => '51','name' => 'Barreiro (Santa Rita)'],
                                            ['code' => '52','name' => 'Caracol'],
                                            ['code' => '53','name' => 'Febres Cordero (Las Juntas)'],
                                            ['code' => '54','name' => 'Pimocha'],
                                            ['code' => '55','name' => 'La Unión']]],

        ['code' => '02','name' => 'Baba', 'parishes' => [
                                            ['code' => '50','name' => 'Baba'],
                                            ['code' => '51','name' => 'Guare'],
                                            ['code' => '52','name' => 'Isla De Bejucal']]],

        ['code' => '03','name' => 'Montalvo', 'parishes' => [
                                            ['code' => '50','name' => 'Montalvo']]],

        ['code' => '04','name' => 'Puebloviejo', 'parishes' => [
                                            ['code' => '50','name' => 'Puebloviejo'],
                                            ['code' => '51','name' => 'Puerto Pechiche'],
                                            ['code' => '52','name' => 'San Juan']]],

        ['code' => '05','name' => 'Quevedo', 'parishes' => [
                                            ['code' => '01','name' => 'Quevedo'],
                                            ['code' => '02','name' => 'San Camilo'],
                                            ['code' => '03','name' => 'San José'],
                                            ['code' => '04','name' => 'Guayacán'],
                                            ['code' => '05','name' => 'Nicolás Infante Díaz'],
                                            ['code' => '06','name' => 'San Cristóbal'],
                                            ['code' => '07','name' => 'Siete De Octubre'],
                                            ['code' => '08','name' => '24 De Mayo'],
                                            ['code' => '09','name' => 'Venus Del Río Quevedo'],
                                            ['code' => '10','name' => 'Viva Alfaro'],
                                            ['code' => '50','name' => 'Quevedo'],
                                            ['code' => '51','name' => 'Buena Fé'],
                                            ['code' => '52','name' => 'Mocache'],
                                            ['code' => '53','name' => 'San Carlos'],
                                            ['code' => '54','name' => 'Valencia'],
                                            ['code' => '55','name' => 'La Esperanza']]],

        ['code' => '06','name' => 'Urdaneta', 'parishes' => [
                                            ['code' => '50','name' => 'Catarama'],
                                            ['code' => '51','name' => 'Ricaurte']]],

        ['code' => '07','name' => 'Ventanas', 'parishes' => [
                                            ['code' => '01','name' => '10 De Noviembre'],
                                            ['code' => '50','name' => 'Ventanas'],
                                            ['code' => '51','name' => 'Quinsaloma'],
                                            ['code' => '52','name' => 'Zapotal'],
                                            ['code' => '53','name' => 'Chacarita'],
                                            ['code' => '54','name' => 'Los Ángeles']]],

        ['code' => '08','name' => 'Vinces', 'parishes' => [
                                            ['code' => '50','name' => 'Vinces'],
                                            ['code' => '51','name' => 'Antonio Sotomayor (Cab. En Playas De Vinces)'],
                                            ['code' => '52','name' => 'Palenque']]],

        ['code' => '09','name' => 'Palenque', 'parishes' => [
                                            ['code' => '50','name' => 'Palenque']]],

        ['code' => '10','name' => 'Buena Fé', 'parishes' => [
                                            ['code' => '01','name' => 'San Jacinto De Buena Fé'],
                                            ['code' => '02','name' => '7 De Agosto'],
                                            ['code' => '03','name' => '11 De Octubre'],
                                            ['code' => '50','name' => 'San Jacinto De Buena Fé'],
                                            ['code' => '51','name' => 'Patricia Pilar']]],

        ['code' => '11','name' => 'Valencia', 'parishes' => [
                                            ['code' => '50','name' => 'Valencia']]],

        ['code' => '12','name' => 'MocacheQuinsaloma', 'parishes' => [
                                            ['code' => '50','name' => 'Mocache']]],

        ['code' => '13','name' => 'Quinsaloma', 'parishes' => [
                                            ['code' => '50','name' => 'Quinsaloma']]],
        ]

     ));


    Province::create(array(
      'code' => '13', 
      'name' => 'Manabí', 
      'cantons' => [
        ['code' => '01','name' => 'Portoviejo', 'parishes' => [
                                            ['code' => '01','name' => 'Portoviejo'],
                                            ['code' => '02','name' => '12 De Marzo'],
                                            ['code' => '03','name' => 'Colón'],
                                            ['code' => '04','name' => 'Picoazá'],
                                            ['code' => '05','name' => 'San Pablo'],
                                            ['code' => '06','name' => 'Andrés De Vera'],
                                            ['code' => '07','name' => 'Francisco Pacheco'],
                                            ['code' => '08','name' => '18 De Octubre'],
                                            ['code' => '09','name' => 'Simón Bolívar'],
                                            ['code' => '50','name' => 'Portoviejo'],
                                            ['code' => '51','name' => 'Abdón Calderón (San Francisco)'],
                                            ['code' => '52','name' => 'Alhajuela (Bajo Grande)'],
                                            ['code' => '53','name' => 'Crucita'],
                                            ['code' => '54','name' => 'Pueblo Nuevo'],
                                            ['code' => '55','name' => 'Riochico (Río Chico)'],
                                            ['code' => '56','name' => 'San Plácido'],
                                            ['code' => '57','name' => 'Chirijos']]],

        ['code' => '02','name' => 'Bolívar', 'parishes' => [
                                            ['code' => '50','name' => 'Calceta'],
                                            ['code' => '51','name' => 'Membrillo'],
                                            ['code' => '52','name' => 'Quiroga']]],

        ['code' => '03','name' => 'Chone', 'parishes' => [
                                            ['code' => '01','name' => 'Chone'],
                                            ['code' => '02','name' => 'Santa Rita'],
                                            ['code' => '50','name' => 'Chone'],
                                            ['code' => '51','name' => 'Boyacá'],
                                            ['code' => '52','name' => 'Canuto'],
                                            ['code' => '53','name' => 'Convento'],
                                            ['code' => '54','name' => 'Chibunga'],
                                            ['code' => '55','name' => 'Eloy Alfaro'],
                                            ['code' => '56','name' => 'Ricaurte'],
                                            ['code' => '57','name' => 'San Antonio']]],

        ['code' => '04','name' => 'El Carmen', 'parishes' => [
                                            ['code' => '01','name' => 'El Carmen'],
                                            ['code' => '02','name' => '4 De Diciembre'],
                                            ['code' => '50','name' => 'El Carmen'],
                                            ['code' => '51','name' => 'Wilfrido Loor Moreira (Maicito)'],
                                            ['code' => '52','name' => 'San Pedro De Suma']]],

        ['code' => '05','name' => 'Flavio', 'parishes' => [
                                            ['code' => '50','name' => 'Flavio Alfaro'],
                                            ['code' => '51','name' => 'San Francisco De Novillo (Cab. En'],
                                            ['code' => '52','name' => 'Zapallo']]],

        ['code' => '06','name' => 'Alfaro', 'parishes' => [
                                            ['code' => '01','name' => 'Dr. Miguel Morán Lucio'],
                                            ['code' => '02','name' => 'Manuel Inocencio Parrales Y Guale'],
                                            ['code' => '03','name' => 'San Lorenzo De Jipijapa'],
                                            ['code' => '50','name' => 'Jipijapa'],
                                            ['code' => '51','name' => 'América'],
                                            ['code' => '52','name' => 'El Anegado (Cab. En Eloy Alfaro)'],
                                            ['code' => '53','name' => 'Julcuy'],
                                            ['code' => '54','name' => 'La Unión'],
                                            ['code' => '55','name' => 'Machalilla'],
                                            ['code' => '56','name' => 'Membrillal'],
                                            ['code' => '57','name' => 'Pedro Pablo Gómez'],
                                            ['code' => '58','name' => 'Puerto De Cayo'],
                                            ['code' => '59','name' => 'Puerto López']]],

        ['code' => '07','name' => 'Jipijapa', 'parishes' => [
                                            ['code' => '50','name' => 'Junín']]],

        ['code' => '08','name' => 'Junín', 'parishes' => [
                                            ['code' => '01','name' => 'Los Esteros'],
                                            ['code' => '02','name' => 'Manta'],
                                            ['code' => '03','name' => 'San Mateo'],
                                            ['code' => '04','name' => 'Tarqui'],
                                            ['code' => '05','name' => 'Eloy Alfaro'],
                                            ['code' => '50','name' => 'Manta'],
                                            ['code' => '51','name' => 'San Lorenzo'],
                                            ['code' => '52','name' => 'Santa Marianita (Boca De Pacoche)']]],

        ['code' => '09','name' => 'Manta', 'parishes' => [
                                            ['code' => '01','name' => 'Anibal San Andrés'],
                                            ['code' => '02','name' => 'Montecristi'],
                                            ['code' => '03','name' => 'El Colorado'],
                                            ['code' => '04','name' => 'General Eloy Alfaro'],
                                            ['code' => '05','name' => 'Leonidas Proaño'],
                                            ['code' => '50','name' => 'Montecristi'],
                                            ['code' => '51','name' => 'Jaramijó'],
                                            ['code' => '52','name' => 'La Pila']]],

        ['code' => '10','name' => 'Montecristi', 'parishes' => [
                                            ['code' => '50','name' => 'Paján'],
                                            ['code' => '51','name' => 'Campozano (La Palma De Paján)'],
                                            ['code' => '52','name' => 'Cascol'],
                                            ['code' => '53','name' => 'Guale'],
                                            ['code' => '54','name' => 'Lascano']]],

        ['code' => '11','name' => 'Paján', 'parishes' => [
                                            ['code' => '50','name' => 'Pichincha'],
                                            ['code' => '51','name' => 'Barraganete'],
                                            ['code' => '52','name' => 'San Sebastián']]],

        ['code' => '12','name' => 'Pichincha', 'parishes' => [
                                            ['code' => '50','name' => 'Rocafuerte']]],

        ['code' => '13','name' => 'Rocafuerte', 'parishes' => [
                                            ['code' => '01','name' => 'Santa Ana'],
                                            ['code' => '02','name' => 'Lodana'],
                                            ['code' => '50','name' => 'Santa Ana De Vuelta Larga'],
                                            ['code' => '51','name' => 'Ayacucho'],
                                            ['code' => '52','name' => 'Honorato Vásquez (Cab. En Vásquez)'],
                                            ['code' => '53','name' => 'La Unión'],
                                            ['code' => '54','name' => 'Olmedo'],
                                            ['code' => '55','name' => 'San Pablo (Cab. En Pueblo Nuevo)']]],

        ['code' => '14','name' => 'Santa Ana', 'parishes' => [
                                            ['code' => '01','name' => 'Bahía De Caráquez'],
                                            ['code' => '02','name' => 'Leonidas Plaza Gutiérrez'],
                                            ['code' => '50','name' => 'Bahía De Caráquez'],
                                            ['code' => '51','name' => 'Canoa'],
                                            ['code' => '52','name' => 'Cojimíes'],
                                            ['code' => '53','name' => 'Charapotó'],
                                            ['code' => '54','name' => '10 De Agosto'],
                                            ['code' => '55','name' => 'Jama'],
                                            ['code' => '56','name' => 'Pedernales'],
                                            ['code' => '57','name' => 'San Isidro'],
                                            ['code' => '58','name' => 'San Vicente']]],

        ['code' => '15','name' => 'Sucre', 'parishes' => [
                                            ['code' => '50','name' => 'Tosagua'],
                                            ['code' => '51','name' => 'Bachillero'],
                                            ['code' => '52','name' => 'Angel Pedro Giler (La Estancilla)']]],

        ['code' => '16','name' => 'Tosagua', 'parishes' => [
                                            ['code' => '50','name' => 'Sucre'],
                                            ['code' => '51','name' => 'Bellavista'],
                                            ['code' => '52','name' => 'Noboa'],
                                            ['code' => '53','name' => 'Arq. Sixto Durán Ballén']]],

        ['code' => '17','name' => '24 De Mayo', 'parishes' => [
                                            ['code' => '50','name' => 'Pedernales'],
                                            ['code' => '51','name' => 'Cojimíes'],
                                            ['code' => '52','name' => '10 De Agosto'],
                                            ['code' => '53','name' => 'Atahualpa']]],

        ['code' => '18','name' => 'Pedernales', 'parishes' => [
                                            ['code' => '50','name' => 'Olmedo']]],

        ['code' => '19','name' => 'Olmedo', 'parishes' => [
                                            ['code' => '50','name' => 'Puerto López'],
                                            ['code' => '51','name' => 'Machalilla'],
                                            ['code' => '52','name' => 'Salango']]],

        ['code' => '20','name' => 'Puerto López', 'parishes' => [
                                            ['code' => '50','name' => 'Jama']]],

        ['code' => '21','name' => 'Jama Jaramijó', 'parishes' => [
                                            ['code' => '50','name' => 'Jaramijó']]],

        ['code' => '22','name' => 'San Vicente', 'parishes' => [
                                            ['code' => '50','name' => 'San Vicente'],
                                            ['code' => '51','name' => 'Canoa']]],
    ]
     ));


    Province::create(array(
      'code' => '14', 
      'name' => 'Morona Santiago', 
      'cantons' => [
        ['code' => '01','name' => 'Morona', 'parishes' => [
                                            ['code' => '50','name' => 'Macas'],
                                            ['code' => '51','name' => 'Alshi (Cab. En 9 De Octubre)'],
                                            ['code' => '52','name' => 'Chiguaza'],
                                            ['code' => '53','name' => 'General Proaño'],
                                            ['code' => '54','name' => 'Huasaga (Cab.En Wampuik)'],
                                            ['code' => '55','name' => 'Macuma'],
                                            ['code' => '56','name' => 'San Isidro'],
                                            ['code' => '57','name' => 'Sevilla Don Bosco'],
                                            ['code' => '58','name' => 'Sinaí'],
                                            ['code' => '59','name' => 'Taisha'],
                                            ['code' => '60','name' => 'Zuña (Zúñac)'],
                                            ['code' => '61','name' => 'Tuutinentza'],
                                            ['code' => '62','name' => 'Cuchaentza'],
                                            ['code' => '63','name' => 'San José De Morona'],
                                            ['code' => '64','name' => 'Río Blanco']]],

        ['code' => '02','name' => 'Gualaquiza', 'parishes' => [
                                            ['code' => '01','name' => 'Gualaquiza'],
                                            ['code' => '02','name' => 'Mercedes Molina'],
                                            ['code' => '50','name' => 'Gualaquiza'],
                                            ['code' => '51','name' => 'Amazonas (Rosario De Cuyes)'],
                                            ['code' => '52','name' => 'Bermejos'],
                                            ['code' => '53','name' => 'Bomboiza'],
                                            ['code' => '54','name' => 'ChigÜInda'],
                                            ['code' => '55','name' => 'El Rosario'],
                                            ['code' => '56','name' => 'Nueva Tarqui'],
                                            ['code' => '57','name' => 'San Miguel De Cuyes'],
                                            ['code' => '58','name' => 'El Ideal']]],

        ['code' => '03','name' => 'Limón Indanza', 'parishes' => [
                                            ['code' => '50','name' => 'General Leonidas Plaza Gutiérrez (Limón)'],
                                            ['code' => '51','name' => 'Indanza'],
                                            ['code' => '52','name' => 'Pan De Azúcar'],
                                            ['code' => '53','name' => 'San Antonio (Cab. En San Antonio Centro'],
                                            ['code' => '54','name' => 'San Carlos De Limón (San Carlos Del'],
                                            ['code' => '55','name' => 'San Juan Bosco'],
                                            ['code' => '56','name' => 'San Miguel De Conchay'],
                                            ['code' => '57','name' => 'Santa Susana De Chiviaza (Cab. En Chiviaza)'],
                                            ['code' => '58','name' => 'Yunganza (Cab. En El Rosario)']]],

        ['code' => '04','name' => 'Palora', 'parishes' => [
                                            ['code' => '50','name' => 'Palora (Metzera)'],
                                            ['code' => '51','name' => 'Arapicos'],
                                            ['code' => '52','name' => 'Cumandá (Cab. En Colonia Agrícola Sevilla Del Oro)'],
                                            ['code' => '53','name' => 'Huamboya'],
                                            ['code' => '54','name' => 'Sangay (Cab. En Nayamanaca)']]],

        ['code' => '05','name' => 'Santiago', 'parishes' => [
                                            ['code' => '50','name' => 'Santiago De Méndez'],
                                            ['code' => '51','name' => 'Copal'],
                                            ['code' => '52','name' => 'Chupianza'],
                                            ['code' => '53','name' => 'Patuca'],
                                            ['code' => '54','name' => 'San Luis De El Acho (Cab. En El Acho)'],
                                            ['code' => '55','name' => 'Santiago'],
                                            ['code' => '56','name' => 'Tayuza'],
                                            ['code' => '57','name' => 'San Francisco De Chinimbimi']]],

        ['code' => '06','name' => 'Sucúa', 'parishes' => [
                                            ['code' => '50','name' => 'Sucúa'],
                                            ['code' => '51','name' => 'Asunción'],
                                            ['code' => '52','name' => 'Huambi'],
                                            ['code' => '53','name' => 'Logroño'],
                                            ['code' => '54','name' => 'Yaupi'],
                                            ['code' => '55','name' => 'Santa Marianita De Jesús']]],

        ['code' => '07','name' => 'Huamboya', 'parishes' => [
                                            ['code' => '50','name' => 'Huamboya'],
                                            ['code' => '51','name' => 'Chiguaza'],
                                            ['code' => '52','name' => 'Pablo Sexto']]],

        ['code' => '08','name' => 'San Juan Bosco', 'parishes' => [
                                            ['code' => '50','name' => 'San Juan Bosco'],
                                            ['code' => '51','name' => 'Pan De Azúcar'],
                                            ['code' => '52','name' => 'San Carlos De Limón'],
                                            ['code' => '53','name' => 'San Jacinto De Wakambeis'],
                                            ['code' => '54','name' => 'Santiago De Pananza']]],

        ['code' => '09','name' => 'Taisha', 'parishes' => [
                                            ['code' => '50','name' => 'Taisha'],
                                            ['code' => '51','name' => 'Huasaga (Cab. En Wampuik)'],
                                            ['code' => '52','name' => 'Macuma'],
                                            ['code' => '53','name' => 'Tuutinentza'],
                                            ['code' => '54','name' => 'Pumpuentsa']]],

        ['code' => '10','name' => 'Logroño', 'parishes' => [
                                            ['code' => '50','name' => 'Logroño'],
                                            ['code' => '51','name' => 'Yaupi'],
                                            ['code' => '52','name' => 'Shimpis']]],

        ['code' => '11','name' => 'Pablo Sexto', 'parishes' => [
                                            ['code' => '50','name' => 'Pablo Sexto']]],

        ['code' => '12','name' => 'Tiwintza', 'parishes' => [
                                            ['code' => '50','name' => 'Santiago'],
                                            ['code' => '51','name' => 'San José De Morona']]],
    ]
     ));


    Province::create(array(
      'code' => '15', 
      'name' => 'Napo', 
      'cantons' => [
        ['code' => '01','name' => 'Tena', 'parishes' => [
                                            ['code' => '50','name' => 'Tena'],
                                            ['code' => '51','name' => 'Ahuano'],
                                            ['code' => '52','name' => 'Carlos Julio Arosemena Tola (Zatza-Yacu)'],
                                            ['code' => '53','name' => 'Chontapunta'],
                                            ['code' => '54','name' => 'Pano'],
                                            ['code' => '55','name' => 'Puerto Misahualli'],
                                            ['code' => '56','name' => 'Puerto Napo'],
                                            ['code' => '57','name' => 'Tálag'],
                                            ['code' => '58','name' => 'San Juan De Muyuna']]],

        ['code' => '03','name' => 'Archidona', 'parishes' => [
                                            ['code' => '50','name' => 'Archidona'],
                                            ['code' => '51','name' => 'Avila'],
                                            ['code' => '52','name' => 'Cotundo'],
                                            ['code' => '53','name' => 'Loreto'],
                                            ['code' => '54','name' => 'San Pablo De Ushpayacu'],
                                            ['code' => '55','name' => 'Puerto Murialdo']]],

        ['code' => '04','name' => 'El Chaco', 'parishes' => [
                                            ['code' => '50','name' => 'El Chaco'],
                                            ['code' => '51','name' => 'Gonzalo DíAz De Pineda (El Bombón)'],
                                            ['code' => '52','name' => 'Linares'],
                                            ['code' => '53','name' => 'Oyacachi'],
                                            ['code' => '54','name' => 'Santa Rosa'],
                                            ['code' => '55','name' => 'Sardinas']]],

        ['code' => '05','name' => 'Quijos', 'parishes' => [
                                            ['code' => '50','name' => 'Baeza'],
                                            ['code' => '51','name' => 'Cosanga'],
                                            ['code' => '52','name' => 'Cuyuja'],
                                            ['code' => '53','name' => 'Papallacta'],
                                            ['code' => '54','name' => 'San Francisco De Borja (Virgilio Dávila)'],
                                            ['code' => '55','name' => 'San José Del Payamino'],
                                            ['code' => '56','name' => 'Sumaco']]],

        ['code' => '09','name' => 'Carlos Julio Arosemena Tola', 'parishes' => [
                                            ['code' => '50','name' => 'Carlos Julio Arosemena Tola']]],
      ]
     ));


    Province::create(array(
      'code' => '16', 
      'name' => 'Pastaza', 
      'cantons' => [
        ['code' => '01','name' => 'Pastaza', 'parishes' => [
                                            ['code' => '50','name' => 'Puyo'],
                                            ['code' => '51','name' => 'Arajuno'],
                                            ['code' => '52','name' => 'Canelos'],
                                            ['code' => '53','name' => 'Curaray'],
                                            ['code' => '54','name' => 'Diez De Agosto'],
                                            ['code' => '55','name' => 'Fátima'],
                                            ['code' => '56','name' => 'Montalvo (Andoas)'],
                                            ['code' => '57','name' => 'Pomona'],
                                            ['code' => '58','name' => 'Río Corrientes'],
                                            ['code' => '59','name' => 'Río Tigre'],
                                            ['code' => '60','name' => 'Santa Clara'],
                                            ['code' => '61','name' => 'Sarayacu'],
                                            ['code' => '62','name' => 'Simón Bolívar (Cab. En Mushullacta)'],
                                            ['code' => '63','name' => 'Tarqui'],
                                            ['code' => '64','name' => 'Teniente Hugo Ortiz'],
                                            ['code' => '65','name' => 'Veracruz (Indillama) (Cab. En Indillama)'],
                                            ['code' => '66','name' => 'El Triunfo']]],

        ['code' => '02','name' => 'Mera', 'parishes' => [
                                            ['code' => '50','name' => 'Mera'],
                                            ['code' => '51','name' => 'Madre Tierra'],
                                            ['code' => '52','name' => 'Shell']]],

        ['code' => '03','name' => 'Santa Clara', 'parishes' => [
                                            ['code' => '50','name' => 'Santa Clara'],
                                            ['code' => '51','name' => 'San José']]],

        ['code' => '04','name' => 'Arajuno', 'parishes' => [
                                            ['code' => '50','name' => 'Arajuno'],
                                            ['code' => '51','name' => 'Curaray']]],
    ]
     ));


    Province::create(array(
      'code' => '17', 
      'name' => 'Pichincha', 
      'cantons' => [
        ['code' => '01','name' => 'Quito', 'parishes' => [
                                            ['code' => '01','name' => 'Belisario Quevedo'],
                                            ['code' => '02','name' => 'Carcelen'],
                                            ['code' => '03','name' => 'Centro Historico'],
                                            ['code' => '04','name' => 'Cochapamba'],
                                            ['code' => '05','name' => 'Comite Del Pueblo'],
                                            ['code' => '06','name' => 'Cotocollao'],
                                            ['code' => '07','name' => 'Chilibulo'],
                                            ['code' => '08','name' => 'Chillogallo'],
                                            ['code' => '09','name' => 'Chimbacalle'],
                                            ['code' => '10','name' => 'El Condado'],
                                            ['code' => '11','name' => 'Guamani'],
                                            ['code' => '12','name' => 'Inaquito'],
                                            ['code' => '13','name' => 'Itchimbia'],
                                            ['code' => '14','name' => 'Jipijapa'],
                                            ['code' => '15','name' => 'Kennedy'],
                                            ['code' => '16','name' => 'La Argelia'],
                                            ['code' => '17','name' => 'La Concepcion'],
                                            ['code' => '18','name' => 'La Ecuatoriana'],
                                            ['code' => '19','name' => 'La Ferroviaria'],
                                            ['code' => '20','name' => 'La Libertad'],
                                            ['code' => '21','name' => 'La Magdalena'],
                                            ['code' => '22','name' => 'La Mena'],
                                            ['code' => '23','name' => 'Mariscal Sucre'],
                                            ['code' => '24','name' => 'Ponceano'],
                                            ['code' => '25','name' => 'Puengasi'],
                                            ['code' => '26','name' => 'Quitumbe'],
                                            ['code' => '27','name' => 'Rumipamba'],
                                            ['code' => '28','name' => 'San Bartolo'],
                                            ['code' => '29','name' => 'San Isidro Del Inca'],
                                            ['code' => '30','name' => 'San Juan'],
                                            ['code' => '31','name' => 'Solanda'],
                                            ['code' => '32','name' => 'Turubamba'],
                                            ['code' => '51','name' => 'Alangasi'],
                                            ['code' => '52','name' => 'Amaguana'],
                                            ['code' => '53','name' => 'Atahualpa'],
                                            ['code' => '54','name' => 'Calacali'],
                                            ['code' => '55','name' => 'Calderon'],
                                            ['code' => '56','name' => 'Conocoto'],
                                            ['code' => '57','name' => 'Cumbaya'],
                                            ['code' => '58','name' => 'Chavezpamba'],
                                            ['code' => '59','name' => 'Checa'],
                                            ['code' => '60','name' => 'El Quinche'],
                                            ['code' => '61','name' => 'Gualea'],
                                            ['code' => '62','name' => 'Guangopolo'],
                                            ['code' => '64','name' => 'La Merced'],
                                            ['code' => '65','name' => 'Llano Chico'],
                                            ['code' => '66','name' => 'Lloa'],
                                            ['code' => '67','name' => 'Mindo'],
                                            ['code' => '68','name' => 'Nanegal'],
                                            ['code' => '69','name' => 'Nanegalito'],
                                            ['code' => '70','name' => 'Nayon'],
                                            ['code' => '71','name' => 'Nono'],
                                            ['code' => '72','name' => 'Pacto'],
                                            ['code' => '73','name' => 'Pedro Vicente Maldonado'],
                                            ['code' => '74','name' => 'Perucho'],
                                            ['code' => '75','name' => 'Pifo'],
                                            ['code' => '76','name' => 'Pintag'],
                                            ['code' => '77','name' => 'Pomasqui'],
                                            ['code' => '78','name' => 'Puellaro'],
                                            ['code' => '79','name' => 'Puembo'],
                                            ['code' => '80','name' => 'San Antonio'],
                                            ['code' => '81','name' => 'San Jose De Minas'],
                                            ['code' => '82','name' => 'San Miguel De Los Bancos'],
                                            ['code' => '83','name' => 'Tababela'],
                                            ['code' => '84','name' => 'Tumbaco'],
                                            ['code' => '85','name' => 'Yaruqui'],
                                            ['code' => '86','name' => 'Zambiza']]],

        ['code' => '02','name' => 'Cayambe', 'parishes' => [
                                            ['code' => '01','name' => 'Ayora'],
                                            ['code' => '02','name' => 'Cayambe'],
                                            ['code' => '03','name' => 'Juan Montalvo'],
                                            ['code' => '50','name' => 'Cayambe'],
                                            ['code' => '51','name' => 'Ascázubi'],
                                            ['code' => '52','name' => 'Cangahua'],
                                            ['code' => '53','name' => 'Olmedo (Pesillo)'],
                                            ['code' => '54','name' => 'Otón'],
                                            ['code' => '55','name' => 'Santa Rosa De Cuzubamba']]],

        ['code' => '03','name' => 'Mejía', 'parishes' => [
                                            ['code' => '50','name' => 'Machachi'],
                                            ['code' => '51','name' => 'Alóag'],
                                            ['code' => '52','name' => 'Aloasí'],
                                            ['code' => '53','name' => 'Cutuglahua'],
                                            ['code' => '54','name' => 'El Chaupi'],
                                            ['code' => '55','name' => 'Manuel Cornejo Astorga (Tandapi)'],
                                            ['code' => '56','name' => 'Tambillo'],
                                            ['code' => '57','name' => 'Uyumbicho']]],

        ['code' => '04','name' => 'Pedro Moncayo', 'parishes' => [
                                            ['code' => '50','name' => 'Tabacundo'],
                                            ['code' => '51','name' => 'La Esperanza'],
                                            ['code' => '52','name' => 'Malchinguí'],
                                            ['code' => '53','name' => 'Tocachi'],
                                            ['code' => '54','name' => 'Tupigachi']]],

        ['code' => '05','name' => 'Rumiñahui', 'parishes' => [
                                            ['code' => '01','name' => 'Sangolquí'],
                                            ['code' => '02','name' => 'San Pedro De Taboada'],
                                            ['code' => '03','name' => 'San Rafael'],
                                            ['code' => '51','name' => 'Cotogchoa'],
                                            ['code' => '52','name' => 'Rumipamba']]],

        ['code' => '07','name' => 'San Miguel De Los Bancos', 'parishes' => [
                                            ['code' => '50','name' => 'San Miguel De Los Bancos'],
                                            ['code' => '51','name' => 'Mindo'],
                                            ['code' => '52','name' => 'Pedro Vicente Maldonado'],
                                            ['code' => '53','name' => 'Puerto Quito']]],

        ['code' => '08','name' => 'Pedro Vicente Maldonado', 'parishes' => [
                                            ['code' => '50','name' => 'Pedro Vicente Maldonado']]],

        ['code' => '09','name' => 'Puerto Quito', 'parishes' => [
                                            ['code' => '50','name' => 'Puerto Quito']]],
        ]
     ));


    Province::create(array(
      'code' => '18', 
      'name' => 'Tungurahua', 
      'cantons' => [
        ['code' => '01','name' => 'Ambato', 'parishes' => [
                                            ['code' => '01','name' => 'Atocha – Ficoa'],
                                            ['code' => '02','name' => 'Celiano Monge'],
                                            ['code' => '03','name' => 'Huachi Chico'],
                                            ['code' => '04','name' => 'Huachi Loreto'],
                                            ['code' => '05','name' => 'La Merced'],
                                            ['code' => '06','name' => 'La Península'],
                                            ['code' => '07','name' => 'Matriz'],
                                            ['code' => '08','name' => 'Pishilata'],
                                            ['code' => '09','name' => 'San Francisco'],
                                            ['code' => '50','name' => 'Ambato'],
                                            ['code' => '51','name' => 'Ambatillo'],
                                            ['code' => '52','name' => 'Atahualpa (Chisalata)'],
                                            ['code' => '53','name' => 'Augusto N. Martínez (Mundugleo)'],
                                            ['code' => '54','name' => 'Constantino Fernández (Cab. En Cullitahua)'],
                                            ['code' => '55','name' => 'Huachi Grande'],
                                            ['code' => '56','name' => 'Izamba'],
                                            ['code' => '57','name' => 'Juan Benigno Vela'],
                                            ['code' => '58','name' => 'Montalvo'],
                                            ['code' => '59','name' => 'Pasa'],
                                            ['code' => '60','name' => 'Picaigua'],
                                            ['code' => '61','name' => 'PilagÜín (PilahÜín)'],
                                            ['code' => '62','name' => 'Quisapincha (Quizapincha)'],
                                            ['code' => '63','name' => 'San Bartolomé De Pinllog'],
                                            ['code' => '64','name' => 'San Fernando (Pasa San Fernando)'],
                                            ['code' => '65','name' => 'Santa Rosa'],
                                            ['code' => '66','name' => 'Totoras'],
                                            ['code' => '67','name' => 'Cunchibamba'],
                                            ['code' => '68','name' => 'Unamuncho']]],

        ['code' => '02','name' => 'Baños De Agua Santa', 'parishes' => [
                                            ['code' => '50','name' => 'Baños De Agua Santa'],
                                            ['code' => '51','name' => 'Lligua'],
                                            ['code' => '52','name' => 'Río Negro'],
                                            ['code' => '53','name' => 'Río Verde'],
                                            ['code' => '54','name' => 'Ulba']]],

        ['code' => '03','name' => 'Cevallos', 'parishes' => [
                                            ['code' => '50','name' => 'Cevallos']]],

        ['code' => '04','name' => 'Mocha', 'parishes' => [
                                            ['code' => '50','name' => 'Mocha'],
                                            ['code' => '51','name' => 'Pinguilí']]],

        ['code' => '05','name' => 'Patate', 'parishes' => [
                                            ['code' => '50','name' => 'Patate'],
                                            ['code' => '51','name' => 'El Triunfo'],
                                            ['code' => '52','name' => 'Los Andes (Cab. En Poatug)'],
                                            ['code' => '53','name' => 'Sucre (Cab. En Sucre-Patate Urcu)']]],

        ['code' => '06','name' => 'Quero', 'parishes' => [
                                            ['code' => '50','name' => 'Quero'],
                                            ['code' => '51','name' => 'Rumipamba'],
                                            ['code' => '52','name' => 'Yanayacu - Mochapata (Cab. En Yanayacu)']]],

        ['code' => '07','name' => 'San Pedro De Pelileo', 'parishes' => [
                                            ['code' => '01','name' => 'Pelileo'],
                                            ['code' => '02','name' => 'Pelileo Grande'],
                                            ['code' => '50','name' => 'Pelileo'],
                                            ['code' => '51','name' => 'Benítez (Pachanlica)'],
                                            ['code' => '52','name' => 'Bolívar'],
                                            ['code' => '53','name' => 'Cotaló'],
                                            ['code' => '54','name' => 'Chiquicha (Cab. En Chiquicha Grande)'],
                                            ['code' => '55','name' => 'El Rosario (Rumichaca)'],
                                            ['code' => '56','name' => 'García Moreno (Chumaqui)'],
                                            ['code' => '57','name' => 'Guambaló (Huambaló)'],
                                            ['code' => '58','name' => 'Salasaca']]],

        ['code' => '08','name' => 'Santiago De Píllaro', 'parishes' => [
                                            ['code' => '01','name' => 'Ciudad Nueva'],
                                            ['code' => '02','name' => 'Píllaro'],
                                            ['code' => '50','name' => 'Píllaro'],
                                            ['code' => '51','name' => 'Baquerizo Moreno'],
                                            ['code' => '52','name' => 'Emilio María Terán (Rumipamba)'],
                                            ['code' => '53','name' => 'Marcos Espinel (Chacata)'],
                                            ['code' => '54','name' => 'Presidente Urbina (Chagrapamba -Patzucul)'],
                                            ['code' => '55','name' => 'San Andrés'],
                                            ['code' => '56','name' => 'San José De Poaló'],
                                            ['code' => '57','name' => 'San Miguelito']]],

        ['code' => '09','name' => 'Tisaleo', 'parishes' => [
                                            ['code' => '50','name' => 'Tisaleo'],
                                            ['code' => '51','name' => 'Quinchicoto']]],
        ]
     ));


    Province::create(array(
      'code' => '19', 
      'name' => 'Zamora Chinchipe', 
      'cantons' => [
        ['code' => '01','name' => 'Zamora', 'parishes' => [
                                            ['code' => '01','name' => 'El Limón'],
                                            ['code' => '02','name' => 'Zamora'],
                                            ['code' => '50','name' => 'Zamora'],
                                            ['code' => '51','name' => 'Cumbaratza'],
                                            ['code' => '52','name' => 'Guadalupe'],
                                            ['code' => '53','name' => 'Imbana (La Victoria De Imbana)'],
                                            ['code' => '54','name' => 'Paquisha'],
                                            ['code' => '55','name' => 'Sabanilla'],
                                            ['code' => '56','name' => 'Timbara'],
                                            ['code' => '57','name' => 'Zumbi'],
                                            ['code' => '58','name' => 'San Carlos De Las Minas']]],

        ['code' => '02','name' => 'Chinchipe', 'parishes' => [
                                            ['code' => '50','name' => 'Zumba'],
                                            ['code' => '51','name' => 'Chito'],
                                            ['code' => '52','name' => 'El Chorro'],
                                            ['code' => '53','name' => 'El Porvenir Del Carmen'],
                                            ['code' => '54','name' => 'La Chonta'],
                                            ['code' => '55','name' => 'Palanda'],
                                            ['code' => '56','name' => 'Pucapamba'],
                                            ['code' => '57','name' => 'San Francisco Del Vergel'],
                                            ['code' => '58','name' => 'Valladolid'],
                                            ['code' => '59','name' => 'San Andrés']]],

        ['code' => '03','name' => 'Nangaritza', 'parishes' => [
                                            ['code' => '50','name' => 'Guayzimi'],
                                            ['code' => '51','name' => 'Zurmi'],
                                            ['code' => '52','name' => 'Nuevo Paraíso']]],

        ['code' => '04','name' => 'Yacuambí', 'parishes' => [
                                            ['code' => '50','name' => '28 De Mayo (San José De Yacuambi)'],
                                            ['code' => '51','name' => 'La Paz'],
                                            ['code' => '52','name' => 'Tutupali']]],

        ['code' => '05','name' => 'Yantzaza', 'parishes' => [
                                            ['code' => '50','name' => 'Yantzaza (Yanzatza)'],
                                            ['code' => '51','name' => 'Chicaña'],
                                            ['code' => '52','name' => 'El Pangui'],
                                            ['code' => '53','name' => 'Los Encuentros']]],

        ['code' => '06','name' => 'El Pangui', 'parishes' => [
                                            ['code' => '50','name' => 'El Pangui'],
                                            ['code' => '51','name' => 'El Guisme'],
                                            ['code' => '52','name' => 'Pachicutza'],
                                            ['code' => '53','name' => 'Tundayme']]],

        ['code' => '07','name' => 'Centinela Del Cóndor', 'parishes' => [
                                            ['code' => '50','name' => 'Zumbi'],
                                            ['code' => '51','name' => 'Paquisha'],
                                            ['code' => '52','name' => 'Triunfo-Dorado'],
                                            ['code' => '53','name' => 'Panguintza']]],

        ['code' => '08','name' => 'Palanda', 'parishes' => [
                                            ['code' => '50','name' => 'Palanda'],
                                            ['code' => '51','name' => 'El Porvenir Del Carmen'],
                                            ['code' => '52','name' => 'San Francisco Del Vergel'],
                                            ['code' => '53','name' => 'Valladolid'],
                                            ['code' => '54','name' => 'La Canela']]],

        ['code' => '09','name' => 'Paquisha', 'parishes' => [
                                            ['code' => '50','name' => 'Paquisha'],
                                            ['code' => '51','name' => 'Bellavista'],
                                            ['code' => '52','name' => 'Nuevo Quito']]],
        ]

     ));


    Province::create(array(
      'code' => '20', 
      'name' => 'Galápagos', 
      'cantons' => [
        ['code' => '01','name' => 'San Cristóbal', 'parishes' => [
                                            ['code' => '50','name' => 'Puerto Baquerizo Moreno'],
                                            ['code' => '51','name' => 'El Progreso'],
                                            ['code' => '52','name' => 'Isla Santa María (Floreana) (Cab. En Pto. Velasco Ibarra)']]],

        ['code' => '02','name' => 'Isabela', 'parishes' => [
                                            ['code' => '50','name' => 'Puerto Villamil'],
                                            ['code' => '51','name' => 'Tomás De Berlanga (Santo Tomás)']]],

        ['code' => '03','name' => 'Santa Cruz', 'parishes' => [
                                            ['code' => '50','name' => 'Puerto Ayora'],
                                            ['code' => '51','name' => 'Bellavista'],
                                            ['code' => '52','name' => 'Santa Rosa (Incluye La Isla Baltra)']]],
      ]
     ));


    Province::create(array(
      'code' => '21', 
      'name' => 'Sucumbíos', 
      'cantons' => [
        ['code' => '01','name' => 'Lago Agrio', 'parishes' => [
                                            ['code' => '50','name' => 'Nueva Loja'],
                                            ['code' => '51','name' => 'Cuyabeno'],
                                            ['code' => '52','name' => 'Dureno'],
                                            ['code' => '53','name' => 'General Farfán'],
                                            ['code' => '54','name' => 'Tarapoa'],
                                            ['code' => '55','name' => 'El Eno'],
                                            ['code' => '56','name' => 'Pacayacu'],
                                            ['code' => '57','name' => 'Jambelí'],
                                            ['code' => '58','name' => 'Santa Cecilia'],
                                            ['code' => '59','name' => 'Aguas Negras']]],

        ['code' => '02','name' => 'Gonzalo Pizarro', 'parishes' => [
                                            ['code' => '50','name' => 'El Dorado De Cascales'],
                                            ['code' => '51','name' => 'El Reventador'],
                                            ['code' => '52','name' => 'Gonzalo Pizarro'],
                                            ['code' => '53','name' => 'Lumbaquí'],
                                            ['code' => '54','name' => 'Puerto Libre'],
                                            ['code' => '55','name' => 'Santa Rosa De Sucumbíos']]],

        ['code' => '03','name' => 'Putumayo', 'parishes' => [
                                            ['code' => '50','name' => 'Puerto El Carmen Del Putumayo'],
                                            ['code' => '51','name' => 'Palma Roja'],
                                            ['code' => '52','name' => 'Puerto Bolívar (Puerto Montúfar)'],
                                            ['code' => '53','name' => 'Puerto Rodríguez'],
                                            ['code' => '54','name' => 'Santa Elena']]],

        ['code' => '04','name' => 'Shushufindi', 'parishes' => [
                                            ['code' => '50','name' => 'Shushufindi'],
                                            ['code' => '51','name' => 'Limoncocha'],
                                            ['code' => '52','name' => 'Pañacocha'],
                                            ['code' => '53','name' => 'San Roque (Cab. En San Vicente)'],
                                            ['code' => '54','name' => 'San Pedro De Los Cofanes'],
                                            ['code' => '55','name' => 'Siete De Julio']]],

        ['code' => '05','name' => 'Sucumbíos', 'parishes' => [
                                            ['code' => '50','name' => 'La Bonita'],
                                            ['code' => '51','name' => 'El Playón De San Francisco'],
                                            ['code' => '52','name' => 'La Sofía'],
                                            ['code' => '53','name' => 'Rosa Florida'],
                                            ['code' => '54','name' => 'Santa Bárbara']]],

        ['code' => '06','name' => 'Cascales', 'parishes' => [
                                            ['code' => '50','name' => 'El Dorado De Cascales'],
                                            ['code' => '51','name' => 'Santa Rosa De Sucumbíos'],
                                            ['code' => '52','name' => 'Sevilla']]],

        ['code' => '07','name' => 'Cuyabeno', 'parishes' => [
                                            ['code' => '50','name' => 'Tarapoa'],
                                            ['code' => '51','name' => 'Cuyabeno'],
                                            ['code' => '52','name' => 'Aguas Negras']]],
        ]
     ));


    Province::create(array(
      'code' => '22', 
      'name' => 'Orellana', 
      'cantons' => [
        ['code' => '01','name' => 'Orellana', 'parishes' => [
                                            ['code' => '50','name' => 'Puerto Francisco De Orellana (El Coca)'],
                                            ['code' => '51','name' => 'Dayuma'],
                                            ['code' => '52','name' => 'Taracoa (Nueva Esperanza: Yuca)'],
                                            ['code' => '53','name' => 'Alejandro Labaka'],
                                            ['code' => '54','name' => 'El Dorado'],
                                            ['code' => '55','name' => 'El Edén'],
                                            ['code' => '56','name' => 'García Moreno'],
                                            ['code' => '57','name' => 'Inés Arango (Cab. En Western)'],
                                            ['code' => '58','name' => 'La Belleza'],
                                            ['code' => '59','name' => 'Nuevo Paraíso (Cab. En Unión'],
                                            ['code' => '60','name' => 'San José De Guayusa'],
                                            ['code' => '61','name' => 'San Luis De Armenia']]],

        ['code' => '02','name' => 'AguaricoLa', 'parishes' => [
                                            ['code' => '01','name' => 'Tipitini'],
                                            ['code' => '50','name' => 'Nuevo Rocafuerte'],
                                            ['code' => '51','name' => 'Capitán Augusto Rivadeneyra'],
                                            ['code' => '52','name' => 'Cononaco'],
                                            ['code' => '53','name' => 'Santa María De Huiririma'],
                                            ['code' => '54','name' => 'Tiputini'],
                                            ['code' => '55','name' => 'Yasuní']]],

        ['code' => '03','name' => 'Joya De Los Sachas', 'parishes' => [
                                            ['code' => '50','name' => 'La Joya De Los Sachas'],
                                            ['code' => '51','name' => 'Enokanqui'],
                                            ['code' => '52','name' => 'Pompeya'],
                                            ['code' => '53','name' => 'San Carlos'],
                                            ['code' => '54','name' => 'San Sebastián Del Coca'],
                                            ['code' => '55','name' => 'Lago San Pedro'],
                                            ['code' => '56','name' => 'Rumipamba'],
                                            ['code' => '57','name' => 'Tres De Noviembre'],
                                            ['code' => '58','name' => 'Unión Milagreña']]],

        ['code' => '04','name' => 'Loreto', 'parishes' => [
                                            ['code' => '50','name' => 'Loreto'],
                                            ['code' => '51','name' => 'Avila (Cab. En Huiruno)'],
                                            ['code' => '52','name' => 'Puerto Murialdo'],
                                            ['code' => '53','name' => 'San José De Payamino'],
                                            ['code' => '54','name' => 'San José De Dahuano'],
                                            ['code' => '55','name' => 'San Vicente De Huaticocha']]],
        ]
     ));


    Province::create(array(
      'code' => '23', 
      'name' => 'Santo Domingo de los Tsáchilas', 
      'cantons' => [
        ['code' => '01','name' => 'Santo Domingo', 'parishes' => [
                                            ['code' => '01','name' => 'Abraham Calazacón'],
                                            ['code' => '02','name' => 'Bombolí'],
                                            ['code' => '03','name' => 'Chiguilpe'],
                                            ['code' => '04','name' => 'Río Toachi'],
                                            ['code' => '05','name' => 'Río Verde'],
                                            ['code' => '06','name' => 'Santo Domingo De Los Colorados'],
                                            ['code' => '07','name' => 'Zaracay'],
                                            ['code' => '50','name' => 'Santo Domingo De Los Colorados'],
                                            ['code' => '51','name' => 'Alluriquín'],
                                            ['code' => '52','name' => 'Puerto Limón'],
                                            ['code' => '53','name' => 'Luz De América'],
                                            ['code' => '54','name' => 'San Jacinto Del Búa'],
                                            ['code' => '55','name' => 'Valle Hermoso'],
                                            ['code' => '56','name' => 'El Esfuerzo'],
                                            ['code' => '57','name' => 'Santa María Del Toachi']]],
    ]
     ));


    Province::create(array(
      'code' => '24', 
      'name' => 'Santa Elena', 
      'cantons' => [
        ['code' => '01','name' => 'Santa Elena', 'parishes' => [
                                            ['code' => '01','name' => 'Ballenita'],
                                            ['code' => '02','name' => 'Santa Elena'],
                                            ['code' => '50','name' => 'Santa Elena'],
                                            ['code' => '51','name' => 'Atahualpa'],
                                            ['code' => '52','name' => 'Colonche'],
                                            ['code' => '53','name' => 'Chanduy'],
                                            ['code' => '54','name' => 'Manglaralto'],
                                            ['code' => '55','name' => 'Simón Bolívar (Julio Moreno)'],
                                            ['code' => '56','name' => 'San José De Ancón']]],

        ['code' => '02','name' => 'La Libertad', 'parishes' => [
                                            ['code' => '50','name' => 'La Libertad']]],

        ['code' => '03','name' => 'Salinas', 'parishes' => [
                                            ['code' => '01','name' => 'Carlos Espinoza Larrea'],
                                            ['code' => '02','name' => 'Gral. Alberto Enríquez Gallo'],
                                            ['code' => '03','name' => 'Vicente Rocafuerte'],
                                            ['code' => '04','name' => 'Santa Rosa'],
                                            ['code' => '50','name' => 'Salinas'],
                                            ['code' => '51','name' => 'Anconcito'],
                                            ['code' => '52','name' => 'José Luis Tamayo (Muey)']]],
        ]
     ));


  }

}
