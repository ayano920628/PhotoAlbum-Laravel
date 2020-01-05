<?php

use Illuminate\Database\Seeder;

class ImagesTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    //
    // Fakerを使う
    $faker = Faker\Factory::create('ja_JP');
    // 固定ユーザーを作成
    DB::table('images')->insert([
      'user_id' => '1',
      'img_name' => 'sample.jpg',
      'img_comment' => 'aiueo',
      'taken' => $faker->dateTime(),
      'created_at' => $faker->dateTime(),
      'updated_at' => $faker->dateTime(),
    ]);
    DB::table('images')->insert([
      'user_id' => '2',
      'img_name' => 'sample.jpg',
      'img_comment' => 'aiueo',
      'taken' => $faker->dateTime(),
      'created_at' => $faker->dateTime(),
      'updated_at' => $faker->dateTime(),
    ]);
    // ランダムにユーザーを作成
    for ($i = 0; $i < 18; $i++)
    {
      DB::table('images')->insert([
        'user_id' => '2',
        'img_name' => 'sample.jpg',
        'img_comment' => 'aiueo',
        'taken' => $faker->dateTime(),
        'created_at' => $faker->dateTime(),
        'updated_at' => $faker->dateTime(),
      ]);
    }
  }
}
