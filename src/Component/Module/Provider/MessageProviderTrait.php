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

use Illuminate\Foundation\Application;
use Pandawa\Component\Message\AbstractMessage;
use Pandawa\Component\Message\NameableMessageInterface;
use ReflectionClass;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 * @property Application $app
 *
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait MessageProviderTrait
{
    protected $messagePathNames = ['Command', 'Query'];

    public function registerMessageProvider(): void
    {
        if (file_exists($this->app->getCachedConfigPath())) {
            return;
        }

        foreach ($this->messagePathNames as $messagePathName) {
            $basePath = $this->getCurrentPath() . '/' . $messagePathName;

            if (is_dir($basePath)) {
                $finder = new Finder();

                /** @var SplFileInfo $file */
                foreach ($finder->in($basePath)->notName('*Handler.php')->files() as $file) {
                    $messageClass = $this->getClassFromFile($file);
                    $reflection = new ReflectionClass($messageClass);

                    if (!$reflection->isAbstract() && $reflection->isSubclassOf(AbstractMessage::class)) {
                        $name = $this->getMessageName($messageClass);

                        $this->mergeConfig(
                            'pandawa_messages',
                            [
                                $name => [
                                    'messageClass' => $messageClass,
                                    'handlerClass' => $this->getHandlerClass($messageClass),
                                ],
                            ]
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
}
