<?php


namespace Okay\Modules\OkayCMS\Banners\Helpers;


use Okay\Core\Config;
use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Request;
use Okay\Entities\BrandsEntity;
use Okay\Entities\CategoriesEntity;
use Okay\Entities\PagesEntity;
use Okay\Modules\OkayCMS\Banners\DTO\BannerImageSettingsDTO;
use Okay\Modules\OkayCMS\Banners\DTO\BannerSettingsDTO;
use Okay\Modules\OkayCMS\Banners\Entities\BannersEntity;
use Okay\Modules\OkayCMS\Banners\Entities\BannersImagesEntity;


class BannersHelper
{
    /**
     * @var BannersEntity
     */
    private $bannersEntity;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Design
     */
    private $design;

    private $config;

    /**
     * @var EntityFactory
     */
    private $entityFactory;

    public function __construct(
        EntityFactory $entityFactory,
        Request       $request,
        Design        $design,
        Config        $config
    ) {
        $this->bannersEntity = $entityFactory->get(BannersEntity::class);
        $this->request       = $request;
        $this->design        = $design;
        $this->config        = $config;
        $this->entityFactory = $entityFactory;
    }

    public function prepareAdd($banner)
    {
        return ExtenderFacade::execute(__METHOD__, $banner, func_get_args());
    }

    public function add($banner)
    {
        $insertId = $this->bannersEntity->add($banner);
        return ExtenderFacade::execute(__METHOD__, $insertId, func_get_args());
    }

    public function prepareUpdate($banner)
    {
        return ExtenderFacade::execute(__METHOD__, $banner, func_get_args());
    }

    public function update($id, $banner)
    {
        $this->bannersEntity->update($id, $banner);
        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function addSelectedEntities($banner)
    {
        $banner->category_selected = $this->request->post('categories');
        $banner->brand_selected = $this->request->post('brands');
        $banner->page_selected = $this->request->post('pages');
        return ExtenderFacade::execute(__METHOD__, $banner, func_get_args());
    }

    public function getBanner($id)
    {
        $banner = $this->bannersEntity->get($id);

        if (!$banner) {
            $banner = new \stdClass();
        }

        if (!empty($banner->settings)) {
            $banner->settings = unserialize($banner->settings);
        } else {
            $banner->settings = new BannerSettingsDTO();
        }

        return ExtenderFacade::execute(__METHOD__, $banner, func_get_args());
    }

    public function getSelectedEntities($banner)
    {
        if (!empty($banner->categories)) {
            $banner->category_selected = explode(",", $banner->categories);//Создаем массив категорий
        }
        if (!empty($banner->brands)) {
            $banner->brand_selected = explode(",",$banner->brands);//Создаем массив брендов
        }
        if (!empty($banner->pages)) {
            $banner->page_selected = explode(",",$banner->pages);//Создаем массив страниц
        }
        return ExtenderFacade::execute(__METHOD__, $banner, func_get_args());
    }

    public function getShowOnFilter()
    {
        $showOnFilter = [];
        if ($category = $this->design->getVar('category')) {
            $showOnFilter['categories'] = $category->id;
        }

        if ($brand = $this->design->getVar('brand')) {
            $showOnFilter['brands'] = $brand->id;
        }

        if ($page = $this->design->getVar('page')) {
            $showOnFilter['pages'] = $page->id;
        }

        $this->design->assign('page', $page);

        return ExtenderFacade::execute(__METHOD__, $showOnFilter, func_get_args());
    }

    public function getBanners($bannersFilter)
    {
        /** @var BannersEntity $bannersEntity */
        $bannersEntity = $this->entityFactory->get(BannersEntity::class);
        /** @var BannersImagesEntity $bannersImagesEntity */
        $bannersImagesEntity = $this->entityFactory->get(BannersImagesEntity::class);
        if ($banners = $bannersEntity->mappedBy('id')->find($bannersFilter)) {
            if ($bannersImages = $bannersImagesEntity->find(['banner_id' => array_keys($banners), 'visible' => true])) {
                foreach ($bannersImages as $bannersImage) {
                    if (isset($banners[$bannersImage->banner_id])) {

                        if (!empty($bannersImage->settings)) {
                            $bannersImage->settings = unserialize($bannersImage->settings);
                        } else {
                            $bannersImage->settings = new BannerImageSettingsDTO();
                        }

                        // Убираем урл у баннеров на странице, на которой они выведены
                        if ($this->config->get('banners_hide_self_url')) {
                        
                            // Делаем путь относительным, если он указан абсолютно на этот домен
                            $bannersImage->url = ltrim(str_replace(Request::getRootUrl(), '', $bannersImage->url), '/');
                            $requestUrl = explode('?', Request::getRequestUri())[0];
                            if ($bannersImage->url == $requestUrl) {
                                $bannersImage->url = '';
                            }
                        }

                        $banners[$bannersImage->banner_id]->items[] = $bannersImage;
                    }
                }
            }
        }
        return ExtenderFacade::execute(__METHOD__, $banners, func_get_args());
    }

    public function getBannersListForAdmin($filter)
    {
        $banners = $this->bannersEntity->mappedBy('id')->find($filter);

        if ($banners) {
            $categories = $this->entityFactory->get(CategoriesEntity::class)->find();
            $brands     = $this->entityFactory->get(BrandsEntity::class)->find();
            $pages      = $this->entityFactory->get(PagesEntity::class)->find();
            foreach ($banners as $banner){
                $banner->category_selected  = explode(",",$banner->categories);//Создаем массив категорий
                $banner->brand_selected     = explode(",",$banner->brands);//Создаем массив брендов
                $banner->page_selected      = explode(",",$banner->pages);//Создаем массив страниц
                foreach ($brands as $b){
                    if (in_array($b->id, $banner->brand_selected)){
                        $banner->brands_show[] = $b;
                    }
                }
                foreach ($categories as $c){
                    if (in_array($c->id, $banner->category_selected)){
                        $banner->category_show[] = $c;
                    }
                }
                foreach ($pages as $p){
                    if (in_array($p->id, $banner->page_selected)){
                        $banner->page_show[] = $p;
                    }
                }
            }
        }
        return ExtenderFacade::execute(__METHOD__, $banners, func_get_args());
    }

    public function buildFilter()
    {
        $filter = [];
        $filter['page'] = max(1, $this->request->get('page', 'integer'));
        $filter['limit'] = 20;
        
        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }

    public function countBannersImages($filter)
    {
        $bannerImagesCount = $this->bannersEntity->count($filter);
        return ExtenderFacade::execute(__METHOD__, $bannerImagesCount, func_get_args());
    }

    public function makePagination($bannersImagesCount, $filter)
    {
        if ($this->request->get('page') == 'all') {
            $filter['limit'] = $bannersImagesCount;
        }

        if ($filter['limit'] > 0) {
            $pagesCount = ceil($bannersImagesCount/$filter['limit']);
        } else {
            $pagesCount = 0;
        }

        $filter['page'] = min($filter['page'], $pagesCount);

        return [$filter, $pagesCount];
    }
    
}