<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Airport;
use App\Models\Airline;
use App\Models\Flight;
use App\Models\FlightSeat;

class FlightDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Création des Utilisateurs par défaut
        $admin = User::firstOrCreate(
            ['email' => 'admin@horizonvols.com'],
            [
                'name' => 'Directeur Opérations',
                'first_name' => 'Marc',
                'last_name' => 'Dubois',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'phone' => '+33 1 42 68 00 00',
                'city' => 'Paris',
                'country' => 'France',
                'membership_tier' => 'Horizon Platinum',
                'miles' => 125000,
            ]
        );

        $customer = User::firstOrCreate(
            ['email' => 'client@horizonvols.com'],
            [
                'name' => 'Amadou Diallo',
                'first_name' => 'Amadou',
                'last_name' => 'Diallo',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'phone' => '+221 77 123 45 67',
                'city' => 'Dakar',
                'country' => 'Sénégal',
                'membership_tier' => 'Horizon Gold',
                'miles' => 45800,
            ]
        );

        // 2. Création des Aéroports Internationaux
        $airportsData = [
            ['iata_code' => 'CDG', 'name' => 'Aéroport Paris-Charles de Gaulle', 'city' => 'Paris', 'country' => 'France', 'region' => 'Europe', 'terminal_default' => 'Terminal 2E', 'timezone' => 'Europe/Paris', 'is_popular' => true],
            ['iata_code' => 'ORY', 'name' => 'Aéroport de Paris-Orly', 'city' => 'Paris', 'country' => 'France', 'region' => 'Europe', 'terminal_default' => 'Terminal 4', 'timezone' => 'Europe/Paris', 'is_popular' => true],
            ['iata_code' => 'DSS', 'name' => 'Aéroport International Blaise Diagne', 'city' => 'Dakar', 'country' => 'Sénégal', 'region' => 'Afrique', 'terminal_default' => 'Terminal Principal', 'timezone' => 'Africa/Dakar', 'is_popular' => true],
            ['iata_code' => 'ABJ', 'name' => 'Aéroport International Félix-Houphouët-Boigny', 'city' => 'Abidjan', 'country' => 'Côte d’Ivoire', 'region' => 'Afrique', 'terminal_default' => 'Terminal 1', 'timezone' => 'Africa/Abidjan', 'is_popular' => true],
            ['iata_code' => 'COO', 'name' => 'Aéroport International Cardinal Bernardin Gantin', 'city' => 'Cotonou', 'country' => 'Bénin', 'region' => 'Afrique', 'terminal_default' => 'Terminal Unique', 'timezone' => 'Africa/Porto-Novo', 'is_popular' => true],
            ['iata_code' => 'DLA', 'name' => 'Aéroport International de Douala', 'city' => 'Douala', 'country' => 'Cameroun', 'region' => 'Afrique', 'terminal_default' => 'Terminal A', 'timezone' => 'Africa/Douala', 'is_popular' => true],
            ['iata_code' => 'DXB', 'name' => 'Dubai International Airport', 'city' => 'Dubaï', 'country' => 'Émirats Arabes Unis', 'region' => 'Moyen-Orient', 'terminal_default' => 'Terminal 3', 'timezone' => 'Asia/Dubai', 'is_popular' => true],
            ['iata_code' => 'JFK', 'name' => 'John F. Kennedy International Airport', 'city' => 'New York', 'country' => 'États-Unis', 'region' => 'Amérique du Nord', 'terminal_default' => 'Terminal 4', 'timezone' => 'America/New_York', 'is_popular' => true],
            ['iata_code' => 'CMN', 'name' => 'Aéroport Mohammed V', 'city' => 'Casablanca', 'country' => 'Maroc', 'region' => 'Afrique', 'terminal_default' => 'Terminal 2', 'timezone' => 'Africa/Casablanca', 'is_popular' => true],
        ];

        $airports = [];
        foreach ($airportsData as $data) {
            $airports[$data['iata_code']] = Airport::updateOrCreate(['iata_code' => $data['iata_code']], $data);
        }

        // 3. Création des Compagnies Aériennes
        $airlinesData = [
            [
                'code' => 'AF',
                'name' => 'Air France',
                'alliance' => 'SkyTeam',
                'country' => 'France',
                'logo_color' => '#002157',
                'accent_color' => '#ED1C24',
                'rating' => 4.6,
                'baggage_policy' => '1 accessoire + 1 bagage cabine (12kg total) + 2 bagages soute (23kg)',
                'fleet' => ['Airbus A350-900', 'Boeing 777-300ER', 'Airbus A330-200'],
                'is_active' => true,
            ],
            [
                'code' => 'HC',
                'name' => 'Air Sénégal',
                'alliance' => 'Indépendant',
                'country' => 'Sénégal',
                'logo_color' => '#0A5C36',
                'accent_color' => '#E5A00D',
                'rating' => 4.2,
                'baggage_policy' => '2 bagages de 23kg en soute inclus + 1 bagage cabine 10kg',
                'fleet' => ['Airbus A330neo', 'Airbus A321', 'Airbus A319'],
                'is_active' => true,
            ],
            [
                'code' => 'SS',
                'name' => 'Corsair',
                'alliance' => 'Indépendant',
                'country' => 'France',
                'logo_color' => '#004B87',
                'accent_color' => '#00A3E0',
                'rating' => 4.3,
                'baggage_policy' => '1 bagage cabine 12kg + 2x23kg en soute',
                'fleet' => ['Airbus A330-900neo'],
                'is_active' => true,
            ],
            [
                'code' => 'EK',
                'name' => 'Emirates',
                'alliance' => 'Indépendant',
                'country' => 'Émirats Arabes Unis',
                'logo_color' => '#D71921',
                'accent_color' => '#B8860B',
                'rating' => 4.8,
                'baggage_policy' => '2 bagages de 23kg inclus en Economy, 2x32kg en Business',
                'fleet' => ['Airbus A380-800', 'Boeing 777-300ER'],
                'is_active' => true,
            ],
            [
                'code' => 'AT',
                'name' => 'Royal Air Maroc',
                'alliance' => 'Oneworld',
                'country' => 'Maroc',
                'logo_color' => '#8B1E41',
                'accent_color' => '#C49A45',
                'rating' => 4.1,
                'baggage_policy' => '2 bagages de 23kg en soute + 1 cabine 10kg',
                'fleet' => ['Boeing 787-9 Dreamliner', 'Boeing 737-800'],
                'is_active' => true,
            ],
        ];

        $airlines = [];
        foreach ($airlinesData as $data) {
            $airlines[$data['code']] = Airline::updateOrCreate(['code' => $data['code']], $data);
        }

        // 4. Création d'un ensemble de Vols réalistes
        $flightsData = [
            // Paris -> Dakar (Direct Air France)
            [
                'flight_number' => 'AF 718',
                'airline_id' => $airlines['AF']->id,
                'departure_airport_id' => $airports['CDG']->id,
                'arrival_airport_id' => $airports['DSS']->id,
                'departure_time' => '15:40:00',
                'arrival_time' => '19:35:00',
                'duration_minutes' => 355, // 5h 55m
                'stops' => 0,
                'aircraft' => 'Boeing 777-300ER',
                'cabin_class' => 'economy',
                'base_price' => 520.00,
                'taxes' => 78.50,
                'baggage_cabin' => '1x 12kg inclus',
                'baggage_hold' => '2x 23kg inclus',
                'ticket_policy' => 'Modifiable avec frais de 80€',
                'is_refundable' => false,
                'services' => ['Wi-Fi Haut Débit', 'Repas gastronomique français', 'Écran HD individuel', 'Prises USB-C'],
                'seats_total' => 280,
                'seats_available' => 42,
                'is_recommended' => true,
            ],
            // Paris -> Dakar (Air Sénégal Direct)
            [
                'flight_number' => 'HC 404',
                'airline_id' => $airlines['HC']->id,
                'departure_airport_id' => $airports['CDG']->id,
                'arrival_airport_id' => $airports['DSS']->id,
                'departure_time' => '11:15:00',
                'arrival_time' => '15:10:00',
                'duration_minutes' => 355,
                'stops' => 0,
                'aircraft' => 'Airbus A330-900neo',
                'cabin_class' => 'economy',
                'base_price' => 465.00,
                'taxes' => 65.00,
                'baggage_cabin' => '1x 10kg inclus',
                'baggage_hold' => '2x 23kg inclus',
                'ticket_policy' => 'Modifiable sous conditions',
                'is_refundable' => false,
                'services' => ['Repas chaud Teranga', 'Divertissement à bord', 'Boissons gratuites'],
                'seats_total' => 290,
                'seats_available' => 19,
                'is_recommended' => true,
            ],
            // Dakar -> Paris (Vol retour)
            [
                'flight_number' => 'AF 719',
                'airline_id' => $airlines['AF']->id,
                'departure_airport_id' => $airports['DSS']->id,
                'arrival_airport_id' => $airports['CDG']->id,
                'departure_time' => '23:20:00',
                'arrival_time' => '06:50:00',
                'duration_minutes' => 330, // 5h 30m
                'stops' => 0,
                'aircraft' => 'Boeing 777-300ER',
                'cabin_class' => 'economy',
                'base_price' => 510.00,
                'taxes' => 82.00,
                'baggage_cabin' => '1x 12kg inclus',
                'baggage_hold' => '2x 23kg inclus',
                'ticket_policy' => 'Modifiable sous conditions',
                'is_refundable' => false,
                'services' => ['Wi-Fi à bord', 'Petit déjeuner offert', 'Écran individuel'],
                'seats_total' => 280,
                'seats_available' => 35,
                'is_recommended' => false,
            ],
            // Paris -> Abidjan (Direct Corsair)
            [
                'flight_number' => 'SS 984',
                'airline_id' => $airlines['SS']->id,
                'departure_airport_id' => $airports['ORY']->id,
                'arrival_airport_id' => $airports['ABJ']->id,
                'departure_time' => '13:30:00',
                'arrival_time' => '18:50:00',
                'duration_minutes' => 380, // 6h 20m
                'stops' => 0,
                'aircraft' => 'Airbus A330-900neo',
                'cabin_class' => 'economy',
                'base_price' => 495.00,
                'taxes' => 74.00,
                'baggage_cabin' => '1x 12kg',
                'baggage_hold' => '2x 23kg inclus',
                'ticket_policy' => 'Billet 100% modifiable',
                'is_refundable' => true,
                'services' => ['Wi-Fi disponible', 'Menu créole & ivoirien', 'Système audio Bluetooth'],
                'seats_total' => 350,
                'seats_available' => 54,
                'is_recommended' => true,
            ],
            // Paris -> Cotonou (Air France Direct)
            [
                'flight_number' => 'AF 804',
                'airline_id' => $airlines['AF']->id,
                'departure_airport_id' => $airports['CDG']->id,
                'arrival_airport_id' => $airports['COO']->id,
                'departure_time' => '14:05:00',
                'arrival_time' => '19:40:00',
                'duration_minutes' => 395, // 6h 35m
                'stops' => 0,
                'aircraft' => 'Airbus A350-900',
                'cabin_class' => 'economy',
                'base_price' => 580.00,
                'taxes' => 88.00,
                'baggage_cabin' => '1x 12kg inclus',
                'baggage_hold' => '2x 23kg inclus',
                'ticket_policy' => 'Modifiable sous conditions',
                'is_refundable' => false,
                'services' => ['Wi-Fi Starlink Ultra-Rapide', 'Repas complet inclus', 'Prises 220V et USB-C'],
                'seats_total' => 292,
                'seats_available' => 22,
                'is_recommended' => true,
            ],
            // Paris -> Cotonou avec Escale Casablanca (Royal Air Maroc)
            [
                'flight_number' => 'AT 751',
                'airline_id' => $airlines['AT']->id,
                'departure_airport_id' => $airports['ORY']->id,
                'arrival_airport_id' => $airports['COO']->id,
                'departure_time' => '08:30:00',
                'arrival_time' => '17:15:00',
                'duration_minutes' => 525,
                'stops' => 1,
                'stop_details' => [
                    'stop_airport' => 'Casablanca (CMN)',
                    'stop_duration' => '1h 45m',
                ],
                'aircraft' => 'Boeing 737-800',
                'cabin_class' => 'economy',
                'base_price' => 390.00, // Prix économique avantageux
                'taxes' => 59.00,
                'baggage_cabin' => '1x 10kg inclus',
                'baggage_hold' => '2x 23kg inclus',
                'ticket_policy' => 'Modifiable avec frais de 50€',
                'is_refundable' => false,
                'services' => ['Repas marocain', 'Thé à la menthe offert'],
                'seats_total' => 160,
                'seats_available' => 14,
                'is_recommended' => false,
            ],
            // Paris -> Dubaï (Emirates A380 Direct)
            [
                'flight_number' => 'EK 074',
                'airline_id' => $airlines['EK']->id,
                'departure_airport_id' => $airports['CDG']->id,
                'arrival_airport_id' => $airports['DXB']->id,
                'departure_time' => '15:35:00',
                'arrival_time' => '00:20:00',
                'duration_minutes' => 405, // 6h 45m
                'stops' => 0,
                'aircraft' => 'Airbus A380-800',
                'cabin_class' => 'business',
                'base_price' => 1850.00,
                'taxes' => 195.00,
                'baggage_cabin' => '2x 14kg',
                'baggage_hold' => '2x 32kg inclus',
                'ticket_policy' => 'Annulation et modification gratuites',
                'is_refundable' => true,
                'services' => ['Bar lounge à bord', 'Siège lit plat 180°', 'Écran 23 pouces ice', 'Accès Salon VIP'],
                'seats_total' => 76,
                'seats_available' => 8,
                'is_recommended' => true,
            ],
            // Paris -> New York (Air France Direct)
            [
                'flight_number' => 'AF 006',
                'airline_id' => $airlines['AF']->id,
                'departure_airport_id' => $airports['CDG']->id,
                'arrival_airport_id' => $airports['JFK']->id,
                'departure_time' => '14:00:00',
                'arrival_time' => '16:30:00',
                'duration_minutes' => 510, // 8h 30m
                'stops' => 0,
                'aircraft' => 'Boeing 777-300ER',
                'cabin_class' => 'economy',
                'base_price' => 430.00,
                'taxes' => 95.00,
                'baggage_cabin' => '1x 12kg inclus',
                'baggage_hold' => '1x 23kg inclus',
                'ticket_policy' => 'Modifiable avec frais',
                'is_refundable' => false,
                'services' => ['Repas avec champagne', 'Wi-Fi Message gratuit', 'Prises universelles'],
                'seats_total' => 312,
                'seats_available' => 38,
                'is_recommended' => true,
            ],
        ];

        foreach ($flightsData as $fData) {
            $flight = Flight::create($fData);

            // Génération de quelques sièges tests pour chaque vol (Hublot, Milieu, Couloir)
            $rows = [11, 12, 14, 15, 16];
            $cols = ['A' => 'window', 'B' => 'middle', 'C' => 'aisle', 'D' => 'aisle', 'E' => 'middle', 'F' => 'window'];

            foreach ($rows as $row) {
                foreach ($cols as $col => $type) {
                    FlightSeat::create([
                        'flight_id' => $flight->id,
                        'seat_number' => "{$row}{$col}",
                        'cabin_class' => $flight->cabin_class,
                        'type' => $type,
                        'is_exit_row' => ($row === 14),
                        'is_available' => (rand(1, 10) > 3), // ~70% de sièges libres
                        'extra_price' => ($row === 14 ? 25.00 : ($type === 'window' ? 12.00 : 0.00)),
                    ]);
                }
            }
        }
    }
}