<?php
namespace CentralApps\Base\ServiceProviders\Routing;

class PaginationInjectionFilter implements RouteFilter, UrlFilter
{
    protected $paginationKey = 'pagination';
    protected $pagination = null;
    protected $pageKey = 'page';

    public function setPagination($pagination)
    {
        $this->pagination = $pagination;
    }

    public function setPaginationKey($pagination_key)
    {
        $this->paginationKey = $pagination_key;
    }

    public function filterUrl($url)
    {
        if (!is_null($this->pagination)) {
            $page = [];

            preg_match('/(\?|\&)?' . $this->pageKey . '+=[^\&]+/', $url, $page);
            if (count($page) > 0) {
                $page = preg_replace('/(\?|\&)?' . $this->pageKey . '=/', '', $page[0]);

                $this->pagination->setCurrentPageNumber($page);
                $url = preg_replace('/(\?|\&)?' . $this->pageKey . '=' . $page . '/', '', $url);
            }

            $url = preg_replace('/(\?|\&)?' . $this->pageKey . '+=[^\&]/', '', $url);
        }

        if (strpos($url, '?') === false) {
            $url = preg_replace('/&/', '?', $url, 1);
        }

        return $url;
    }

    public function filterRoute(array $route)
    {
        if (is_object($this->pagination) && array_key_exists($this->paginationKey, $route) && true == $route[$this->paginationKey]) {
            $route[$this->paginationKey] = $this->pagination;
         }

         return $route;
    }
}
