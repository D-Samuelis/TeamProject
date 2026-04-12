<?php

namespace Database\Seeders;

use App\Models\Auth\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Creates a set of predictable regular users.
 * Factories handle random data; this seeder gives you known credentials
 * you can actually log in with during development.
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Jana Nováková',   'email' => 'jana@example.com',    'gender' => 'female', 'title_prefix' => 'Mgr.', 'birth_date' => '1990-04-15', 'phone_number' => '+421911111001'],
            ['name' => 'Peter Kováč',     'email' => 'peter@example.com',   'gender' => 'male',   'birth_date' => '1985-08-22', 'phone_number' => '+421911111002'],
            ['name' => 'Mária Horáková',  'email' => 'maria@example.com',   'gender' => 'female', 'title_prefix' => 'Dr.', 'birth_date' => '1993-01-30', 'phone_number' => '+421911111003'],
            ['name' => 'Tomáš Blaho',     'email' => 'tomas@example.com',   'gender' => 'male',   'birth_date' => '1988-11-05', 'phone_number' => '+421911111004'],
            ['name' => 'Zuzana Kráľová',  'email' => 'zuzana@example.com',  'gender' => 'female', 'birth_date' => '1995-06-18', 'phone_number' => '+421911111005'],
            ['name' => 'Andrej Šikovný',  'email' => 'andrej@barber.sk',    'gender' => 'male',   'birth_date' => '1992-03-12', 'phone_number' => '+421900100200'],
            ['name' => 'Lucia Sladká',    'email' => 'lucia@joga.sk',       'gender' => 'female', 'birth_date' => '1989-12-05', 'phone_number' => '+421900300400'],
            ['name' => 'Michal Drsný',    'email' => 'michal@klient.sk',    'gender' => 'male',   'birth_date' => '1980-05-20', 'phone_number' => '+421905111222'],
            ['name' => 'Katarína Malá',   'email' => 'katka@klient.sk',     'gender' => 'female', 'birth_date' => '1998-07-07', 'phone_number' => '+421905333444'],
            ['name' => 'Pavol Hruška',    'email' => 'pavol@test.sk',       'gender' => 'male',   'birth_date' => '1982-10-10', 'phone_number' => '+421905555666'],
            ['name' => 'Simona Vlková',   'email' => 'simona@test.sk',      'gender' => 'female', 'birth_date' => '1994-02-28', 'phone_number' => '+421905777888'],
            ['name' => 'Igor Rýchly',     'email' => 'igor@auto.sk',        'gender' => 'male',   'birth_date' => '1975-01-15', 'phone_number' => '+421905999000'],
            ['name' => 'Beáta Dlhá',      'email' => 'beata@priklad.sk',    'gender' => 'female', 'birth_date' => '1987-09-12', 'phone_number' => '+421918123456'],
            ['name' => 'Martin Modrý',    'email' => 'martin@web.sk',       'gender' => 'male',   'birth_date' => '1991-04-04', 'phone_number' => '+421918654321'],
            ['name' => 'Elena Stará',     'email' => 'elena@gmail.sk',      'gender' => 'female', 'birth_date' => '1965-11-22', 'phone_number' => '+421918111222'],
            ['name' => 'Rastislav Novák',   'email' => 'rasto@fitzone.sk',     'gender' => 'male',   'birth_date' => '1986-03-18', 'phone_number' => '+421911200001'],
            ['name' => 'Veronika Procházka','email' => 'vero@fitzone.sk',      'gender' => 'female', 'birth_date' => '1991-07-24', 'phone_number' => '+421911200002'],
            ['name' => 'Jakub Lesný',       'email' => 'jakub@wellness.sk',    'gender' => 'male',   'birth_date' => '1984-01-11', 'phone_number' => '+421911200003'],
            ['name' => 'Kristína Olejníková','email' => 'kristina@wellness.sk','gender' => 'female', 'title_prefix' => 'Bc.', 'birth_date' => '1996-05-30', 'phone_number' => '+421911200004'],
            ['name' => 'Marek Sloboda',     'email' => 'marek@photo.sk',       'gender' => 'male',   'birth_date' => '1989-09-09', 'phone_number' => '+421911200005'],
            ['name' => 'Denisa Horná',      'email' => 'denisa@photo.sk',      'gender' => 'female', 'birth_date' => '1993-12-20', 'phone_number' => '+421911200006'],
            ['name' => 'Róbert Fekete',     'email' => 'robert@autoservis.sk', 'gender' => 'male',   'birth_date' => '1978-06-14', 'phone_number' => '+421911200007'],
            ['name' => 'Silvia Žiaková',    'email' => 'silvia@autoservis.sk', 'gender' => 'female', 'birth_date' => '1983-02-27', 'phone_number' => '+421911200008'],
            ['name' => 'Ľuboš Mináč',      'email' => 'lubos@dentist.sk',     'gender' => 'male',   'title_prefix' => 'MDDr.', 'birth_date' => '1981-10-03', 'phone_number' => '+421911200009'],
            ['name' => 'Andrea Šimková',    'email' => 'andrea@dentist.sk',    'gender' => 'female', 'title_prefix' => 'MDDr.', 'birth_date' => '1985-08-16', 'phone_number' => '+421911200010'],
            ['name' => 'Tomáš Vlček',       'email' => 'tvlcek@dentist.sk',   'gender' => 'male',   'birth_date' => '1990-04-22', 'phone_number' => '+421911200011'],
            ['name' => 'Miroslava Benková', 'email' => 'mirka@relax.sk',       'gender' => 'female', 'birth_date' => '1988-11-11', 'phone_number' => '+421911200012'],
            ['name' => 'Filip Záhradník',   'email' => 'filip@relax.sk',       'gender' => 'male',   'birth_date' => '1994-07-07', 'phone_number' => '+421911200013'],
            ['name' => 'Natália Červená',   'email' => 'natalia@barber.sk',    'gender' => 'female', 'birth_date' => '1997-03-15', 'phone_number' => '+421911200014'],
            ['name' => 'Dominik Bartoš',    'email' => 'dominik@barber.sk',    'gender' => 'male',   'birth_date' => '1995-09-28', 'phone_number' => '+421911200015'],
            ['name' => 'Vladimír Pokorný',  'email' => 'vlado@gmail.com',      'gender' => 'male',   'birth_date' => '1979-04-01', 'phone_number' => '+421911300001'],
            ['name' => 'Monika Štefanová',  'email' => 'monika@gmail.com',     'gender' => 'female', 'birth_date' => '1992-06-13', 'phone_number' => '+421911300002'],
            ['name' => 'Stanislav Brúsik',  'email' => 'stano@email.sk',       'gender' => 'male',   'birth_date' => '1983-08-05', 'phone_number' => '+421911300003'],
            ['name' => 'Gabriela Tóthová',  'email' => 'gabi@email.sk',        'gender' => 'female', 'birth_date' => '1990-01-19', 'phone_number' => '+421911300004'],
            ['name' => 'Ján Krížik',        'email' => 'jano@post.sk',         'gender' => 'male',   'birth_date' => '1988-10-31', 'phone_number' => '+421911300005'],
            ['name' => 'Renáta Barošová',   'email' => 'renata@post.sk',       'gender' => 'female', 'birth_date' => '1996-05-05', 'phone_number' => '+421911300006'],
            ['name' => 'Ondrej Hamada',     'email' => 'ondrej@zoznam.sk',     'gender' => 'male',   'birth_date' => '1975-12-12', 'phone_number' => '+421911300007'],
            ['name' => 'Iveta Mičková',     'email' => 'iveta@zoznam.sk',      'gender' => 'female', 'birth_date' => '1984-03-08', 'phone_number' => '+421911300008'],
            ['name' => 'Patrik Kováčik',    'email' => 'patrik@centrum.sk',    'gender' => 'male',   'birth_date' => '1999-07-23', 'phone_number' => '+421911300009'],
            ['name' => 'Dagmar Nováčková',  'email' => 'dagmar@centrum.sk',    'gender' => 'female', 'birth_date' => '1971-02-14', 'phone_number' => '+421911300010'],
            ['name' => 'Ľubomír Sedlák',   'email' => 'lubom@atlas.sk',        'gender' => 'male',   'birth_date' => '1980-09-17', 'phone_number' => '+421911300011'],
            ['name' => 'Petra Kováčová',    'email' => 'petra.k@atlas.sk',     'gender' => 'female', 'birth_date' => '1993-11-29', 'phone_number' => '+421911300012'],
            ['name' => 'Boris Molnár',      'email' => 'boris@email.com',      'gender' => 'male',   'birth_date' => '1987-06-06', 'phone_number' => '+421911300013'],
            ['name' => 'Zdenka Polónyiová','email' => 'zdenka@email.com',      'gender' => 'female', 'birth_date' => '1995-04-17', 'phone_number' => '+421911300014'],
            ['name' => 'Marián Baláž',      'email' => 'marian@icloud.com',    'gender' => 'male',   'birth_date' => '1977-01-25', 'phone_number' => '+421911300015'],
            ['name' => 'Soňa Macháčková',   'email' => 'sona@icloud.com',      'gender' => 'female', 'birth_date' => '1991-08-08', 'phone_number' => '+421911300016'],
            ['name' => 'Tibor Lukáč',       'email' => 'tibor@outlook.com',    'gender' => 'male',   'birth_date' => '1985-05-15', 'phone_number' => '+421911300017'],
            ['name' => 'Alžbeta Mináčová', 'email' => 'alzbet@outlook.com',    'gender' => 'female', 'birth_date' => '1998-02-20', 'phone_number' => '+421911300018'],
            ['name' => 'Karol Bučko',       'email' => 'karol@yahoo.com',      'gender' => 'male',   'birth_date' => '1982-07-30', 'phone_number' => '+421911300019'],
            ['name' => 'Adriana Kňazová',   'email' => 'adriana@yahoo.com',    'gender' => 'female', 'birth_date' => '1994-10-10', 'phone_number' => '+421911300020'],
        ];

        foreach ($users as $data) {
            User::firstOrCreate(
                ['email' => $data['email']],
                array_merge($data, [
                    'password'          => Hash::make('password'),
                    'is_admin'          => false,
                    'country'           => 'Slovakia',
                    'city'              => 'Bratislava',
                    'is_visible'        => true,
                    'notify_email'     => false,
                    'notify_sms'       => false,
                    'email_verified_at' => now(),
                ])
            );
        }
    }
}