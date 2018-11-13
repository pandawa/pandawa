<?php
declare(strict_types=1);

namespace Pandawa\Module\Reactive\Observable;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Pandawa\Module\Reactive\Operator\PipeOperatorTrait;
use Psr\Http\Message\ResponseInterface;
use Rx\Disposable\CompositeDisposable;
use Rx\DisposableInterface;
use Rx\Observable;
use Rx\ObserverInterface;
use Rx\SchedulerInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class HttpClientObservable extends Observable
{
    use PipeOperatorTrait;

    /**
     * @var Client
     */
    private $client;
    /**
     * @var string
     */
    private $uri;
    /**
     * @var string
     */
    private $method;
    /**
     * @var array
     */
    private $options;
    /**
     * @var SchedulerInterface
     */
    private $scheduler;

    /**
     * Constructor.
     *
     * @param string             $uri
     * @param string             $method
     * @param array              $options
     * @param SchedulerInterface $scheduler
     */
    public function __construct(string $uri, string $method, array $options, SchedulerInterface $scheduler)
    {
        $this->uri = $uri;
        $this->method = $method;
        $this->options = $options;
        $this->scheduler = $scheduler;
    }

    /**
     * @param ObserverInterface $observer
     *
     * @return DisposableInterface
     */
    protected function _subscribe(ObserverInterface $observer): DisposableInterface
    {
        $disposable = new CompositeDisposable();

        $disposable->add($this->scheduler->schedule(function () use ($observer) {
            try {
                $observer->onNext($this->request($this->uri, $this->method, $this->options));
            } catch (GuzzleException $e) {
                $observer->onError($e);
            }
        }));

        $disposable->add($this->scheduler->schedule(function () use ($observer) {
            $observer->onCompleted();
        }));

        return $disposable;
    }

    /**
     * @param string $uri
     * @param string $method
     * @param array  $options
     *
     * @return ResponseInterface
     * @throws GuzzleException
     */
    protected function request(string $uri, string $method, array $options): ResponseInterface
    {
        return (new Client())->request($method, $uri, $options);
    }
}
