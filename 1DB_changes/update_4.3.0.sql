UPDATE `ok_menu_items` SET `url` = 'all-products/filter-discounted' WHERE `url` LIKE '%discounted%';
DELETE FROM ok_lang_pages WHERE page_id IN (SELECT id FROM ok_pages WHERE url IN ('bestsellers', 'discounted'));
DELETE FROM ok_pages WHERE url IN ('bestsellers', 'discounted');

INSERT INTO ok_settings (param, value) VALUES('features_max_count_products', 10);

ALTER TABLE `ok_features`  ADD `show_in_product` INT(2) NOT NULL DEFAULT '1'  AFTER `visible`;