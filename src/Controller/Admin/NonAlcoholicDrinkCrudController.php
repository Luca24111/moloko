<?php

namespace App\Controller\Admin;

final class NonAlcoholicDrinkCrudController extends AbstractDrinkCrudController
{
    protected function isAlcoholicGroup(): bool
    {
        return false;
    }
}
