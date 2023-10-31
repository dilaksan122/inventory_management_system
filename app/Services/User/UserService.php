<?php

namespace App\Services\User;

use App\Notifications\UserCreateNotification;
use App\Services\Role\RoleService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Contracts\Role;
use Throwable;
use App\Models\User;
use App\Services\BaseService;
use Illuminate\Support\Facades\Hash;

/**
 * UserService
 */
class UserService extends BaseService
{
    /**
     * __construct
     *
     * @param mixed $model
     * @return void
     */
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * createOrUpdate
     *
     * @param mixed $data
     * @param mixed $id
     * @return void
     */
    public function createOrUpdate(array $data, $id = null)
    {
        if ($id) {
            // Update
            $user = $this->get($id);

            // Password
            if (isset($data['password']) && $data['password']) {
                $user->password = Hash::make($data['password']);
            }

            // Avatar
            if (isset($data['avatar']) && $data['avatar'] != null) {
                $user->avatar = $this->uploadFile($data['avatar'], $user->avatar);
            }

            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->status = $data['status'];

            // Update user
            $user->save();
            // Give user role
            return $user->syncRoles($data['role']);
        } else {
            // Create
            $data['password'] = Hash::make($data['password']);
            if (isset($data['avatar']) && $data['avatar'] != null) {
                $data['avatar'] = $this->uploadFile($data['avatar']);
            }

            // Store user
            $user = $this->model::create($data);

            $this->sendUserNotification($data);

            // Give user role
            return $user->syncRoles($data['role']);
        }
    }

    public function sendUserNotification($data)
    {
        try {

            $appName = (config('site_title') ?? config('app.name'));
            $itclan = "<a href='https://www.itclanbd.com/'>ITclan BD</a>";
            $reset_url = route('admin.auth.reset-password').'?email='.$data['email'].'&token='.config('app.key');
            $content = [

                'subject'           => 'Welcome to ' . $appName,
                'greeting'          => "Greetings from $itclan,",
                'content2'          => __('custom.main_content', ['attribute' => $appName]),
                'content_bn'        => '',//__('custom.main_content_bn', ['attribute' => $appName]),
                'reset_button_name' => __('custom.click_here'),
                'reset_url'         => $reset_url,
                'support_content'   => __('custom.support_content'),
                'support_content_bn'   => '',//__('custom.support_content_bn'),

            ];
            Notification::route('mail', $data['email'])
                ->notify(new UserCreateNotification($content));

        }catch (\Exception $e){

            Log::info($e->getMessage());

        }
    }

    /**
     * delete
     *
     * @param mixed $id
     * @return void
     */
    public function delete($id)
    {
        try {
            $user = $this->model::findOrFail($id);
            // Delete avatar
            try {
                Storage::disk(config('filesystems.default'))->delete($this->model::FILE_STORE_PATH . '/' . $user->avatar);
            } catch (Throwable $th) {
                throw $th;
            }
            // Delete user
            return $user->delete();
        } catch (Throwable $th) {
            throw $th;
        }
    }

    public function updateProfile(array $data, $id)
    {
        try {
            // Update
            $user = $this->get($id);

            // Password
            if (isset($data['password']) && $data['password']) {
                $user->password = Hash::make($data['password']);
            }

            // Avatar
            if (isset($data['avatar']) && $data['avatar'] != null) {
                $user->avatar = $this->uploadFile($data['avatar'], $user->avatar);
            }

            $user->name = $data['name'];
            $user->email = $data['email'];

            // Update user
            return $user->save();
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
