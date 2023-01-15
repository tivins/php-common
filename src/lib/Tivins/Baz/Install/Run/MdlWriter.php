<?php

namespace Tivins\Baz\Install\Run;

use Tivins\Core\StrUtil;
use Tivins\Baz\install\schema\Enum;
use Tivins\Baz\install\schema\Field;
use Tivins\Baz\install\schema\FieldType;
use Tivins\Baz\install\schema\Schema;

class MdlWriter {

    private static Schema $schema;
    public static function writeComment(int $depth, string ...$comments): string
    {
        if (empty($comments)) {
            return '';
        }
        $tab  = str_repeat('    ', $depth);
        $body = "$tab/**\n";
        foreach ($comments as $comment) {
            $body .= "$tab * $comment\n";
        }
        $body .= "$tab */\n";
        return $body;
    }

    private static function writeEnum(Enum $enum): string
    {
        $body   = self::writeComment(0, $enum->comment);
        $body   .= 'enum ' . $enum->name . ': int' . "\n" . '{' . "\n";
        $body   .= '    use EnumExtra;' . "\n";
        $body   .= "\n";
        $maxLen = 0;
        foreach ($enum->cases as $case => $val) {
            if (strlen($case) > $maxLen) {
                $maxLen = strlen($case);
            }
        }
        foreach ($enum->cases as $case => $val) {
            $body .= '    case ' . sprintf('% ' . $maxLen . 's', $case) . ' = ' . $val . ';' . "\n";
        }
        if ($enum->access) {
            $enumPath = explode('\\', var_export($enum->access->method, true));
            $body     .= "\n";
            $body     .= '    ' . sprintf(
                    "#[APIAccess('%s', %s, '%s')]\n",
                    $enum->access->service,
                    end($enumPath),
                    $enum->access->permission,
                );
            $body     .= '    #[APIRequestHeaderBearer]' . "\n";
            $body     .= '    /** @return array<int,string> Indexed cases of enum */' . "\n";
            $body     .= '    public static function getList(): array {' . "\n";
            $body     .= '        return self::getAssociative();' . "\n";
            $body     .= '    }' . "\n";
        }
        $body .= '}' . "\n";
        return $body;
    }

    private static function getDefaultPHP(Field $struct): string
    {
        if (!$struct->hasDefault()) {
            return '';
        }

        [$type] = self::schema_type_php($struct->getType());
        $isString = $type == 'string';
        $default = $struct->getDefault();
        if ($type == 'int' &&  $struct->getType() != FieldType::ENUM) {
            $default = 0;
        }
        $value = ' = ' . ($isString ? '"' : '') . $default . ($isString ? '"' : '');
        if ($struct->getType() == FieldType::ENUM) {
            $value = ' = ' . $struct->getEnum()->cases[$struct->getDefault()];
        }
        return $value;
    }

