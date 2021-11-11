<?php

namespace Pushword\AdminBlockEditor;

use Exception;
use Pushword\AdminBlockEditor\Block\BlockInterface;
use Pushword\AdminBlockEditor\Block\DefaultBlock;
use Pushword\Core\AutowiringTrait\RequiredAppTrait;
use Pushword\Core\AutowiringTrait\RequiredEntityTrait;
use Pushword\Core\AutowiringTrait\RequiredTwigTrait;
use Pushword\Core\Component\EntityFilter\Filter\AbstractFilter;
use Pushword\Core\Twig\ClassTrait;

final class BlockEditorFilter extends AbstractFilter
{
    use ClassTrait;
    use RequiredAppTrait;
    use RequiredEntityTrait;
    use RequiredTwigTrait;

    private ?array $appBlocks = null;

    /**
     * @return string
     */
    public function apply($propertyValue)
    {
        $json = json_decode($propertyValue);

        if (false === $json || null === $json) {
            return $propertyValue;
        }

        $blocks = $json->blocks;

        $renderValue = '';

        foreach ($blocks as $block) {
            $classBlock = $this->getBlockManager($block->type);
            $blockRendered = $classBlock->render($block->data);
            $renderValue .= $blockRendered."\n";
        }

        return $renderValue;
    }

    private function loadBlockManager(BlockInterface $block): BlockInterface
    {
        $block
            ->setApp($this->app)
            ->setEntity($this->getEntity())
            ->setTwig($this->getTwig())
        ;

        return $block;
    }

    private function getBlockManager(string $type): BlockInterface
    {
        $blocks = $this->getAppBlocks();

        if (! isset($blocks[$type])) {
            throw new Exception('Block `'.$type.'` not configured to be used.');
        }

        return $blocks[$type];
    }

    /**
     * @psalm-suppress NullableReturnStatement
     * @psalm-suppress InvalidNullableReturnType
     */
    private function getAppBlocks(): array
    {
        if (\is_array($this->appBlocks)) {
            return $this->appBlocks;
        }

        $blocks = $this->app->get('admin_block_editor_blocks');

        foreach ($blocks as $block) {
            if (class_exists($block)) {
                $this->appBlocks[$block::NAME] = $this->loadBlockManager(new $block());

                continue;
            }

            if (\in_array($block, DefaultBlock::AVAILABLE_BLOCKS)) {
                $this->appBlocks[$block] = $this->loadBlockManager(new DefaultBlock($block));

                continue;
            }

            $class = '\Pushword\AdminBlockEditor\Block\\'.ucfirst($block).'Block';
            if (class_exists($class)) {
                $this->appBlocks[$block] = $this->loadBlockManager(new $class());

                continue;
            }

            throw new Exception('Block Manager for `'.$block.'` not found.');
        }

        return $this->appBlocks;
    }
}
