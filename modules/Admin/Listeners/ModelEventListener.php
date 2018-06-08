<?php
/**
 * To handle Cache clear based on Memcache driver
 * on Model manipulation actions.
 *
 * @author Gaurav Patel <gauravp@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */
namespace Modules\Admin\Listeners;

use Cache;

class ModelEventListener
{

    /**
     * General function to clear cache based on Tags
     *
     * @param  string $tags as Model table
     * @return void
     */
    protected function clearCacheTags($tags)
    {
        Cache::tags($tags)->flush();
    }

    /**
     * Clear Cache while newly created records
     *
     * @param  Instnce of $model
     * @return void
     */
    public function created($model)
    {
        $this->clearCacheTags($model->getTable());
    }

    /**
     * Clear Cache while updating records
     *
     * @param  Instnce of $model
     * @return void
     */
    public function updated($model)
    {
        $this->clearCacheTags($model->getTable());
    }

    /**
     * Clear Cache while saved records
     *
     * @param  Instnce of $model
     * @return void
     */
    public function saved($model)
    {
        $this->clearCacheTags($model->getTable());
    }

    /**
     * Clear Cache while deleted records
     *
     * @param  Instnce of $model
     * @return void
     */
    public function deleted($model)
    {
        $this->clearCacheTags($model->getTable());
    }

    /**
     * Clear Cache while restored records
     *
     * @param  Instnce of $model
     * @return void
     */
    public function restored($model)
    {
        $this->clearCacheTags($model->getTable());
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     * @return array
     */
    public function subscribe($events)
    {
        $events->listen(
            'eloquent.created: *', 'Modules\Admin\Listeners\ModelEventListener@created'
        );
        $events->listen(
            'eloquent.updated: *', 'Modules\Admin\Listeners\ModelEventListener@updated'
        );
        $events->listen(
            'eloquent.saved: *', 'Modules\Admin\Listeners\ModelEventListener@saved'
        );
        $events->listen(
            'eloquent.deleted: *', 'Modules\Admin\Listeners\ModelEventListener@deleted'
        );
        $events->listen(
            'eloquent.restored: *', 'Modules\Admin\Listeners\ModelEventListener@restored'
        );
    }
}
