<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder; use App\Models\User; use App\Models\Equipment; use Illuminate\Support\Facades\Hash;
class DatabaseSeeder extends Seeder { public function run(): void {
 foreach ([['admin','admin@maintenx.com','admin123','ADMIN'],['engineer','engineer@maintenx.com','engineer123','ENGINEER'],['supervisor','supervisor@maintenx.com','supervisor123','SUPERVISOR'],['manager','manager@maintenx.com','manager123','MANAGER']] as [$u,$e,$p,$r]) User::updateOrCreate(['username'=>$u],['email'=>$e,'password'=>Hash::make($p),'role'=>$r]);
 foreach ([['EQ-001','Mesin Produksi 01','Area Produksi','Mesin produksi utama','ACTIVE'],['EQ-002','Compressor 01','Utility','Compressor udara','ACTIVE'],['EQ-003','Generator 01','Power House','Generator cadangan','ACTIVE']] as [$c,$n,$l,$d,$s]) Equipment::updateOrCreate(['equipment_code'=>$c],['name'=>$n,'location'=>$l,'description'=>$d,'status'=>$s]);
 }}
