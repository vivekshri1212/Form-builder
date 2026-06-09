<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FormBuilderController extends Controller
{
    /**
     * All supported field types with display labels and icons.
     */
    private array $fieldTypes = [
        ['type' => 'text',     'label' => 'Text Input',     'icon' => '📝'],
        ['type' => 'number',   'label' => 'Number',         'icon' => '🔢'],
        ['type' => 'email',    'label' => 'Email',          'icon' => '📧'],
        ['type' => 'phone',    'label' => 'Phone',          'icon' => '📞'],
        ['type' => 'textarea', 'label' => 'Text Area',      'icon' => '📄'],
        ['type' => 'dropdown', 'label' => 'Dropdown',       'icon' => '▾'],
        ['type' => 'radio',    'label' => 'Radio Buttons',  'icon' => '⊙'],
        ['type' => 'checkbox', 'label' => 'Checkboxes',     'icon' => '☑'],
        ['type' => 'date',     'label' => 'Date',           'icon' => '📅'],
        ['type' => 'hidden',   'label' => 'Hidden Field',   'icon' => '🔒'],
    ];

    /**
     * Show the form builder UI.
     */
    public function index()
    {
        return view('form-builder', [
            'fieldTypes' => $this->fieldTypes,
        ]);
    }
}
