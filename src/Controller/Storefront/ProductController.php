<?php

namespace CrehlerPlugin\Controller\Storefront;

use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Context;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @RouteScope(scopes={"storefront"})
 */
class ProductController extends StorefrontController
{
    /**
     * @Route("/product/{productId}/full-text", name="storefront.action.product.full-text", methods={"GET"})
     */
    public function getFullText(string $productId, Context $context): Response
    {
        // Utworzenie kryteriów wyszukiwania dla produktu
        $criteria = new Criteria([$productId]);

        // Dodanie filtru dla UUID produktu
        $criteria->addFilter(new EqualsFilter('id', $productId));

        // Pobranie produktu z bazy danych
        /** @var ProductEntity|null $product */
        $product = $this->container->get('product.repository')->search($criteria, $context)->first();

        // Pobranie pełnego tekstu z pola tekstowego produktu
        $fullText = $product ? $product->getCustomFields()['crehler_plugin_custom_text_field'] : '';

        // Zwrócenie pełnego tekstu jako odpowiedzi
        return $this->renderStorefront('@Storefront/storefront/page/product/full-text.html.twig', [
            'fullText' => $fullText
        ]);
    }
}
