<?php

use Illuminate\Database\Seeder;
use App\Models\Person;

class PersonsSedeer extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create Persons  instances...
        $persons = factory(Person::class, 30)->create();
    }
}
