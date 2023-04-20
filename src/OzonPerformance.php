<?php

namespace Filippi4\Ozon;

use Carbon\Carbon;

class OzonPerformance extends OzonPerformanceClient
{
    private const DT_FORMAT_DATE_TIME_TZ = 'Y-m-d\TH:i:s.v\Z';
    private const DT_FORMAT_DATE = 'Y-m-d';

    public function config(array $keys): OzonPerformance
    {
        $this->validateKeys($keys);

        $this->config = $keys;

        return $this;
    }

    private function formatDate(?Carbon $dateTime, string $format = self::DT_FORMAT_DATE_TIME_TZ): ?string
    {
        return $dateTime ? $dateTime->format($format) : null;
    }

    /**
     * Список кампаний
     *
     * @return mixed
     */
    public function getCampaign(
        array  $campaign_ids = null,
        string $adv_object_type = null,
        string $state = null
    ): mixed
    {
        return (new OzonData($this->getResponse(
            'api/client/campaign',
            array_merge(
                compact('campaign_ids'),
                array_diff(compact('adv_object_type', 'state'), [''])
            )
        )))->data;
    }

    /**
     * Список рекламируемых объектов в кампании
     *
     * @param int $campaign_id Идентификатор кампании
     * @return mixed
     */
    public function getCampaignObjects(int $campaign_id): mixed
    {
        return (new OzonData($this->getResponse(
            'api/client/campaign/' . $campaign_id . '/objects'
        )))->data;
    }

    /**
     * Статистика по расходу кампаний
     *
     * @param int $campaign_id Идентификатор кампании
     * @return mixed
     */
    public function getStatisticsExpense(
        int    $campaigns = null,
        Carbon $dateFrom = null,
        Carbon $dateTo = null
    ): mixed
    {
        $dateFrom = $this->formatDate($dateFrom, self::DT_FORMAT_DATE);
        $dateTo = $this->formatDate($dateTo, self::DT_FORMAT_DATE);

        return (new OzonData($this->getResponse(
            'api/client/statistics/expense',
            array_merge(
                array_diff(compact('campaigns', 'dateFrom', 'dateTo'), [''])
            ),
            false
        )))->data;
    }

    /**
     * Дневная статистика по кампаниям
     *
     * @param array|null $campaignId Список идентификаторов кампаний
     * @param Carbon|null $dateFrom Начальная дата периода отчёта
     * @param Carbon|null $dateTo Конечная дата периода отчёта
     * @return mixed
     */
    public function getStatisticsDaily(
        array  $campaignId = null,
        Carbon $dateFrom = null,
        Carbon $dateTo = null,
    ): mixed
    {
        $dateFrom = $this->formatDate($dateFrom, self::DT_FORMAT_DATE);
        $dateTo = $this->formatDate($dateTo, self::DT_FORMAT_DATE);

        $params = $this->getNotNullParams(compact('campaignId', 'dateFrom', 'dateTo'));

        return (new OzonData($this->getResponse('api/client/statistics/daily', $params, false)))->data;
    }

    private function getNotNullParams(array $params): array
    {
        $notNullParams = [];
        foreach ($params as $key => $value) {
            if (!empty($value)) {
                $notNullParams[$key] = $value;
            }
        }
        return $notNullParams;
    }
}
