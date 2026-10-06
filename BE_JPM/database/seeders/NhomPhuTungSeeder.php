<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class NhomPhuTungSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            ['ma_nhom' => 'ACC01', 'ten_nhom' => 'Accessories', 'trang_thai' => 1],
            ['ma_nhom' => 'ALT02', 'ten_nhom' => 'ALTERNATOR', 'trang_thai' => 1],
            ['ma_nhom' => 'BOL03', 'ten_nhom' => 'Bolt Kit', 'trang_thai' => 1],
            ['ma_nhom' => 'CRA04', 'ten_nhom' => 'CYLINDER BARREL', 'trang_thai' => 1],
            ['ma_nhom' => 'CYL05', 'ten_nhom' => 'CYLINDER BLOCK', 'trang_thai' => 1],
            ['ma_nhom' => 'CYL06', 'ten_nhom' => 'CYLINDER HEAD', 'trang_thai' => 1],
            ['ma_nhom' => 'CAB07', 'ten_nhom' => 'Cable (Dual)', 'trang_thai' => 1],
            ['ma_nhom' => 'CAB08', 'ten_nhom' => 'Cable (Single)', 'trang_thai' => 1],
            ['ma_nhom' => 'CAM09', 'ten_nhom' => 'Camshaft, Rocker Arm', 'trang_thai' => 1],
            ['ma_nhom' => 'CLA10', 'ten_nhom' => 'Clamp', 'trang_thai' => 1],
            ['ma_nhom' => 'CON11', 'ten_nhom' => 'Control Panel', 'trang_thai' => 1],
            ['ma_nhom' => 'CON12', 'ten_nhom' => 'Control Panel Kit', 'trang_thai' => 1],
            ['ma_nhom' => 'CRA13', 'ten_nhom' => 'Crankshaft Piston', 'trang_thai' => 1],
            ['ma_nhom' => 'CYL14', 'ten_nhom' => 'Cylinder Head Assembly', 'trang_thai' => 1],
            ['ma_nhom' => 'CYL15', 'ten_nhom' => 'Cylinder Head Cover', 'trang_thai' => 1],
            ['ma_nhom' => 'ENG16', 'ten_nhom' => 'ENGINE COVER', 'trang_thai' => 1],
            ['ma_nhom' => 'ELE17', 'ten_nhom' => 'Electric Parts Cover', 'trang_thai' => 1],
            ['ma_nhom' => 'ELE18', 'ten_nhom' => 'Electrical Parts Box + Motor Control', 'trang_thai' => 1],
            ['ma_nhom' => 'ELE19', 'ten_nhom' => 'Electronic Control Unit', 'trang_thai' => 1],
            ['ma_nhom' => 'ENG20', 'ten_nhom' => 'Engine Cover (Accessory Soft Cover)', 'trang_thai' => 1],
            ['ma_nhom' => 'EXH21', 'ten_nhom' => 'Exhaust Manifold + Thermostat', 'trang_thai' => 1],
            ['ma_nhom' => 'EXT22', 'ten_nhom' => 'Extension Case', 'trang_thai' => 1],
            ['ma_nhom' => 'FRO23', 'ten_nhom' => 'From Rigging', 'trang_thai' => 1],
            ['ma_nhom' => 'FRO24', 'ten_nhom' => 'Front Cover Bracket + Front Lock Shaft', 'trang_thai' => 1],
            ['ma_nhom' => 'FUE25', 'ten_nhom' => 'Fuel Pipe + Injector', 'trang_thai' => 1],
            ['ma_nhom' => 'FUE26', 'ten_nhom' => 'Fuel Tube', 'trang_thai' => 1],
            ['ma_nhom' => 'GEA27', 'ten_nhom' => 'GEAR CASE', 'trang_thai' => 1],
            ['ma_nhom' => 'GAS28', 'ten_nhom' => 'Gasket Kit (Short Block & Head Assy.)', 'trang_thai' => 1],
            ['ma_nhom' => 'GEA29', 'ten_nhom' => 'Gear Case Assy.', 'trang_thai' => 1],
            ['ma_nhom' => 'INT30', 'ten_nhom' => 'INTAKE MANIFOLD', 'trang_thai' => 1],
            ['ma_nhom' => 'IST31', 'ten_nhom' => 'IST Cowel (Aquamarine And GP White)', 'trang_thai' => 1],
            ['ma_nhom' => 'IST32', 'ten_nhom' => 'IST Decal', 'trang_thai' => 1],
            ['ma_nhom' => 'LAB33', 'ten_nhom' => 'Labels', 'trang_thai' => 1],
            ['ma_nhom' => 'MET34', 'ten_nhom' => 'METER', 'trang_thai' => 1],
            ['ma_nhom' => 'MOU35', 'ten_nhom' => 'Mount Case', 'trang_thai' => 1],
            ['ma_nhom' => 'MOU36', 'ten_nhom' => 'Mount Case Bolt', 'trang_thai' => 1],
            ['ma_nhom' => 'OIL37', 'ten_nhom' => 'OIL PAN', 'trang_thai' => 1],
            ['ma_nhom' => 'OIL38', 'ten_nhom' => 'Oil Pump + Flywheel', 'trang_thai' => 1],
            ['ma_nhom' => 'PIP39', 'ten_nhom' => 'Piping', 'trang_thai' => 1],
            ['ma_nhom' => 'POW40', 'ten_nhom' => 'Power Trim_Tilt', 'trang_thai' => 1],
            ['ma_nhom' => 'PRO41', 'ten_nhom' => 'Propeller', 'trang_thai' => 1],
            ['ma_nhom' => 'PRO42', 'ten_nhom' => 'Propeller Shaft (1)', 'trang_thai' => 1],
            ['ma_nhom' => 'REM43', 'ten_nhom' => 'REMOTE CONTROL', 'trang_thai' => 1],
            ['ma_nhom' => 'REM44', 'ten_nhom' => 'Remote Control (Flush Mount Type) (L.)', 'trang_thai' => 1],
            ['ma_nhom' => 'REM45', 'ten_nhom' => 'Remote Control (Flush Mount Type) (R.)', 'trang_thai' => 1],
            ['ma_nhom' => 'REM46', 'ten_nhom' => 'Remote Control (Top Mount Dual Type) (L.)', 'trang_thai' => 1],
            ['ma_nhom' => 'REM47', 'ten_nhom' => 'Remote Control (Top Mount Dual Type) (R.)', 'trang_thai' => 1],
            ['ma_nhom' => 'REM48', 'ten_nhom' => 'Remote Control (Top Mount Single Type) (L.)', 'trang_thai' => 1],
            ['ma_nhom' => 'REM49', 'ten_nhom' => 'Remote Control (Top Mount Single Type) (R.)', 'trang_thai' => 1],
            ['ma_nhom' => 'REM50', 'ten_nhom' => 'Remote Control Cover Kit', 'trang_thai' => 1],
            ['ma_nhom' => 'STA51', 'ten_nhom' => 'STARTER MOTOR', 'trang_thai' => 1],
            ['ma_nhom' => 'SHI52', 'ten_nhom' => 'Shift Shaft', 'trang_thai' => 1],
            ['ma_nhom' => 'SHI53', 'ten_nhom' => 'Shift Throttle Activator', 'trang_thai' => 1],
            ['ma_nhom' => 'SHO54', 'ten_nhom' => 'Short Block', 'trang_thai' => 1],
            ['ma_nhom' => 'SPA55', 'ten_nhom' => 'Spark Plug', 'trang_thai' => 1],
            ['ma_nhom' => 'STA56', 'ten_nhom' => 'Starter Cable', 'trang_thai' => 1],
            ['ma_nhom' => 'STE57', 'ten_nhom' => 'Stern Bracket + Swivel Case', 'trang_thai' => 1],
            ['ma_nhom' => 'SWI58', 'ten_nhom' => 'Swivel Case Assy.', 'trang_thai' => 1],
            ['ma_nhom' => 'THR59', 'ten_nhom' => 'THROTTLE BODY', 'trang_thai' => 1],
            ['ma_nhom' => 'TIM60', 'ten_nhom' => 'TIMING BELT', 'trang_thai' => 1],
            ['ma_nhom' => 'TIM61', 'ten_nhom' => 'Timing Belt Cover', 'trang_thai' => 1],
            ['ma_nhom' => 'TOO62', 'ten_nhom' => 'Tools', 'trang_thai' => 1],
            ['ma_nhom' => 'TRI63', 'ten_nhom' => 'Trim Adjust Plate Set', 'trang_thai' => 1],
            ['ma_nhom' => 'VAP64', 'ten_nhom' => 'Vapor Separator Assy.', 'trang_thai' => 1],
            ['ma_nhom' => 'WAT65', 'ten_nhom' => 'Water Pump + Vertical Shaft', 'trang_thai' => 1],
            ['ma_nhom' => 'WAT66', 'ten_nhom' => 'Water Pump Impeller Kit', 'trang_thai' => 1],
            ['ma_nhom' => 'WAT67', 'ten_nhom' => 'Water Separator + Fuel Strainer', 'trang_thai' => 1],
            ['ma_nhom' => 'WIR68', 'ten_nhom' => 'Wire Harness', 'trang_thai' => 1],
            ['ma_nhom' => 'BAL01', 'ten_nhom' => 'Balancer Shaft', 'trang_thai' => 1],
            ['ma_nhom' => 'CAM02', 'ten_nhom' => 'Camshaft + Cylinder Head', 'trang_thai' => 1],
            ['ma_nhom' => 'CLA02', 'ten_nhom' => 'Clamp (Fuse Side)', 'trang_thai' => 1],
            ['ma_nhom' => 'CLA03', 'ten_nhom' => 'Clamp (Hose Side)', 'trang_thai' => 1],
            ['ma_nhom' => 'FUE03', 'ten_nhom' => 'Fuel Pipe', 'trang_thai' => 1],
            ['ma_nhom' => 'FUE04', 'ten_nhom' => 'Fuel Tank + Fuel Tube', 'trang_thai' => 1],
            ['ma_nhom' => 'GAS02', 'ten_nhom' => 'Gasket Kit', 'trang_thai' => 1],
            ['ma_nhom' => 'IGN01', 'ten_nhom' => 'Ignition Coil + C.D.I. Unit', 'trang_thai' => 1],
            ['ma_nhom' => 'LOW01', 'ten_nhom' => 'Lower Case', 'trang_thai' => 1],
            ['ma_nhom' => 'MET02', 'ten_nhom' => 'Meter + Hose Joint', 'trang_thai' => 1],
            ['ma_nhom' => 'MET03', 'ten_nhom' => 'Meter Kit + Meter Harness', 'trang_thai' => 1],
            ['ma_nhom' => 'OIL02', 'ten_nhom' => 'Oil Pump + Exhaust Pipe', 'trang_thai' => 1],
            ['ma_nhom' => 'PRO02', 'ten_nhom' => 'Propeller Shaft', 'trang_thai' => 1],
            ['ma_nhom' => 'REM05', 'ten_nhom' => 'Remote Control (HI-LEX) (Handle Cap Gray)', 'trang_thai' => 1],
            ['ma_nhom' => 'REM06', 'ten_nhom' => 'Remote Control (Panel Mount)', 'trang_thai' => 1],
            ['ma_nhom' => 'REM07', 'ten_nhom' => 'Remote Control Box Kit', 'trang_thai' => 1],
            ['ma_nhom' => 'REM08', 'ten_nhom' => 'Remote Control Cable', 'trang_thai' => 1],
            ['ma_nhom' => 'SWI02', 'ten_nhom' => 'Switch Panel Kit', 'trang_thai' => 1],
            ['ma_nhom' => 'THE01', 'ten_nhom' => 'THERMOSTAT', 'trang_thai' => 1],
            ['ma_nhom' => 'VAP02', 'ten_nhom' => 'Vapor Separator', 'trang_thai' => 1],
            ['id' => 111, 'ma_nhom' => 'ACCENT', 'ten_nhom' => 'Accent Panel', 'trang_thai' => 1],
            ['id' => 100, 'ma_nhom' => 'BOTC01', 'ten_nhom' => 'Bottom Cowling 1', 'trang_thai' => 1],
            ['id' => 101, 'ma_nhom' => 'BOTC02', 'ten_nhom' => 'Bottom Cowling 2', 'trang_thai' => 1],
        ];

        $now = Carbon::now();
        foreach ($groups as $item) {

            $item['created_at'] = $now;
            $item['updated_at'] = $now;
            DB::table('nhom_phu_tungs')->updateOrInsert(['ma_nhom' => $item['ma_nhom']], $item);
        }
    }
}


