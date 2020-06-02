<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\User::class, 20)->create()->each(
            function($user)
            {
                factory(App\ToDoItem::class, 2)->create(['owner_id' => $user->id]);
            }
        );
    }
}
