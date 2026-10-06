<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PhuTungSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $data = [

          [ 
            'id' => 1, 'thuong_hieu_id' => 2, 'nhom_phu_tung_id' => 2, 
            'ma_phu_tung' => "31105-PR7-A01", 'ten_phu_tung' => "HOLDER ASSY, BRUSH", 
            'gia_ban' => 1218000, 'is_gia_lien_he' => true, 'trang_thai' => 1 
          ],
          [ 
            'id' => 2, 'thuong_hieu_id' => 2, 'nhom_phu_tung_id' => 2, 
            'ma_phu_tung' => "31111-PT0-003", 'ten_phu_tung' => "BEARING, RR. GENERATOR", 
            'gia_ban' => 996000, 'is_gia_lien_he' => true, 'trang_thai' => 1 
          ],
          [ 
            'id' => 3, 'thuong_hieu_id' => 2, 'nhom_phu_tung_id' => 2, 
            'ma_phu_tung' => "31114-PT0-013", 'ten_phu_tung' => "BEARING, FR. GENERATOR", 
            'gia_ban' => 996000, 'is_gia_lien_he' => true, 'trang_thai' => 1 
          ],
          [ 
            'id' => 4, 'thuong_hieu_id' => 2, 'nhom_phu_tung_id' => 2, 
            'ma_phu_tung' => "31118-PT0-003", 'ten_phu_tung' => "BUSH, INSULATION", 
            'gia_ban' => 190000, 'is_gia_lien_he' => true, 'trang_thai' => 1 
          ],
          [ 
            'id' => 5, 'thuong_hieu_id' => 2, 'nhom_phu_tung_id' => 2, 
            'ma_phu_tung' => "31130-PD1-001", 'ten_phu_tung' => "COVER, BEARING", 
            'gia_ban' => 239000, 'is_gia_lien_he' => true, 'trang_thai' => 1 
          ],
          [ 
            'id' => 6, 'thuong_hieu_id' => 2, 'nhom_phu_tung_id' => 2, 
            'ma_phu_tung' => "31141-P13-003", 'ten_phu_tung' => "PULLEY", 
            'gia_ban' => 2978000, 'is_gia_lien_he' => true, 'trang_thai' => 1 
          ],
          [ 
            'id' => 7, 'thuong_hieu_id' => 2, 'nhom_phu_tung_id' => 2, 
            'ma_phu_tung' => "31142-PD1-001", 'ten_phu_tung' => "NUT, PULLEY LOCK", 
            'gia_ban' => 190000, 'is_gia_lien_he' => true, 'trang_thai' => 1 
          ],
          [ 
            'id' => 8, 'thuong_hieu_id' => 2, 'nhom_phu_tung_id' => 2, 
            'ma_phu_tung' => "31145-PD1-004", 'ten_phu_tung' => "BOLTS, STUDS (6X68)", 
            'gia_ban' => 202000, 'is_gia_lien_he' => true, 'trang_thai' => 1 
          ],
          [ 
            'id' => 9, 'thuong_hieu_id' => 2, 'nhom_phu_tung_id' => 2, 
            'ma_phu_tung' => "31146-PT0-003", 'ten_phu_tung' => "SCREW", 
            'gia_ban' => 72000, 'is_gia_lien_he' => true, 'trang_thai' => 1 
          ],
          [ 
            'id' => 10, 'thuong_hieu_id' => 2, 'nhom_phu_tung_id' => 2, 
            'ma_phu_tung' => "31147-PT0-003", 'ten_phu_tung' => "SCREW-WASHER", 
            'gia_ban' => 73000, 'is_gia_lien_he' => true, 'trang_thai' => 1 
          ],
          [ 
            'id' => 11, 'thuong_hieu_id' => 2, 'nhom_phu_tung_id' => 2, 
            'ma_phu_tung' => "31147-P13-003", 'ten_phu_tung' => "SCREW", 
            'gia_ban' => 42000, 'is_gia_lien_he' => true, 'trang_thai' => 1 
          ],
          [ 
            'id' => 12, 'thuong_hieu_id' => 2, 'nhom_phu_tung_id' => 2, 
            'ma_phu_tung' => "31148-PT0-003", 'ten_phu_tung' => "SCREW, WAVE WASHER", 
            'gia_ban' => 56000, 'is_gia_lien_he' => true, 'trang_thai' => 1 
          ],
          [ 
            'id' => 13, 'thuong_hieu_id' => 2, 'nhom_phu_tung_id' => 2, 
            'ma_phu_tung' => "31148-P13-003", 'ten_phu_tung' => "SCREW, WAVE WASHER", 
            'gia_ban' => 44000, 'is_gia_lien_he' => true, 'trang_thai' => 1 
          ],
          [ 
            'id' => 14, 'thuong_hieu_id' => 2, 'nhom_phu_tung_id' => 2, 
            'ma_phu_tung' => "31630-ZY3-013", 'ten_phu_tung' => "SEE PART DETAILS - PRI; ALTERNATOR ASSY.", 
            'gia_ban' => 30237000, 'is_gia_lien_he' => true, 'trang_thai' => 1 
          ],
          [ 
            'id' => 15, 'thuong_hieu_id' => 2, 'nhom_phu_tung_id' => 2, 
            'ma_phu_tung' => "31631-ZY3-003", 'ten_phu_tung' => "ROTOR ASSY.", 
            'gia_ban' => 11023000, 'is_gia_lien_he' => true, 'trang_thai' => 1 
          ],
          [ 
            'id' => 16, 'thuong_hieu_id' => 2, 'nhom_phu_tung_id' => 2, 
            'ma_phu_tung' => "31637-ZY3-000", 'ten_phu_tung' => "STAY, ALTERNATOR", 
            'gia_ban' => 125000, 'is_gia_lien_he' => true, 'trang_thai' => 1 
          ],
          [ 
            'id' => 17, 'thuong_hieu_id' => 2, 'nhom_phu_tung_id' => 2, 
            'ma_phu_tung' => "31639-ZY3-003", 'ten_phu_tung' => "END COVER, RR.", 
            'gia_ban' => 3674000, 'is_gia_lien_he' => true, 'trang_thai' => 1 
          ],
          [ 
            'id' => 18, 'thuong_hieu_id' => 2, 'nhom_phu_tung_id' => 2, 
            'ma_phu_tung' => "31694-ZY3-003", 'ten_phu_tung' => "BUSH, INSULATION", 
            'gia_ban' => 250000, 'is_gia_lien_he' => true, 'trang_thai' => 1 
          ],
          [ 
            'id' => 19, 'thuong_hieu_id' => 2, 'nhom_phu_tung_id' => 2, 
            'ma_phu_tung' => "31710-ZY3-003", 'ten_phu_tung' => "RECTIFIER ASSY.", 
            'gia_ban' => 4886000, 'is_gia_lien_he' => true, 'trang_thai' => 1 
          ],
          [ 
            'id' => 20, 'thuong_hieu_id' => 2, 'nhom_phu_tung_id' => 2, 
            'ma_phu_tung' => "31750-ZY3-003", 'ten_phu_tung' => "REGULATOR ASSY.", 
            'gia_ban' => 6487000, 'is_gia_lien_he' => true, 'trang_thai' => 1 
          ],
          [ 
            'id' => 21, 'thuong_hieu_id' => 2, 'nhom_phu_tung_id' => 2, 
            'ma_phu_tung' => "90002-ZY3-000", 'ten_phu_tung' => "BOLT, FLANGE (10X90)", 
            'gia_ban' => 0, 'is_gia_lien_he' => true, 'trang_thai' => 1 
          ],
          [ 
            'id' => 22, 'thuong_hieu_id' => 2, 'nhom_phu_tung_id' => 2, 
            'ma_phu_tung' => "90011-ZW5-010", 'ten_phu_tung' => "BOLT, SPECIAL ADJUSTER", 
            'gia_ban' => 138000, 'is_gia_lien_he' => true, 'trang_thai' => 1 
          ],
          [ 
            'id' => 23, 'thuong_hieu_id' => 2, 'nhom_phu_tung_id' => 2, 
            'ma_phu_tung' => "90017-ZY3-000", 'ten_phu_tung' => "BOLT, FLANGE (6X16)", 
            'gia_ban' => 31000, 'is_gia_lien_he' => true, 'trang_thai' => 1 
          ],
          [ 
            'id' => 24, 'thuong_hieu_id' => 2, 'nhom_phu_tung_id' => 2, 
            'ma_phu_tung' => "90055-ZY3-000", 'ten_phu_tung' => "BOXT, TENSIONER", 
            'gia_ban' => 0, 'is_gia_lien_he' => true, 'trang_thai' => 1 
          ],
          [ 
            'id' => 25, 'thuong_hieu_id' => 2, 'nhom_phu_tung_id' => 2, 
            'ma_phu_tung' => "90203-ZW5-010", 'ten_phu_tung' => "NUT, FLANGE (8MM)", 
            'gia_ban' => 43000, 'is_gia_lien_he' => true, 'trang_thai' => 1 
          ],
          [ 
            'id' => 26, 'thuong_hieu_id' => 2, 'nhom_phu_tung_id' => 2, 
            'ma_phu_tung' => "90231-PD1-014", 'ten_phu_tung' => "NUT-WASHER (5MM)", 
            'gia_ban' => 64000, 'is_gia_lien_he' => true, 'trang_thai' => 1 
          ],
          [ 
            'id' => 27, 'thuong_hieu_id' => 2, 'nhom_phu_tung_id' => 2, 
            'ma_phu_tung' => "94001-06080-0S", 'ten_phu_tung' => "NUT, HEX. (6MM)", 
            'gia_ban' => 30000, 'is_gia_lien_he' => true, 'trang_thai' => 1 
          ],
          [ 'id' => 1000, 'thuong_hieu_id' => 1, 'nhom_phu_tung_id' => 100, 'ma_phu_tung' => "6P2-42710-00-8D", 'ten_phu_tung' => "BOTTOM COWLING ASSY", 'gia_ban' => 19900000, 'is_gia_lien_he' => true, 'trang_thai' => 1 ],
          [ 'id' => 1001, 'thuong_hieu_id' => 1, 'nhom_phu_tung_id' => 100, 'ma_phu_tung' => "6P2-42738-00-5B", 'ten_phu_tung' => "PLATE, FITTING", 'gia_ban' => 1070000, 'is_gia_lien_he' => true, 'trang_thai' => 1 ],
          [ 'id' => 1002, 'thuong_hieu_id' => 1, 'nhom_phu_tung_id' => 100, 'ma_phu_tung' => "6P2-42717-00-00", 'ten_phu_tung' => "RUBBER, SEAL 2", 'gia_ban' => 267000, 'is_gia_lien_he' => true, 'trang_thai' => 1 ],
          [ 'id' => 1003, 'thuong_hieu_id' => 1, 'nhom_phu_tung_id' => 100, 'ma_phu_tung' => "6P2-42718-00-00", 'ten_phu_tung' => "RUBBER, SEAL 3", 'gia_ban' => 267000, 'is_gia_lien_he' => true, 'trang_thai' => 1 ],
          [ 'id' => 1004, 'thuong_hieu_id' => 1, 'nhom_phu_tung_id' => 100, 'ma_phu_tung' => "90109-06M82-00", 'ten_phu_tung' => "BOLT", 'gia_ban' => 57000, 'is_gia_lien_he' => true, 'trang_thai' => 1 ],
          [ 'id' => 1005, 'thuong_hieu_id' => 1, 'nhom_phu_tung_id' => 100, 'ma_phu_tung' => "6H4-42748-00-00", 'ten_phu_tung' => "GROMMET", 'gia_ban' => 39000, 'is_gia_lien_he' => true, 'trang_thai' => 1 ],
          [ 'id' => 1006, 'thuong_hieu_id' => 1, 'nhom_phu_tung_id' => 100, 'ma_phu_tung' => "6P2-42725-00-00", 'ten_phu_tung' => "GROMMET", 'gia_ban' => 78000, 'is_gia_lien_he' => true, 'trang_thai' => 1 ],
          [ 'id' => 1007, 'thuong_hieu_id' => 1, 'nhom_phu_tung_id' => 100, 'ma_phu_tung' => "6P2-42615-00-00", 'ten_phu_tung' => "SEAL", 'gia_ban' => 328000, 'is_gia_lien_he' => true, 'trang_thai' => 1 ],
          [ 'id' => 1008, 'thuong_hieu_id' => 1, 'nhom_phu_tung_id' => 100, 'ma_phu_tung' => "69J-42716-00-00", 'ten_phu_tung' => "RUBBER SEAL 1", 'gia_ban' => 1535000, 'is_gia_lien_he' => true, 'trang_thai' => 1 ],
          [ 'id' => 1009, 'thuong_hieu_id' => 1, 'nhom_phu_tung_id' => 100, 'ma_phu_tung' => "90119-08M22-00", 'ten_phu_tung' => "BOLT, WITH WASHER", 'gia_ban' => 75000, 'is_gia_lien_he' => true, 'trang_thai' => 1 ],
          [ 'id' => 1010, 'thuong_hieu_id' => 1, 'nhom_phu_tung_id' => 100, 'ma_phu_tung' => "90480-22M81-00", 'ten_phu_tung' => "GROMMET", 'gia_ban' => 88000, 'is_gia_lien_he' => true, 'trang_thai' => 1 ],
          [ 'id' => 1011, 'thuong_hieu_id' => 1, 'nhom_phu_tung_id' => 100, 'ma_phu_tung' => "90387-08M37-00", 'ten_phu_tung' => "COLLAR", 'gia_ban' => 220000, 'is_gia_lien_he' => true, 'trang_thai' => 1 ],
          [ 'id' => 1012, 'thuong_hieu_id' => 1, 'nhom_phu_tung_id' => 100, 'ma_phu_tung' => "6H1-42726-21-00", 'ten_phu_tung' => "GROMMET", 'gia_ban' => 155000, 'is_gia_lien_he' => true, 'trang_thai' => 1 ],
          [ 'id' => 1013, 'thuong_hieu_id' => 1, 'nhom_phu_tung_id' => 100, 'ma_phu_tung' => "90465-11M10-00", 'ten_phu_tung' => "CLAMP", 'gia_ban' => 70000, 'is_gia_lien_he' => true, 'trang_thai' => 1 ],
          [ 'id' => 1014, 'thuong_hieu_id' => 1, 'nhom_phu_tung_id' => 100, 'ma_phu_tung' => "61A-42727-00-00", 'ten_phu_tung' => "GROMMET", 'gia_ban' => 60000, 'is_gia_lien_he' => true, 'trang_thai' => 1 ],
          [ 'id' => 3001, 'thuong_hieu_id' => 7, 'nhom_phu_tung_id' => 111, 'ma_phu_tung' => "8M0142205", 'ten_phu_tung' => "ACCENT COWL ASSEMBLY Redline Red", 'gia_ban' => 17000000, 'is_gia_lien_he' => true, 'trang_thai' => 1 ],
          [ 'id' => 3002, 'thuong_hieu_id' => 7, 'nhom_phu_tung_id' => 111, 'ma_phu_tung' => "8M0142206", 'ten_phu_tung' => "ACCENT COWL ASSEMBLY Pacific Blue", 'gia_ban' => 17000000, 'is_gia_lien_he' => true, 'trang_thai' => 1 ],
          [ 'id' => 3003, 'thuong_hieu_id' => 7, 'nhom_phu_tung_id' => 111, 'ma_phu_tung' => "8M0142208", 'ten_phu_tung' => "ACCENT COWL ASSEMBLY Graphite Grey", 'gia_ban' => 17000000, 'is_gia_lien_he' => true, 'trang_thai' => 1 ],
          [ 'id' => 3004, 'thuong_hieu_id' => 7, 'nhom_phu_tung_id' => 111, 'ma_phu_tung' => "8M0142210", 'ten_phu_tung' => "ACCENT COWL ASSEMBLY Ready to Paint", 'gia_ban' => 16500000, 'is_gia_lien_he' => true, 'trang_thai' => 1 ],
          [ 'id' => 3005, 'thuong_hieu_id' => 7, 'nhom_phu_tung_id' => 111, 'ma_phu_tung' => "8M0142207", 'ten_phu_tung' => "ACCENT COWL ASSEMBLY Mercury Silver", 'gia_ban' => 17000000, 'is_gia_lien_he' => true, 'trang_thai' => 1 ],
          [ 'id' => 3006, 'thuong_hieu_id' => 7, 'nhom_phu_tung_id' => 111, 'ma_phu_tung' => "8M0142209", 'ten_phu_tung' => "ACCENT COWL ASSEMBLY Pro Black", 'gia_ban' => 16800000, 'is_gia_lien_he' => true, 'trang_thai' => 1 ],
          [ 'id' => 3007, 'thuong_hieu_id' => 7, 'nhom_phu_tung_id' => 111, 'ma_phu_tung' => "8M0147920", 'ten_phu_tung' => "DECAL Mercury, Silver", 'gia_ban' => 1000000, 'is_gia_lien_he' => true, 'trang_thai' => 1 ],
          [ 'id' => 3008, 'thuong_hieu_id' => 7, 'nhom_phu_tung_id' => 111, 'ma_phu_tung' => "8M0147917", 'ten_phu_tung' => "DECAL Mercury, Dark Silver", 'gia_ban' => 500000, 'is_gia_lien_he' => true, 'trang_thai' => 1 ],
          [ 'id' => 3009, 'thuong_hieu_id' => 7, 'nhom_phu_tung_id' => 111, 'ma_phu_tung' => "8M0043703", 'ten_phu_tung' => "DECAL M-Icon, 66 MM, Black", 'gia_ban' => 300000, 'is_gia_lien_he' => true, 'trang_thai' => 1 ]
        
        ];
        
        $imageUrl = 'https://imgs.search.brave.com/Tuuaec6b4bnBCERP1mrixq3iANwRwsLWmVGN-fgKsxU/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9tZWRp/YS5pc3RvY2twaG90/by5jb20vaWQvMTYy/MzI0OTQxL3ZpL2Fu/aC9tJUUxJUJCJTk5/dC1jaGklRTElQkEl/QkZjLWR1LXRodXkl/RTElQkIlODFuLW0l/QzMlQTB1LXRyJUUx/JUJBJUFGbmctcyVF/MSVCQiU4RGMteGFu/aC1jaCVFMSVCQSVB/MXktcXUlQzMlQTEt/dCVFMSVCQiU5MWMt/JUM0JTkxJUUxJUJC/JTk5LXRyJUMzJUFB/bi1iaSVFMSVCQiU4/M24uanBnP3M9NjEy/eDYxMiZ3PTAmaz0y/MCZjPWo0QVc2aWxC/OThPc1Qwc2k2WEJa/RWdZeG1lV042cTE1/YjItdFd1SDlQUnM9';

        foreach ($data as &$row) {
            $row['hinh_anh'] = $imageUrl;
            if (isset($row['is_gia_lien_he'])) {
                $row['is_gia_lien_he'] = $row['is_gia_lien_he'] ? 1 : 0;
            }
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
        }

        $chunks = array_chunk($data, 50);
        foreach ($chunks as $chunk) {
            DB::table('phu_tungs')->insert($chunk);
        }
    }
}





