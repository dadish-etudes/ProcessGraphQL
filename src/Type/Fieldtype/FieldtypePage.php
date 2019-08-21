<?php namespace ProcessWire\GraphQL\Type\Fieldtype;

use GraphQL\Type\Definition\Type;
use ProcessWire\FieldtypePage as PWFieldtypePage;
use ProcessWire\GraphQL\Type\PageArrayType;
use ProcessWire\GraphQL\Type\Traits\CacheTrait;

class FieldtypePage
{
  use CacheTrait;
  public static function type($field)
  {
    $template = null;
    // if template is chosen for the FieldtypePage
    // then we resolve to TemplatedPageArrayType
    if ($field->template_id) {
      $template = \ProcessWire\wire('templates')->get($field->template_id);
    }
      
    return PageArrayType::type($template);
  }

  public static function field($field)
  {
    return self::cache("field-{$field->name}", function () use ($field) {
      // description
      $desc = $field->description;
      if (!$desc) {
        $desc = "Field with the type of {$field->type}";
      }

      return [
        'name' => $field->name,
        'description' => $desc,
        'type' => $field->required ? Type::nonNull(self::type($field)) : self::type($field),
        'resolve' => function ($value) use ($field) {
          $fieldName = $field->name;
          $field = \ProcessWire\wire('fields')->get($fieldName);
          $field->derefAsPage = PWFieldtypePage::derefAsPageArray;
          return $value->$fieldName;
        }
      ];
    });
  }

  public static function inputField()
  {
    return Type::listOf(Type::string());
  }

  public function setValue(Page $page, $field, $value)
  {
    $fieldName = $field->name;
    $page->$fieldName = implode('|', $value);
  }
}