<?php

namespace Pushword\AdminBlockEditor\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;

class EditorjsType extends TextType
{
    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'editorjs';
    }
}
