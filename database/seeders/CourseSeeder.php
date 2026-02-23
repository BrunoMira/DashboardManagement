<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Course::create([
            'name' => 'Laravel',
            'description' => 'Laravel is a free, open-source PHP web framework, designed for the development of web applications following the model–view–controller (MVC) architectural pattern.',
            'image' => 'https://laravel.com/img/logotype.min.svg',
            'price' => '100',
        ]);

        Course::create([
            'name' => 'React Js',
            'description' => 'React is a JavaScript library for building user interfaces.',
            'image' => 'https://react.dev/images/react-logo-200x200.png',
            'price' => '200',
        ]);

        Course::create([
            'name' => 'Vue.js',
            'description' => 'Vue.js is a progressive JavaScript framework for building user interfaces.',
            'image' => 'https://vuejs.org/images/logo.png',
            'price' => '300',
        ]);
    }
}
