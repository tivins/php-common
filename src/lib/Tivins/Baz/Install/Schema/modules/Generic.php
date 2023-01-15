<?php

namespace Tivins\Baz\Install\Schema\modules;

use Tivins\Baz\install\schema\Enum;
use Tivins\Baz\install\schema\Field;
use Tivins\Baz\install\schema\Table;

class Generic
{
    public const TableUser = 'users';

    public static function getModuleProcess(Table $userTable, Enum $types): Table
    {
        $id_process = Field::newSerial('id_process', 'the unique identifier of processes')->setExchange('id');
        $id_user    = Field::newForeign('id_user', $userTable, 'owner');
        $progress   = Field::newInt('progress', 0, 'Integer from 0 to 100')
            ->setExchange(true)
            ->setNotNull();
        $status     = Field::newString('status', 255)
            ->setComment('A string that define the current state of the process.')
            ->setExchange(true);
        $back_url   = Field::newString('back_url', 255);
        $data       = Field::newText('data', 'json');
        $type       = Field::newEnum('type', $types);
        $created    = Field::newTimestamp('created', 'the creation timestamp')
            ->setExchange(true);
        $started    = Field::newTimestamp('started', 'the started timestamp')
            ->setExchange(true);
        $terminated = Field::newTimestamp('terminated', 'the end timestamp')
            ->setExchange(true)
            ->setDefault(0)
            ->setNotNull();
        //
        return (new Table('process', 'Process', 'Process info'))
            ->addFields($id_process, $id_user, $progress, $status, $back_url, $data, $type, $created, $started, $terminated)
            ->setExchange('ProcessInfo')
            ->setPrimaryKey($id_process);
    }

    public static function getUserSchema(): Table
    {
        $id        = Field::newSerial('id_user', 'User unique ID')->setSelectable();
        $name      = Field::newString('name', 32, 'User display name')
            ->setSelectable()
            ->setUnique()
            ->setNotEmpty();
        $mail      = Field::newString('mail', 255, 'User email')
            ->setSelectable()
            ->setUnique()
            ->setValidEmail();
        $pass      = Field::newString('pass', 255, 'Encrypted password')->setNotEmpty();
        $created   = Field::newTimestamp('created', 'the creation timestamp');
        $deleted   = Field::newTimestamp('deleted', 'the deletion timestamp');
        $last_conn = Field::newTimestamp('last_conn', 'the last_conn timestamp');
        $nb_conn   = Field::newInt('nb_conn');
        //
        return (new Table(static::TableUser, 'User', 'Players account'))
            ->addFields($id, $name, $mail, $pass, $created, $deleted, $last_conn, $nb_conn)
            ->setPrimaryKey($id);
    }

    public static function getPermissionSchema(): Table
    {
        $id      = Field::newSerial('id_permission', 'Permission unique ID')->setSelectable();
        $name    = Field::newString('name', 64, 'User display name')
            ->setSelectable()
            ->setUnique()
            ->setNotEmpty();
        $created = Field::newTimestamp('created', 'the creation timestamp');
        $deleted = Field::newTimestamp('deleted', 'the deleted timestamp');
        //
        return (new Table('permissions', 'Permission', 'Permissions'))
            ->addFields($id, $name, $created, $deleted)
            ->setPrimaryKey($id);
    }
    public static function getUsersPermissionsSchema(Table $user, Table $perms): Table
    {
        $id            = Field::newSerial('id_user_permission', 'Users Permissions unique ID')
            ->setSelectable();
        $id_user       = Field::newForeign('id_user', $user)->setSelectable();
        $id_permission = Field::newForeign('id_permission', $perms)->setSelectable();
        $created       = Field::newTimestamp('created', 'the creation timestamp');
        $seen          = Field::newTimestamp('seen', 'the timestamp of when the user has seen the notification');
        //
        $table = new Table('users_permissions', 'UsersPermissions', 'Users Permissions');
        $table->addFields($id, $id_user, $id_permission, $created, $seen);
        $table->setPrimaryKey($id);
        //
        return $table;
    }

    public static function getModuleTranslation(): Table
    {
        $idPK   = Field::newSerial('id_translation');
        $lang   = Field::newString('lang', 5);
        $key    = Field::newString('key', 128);
        $id     = Field::newUint('id');
        $value1 = Field::newText('value1');
        $value2 = Field::newText('value2');
        $value3 = Field::newText('value3');
        //
        return (new Table('translation', 'Translation', 'translation'))
            ->addFields($idPK,$id, $lang, $key, $id, $value1, $value2, $value3)
            ->setPrimaryKey($idPK)
            ->addIndex('key_id', ['lang', 'key', 'id'])
            ->setMapId('id');
    }

    /**
     * @return array<Table|Enum>
     */
    public static function getModuleUser(): array
    {
        $enumProc = (new Enum('ProcessType', 'int', cases: [
            'NONE'     => 0,
            'POPULATE' => 1,
        ]));
        return [
            $enumProc,
            $tableUser = self::getUserSchema(),
            $tablePerms = self::getPermissionSchema(),
            self::getUsersPermissionsSchema($tableUser, $tablePerms),
            self::getModuleProcess($tableUser, $enumProc),
        ];
    }
}