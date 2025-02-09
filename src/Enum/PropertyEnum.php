<?php
namespace App\Enum;

enum PropertyEnum: string {
    case APPARTEMENT = 'Appartement';
    case HOUSE = 'Maison';
    case GARAGE = 'Garage / Box';
    case PARKING_SPACE = 'Place de parking';
}