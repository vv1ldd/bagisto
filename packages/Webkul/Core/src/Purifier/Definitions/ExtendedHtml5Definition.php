<?php

namespace Webkul\Core\Purifier\Definitions;

use HTMLPurifier_HTMLDefinition;
use Stevebauman\Purify\Definitions\Definition;
use Stevebauman\Purify\Definitions\Html5Definition;

class ExtendedHtml5Definition implements Definition
{
    /**
     * Apply rules to the HTML Purifier definition.
     */
    public static function apply(HTMLPurifier_HTMLDefinition $definition)
    {
        Html5Definition::apply($definition);

        $definition->addElement('div', 'Block', 'Flow', 'Common', [
            'class' => 'Class',
            'id' => 'Text',
        ]);

        $definition->addElement('button', 'Inline', 'Flow', 'Common', [
            'class' => 'Class',
            'type' => 'Enum#button,submit,reset',
        ]);

        $definition->addElement('iframe', 'Block', 'Flow', 'Common', [
            'src' => 'URI',
            'width' => 'Length',
            'height' => 'Length',
            'style' => 'Text',
            'class' => 'Class',
            'id' => 'Text',
            'title' => 'Text',
            'allow' => 'Text',
            'allowfullscreen' => 'Bool',
            'frameborder' => 'Text',
            'scrolling' => 'Enum#yes,no,auto',
        ]);

        $definition->addAttribute('img', 'data-src', 'URI');
        $definition->addAttribute('img', 'loading', 'Enum#lazy,eager');
        $definition->addAttribute('span', 'data-custom', 'Text');
        $definition->addAttribute('div', 'data-custom', 'Text');
    }
}
