<?php

use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $newAttributes = [
            'book_shelf' => [
                'view'      => 'book_shelf.index',
                'create'    => 'book_shelf.create',
                'store'     => 'book_shelf.store',
                'published' => 'book_shelf.publish',
                'edit'      => 'book_shelf.edit',
                'delete'    => 'book_shelf.destroy',
            ],
        ];

        foreach ($newAttributes as $key => $attribute) {
            $permission            = new Permission;
            $permission->name      = str_replace('_', ' ', $key);
            $permission->attribute = $key;
            $permission->keywords  = $attribute;
            $permission->save();
            foreach ($attribute as $index => $permit) {
                $admin_permission[] = trim($permit);
            }
            $user                  = User::first();
            $user->permissions     = array_merge($user->permissions, $admin_permission);
            $user->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $attributes = [
            'book_shelf' => [
                'view'      => 'book_shelf.index',
                'create'    => 'book_shelf.create',
                'store'     => 'book_shelf.store',
                'published' => 'book_shelf.publish',
                'edit'      => 'book_shelf.edit',
                'delete'    => 'book_shelf.destroy',
            ],
        ];

        foreach ($attributes as $key => $attribute) {
            Permission::where('attribute', $key)->delete();
            $admin_permission = [];
            $user = User::first();
            foreach ($attribute as $index => $permit) {
                $admin_permission[] = trim($permit);
            }
            $user->permissions = array_diff($user->permissions, $admin_permission);
            $user->save();
        }
    }
};