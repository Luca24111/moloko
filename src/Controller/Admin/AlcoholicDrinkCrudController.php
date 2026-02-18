<?php

namespace App\Controller\Admin;

final class AlcoholicDrinkCrudController extends AbstractDrinkCrudController
{
    protected function isAlcoholicGroup(): bool
    {
        return true;
    }
}
