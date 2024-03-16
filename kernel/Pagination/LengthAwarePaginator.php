<?php

namespace App\Kernel\Pagination;

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
    protected string $prevLink = "";
    // ссылка указывающая на следующий элемент
    protected string $nextLink = "";

    /**
     * @param array $data
     * данные
     * @param int $perPage
     * количество элементов на странице
     * @param int $page
     * текущая страница
     */
    public function __construct(array $data = [], int $perPage = 12, int $page = 1)
    {
        // количество элементов
        $dataCount = count($data);
        // общее колво страниц
        $totalPages = (int)ceil($dataCount / $perPage);

        // если $page больше 1
        // узнаем номер страницы на которой сейчас находится пользователь
        // иначе - 0
        $start = ($page > 1) ? ($page * $perPage) - $perPage : 0;

        // выбираем промежуток данных текущей страницы
        $this->items = array_slice($data, $start, $perPage);

        $this->setTotalPages($totalPages);
        $this->setCurrentPage($page);
        $this->setTotal($dataCount);

        // если предыдущего элемента несуществует - null
        $prevLink = $page - 1 <= 0 ? null : $page + 1;

        $appUrl = config('app.');

        dd($prevLink);
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

    public function setTotalPages(int $totalPages): void
    {
        $this->totalPages = $totalPages;
    }

    public function setTotal(int $total): void
    {
        $this->total = $total;
    }

    public function setCurrentPage(int $currentPage): void
    {
        $this->currentPage = $currentPage;
    }

    public function getPrevLink(): string
    {
        return $this->prevLink;
    }

    public function setPrevLink(string $prevLink): void
    {
        $this->prevLink = $prevLink;
    }

    public function getNextLink(): string
    {
        return $this->nextLink;
    }

    public function setNextLink(string $nextLink): void
    {
        $this->nextLink = $nextLink;
    }

}