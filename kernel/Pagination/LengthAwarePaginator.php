<?php

namespace App\Kernel\Pagination;

use App\Kernel\Collections\Collection;

class LengthAwarePaginator
{
    // данные
    protected array $items = [];
    // общее количество страниц
    protected int $totalPages;
    // общее количество элементов на странице
    protected int $total;
    // текущая страница
    protected int $currentPage;
    // ссылка указывающая на предыдущий элемент
    protected ?string $prevLink = "";
    // ссылка указывающая на следующий элемент
    protected ?string $nextLink = "";

    protected ?int $perPage = 12;

    protected array $meta = [];

    /**
     * @param array $data
     * данные
     * @param int $perPage
     * количество элементов на странице
     * @param int $page
     * текущая страница
     */
    public function __construct(array|Collection $data = [], int $perPage = 12, int $page = 1)
    {
        if ($data instanceof Collection) {
            $data = $data->toArray();
        }

        $itemsCount = count($data);

        $totalPages = (int)ceil($itemsCount / $perPage); // Округляем результат вверх

        $start = ($page > 1) ? ($page * $perPage) - $perPage : 0;

        // выбираем промежуток данных текущей страницы
        $this->setItems(array_slice($data, $start, $perPage));

        $this->setTotalPages($totalPages);
        $this->setCurrentPage($page);
        $this->setTotal($itemsCount);

        $debugModel = debug_backtrace()[0]['file'];

        $mPosition = strpos(strrev($debugModel), 'M', 0);

        $model = strrev(substr(strrev($debugModel), 0, $mPosition + 1));

        if ($model !== "Model.php") {
            $this->metaConfiguration();
        }
    }

    public function metaConfiguration(): void
    {
        $oldUrl = request()->fullUrl();

        $queryParams = [];

        $parseOldUrlQuery = parse_url($oldUrl)['query'] ?? null;
        if (!is_null($parseOldUrlQuery)) {
            parse_str($parseOldUrlQuery, $queryParams);
        }

        $newUrl = app_url() . request()->uri();

        $counter = 0;

        $nextUrl = $newUrl;
        $prevUrl = $newUrl;

        foreach ($queryParams as $index => $param) {

            if ($index === 'page') {
                $param = $param + 1;
            }

            if ($counter === 0) {
                $nextUrl .= "?$index=$param";
            } else {
                $nextUrl .= "&$index=$param";
            }
            $counter++;
        }

        foreach ($queryParams as $index => $param) {

            if ($index === 'page') {
                $param = $param - 1;
            }

            if ($counter === 0) {
                $prevUrl .= "?$index=$param";
            } else {
                $prevUrl .= "&$index=$param";
            }
            $counter++;
        }

        // если в query ничего не было передано - заполняем perPage и page сами
        if (!isset($parseOldUrlQuery)) {

            $prevPage = $this->getTotalPages() - $this->getCurrentPage() < 0
                ? null
                : $this->getCurrentPage() - 1;

            $nextPage = $this->getCurrentPage() + 1 > $this->getTotalPages()
                ? null
                : $this->getCurrentPage() + 1;

            $prevUrl .= "?per_page={$this->getPerPage()}&page=$prevPage";
            $nextUrl .= "?per_page={$this->getPerPage()}&page=$nextPage";
        } else {
            $prevUrl = $this->getCurrentPage() - 1 <= 0 ? null : $prevUrl;
            $nextUrl = $this->getCurrentPage() >= $this->getTotalPages() ? null : $nextUrl;
        }

        $this->setPrevLink($prevUrl);
        $this->setNextLink($nextUrl);

        $this->setMeta([
        'totalPages' => $this->getTotalPages(),
        'currentPage' => $this->getCurrentPage(),
        'total' => $this->getTotal(),
        'meta' => [
            'prev' => $this->getPrevLink(),
            'next' => $this->getNextLink()
        ],
    ]);
    }

    public function getInfo(string $varName = 'pagination', bool $withoutItems = false): array
    {
        return [
            'items' => $withoutItems ? [] : $this->getItems(),

            "$varName" => [
                'totalPages' => $this->getTotalPages(),
                'currentPage' => $this->getCurrentPage(),
                'total' => $this->getTotal(),
                'meta' => [
                    'prev' => $this->getPrevLink(),
                    'next' => $this->getNextLink()
                ],
            ]
        ];
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    public function setTotalPages(float $totalPages): void
    {
        $this->totalPages = round($totalPages);
    }


    public function setTotal(int $total): void
    {
        $this->total = $total;
    }

    public function setCurrentPage(int $currentPage): void
    {
        $this->currentPage = $currentPage;
    }

    public function getPrevLink(): ?string
    {
        return $this->prevLink;
    }

    public function setPrevLink(?string $prevLink): void
    {
        $this->prevLink = $prevLink;
    }

    public function getNextLink(): ?string
    {
        return $this->nextLink;
    }

    public function setNextLink(?string $nextLink): void
    {
        $this->nextLink = $nextLink;
    }

    public function getPerPage(): ?int
    {
        return $this->perPage;
    }

    public function setPerPage(?int $perPage): void
    {
        $this->perPage = $perPage;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }

    public function setMeta(array $meta): void
    {
        $this->meta = $meta;
    }

}