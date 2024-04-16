INSERT INTO `ok_settings` (`param`, `value`) VALUES ('email_for_module', '');
ALTER TABLE `ok_categories`  ADD `on_main` INT(2) NOT NULL DEFAULT '0'  AFTER `visible`;
INSERT INTO `ok_settings` (`param`, `value`) VALUES
 ('open_ai_temperature', '1'),
 ('open_ai_presence_penalty', '0'),
 ('open_ai_frequency_penalty', '0'),
 ('open_ai_max_tokens', '4096');

INSERT INTO `ok_settings_lang` (`lang_id`, `param`, `value`) VALUES
 (3, 'ai_system_message', 'Ти контент менеджер. Пиши тільки метадані, які привертатимуть увагу користувачів і включатимуть ключові слова для оптимізації пошукового двигуна. Використовуй emoji. Розписуй по максимуму все в деталях'),
 (2, 'ai_system_message', 'You are a content manager. Only write metadata that will attract users'' attention and include keywords for search engine optimization. Use emoji. Paint everything in detail as much as possible'),
 (1, 'ai_system_message', 'Ты контент менеджер. Пиши только метаданные, которые будут привлекать пользователей и включать ключевые слова для оптимизации поискового двигателя. Используй emoji. Расписывай по максимуму все в деталях'),
 (3, 'ai_product_title_template', 'Створи мета-тайтл для товару {$product}'),
 (2, 'ai_product_title_template', 'Create a meta title for product {$product}'),
 (1, 'ai_product_title_template', 'Создай мета-тайтл для товара {$product}'),
 (3, 'ai_product_meta_description_template', 'Створи мета-description для товару {$product}'),
 (2, 'ai_product_meta_description_template', 'Create a meta-description for the product {$product}'),
 (1, 'ai_product_meta_description_template', 'Создай мета-description для товара {$product}'),
 (3, 'ai_product_keywords_template', 'Напиши ключові слова для товару {$product}'),
 (2, 'ai_product_keywords_template', 'Write keywords for the product {$product}'),
 (1, 'ai_product_keywords_template', 'Напиши ключевые слова для товара {$product}'),
 (3, 'ai_product_description_template', 'Створи повний опис для товару {$product}'),
 (2, 'ai_product_description_template', 'Create a full description for the product {$product}'),
 (1, 'ai_product_description_template', 'Создай полное описание для товара {$product}'),
 (3, 'ai_product_annotation_template', 'Створи короткий опис для товару {$product}'),
 (2, 'ai_product_annotation_template', 'Create a short description for {$product}'),
 (1, 'ai_product_annotation_template', 'Создай краткое описание для товара {$product}'),
 (3, 'ai_category_title_template', 'Створи мета-тайтл для категорії {$category}'),
 (2, 'ai_category_title_template', 'Create a meta title for category {$category}'),
 (1, 'ai_category_title_template', 'Создай мета-тайтл для категории {$category}'),
 (3, 'ai_category_meta_description_template', 'Створи мета-description для категорії {$category}'),
 (2, 'ai_category_meta_description_template', 'Create a meta-description for the category {$category}'),
 (1, 'ai_category_meta_description_template', 'Создай мета-description для категории {$category}'),
 (3, 'ai_category_keywords_template', 'Напиши ключові слова для категорії {$category}'),
 (2, 'ai_category_keywords_template', 'Write keywords for the category {$category}'),
 (1, 'ai_category_keywords_template', 'Напиши ключевые слова для категории {$category}'),
 (3, 'ai_category_description_template', 'Створи повний опис для категорії {$category}'),
 (2, 'ai_category_description_template', 'Create a full description for the category {$category}'),
 (1, 'ai_category_description_template', 'Создай полное описание для категории {$category}'),
 (3, 'ai_category_annotation_template', 'Створи короткий опис для категорії {$category}'),
 (2, 'ai_category_annotation_template', 'Create a short description for the category {$category}'),
 (1, 'ai_category_annotation_template', 'Создай краткое описание для категории {$category}'),
 (3, 'ai_brand_title_template', 'Створи мета-тайтл для бренда {$brand}'),
 (2, 'ai_brand_title_template', 'Create a meta title for brand {$brand}'),
 (1, 'ai_brand_title_template', 'Создай мета-тайтл для бренда {$brand}'),
 (3, 'ai_brand_meta_description_template', 'Створи мета-description для бренда {$brand}'),
 (2, 'ai_brand_meta_description_template', 'Create a meta-description for the brand {$brand}'),
 (1, 'ai_brand_meta_description_template', 'Создай мета-description для бренда {$brand}'),
 (3, 'ai_brand_keywords_template', 'Напиши ключові слова для бренда {$brand}'),
 (2, 'ai_brand_keywords_template', 'Write keywords for the brand {$product}'),
 (1, 'ai_brand_keywords_template', 'Напиши ключевые слова для бренда {$brand}'),
 (3, 'ai_brand_description_template', 'Створи повний опис для бренда {$brand}'),
 (2, 'ai_brand_description_template', 'Create a full description for the brand {$brand}'),
 (1, 'ai_brand_description_template', 'Создай полное описание для бренда {$brand}'),
 (3, 'ai_brand_annotation_template', 'Створи короткий опис для бренда {$brand}'),
 (2, 'ai_brand_annotation_template', 'Create a short description for the brand {$brand}'),
 (1, 'ai_brand_annotation_template', 'Создай краткое описание для бренда {$brand}');




