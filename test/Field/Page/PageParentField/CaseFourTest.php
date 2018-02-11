<?php

/**
 * `parent` field supports optional selector.
 */

namespace ProcessWire\GraphQL\Test\Field\Page\Fieldtype;

use \ProcessWire\GraphQL\Utils;
use \ProcessWire\GraphQL\Test\GraphQLTestCase;
use \ProcessWire\GraphQL\Test\Field\Page\Traits\AccessTrait;

class PageParentFieldCaseFourTest extends GraphQLTestCase {

  const accessRules = [
    'legalTemplates' => ['skyscraper'],
    'legalPageFields' => ['parent', 'name'],
  ];

  use AccessTrait;
  
  public function testValue()
  {
    $skyscraper = Utils::pages()->get("template=skyscraper");
    $query = "{
      skyscraper (s: \"id=$skyscraper->id\") {
        list {
          parent (s: \"template=cities\") {
            name
          }
        }
      }
    }";
    $res = $this->execute($query);
    $this->assertTrue(is_null($res->data->skyscraper->list[0]->parent), 'parent returns null if no access.');
  }

}