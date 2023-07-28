<?php

namespace CrehlerPlugin\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Defaults;

class Migration1627470380AddCustomField extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1627470380;
    }

    public function update(Connection $connection): void
    {
        // Utworzenie nowego pola niestandardowego
        $customFieldId = Uuid::randomBytes();
        $connection->insert('custom_field', [
            'id' => $customFieldId,
            'name' => 'crehler_plugin_custom_text_field',
            'type' => 'text',
            'config' => json_encode([
                'type' => 'text',
                'customFieldPosition' => 1,
                'componentName' => 'sw-field',
                'label' => [
                    'en-GB' => 'Custom Text Field',
                    'de-DE' => 'Benutzerdefiniertes Textfeld'
                ]
            ]),
            'active' => 1,
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)
        ]);

        // Dodanie nowego pola niestandardowego do zbioru pól niestandardowych dla produktu
        $connection->insert('custom_field_set_relation', [
            'id' => Uuid::randomBytes(),
            'custom_field_id' => $customFieldId,
            'custom_field_set_id' => $this->getCustomFieldSetId($connection),
            'position' => 1,
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)
        ]);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }

    private function getCustomFieldSetId(Connection $connection): string
    {
        // Pobranie ID zbioru pól niestandardowych dla produktu
        $customFieldSetId = $connection->executeQuery('SELECT id FROM custom_field_set WHERE name = "product"')->fetchColumn();

        if (!$customFieldSetId) {
            // Utworzenie nowego zbioru pól niestandardowych dla produktu, jeśli nie istnieje
            $customFieldSetId = Uuid::randomBytes();
            $connection->insert('custom_field_set', [
                'id' => $customFieldSetId,
                'name' => 'product',
                'config' => json_encode([
                    'label' => [
                        'en-GB' => 'Product',
                        'de-DE' => 'Produkt'
                    ]
                ]),
                'active' => 1,
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)
            ]);

            // Dodanie nowego zbioru pól niestandardowych do encji produktu
            $connection->insert('custom_field_set_relation', [
                'id' => Uuid::randomBytes(),
                'set_id' => $customFieldSetId,
                'entity_name' => 'product',
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)
            ]);
        }

        return $customFieldSetId;
    }
}
