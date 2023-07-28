<?php

namespace CrehlerPlugin;

use CrehlerPlugin\Subscriber\ProductLoadedSubscriber;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;

class CrehlerPlugin extends Plugin
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        // Rejestracja subskrybenta zdarzeń
        $container->addCompilerPass(
            new RegisterEventSubscriberCompilerPass(
                ProductLoadedSubscriber::class,
                [ProductLoadedSubscriber::getSubscribedEvents()]
            )
        );

        // Ładowanie pliku konfiguracyjnego dla wtyczki
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');
    }

    public function install(InstallContext $context): void
    {
        parent::install($context);

        // Ustawienie domyślnej wartości dla opcji konfiguracyjnej
        $this->setConfig('maxTextLength', 100);
    }

    public function uninstall(UninstallContext $context): void
    {
        parent::uninstall($context);

        // Usunięcie wartości dla opcji konfiguracyjnej
        if ($context->keepUserData()) {
            return;
        }
        $this->removeConfig('maxTextLength');
    }

    private function setConfig(string $key, $value): void
    {
        /** @var SystemConfigService $systemConfigService */
        $systemConfigService = $this->container->get(SystemConfigService::class);
        $systemConfigService->set('CrehlerPlugin.config.' . $key, $value);
    }

    private function removeConfig(string $key): void
    {
        /** @var SystemConfigService $systemConfigService */
        $systemConfigService = $this->container->get(SystemConfigService::class);
        $systemConfigService->delete('CrehlerPlugin.config.' . $key);
    }
}