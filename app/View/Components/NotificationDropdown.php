<?php

namespace App\View\Components;

use Illuminate\View\Component;

class NotificationDropdown extends Component
{
    public $notifications;

    public function __construct($notifications)
    {
        $this->notifications = $notifications;
    }

    public function render()
    {
        return view('components.notification-dropdown');
    }
} 