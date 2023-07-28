<?php

namespace CrehlerPlugin\Subscriber;

use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductLoadedSubscriber implements EventSubscriberInterface
{
    private $systemConfigService;
    private $productRepository;

    public function __construct(SystemConfigService $systemConfigService, EntityRepositoryInterface $productRepository)
    {
        $this->systemConfigService = $systemConfigService;
        $this->productRepository = $productRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'product.loaded' => 'onProductLoaded'
        ];
    }

    public function onProductLoaded(EntityLoadedEvent $event): void
    {
        // Pobranie produktów z zdarzenia
        $products = $event->getEntities();

        // Pobranie maksymalnej ilości znaków z opcji konfiguracyjnej wtyczki
        $maxTextLength = $this->systemConfigService->get('CrehlerPlugin.config.maxTextLength');

        // Iteracja po produktach
        foreach ($products as $product) {
            // Pobranie pełnego tekstu z pola tekstowego produktu
            $fullText = 'Pełny tekst z pola tekstowego produktu';

            // Obliczenie skróconego tekstu na podstawie pełnego tekstu i maksymalnej ilości znaków
            $shortText = strlen($fullText) > $maxTextLength ? substr($fullText, 0, $maxTextLength) . '...' : $fullText;

            // Zapisanie skróconego tekstu w bazie danych dla pola niestandardowego produktu
            $this->productRepository->update([
                [
                    'id' => $product->getId(),
                    'customFields' => [
                        'crehler_plugin_custom_text_field' => $shortText
                    ]
                ]
            ], $event->getContext());
        }
    }
}
