<?php

namespace Database\Seeders;

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
    $company = \App\Models\Company::create(['name'=>'Acme']);
    $client = \App\Models\User::factory()->create([
        'name'=>'Acme Admin',
        'email'=>'acme_admin@example.com',
        'password'=>bcrypt('Admin@12345'),
        'company_id'=>$company->id,
        'role'=>'CLIENT',
        'position'=>'Manager'
    ]);
$company = \App\Models\Company::create(['name' => 'Acme']);
    // 3 sample employees
  // 3 sample employees (same company)
\App\Models\Employee::factory()
    ->for($company) // <-- yeh ensure karega company_id sahi lage
    ->createMany([
        ['first_name'=>'Sarah','last_name'=>'Johnson','email'=>'sarah@example.com','position'=>'Sales','status'=>'ACTIVE'],
        ['first_name'=>'Mike','last_name'=>'Chen','email'=>'mike@example.com','position'=>'Customer Service','status'=>'ACTIVE'],
        ['first_name'=>'Emma','last_name'=>'Davis','email'=>'emma@example.com','position'=>'Marketing','status'=>'INACTIVE'],
    ]);

    // Diagnostic template
    \App\Models\TestTemplate::create([
        'company_id'=>$company->id,
        'title'=>'Awareness Diagnostic',
        'schema'=>[
            'items'=>[
                ['key'=>'q1','type'=>'mcq','label'=>'Pick A','options'=>['A','B','C'],'correctIndex'=>0,'weight'=>2],
                ['key'=>'q2','type'=>'boolean','label'=>'Company values known?','correct'=>true,'weight'=>1],
                ['key'=>'q3','type'=>'scale','label'=>'Self awareness','min'=>1,'max'=>5,'weight'=>2],
                ['key'=>'q4','type'=>'short_text','label'=>'Any note?','weight'=>0],
            ]
        ],
        'is_active'=>true,
    ]);

    // Mystery checklist
    \App\Models\MysteryChecklist::create([
        'company_id'=>$company->id,
        'title'=>'Monthly Checklist',
        'schema'=>[
            'items'=>[
                ['key'=>'greet','label'=>'Greets customer','type'=>'yes_no','weight'=>2],
                ['key'=>'tone','label'=>'Tone of voice','type'=>'scale','min'=>1,'max'=>5,'weight'=>3],
                ['key'=>'notes','label'=>'Notes','type'=>'note','weight'=>0],
            ]
        ],
        'is_active'=>true,
    ]);
}

}
