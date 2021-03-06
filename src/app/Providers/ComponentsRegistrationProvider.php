<?php

namespace LAdmin\Providers;

use Exception;
use LAForm;
use LAFormItem;
use LAListView;
use LAListColumn;
use LATable;
use LAdmin\Package\Form\Instance as PackageForms;
use LAdmin\Package\FormItem\Instance as PackageFormItems;
use LAdmin\Package\ListView\Instance as PackageListViews;
use LAdmin\Package\ListColumn\Instance as PackageListColumns;
use LAdmin\Package\Table\Instance as PackageTables;

class ComponentsRegistrationProvider extends BaseProvider
{

    /**
     * Registering all available components
     *
     * @param  null
     *
     * @return void
     */
    public function register()
    {
        $this->registerForms();
        $this->registerFormItems();
        $this->registerListViews();
        $this->registerListColumns();
        $this->registerTables();
    }

    /**
     * Registering all events the package is interested in
     *
     * @param  null
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Regitering all default available Form components
     *
     * @param  null
     *
     * @return void
     */
    private function registerForms()
    {
        LAForm::register('simple', PackageForms\SimpleForm::class);
    }

    /**
     * Regitering all default available Form Item components
     *
     * @param  null
     *
     * @return void
     */
    private function registerFormItems()
    {
        LAFormItem::register('simple', PackageFormItems\SimpleFormItem::class);
        LAFormItem::register('text', PackageFormItems\BaseFormTags\Text::class);
        LAFormItem::register('file', PackageFormItems\BaseFormTags\File::class);
        LAFormItem::register('email', PackageFormItems\BaseFormTags\Email::class);
        LAFormItem::register('number', PackageFormItems\BaseFormTags\Number::class);
        LAFormItem::register('button', PackageFormItems\BaseFormTags\Button::class);
        LAFormItem::register('reset-button', PackageFormItems\BaseFormTags\ResetButton::class);
        LAFormItem::register('submit-button', PackageFormItems\BaseFormTags\SubmitButton::class);
        // LAFormItem::register('datalist', PackageFormItems\SimpleFormItem::class);
        // LAFormItem::register('range', PackageFormItems\SimpleFormItem::class);
        LAFormItem::register('textarea', PackageFormItems\BaseFormTags\Textarea::class);
        LAFormItem::register('select', PackageFormItems\BaseFormTags\Select::class);
        LAFormItem::register('checkbox', PackageFormItems\BaseFormTags\Checkbox::class);
        LAFormItem::register('radio', PackageFormItems\BaseFormTags\RadioButtonSet::class);
        // LAFormItem::register('image', PackageFormItems\SimpleFormItem::class);
        // LAFormItem::register('images', PackageFormItems\SimpleFormItem::class);
    }

    /**
     * Regitering all default available List View components
     *
     * @param  null
     *
     * @return void
     */
    private function registerListViews()
    {
        LAListView::register('simple', PackageListViews\SimpleListView::class);
    }

    /**
     * Regitering all default available List Column components
     *
     * @param  null
     *
     * @return void
     */
    private function registerListColumns()
    {
        LAListColumn::register('simple', PackageListColumns\SimpleListColumn::class);
    }

    /**
     * Regitering all default available Table components
     *
     * @param  null
     *
     * @return void
     */
    private function registerTables()
    {
        LATable::register('simple',             PackageTables\SimpleTable::class);
        LATable::register('model-collection',   PackageTables\ModelCollectionTable::class);
        LATable::register('model',              PackageTables\ModelTable::class);
    }

}
