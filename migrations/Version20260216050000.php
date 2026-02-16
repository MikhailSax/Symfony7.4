<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260216050000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create core web-to-print tables: orders, order items, pricing rules, product attributes/values and file assets';
    }

    public function up(Schema $schema): void
    {
        $userTable = $this->resolveUserTable($schema);
        $productTable = $this->resolveProductTable($schema);

        $orders = $schema->createTable('orders');
        $orders->addColumn('id', 'integer', ['autoincrement' => true]);
        $orders->addColumn('client_id', 'integer');
        $orders->addColumn('status', 'string', ['length' => 30]);
        $orders->addColumn('total_price', 'decimal', ['precision' => 12, 'scale' => 2]);
        $orders->addColumn('material_cost', 'decimal', ['precision' => 12, 'scale' => 2]);
        $orders->addColumn('comment', 'text', ['notnull' => false]);
        $orders->addColumn('created_at', 'datetime_immutable');
        $orders->addColumn('updated_at', 'datetime_immutable');
        $orders->setPrimaryKey(['id']);
        $orders->addIndex(['client_id'], 'IDX_ORDERS_CLIENT_ID');
        if (null !== $userTable) {
            $orders->addForeignKeyConstraint($userTable, ['client_id'], ['id'], ['onDelete' => 'CASCADE']);
        }

        $orderItems = $schema->createTable('order_item');
        $orderItems->addColumn('id', 'integer', ['autoincrement' => true]);
        $orderItems->addColumn('order_id', 'integer');
        $orderItems->addColumn('product_id', 'integer');
        $orderItems->addColumn('quantity', 'integer');
        $orderItems->addColumn('selected_attributes', 'json');
        $orderItems->addColumn('unit_price', 'decimal', ['precision' => 12, 'scale' => 2]);
        $orderItems->addColumn('line_price', 'decimal', ['precision' => 12, 'scale' => 2]);
        $orderItems->setPrimaryKey(['id']);
        $orderItems->addIndex(['order_id'], 'IDX_ORDER_ITEM_ORDER_ID');
        $orderItems->addIndex(['product_id'], 'IDX_ORDER_ITEM_PRODUCT_ID');
        $orderItems->addForeignKeyConstraint('orders', ['order_id'], ['id'], ['onDelete' => 'CASCADE']);
        if (null !== $productTable) {
            $orderItems->addForeignKeyConstraint($productTable, ['product_id'], ['id'], ['onDelete' => 'CASCADE']);
        }

        $pricingRules = $schema->createTable('pricing_rule');
        $pricingRules->addColumn('id', 'integer', ['autoincrement' => true]);
        $pricingRules->addColumn('product_id', 'integer');
        $pricingRules->addColumn('name', 'string', ['length' => 255]);
        $pricingRules->addColumn('attribute_conditions', 'json');
        $pricingRules->addColumn('formula', 'text');
        $pricingRules->addColumn('priority', 'integer');
        $pricingRules->addColumn('active', 'boolean');
        $pricingRules->addColumn('created_at', 'datetime_immutable');
        $pricingRules->addColumn('updated_at', 'datetime_immutable');
        $pricingRules->setPrimaryKey(['id']);
        $pricingRules->addIndex(['product_id'], 'IDX_PRICING_RULE_PRODUCT_ID');
        if (null !== $productTable) {
            $pricingRules->addForeignKeyConstraint($productTable, ['product_id'], ['id'], ['onDelete' => 'CASCADE']);
        }

        $productAttribute = $schema->createTable('product_attribute');
        $productAttribute->addColumn('id', 'integer', ['autoincrement' => true]);
        $productAttribute->addColumn('product_id', 'integer');
        $productAttribute->addColumn('name', 'string', ['length' => 255]);
        $productAttribute->addColumn('type', 'string', ['length' => 50]);
        $productAttribute->addColumn('required', 'boolean');
        $productAttribute->setPrimaryKey(['id']);
        $productAttribute->addIndex(['product_id'], 'IDX_PRODUCT_ATTRIBUTE_PRODUCT_ID');
        if (null !== $productTable) {
            $productAttribute->addForeignKeyConstraint($productTable, ['product_id'], ['id'], ['onDelete' => 'CASCADE']);
        }

        $productAttributeValue = $schema->createTable('product_attribute_value');
        $productAttributeValue->addColumn('id', 'integer', ['autoincrement' => true]);
        $productAttributeValue->addColumn('attribute_id', 'integer');
        $productAttributeValue->addColumn('label', 'string', ['length' => 255]);
        $productAttributeValue->addColumn('value', 'string', ['length' => 255]);
        $productAttributeValue->setPrimaryKey(['id']);
        $productAttributeValue->addIndex(['attribute_id'], 'IDX_PRODUCT_ATTRIBUTE_VALUE_ATTR_ID');
        $productAttributeValue->addForeignKeyConstraint('product_attribute', ['attribute_id'], ['id'], ['onDelete' => 'CASCADE']);

        $fileAsset = $schema->createTable('file_asset');
        $fileAsset->addColumn('id', 'integer', ['autoincrement' => true]);
        $fileAsset->addColumn('order_id', 'integer', ['notnull' => false]);
        $fileAsset->addColumn('uploaded_by_id', 'integer', ['notnull' => false]);
        $fileAsset->addColumn('original_name', 'string', ['length' => 255]);
        $fileAsset->addColumn('path', 'string', ['length' => 255]);
        $fileAsset->addColumn('size', 'integer');
        $fileAsset->addColumn('check_status', 'string', ['length' => 20]);
        $fileAsset->addColumn('check_message', 'text', ['notnull' => false]);
        $fileAsset->addColumn('created_at', 'datetime_immutable');
        $fileAsset->setPrimaryKey(['id']);
        $fileAsset->addIndex(['order_id'], 'IDX_FILE_ASSET_ORDER_ID');
        $fileAsset->addIndex(['uploaded_by_id'], 'IDX_FILE_ASSET_UPLOADED_BY_ID');
        $fileAsset->addForeignKeyConstraint('orders', ['order_id'], ['id'], ['onDelete' => 'SET NULL']);
        if (null !== $userTable) {
            $fileAsset->addForeignKeyConstraint($userTable, ['uploaded_by_id'], ['id'], ['onDelete' => 'SET NULL']);
        }
    }

    private function resolveUserTable(Schema $schema): ?string
    {
        foreach (['user', 'app_user', 'users'] as $tableName) {
            if ($schema->hasTable($tableName)) {
                return $tableName;
            }
        }

        $this->write('No user table found (`user`, `app_user`, or `users`). Creating tables without user foreign keys.');

        return null;
    }


    private function resolveProductTable(Schema $schema): ?string
    {
        foreach (['product', 'products'] as $tableName) {
            if ($schema->hasTable($tableName)) {
                return $tableName;
            }
        }

        $this->write('No product table found (`product` or `products`). Creating tables without product foreign keys.');

        return null;
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('file_asset');
        $schema->dropTable('product_attribute_value');
        $schema->dropTable('product_attribute');
        $schema->dropTable('pricing_rule');
        $schema->dropTable('order_item');
        $schema->dropTable('orders');
    }
}
