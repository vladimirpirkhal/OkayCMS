<?php


namespace Okay\Modules\OkayCMS\Banners\Backend\Controllers;


use Okay\Admin\Controllers\IndexAdmin;
use Okay\Modules\OkayCMS\Banners\Entities\BannersEntity;
use Okay\Modules\OkayCMS\Banners\Helpers\BannersImagesHelper;
use Okay\Modules\OkayCMS\Banners\Requests\BannersImagesRequest;

class BannersImageAdmin extends IndexAdmin
{
    
    public function fetch(
        BannersEntity $bannersEntity,
        BannersImagesRequest $bannersImagesRequest,
        BannersImagesHelper $bannersImagesHelper
    ) {
        /*Принимаем данные о слайде*/
        if ($this->request->method('post')) {
            $bannersImage = $bannersImagesRequest->postBannerImage();
            
            /*Добавляем/удаляем слайд*/
            if (empty($bannersImage->id)) {
                $preparedBannersImage = $bannersImagesHelper->prepareAdd($bannersImage);
                $bannersImage->id     = $bannersImagesHelper->add($preparedBannersImage);
                $this->postRedirectGet->storeMessageSuccess('added');
                $this->postRedirectGet->storeNewEntityId($bannersImage->id);
                $isNewBannersImage = true;
            } else {
                
                // если сняли галочку "Мультиязычный баннер", проставим изображение баннера с основного языка для всех
                if (!$bannersImage->is_lang_banner) {
                    $currentLangId = $this->languages->getLangId();
                    $mainLang = $this->languages->getMainLanguage();
                    $this->languages->setLangId($mainLang->id);
                    $currentBannersImage = $bannersImagesHelper->getBannerImage((int)$bannersImage->id);
                    if ($currentBannersImage->is_lang_banner != $bannersImage->is_lang_banner) {
                        
                        foreach ($this->languages->getAllLanguages() as $lang) {
                            $this->languages->setLangId($lang->id);
                            $bannersImagesHelper->update($bannersImage->id, [
                                'image' => $currentBannersImage->image,
                                'image_mobile' => $currentBannersImage->image_mobile,
                            ]);
                        }
                        
                    }
                    $this->languages->setLangId($currentLangId);
                }
                
                $preparedBannersImage = $bannersImagesHelper->prepareUpdate($bannersImage);
                $bannersImagesHelper->update($preparedBannersImage->id, $preparedBannersImage);
                $this->postRedirectGet->storeMessageSuccess('updated');
                $isNewBannersImage = false;
            }

            // Картинка
            if ($deleteTypes = $bannersImagesRequest->postDeleteImage()) {
                if (in_array('desktop', $deleteTypes)) {
                    $bannersImagesHelper->deleteImage($bannersImage);
                }
                if (in_array('mobile', $deleteTypes)) {
                    $bannersImagesHelper->deleteImage($bannersImage, 'image_mobile');
                }
            }

            if ($image = $bannersImagesRequest->fileImage()) {
                $bannersImagesHelper->uploadImage($image, $bannersImage, $isNewBannersImage);
            }

            if ($imageMobile = $bannersImagesRequest->mobileFileImage()) {
                $bannersImagesHelper->uploadImage($imageMobile, $bannersImage, $isNewBannersImage, 'image_mobile');
            }

            $this->postRedirectGet->redirect();

        } else {
            $bannersImageId = $this->request->get('id', 'integer');

            // Если пришли с меню быстрого редактирования
            if ($bannerSlideId = $this->request->get('banner_slide_id')) {
                list($bannerId, $bannersImageId) = explode(':', $bannerSlideId);
            } elseif ($bannerSlideId = $this->request->get('banner_slide_id_add')) {
                list($bannerId) = explode(':', $bannerSlideId);
                $this->design->assign('banner_id', $bannerId);
            }
            
            $bannersImage = $bannersImagesHelper->getBannerImage($bannersImageId);
        }
        
        $banners = $bannersEntity->find();//todo

        $this->design->assign('banners_image', $bannersImage);
        $this->design->assign('banners', $banners);

        $this->response->setContent($this->design->fetch('banners_image.tpl'));
    }
    
}
