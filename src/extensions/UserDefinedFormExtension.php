<?php

namespace Firesphere\PartialUserforms\Extensions;

use Firesphere\PartialUserforms\Models\PartialFormSubmission;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\ORM\DataExtension;

/**
 * Class \Firesphere\PartialUserforms\Extensions\UserDefinedFormExtension
 *
 * @property \Firesphere\PartialUserforms\Extensions\UserDefinedFormExtension $owner
 * @method \SilverStripe\ORM\DataList|\Firesphere\PartialUserforms\Models\PartialFormSubmission[] PartialSubmissions()
 */
class UserDefinedFormExtension extends DataExtension
{
    private static $has_many = [
        'PartialSubmissions' => PartialFormSubmission::class
    ];

    public function updateCMSFields(FieldList $fields)
    {
        /** @var GridFieldConfig_RelationEditor $gridfieldConfig */
        $gridfieldConfig = GridFieldConfig_RelationEditor::create();
        $gridfieldConfig->removeComponentsByType(GridFieldAddNewButton::class);

        $fields->addFieldToTab(
            'Root.PartialSubmissions',
            GridField::create('PartialSubmissions', 'Partial submissions', $this->PartialSubmissions())
        );
    }
}