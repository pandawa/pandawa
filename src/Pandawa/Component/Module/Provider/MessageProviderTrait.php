<?php
/**
 * This file is part of the Pandawa package.
 *
 * (c) 2018 Pandawa <https://github.com/bl4ckbon3/pandawa>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Pandawa\Component\Module\Provider;

use Pandawa\Component\Message\AbstractMessage;
use Pandawa\Component\Message\MessageRegistryInterface;
use Pandawa\Component\Message\Metadata;
use Pandawa\Component\Message\NameableMessageInterface;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait MessageProviderTrait
{
    protected $messagePathNames = ['Command', 'Query'];

    public function bootMessageProvider(): void
    {
        if (null === $this->messageRegistry()) {
            return;
        }

        foreach ($this->messagePathNames as $messagePathName) {
            $basePath = $this->getCurrentPath() . '/' . $messagePathName;

            if (is_dir($basePath)) {
                $finder = new Finder();

                /** @var SplFileInfo $file */
                foreach ($finder->in($basePath) as $file) {
                    $messageClass = $this->getClassFromFile($file);

                    if (is_subclass_of($messagePathName, AbstractMessage::class)) {
                        $name = $this->getMessageName($messageClass);

                        $this->messageRegistry()->add(
                            $name,
                            new Metadata($messageClass, $this->getHandlerClass($messageClass))
                        );
                    }
                }
            }
        }
    }

    private function getHandlerClass(string $messageClass): ?string
    {
        $handlerClass = sprintf('%sHandler', $messageClass);

        if (class_exists($handlerClass)) {
            return $handlerClass;
        }

        return null;
    }

    private function getMessageName(string $messageClass): string
    {
        if (in_array(NameableMessageInterface::class, class_implements($messageClass))) {
            return $messageClass::{'name'}();
        }

        return $messageClass;
    }

    private function messageRegistry(): ?MessageRegistryInterface
    {
        if (isset($this->app[MessageRegistryInterface::class])) {
            return $this->app[MessageRegistryInterface::class];
        }

        return null;
    }
}
