<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\DataManager\Mandango\Action;

use WhiteOctober\AdminBundle\DataManager\Base\Action\ListAction as BaseListAction;
use Symfony\Component\HttpFoundation\ParameterBag;
use Pagerfanta\Adapter\MandangoAdapter;

class ListAction extends BaseListAction
{
    private $query;

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('mandango.list')
        ;
    }

    public function configureActionsVars(ParameterBag $actionsVars)
    {
        parent::configureActionsVars($actionsVars);

        $dataClass = $this->getDataClass();

        $actionsVars->set('createData', function () use ($dataClass) {
            return new $dataClass();
        });
        $actionsVars->set('findDataById', function ($id) use ($dataClass) {
            return $dataClass::getRepository()->findOneById($id);
        });
        $actionsVars->set('saveData', function ($data) {
            $data->save();
        });
        $actionsVars->set('deleteData', function ($data) {
            $data->delete();
        });
    }

    protected function initQuery()
    {
        $dataClass = $this->getDataClass();
        $this->query = $dataClass::getRepository()->createQuery();
    }

    protected function applyFilter($filter)
    {
        foreach ($this->getFilterFields() as $field) {
            $this->query->mergeCriteria(array($field => new \MongoRegex(sprintf('/%s/', $filter))));
        }
    }

    protected function applySort($sort, $order)
    {
        $this->query->sort(array($sort => 'asc' == $order ? \MongoCollection::ASCENDING : \MongoCollection::DESCENDING));
    }

    protected function createPagerfantaAdapter()
    {
        return new MandangoAdapter($this->query);
    }
}
