<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
    
        Event::listen(BuildingMenu::class, function (BuildingMenu $event) {
            // Add some items to the menu...
            
            $menus =  User::select('system_menu_mapping.*','system_menu.*')->join('system_user_group','system_user_group.user_group_id','=','system_user.user_group_id')->join('system_menu_mapping','system_menu_mapping.user_group_level','=','system_user_group.user_group_level')->join('system_menu','system_menu.id_menu','=','system_menu_mapping.id_menu')->where('system_user.user_id','=',Auth::id())->orderBy('system_menu_mapping.id_menu','ASC')->get();

            $last_key   = 'tes';
            $last_key2  = 'tes';
            $last_key3  = 'tes';
            foreach($menus as $key => $val){
                if($val['indent_level']==1){
                    $event->menu->add([
                        'key'       => $val['id_menu'],
                        'text'      => $val['text'],
                        'url'       => $val['id'],
                        'active'    => [$val['id'].'/*'],
                        'icon'      => '',
                    ]);
                    $last_key = $val['id_menu'];
                }else if($val['indent_level']==2){
                    $event->menu->addIn($last_key,[
                        'key'       => $val['id_menu'],
                        'text'      => $val['text'],
                        'url'       => $val['id'],
                        'active'    => [$val['id'].'/*'],
                        'classes'   => 'level-two',
                        'icon'      => '',
                    ]);
                    $last_key2 = $val['id_menu'];
                }else if($val['indent_level']==3){
                    $event->menu->addIn($last_key2,[
                        'key'       => $val['id_menu'],
                        'text'      => $val['text'],
                        'url'       => $val['id'],
                        'active'    => [$val['id'].'/*'],
                        'classes'   => 'level-three',
                        'icon'      => '',
                    ]);
                    $last_key3 = $val['id_menu'];
                }else if($val['indent_level']==4){
                    $event->menu->addIn($last_key3,[
                        'key'       => $val['id_menu'],
                        'text'      => $val['text'],
                        'url'       => $val['id'],
                        'active'    => [$val['id'].'/*'],
                        'classes'   => 'level-four',
                        'icon'      => '',
                    ]);
                }
            }
        });
    }
}
