<?php

namespace App\Controllers;
use App\Core\Controller;
use App\Models\Employee;
use App\Models\Event;


class CalendarController extends Controller {

    public function index()
    {
       
        
        $user = Employee::find('1003'); 
      
        $this->render('/tools/calendar', ['user' => $user]);
    }

    public function allEvents()
    {
        $events = Event::all();

        $formatted = [];

        foreach ($events as $event) {
            $formatted[] = [
                'id' => $event['id'],
                'title' => $event['title'],
                'start' => $event['start'],
                'description' => $event['description'],
                'end' => $event['end'],
                'allDay' => (bool) $event['all_day'],
                'backgroundColor' => $event['background_color'],
                'borderColor' => $event['border_color'],
                'textColor' => $event['text_color'],
            ];
        }

        header('Content-Type: application/json');

        echo json_encode($formatted);

    }


}