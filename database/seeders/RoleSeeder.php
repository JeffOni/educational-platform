<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleAdmin = Role::create(['name' => 'admin']);
        $roleTeacher = Role::create(['name' => 'teacher']);
        $roleStudent = Role::create(['name' => 'student']);

        Permission::create(['name' => 'create courses'])->syncRoles([$roleTeacher, $roleAdmin]);
        Permission::create(['name' => 'read courses'])->syncRoles([$roleTeacher, $roleAdmin, $roleStudent]);
        Permission::create(['name' => 'update courses'])->syncRoles([$roleTeacher, $roleAdmin]);
        Permission::create(['name' => 'delete courses'])->syncRoles([$roleTeacher, $roleAdmin]);
        
        Permission::create(['name' => 'publish courses'])->syncRoles([$roleAdmin]);
    }
}
