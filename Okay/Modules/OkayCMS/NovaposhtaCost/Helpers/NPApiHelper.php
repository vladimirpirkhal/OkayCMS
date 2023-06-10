<?php


namespace Okay\Modules\OkayCMS\NovaposhtaCost\Helpers;


use Okay\Core\Settings;
use Okay\Modules\OkayCMS\NovaposhtaCost\DTO\NPCitiesCollectionDTO;
use Okay\Modules\OkayCMS\NovaposhtaCost\DTO\NPCityDTO;
use Okay\Modules\OkayCMS\NovaposhtaCost\DTO\NPWarehouseDTO;
use Okay\Modules\OkayCMS\NovaposhtaCost\DTO\NPWarehousesCollectionDTO;
use Okay\Modules\OkayCMS\NovaposhtaCost\DTO\NPWarehouseTypeDTO;

class NPApiHelper
{
    private string $apiKey;
    private string $lastCallError = '';
    private Settings $settings;

    public function __construct(
        Settings $settings
    ) {
        $this->apiKey = $settings->get('newpost_key');
        $this->settings = $settings;
    }

    /**
     * Метод достает типы отделений из API Новой Почты
     * @return NPWarehouseTypeDTO[]
     */
    public function getWarehouseTypes(): array
    {
        $request = [
            "modelName" => "Address",
            "calledMethod" => "getWarehouseTypes",
        ];

        $response = $this->request($request);
        if (!empty($response->success)) {
            $result = [];
            foreach ($response->data as $warehouseTypeData) {
                $name = $nameRu = htmlspecialchars($warehouseTypeData->Description);
                if (!empty($warehouseTypeData->DescriptionRu)) {
                    $nameRu = htmlspecialchars($warehouseTypeData->DescriptionRu);
                }
                $result[] = new NPWarehouseTypeDTO(
                    $name,
                    $nameRu,
                    $warehouseTypeData->Ref
                );
            }
            return $result;
        }
        return [];
    }

    public function getWarehouses(string $warehouseType, int $page, int $limit): ?NPWarehousesCollectionDTO
    {
        $request = [
            "modelName" => "Address",
            "calledMethod" => "getWarehouses",
            "methodProperties" => [
                "TypeOfWarehouseRef" => $warehouseType,
                "Page" => $page,
                "Limit" => $limit,
            ]
        ];

        $response = $this->request($request);
        if (!empty($response->success)) {
            $warehousesDTO = new NPWarehousesCollectionDTO();
            foreach ($response->data as $warehouseData) {
                // Перевіряємо тип, оскільки НП може повернути відділення не того типу і вони задублюються на сайті
                if ($warehouseData->TypeOfWarehouse != $warehouseType) {
                    continue;
                }
                $warehouseDTO = new NPWarehouseDTO(
                    htmlspecialchars($warehouseData->Description),
                    $warehouseData->Ref,
                    $warehouseData->CityRef,
                    $warehouseData->TypeOfWarehouse,
                    (int)$warehouseData->Number
                );
                if (!empty($warehouseData->DescriptionRu)) {
                    $warehouseDTO->setNameRu(htmlspecialchars($warehouseData->DescriptionRu));
                }
                $warehousesDTO->setWarehouse($warehouseDTO);
            }
            if (!empty($response->info->totalCount)) {
                $warehousesDTO->setTotalCount($response->info->totalCount);
            }
            return $warehousesDTO;
        } else {
            return null;
        }
    }

    public function getCities(int $page, int $limit): ?NPCitiesCollectionDTO
    {
        $request = [
            "modelName" => "Address",
            "calledMethod" => "getCities",
            "methodProperties" => [
                "Page" => $page,
                "Limit" => $limit,
            ],
        ];

        $response = $this->request($request);
        if (!empty($response->success)) {
            $citiesDTO = new NPCitiesCollectionDTO();
            foreach ($response->data as $cityData) {
                $cityDTO = new NPCityDTO(
                    htmlspecialchars($cityData->Description),
                    $cityData->Ref
                );
                if (!empty($cityData->DescriptionRu)) {
                    $cityDTO->setNameRu(htmlspecialchars($cityData->DescriptionRu));
                }
                $citiesDTO->setCity($cityDTO);
            }
            if (!empty($response->info->totalCount)) {
                $citiesDTO->setTotalCount($response->info->totalCount);
            }
            return $citiesDTO;
        } else {
            return null;
        }
    }

    public function getLastCallError(): string
    {
        return $this->lastCallError;
    }

    private function request(array $requestParams)
    {
        if (empty($requestParams)) {
            return false;
        }
        $requestParams["apiKey"] = $this->apiKey;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.novaposhta.ua/v2.0/json/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestParams));
        curl_setopt($ch, CURLOPT_POST, 1);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            $this->lastCallError = 'Error in API call';
            return false;
        }

        $response = json_decode($response);

        if (!empty($response->errors)) {
            $this->lastCallError = implode('<br>', $response->errors);
            // Запам'ятовуємо помилку по API key
            if (strpos($this->lastCallError, 'API key') !== false) {
                $this->settings->set('np_api_key_is_incorrect', true);
            }
            return false;
        }
        if (!empty($response->success)) {
            if (empty($response->data)) {
                $this->lastCallError = 'Response data is empty';
                return false;
            }
            return $response;
        }

        return false;
    }
}