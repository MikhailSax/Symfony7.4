<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;

class BreadCrumbService
{
    private $requestStack;
    public function __construct(
        private  Request $request,
  )
  {
      $this->requestStack = $request;
  }

  public function getCurrentUri(): string
  {
      $request = $this->requestStack->getUri();
      if(!$request)
      {
          return '';
      }
      return $request;
  }

  public function getRefererUri(): string
  {
      return $this->request->headers->get('referer');
  }
}
