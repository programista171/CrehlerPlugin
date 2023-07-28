<?php

namespace CrehlerPlugin\Controller\Administration;

use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Routing\AbstractRouteScope;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @RouteScope(scopes={"api"})
 */
class ConfigController extends AbstractController
{
    /**
     * @Route("/api/v{version}/_action/crehler-plugin/config", name="api.action.crehler-plugin.config", methods={"GET"})
     */
    public function getConfig(Context $context): JsonResponse
    {
        // Pobranie konfiguracji wtyczki z bazy danych
        $config = $this->container->get('system_config_service')->get('CrehlerPlugin.config', $context->getScopeId());

        // Zwrócenie konfiguracji wtyczki jako odpowiedzi JSON
        return new JsonResponse($config);
    }

    /**
     * @Route("/api/v{version}/_action/crehler-plugin/config", name="api.action.crehler-plugin.config.save", methods={"POST"})
     */
    public function saveConfig(RequestDataBag $data, Context $context): JsonResponse
    {
        // Zapisanie konfiguracji wtyczki w bazie danych
        $this->container->get('system_config_service')->set('CrehlerPlugin.config', $data->all(), $context->getScopeId());

        // Zwrócenie pustej odpowiedzi JSON
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
