<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run()
  {
    // $this->call(
    //   UsersTableSeeder::class,
    //   ImagesTableSeeder::class
    // );
    factory(App\User::class, 20)->create()->each(function ($user) {
      $user->images()->save(
        factory(App\Image::class)->make()
      );
    });  
  }
}
