<?php

namespace Pushword\AdminBlockEditor\EventSuscriber;

use Pushword\Admin\FormField\Event as FormEvent;
use Pushword\Admin\FormField\PageH1Field;
use Pushword\Admin\FormField\PageMainContentField;
use Pushword\Admin\PageAdminInterface;
use Pushword\Admin\Utils\FormFieldReplacer;
use Pushword\AdminBlockEditor\FormField\PageH1FormField;
use Pushword\AdminBlockEditor\FormField\PageImageFormField;
use Pushword\AdminBlockEditor\FormField\PageMainContentFormField;
use Pushword\Core\Entity\PageInterface;
use Sonata\AdminBundle\Event\PersistenceEvent;

class AdminFormEventSuscriber extends AbstractEventSuscriber
{
    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'pushword.admin.load_field' => 'replaceFields',
            'sonata.admin.event.persistence.pre_update' => 'setMainContent',
            'sonata.admin.event.persistence.pre_persist' => 'setMainContent',
        ];
    }

    public function setMainContent(PersistenceEvent $persistenceEvent): void
    {
        if (! $persistenceEvent->getAdmin() instanceof PageAdminInterface) {
            return;
        }

        $returnValues = $persistenceEvent->getAdmin()->getRequest()->get($persistenceEvent->getAdmin()->getRequest()->get('uniqid'));
        //dd($returnValues);
        if (isset($returnValues['mainContent'])) {
            // sanitize with https://github.com/editor-js/editorjs-php
            $persistenceEvent->getAdmin()->getSubject()->setMainContent($returnValues['mainContent']);
        }
    }

    /** @psalm-suppress  NoInterfaceProperties */
    public function replaceFields(FormEvent $formEvent): void
    {
        if (! $formEvent->getAdmin() instanceof PageAdminInterface || ! $this->mayUseEditorBlock($formEvent->getAdmin()->getSubject())) {
            return;
        }

        $fields = $formEvent->getFields();

        $fields = (new FormFieldReplacer())->run(PageMainContentField::class, PageMainContentFormField::class, $fields);
        $fields = (new FormFieldReplacer())->run(PageH1Field::class, PageH1FormField::class, $fields);

        $fields[0][PageImageFormField::class] = PageImageFormField::class;

        $formEvent->setFields($fields);

        /** @var PageInterface $page */
        $page = $formEvent->getAdmin()->getSubject();
        $page->jsMainContent = $this->transformMainContent($page->getMainContent());
    }

    /**
     * @return string
     */
    private function transformMainContent(string $content)
    {
        // We never come to false here because we ever checked before with mayUseEditorBlock
        /*
        $jsonContent = json_decode($content);
        if (false === $jsonContent) {
            // we just start to use editor.js for this page... try parsing raw content and creating a JS
            return json_encode(['blocks' => [['type' => 'raw', 'data' => ['html' => $content]]]]);
        }*/

        return $content;
    }
}
