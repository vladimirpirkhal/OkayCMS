{foreach $hotlineFeeds as $feed}
    <option value="{Okay\Modules\OkayCMS\Hotline\Init\Init::TO_FEED_FIELD}@{$feed->id}" data-label="{$btr->getTranslation('okaycms__hotline__import_field')} {$feed@iteration}.{$feed->name|escape}">
        {$btr->getTranslation('okaycms__hotline__import_field')} {$feed@iteration}.{$feed->name|escape}
    </option>
{/foreach}