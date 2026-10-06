<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\IAM\Models\User;
use Illuminate\Notifications\Notification;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        foreach($users as $user) {
            $user->notifications()->delete();
            $user->notify(new class extends Notification {
                public function via($notifiable) { return ['database']; }
                public function toArray($notifiable) {
                    return [
                        'title' => 'Đơn hàng #DH-202410 Đã Giao',
                        'body' => 'Đơn hàng của bạn đã được giao thành công. Cảm ơn bạn đã mua sắm!',
                        'icon' => 'bi-box-seam',
                        'color' => 'success',
                        'url' => '/lich-su-mua-hang'
                    ];
                }
            });
            $user->notify(new class extends Notification {
                public function via($notifiable) { return ['database']; }
                public function toArray($notifiable) {
                    return [
                        'title' => 'Khuyến mãi đặc biệt!',
                        'body' => 'Giảm giá 15% cho tất cả phụ tùng Yamaha duy nhất trong tháng này.',
                        'icon' => 'bi-percent',
                        'color' => 'danger',
                        'url' => '/thuong-hieu/yamaha'
                    ];
                }
            });
            $user->notify(new class extends Notification {
                public function via($notifiable) { return ['database']; }
                public function toArray($notifiable) {
                    return [
                        'title' => 'Cập nhật hệ thống',
                        'body' => 'Hệ thống JPM ERP đã nâng cấp phiên bản 2.0. Trải nghiệm mượt mà hơn.',
                        'icon' => 'bi-info-circle',
                        'color' => 'primary',
                        'url' => '/'
                    ];
                }
            });
        }
    }
}