    public static function writeModels(string $filename): bool
    {
        $body    = '<?' . 'php' . "\n\n";
        $body    .= "// ATTENTION !\n";
        $body    .= "// This file was generated ! Do not change !\n";
        $body    .= '// @generated on ' . gmdate('Y-m-d H:i:s T') . "\n";
        $body    .= '/* @noinspection PhpUnused */' . "\n";
        $body    .= "\n";
        $body    .= self::writeComment(0, self::$schema->getComment());
        $body    .= 'namespace ' . self::$schema->getNamespace() . ';' . "\n\n";
        $body    .= 'use Tivins\Baz\{ DB, DBCondition, Mdl };' . "\n";
        $body    .= 'use Tivins\Baz\API\APIAccess;' . "\n";
        $body    .= 'use Tivins\Baz\API\APIRequestHeaderBearer;' . "\n";
        $body    .= 'use Tivins\Baz\Core\Net\http\Method;' . "\n";
        $body    .= 'use Tivins\Baz\Core\code\EnumExtra;' . "\n";
        $body    .= "\n";
        $exposed = [];
        foreach (self::$schema->getEnums() as $enum) {
            $body .= self::writeEnum($enum);
        }
        foreach (self::$schema->getTables() as $table) {
            if (!$table->getClass()) {
                continue;
            }
            $body .= self::writeComment(0, $table->get_comment());
            $body .= 'class ' . $table->getClass() . ' extends Mdl ' . "\n" . '{' . "\n";
            $body .= '    public const __type = \'' . $table->getName() . '\';' . "\n";
            $body .= '    public const __pk = \'' . $table->pk . '\';' . "\n";
            $body .= "\n";
            //
            // == PROPERTIES ==
            //
            foreach ($table->getFields() as $field) {
                [$type] = self::schema_type_php($field->getType());
                $value = self::getDefaultPHP($field);
                $body .= '    private ' . $type . ' $' . $field->getName() . $value . ';' . "\n";
                if ( !empty($field->getExchange())) {
                    $exposed[
                    $table->getExchange() ?: $table->getClass()
                    ][
                    $field->getExchange() === true ? $field->getName() : $field->getExchange()
                    ]
                        = $field;
                }
            }
            $body .= "\n";
            $body .= '    protected function _getState(): array' . "\n";
            $body .= '    {' . "\n";
            $body .= '        return [' . "\n";
            foreach ($table->getFields() as $field) {
                [, $castFunc] = self::schema_type_php($field->getType());
                $body .= '         \'' . $field->getName() . '\' => ' . $castFunc . '($this->' . $field->getName() . ' ?? \'\'),' . "\n";
            }
            $body .= '        ];' . "\n";
            $body .= '    }' . "\n";
            $body .= '    /**' . "\n";
            $body .= '     * @throws ' . "\n";
            $body .= '     */' . "\n";
            $body .= '    protected function _setState(object $values): void' . "\n";
            $body .= '    {' . "\n";
            foreach ($table->getFields() as $field) {
                [, $castFunc] = self::schema_type_php($field->getType());
                $body .= '         $this->' . $field->getName() . ' = ' . $castFunc . '($values->' . $field->getName() . ' ?? \'\');' . "\n";
            }
            $body        .= '    }' . "\n\n";
            $validations = '';
            foreach ($table->getFields() as $field_name => $field) {
                if ($field->valid_email) {
                    $validations .= '        if (! filter_var($this->' . $field_name . ' ?? \'\', FILTER_VALIDATE_EMAIL)) {' . "\n";
                    $validations .= '            $errors[\'' . $field_name . '\'][] = \'is not a valid email.\';' . "\n";
                    $validations .= '        }' . "\n";
                }
                if ($field->isNotEmpty()) {
                    $validations .= '        if (empty($this->' . $field_name . ' ?? \'\')) {' . "\n";
                    $validations .= '            $errors[\'' . $field_name . '\'][] = \'is not defined.\';' . "\n";
                    $validations .= '        }' . "\n";
                }
                /** @todo */
                // if (isset($struct['greater_than'])) {
                //     $validations .= '        if (($this->' . $field_name . ' ?? 0) <= ' . $struct['greater_than'] . ') {' . "\n";
                //     $validations .= '            $errors[\'' . $field_name . '\'][] = \'is not greater than ' . $struct['greater_than'] . '.\';' . "\n";
                //     $validations .= '        }' . "\n";
                // }
            }
            $body .= '    public function validate(): array' . "\n";
            $body .= '    {' . "\n";
            if (empty($validations)) {
                $body .= '        return [];' . "\n";
            } else {
                $body .= '        $errors = [];' . "\n";
                $body .= $validations;
                $body .= '        return $errors;' . "\n";
            }
            $body .= '    }' . "\n\n";

            $fieldMap = $table->getMapId() ?: $table->getPKName();

            $body .= '    /**' . "\n";
            $body .= '     * @param DBCondition[] $conditions' . "\n";
            $body .= '     * @return ' . $table->getClass() . '[]' . ' An array of ' .  $table->getClass() . ' objects, indexed with the field "' . ($fieldMap) . '"' . "\n";
            $body .= '     */' . "\n";
            $body .= '    public static function getListIndexed(array $conditions, string $order = \'\'): array' . "\n";
            $body .= '    {' . "\n";
            $body .= '        [$sql,$args] = DBCondition::render($conditions);' . "\n";
            $body .= '        $sql  = \'select * from ' .  $table->getName() . '\' . $sql . ($order ? \' order by \' . $order : \'\');' . "\n";
            $body .= '        $data = DB::fetchAll($sql, $args, static::class);' . "\n";
            $body .= '        array_map(fn($o) => static::storeInstance($o), $data);' . "\n";
            $body .= '        return array_combine(array_map(fn($i) => $i->get_' . ($fieldMap) . '(), $data), $data);' . "\n";
            $body .= '    }' . "\n";
            $body .= self::writeComment(1, '@param DBCondition[] $conditions');
            $body .= '    public static function getCount(array $conditions): int' . "\n";
            $body .= '    {' . "\n";
            $body .= '        [$sql,$args] = DBCondition::render($conditions);' . "\n";
            $body .= '        $sql  = \'select count(*) from ' . $table->getName() . '\' . $sql;' . "\n";
            $body .= '        return DB::fetchField($sql, $args);' . "\n";
            $body .= '    }' . "\n";
            $body .= self::writeComment(
                1,
                '@return ' .  $table->getClass() . '[]' . ' An array of ' . $table->getClass() . ' objects, indexed with the field "' . ($fieldMap) . '"'
            );
            $body .= '    public static function getList(string $query, array $args): array' . "\n";
            $body .= '    {' . "\n";
            $body .= '        $data = DB::fetchAll(str_replace(\'TableName\', \'' . $table->getName() . '\', $query), $args, static::class);' . "\n";
            $body .= '        return array_combine(array_map(fn($i) => $i->get_' . ($fieldMap) . '(), $data), $data);' . "\n";
            $body .= '    }' . "\n";
            // -- TRANSLATIONS
            $translationFields = [];
            foreach ($table->getFields() as $_fieldName => $_field) {
                if ($_field->getTranslationMap()) {
                    $translationFields[$_fieldName] = $_field->getTranslationMap();
                }
            }
            if ( !empty($translationFields)) {
                $body .= '    public static function mapTranslation(array $list, string $lang): void' . "\n";
                $body .= '    {' . "\n";
                $body .= '        $translations = Translation::getListIndexed([' . "\n";
                $body .= '            new DBCondition(\'key\', \'awards\'),' . "\n";
                $body .= '            new DBCondition(\'lang\', $lang),' . "\n";
                $body .= '        ]);' . "\n";
                $body .= '        foreach ($translations as $id => $translation) {' . "\n";
                foreach ($translationFields as $_fieldName => $_fieldMap) {
                    $body .= '            $list[$id]->set_' . $_fieldName . '($translation->get_' . $_fieldMap . '());' . "\n";
                }
                $body .= '        }' . "\n";
                $body .= '    }' . "\n";
            }
            $body .= "\n";
            foreach ($table->getFields() as $field_name => $field) {
                if (!$field->isSelectable()) {
                    continue;
                }
                $body .= '    public static function loadBy' . ucfirst($field_name) . '(string $value): ?static' . "\n";
                $body .= '    {' . "\n";
                $body .= '        $obj = new static();' . "\n";
                $body .= '        if ($obj->loadBy(\'' . $field_name . '\', $value)) {' . "\n";
                $body .= '            return $obj;' . "\n";
                $body .= '        }' . "\n";
                $body .= '        return null;' . "\n";
                $body .= '    }' . "\n";
            }
            $body .= "\n";
            foreach ($table->getFields() as $field_name => $field) {
                [$type] = self::schema_type_php($field->getType());
                $isString  = $type == 'string';
                $stringLen = $struct['length'] ?? 0;
                //
                // -- getter
                //
                if ($field->getComment()) {
                    $body .= self::writeComment(1, 'get ' . $field->getComment());
                }
                $body .= '    public function get_' . $field_name . '(): ' . $type . ' {' . "\n";
                $body .= '        return $this->' . $field_name . ';' . "\n";
                $body .= '    }' . "\n";
                //
                // -- setter
                //
                if ($field->getComment()) {
                    $body .= self::writeComment(1, 'set ' . $field->getComment());
                }
                $body      .= '    public function set_' . $field_name . '(' . $type . ' $value): static {' . "\n";
                $codeValue = '$value';
                if ($isString && $stringLen) {
                    $codeValue = 'mb_substr($value, 0, ' . $stringLen . ')';
                }
                $body .= '        $this->' . $field_name . ' = ' . $codeValue . ';' . "\n";
                $body .= '        return $this;' . "\n";
                $body .= '    }' . "\n";
                if ($field->getType() == FieldType::ENUM) {
                    $_enumName = $field->getEnum()->name;
                    if ($field->getComment()) {
                        $body .= self::writeComment(1, 'get ' . $field->getComment());
                    }
                    $body .= '    public function get_link_' . $field_name . '(): ?' . $_enumName . ' {' . "\n";
                    $body .= '        return ' . $_enumName . '::tryFrom($this->' . $field_name . ');' . "\n";
                    $body .= '    }' . "\n";
                    if ($field->getComment()) {
                        $body .= self::writeComment(1, 'check if ' . $field->getComment());
                    }
                    $body .= '    public function is_link_' . $field_name . '(' . $_enumName . ' $value): bool {' . "\n";
                    $body .= '        return $value->value == $this->' . $field_name . ';' . "\n";
                    $body .= '    }' . "\n";
                    if ($field->getComment()) {
                        $body .= self::writeComment(1, 'set ' . $field->getComment());
                    }
                    $body .= '    public function set_link_' . $field_name . '(' . $_enumName . ' $value): static {' . "\n";
                    $body .= '        $this->' . $field_name . ' = $value->value;' . "\n";
                    $body .= '        return $this;' . "\n";
                    $body .= '    }' . "\n";
                }
                if ($fieldFK = $field->get_fk()) {
                    $fkConf  = $fieldFK['table'];
                    if ($fkConf->getClass()) {
                        $body .= '    public function get_link_' . $field_name . '(): ?' . $fkConf->getClass() . ' {' . "\n";
                        $body .= '         return ' . $fkConf->getClass() . '::getInstance($this->' . $field_name . ');' . "\n";
                        $body .= '    }' . "\n";
                        $body .= '    public function set_link_' . $field_name . '(' . $fkConf->getClass() . ' $obj): static {' . "\n";
                        $body .= '         $this->' . $field_name . ' = $obj->get_' . $fieldFK['pk'] . '();' . "\n";
                        $body .= '         return $this;' . "\n";
                        $body .= '    }' . "\n";
                    }
                }
            }
            $body .= '}' . "\n\n";
            /*
            if ($config['api'] ?? false)
            {
                // HTTP_METHOD  => ACTION
                // ----------------------
                // PUT          => UPDATE
                // POST         => CREATE
                // GET          => SELECT
                // DELETE       => DELETE


                $body .= '/**' . "\n";
                $body .= ' * Gestion des ' . $config['class'] . "\n";
                $body .= ' * /' . "\n";
                $body .= '#[APIService(\''.$config['api']['service'].'\')]'."\n";
                $body .= 'class ' . $config['class'] . 'API ' . "\n" . '{' . "\n";
                if ($config['api']['features']['select'] ?? false)
                {
                    $body .= '    /**'."\n";
                    $body .= '     * Get the requested '.$config['class']."\n";
                    $body .= '     * @param int $id ID of the object to load.'."\n";
                    $body .= '     * @return '.$config['class'].'|null the loaded object or null.'."\n";
                    $body .= '     * @since 1.0'."\n";
                    $body .= '     * /'."\n";
                    $body .= '    #['."\n";
                    $body .= '        APIAccess(\'get\', Method::GET, \'public\'),'."\n";
                    $body .= '        APIResp(HTTPStatus::OK, HTTPStatus::NotFound)'."\n";
                    $body .= '    ]'."\n";
                    $body .= '    public function get(int $id): ?'.$config['class'].' {' . "\n";
                    $body .= '        if (!$id) return null;' . "\n";
                    $body .= '        return '.$config['class'].'::getInstance($id);' . "\n";
                    $body .= '    }' . "\n";
                }
                if ($config['api']['features']['create'] ?? false)
                {
                    $body .= '    '."\n";
                    $body .= '    /**'."\n";
                    $body .= '     * Object Description'."\n";
                    $body .= '     * @param '.$config['class'].' $data The object to create.'."\n";
                    $body .= '     * @return bool If the operation was successful or not.'."\n";
                    $body .= '     * @since 1.0'."\n";
                    $body .= '     * /'."\n";
                    $body .= '    #[APIAccess(\'post\', Method::POST, \'public\')]'."\n";
                    $body .= '    #[APIResp(HTTPStatus::OK, HTTPStatus::Unauthorized)]'."\n";
                    $body .= '    public function post('.$config['class'].' $data): bool {' . "\n";
                    $body .= '        // If the primary key is defined, it is not an update.' . "\n";
                    $body .= '        if ($data->getPrimaryKeyValue()) {' . "\n";
                    $body .= '            return false;' . "\n";
                    $body .= '        }' . "\n";
                    $body .= '        return $data->save();' . "\n";
                    $body .= '    }' . "\n";
                }
                $body .= '}' . "\n\n";
            }
            */
        }
        $body .= 'namespace ' . self::$schema->getNamespace() . '\\api\\exchange;' . "\n\n";
        $body .= 'use Tivins\Baz\API\IOStruct;' . "\n\n";
        foreach ($exposed as $class => $fields) {
            $body .= '#[IOStruct]' . "\n";
            $body .= "class $class\n{\n";

            /**
             * @var Field $_struct
             */
            foreach ($fields as $_name => $_struct) {
                [$type] = self::schema_type_php($_struct->getType());
                if ($_struct->getComment()) {
                    $body .= self::writeComment(1, '@var ' . $type . ' ' . $_struct->getComment());
                }
                $default = self::getDefaultPHP($_struct);
                $body    .= '    public ' . $type . ' $' . StrUtil::snakeToCamel($_name) . $default . ';' . "\n";
            }
            $body .= '}' . "\n\n";
        }
        return file_put_contents($filename, $body) !== false;
    }

    public static function schema_type_php(FieldType $type): array
    {
        return match ($type) {
            FieldType::BOOL => ['bool', 'boolval'],
            FieldType::INT, FieldType::UINT, FieldType::ENUM, FieldType::TIMESTAMP, FieldType::BYTE, FieldType::SERIAL => ['int', 'intval'],
            FieldType::DOUBLE, FieldType::FLOAT => ['float', 'floatval'],
            FieldType::TEXT, FieldType::STRING => ['string', null],
        };
    }

    public static function setSchema(Schema $schema): void
    {
        self::$schema = $schema;
    }
}