<?php declare(strict_types=1);

/* Copyright (c) 2021 - Nils Haagen <nils.haagen@concepts-and-training.de> - Extended GPL, see LICENSE */

use ILIAS\DI\Container;

/**
 * Handle Conditions within the LearningSequence Objects.
 */
class ilLearningSequenceConditionController implements ilConditionControllerInterface
{
    /**
     * @inheritdoc
     */
    public function isContainerConditionController($a_container_ref_id) : bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getConditionSetForRepositoryObject($a_container_child_ref_id) : ilConditionSet
    {
        $f = $this->getConditionsFactory();
        $conditions = [];

        $container_ref_id = $this->getParentRefIdFor((int) $a_container_child_ref_id);

        //for users with edit-permissions, do not apply conditions
        if ($this->applyConditionsForCurrentUser($container_ref_id)) {
            $sequence = $this->getSequencedItems($container_ref_id);

            //find position
            $pos = 0;
            foreach ($sequence as $index => $item) {
                if ($item->getRefId() === (int) $a_container_child_ref_id) {
                    $pos = $index;
                    break;
                }
            }

            if ($pos > 0) {
                $previous_item = $sequence[$pos - 1];
                $post_conditions = array($previous_item->getPostCondition());

                if (count($post_conditions) > 0) {
                    foreach ($post_conditions as $post_condition) {
                        $condition_op = $post_condition->getConditionOperator();
                        if ($condition_op === 'learning_progress') {
                            $condition_op = 'learningProgress';
                        }
                        if ($condition_op !== ilLSPostConditionDB::STD_ALWAYS_OPERATOR) {
                            $conditions[] = $f->condition(
                                $f->repositoryTrigger($previous_item->getRefId()),
                                $f->operator()->$condition_op(),
                                $post_condition->getValue()
                            );
                        }
                    }
                }
            }
        }

        return $f->set($conditions);
    }

    protected function getConditionsFactory() : ilConditionFactory
    {
        return $this->getDIC()->conditions()->factory();
    }

    protected function getDIC() : Container
    {
        global $DIC;
        return $DIC;
    }

    protected function getTree() : ilTree
    {
        $dic = $this->getDIC();
        return $dic['tree'];
    }

    protected function getAccess() : ilAccess
    {
        $dic = $this->getDIC();
        return $dic['ilAccess'];
    }

    protected function getParentRefIdFor(int $child_ref_id) : int
    {
        $tree = $this->getTree();
        return (int) $tree->getParentId($child_ref_id);
    }

    protected function getContainerObject(int $container_ref_id) : ilObjLearningSequence
    {
        /** @var ilObjLearningSequence $possible_object */
        $possible_object = ilObjectFactory::getInstanceByRefId($container_ref_id);

        if (!$possible_object instanceof ilObjLearningSequence) {
            throw new LogicException("Object type should be ilObjLearningSequence. Actually is " . get_class($possible_object));
        }

        if (!$possible_object) {
            throw new Exception('No object found for ref id ' . $container_ref_id . '.');
        }

        return $possible_object;
    }

    /**
     * @return LSItem[]
     */
    protected function getSequencedItems(int $container_ref_id) : array
    {
        $container = $this->getContainerObject($container_ref_id);
        return $container->getLSItems();
    }

    protected function applyConditionsForCurrentUser(int $container_ref_id) : bool
    {
        $il_access = $this->getAccess();
        $may_edit = $il_access->checkAccess('edit_permission', '', $container_ref_id);
        return $may_edit === false;
    }
}
