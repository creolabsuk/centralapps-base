<?php
namespace CentralApps\Base\ServiceProviders\Routing;

class PaginationInjectionFilter implements RouteFilter, UrlFilter
{
    protected $paginationKey = 'pagination';
    protected $pagination = null;
    protected $pageKey = 'page';
    protected $request = null;

    public function setPagination($pagination)
    {
        $this->pagination = $pagination;
    }

    public function setRequest(\Symfony\Component\HttpFoundation\Request $request)
    {
        $this->request = $request;
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
            } elseif (!is_null($this->request)) {
                $page = $this->request->query->get($this->pageKey, 1);
                $this->pagination->setCurrentPageNumber($page);
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
        if (array_key_exists($this->paginationKey, $route)) {
            if (is_object($this->pagination) && array_key_exists($this->paginationKey, $route) && true == $route[$this->paginationKey]) {
                $route[$this->paginationKey] = $this->pagination;
            }

            $pagination = $route[$this->paginationKey];
            unset($route[$this->paginationKey]);
            $route[$this->paginationKey] = $pagination;
        }


        return $route;
    }
}
